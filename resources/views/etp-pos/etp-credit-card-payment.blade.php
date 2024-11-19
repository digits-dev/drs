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
    .top {
        position: sticky;
        top: 0;
        background-color: white;
        /* Ensure it blends with your page */
        z-index: 100;
        /* Ensure it appears above other elements */
        padding: 10px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dataTables_wrapper .dt-buttons {
        margin-left: 10px;
        /* Space between buttons */
    }

    .dataTables_wrapper .dataTables_length {
        margin-right: 10px;
        /* Space between "Show entries" dropdown and buttons */
    }

    .dataTables_wrapper .dataTables_filter {
        display: flex;
        align-items: center;
        /* Center search input vertically */
        margin-left: 10px;
        /* Space between buttons and search */
    }

    .dataTables_wrapper .dataTables_filter label {
        margin-right: 5px;
        /* Space between label and input */
    }
    .custom-button {
        background-color: #28a745;
        color: white;
        border: 1px solid transparent;
        border-radius: 4px;
        padding: 6px 7px;
        font-size: 13px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-button:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: whitesmoke;
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
    }

    .custom-button:active {
        background-color: #1e7e34;
        color: white;
        box-shadow: none;
    }
</style>
<div class='panel panel-default' style="width: 100%" >
    <div class='panel-heading'>
        Credit Card Lists
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered" id="credit-card-table" style="width:100%">
            <thead>
                <tr>
                    @if(isset($credit_card_payment[0])) 
                        @foreach (array_keys((array) $credit_card_payment[0]) as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($credit_card_payment as $lines)
                    <tr>
                        @foreach ((array) $lines as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    </div>
@endsection
@push('bottom')
<script src="https://cdn.datatables.net/buttons/2.3.1/js/dataTables.buttons.min.js"></script>
<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<!-- Buttons for Excel export -->
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $("#credit-card-table").dataTable({
        dom: '<"top"lBf>rt<"bottom"ip><"clear">',
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        fixedHeader: false,
        buttons: [{
                        extend: 'csv',
                        text: '<i class="fa fa-download"></i> Export CSV',
                        className: 'btn custom-button'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-download"></i> Download Excel',
                        className: 'btn custom-button'
                    }
                ],
    });
</script>
@endpush