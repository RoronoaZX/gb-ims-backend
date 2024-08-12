<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchProductController;
use App\Http\Controllers\BranchRawMaterialsReportController;
use App\Http\Controllers\BranchReportController;
use App\Http\Controllers\InitialBakerReportController;
use App\Http\Controllers\InitialBakerreportsController;
use App\Http\Controllers\SalesReportsController;
use App\Models\BranchRawMaterialsReport;
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

    // Route::post('refresh-tokens',[ApiController::class, 'logout']);


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('users', UserController::class);
Route::apiResource('raw-materials', RawMaterialController::class);
Route::apiResource('warehouses', WarehouseController::class);
Route::apiResource('branches', BranchController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('recipes', RecipeController::class);
Route::apiResource('branch-raw-materials', BranchRawMaterialsReportController::class);
Route::apiResource('initial-baker-report', InitialBakerreportsController::class);
Route::apiResource('branch-products', BranchProductController::class);
Route::apiResource('sales-report', SalesReportsController::class);
Route::apiResource('branch-production-report', BranchReportController::class);

Route::post('confirm-initial-baker-report/{id}', [InitialBakerreportsController::class, 'confirmReport']);
Route::post('decline-initial-baker-report/{id}', [InitialBakerreportsController::class, 'declineReport']);
Route::post('search-branches-by-id', [BranchProductController::class, 'searchBranchId' ]);
Route::post('search-user', [UserController::class, 'searchUser' ]);

Route::put('update-name/{id}', [RecipeController::class, 'updateName']);
Route::put('update-target/{id}', [RecipeController::class, 'updateTarget']);
Route::put('update-branch-products/{id}', [BranchProductController::class, 'updatePrice' ]);
Route::put('update-branch-products-total-quantity/{id}', [BranchProductController::class, 'updateTotatQuatity' ]);
Route::put('update-branch-rawMaterials/{id}', [BranchRawMaterialsReportController::class, 'updateStocks' ]);

Route::get('get-bread-production', [InitialBakerreportsController::class, 'getInitialReportsData']);
Route::get('branch/{branchId}/rawMaterials',[ BranchRawMaterialsReportController::class, 'getRawMaterials']);
Route::get('branch/{branchId}/bakerDoughReport',[ InitialBakerreportsController::class, 'fetchDoughReports']);
Route::get('branch/{userId}/bakerReport',[ InitialBakerreportsController::class, 'getReportsByUserId']);
Route::get('ingredients',[ RawMaterialController::class, 'fetchRawMaterialsIngredients']);
Route::get('bread-products', [ProductController::class, 'fetchBreadProducts']);
Route::get('search-recipes',[ RecipeController::class, 'searchRecipe']);
Route::get('branches/{branchId}/products', [BranchProductController::class, 'getProducts']);
Route::get('branches/{branchId}/production-report', [BranchReportController::class, 'fetchBranchReport']);
Route::get('search-products', [ProductController::class, 'searchProducts']);
Route::get('search-rawMaterials', [RawMaterialController::class, 'searchRawMaterials']);

