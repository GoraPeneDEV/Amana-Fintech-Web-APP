<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\UserAction;


class AuthorizedTransactionManager
{
    public function process($remark)
    {   
        $userAction = UserAction::where('user_id', auth()->id())
            // ->where('is_used', Status::NO)
            ->orderBy('id', 'desc')
            ->where('remark', $remark)
            ->where('platform', isApiRequest() ? Status::PLATFORM_APP : Status::PLATFORM_WEB);

        if (gs('otp_verification')) {
            $userAction = $userAction->whereNotNull('expired_at');
        }

        $userAction = $userAction->first();


        if (!$userAction) {
            return apiResponse("error", "error", ["The something went wrong"]);
        }

        $userAction->is_used = Status::YES;
        $userAction->used_at = now();
        $userAction->save();

        $className = $this->getClassName($remark);
        return (new $className())->store($userAction);
    }

    private function getClassName($remark)
    {
        return [
            'send_money'             => AuthorizeSendMoney::class,
            'make_payment'           => AuthorizeMakePayment::class,
            'request_money'          => AuthorizeRequestMoney::class,
            'request_money_received' => AuthorizeRequestMoneyReceived::class,
            'donation'               => AuthorizeDonation::class,
            'cash_out'               => AuthorizeCashOut::class,
            'utility_bill'           => AuthorizeUtilityBill::class,
            'education_fee'          => AuthorizeEducationFee::class,
            'microfinance'           => AuthorizeMicrofinance::class,
            'bank_transfer'          => AuthorizeBankTransfer::class,
            'education_fee'          => AuthorizeEducationFee::class,
            'mobile_recharge'        => AirTime::class,
            'gift_card'              => AuthorizeGiftCard::class,
            'investment'              => AuthorizeInvestment::class,
        ][$remark];
    }
}
