@extends('crudbooster::admin_template')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


body{
    overflow: hidden;
}

::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    /* General table styling */
    .dataTable {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #333;
        background-color: #f9f9f9;
    }

    .wrapper{
        overflow: hidden;
    }

    /* Header styling */
    .dataTable thead {
        background-color: #3C8DBC;
        color: white;
    }

    .dataTable thead th {
        padding: 12px 15px;
        text-align: center;
        font-weight: bold;
        border: 2px solid #3c8dbcc1;
    }

    /* Body styling */
    .dataTable tbody td {
        padding: 10px 15px;
        border: 1px solid #ddd;
        text-align: center;
        cursor: pointer;
    }

    /* Row hover effect */
    .dataTable tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Search box styling */
    .dataTables_wrapper .dataTables_filter input {
        padding: 6px;
        width: 300px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-left: 10px;
    }

    /* Entries dropdown styling */
    .dataTables_wrapper .dataTables_length select {
        padding: 6px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    /* Info and pagination area styling */
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: inline-block; 
        margin-top: 10px;
    }

    .dataTables_wrapper .dataTables_info {
        float: left; 
    }

    .dataTables_wrapper .dataTables_paginate {
        float: right; 
    }

    .dataTables_wrapper .top {
        display: flex;
        justify-content: space-between; /* Aligns all items to the right */
        align-items: center; /* Vertically centers items */
        margin-bottom: 10px; /* Space between the top and the table */
    }

    .dataTables_wrapper .dt-buttons {
        margin-left: 10px; /* Space between buttons */
    }

    .dataTables_wrapper .dataTables_length {
        margin-right: 10px; /* Space between "Show entries" dropdown and buttons */
    }

    .dataTables_wrapper .dataTables_filter {
        display: flex;
        align-items: center; /* Center search input vertically */
        margin-left: 10px; /* Space between buttons and search */
    }

    .dataTables_wrapper .dataTables_filter label {
        margin-right: 5px; /* Space between label and input */
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

    .copy-note {
        display: inline-block;
        color: green;
        font-size: 12px;
    }

    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .spinner {
        width: 55px;
        height: 55px;
        border: 10px solid rgba(43, 253, 253, 0.312);
        border-left-color: #3C8DBC;
        border-radius: 50%;
        animation: spin 0.5s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }


</style>
    
@endpush
@section('content')
    
    <div class="panel panel-default" style="overflow:hidden; padding: 15px; border: none; display: show;" id="rawData " >
        <div>
            <button class="btn btn-primary btn-sm" onclick="location.reload()" style="margin-bottom: 15px;"> <i class="fa fa-refresh"></i> Refresh</button>

            <table class="table" id="store-sync">
                <thead>
                    <tr>
                        <th>Store Name</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($store_sync_data) && is_array($store_sync_data))
                        @foreach ($store_sync_data as $row)
                            <tr>
                                <td>{{ $row->{'Warehouse'} }}</td>
                                <td>{{ $row->Date}}</td>
                                <td>{{ $row->{'Time'} }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('bottom')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.3.1/js/dataTables.buttons.min.js"></script>
<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<!-- Buttons for Excel export -->
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.html5.min.js"></script>
<script>
  
    $(document).ready(function(){
        $('.js-example-basic-multiple').select2({
            placeholder: "Select Store",
        });

        $('#store-sync').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                scrollY: '400px', // Adjust the height to your needs
                scrollX: true, // Ensure horizontal scrolling if needed
                scrollCollapse: true,
                paging: true,
                fixedHeader: false,
                buttons: [
                    {
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
                initComplete: function() {
            // Move buttons to the right side
            const buttons = $('.dt-buttons').detach();
            $('.top').append(buttons);
        }
        });


    });


</script>
@endpush