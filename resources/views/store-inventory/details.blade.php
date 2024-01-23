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
                    <td>{{{$store_inventory_details->reference_number}}}</td>
                    <td>UPC CODE-3 (IMFS)</td>
                    <td>{{{$store_inventory_details->upc_code3}}}</td>
                </tr>
                <tr>
                    <td>SYSTEM</td>
                    <td>{{{$store_inventory_details->system_name}}}</td>
                    <td>UPC CODE-4 (IMFS)</td>
                    <td>{{{$store_inventory_details->upc_code4}}}</td>
                </tr>
                <tr>
                    <td>ORG</td>
                    <td>{{{$store_inventory_details->organization_name}}}</td>
                    <td>UPC CODE-5 (IMFS)</td>
                    <td>{{{$store_inventory_details->upc_code5}}}</td>
                </tr>
                <tr>
                    <td>REPORT TYPE</td>
                    <td>{{{$store_inventory_details->report_type}}}</td>
                    <td>ITEM DESCRIPTION (IMFS)</td>
                    <td>{{{$store_inventory_details->imfs_item_description}}}</td>
                </tr>
                <tr>
                    <td>CHANNEL NAME</td>
                    <td>{{{$store_inventory_details->channel_name}}}</td>
                    <td>BRAND</td>
                    <td>{{{$store_inventory_details->brand_description}}}</td>
                </tr>
                <tr>
                    <td>CUSTOMER / LOCATION</td>
                    <td>{{{$store_inventory_details->customer_location}}}</td>
                    <td>CATEGORY</td>
                    <td>{{{$store_inventory_details->category_description}}}</td>
                </tr>
                <tr>
                    <td>STORE CONCEPT</td>
                    <td>{{{$store_inventory_details->store_concept_name}}}</td>
                    <td>MARGIN CATEGORY</td>
                    <td>{{{$store_inventory_details->margin_category_description}}}</td>
                </tr>
                <tr>
                    <td>ITEM NUMBER</td>
                    <td>{{{$store_inventory_details->item_code}}}</td>
                    <td>VENDOR TYPE CODE</td>
                    <td>{{{$store_inventory_details->vendor_type_code}}}</td>
                </tr>
                <tr>
                    <td>ITEM DESCRIPTION</td>
                    <td>{{{$store_inventory_details->item_description}}}</td>
                    <td>INVENTORY TYPE CODE</td>
                    <td>{{{$store_inventory_details->inventory_type_description}}}</td>
                </tr>
                <tr>
                    <td>QTY INV</td>
                    <td>{{{$store_inventory_details->quantity_inv}}}</td>
                    <td>SKU STATUS</td>
                    <td>{{{$store_inventory_details->sku_status_description}}}</td>
                </tr>
                <tr>
                    <td>DIGITS CODE (REF)</td>
                    <td>{{{$store_inventory_details->digits_code}}}</td>
                    <td>BRAND STATUS</td>
                    <td>{{{$store_inventory_details->brand_status}}}</td>
                </tr>
                <tr>
                    <td>UPC CODE (IMFS)</td>
                    <td>{{{$store_inventory_details->upc_code}}}</td>
                    <td>LANDED COST</td>
                    <td>{{{$store_inventory_details->landed_cost}}}</td>
                </tr>
                <tr>
                    <td>UPC CODE-2 (IMFS)</td>
                    <td>{{{$store_inventory_details->upc_code2}}}</td>
                    <td></td>
                    <td></td>
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