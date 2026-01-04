<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;
use Carbon\Carbon;

class KundaliReportController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getKundaliEarnings(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $kundali = DB::table('kundalis')
                ->join('users', 'users.id', '=', 'kundalis.createdBy')
                ->leftjoin('astrologers', 'astrologers.userId', '=', 'users.id')
                ->select([
                    'users.name as userName',
                    'users.contactNo as userContactNo',
                    'kundalis.pdf_type as kundaliType',
                    'kundalis.created_at',
                    'kundalis.pdf_link',
                    DB::raw('IF(astrologers.userId IS NOT NULL, "Astrologer", "User") as user_type')
                ])
                ->orderBy('kundalis.id', 'DESC');

                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $kundali = $kundali->where(function ($q) use ($searchString) {
                        $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('kundalis.pdf_type', 'LIKE', '%' . $searchString . '%');
                    });
                }

                $countQuery = clone $kundali;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $kundali->whereBetween('kundalis.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('kundalis.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $kundali->where('kundalis.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('kundalis.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $kundali->where('kundalis.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('kundalis.created_at', '<=', $to_date . ' 23:59:59');
                }


                $kundali = $kundali->skip($paginationStart);
                $kundali = $kundali->take($this->limit);
                $kundaliEarnings = $kundali->get();
                $kundaliCount = DB::table('kundalis')
                    ->join('users', 'users.id', '=', 'kundalis.createdBy')
                    ->select('users.name as userName','users.contactNo as userContactNo', 'kundalis.pdf_type as kundaliType');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $kundaliCount = $kundaliCount->where(function ($q) use ($searchString) {
                        $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');

                    });
                }
                $kundaliCount = $kundaliCount->count();
                $totalPages = ceil($kundaliCount / $this->limit);
                $totalRecords = $kundaliCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.kundali-earnings', compact('kundaliEarnings', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page','from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

     #-------kundali Prices--------------------------------

    public function kundaliPrices(Request $request)
    {
        try {

            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $kundaliAmount = DB::table('kundali_prices');
                $kundaliAmount->orderBy('id', 'DESC');
                $kundaliAmount->skip($paginationStart);
                $kundaliAmount->take($this->limit);
                $kundaliAmountCount = $kundaliAmount->count();
                $kundaliAmount = $kundaliAmount->get();


                $totalPages = ceil($kundaliAmountCount / $this->limit);
                $totalRecords = $kundaliAmountCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.kundali-prices', compact('kundaliAmount', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }


    public function editkundaliAmount(Request $req)
    {
        // dd($req->all());
        try {
            if (Auth::guard('web')->check()) {
                $amount = DB::table('kundali_prices')->find($req['filed_id']);


                if ($amount) {
                    DB::table('kundali_prices')
                        ->where('id', $req['filed_id'])
                        ->update([
                            'price' => $req['price'],
                            'price_usd' => $req['price_usd'],

                        ]);

                    return redirect()->back();
                } else {
                    return redirect()->back()->with('error', 'Record not found');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
