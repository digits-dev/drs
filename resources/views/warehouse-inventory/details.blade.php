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
        Warehouse Inventory Details
    </div>
    <div class="panel-body">
        <table class="custom_table">
            <tbody>
                @php
                    $count = array_keys($report);
                    $chunkedReports = array_chunk($count , 2);
                @endphp
                @foreach ($chunkedReports as $chunk)
                    <tr>
                        @foreach ($chunk as $key)
                            @if (array_key_exists($key, $warehouse_inventory_details->toArray()))
                                <td>{{ $report[$key] }}</td>
                                <td>{{ $warehouse_inventory_details->$key }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
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