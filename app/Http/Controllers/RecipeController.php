<?php

namespace App\Http\Controllers;

use App\Models\BreadGroup;
use App\Models\IngredientGroups;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with(['breadGroups.bread', 'ingredientGroups.ingredient'])->get();

        $formattedRecipes = $recipes->map(function ($recipe) {
            return [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'category' => $recipe->category,
                'target' => $recipe->target,
                'bread_groups' => $recipe->breadGroups->pluck('bread.name'),
                'ingredient_groups' => $recipe->ingredientGroups->map(function ($ingredientGroup) {
                    return [
                        'ingredient_name' => $ingredientGroup->ingredient->name,
                        'quantity' => $ingredientGroup->quantity,
                        'unit' => $ingredientGroup->ingredient->unit
                    ];
                }),
            ];
        });

        return response()->json($formattedRecipes);
    }

    public function searchRecipe(Request $request)
    {

        $request->validate([
            'keyword' => 'required|string|max:255'
        ]);
        $keyword = $request->input('keyword');


       $recipes = Recipe::with(['breadGroups.bread', 'ingredientGroups.ingredient'])
                ->where('name', 'LIKE', "%{$keyword}%")->get();

                $formattedRecipes = $recipes->map(function ($recipe){
                    return [
                        'id' => $recipe->id,
                        'name' => $recipe->name,
                        'category' => $recipe->category,
                        'target' => $recipe->target,
                        'bread_groups' => $recipe->breadGroups->pluck('bread.name'),
                        'ingredients' => $recipe->ingredientGroups->map(function ($ingredientGroup) {
                            return [
                                'ingredient_name' => $ingredientGroup->ingredient->name,
                                'quantity' => $ingredientGroup->quantity,
                                'unit' => $ingredientGroup->ingredient->unit,
                            ];
                        })
                    ];
                });

                return response()->json($formattedRecipes);

    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:30',
            'target' => 'required|integer',
            'breads' => 'required|array',
            'breads.*.bread_id' => 'required|integer|exists:products,id',
            'ingredients' => 'required|array',
            'ingredients.*.ingredient_id' => 'required|integer|exists:raw_materials,id',
            'ingredients.*.quantity' => 'required|integer',
        ]);

        $recipe = Recipe::create($validatedData);

        $recipe->ingredientGroups()->createMany($validatedData['ingredients']);
        $recipe->breadGroups()->createMany($validatedData['breads']);

        return response()->json($recipe->load(['breadGroups', 'ingredientGroups']));
    }

    /**
     * Display the specified resource.
     */
    // public function show(Recipe $recipe)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Recipe $recipe)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Recipe $recipe)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json([
                'success' => false,
                'message' => 'Recipe not found'
            ], 404);
        }

        $recipe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recipe deleted successfully'
        ], 200);
    }


    public function updateTarget(Request $request, $id)
    {
        $validatedData = $request->validate([
            'target' => 'required|integer',
        ]);

        $recipe = Recipe::findOrFail($id);
        $recipe->target = $validatedData['target'];
        $recipe->save();

        return response()->json(['message' => 'Target updated successfully', 'recipe' => $recipe]);
    }
    public function updateName(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $recipe = Recipe::findOrFail($id);
        $recipe->name = $validatedData['name'];
        $recipe->save();

        return response()->json(['message' => 'Name updated successfully', 'recipe' => $recipe]);
    }
}
