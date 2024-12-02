<?php


use App\Http\Controllers\CBHook;
use Illuminate\Support\Facades\Route;
use App\Jobs\ProcessStoresInventoryJob;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RunRateController;
use App\Http\Controllers\StoreSaleController;
use App\Http\Controllers\AdminItemsController;
use App\Http\Controllers\DigitsSaleController;
use App\Http\Controllers\AdminRunRateController;
use App\Http\Controllers\EtpBirReportController;
use App\Http\Controllers\AdminCmsUsersController;
use App\Http\Controllers\AdminRmaItemsController;
use App\Http\Controllers\AdminCustomersController;
use App\Http\Controllers\AdminEmployeesController;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\AdminGachaItemsController;
use App\Http\Controllers\AdminStoreSalesController;
use App\Http\Controllers\EtpTenderReportController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use App\Http\Controllers\AdminDigitsSalesController;
use App\Http\Controllers\AdminAppleCutoffsController;
use App\Http\Controllers\AdminServiceItemsController;
use App\Http\Controllers\GashaponInventoryController;
use App\Http\Controllers\AdminAnnouncementsController;
use App\Http\Controllers\EtpStoreSyncReportController;
use App\Http\Controllers\GashaponStoreSalesController;
use App\Http\Controllers\WarehouseInventoryController;
use App\Http\Controllers\AdminAdminItemMasterController;
use App\Http\Controllers\AdminNonAppleCutoffsController;
use App\Http\Controllers\EtpCreditCardPaymentController;
use App\Http\Controllers\AdminReportPrivilegesController;
use App\Http\Controllers\AdminStoreInventoriesController;
use App\Http\Controllers\AdminStoreSalesUploadsController;
use App\Http\Controllers\AdminDigitsSalesUploadsController;
use App\Http\Controllers\AdminGashaponStoreSalesController;
use App\Http\Controllers\AdminGashaponInventoriesController;
use App\Http\Controllers\AdminWarehouseInventoriesController;
use App\Http\Controllers\AdminStoreInventoryUploadsController;
use App\Http\Controllers\SupplierIntransitInventoryController;
use App\Http\Controllers\AdminGashaponInventoryUploadsController;
use App\Http\Controllers\AdminGashaponStoreSalesUploadsController;
use App\Http\Controllers\AdminWarehouseInventoryUploadsController;
use App\Http\Controllers\EtpStoreInventoryDetailedReportController;
use App\Http\Controllers\AdminSupplierIntransitInventoriesController;
use App\Http\Controllers\AdminSupplierIntransitInventoryUploadsController;

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

Route::get('/get-store-sales', [StoreSaleController::class, 'StoresSalesFromPosEtp']);
Route::get('/test-store-inventory-etp/{datefrom?}/{dateto?}', [StoreInventoryController::class, 'StoresInventoryFromPosEtp']);

Route::get('/run-process-stores-inventory-job/{datefrom}/{dateto}', function ($datefrom, $dateto) {
    ProcessStoresInventoryJob::dispatch('20241115', '20241130');
    return response()->json([
        'message' => 'Job dispatched successfully',
        'datefrom' => $datefrom,
        'dateto' => $dateto
    ]);
});

Route::group(['middleware' => ['web'], 'prefix' => config('crudbooster.ADMIN_PATH')], function () {
    Route::post('login', [CBHook::class, 'postLogin'])->name('postLogin');
});

//RESET PASSWORD
Route::get('/reset_password_email/{email}', [AdminCmsUsersController::class, 'getResetView'])->name('reset_password_email');
Route::post('/send_resetpass_email/reset',[AdminCmsUsersController::class, 'postSaveResetPassword'])->name('postResetPassword');

//Update Password
Route::group(['middleware' => ['web'], 'prefix' => config('crudbooster.ADMIN_PATH')], function () {
    Route::get('change-password', [AdminCmsUsersController::class, 'showChangeForcePasswordForm'])->name('show-change-force-password');
    Route::post('save-change-password', [AdminCmsUsersController::class, 'postUpdatePassword'])->name('update_password');
    Route::post('check-password', [AdminCmsUsersController::class, 'checkPassword'])->name('check-current-password');
    Route::post('check-waive', [AdminCmsUsersController::class, 'checkWaive'])->name('check-waive-count');
    Route::get('show-change-pass', [RequestController::class, 'showChangePassword'])->name('change-password');
    Route::post('reset-password', [AdminCmsUsersController::class, 'postSendEmailResetPassword'])->name('reset-password');
    Route::post('waive-change-password',[AdminCmsUsersController::class, 'waiveChangePassword'])->name('waive-change-password');

    //ANNOUNCEMENT
    Route::get('unread-announcement', [AdminAnnouncementsController::class, 'getUnreadAnnouncements'])->name('show-announcement');
    Route::post('read-announcement', [AdminAnnouncementsController::class, 'markAnnouncementAsRead'])->name('read-announcement');
    Route::get('announcement', [AdminAnnouncementsController::class, 'getAnnouncements'])->name('announcement');
});

Route::group(['middleware' => ['web','\crocodicstudio\crudbooster\middlewares\CBBackend','check.user'], 'prefix'=>'admin'], function(){

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

    //GASHAPON SALES
    Route::post('gashapon_store_sales_uploads/import-upload',[GashaponStoreSalesController::class, 'storeSalesUpload'])->name('gashapon-store-sales.upload');
    Route::get('gashapon_store_sales_uploads/import',[GashaponStoreSalesController::class, 'storeSalesUploadView'])->name('gashapon-store-sales.upload-view');
    Route::get('gashapon_store_sales_uploads/template',[GashaponStoreSalesController::class, 'uploadTemplate'])->name('gashapon-store-sales.template');
    Route::post('gashapon_store_sales_uploads/export',[GashaponStoreSalesController::class, 'exportSales'])->name('gashapon-store-sales.export');
    Route::get('gashapon_store_sales_uploads/batch/{batch_id}',[GashaponStoreSalesController::class, 'getBatchDetails']);
    Route::get('gashapon_store_sales_uploads/batch/{batch_id}',[GashaponStoreSalesController::class, 'getBatchDetails']);
    Route::get('gashapon_store_sales_uploads/generate-file/{id}',[AdminGashaponStoreSalesUploadsController::class, 'generateFile']);
    Route::get('gashapon_store_sales_uploads/export-batch/{id}',[AdminGashaponStoreSalesUploadsController::class, 'exportBatch']);
    Route::get('gashapon_store_sales_uploads/regenerate-file/{id}',[AdminGashaponStoreSalesUploadsController::class, 'regenerateFile']);
    Route::get('gashapon_store_sales_uploads/download-uploaded-file/{id}',[AdminGashaponStoreSalesUploadsController::class, 'downloadUploadedFile']);
    Route::get('gashapon_store_sales_uploads/detail/{id}', [AdminGashaponStoreSalesUploadsController::class, 'getDetail'])->name('gashapon_store_sales.detail');
    Route::any('gashapon_store_sales_uploads/filter',[AdminGashaponStoreSalesController::class, 'filterGashaponStoreSales'])->name('gashapon-store-sales.filter');
    Route::post('gashapon-store-concepts',[AdminGashaponStoreSalesController::class, 'concepts'])->name('gashapon-store-concepts');

    //SUPPLIER INTRANSIT INVENTORY
    Route::post('supplier_intransit_inventory_uploads/import-upload',[SupplierIntransitInventoryController::class, 'supplierIntransitInventoryUpload'])->name('supplier-intransit-inventory.upload');
    Route::get('supplier_intransit_inventory_uploads/import',[SupplierIntransitInventoryController::class, 'supplierIntransitInventoryUploadView'])->name('supplier-intransit-inventory.upload-view');
    Route::get('supplier_intransit_inventory_uploads/template',[SupplierIntransitInventoryController::class, 'uploadTemplate'])->name('supplier-intransit-inventory.template');
    Route::post('supplier_intransit_inventory_uploads/export',[SupplierIntransitInventoryController::class, 'exportSupplierIntransitInventory'])->name('supplier-intransit-inventory.export');
    Route::get('supplier_intransit_inventory_uploads/batch/{batch_id}',[SupplierIntransitInventoryController::class, 'getBatchDetails']);
    Route::get('supplier_intransit_inventory_uploads/batch/{batch_id}',[SupplierIntransitInventoryController::class, 'getBatchDetails']);
    Route::get('supplier_intransit_inventory_uploads/generate-file/{id}',[AdminSupplierIntransitInventoryUploadsController::class, 'generateFile']);
    Route::get('supplier_intransit_inventory_uploads/export-batch/{id}',[AdminSupplierIntransitInventoryUploadsController::class, 'exportBatch']);
    Route::get('supplier_intransit_inventory_uploads/regenerate-file/{id}',[AdminSupplierIntransitInventoryUploadsController::class, 'regenerateFile']);
    Route::get('supplier_intransit_inventory_uploads/download-uploaded-file/{id}',[AdminSupplierIntransitInventoryUploadsController::class, 'downloadUploadedFile']);
    Route::get('supplier_intransit_inventory/detail/{id}', [AdminSupplierIntransitInventoryUploadsController::class, 'getDetail'])->name('supplier_intransit_inventory.detail');
    Route::any('supplier_intransit_inventory/filter',[AdminSupplierIntransitInventoriesController::class, 'filterSupplierIntransitInventory'])->name('supplier-intransit-inventory.filter');
    Route::post('supplier-intransit-inventory-concepts',[AdminSupplierIntransitInventoriesController::class, 'concepts'])->name('supplier-intransit-inventory-concepts');
   
    //GASHAPON INVENTORY
    Route::post('gashapon_inventory_uploads/import-upload',[GashaponInventoryController::class, 'GashaponInventoryUpload'])->name('gashapon-inventory.upload');
    Route::get('gashapon_inventory_uploads/import',[GashaponInventoryController::class, 'GashaponInventoryUploadView'])->name('gashapon-inventory.upload-view');
    Route::get('gashapon_inventory_uploads/template',[GashaponInventoryController::class, 'uploadTemplate'])->name('gashapon-inventory.template');
    Route::post('gashapon_inventory_uploads/export',[GashaponInventoryController::class, 'exportGashaponInventory'])->name('gashapon-inventory.export');
    Route::get('gashapon_inventory_uploads/batch/{batch_id}',[GashaponInventoryController::class, 'getBatchDetails']);
    Route::get('gashapon_inventory_uploads/batch/{batch_id}',[GashaponInventoryController::class, 'getBatchDetails']);
    Route::get('gashapon_inventory_uploads/generate-file/{id}',[AdminGashaponInventoryUploadsController::class, 'generateFile']);
    Route::get('gashapon_inventory_uploads/export-batch/{id}',[AdminGashaponInventoryUploadsController::class, 'exportBatch']);
    Route::get('gashapon_inventory_uploads/regenerate-file/{id}',[AdminGashaponInventoryUploadsController::class, 'regenerateFile']);
    Route::get('gashapon_inventory_uploads/download-uploaded-file/{id}',[AdminGashaponInventoryUploadsController::class, 'downloadUploadedFile']);
    Route::get('gashapon_inventory/detail/{id}', [AdminGashaponInventoryUploadsController::class, 'getDetail'])->name('gashapon_inventory.detail');
    Route::any('gashapon_inventory/filter',[AdminGashaponInventoriesController::class, 'filterGashaponInventory'])->name('gashapon-inventory.filter');
    Route::post('gashapon-inventory-concepts',[AdminGashaponInventoriesController::class, 'concepts'])->name('gashapon-inventory-concepts');

    // ETP ROUTES
    Route::get('etp_bir_report', [EtpBirReportController::class, 'getIndex'])->name('etp_bir_report');
    Route::get('etp_tender_report', [EtpTenderReportController::class, 'getIndex'])->name('etp_tender_report');
    Route::get('etp_storesync_report', [EtpStoreSyncReportController::class, 'getIndex'])->name('etp_storesync_report');
    Route::get('etp_storesync_report/data', [EtpStoreSyncReportController::class, 'getStoreSync'])->name('etp_storesync_report_data');
    Route::get('etp_storeinventorydetailed_report', [EtpStoreInventoryDetailedReportController::class, 'getIndex'])->name('etp_storeinventorydetailed_report');
    Route::get('etp_credit_card_payment', [EtpCreditCardPaymentController::class, 'getIndex'])->name('etp_credit_card_payment');
    // TENDER REPORT
    Route::post('generateTender/report', [EtpTenderReportController::class, 'getIndex']);
    
    // STORE INVENTORY DETAILED
    Route::post('generate_store_inventory_detailed/report', [EtpStoreInventoryDetailedReportController::class, 'getIndex']);
    
    // BIR REPORT
    Route::post('generate_bir/report', [EtpBirReportController::class, 'getIndex']);

   
});

