<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\ApiProvider;
use App\Models\Company;
use App\Models\Transaction;
use App\Models\UtilityBill;
use Exception;

class AuthorizeUtilityBill
{
    public function store($userAction)
    {
        $user    = auth()->user();
        $details = $userAction->details;

        if (@$details->total_amount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $company = Company::where('id', $details->company_id)->first();

        if (!$company) {
            $notify[] = 'Sorry! Utility not found';
            return apiResponse("validation_error", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $provider = ApiProvider::whereJsonContains('module', 'utility_bill')->where('status->utility_bill', Status::YES)->first();

        if ($provider) {
            try {
                $className = 'App\\Lib\\UtilityBillProvider\\' . $provider->alias;
                $automaticBillProcess = (new $className())->payBill($company, $details);

                if ($automaticBillProcess['processing_status']) {
                    return $this->utilityBillTrx($user, $details, $company, $provider, $automaticBillProcess['message'], $automaticBillProcess['bill_status'], $automaticBillProcess['api_provider_trx']);
                } else {
                    $notify[] = "The bill payment process failed. Please try again later or contact support";
                    return apiResponse("utility_bill_failed", "error", $notify);
                }
            } catch (Exception $ex) {
                $notify[] = "The bill payment process failed. Please try again later or contact support. Error: " . $ex->getMessage();
                return apiResponse("utility_bill_failed", "error", $notify);
            }
        } else {
            return $this->utilityBillTrx($user, $details, $company, false);
        }
    }


    private function  utilityBillTrx($user, $details, $company, $apiProvider = null, $notifyMessage = null, $billStatus = Status::UTILITY_BILL_PENDING, $apiProviderTrx = null)
    {
        $user->balance -= $details->total_amount;
        $user->save();

        $utilityBill                   = new UtilityBill();
        $utilityBill->user_id          = $user->id;
        $utilityBill->company_id       = $company->id;
        $utilityBill->amount           = $details->amount;
        $utilityBill->charge           = $details->total_charge;
        $utilityBill->total            = $details->total_amount;
        $utilityBill->trx              = generateUniqueTrxNumber();
        $utilityBill->unique_id        = $details->unique_id;
        $utilityBill->reference        = $details->reference;
        $utilityBill->api_provider_id  = @$apiProvider->id ?? 0;
        $utilityBill->status           = $billStatus;
        $utilityBill->api_provider_trx = $apiProviderTrx;
        $utilityBill->rate             = $details->rate;
        $utilityBill->cron_ordering    = time();
        $utilityBill->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $details->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $details->total_charge;
        $transaction->trx_type     = '-';
        $transaction->remark       = 'utility_bill';
        $transaction->details      = 'Utility bill';
        $transaction->trx          = $utilityBill->trx;
        $transaction->save();


        if (Status::UTILITY_BILL_SUCCESSFUL == $billStatus) {
            notify($user, 'UTILITY_BILL_APPROVE', [
                'user'         => $user->fullname,
                'amount'       => showAmount($utilityBill->amount, currencyFormat: false),
                'charge'       => showAmount($utilityBill->charge, currencyFormat: false),
                'utility'      => $company->name,
                'trx'          => $utilityBill->trx,
                'time'         => showDateTime($utilityBill->created_at),
                'post_balance' => showAmount($utilityBill->getTrx->post_balance, currencyFormat: false),
            ]);

            $adminMessage = "An utility bill successful via " . $apiProvider->title;
        } else {
            $adminMessage = 'New utility bill request from ' . $user->username;
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = $adminMessage;
        $adminNotification->click_url = urlPath('admin.utility.bill.all');
        $adminNotification->save();

        $notify[] = $notifyMessage ?? 'Successfully complete the utility bill process';

        return apiResponse("utility_bill_done", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.utility.bill.details', $utilityBill->id),
            'bill'          => $utilityBill
        ]);
    }
}
