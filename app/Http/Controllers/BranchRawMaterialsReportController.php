<?php

namespace App\Http\Controllers;

use App\Models\BranchRawMaterialsReport;
use Illuminate\Http\Request;

class BranchRawMaterialsReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branchRawMaterials = BranchRawMaterialsReport::with('ingredients')->get();
        return $branchRawMaterials;
    }

    public function getRawMaterials($branchId)
    {
        $branchRawMaterials = BranchRawMaterialsReport::where('branch_id', $branchId)->with(['branch', 'ingredients'])->get();

        return response()->json($branchRawMaterials, 200);
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
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'ingredients_id' => 'required|exists:products,id',
            'total_quantity' => 'required|numeric',
        ]);

        $existingBranchRawMaterials = BranchRawMaterialsReport::where('branch_id', $validatedData['branch_id'])->where('ingredients_id', $validatedData['ingredients_id'])->first();

        if ($existingBranchRawMaterials) {
            return response()->json([
                'message' => 'The RawMaterials already exists in this branch.'
            ]);
        }

        $branchRawMaterials = BranchRawMaterialsReport::create([
            'branch_id' => $validatedData['branch_id'],
            'ingredients_id' => $validatedData['ingredients_id'],
            'total_quantity' => $validatedData['total_quantity'],
        ]);

        return response()->json([
            'message' => "Branch Raw Materials saved successfully",
            'data' => $branchRawMaterials
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BranchRawMaterialsReport  $branchRawMaterialsReport
     * @return \Illuminate\Http\Response
     */
    public function show(BranchRawMaterialsReport $branchRawMaterialsReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BranchRawMaterialsReport  $branchRawMaterialsReport
     * @return \Illuminate\Http\Response
     */
    public function edit(BranchRawMaterialsReport $branchRawMaterialsReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BranchRawMaterialsReport  $branchRawMaterialsReport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BranchRawMaterialsReport $branchRawMaterialsReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BranchRawMaterialsReport  $branchRawMaterialsReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchRawMaterialsReport $branchRawMaterialsReport)
    {
        //
    }
}
