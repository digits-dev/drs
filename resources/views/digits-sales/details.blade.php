@extends('crudbooster::admin_template')

@section('content')
<style>
    .table tbody tr td, .table thead tr th, .table{
        border: 1px solid #ddd;
    }
    .custom_table {
    width: 100%;
    border-collapse: collapse;
    background-color: #f9f9f9;
    border-radius: 5px !important;
}

.custom_table th, .custom_table td {
    border: 1px solid #ddd;
    text-align: left;
    width: 300px;
    font-size: 14px;
}

.custom_table tr td:nth-child(odd){
    font-weight: bold;
    color: #4d4b4b !important;
}

.custom_table td{
    padding: 10px;
}
</style>
<div class='panel panel-default' style="width: 100%" >
    <div class='panel-heading'>
        Store Sales Details
    </div>
    <div class="panel-body">
        <table class="custom_table">
            <tbody>
                <tr>
                    <td>REFERENCE #</td>
                    <td>{{{$digits_sales_details->reference_number}}}</td>
                    <td>UPC CODE-3 (IMFS)</td>
                    <td>{{{$store_sales_details->upc_code3}}}</td>
                </tr>
                <tr>
                    <td>SYSTEM</td>
                    <td>{{{$digits_sales_details->system_name}}}</td>
                    <td>UPC CODE-4 (IMFS)</td>
                    <td>{{{$digits_sales_details->upc_code4}}}</td>
                </tr>
                <tr>
                    <td>ORG</td>
                    <td>{{{$digits_sales_details->organization_name}}}</td>
                    <td>UPC CODE-5 (IMFS)</td>
                    <td>{{{$digits_sales_details->upc_code5}}}</td>
                </tr>
                <tr>
                    <td>REPORT TYPE</td>
                    <td>{{{$digits_sales_details->report_type}}}</td>
                    <td>ITEM DESCRIPTION (IMFS)</td>
                    <td>{{{$digits_sales_details->imfs_item_description}}}</td>
                </tr>
                <tr>
                    <td>CHANNEL NAME</td>
                    <td>{{{$digits_sales_details->channel_name}}}</td>
                    <td>BRAND</td>
                    <td>{{{$digits_sales_details->brand_description}}}</td>
                </tr>
                <tr>
                    <td>CUSTOMER / LOCATION</td>
                    <td>{{{$digits_sales_details->customer_location}}}</td>
                    <td>CATEGORY</td>
                    <td>{{{$digits_sales_details->category_description}}}</td>
                </tr>
                <tr>
                    <td>STORE CONCEPT</td>
                    <td>{{{$digits_sales_details->store_concept_name}}}</td>
                    <td>MARGIN CATEGORY</td>
                    <td>{{{$digits_sales_details->margin_category_description}}}</td>
                </tr>
                <tr>
                    <td>RECEIPT #</td>
                    <td>{{{$digits_sales_details->receipt_number}}}</td>
                    <td>VENDOR TYPE CODE</td>
                    <td>{{{$digits_sales_details->vendor_type_code}}}</td>
                </tr>
                <tr>
                    <td>SOLD DATE</td>
                    <td>{{{$digits_sales_details->sales_date}}}</td>
                    <td>INVENTORY TYPE CODE</td>
                    <td>{{{$digits_sales_details->inventory_type_description}}}</td>
                </tr>
                <tr>
                    <td>ITEM NUMBER</td>
                    <td>{{{$digits_sales_details->item_code}}}</td>
                    <td>SKU STATUS</td>
                    <td>{{{$digits_sales_details->sku_status_description}}}</td>
                </tr>
                <tr>
                    <td>ITEM DESCRIPTION</td>
                    <td>{{{$digits_sales_details->item_description}}}</td>
                    <td>BRAND STATUS</td>
                    <td>{{{$digits_sales_details->brand_status}}}</td>
                </tr>
                <tr>
                    <td>QTY SOLD</td>
                    <td>{{{$digits_sales_details->quantity_sold}}}</td>
                    <td>QTY SOLD SC</td>
                    <td>{{{$digits_sales_details->qtysold_sc}}}</td>
                </tr>
                <tr>
                    <td>"SOLD PRICE</td>
                    <td>{{{$digits_sales_details->sold_price}}}</td>
                    <td>QTY SOLD ECOM</td>
                    <td>{{{$digits_sales_details->qtysold_ecom}}}</td>
                </tr>
                <tr>
                    <td>NET SALES</td>
                    <td>{{{$digits_sales_details->net_sales}}}</td>
                    <td>landed_cost</td>
                    <td>{{{$digits_sales_details->landed_cost}}}</td>
                </tr>
                <tr>
                    <td>STORE COST ACTUAL</td>
                    <td>{{{$digits_sales_details->store_cost}}}</td>
                    <td>QTY SOLD LC</td>
                    <td>{{{$digits_sales_details->qtysold_lc}}}</td>
                </tr>
                <tr>
                    <td>STORE COST ACTUAL - ECOM</td>
                    <td>{{{$digits_sales_details->dtp_ecom}}}</td>
                    <td>APPLE (FYYY-YY/QQ/WKWK)</td>
                    <td>{{{$digits_sales_details->apple_yr_qtr_wk}}}</td>
                </tr>
                <tr>
                    <td>SALE MEMO REF #</td>
                    <td>{{{$digits_sales_details->sale_memo_reference}}}</td>
                    <td>APPLE (WEEKS CUTOFF)</td>
                    <td>{{{$digits_sales_details->apple_week_cutoff}}}</td>
                </tr>
                <tr>
                    <td>DIGITS CODE (REF)</td>
                    <td>{{{$digits_sales_details->digits_code}}}</td>
                    <td>NON-APPLE (CYYY/MM/WKWK)</td>
                    <td>{{{$digits_sales_details->non_apple_yr_mon_wk}}}</td>
                </tr>
                <tr>
                    <td>UPC CODE (IMFS)</td>
                    <td>{{{$digits_sales_details->upc_code}}}</td>
                    <td>NON-APPLE (WEEKS CUTOFF)</td>
                    <td>{{{$digits_sales_details->non_apple_week_cutoff}}}</td>
                </tr>
                <tr>
                    <td>UPC CODE-2 (IMFS)</td>
                    <td>{{{$digits_sales_details->upc_code2}}}</td>
                    <td>DIGITS CODE (RR REF)</td>
                    <td>{{{$digits_sales_details->digits_code_rr_ref}}}</td>
                </tr>
            </tbody>
          </table>
    </div>
        <div class='panel-footer'>
            <a href="{{ CRUDBooster::mainpath() }}" class="btn btn-default">BACK</a>
        </div>
    </div>
@endsection
@push('bottom')
<script src="{{ asset('plugins/sweetalert.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">

    
</script>
@endpush