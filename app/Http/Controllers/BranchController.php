<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('warehouse')->get();

        return response()->json($branches, 200);
    }


    public function show($id)
    {
        $branch = Branch::where('id', $id)->with('branch_products')->first();
        return response()->json($branch );
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'person_incharge' => 'required',
            'name' => 'required||unique:branches',
            'location' => 'nullable',
            'phone' => 'nullable',
            'status' => 'nullable',
        ]);

        $branch = Branch::create($validatedData);
        return response()->json($branch, 201);
    }

    public function update(Request $request, $id)
    {
        Log::info("Received ID: $id");

        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json([
                'message' => 'Branch not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'person_incharge' => 'sometimes|required',
            'name' => 'sometimes|required|unique:branches,name,' . $id,
            'location' => 'nullable',
            'phone' => 'nullable',
            'status' => 'nullable',
        ]);

        $branch->update($validatedData);
        $updated_branch = $branch->fresh();
        return response()->json($updated_branch);
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json([
                'message' => 'Branch not found'
            ], 404);
        }
        $branch->delete();
        return response()->json([
            'message' => 'Branch deleted successfully'
        ]);
    }
}
