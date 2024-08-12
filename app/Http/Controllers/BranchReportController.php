<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchReport;
use App\Models\InitialBakerreports;
use App\Models\SalesReports;
use Illuminate\Http\Request;

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
        // Validate branchId
        $branch = Branch::find($branchId);
        if (!$branch) {
            return response()->json(['message' => 'Branch not found'], 404);
        }

        // Get the latest report dates for the branch
        $latestBranchReport = SalesReports::where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->get();

        $branchReports = [];

        foreach ($latestBranchReport as $branchReport) {
            $date = $branchReport->created_at->toDateString();

            // Fetch SalesReports and InitialBakerreports for the specific date
            $salesReports = SalesReports::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->with(['breadReports', 'selectaReports', 'softdrinksReports', 'expensesReports', 'denominationReports'])
                ->get();

            $bakerReports = InitialBakerreports::where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->with(['breadBakersReports', 'ingredientBakersReports', 'fillingBakersReports', 'breadProductionReports'])
                ->get();

            $branchReports[] = [
                'date' => $date,
                'branch_name' => $branch->name,
                'sales_reports' => $salesReports,
                'baker_reports' => $bakerReports,
            ];
        }

        // Prepare response data
        $data = [
            'branch_id' => $branchId,
            'reports' => $branchReports
        ];

        if (!empty($branchReports)) {
            return response()->json($branchReports);
        } else {
            return response()->json(['message' => 'No reports found'], 404);
        }
    }

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
