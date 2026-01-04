<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use PDF;

class GlobalReportController extends Controller
{
    // public function downloadPDF(Request $request)
    // {
    //     $tableData = json_decode($request->tableData, true);
    //     $title = $request->title ?? 'Data Report';
    //     $logo = public_path('build/assets/images/logo.png'); // site logo path

    //     $pdf = PDF::loadView('reports.dynamic_table_pdf', [
    //         'tableData' => $tableData,
    //         'title' => $title,
    //         'logo' => $logo
    //     ]);

    //     $pdf->setPaper('A4', 'portrait');

    //     return $pdf->download($title . '_' . now()->format('Ymd_His') . '.pdf');
    // }
}
