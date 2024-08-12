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
        $branchRawMaterials = BranchRawMaterialsReport::orderBy('created_at', 'desc')->with('ingredients')->get();
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

    public function updateStocks(Request $request, $id)
    {
        $validateData = $request->validate([
            'total_quantity' => 'required|integer'
        ]);
        $branchRawMaterials = BranchRawMaterialsReport::findorFail($id);
        $branchRawMaterials->total_quantity = $validateData['total_quantity'];
        $branchRawMaterials->save();

        return response()->json(['message' => 'Stocks updated successfully', 'total_quantity' => $branchRawMaterials]);
    }

    public function destroy($id)
    {
        $branchRawMaterials = BranchRawMaterialsReport::find($id);

        if (!$branchRawMaterials) {
            return response()->json([
                'message' => 'Raw materials not found'
            ], 404);
        }

        $branchRawMaterials->delete();
        return response()->json([
            'message' => 'Raw materials deleted successfully'
        ]);

    }
}
