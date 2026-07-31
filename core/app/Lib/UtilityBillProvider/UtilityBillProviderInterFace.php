<?php

namespace App\Lib\UtilityBillProvider;

/**
 * Interface UtilityBillProviderInterFace
 *
 * This interface defines the contract for utility bill service providers.
 * Implementing classes should handle the process of paying bills, fetching billers,
 * importing biller categories, and validating HTTP responses.
 */
interface UtilityBillProviderInterFace
{
    /**
     * Initiates the payment process for a utility bill.
     *
     * @param object $company  The company or service provider for the bill.
     * @param mixed $details  The payment details including amount, account, etc.
     * @return mixed
     */
    public function payBill($company, $details);

    /**
     * Returns the required HTTP headers for API requests to the biller service.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Imports or syncs available biller categories from the external provider.
     *
     * @return mixed
     */
    public function importBillerCategory();

    /**
     * Retrieves a list of billers from the external provider.
     *
     * @return mixed
     */
    public function getBillers($country);

    /**
     * Validates the HTTP response received from the biller API.
     *
     * @param mixed $response
     * @return bool
     */
    public function validateHttpResponse($response);

    /**
     * Updates the status or record of a bill after the payment is processed.
     *
     * @param object $bill
     * @return mixed
     */
    public function payBillUpdate($bill);
}
