<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\AdminGetTDSCommission;
use App\Models\UserModel\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminGetTDSCommissionController extends Controller
{
    public function getTdsGst(Request $request)
    {
        $query = AdminGetTDSCommission::query();

        if ($request->filled('searchString')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->searchString . '%');
            });
        }

        if ($request->filled('orderType')) {
            if ($request->orderType == 'pending') {
                $query->where('status', 0);
            } elseif ($request->orderType == 'approve') {
                $query->where('status', 1);
            } elseif ($request->orderType == 'reject') {
                $query->where('status', 2);
            }
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        $AdminGetTDScomm = $query->orderBy('id', 'desc')->get();
        return view('pages.admin-get-tds-comm', compact('AdminGetTDScomm'));
    }

    public function changeStatus($id, $action)
    {
        $record = AdminGetTDSCommission::findOrFail($id);

        if ($action === 'approve') {
            $record->status = 1;
            $record->reject_reason = null;
            $record->save();
            return back()->with('success', 'Withdrawal request approved successfully!');
        }

        return back();
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:admin_get_tds_comm,id',
            'reject_reason' => 'required|string|max:255',
        ]);

        $record = AdminGetTDSCommission::findOrFail($request->id);
        $record->status = 2;
        $record->reject_reason = $request->reject_reason;
        $record->save();
        UserWallet::where('userId',$record->userId)->increment('amount',$record->amount);
        return back()->with('success', 'Withdrawal request rejected and amount returned to user wallet!');
    }
}
