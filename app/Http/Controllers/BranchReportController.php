<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchReport;
use App\Models\InitialBakerreports;
use App\Models\SalesReports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all unique branch IDs
        $branches = InitialBakerreports::select('branch_id')
                        ->distinct()
                        ->get();

        $data = [];

        foreach ($branches as $branch) {
            $branchId = $branch->branch_id;

            // Get the latest report date for each branch
            $latestBranchReport = InitialBakerreports::where('branch_id', $branchId)
                                   ->orderBy('created_at', 'desc')
                                   ->get();

            $branchReports = [];

            foreach ($latestBranchReport as $bakerReports) {
                $date = $bakerReports->created_at->toDateString();

                $salesReports = SalesReports::where('branch_id', $branchId)
                                ->whereDate('created_at' , $date)
                                ->with(['breadReports', 'selectaReports', 'softdrinksReports', 'expensesReports', 'denominationReports'])
                                ->get();

                $bakerReports = InitialBakerreports::where('branch_id', $branchId)
                                ->whereDate('created_at', $date)
                                ->with(['breadBakersReports', 'ingredientBakersReports', 'fillingBakersReports', 'breadProductionReports'])
                                ->get();

                $branchReports[] = [
                    'date' => $date,
                    'sales_reports' => $salesReports,
                    'baker_reports' => $bakerReports
                ];
            }

            $data[] = [
                'branch_id' => $branchId,
                'reports' => $branchReports
            ];
        }

        if (!empty($data)) {
            return response()->json($data);
        } else {
            return response()->json(['message' => 'No reports found'], 404);
        }
    }

    public function fetchBranchReport($branchId)
    {
        $branch = Branch::find($branchId);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        // Fetch unique dates from both SalesReports and InitialBakerreports in UTC and convert to local time zone
        $dates = DB::table('sales_reports')
            ->select(DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "+08:00")) as date'))
            ->where('branch_id', $branchId)
            ->union(
                DB::table('initial_bakerreports')
                    ->select(DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "+08:00")) as date'))
                    ->where('branch_id', $branchId)
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->pluck('date');

        $branchReports = [];

        foreach ($dates as $date) {
            // Convert the date string back to a Carbon instance in the Philippine timezone
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Manila');

            // Fetch Sales Reports for AM and PM
            $amSalesReports = SalesReports::where('branch_id', $branchId)
                ->whereDate(DB::raw('CONVERT_TZ(created_at, "+00:00", "+08:00")'), $carbonDate)
                ->get()
                ->filter(function($report) {
                    $localTime = Carbon::parse($report->created_at)->setTimezone('Asia/Manila');
                    return $localTime->hour < 12;
                })
                ->load(['user', 'branch', 'breadReports', 'selectaReports', 'softdrinksReports', 'expensesReports', 'denominationReports', 'creditReports']);

            $pmSalesReports = SalesReports::where('branch_id', $branchId)
                ->whereDate(DB::raw('CONVERT_TZ(created_at, "+00:00", "+08:00")'), $carbonDate)
                ->get()
                ->filter(function($report) {
                    $localTime = Carbon::parse($report->created_at)->setTimezone('Asia/Manila');
                    return $localTime->hour >= 12;
                })
                ->load(['user', 'branch', 'breadReports', 'selectaReports', 'softdrinksReports', 'expensesReports', 'denominationReports', 'creditReports']);

            // Fetch Baker Reports for AM and PM
            $amBakerReports = InitialBakerreports::where('branch_id', $branchId)
                ->whereDate(DB::raw('CONVERT_TZ(created_at, "+00:00", "+08:00")'), $carbonDate)
                ->get()
                ->filter(function($report) {
                    $localTime = Carbon::parse($report->created_at)->setTimezone('Asia/Manila');
                    return $localTime->hour < 12;
                })
                ->load(['user','branch', 'breadBakersReports', 'ingredientBakersReports', 'fillingBakersReports', 'breadProductionReports', 'recipe']);

            $pmBakerReports = InitialBakerreports::where('branch_id', $branchId)
                ->whereDate(DB::raw('CONVERT_TZ(created_at, "+00:00", "+08:00")'), $carbonDate)
                ->get()
                ->filter(function($report) {
                    $localTime = Carbon::parse($report->created_at)->setTimezone('Asia/Manila');
                    return $localTime->hour >= 12;
                })
                ->load(['user', 'branch', 'breadBakersReports', 'ingredientBakersReports', 'fillingBakersReports', 'breadProductionReports', 'recipe']);

            // Group the reports by date
            $branchReports[] = [
                'date' => $carbonDate->toDateString(),

                'AM' => [
                    'sales_reports' => $amSalesReports,
                    'baker_reports' => $amBakerReports,
                    'date' => $carbonDate->toDateString(),
                'branch_name' => $branch->name,
                ],
                'PM' => [
                    'sales_reports' => $pmSalesReports,
                    'baker_reports' => $pmBakerReports,
                    'date' => $carbonDate->toDateString(),
                    'branch_name' => $branch->name,
                ],
            ];
        }

        if (!empty($branchReports)) {
            return response()->json($branchReports);
        } else {
            return response()->json(['message' => 'No reports found'], 404);
        }
    }
//     public function fetchBranchReport($branchId)
// {
//     $branch = Branch::find($branchId);
//     if (!$branch) {
//         return response()->json(['message' => 'Branch not found'], 404);
//     }

//     $latestBranchReport = SalesReports::where('branch_id', $branchId)
//         ->orderBy('created_at', 'desc')
//         ->get();

//     $branchReports = [];

//     foreach ($latestBranchReport as $branchReport) {
//         $date = $branchReport->created_at->toDateString();
//         $time = $branchReport->created_at->toTimeString();
//         $hour = $branchReport->created_at->hour;


//         $period = $hour < 12 ? 'AM' : 'PM';

//         $salesReports = SalesReports::where('branch_id', $branchId)
//             ->whereDate('created_at', $date)
//             ->whereTime('created_at', '>=', $period == 'AM' ? '00:00:00' : '12:00:00')
//             ->whereTime('created_at', '<', $period == 'AM' ? '12:00:00' : '23:59:59')
//             ->with(['user', 'breadReports', 'selectaReports', 'softdrinksReports', 'expensesReports', 'denominationReports'])
//             ->get();

//         $bakerReports = InitialBakerreports::where('branch_id', $branchId)
//             ->whereDate('created_at', $date)
//             ->whereTime('created_at', '>=', $period == 'AM' ? '00:00:00' : '12:00:00')
//             ->whereTime('created_at', '<', $period == 'AM' ? '12:00:00' : '23:59:59')
//             ->with(['user', 'breadBakersReports', 'ingredientBakersReports', 'fillingBakersReports', 'breadProductionReports', 'recipe'])
//             ->get();

//         if ($salesReports->isNotEmpty() || $bakerReports->isNotEmpty()) {
//             $branchReports[$period][] = [
//                 'date' => $date,
//                 'time' => $time,
//                 'branch_name' => $branch->name,
//                 'sales_reports' => $salesReports,
//                 'baker_reports' => $bakerReports,
//             ];
//         }
//     }

//     $data = [
//         'branch_id' => $branchId,
//         'reports' => $branchReports
//     ];

//     if (!empty($branchReports)) {
//         return response()->json($branchReports);
//     } else {
//         return response()->json(['message' => 'No reports found'], 404);
//     }
// }


    // public function fetchBranchReport($branchId)
    // {

    //     $branch = Branch::find($branchId);
    //     if (!$branch) {
    //         return response()->json(['message' => 'Branch not found'], 404);
    //     }

    //     $latestBranchReport = SalesReports::where('branch_id', $branchId)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     $branchReports = [];

    //     foreach ($latestBranchReport as $branchReport) {
    //         $date = $branchReport->created_at->toDateString();
    //         $time = $branchReport->created_at->toTimeString();


    //         $salesReports = SalesReports::where('branch_id', $branchId)
    //             ->whereDate('created_at', $date)
    //             ->with(['user','breadReports', 'selectaReports', 'softdrinksReports', 'expensesReports', 'denominationReports'])
    //             ->get();

    //         $bakerReports = InitialBakerreports::where('branch_id', $branchId)
    //             ->whereDate('created_at', $date)
    //             ->with(['user', 'breadBakersReports', 'ingredientBakersReports', 'fillingBakersReports', 'breadProductionReports', 'recipe'])
    //             ->get();

    //         $branchReports[] = [
    //             'date' => $date,
    //             'time' => $time,
    //             'branch_name' => $branch->name,
    //             'sales_reports' => $salesReports,
    //             'baker_reports' => $bakerReports,
    //         ];
    //     }


    //     $data = [
    //         'branch_id' => $branchId,
    //         'reports' => $branchReports
    //     ];

    //     if (!empty($branchReports)) {
    //         return response()->json($branchReports);
    //     } else {
    //         return response()->json(['message' => 'No reports found'], 404);
    //     }
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BranchReport  $branchReport
     * @return \Illuminate\Http\Response
     */
    public function show(BranchReport $branchReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BranchReport  $branchReport
     * @return \Illuminate\Http\Response
     */
    public function edit(BranchReport $branchReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BranchReport  $branchReport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BranchReport $branchReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BranchReport  $branchReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchReport $branchReport)
    {
        //
    }
}
