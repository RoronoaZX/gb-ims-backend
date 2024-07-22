<?php

namespace App\Http\Controllers;

use App\Models\BranchEmployee;
use App\Models\BranchProduct;
use App\Models\SalesReports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer|exists:branches,id',
            'user_id' => 'required|integer|exists:users,id',
            'denomination_total' => 'required|integer',
            'expenses_total' => 'required|integer',
            'products_total_sales' => 'required|integer',
            'charges_amount' => 'required|integer',
            'breadReports' => 'required|array',
            'selectaReports' => 'required|array',
            'softdrinksReports' => 'required|array',
            'expensesReports' => 'required|array',
            'denominationReports' => 'required|array',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $salesReport = SalesReports::create([
            'branch_id' => $request->branch_id,
            'user_id' => $request->user_id,
            'denomination_total' => $request->denomination_total,
            'expenses_total' => $request->expenses_total,
            'products_total_sales' => $request->products_total_sales,
            'charges_amount' => $request->charges_amount,
        ]);

        foreach ($request->breadReports as $breadReport) {
            $salesReport->breadReports()->create($breadReport);

            $branchProduct = BranchProduct::where('branches_id', $request->branch_id)
            ->where('product_id', $breadReport['product_id'])
            ->first();

            if ($branchProduct) {
                $branchProduct->beginnings = $breadReport['remaining'];
                $branchProduct->total_quantity = $breadReport['remaining'];
                $branchProduct->save();
            }

        }

        // Store Selecta Reports
        foreach ($request->selectaReports as $selectaReport) {
            $salesReport->selectaReports()->create($selectaReport);

            $branchProduct = BranchProduct::where('branches_id', $request->branch_id)
            ->where('product_id', $selectaReport['product_id'])
            ->first();

        if ($branchProduct) {
            $branchProduct->beginnings = $selectaReport['remaining'];
            $branchProduct->total_quantity = $selectaReport['remaining'];
            $branchProduct->save();
        }
        }

        // Store Softdrinks Reports
        foreach ($request->softdrinksReports as $softdrinksReport) {
            $salesReport->softdrinksReports()->create($softdrinksReport);

            $branchProduct = BranchProduct::where('branches_id', $request->branch_id)
            ->where('product_id', $softdrinksReport['product_id'])
            ->first();

        if ($branchProduct) {
            $branchProduct->beginnings = $softdrinksReport['remaining'];
            $branchProduct->total_quantity = $softdrinksReport['remaining'];
            $branchProduct->save();
        }
        }

        // Store Expenses Reports
        foreach ($request->expensesReports as $expensesReport) {
            $salesReport->expensesReports()->create($expensesReport);
        }

        // Store Denomination Reports
        foreach ($request->denominationReports as $denominationReport) {
            $salesReport->denominationReports()->create($denominationReport);
        }

        return response()->json(['message' => 'Sales report created successfully', 'salesReport' => $salesReport], 201);

    }

    public function show(SalesReports $salesReports)
    {
        //
    }


    public function edit(SalesReports $salesReports)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SalesReports  $salesReports
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalesReports $salesReports)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalesReports  $salesReports
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesReports $salesReports)
    {
        //
    }
}
