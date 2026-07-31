<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\EducationFee;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\User;
use Illuminate\Http\Request;

class EducationFeeController extends Controller
{
    public function pending()
    {
        $pageTitle = 'Pending Education Fee';
        $feeData   = $this->educationFeeData("pending");
        $fees      = $feeData['data'];

        if (request()->export) {
            return exportData($fees, request()->export, "EducationFee", "A4 landscape");
        }

        $fees = $fees->paginate(getPaginate());
        $widget    = $feeData['widget'];

        return view('admin.education_fee.history', compact('pageTitle', 'fees', 'widget'));
    }

    public function approved()
    {
        $pageTitle    = 'Approved Education Fee';
        $feeData = $this->educationFeeData("approved");
        $fees    = $feeData['data'];

        if (request()->export) {
            return exportData($fees, request()->export, "EducationFee", "A4 landscape");
        }

        $fees   = $fees->paginate(getPaginate());
        $widget = $feeData['widget'];

        return view('admin.education_fee.history', compact('pageTitle', 'fees', 'widget'));
    }

    public function rejected()
    {
        $pageTitle = 'Rejected Education Fee';
        $feeData   = $this->educationFeeData("rejected");
        $fees      = $feeData['data'];

        if (request()->export) {
            return exportData($fees, request()->export, "EducationFee", "A4 landscape");
        }

        $fees = $fees->paginate(getPaginate());
        $widget    = $feeData['widget'];

        return view('admin.education_fee.history', compact('pageTitle', 'fees', 'widget'));
    }

    public function all()
    {
        $pageTitle = 'All Education Fee';
        $feeData   = $this->educationFeeData();
        $fees      = $feeData['data'];

        if (request()->export) {
            return exportData($fees, request()->export, "EducationFee", "A4 landscape");
        }

        $fees = $fees->paginate(getPaginate());
        $widget    = $feeData['widget'];

        return view('admin.education_fee.history', compact('pageTitle', 'fees', 'widget'));
    }

    private function educationFeeData($scope = 'query')
    {

        $widget = [
            'pending'           => EducationFee::pending()->sum('amount'),
            'approved'          => EducationFee::approved()->sum('amount'),
            'rejected'          => EducationFee::rejected()->sum('amount'),
            'all'               => EducationFee::sum('amount'),
            'today_charge'      => EducationFee::approved()->whereDate('created_at', now()->today())->sum('charge'),
            'yesterday_charge'  => EducationFee::approved()->whereDate('created_at', now()->yesterday())->sum('charge'),
            'this_month_charge' => EducationFee::approved()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('charge'),
            'all_charge'        => EducationFee::approved()->sum('charge'),
        ];

        $query = EducationFee::$scope();
        $fees  = $query->searchable(['user:username', 'institution:name', 'trx'])->dateFilter()->with('institution', 'user', 'getTrx')->orderBy('id', getOrderBy());

        return [
            'data'    => $fees,
            'widget'  => $widget,
        ];
    }

    public function approve($id)
    {

        $educationFee      = EducationFee::where('status', Status::PENDING)->findOrFail($id);
        $setupEducationFee = $educationFee->institution;
        $user              = User::findOrFail($educationFee->user_id);

        $educationFee->status = Status::APPROVED;
        $educationFee->save();

        notify($user, 'EDUCATION_FEE_APPROVE', [
            'user'         => $user->fullname,
            'amount'       => showAmount($educationFee->amount, currencyFormat: false),
            'charge'       => showAmount($educationFee->charge, currencyFormat: false),
            'academy'      => $setupEducationFee->name,
            'trx'          => $educationFee->trx,
            'time'         => showDateTime($educationFee->created_at),
            'post_balance' => showAmount($educationFee->getTrx->post_balance, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Education fee has been approved successfully'];
        return back()->withNotify($notify);
    }
    public function reject(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $educationFee      = EducationFee::where('status', Status::PENDING)->findOrFail($id);
        $setupEducationFee = $educationFee->institution;
        $user              = User::findOrFail($educationFee->user_id);

        $educationFee->status         = Status::REJECTED;
        $educationFee->admin_feedback = $request->message;
        $educationFee->save();

        $user->balance += $educationFee->total;
        $user->save();

        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $educationFee->total;
        $transaction->post_balance  = $user->balance;
        $transaction->charge        = 0;
        $transaction->trx_type      = '+';
        $transaction->remark        = 'reject_education_fee';
        $transaction->details       = 'Rejection of education fee';
        $transaction->trx           = $educationFee->trx;
        $transaction->save();

        notify($user, 'EDUCATION_FEE_REJECT', [
            'user'         => $user->fullname,
            'amount'       => showAmount($educationFee->amount, currencyFormat: false),
            'charge'       => showAmount($educationFee->charge, currencyFormat: false),
            'academy'      => $setupEducationFee->name,
            'trx'          => $educationFee->trx,
            'reason'       => $request->message,
            'time'         => showDateTime($educationFee->created_at),
            'post_balance' => showAmount($educationFee->post_balance, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Education fee has been rejected successfully'];
        return back()->withNotify($notify);
    }

    public function chargeSetting()
    {
        $charge    = TransactionCharge::where('slug', "education_charge")->firstOrFail();
        $pageTitle = "Education Fee Charge & Limit Setting ";

        return view('admin.education_fee.charge_setting', compact('pageTitle', 'charge'));
    }

    public function updateCharges(Request $request)
    {
        $request->validate([
            'min_limit'      => 'required|numeric|gte:0',
            'max_limit'      => 'required|numeric|gt:min_limit',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
            'cap'            => 'required|numeric|gte:-1',
            'daily_limit'    => 'required|numeric|gte:-1',
            'monthly_limit'  => 'required|numeric|gte:-1',
        ]);

        if ($request->monthly_limit != -1 && $request->monthly_limit < $request->daily_limit) {
            $notify[] = ['error', 'The daily limit must not exceed the monthly limit.'];
            return back()->withNotify($notify);
        }

        $charge = TransactionCharge::where('slug', 'education_charge')->first();

        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "education_charge";
        }

        $charge->percent_charge = $request->percent_charge ?? 0;
        $charge->fixed_charge   = $request->fixed_charge ?? 0;
        $charge->min_limit      = $request->min_limit ?? 0;
        $charge->max_limit      = $request->max_limit ?? 0;
        $charge->cap            = $request->cap ?? 0;
        $charge->monthly_limit  = $request->monthly_limit ?? 0;
        $charge->daily_limit    = $request->daily_limit ?? 0;
        $charge->save();


        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
