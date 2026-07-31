<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\Gateway;
use App\Models\VirtualCard as ModelsVirtualCard;
use App\Models\VirtualCardHolder;
use Exception;
use Stripe\Exception\SignatureVerificationException;
use Stripe\File;
use Stripe\Stripe;
use Stripe\Issuing\Cardholder;
use Stripe\Issuing\Card;
use Stripe\StripeClient;
use UnexpectedValueException;

class VirtualCard
{
    private $user;

    /**
     * VirtualCard constructor.
     * Initializes the user by fetching the currently authenticated user.
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }


    /**
     * Creates a new virtual card for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request The request object containing user input.
     * @return \Illuminate\Http\JsonResponse A JSON response with the card creation result.
     */
    public function newCard($request)
    {
        try {
            $this->setUpApiStripe();
            if (request()->card_holder_type == Status::VIRTUAL_CARD_HOLDER_NEW) {
                $provideCardHolder = $this->createCardHolder();
                $cardHolder        = $this->saveNewCardHolder($provideCardHolder);
            } else {
                $cardHolder = VirtualCardHolder::where('id', request()->card_holder)->where('user_id', $this->user->id)->first();
                if (!$cardHolder) {
                    $message[] = "The card holder is not found";
                    return apiResponse('not_found', 'error', $message);
                }
            }

            return $this->createCard($cardHolder, $request);
        } catch (Exception $ex) {
            return apiResponse('exception', 'error', [$ex->getMessage()]);
        }
    }

    /**
     * Creates a new cardholder using the Stripe API.
     *
     * @return \Stripe\Issuing\Cardholder The created cardholder object.
     * @throws Exception If an error occurs while creating the cardholder.
     */
    private function createCardHolder()
    {
        try {

            $request = request();

            // Upload the government ID images to Stripe
            $idFrontImage = $this->uploadImageToStripe($request->document_front);
            $idBackImage  = $this->uploadImageToStripe($request->document_back);

            return Cardholder::create([
                'name'         => $request->card_name,
                'email'        => $request->email,
                'phone_number' => $request->mobile_number,
                'type'         => 'individual',
                'individual'   => [
                    'first_name'   => $request->first_name,
                    'last_name'    => $request->last_name,
                    'dob'          => ['day' => $request->birthday, 'month' => $request->birthday_month, 'year' => $request->birthday_year],
                    'card_issuing' => [
                        'user_terms_acceptance' => [
                            'date' => time(),
                            'ip'   => request()->ip(),
                        ],
                    ],
                    'verification' => [
                        'document' => [
                            'front' => $idFrontImage,  // Uploaded front image
                            'back'  => $idBackImage,   // Uploaded back image
                        ],
                    ],
                ],
                'billing' => $this->getBillingAddress($request),
            ]);
        } catch (Exception $ex) {
            throw new Exception("Failed to create cardholder: " . $ex->getMessage());
        }
    }


    /**
     * Retrieves the billing address details for the cardholder.
     *
     * @param \Illuminate\Http\Request $request The request object containing address details.
     * @return array The billing address.
     */
    private function getBillingAddress($request)
    {
        $country = getUserSelectCountry();
        return [
            'address' => [
                'line1'       => $request->address,
                'city'        => $request->city,
                'state'       => $request->state,
                'postal_code' => $request->zip_code,
                'country'     => strtoupper($country->code)
            ]
        ];
    }

    /**
     * Creates a new virtual card for the specified cardholder.
     *
     * @param \App\Models\VirtualCardHolder $cardHolder The cardholder for the card.
     * @param \Illuminate\Http\Request $request The request object containing card details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the card creation result.
     * @throws Exception If card creation fails.
     */
    private function createCard($cardHolder, $request)
    {
        try {
            $card = Card::create([
                'cardholder'        => $cardHolder->card_holder_id,
                'currency'          => strtoupper(gs('cur_text')),
                'type'              => "virtual",
                'status'            => "active",
                'spending_controls' => [
                    'blocked_categories' => [],
                    'spending_limits'    => [
                        [
                            'amount'   => 1, // stripe required minimum amount 1
                            'interval' => 'all_time',
                            'categories' => []
                        ],
                    ],
                ],
            ]);
            $cardDetails = $this->saveCardDetails($card, $cardHolder);
            $notify[] = "The new card is created successfully";
            return apiResponse('created', 'success', $notify, [
                'card_id' => $cardDetails->id
            ]);
        } catch (Exception $ex) {
            throw new Exception("Failed to create card: " . $ex->getMessage());
        }
    }


    /**
     * Saves the newly created card details to the database.
     *
     * @param \Stripe\Issuing\Card $card The Stripe card object.
     * @param \App\Models\VirtualCardHolder $cardHolder The associated cardholder.
     * @return \App\Models\VirtualCard The saved virtual card object.
     */
    private function saveCardDetails($card, $cardHolder)
    {
        $status = [
            'active'    => Status::VIRTUAL_CARD_ACTIVE,
            'inactive'  => Status::VIRTUAL_CARD_INACTIVE,
            'cancelled' => Status::VIRTUAL_CARD_CLOSED,

        ][$card->status];

        $virtualCard                 = new ModelsVirtualCard();
        $virtualCard->user_id        = $this->user->id;
        $virtualCard->brand          = $card->brand;
        $virtualCard->card_id        = $card->id;
        $virtualCard->last4          = $card->last4;
        $virtualCard->exp_month      = $card->exp_month;
        $virtualCard->exp_year       = $card->exp_year;
        $virtualCard->cardholder_id  = $cardHolder->id;
        $virtualCard->status         = $status;
        $virtualCard->card_type      = $card->type;
        $virtualCard->balance        = 0;
        $virtualCard->usability_type = request()->usability_type;
        $virtualCard->save();

        return $virtualCard;
    }

    /**
     * Sets up the Stripe API with the appropriate API key from the gateway.
     *
     * @throws Exception If Stripe setup fails.
     */
    private function setUpApiStripe()
    {
        $gateway = Gateway::automatic()->where('alias', "StripeV3")->first();
        if (!$gateway) {
            throw new Exception("The problem found to setup Stripe");
        }

        $parameters = json_decode($gateway->gateway_parameters);
        if (!@$parameters->secret_key->value) {
            throw new Exception("The problem found to setup Stripe");
        }

        Stripe::setApiKey($this->getStripeKey());
    }


    /**
     * Saves a new cardholder to the database.
     *
     * @param \Stripe\Issuing\Cardholder $cardHolder The cardholder object to save.
     * @return \App\Models\VirtualCardHolder The saved cardholder object.
     */
    private function saveNewCardHolder($cardHolder)
    {

        $virtualCardHolder                 = new VirtualCardHolder();
        $virtualCardHolder->card_holder_id = $cardHolder->id;
        $virtualCardHolder->user_id        = auth()->id();
        $virtualCardHolder->name           = $cardHolder->name;
        $virtualCardHolder->first_name     = request()->first_name;
        $virtualCardHolder->last_name      = request()->last_name;
        $virtualCardHolder->name           = request()->card_name;
        $virtualCardHolder->phone_number   = request()->mobile_number;
        $virtualCardHolder->email          = $cardHolder->email;
        $virtualCardHolder->address        = $cardHolder->billing->address->line1;
        $virtualCardHolder->state          = $cardHolder->billing->address->state;
        $virtualCardHolder->country        = $cardHolder->billing->address->country;
        $virtualCardHolder->country        = $cardHolder->billing->address->country;
        $virtualCardHolder->city           = $cardHolder->billing->address->city;
        $virtualCardHolder->postal_code    = $cardHolder->billing->address->postal_code;
        $virtualCardHolder->dob            = @$cardHolder->individual->dob;
        $virtualCardHolder->document_front = @$cardHolder->individual->verification->document->front;
        $virtualCardHolder->document_back  = @$cardHolder->individual->verification->document->back;
        $virtualCardHolder->save();

        return $virtualCardHolder;
    }


    /**
     * Decreases the spending limit of a virtual card.
     *
     * @param string $cardId The ID of the card to update.
     * @param float $decreaseAmount The amount to decrease the spending limit.
     * @throws Exception If an error occurs while updating the spending limit.
     */
    public function updateSpendingLimit($cardId, $amount)
    {
        try {
            $this->setUpApiStripe();
            $allowCategories = $this->stripeAllCategories();

            if ($amount <= 1) {
                $amount = 1; // Stripe requires at least 1
                $spendingControls = [
                    'spending_limits' => [
                        [
                            'amount'   => $amount * 100, // convert to cent
                            'interval' => 'all_time',
                            'categories' => []
                        ]
                    ],
                    "allowed_categories" => [],
                ];
            } else {
                $spendingControls = [
                    'spending_limits' => [
                        [
                            'amount'   => (int)$amount * 100, // convert to cent
                            'interval' => 'all_time',
                            'categories' => $allowCategories
                        ]
                    ],
                    'allowed_categories' => $allowCategories // Allow all categories
                ];
            }


            Card::update($cardId, [
                'spending_controls' => $spendingControls,
            ]);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }


    /**
     * Cancels a virtual card by updating its status to 'canceled'.
     *
     * @param string $cardId The ID of the card to cancel.
     * @throws Exception If an error occurs while canceling the card.
     */
    public function cancel($cardId)
    {
        try {
            $this->setUpApiStripe();
            Card::update(
                $cardId,
                ['status' => 'canceled']
            );
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }


    /**
     * Retrieves confidential card data (e.g., number, cvc).
     *
     * @param string $cardId The ID of the card to retrieve confidential data for.
     * @return \Illuminate\Http\JsonResponse A JSON response with the confidential data.
     * @throws Exception If an error occurs while fetching the data.
     */
    public function getCardConfidential($cardId)
    {
        try {
            $nonce            = $this->fetchPrivateNonce();
            $publicNonce      = $nonce['public'];
            $privateNonce     = $nonce['private'];
            $kes              = $this->ephemeralKeys($publicNonce, $cardId);
            $confidentialData = $this->fetchCardDetails($kes['ephemeralKeySecret'], $privateNonce, $cardId);

            $message[] = "The card confidential data is provided";
            return apiResponse('success', 'success', $message, $confidentialData);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Fetches the private nonce from an external service.
     *
     * @return string The private nonce.
     * @throws Exception If an error occurs while fetching the nonce.
     */
    private function fetchPrivateNonce()
    {
        try {
            $url = 'https://api.stripe.com/v1/ephemeral_key_nonces';
            $key = $this->getStripeKey();

            $headers = [
                "Authorization: Bearer $key",
                'Stripe-Version: ' . "2022-08-01",
                'Content-Type: application/json'
            ];

            $response = CurlRequest::curlPostContent($url, header: $headers);
            $nonces   = json_decode($response, true);

            // Check if the response contains the required data
            if (isset($nonces['public_nonce']) && isset($nonces['private_nonce'])) {
                return [
                    'public'  => $nonces['public_nonce'],
                    'private' => $nonces['private_nonce']
                ];
            }
            throw new Exception("Failed to fetch nonces or invalid response from provider");
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Fetches the card details from Stripe's Issuing API.
     *
     * This method retrieves the card details, such as the card number, CVC, and PIN,
     * using the provided card ID, private nonce, and API key. The sensitive data 
     * is encoded in base64 before returning.
     *
     * @param string $key The Stripe API key used for authorization.
     * @param string $nonce The private nonce used for secure communication.
     * @param string $cardId The ID of the card whose details are to be fetched.
     * @return array An associative array containing the card number and CVC (both base64 encoded).
     * @throws Exception If the card details cannot be fetched or the response is invalid.
     */
    private function fetchCardDetails($key, $nonce, $cardId)
    {
        try {
            $url = "https://api.stripe.com/v1/issuing/cards/$cardId?ephemeral_key_private_nonce=$nonce&expand[0]=number&expand[1]=cvc&expand[2]=pin.number&safe_expands=true";
            $headers = [
                "Authorization: Bearer $key",
                'Stripe-Version: ' . "2022-08-01",
                'Content-Type: application/json'
            ];

            $response = CurlRequest::curlContent($url, header: $headers);
            $response = json_decode($response, true);

            // Check if the response contains the required data
            if (isset($response['id']) && isset($response['cvc']) && isset($response['number'])) {
                return [
                    'number' => base64_encode($response['number']),
                    'cvc'    => base64_encode($response['cvc']),
                ];
            }
            throw new Exception("Failed to fetch the card confidential data.");
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * Generates ephemeral keys for secure communication with Stripe.
     *
     * @param string $nonce The private nonce to use.
     * @param string $cardId The card ID to associate with the keys.
     * @return array The ephemeral key details.
     */

    public function ephemeralKeys($nonce, $cardId)
    {
        try {
            $stripe       = new StripeClient($this->getStripeKey());
            $ephemeralKey = $stripe->ephemeralKeys->create([
                'nonce'        => $nonce,
                'issuing_card' => $cardId,
            ], [
                'stripe_version' => '2022-08-01',
            ]);
            return [
                'ephemeralKey' => $ephemeralKey,
                'ephemeralKeySecret' => $ephemeralKey->secret,
                'id' => $ephemeralKey->id
            ];
        } catch (UnexpectedValueException $e) {
            throw new Exception($e->getMessage());
        } catch (SignatureVerificationException $e) {
            throw new Exception($e->getMessage());
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieves the Stripe API secret key.
     *
     * This method returns the Stripe secret key used for API requests. The secret
     * key is required for authorization when making requests to the Stripe API.
     *
     * @return string The Stripe secret API key.
     */
    private function getStripeKey()
    {
        $gateway    = Gateway::automatic()->with('currencies', 'currencies.method')->where('alias', "StripeV3")->first();
        if (!$gateway) {
            throw new Exception('The virtual card provide is not fund');
        }
        $parameters = collect(json_decode($gateway->gateway_parameters));

        if (!@$parameters || !@$parameters['secret_key']->value) {
            throw new Exception('The problem found to api key');
        }

        return $parameters['secret_key']->value;
    }

    private function stripeAllCategories()
    {
        return [
            "ac_refrigeration_repair",
            "accounting_bookkeeping_services",
            "advertising_services",
            "agricultural_cooperative",
            "airlines_air_carriers",
            "airports_flying_fields",
            "ambulance_services",
            "amusement_parks_carnivals",
            "antique_reproductions",
            "antique_shops",
            "aquariums",
            "architectural_surveying_services",
            "art_dealers_and_galleries",
            "artists_supply_and_craft_shops",
            "auto_body_repair_shops",
            "auto_paint_shops",
            "auto_service_shops",
            "auto_and_home_supply_stores",
            "automated_cash_disburse",
            "automated_fuel_dispensers",
            "automobile_associations",
            "automotive_parts_and_accessories_stores",
            "automotive_tire_stores",
            "bail_and_bond_payments",
            "bakeries",
            "bands_orchestras",
            "barber_and_beauty_shops",
            "betting_casino_gambling",
            "bicycle_shops",
            "billiard_pool_establishments",
            "boat_dealers",
            "boat_rentals_and_leases",
            "book_stores",
            "books_periodicals_and_newspapers",
            "bowling_alleys",
            "bus_lines",
            "business_secretarial_schools",
            "buying_shopping_services",
            "cable_satellite_and_other_pay_television_and_radio",
            "camera_and_photographic_supply_stores",
            "candy_nut_and_confectionery_stores",
            "car_rental_agencies",
            "car_washes",
            "car_and_truck_dealers_new_used",
            "car_and_truck_dealers_used_only",
            "carpentry_services",
            "carpet_upholstery_cleaning",
            "caterers",
            "charitable_and_social_service_organizations_fundraising",
            "chemicals_and_allied_products",
            "child_care_services",
            "childrens_and_infants_wear_stores",
            "chiropodists_podiatrists",
            "chiropractors",
            "cigar_stores_and_stands",
            "civic_social_fraternal_associations",
            "cleaning_and_maintenance",
            "clothing_rental",
            "colleges_universities",
            "commercial_equipment",
            "commercial_footwear",
            "commercial_photography_art_and_graphics",
            "commuter_transport_and_ferries",
            "computer_network_services",
            "computer_programming",
            "computer_repair",
            "computer_software_stores",
            "computers_peripherals_and_software",
            "concrete_work_services",
            "construction_materials",
            "consulting_public_relations",
            "correspondence_schools",
            "cosmetic_stores",
            "counseling_services",
            "country_clubs",
            "courier_services",
            "court_costs",
            "credit_reporting_agencies",
            "cruise_lines",
            "dairy_products_stores",
            "dance_hall_studios_schools",
            "dating_escort_services",
            "dentists_orthodontists",
            "department_stores",
            "detective_agencies",
            "digital_goods_media",
            "digital_goods_applications",
            "digital_goods_games",
            "digital_goods_large_volume",
            "direct_marketing_catalog_merchant",
            "direct_marketing_combination_catalog_and_retail_merchant",
            "direct_marketing_inbound_telemarketing",
            "direct_marketing_insurance_services",
            "direct_marketing_other",
            "direct_marketing_outbound_telemarketing",
            "direct_marketing_subscription",
            "direct_marketing_travel",
            "discount_stores",
            "doctors",
            "door_to_door_sales",
            "drapery_window_covering_and_upholstery_stores",
            "drinking_places",
            "drug_stores_and_pharmacies",
            "drugs_drug_proprietaries_and_druggist_sundries",
            "dry_cleaners",
            "durable_goods",
            "duty_free_stores",
            "eating_places_restaurants",
            "educational_services",
            "electric_razor_stores",
            "electric_vehicle_charging",
            "electrical_parts_and_equipment",
            "electrical_services",
            "electronics_repair_shops",
            "electronics_stores",
            "elementary_secondary_schools",
            "emergency_services_gcas_visa_use_only",
            "employment_temp_agencies",
            "equipment_rental",
            "exterminating_services",
            "family_clothing_stores",
            "fast_food_restaurants",
            "financial_institutions",
            "fines_government_administrative_entities",
            "fireplace_fireplace_screens_and_accessories_stores",
            "floor_covering_stores",
            "florists",
            "florists_supplies_nursery_stock_and_flowers",
            "freezer_and_locker_meat_provisioners",
            "fuel_dealers_non_automotive",
            "funeral_services_crematories",
            "furniture_repair_refinishing",
            "furniture_home_furnishings_and_equipment_stores_except_appliances",
            "furriers_and_fur_shops",
            "general_services",
            "gift_card_novelty_and_souvenir_shops",
            "glass_paint_and_wallpaper_stores",
            "glassware_crystal_stores",
            "golf_courses_public"
        ];
    }

    /**
     * Function to upload an image to Stripe (returns the image file ID)
     */
    private function uploadImageToStripe($image)
    {

        try {
            $file = file_get_contents($image);
            $stripeFile = File::create([
                'purpose' => 'identity_document',
                'file'    => fopen($image, 'r'),
            ]);
            return $stripeFile->id;
        } catch (Exception $e) {
            throw new Exception("Failed to upload image to Stripe: " . $e->getMessage());
        }
    }

    // Function to retrieve the file URL from Stripe
    public function getFileUrlFromStripe($fileId)
    {
        try {
            $file = File::retrieve($fileId);
            return $file->url;  // Returns the URL of the uploaded file
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve file from Stripe: " . $e->getMessage());
        }
    }
}
