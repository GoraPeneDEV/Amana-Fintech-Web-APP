<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BankTransfer;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\User;
use Illuminate\Http\Request;

class BankTransferController extends Controller
{
    public function pending()
    {
        $pageTitle    = 'Pending Bank Transfers';
        $transferData = $this->bankTransferData("pending");
        $transfers    = $transferData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "BankTransfer", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $transferData['widget'];

        return view('admin.bank_transfer.history', compact('pageTitle', 'transfers', 'widget'));
    }

    public function approved()
    {
        $pageTitle    = 'Approved Bank Transfers';
        $transferData = $this->bankTransferData("approved");
        $transfers    = $transferData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "BankTransfer", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $transferData['widget'];

        return view('admin.bank_transfer.history', compact('pageTitle', 'transfers', 'widget'));
    }

    public function rejected()
    {
        $pageTitle    = 'Rejected Bank Transfers';
        $transferData = $this->bankTransferData("rejected");
        $transfers    = $transferData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "BankTransfer", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $transferData['widget'];

        return view('admin.bank_transfer.history', compact('pageTitle', 'transfers', 'widget'));
    }

    public function all()
    {
        $pageTitle    = 'All Bank Transfers';
        $transferData = $this->bankTransferData();
        $transfers    = $transferData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "BankTransfer", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $transferData['widget'];

        return view('admin.bank_transfer.history', compact('pageTitle', 'transfers', 'widget'));
    }

    private function bankTransferData($scope = 'query')
    {

        $widget = [
            'pending'           => BankTransfer::pending()->sum('amount'),
            'approved'          => BankTransfer::approved()->sum('amount'),
            'rejected'          => BankTransfer::rejected()->sum('amount'),
            'all'               => BankTransfer::sum('amount'),
            'today_charge'      => BankTransfer::approved()->whereDate('created_at', now()->today())->sum('charge'),
            'yesterday_charge'  => BankTransfer::approved()->whereDate('created_at', now()->yesterday())->sum('charge'),
            'this_month_charge' => BankTransfer::approved()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('charge'),
            'all_charge'        => BankTransfer::approved()->sum('charge'),
        ];

        $query     = BankTransfer::$scope();
        $transfers = $query->searchable(['user:username', 'bank:name', 'trx'])->dateFilter()->with('bank', 'user', 'getTrx')->orderBy('id', getOrderBy());

        return [
            'data'    => $transfers,
            'widget'  => $widget,
        ];
    }

    public function approve($id)
    {
        $bankTransfer = BankTransfer::where('status', Status::PENDING)->findOrFail($id);
        $bank         = $bankTransfer->bank;
        $getTrx       = $bankTransfer->getTrx;
        $user         = User::findOrFail($bankTransfer->user_id);

        $bankTransfer->status = Status::APPROVED;
        $bankTransfer->save();

        notify($user, 'BANK_TRANSFER_APPROVE', [
            'amount'         => showAmount($bankTransfer->amount, currencyFormat: false),
            'charge'         => showAmount($getTrx->charge, currencyFormat: false),
            'account_number' => $bankTransfer->account_number,
            'bank'           => $bank->name,
            'trx'            => $getTrx->trx,
            'time'           => showDateTime($bankTransfer->created_at),
            'post_balance'   => showAmount($getTrx->post_balance, currencyFormat: false),
            'username'       => $user->username,
            'fullname'       => $user->fullname
        ]);

        $notify[] = ['success', 'Bank transfer has been approved successfully'];
        return back()->withNotify($notify);
    }
    public function reject(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $bankTransfer = BankTransfer::where('status', Status::PENDING)->findOrFail($id);

        $bank         = $bankTransfer->bank;
        $user         = User::findOrFail($bankTransfer->user_id);

        $bankTransfer->status         = Status::REJECTED;
        $bankTransfer->admin_feedback = $request->message;
        $bankTransfer->save();

        $user->balance += $bankTransfer->total;
        $user->save();

        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $bankTransfer->total;
        $transaction->post_balance  = $user->balance;
        $transaction->charge        = 0;
        $transaction->trx_type      = '+';
        $transaction->remark        = 'reject_bank_transfer';
        $transaction->details       = 'Rejection of bank transfer';
        $transaction->trx           = $bankTransfer->trx;
        $transaction->save();

        notify($user, 'BANK_TRANSFER_REJECT', [
            'amount'         => showAmount($bankTransfer->amount, currencyFormat: false),
            'charge'         => showAmount($bankTransfer->charge, currencyFormat: false),
            'account_number' => $bankTransfer->account_number,
            'bank'           => $bank->name,
            'trx'            => $bankTransfer->trx,
            'reason'         => $request->message,
            'time'           => showDateTime($bankTransfer->created_at),
            'post_balance'   => showAmount($user->post_balance, currencyFormat: false),
            'username'       => $user->username,
            'fullname'       => $user->fullname
        ]);

        $notify[] = ['success', 'Bank transfer has been rejected successfully'];
        return back()->withNotify($notify);
    }

    public function chargeSetting()
    {
        $charge    = TransactionCharge::where('slug', "bank_transfer")->firstOrFail();
        $pageTitle = "Bank Transfer Charge & Limit Setting ";
        return view('admin.bank_transfer.charge_setting', compact('pageTitle', 'charge'));
    }

    public function updateCharges(Request $request)
    {
        $request->validate([
            'min_limit'                => 'required|numeric|gte:0',
            'max_limit'                => 'required|numeric|gt:min_limit',
            'fixed_charge'             => 'required|numeric|gte:0',
            'percent_charge'           => 'required|numeric|between:0,100',
            'cap'                      => 'required|numeric|gte:-1',
            'daily_limit'              => 'required|numeric|gte:-1',
            'monthly_limit'            => 'required|numeric|gte:-1',
        ]);

        if ($request->monthly_limit != -1 && $request->monthly_limit < $request->daily_limit) {
            $notify[] = ['error', 'The daily limit must not exceed the monthly limit.'];
            return back()->withNotify($notify);
        }

        $charge = TransactionCharge::where('slug', 'bank_transfer')->first();

        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "bank_transfer";
        }

        $charge->percent_charge           = $request->percent_charge ?? 0;
        $charge->fixed_charge             = $request->fixed_charge ?? 0;
        $charge->min_limit                = $request->min_limit ?? 0;
        $charge->max_limit                = $request->max_limit ?? 0;
        $charge->cap                      = $request->cap ?? 0;
        $charge->monthly_limit            = $request->monthly_limit ?? 0;
        $charge->daily_limit              = $request->daily_limit ?? 0;
        $charge->save();


        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
