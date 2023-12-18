<?php

use App\Http\Controllers\Api\FootprintsReferenceResourceController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\LibraryRefReferenceResourceController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\PcbCodeController;
use App\Http\Controllers\Api\VersionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ElementResourceController;
use App\Http\Controllers\Api\ComponentsReferenceResourceController;
use App\Http\Controllers\Api\TempRangeReferenceResourceController;
use App\Http\Controllers\Api\GroupsReferenceResourceController;
use App\Http\Controllers\Api\CategoriesReferenceResourceController;
use App\Http\Controllers\Api\ManufacturersReferenceResourceController;
use App\Http\Controllers\Api\PartStatusesReferenceResourceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['authspo', 'converter']], function () {
    Route::apiResource('elements', ElementResourceController::class, ['except' => ['show']]);
    Route::group(['prefix' => 'elements'], function () {
        Route::get('/restore/{id}', [ElementResourceController::class, 'restore']);
        Route::post('/multi_restore', [ElementResourceController::class, 'multipleRestore']);
        Route::post('/multi_hide', [ElementResourceController::class, 'multipleDestroy']);
        Route::get('/references', [ElementResourceController::class, 'getReferences']);
        Route::get('/update_stock_data', [ElementResourceController::class, 'updateStockData']);
        Route::get('/history/{id}', [ElementResourceController::class, 'getHistory']);
    });

    Route::apiResource('pcb_codes', PcbCodeController::class, ['except' => ['show']]);
    Route::apiResource('versions', VersionController::class, ['except' => ['index', 'show']]);
    Route::get('pcb_codes/restore/{id}', [PcbCodeController::class, 'restore']);
    Route::get('pcb_codes/elements/{url_stock_title}', [PcbCodeController::class, 'elements']);
    Route::get('versions/restore/{id}', [VersionController::class, 'restore']);

    Route::group(['prefix' => 'references'], function () {
        Route::apiResources([
            'components' => ComponentsReferenceResourceController::class,
            'groups' => GroupsReferenceResourceController::class,
            'categories' => CategoriesReferenceResourceController::class,
            'manufacturers' => ManufacturersReferenceResourceController::class,
            'temp_ranges' => TempRangeReferenceResourceController::class,
            'part_statuses' => PartStatusesReferenceResourceController::class,
            'library_refs' => LibraryRefReferenceResourceController::class,
            'footprints' => FootprintsReferenceResourceController::class,
        ], ['except' => ['show']]);
        Route::group(['prefix' => 'components'], function () {
            Route::post('/join/{id}', [ComponentsReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [ComponentsReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [ComponentsReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [ComponentsReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [ComponentsReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [ComponentsReferenceResourceController::class, 'getHistory']);
        });
        Route::group(['prefix' => 'groups'], function () {
            Route::post('/join/{id}', [GroupsReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [GroupsReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [GroupsReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [GroupsReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [GroupsReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [GroupsReferenceResourceController::class, 'getHistory']);
        });
        Route::group(['prefix' => 'categories'], function () {
            Route::post('/join/{id}', [CategoriesReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [CategoriesReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [CategoriesReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [CategoriesReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [CategoriesReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [CategoriesReferenceResourceController::class, 'getHistory']);
        });
        Route::group(['prefix' => 'manufacturers'], function () {
            Route::post('/join/{id}', [ManufacturersReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [ManufacturersReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [ManufacturersReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [ManufacturersReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [ManufacturersReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [ManufacturersReferenceResourceController::class, 'getHistory']);
        });
        Route::group(['prefix' => 'temp_ranges'], function () {
            Route::post('/join/{id}', [TempRangeReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [TempRangeReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [TempRangeReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [TempRangeReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [TempRangeReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [TempRangeReferenceResourceController::class, 'getHistory']);
        });
        Route::group(['prefix' => 'part_statuses'], function () {
            Route::post('/join/{id}', [PartStatusesReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [PartStatusesReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [PartStatusesReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [PartStatusesReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [PartStatusesReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [PartStatusesReferenceResourceController::class, 'getHistory']);
        });
        Route::group(['prefix' => 'library_refs'], function () {
            Route::post('/join/{id}', [LibraryRefReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [LibraryRefReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [LibraryRefReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [LibraryRefReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [LibraryRefReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [LibraryRefReferenceResourceController::class, 'getHistory']);
        });
        Route::group(['prefix' => 'footprints'], function () {
            Route::post('/join/{id}', [FootprintsReferenceResourceController::class, 'join']);
            Route::get('/restore/{id}', [FootprintsReferenceResourceController::class, 'restore']);
            Route::post('/multi_restore', [FootprintsReferenceResourceController::class, 'multipleRestore']);
            Route::post('/multi_hide', [FootprintsReferenceResourceController::class, 'multipleDestroy']);
            Route::post('/multi_join', [FootprintsReferenceResourceController::class, 'multipleJoin']);
            Route::get('/history/{id}', [FootprintsReferenceResourceController::class, 'getHistory']);
        });
    });
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/history', [HistoryController::class, 'index']);
    Route::get('/history/select_data', [HistoryController::class, 'getSelectData']);
});
