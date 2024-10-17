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
            margin-left: auto;

        }

        .main-content {
            background: white;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .tabs {
            display: flex;
            cursor: pointer;
            width: 100%;
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
            opacity: 0; /* Start hidden */
            transition: opacity 150ms ease; /* Add transition */
        }

        .tab-content.active {
            display: block; /* Make it block when active */
            opacity: 1; /* Fade in */
        }
      
        .nav-tabs > li > a {
            border: 1px solid transparent;
            border-radius: 4px;
            background: #fff;
            color: #007bff;
        }
        .nav-tabs > li.active > a {
            background: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .nav-tabs > li > a:hover {
            background: #e2e6ea;
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
            opacity: 0; /* Start fading out */
        }

        .fade-in {
            opacity: 1; /* Fade in */
        }

        h2{
            font-size: 16px;
            font-weight: 600;
        }

    </style>

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">


@endpush

@section('content')

<div class="main-content">

    <div class="tabs">
        <div class="tab active" data-tab="tab1">Daily Sales Report</div>
        <div class="tab " data-tab="tab2">Monthly Sales Report</div>
        <div class="tab" data-tab="tab3">Quarterly Sales Report</div>    
        <div class="tab" data-tab="tab4">YTD Sales</div>

        <div class="export">
            <a href="{{ route('weekly_export_excel') }}" class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-download" aria-hidden="true"></i> Export to Excel
            </a>
            <a id="exportPDF" href="{{ route('weekly_export_pdf') }}?perChannel=false&category=total" class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-download" aria-hidden="true"></i> Export to PDF
            </a>

            <a id="refreshData" href="{{ CRUDBooster::mainpath() }}?reload_data " class="btn btn-warning   btn-sm pull-right">
                <i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> Reload Data
            </a>
        </div>
    </div>

    <div class="divider">
    </div>

    <div class="tab-content-container">
        <div id="tab1" class="tab-content ">

            <div class="weekly-section">
                <div class="dashboard">
                    @foreach ($channel_codes as $channel => $channelData)
                        @if ($channel == 'OTHER' || $channel == '')
                            @continue
                        @endif
                        
                        <x-sales-report 
                            :isTopOpen="$loop->first"
                            :channel="$channel" 
                            :data="$channelData"
                            :prevYear="$yearData['previousYear']" 
                            :currYear="$yearData['currentYear']"
                            :lastThreeDaysDates="$lastThreeDaysDates"
                        />
                    @endforeach
                </div>
            </div>

        </div>

        <div id="tab2" class="tab-content ">
            <div class="monthly-section">
                <div class="dashboard">
                    @foreach ($channel_codes as $channel => $channelData)
                        @if ($channel == 'OTHER' || $channel == '')
                            @continue
                        @endif
                        
                        <x-monthly-sales-report 
                            :isTopOpen="$loop->first"
                            :channel="$channel" 
                            :data="$channelData"
                            :prevYear="$yearData['previousYear']" 
                            :currYear="$yearData['currentYear']"
                        />
                    @endforeach
                </div>
            </div>
        </div>

        <div id="tab3" class="tab-content ">
            <div class="quarterly-section">
                <div class="dashboard">
                    @foreach ($channel_codes as $channel => $channelData)
                        @if ($channel == 'OTHER' || $channel == '')
                            @continue
                        @endif
                        
                        <x-quarterly-sales-report 
                            :isTopOpen="$loop->first"
                            :channel="$channel" 
                            :data="$channelData"
                            :prevYear="$yearData['previousYear']" 
                            :currYear="$yearData['currentYear']"
                        />
                    @endforeach
                </div>
            </div>
        </div>

        <div id="tab4" class="tab-content active">
            <div class="ytd-section">
                <h2 class="text-start" >YTD SALES REPORT</h2>
                    <div class="" style="display: flex; gap:10px; justify-content:start; align-items:flex-start; margin-top:40px; ">
                
                        <div class="form-group" style="display: inline-block; margin-right: 15px;">
                            <label   class="control-label" for="categorySelector" style="margin-bottom: 15px;">Select Channel:</label>
                            
                            <select id="channelSelector" class="form-control">
                                @foreach ($channels as $channel)
                                    <option value="{{$channel->id}}">{{$channel->channel_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" style="display: inline-block; margin-right: 15px;">
                            <label   class="control-label" for="categorySelector" style="margin-bottom: 15px;">Select Store Concept:</label>
                            <select id="conceptSelector" class="form-control">
                                @foreach ($concepts as $concept)
                                    <option value="{{$concept->id}}">{{$concept->concept_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <button id="updateChartButton" class="btn btn-primary" style="align-self: center; ">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Update Charts
                        </button>
                    </div>
                
            </div>
        </div>

    </div>

</div>

@endsection

@push('bottom')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
   $(function() {

    $('.tab').click(function() {
        var tabId = $(this).data('tab');

        // Fade out the currently active tab content
        $('.tab-content.active').removeClass('active').addClass('fade-out');

        // Use a timeout to ensure the fade-out completes before switching
        setTimeout(() => {
            // Remove fade-out class and hide display style
            $('.tab-content.fade-out').removeClass('fade-out').css('display', 'none');
            
            // Activate the new tab and its content
            $('.tab').removeClass('active');
            $(this).addClass('active');
            $('#' + tabId).css('display', 'block').addClass('active').css('opacity', '0').animate({ opacity: 1 }, 500); // Fade in
        }, 150); // Match the timeout with the CSS transition duration
    });

    $('#channelSelector').select2();
    $('#conceptSelector').select2();


});
</script>
@endpush
