<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\GiftCardProvider\ReloadLy;
use App\Models\Country;
use App\Models\GiftCardCategory;
use App\Models\GiftCard;
use App\Models\GiftCardPurchase;
use Exception;
use Illuminate\Http\Request;

class GiftCardController extends Controller
{
    public function allCategory()
    {
        $pageTitle = 'Gift Card Categories';
        $baseQuery = GiftCardCategory::searchable(['name'])->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "GiftCardCategory");
        }

        $categories = $baseQuery->paginate(getPaginate());
        return view('admin.gift_card.categories', compact('pageTitle', 'categories'));
    }

    public function fetchCategories()
    {
        $pageTitle                 = 'Gift Card Categories';
        $existingCategoryUniqueIds = GiftCardCategory::pluck('unique_id')->toArray();

        try {
            $reloadly      = new ReloadLy();
            $apiCategories = $reloadly->getCategories();
        } catch (Exception $ex) {
            $notify[] = ['error', $ex->getMessage() ?? "Something went wrong, please try again"];
            return back()->withNotify($notify);
        }

        session()->put('gift_card_categories', $apiCategories);

        $apiCategories = collect($apiCategories);

        return view('admin.gift_card.fetch_categories', compact('pageTitle', 'existingCategoryUniqueIds', 'apiCategories'));
    }

    public function saveCategories(Request $request)
    {

        $request->validate([
            'categories' => 'required|array|min:1',
        ]);

        $categoryArray     = [];
        $requestCategories = collect(session('gift_card_categories'))->whereIn('name', $request->categories);

        foreach ($requestCategories as $item) {

            $item    = (object) $item;
            $category = GiftCardCategory::where('name', @$item->name)->first();

            if ($category) continue;

            $categoryArray[] = [
                'name'      => $item->name,
                'unique_id' => $item->id,
            ];
        }

        GiftCardCategory::insert($categoryArray);

        session()->forget('gift_card_categories');

        $notify[] = ['success', 'Gift Card Category added successfully'];
        return to_route('admin.gift.card.category.all')->withNotify($notify);
    }

    public function updateCategoryStatus($id)
    {
        return GiftCardCategory::changeStatus($id);
    }

    public function editCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:40|unique:gift_card_categories,name,' . $id,
        ]);

        $category       = GiftCardCategory::findOrFail($id);
        $category->name = $request->name;
        $category->save();

        $notify[] = ['success', 'Gift Card Category name updated successfully'];
        return back()->withNotify($notify);
    }

    public function countries()
    {
        $pageTitle = 'Gift Card Countries';
        $countries = Country::searchable(['name', 'iso_name', 'continent', 'currency_code'])->orderBy('name', getOrderBy())->giftCard()->paginate(getPaginate());

        if (session()->has('giftCardCountries')) session()->forget('giftCardCountries');

        return view('admin.gift_card.countries', compact('pageTitle', 'countries'));
    }

    public function fetchCountries()
    {
        $pageTitle            = 'Reloadly Supported Countries';
        $existingCountryCodes = Country::where('is_gift_card', Status::YES)->pluck('iso_name')->toArray();

        try {
            $reloadly     = new ReloadLy();
            $apiCountries = $reloadly->getCountries();
        } catch (Exception $ex) {
            $notify[] = ['error', $ex->getMessage() ?? "Something went wrong"];
            return back()->withNotify($notify);
        }

        session()->put('giftCardCountries', $apiCountries);
        $apiCountries = collect($apiCountries);

        return view('admin.gift_card.fetch_countries', compact('pageTitle', 'existingCountryCodes', 'apiCountries'));
    }

    public function saveCountries(Request $request)
    {

        $request->validate([
            'countries' => 'required|array|min:1',
        ]);

        $countryArray     = [];
        $requestCountries = collect(session('giftCardCountries'))->whereIn('isoName', $request->countries);

        foreach ($requestCountries as $item) {
            $item    = (object) $item;
            $country = Country::where('iso_name', @$item->isoName)->first();

            if ($country) {
                if ($country->is_gift_card == Status::NO) {
                    $country->is_gift_card = Status::YES;
                    $country->save();
                }
                continue;
            };
            $countryArray[] = [
                'name'            => $item->name,
                'iso_name'        => $item->isoName,
                'continent'       => @$item->continent ?? '',
                'currency_code'   => $item->currencyCode,
                'currency_name'   => $item->currencyName,
                'currency_symbol' => $item->currencySymbol ?? '',
                'flag_url'        => $item->flagUrl,
                'is_gift_card'    => Status::YES,
            ];
        }

        session()->forget('giftCardCountries');
        Country::insert($countryArray);

        $notify[] = ['success', 'Gift Card Country added successfully'];
        return to_route('admin.gift.card.countries')->withNotify($notify);
    }

    public function updateCountryStatus($id)
    {
        return Country::changeStatus($id);
    }

    public function allProducts($countryId)
    {
        $pageTitle = 'Gift Cards';
        $country = Country::where('is_gift_card', Status::YES)->findOrFail($countryId);
        $baseQuery = GiftCard::where('country_id', $country->id)->orderBy('id', getOrderBy())->active();

        if (request()->export) {
            return exportData($baseQuery, request()->export, "GiftCard");
        }

        $products = $baseQuery->paginate(getPaginate());

        return view('admin.gift_card.products', compact('pageTitle', 'products', 'country'));
    }

    public function fetchProducts($countryId)
    {
        $country   = Country::where('is_gift_card', Status::YES)->findOrFail($countryId);
        $pageTitle = 'Fetch Gift Cards: ' . $country->name;
        $reloadly  = new ReloadLy();
        $apiProducts = [];

        try {

            $products = $reloadly->getProducts($country->iso_name);

            if (!empty($products) && is_array($products)) {
                foreach ($products as $item) {
                    $apiProducts[] = $item;
                }
            }

            if (empty($apiProducts)) {
                $notify[] = ['error', 'No products found from Reloadly API'];
                return back()->withNotify($notify);
            }
        } catch (\Exception $ex) {
            $notify[] = ['error', $ex->getMessage() ?? "Something went wrong while fetching products"];
            return back()->withNotify($notify);
        }

        $existingProductIds = GiftCard::pluck('product_id')->toArray();

        session()->put('gift_card_products', $apiProducts);

        return view('admin.gift_card.fetch_products', compact('pageTitle', 'apiProducts', 'existingProductIds', 'country'));
    }


    public function saveProducts(Request $request, $countryId)
    {
        $request->validate([
            'products' => 'required|array|min:1',
        ]);

        $country          = Country::where('is_gift_card', Status::YES)->findOrFail($countryId);

        $apiProducts      = collect(session('gift_card_products'));
        $selectedProducts = $apiProducts->whereIn('productId', $request->products);
        $newProductCount  = 0;

        foreach ($selectedProducts as $item) {

            $existing = GiftCard::where('product_id', $item['productId'])->first();

            if ($existing) continue;

            $product                                = new GiftCard();
            $product->product_id                    = $item['productId'];
            $product->product_name                  = $item['productName'];
            $product->is_global                     = $item['global'];
            $product->status                        = $item['status'] == 'ACTIVE' ? Status::ENABLE : Status::DISABLE;
            $product->supports_pre_order            = $item['supportsPreOrder'];
            $product->sender_fee                    = $item['senderFee'];
            $product->sender_fee_percentage         = $item['senderFeePercentage'];
            $product->discount_percentage           = $item['discountPercentage'];
            $product->denomination_type             = $item['denominationType'];
            $product->recipient_currency_code       = $item['recipientCurrencyCode'];
            $product->min_recipient_denomination    = $item['minRecipientDenomination'];
            $product->max_recipient_denomination    = $item['maxRecipientDenomination'];
            $product->sender_currency_code          = $item['senderCurrencyCode'];
            $product->min_sender_denomination       = $item['minSenderDenomination'];
            $product->max_sender_denomination       = $item['maxSenderDenomination'];
            $product->fixed_recipient_denominations = $item['fixedRecipientDenominations'];
            $product->fixed_sender_denominations    = $item['fixedSenderDenominations'];
            $product->fixed_recipient_to_sender_map = $item['fixedRecipientToSenderDenominationsMap'];
            $product->metadata                      = $item['metadata'];
            $product->logo_urls                     = $item['logoUrls'];
            $product->country_id                    = $country->id;

            if (isset($item['brand'])) {
                $product->brand_id   = $item['brand']['brandId'];
                $product->brand_name = $item['brand']['brandName'];
            }

            if (isset($item['category'])) {
                $product->category_id   = $item['category']['id'];
                $product->category_name = $item['category']['name'];
            }

            if (isset($item['redeemInstruction'])) {
                $product->redeem_instruction_concise = $item['redeemInstruction']['concise'] ?? null;
                $product->redeem_instruction_verbose = $item['redeemInstruction']['verbose'] ?? null;
            }

            $product->user_id_required                  = $item['additionalRequirements']['userIdRequired'] ?? 0;
            $product->recipient_to_sender_exchange_rate = $item['recipientCurrencyToSenderCurrencyExchangeRate'];

            $product->save();
            $newProductCount++;
        }

        session()->forget('gift_card_products');

        if ($newProductCount == 0) {
            $notify[] = ['error', 'No new products were added'];
        } else {
            $notify[] = ['success', $newProductCount . ' new products saved successfully'];
        }

        return to_route('admin.gift.card.products.all', $country->id)->withNotify($notify);
    }


    public function updateProductStatus($id)
    {
        return GiftCard::changeStatus($id);
    }

    public function history()
    {
        $pageTitle  = 'Gift Card Purchase History';
        $baseQuery  = GiftCardPurchase::with('giftCard')->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "GiftCardPurchase", $printPageSize = "Letter landscape");
        }

        $transactions = $baseQuery->paginate(getPaginate());

        return view('admin.gift_card.history', compact('pageTitle', 'transactions'));
    }
}
