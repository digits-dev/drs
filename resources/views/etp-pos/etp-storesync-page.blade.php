@extends('crudbooster::admin_template')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');




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
        flex-direction: column; /* Stack spinner and text vertically */
        z-index: 9999;
    }

    .spinner-dots {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .spinner-dots div {
        width: 12px;
        height: 12px;
        margin: 5px;
        /* background-color: #f1f1f1; */
        background-color: #3498db;
        border-radius: 50%;
        animation: bounce 0.4s infinite alternate; /* Reduced duration to 0.4s for faster bounce */
    }

    .spinner-dots div:nth-child(2) {
        animation-delay: 0.1s; /* Reduced delay to 0.1s */
    }

    .spinner-dots div:nth-child(3) {
        animation-delay: 0.2s; /* Reduced delay to 0.2s */
    }

    @keyframes bounce {
        to {
            opacity: 0.3;
            transform: translateY(-15px);
        }
    }

    .tblLoading{
        padding: 10px 0px 10px 10px;
        font-size: 17px;
        font-weight: 600;
        color: #3C8DBC;
    }

    .cus-progress-container {
        width: 100%;
        height: 8px;
        background-color: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
    }

    .cus-progress-bar {
        height: 100%;
        width: 0;
        background-color: #3498db;
        transition: width 0.4s ease;
    }

    .ld-note{
        font-size: 15px; 
        color: #3C8DBC; 
        /* color: #f1f1f1;  */
        font-weight: normal;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* #tdAll{
        background-image: url('https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExdmwxYnUzbzE3ZmdncXpiOXNzMmM5ejByNzE0bGRmMXNpNmx3Z2xhZCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/V4NSR1NG2p0KeJJyr5/200.webp');
    } */

</style>
    
@endpush
@section('content')

    <div class="panel panel-default" style="overflow:hidden; padding: 15px; border: none; display: show;" id="rawData" >
        <div>
            <button class="btn btn-primary btn-sm" onclick="location.reload()" style="margin-bottom: 15px;"> <i class="fa fa-refresh"></i> Refresh</button>

            <table class="table" id="store-sync">
                <thead>
                    <tr>
                        <th>Store Name</th>
                        <th>Last Zread Date</th>
                        <th>Last Zread Time</th>
                        <th>Store Last DRS Sync</th>
                        <th>Inventory Last DRS Sync</th>
                    </tr>
                </thead>
                <tbody id="store_sync_data">
                    <tr>
                        <td style="display: none"></td>
                        <td style="display: none"></td>
                        <td colspan="5" id="tdAll"><span class="tblLoading" style="display: show;" id="loadingTable">
                            <div class="spinner-dots">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                            
                            <p style='display:none;' class="ld-note" id="loadingData">Loading Data</p> <p id="pleaseWait" class="ld-note">Syncing, Please wait</p>
                            
                        <div class="cus-progress-container">
                            <div class="cus-progress-bar" id="animated-cus-progress-bar"></div>
                        </div>
                        </span>
                        </td>
                        <td style="display: none;"></td>
                        <td style="display: none;"></td>
                    </tr>
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
                "language": {
                    "emptyTable": 
                        `<div style="text-align: center;">
                            <img src="https://cdn-icons-png.flaticon.com/128/9841/9841554.png" alt="No Data Icon" style="width: 70px; margin-bottom: 10px; margin-top: 10px;">
                            <p style='font-size: 14px; color: #3C8DBC; font-weigth: 700;'>No matching Data found.</p>
                        </div>`
                },
                initComplete: function() {
            // Move buttons to the right side
            const buttons = $('.dt-buttons').detach();
            $('.top').append(buttons);
        }
        });
    });

    function startProgressBar(totalDuration) {
        const progressBar = document.getElementById('animated-cus-progress-bar');
        let progressValue = 0;
        const incrementDuration = totalDuration / 100; 

        const interval = setInterval(() => {
            if (progressValue < 100) {
                progressValue++;
                progressBar.style.width = progressValue + '%';
            } else {
                clearInterval(interval);
            }
        }, incrementDuration); 

        return interval;
    }

    $(document).ready(function() {
        const startTime = Date.now(); 

        $.ajax({
            url: 'etp_storesync_report/data',
            type: 'GET',
            success: function(response) {
                $('#loadingData').show();
                $('#pleaseWait').hide();
                const elapsedTime = Date.now() - startTime; 
                const totalDuration = Math.max(elapsedTime, 1000); 
                const interval = startProgressBar(totalDuration);
                
                setTimeout(() => {
                    clearInterval(interval);
                    $('#animated-cus-progress-bar').css('width', '100%');
                    $('#rawData').html(response);
                    $('#loadingTable').hide();
                }, totalDuration);
            },
            error: function(xhr, status, error) {
                const elapsedTime = Date.now() - startTime;
                const totalDuration = Math.max(elapsedTime, 1000); 
                const interval = startProgressBar(totalDuration);
                
                setTimeout(() => {
                    clearInterval(interval);
                    $('#animated-cus-progress-bar').css('width', '0%');
                    alert('Failed: ' + error);
                    $('#loadingTable').hide();
                }, totalDuration);
            }
        });
    });

</script>
@endpush