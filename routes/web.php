<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminReportPrivilegesController;
use App\Http\Controllers\DigitsSaleController;
use App\Http\Controllers\StoreSaleController;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\WarehouseInventoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=>'admin'], function(){

//import store sales
Route::post('store_sales/import-upload',[StoreSaleController::class, 'storeSalesUpload'])->name('store-sales.upload');
Route::get('store_sales/import',[StoreSaleController::class, 'storeSalesUploadView'])->name('store-sales.upload-view');
Route::get('store_sales/template',[StoreSaleController::class, 'uploadTemplate'])->name('store-sales.template');
Route::post('store_sales/export',[StoreSaleController::class, 'exportSales'])->name('store-sales.export');

Route::post('digits_sales/import-upload',[DigitsSaleController::class, 'storeSalesUpload'])->name('digits-sales.upload');
Route::get('digits_sales/import',[DigitsSaleController::class, 'storeSalesUploadView'])->name('digits-sales.upload-view');
Route::get('digits_sales/template',[DigitsSaleController::class, 'uploadTemplate'])->name('digits-sales.template');
Route::post('digits_sales/export',[DigitsSaleController::class, 'exportSales'])->name('digits-sales.export');

//import store inventory
Route::post('store_inventory/import-upload',[StoreInventoryController::class, 'storeInventoryUpload'])->name('store-inventory.upload');
Route::get('store_inventory/import',[StoreInventoryController::class, 'storeInventoryUploadView'])->name('store-inventory.upload-view');
Route::get('store_inventory/template',[StoreInventoryController::class, 'uploadTemplate'])->name('store-inventory.template');
Route::post('store_inventory/export',[StoreInventoryController::class, 'exportInventory'])->name('store-inventory.export');

//import warehouse inventory
Route::post('warehouse_inventory/import-upload',[WarehouseInventoryController::class, 'warehouseInventoryUpload'])->name('warehouse-inventory.upload');
Route::get('warehouse_inventory/import',[WarehouseInventoryController::class, 'warehouseInventoryUploadView'])->name('warehouse-inventory.upload-view');
Route::get('warehouse_inventory/template',[WarehouseInventoryController::class, 'uploadTemplate'])->name('warehouse-inventory.template');
Route::post('warehouse_inventory/export',[WarehouseInventoryController::class, 'exportInventory'])->name('warehouse-inventory.export');

Route::post('report_privileges/get/table-columns',[AdminReportPrivilegesController::class, 'getTableColumns'])->name('report-privileges.getTableColumns');
Route::post('report_privileges/create/save',[AdminReportPrivilegesController::class, 'saveReport'])->name('report-privileges.save');
});
