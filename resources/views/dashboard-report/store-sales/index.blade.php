@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .dashboard {
            padding-top: 10px;
        }

        .export {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            /* margin-left: auto; */

        }

        .main-content {
            background: white;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .dashboard-nav-tabs{
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            justify-content: space-between;
            gap:20px;
        }

        .tabs {
            display: flex;
            cursor: pointer;
        }
        .tab {
            padding: 10px 20px;
            background: #f8f6f6;
            color: black;
            border: 1px solid #d5d2d2;
        }

        .tab:nth-child(1) {
            border-radius: 4px 0 0 4px;
        }

        .tab:nth-child(2),
        .tab:nth-child(3),
        .tab:nth-child(4) {
            border-left: none; 
        }

        .tab:nth-child(4) {
            border-radius: 0 4px 4px 0;
        }


        .tab.active {
            background: #fff;
            border-bottom: 2px solid #3c8dbc;
            font-weight: 600;
            color: #3c8dbc;
        }
      
        .tab-content {
            display: none;
            opacity: 0; 
            transition: opacity 150ms ease; 
        }

        .tab-content.active {
            display: block; 
            opacity: 1; 
        }
        .tab-content {
            background: white;
        }
        .divider {
            background-color: #ddd;
            height: 0.5px;
            width: 100%;
        }

        .fade-out {
            opacity: 0;
        }

        .fade-in {
            opacity: 1;
        }

        h2{
            font-size: 16px;
            font-weight: 700;
        }

      
        #loading {
            height: 600px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #3c8dbc;
            border-radius: 5px;
        }

        #loading2 {
            height: 150px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #3c8dbc;
            border-radius: 5px;
        }

        .loader {
            border: 8px solid rgba(60, 141, 188, 0.3); 
            border-top: 8px solid #3c8dbc; 
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

    </style>

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">


@endpush

@section('content')

<div class="main-content">


    <div class="dashboard-nav-tabs">
        <div class="tabs">
            <div class="tab active" data-tab="tab1">Daily Sales Report</div>
            <div class="tab " data-tab="tab2">Monthly Sales Report</div>
            <div class="tab" data-tab="tab3">Quarterly Sales Report</div>    
            <div class="tab" data-tab="tab4">YTD Sales Report</div>
        </div>

        <div class="export">
            <a href="{{ route('export_sales_report_excel') }}" class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-download" aria-hidden="true"></i> Export to Excel
            </a>
            <a id="exportPDF" href="{{ route('export_sales_report_pdf') }}?perChannel=false&category=total" class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-download" aria-hidden="true"></i> Export to PDF
            </a>

            <btn id="refreshData"  class="btn btn-warning   btn-sm pull-right">
                <i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> Reload Data
            </btn>
        </div>
    </div>

    <div class="divider">
    </div>

    <div id="loading" class="text-center">
        <div class="loader"></div>
        <p>Loading, please wait...</p>
    </div>

    <div class="tab-content-container">
        <div id="tab1" class="tab-content active"></div>
        <div id="tab2" class="tab-content"></div>
        <div id="tab3" class="tab-content"></div>
        <div id="tab4" class="tab-content"></div>
    </div>

</div>

@endsection

@push('bottom')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>


<script>
   $(function() {

    fetchData();

    function fetchData(reloadData = false){
        $('.tab-content-container').hide();
        $('#loading').show(); 

        // Fetch data after the page has loaded
        $.ajax({
            url: '{{ route("fetch_store_sales") }}' + `${reloadData ? '?reload_data' : '' }`, 
            method: 'GET',
            success: function(data) {
                $('#loading').hide(); 
                $('.tab-content-container').show();


                // Populate each tab with data
                $('#tab1').html(data.tab1Html);
                $('#tab2').html(data.tab2Html);
                $('#tab3').html(data.tab3Html);
                $('#tab4').html(data.tab4Html);

                // Initialize Select2
                $('#channelSelector').select2();
                $('#conceptSelector').select2();
            },
            error: function(xhr, status, error) {
                $('#loading').hide(); 
                $('.tab-content-container').show();
                console.error('Error fetching data:', error);
            }
        });
    }

    $('#refreshData').click(function() {
        fetchData(true);
    });

    $('.tab').click(function() {
        var tabId = $(this).data('tab');

        // Fade out the currently active tab content
        $('.tab-content.active').removeClass('active').addClass('fade-out');

        setTimeout(() => {
            // Remove fade-out class and hide display style
            $('.tab-content.fade-out').removeClass('fade-out').css('display', 'none');
            
            // Activate the new tab and its content
            $('.tab').removeClass('active');
            $(this).addClass('active');
            $('#' + tabId).css('display', 'block').addClass('active').css('opacity', '0').animate({ opacity: 1 }, 500); // Fade in
        }, 150); 
    });

   


});
</script>
@endpush
