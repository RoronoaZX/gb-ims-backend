<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('register',[ApiController::class, 'register']);
Route::post('login',[ApiController::class, 'login']);
Route::group([
    "middleware" => ['auth:sanctum']
], function(){
    //profile
    Route::get('profile',[ApiController::class, 'profile']);

    //
    Route::get('logout',[ApiController::class, 'logout']);
});

Route::apiResource('users', UserController::class);
Route::apiResource('raw-materials', RawMaterialController::class);
Route::apiResource('warehouses', WarehouseController::class);
Route::apiResource('branches', BranchController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('recipes', RecipeController::class);
Route::get('search-recipes',[ RecipeController::class, 'searchRecipe']);
Route::put('update-name/{id}', [RecipeController::class, 'updateName']);
Route::put('update-target/{id}', [RecipeController::class, 'updateTarget']);
Route::get('bread-products', [ProductController::class, 'fetchBreadProducts']);
Route::get('ingredients',[ RawMaterialController::class, 'fetchRawMaterialsIngredients']);
Route::apiResource('branch-products', BranchProductController::class);
Route::post('search-branches-by-id', [BranchProductController::class, 'searchBranchId' ]);
Route::put('update-branch-products/{id}', [BranchProductController::class, 'updatePrice' ]);
Route::get('branches/{branchId}/products', [BranchProductController::class, 'getProducts']);
Route::get('search-products', [ProductController::class, 'searchProducts']);

