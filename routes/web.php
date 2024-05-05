<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminReportPrivilegesController;
use App\Http\Controllers\AdminDigitsSalesUploadsController;
use App\Http\Controllers\AdminStoreSalesController;
use App\Http\Controllers\AdminStoreSalesUploadsController;
use App\Http\Controllers\AdminDigitsSalesController;
use App\Http\Controllers\AdminRunRateController;
use App\Http\Controllers\AdminStoreInventoriesController;
use App\Http\Controllers\AdminStoreInventoryUploadsController;
use App\Http\Controllers\AdminWarehouseInventoryUploadsController;
use App\Http\Controllers\AdminWarehouseInventoriesController;
use App\Http\Controllers\DigitsSaleController;
use App\Http\Controllers\StoreSaleController;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\WarehouseInventoryController;
use App\Http\Controllers\RunRateController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

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
    Route::post('sales_upload/import-upload',[StoreSaleController::class, 'storeSalesUpload'])->name('store-sales.upload');
    Route::get('sales_upload/import',[StoreSaleController::class, 'storeSalesUploadView'])->name('store-sales.upload-view');
    Route::get('store_sales/template',[StoreSaleController::class, 'uploadTemplate'])->name('store-sales.template');
    Route::post('store_sales/export',[StoreSaleController::class, 'exportSales'])->name('store-sales.export');
    Route::get('store_sales/batch/{batch_id}',[StoreSaleController::class, 'getBatchDetails']);
    Route::get('sales_upload/batch/{batch_id}',[StoreSaleController::class, 'getBatchDetails']);
    Route::get('sales_upload/generate-file/{id}',[AdminStoreSalesUploadsController::class, 'generateFile']);
    Route::get('sales_upload/export-batch/{id}',[AdminStoreSalesUploadsController::class, 'exportBatch']);
    Route::get('sales_upload/download-uploaded-file/{id}',[AdminStoreSalesUploadsController::class, 'downloadUploadedFile']);
    Route::get('sales_upload/detail/{id}', [AdminStoreSalesUploadsController::class, 'getDetail'])->name('store_sales.detail');
    //PROGRESS EXPORT
    Route::post('store_sales/progress-export',[StoreSaleController::class, 'progressExport'])->name('store-sales-progress-export');
    Route::any('store_sales/filter',[AdminStoreSalesController::class, 'filterStoreSales'])->name('store-sales.filter');
    Route::post('store-concepts',[AdminStoreSalesController::class, 'concepts'])->name('store-concepts');


    Route::post('sales_digits_uploads/import-upload',[DigitsSaleController::class, 'digitsSalesUpload'])->name('digits-sales.upload');
    Route::get('sales_digits_uploads/import',[DigitsSaleController::class, 'digitsSalesUploadView'])->name('digits-sales.upload-view');
    Route::get('digits_sales/template',[DigitsSaleController::class, 'uploadTemplate'])->name('digits-sales.template');
    Route::post('digits_sales/export',[DigitsSaleController::class, 'exportSales'])->name('digits-sales.export');
    Route::get('sales_digits_uploads/batch/{batch_id}',[DigitsSaleController::class, 'getBatchDetails']);
    Route::get('sales_digits_uploads/generate-file/{id}',[AdminDigitsSalesUploadsController::class, 'generateFile']);
    Route::get('sales_digits_uploads/export-batch/{id}',[AdminDigitsSalesUploadsController::class, 'exportBatch']);
    Route::get('sales_digits_uploads/download-uploaded-file/{id}',[AdminDigitsSalesUploadsController::class, 'downloadUploadedFile']);
    Route::get('sales_digits_uploads/detail/{id}', [AdminDigitsSalesUploadsController::class, 'getDetail'])->name('digits-sales.detail');
    
    Route::any('digits_sales/filter',[AdminDigitsSalesController::class, 'filterDigitsSales'])->name('digits-sales.filter');
    Route::post('digits-concepts',[AdminDigitsSalesController::class, 'concepts'])->name('digits-concepts');



    //import store inventory
    Route::post('inventory_store_uploads/import-upload',[StoreInventoryController::class, 'storeInventoryUpload'])->name('store-inventory.upload');
    Route::get('inventory_store_uploads/import',[StoreInventoryController::class, 'storeInventoryUploadView'])->name('store-inventory.upload-view');
    Route::get('store_inventories/template',[StoreInventoryController::class, 'uploadTemplate'])->name('store-inventory.template');
    Route::post('store_inventories/export',[StoreInventoryController::class, 'exportInventory'])->name('store-inventory.export');
    Route::get('inventory_store_uploads/batch/{batch_id}',[StoreInventoryController::class, 'getBatchDetails']);
    Route::get('inventory_store_uploads/generate-file/{id}',[AdminStoreInventoryUploadsController::class, 'generateFile']);
    Route::get('inventory_store_uploads/export-batch/{id}',[AdminStoreInventoryUploadsController::class, 'exportBatch']);
    Route::get('inventory_store_uploads/download-uploaded-file/{id}',[AdminStoreInventoryUploadsController::class, 'downloadUploadedFile']);
    Route::get('inventory_store_uploads/detail/{id}', [AdminStoreInventoryUploadsController::class, 'getDetail'])->name('store-inventory.detail');
    Route::any('store_inventories/filter',[AdminStoreInventoriesController::class, 'filterStoreInventory'])->name('store-inventory.filter');


    //import warehouse inventory
    Route::post('warehouse_inventories/import-upload',[WarehouseInventoryController::class, 'warehouseInventoryUpload'])->name('warehouse-inventory.upload');
    Route::get('inventory_warehouse_uploads/import',[WarehouseInventoryController::class, 'warehouseInventoryUploadView'])->name('warehouse-inventory.upload-view');
    Route::get('warehouse_inventories/template',[WarehouseInventoryController::class, 'uploadTemplate'])->name('warehouse-inventory.template');
    Route::post('warehouse_inventories/export',[WarehouseInventoryController::class, 'exportInventory'])->name('warehouse-inventory.export');
    Route::get('inventory_warehouse_uploads/batch/{batch_id}',[WarehouseInventoryController::class, 'getBatchDetails']);
    Route::get('inventory_warehouse_uploads/generate-file/{id}',[AdminWarehouseInventoryUploadsController::class, 'generateFile']);
    Route::get('inventory_warehouse_uploads/export-batch/{id}',[AdminWarehouseInventoryUploadsController::class, 'exportBatch']);
    Route::get('inventory_warehouse_uploads/download-uploaded-file/{id}',[AdminWarehouseInventoryUploadsController::class, 'downloadUploadedFile']);
    Route::get('inventory_warehouse_uploads/detail/{id}', [AdminWarehouseInventoryUploadsController::class, 'getDetail'])->name('warehouse_sales.detail');
    Route::any('warehouse_inventories/filter',[AdminWarehouseInventoriesController::class, 'filterWarehouseInventory'])->name('warehouse-inventory.filter');

    Route::post('report_privileges/get/table-columns',[AdminReportPrivilegesController::class, 'getTableColumns'])->name('report-privileges.getTableColumns');
    Route::post('report_privileges/create/save',[AdminReportPrivilegesController::class, 'saveReport'])->name('report-privileges.save');
    
    // run rate
    Route::get('run-rate/year',[RunRateController::class, 'getYear'])->name('get-year');
    Route::get('run-rate/month',[RunRateController::class, 'getMonth'])->name('get-month');
    Route::get('run-rate/get-cutoff',[RunRateController::class, 'getCutoffRange'])->name('get-cutoff-range');
    Route::get('run-rate/filter-run-rate',[AdminRunRateController::class, 'filterRunRate'])->name('run-rate.filter-run-rate');
    Route::get('run-rate/filter-run-rate/export', [AdminRunRateController::class, 'exportRunRate'])->name('run-rate.export-run-rate');
    
});