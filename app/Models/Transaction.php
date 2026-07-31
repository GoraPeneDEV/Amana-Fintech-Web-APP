<?php
namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    use ApiQuery;

    protected $appends = ['total_amount'];

    protected $casts = [
        'amount'       => 'double',
        'charge'       => 'double',
        'post_balance' => 'double',
    ];

    public function exportColumns(): array {
        return [
            'user_id'      => [
                'name'     => "User",
                'callback' => function ($item) {
                    if ($item->user_id != 0 && $item->user) {
                        return $item->user->username;
                    } elseif ($item->agent_id != 0 && $item->agent) {
                        return $item->agent->username;
                    } elseif ($item->merchant_id != 0 && $item->merchant) {
                        return $item->merchant->username;
                    }
                    return 'N/A';
                },
            ],
            'trx',
            'created_at'   => [
                'name'     => "transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                },
            ],
            'amount'       => [
                'callback' => function ($item) {
                    return showAmount($item->amount);
                },
            ],
            'post_balance' => [
                'callback' => function ($item) {
                    return showAmount($item->post_balance);
                },
            ],
        ];
    }

    public function operator() {
        return $this->belongsTo(Operator::class);
    }

    public function mobileRecharge() {
        return $this->belongsTo(MobileRecharge::class, 'trx', 'trx');
    }

    public function merchantWithdrawal() {
        return $this->belongsTo(Withdrawal::class, 'trx', 'trx')->where('merchant_id', '!=', 0);
    }
    public function agentWithdrawal() {
        return $this->belongsTo(Withdrawal::class, 'trx', 'trx')->where('agent_id', '!=', 0);
    }

    public function userDeposit() {
        return $this->belongsTo(Deposit::class, 'trx', 'trx')->where('user_id', '!=', 0);
    }
    public function agentDeposit() {
        return $this->belongsTo(Deposit::class, 'trx', 'trx')->where('agent_id', '!=', 0);
    }

    public function bankTransfer() {
        return $this->belongsTo(BankTransfer::class, 'trx', 'trx');
    }

    public function educationFee() {
        return $this->belongsTo(EducationFee::class, 'trx', 'trx');
    }

    public function microfinance() {
        return $this->belongsTo(Microfinance::class, 'trx', 'trx');
    }
    public function donation() {
        return $this->belongsTo(Donation::class, 'trx', 'trx');
    }

    public function investment() {
        return $this->belongsTo(Investment::class, 'trx', 'trx');
    }

    public function investmentInterest() {
        return $this->belongsTo(InvestmentInterest::class, 'trx', 'trx');
    }

    public function topup() {
        return $this->belongsTo(Topup::class, 'trx', 'trx');
    }

    public function utilityBill() {
        return $this->belongsTo(UtilityBill::class, 'trx', 'trx');
    }

    public function donationFor() {
        return $this->belongsTo(Charity::class, 'charity_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function agent() {
        return $this->belongsTo(Agent::class);
    }

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public function virtualCard() {
        return $this->belongsTo(VirtualCard::class, 'virtual_card_id');
    }
    public function forVirtualCard() {
        return $this->belongsTo(VirtualCard::class, 'for_virtual_card_id');
    }
    public function sendMoney() {
        return $this->belongsTo(SendMoney::class, 'trx', 'trx');
    }
    public function cashOut() {
        return $this->belongsTo(CashOut::class, 'trx', 'trx');
    }

    public function cashOutCommission() {
        return $this->belongsTo(CashOut::class, 'trx', 'trx');
    }
    public function cashIn() {
        return $this->belongsTo(CashIn::class, 'trx', 'trx');
    }

    public function cashInCommission() {
        return $this->belongsTo(CashIn::class, 'trx', 'trx');
    }

    public function payment() {
        return $this->belongsTo(MakePayment::class, 'trx', 'trx');
    }
    public function moneyRequest() {
        return $this->belongsTo(MoneyRequest::class, 'trx', 'trx');
    }

    public function giftCardPurchase() {
        return $this->belongsTo(GiftCardPurchase::class, 'trx', 'trx');
    }

    public function totalAmount(): Attribute {
        return new Attribute(
            get: fn() => $this->remark == 'withdraw' ? ($this->amount - $this->charge) : ($this->charge + $this->amount)
        );
    }
}
