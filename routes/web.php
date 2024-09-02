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
use App\Http\Controllers\AdminAdminItemMasterController;
use App\Http\Controllers\AdminServiceItemsController;
use App\Http\Controllers\AdminCustomersController;
use App\Http\Controllers\AdminEmployeesController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use App\Http\Controllers\AdminGachaItemsController;
use App\Http\Controllers\AdminRmaItemsController;
use App\Http\Controllers\AdminItemsController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AdminAppleCutoffsController;
use App\Http\Controllers\AdminNonAppleCutoffsController;
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
    Route::get('sales_upload/regenerate-file/{id}',[AdminStoreSalesUploadsController::class, 'regenerateFile']);
    Route::get('sales_upload/download-uploaded-file/{id}',[AdminStoreSalesUploadsController::class, 'downloadUploadedFile']);
    Route::get('sales_upload/detail/{id}', [AdminStoreSalesUploadsController::class, 'getDetail'])->name('store_sales.detail');
    //PROGRESS EXPORT
    Route::post('store_sales/progress-export',[StoreSaleController::class, 'progressExport'])->name('store-sales-progress-export');
    Route::post('store_sales/send-email',[StoreSaleController::class, 'sendEmail'])->name('store-sales-send-email');
   
    Route::any('store_sales/filter',[AdminStoreSalesController::class, 'filterStoreSales'])->name('store-sales.filter');
    Route::post('store-concepts',[AdminStoreSalesController::class, 'concepts'])->name('store-concepts');


    Route::post('sales_digits_uploads/import-upload',[DigitsSaleController::class, 'digitsSalesUpload'])->name('digits-sales.upload');
    Route::get('sales_digits_uploads/import',[DigitsSaleController::class, 'digitsSalesUploadView'])->name('digits-sales.upload-view');
    Route::get('digits_sales/template',[DigitsSaleController::class, 'uploadTemplate'])->name('digits-sales.template');
    Route::post('digits_sales/export',[DigitsSaleController::class, 'exportSales'])->name('digits-sales.export');
    Route::get('sales_digits_uploads/batch/{batch_id}',[DigitsSaleController::class, 'getBatchDetails']);
    Route::get('sales_digits_uploads/generate-file/{id}',[AdminDigitsSalesUploadsController::class, 'generateFile']);
    Route::get('sales_digits_uploads/export-batch/{id}',[AdminDigitsSalesUploadsController::class, 'exportBatch']);
    Route::get('sales_digits_uploads/regenerate-file/{id}',[AdminDigitsSalesUploadsController::class, 'regenerateFile']);
    Route::get('sales_digits_uploads/download-uploaded-file/{id}',[AdminDigitsSalesUploadsController::class, 'downloadUploadedFile']);
    Route::get('sales_digits_uploads/detail/{id}', [AdminDigitsSalesUploadsController::class, 'getDetail'])->name('digits-sales.detail');
    
    Route::any('digits_sales/filter',[AdminDigitsSalesController::class, 'filterDigitsSales'])->name('digits-sales.filter');
    Route::post('digits-concepts',[AdminDigitsSalesController::class, 'concepts'])->name('digits-concepts');
    //PROGRESS EXPORT
    Route::post('digits_sales/progress-export',[DigitsSaleController::class, 'progressExport'])->name('digits-sales-progress-export');


    //import store inventory
    Route::post('inventory_store_uploads/import-upload',[StoreInventoryController::class, 'storeInventoryUpload'])->name('store-inventory.upload');
    Route::get('inventory_store_uploads/import',[StoreInventoryController::class, 'storeInventoryUploadView'])->name('store-inventory.upload-view');
    Route::get('store_inventories/template',[StoreInventoryController::class, 'uploadTemplate'])->name('store-inventory.template');
    Route::post('store_inventories/export',[StoreInventoryController::class, 'exportInventory'])->name('store-inventory.export');
    Route::get('inventory_store_uploads/batch/{batch_id}',[StoreInventoryController::class, 'getBatchDetails']);
    Route::get('inventory_store_uploads/generate-file/{id}',[AdminStoreInventoryUploadsController::class, 'generateFile']);
    Route::get('inventory_store_uploads/export-batch/{id}',[AdminStoreInventoryUploadsController::class, 'exportBatch']);
    Route::get('inventory_store_uploads/regenerate-file/{id}',[AdminStoreInventoryUploadsController::class, 'regenerateFile']);
    Route::get('inventory_store_uploads/download-uploaded-file/{id}',[AdminStoreInventoryUploadsController::class, 'downloadUploadedFile']);
    Route::get('inventory_store_uploads/detail/{id}', [AdminStoreInventoryUploadsController::class, 'getDetail'])->name('store-inventory.detail');
    Route::any('store_inventories/filter',[AdminStoreInventoriesController::class, 'filterStoreInventory'])->name('store-inventory.filter');
    //PROGRESS EXPORT
    Route::post('store_inventories/progress-export',[StoreInventoryController::class, 'progressExport'])->name('store-inventory-progress-export');


    //import warehouse inventory
    Route::post('warehouse_inventories/import-upload',[WarehouseInventoryController::class, 'warehouseInventoryUpload'])->name('warehouse-inventory.upload');
    Route::get('inventory_warehouse_uploads/import',[WarehouseInventoryController::class, 'warehouseInventoryUploadView'])->name('warehouse-inventory.upload-view');
    Route::get('warehouse_inventories/template',[WarehouseInventoryController::class, 'uploadTemplate'])->name('warehouse-inventory.template');
    Route::post('warehouse_inventories/export',[WarehouseInventoryController::class, 'exportInventory'])->name('warehouse-inventory.export');
    Route::get('inventory_warehouse_uploads/batch/{batch_id}',[WarehouseInventoryController::class, 'getBatchDetails']);
    Route::get('inventory_warehouse_uploads/generate-file/{id}',[AdminWarehouseInventoryUploadsController::class, 'generateFile']);
    Route::get('inventory_warehouse_uploads/export-batch/{id}',[AdminWarehouseInventoryUploadsController::class, 'exportBatch']);
    Route::get('inventory_warehouse_uploads/regenerate-file/{id}',[AdminWarehouseInventoryUploadsController::class, 'regenerateFile']);
    Route::get('inventory_warehouse_uploads/download-uploaded-file/{id}',[AdminWarehouseInventoryUploadsController::class, 'downloadUploadedFile']);
    Route::get('inventory_warehouse_uploads/detail/{id}', [AdminWarehouseInventoryUploadsController::class, 'getDetail'])->name('warehouse_sales.detail');
    Route::any('warehouse_inventories/filter',[AdminWarehouseInventoriesController::class, 'filterWarehouseInventory'])->name('warehouse-inventory.filter');
    //PROGRESS EXPORT
    Route::post('warehouse_inventories/progress-export',[WarehouseInventoryController::class, 'progressExport'])->name('warehouse-inventory-progress-export');

    Route::post('report_privileges/get/table-columns',[AdminReportPrivilegesController::class, 'getTableColumns'])->name('report-privileges.getTableColumns');
    Route::post('report_privileges/create/save',[AdminReportPrivilegesController::class, 'saveReport'])->name('report-privileges.save');
    
    // run rate
    Route::get('run-rate/year',[RunRateController::class, 'getYear'])->name('get-year');
    Route::get('run-rate/month',[RunRateController::class, 'getMonth'])->name('get-month');
    Route::get('run-rate/get-cutoff',[RunRateController::class, 'getCutoffRange'])->name('get-cutoff-range');
    Route::get('run-rate/filter-run-rate',[AdminRunRateController::class, 'filterRunRate'])->name('run-rate.filter-run-rate');
    Route::get('run-rate/filter-run-rate/export', [AdminRunRateController::class, 'exportRunRate'])->name('run-rate.export-run-rate');
    
    //Customer Employees export
    Route::post('admin/customers_master/export', [AdminCustomersController::class, 'exportData'])->name('customers_export');
    Route::post('admin/employees/export', [AdminEmployeesController::class, 'exportData'])->name('employees_export');

    //Admin upload
    Route::get('admin_items/admin-items-upload',[AdminAdminItemMasterController::class, 'importData']);
    Route::get('admin_items/upload-items-template',[AdminAdminItemMasterController::class, 'importItemsTemplate']);
    Route::post('admin_items/upload-items-save',[AdminAdminItemMasterController::class, 'importPostSave'])->name('upload-item-save');
    Route::post('admin/admin_items/export', [AdminAdminItemMasterController::class, 'exportData'])->name('admin_imfs_export');
    Route::post('admin/admin_items/export-with-headers', [AdminAdminItemMasterController::class, 'exportWithHeadersData'])->name('admin_imfs_export_with_headers');

    //Gacha upload
    Route::get('gacha_items/gacha-items-upload',[AdminGachaItemsController::class, 'importData']);
    Route::get('gacha_items/upload-gacha-items-template',[AdminGachaItemsController::class, 'importItemsTemplate']);
    Route::post('gacha_items/upload-gacha-items-save',[AdminGachaItemsController::class, 'importPostSave'])->name('upload-gacha-item-save');
    Route::post('admin/gacha_items/export', [AdminGachaItemsController::class, 'exportData'])->name('gacha_imfs_export');
    
    //Rma upload
    Route::get('rma_items/rma-items-upload',[AdminRmaItemsController::class, 'importData']);
    Route::get('rma_items/upload-rma-items-template',[AdminRmaItemsController::class, 'importItemsTemplate']);
    Route::post('rma_items/upload-rma-items-save',[AdminRmaItemsController::class, 'importPostSave'])->name('upload-rma-item-save');
    Route::post('admin/rma_items/export', [AdminRmaItemsController::class, 'exportData'])->name('rma_imfs_export');

    //Services upload
    Route::get('service_items/service-items-upload',[AdminServiceItemsController::class, 'importData']);
    Route::get('service_items/upload-service-items-template',[AdminServiceItemsController::class, 'importItemsTemplate']);
    Route::post('service_items/upload-service-items-save',[AdminServiceItemsController::class, 'importPostSave'])->name('upload-service-item-save');
    Route::post('admin/service_items/export', [AdminServiceItemsController::class, 'exportData'])->name('pos_imfs_export');
    Route::post('admin/service_items/export-service-with-headers', [AdminServiceItemsController::class, 'exportWithHeadersData'])->name('pos_imfs_export_with_headers');
   
    //Item upload
    Route::get('items/digits-items-upload',[AdminItemsController::class, 'importData']);
    Route::get('items/upload-digits-items-template',[AdminItemsController::class, 'importItemsTemplate']);
    Route::post('items/upload-digits-items-save',[AdminItemsController::class, 'importPostSave'])->name('upload-digits-item-save');
    Route::post('admin/items/export', [AdminItemsController::class, 'exportData'])->name('item_imfs_export');

    //ITEM update
    Route::post('digits_sales/update-items-save',[RequestController::class, 'updateSaveItem'])->name('update-items-save');

    //submaster upload
    Route::get('apple_cutoffs/import-apple', [AdminAppleCutoffsController::class, 'importPage']);
    Route::get('apple_cutoffs/import-template', [AdminAppleCutoffsController::class, 'importTemplate']);
    Route::post('apple_cutoffs/import-items',[AdminAppleCutoffsController::class, 'importExcel'])->name('upload.createApple');


    Route::get('non_apple_cutoffs/import-non-apple', [AdminNonAppleCutoffsController::class, 'importPage']);
    Route::get('non_apple_cutoffs/import-template', [AdminNonAppleCutoffsController::class, 'importTemplate']);
    Route::post('non_apple_cutoffs/import-items',[AdminNonAppleCutoffsController::class, 'importExcel'])->name('upload.createNonApple');
});