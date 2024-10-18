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
            opacity: 0; /* Start hidden */
            transition: opacity 150ms ease; /* Add transition */
        }

        .tab-content.active {
            display: block; /* Make it block when active */
            opacity: 1; /* Fade in */
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
            font-weight: 700;
        }

        /* #loading {
            display: none;
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
        } */
    </style>

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">


@endpush

@section('content')

<div class="main-content">

    <div id="loading">Loading data, please wait...</div>

    <div class="dashboard-nav-tabs">
        <div class="tabs">
            <div class="tab active" data-tab="tab1">Daily Sales Report</div>
            <div class="tab " data-tab="tab2">Monthly Sales Report</div>
            <div class="tab" data-tab="tab3">Quarterly Sales Report</div>    
            <div class="tab" data-tab="tab4">YTD Sales Report</div>
        </div>

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
        <div id="tab1" class="tab-content active"></div>
        <div id="tab2" class="tab-content"></div>
        <div id="tab3" class="tab-content"></div>
        <div id="tab4" class="tab-content"></div>
    </div>

    {{-- <div class="tab-content-container">

        @php
            $prevYear = $yearData['previousYear'];
            $currYear = $yearData['currentYear'];
            $month = $yearData['month'];
        @endphp

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
                            :prevYear="$prevYear"
                            :currYear="$currYear"
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
                            :prevYear="$prevYear"
                            :currYear="$currYear"
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
                            :prevYear="$prevYear"
                            :currYear="$currYear"
                        />
                    @endforeach
                </div>
            </div>
        </div>

        <div id="tab4" class="tab-content active">
            <div class="ytd-section">
                <h2 class="text-start" style="margin-top:25px;">YTD SALES REPORT</h2>

                <div class="" style="display: flex; flex-wrap:wrap; gap:15px; justify-content:flex-start; align-items:ceneter; margin:20px 0px 8px;">
            
                    <div class="form-group" >
                        <label class="control-label" for="channelSelector" >Channel:</label>
                        
                        <select id="channelSelector" class="form-control">
                            <option value="all">All</option>

                            @foreach ($channels as $channel)
                                <option value="{{$channel->id}}">{{$channel->channel_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" >
                        <label class="control-label" for="conceptSelector" >Store Concept:</label>
                        <select id="conceptSelector" class="form-control">
                            <option value="all">All</option>

                            @foreach ($concepts as $concept)
                                <option value="{{$concept->id}}">{{$concept->concept_name}}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <button id="updateTableButton" class="btn btn-primary" style="align-self: center; margin-top:8px; height:30px;">
                        <i class="fa fa-refresh" aria-hidden="true"></i> Update Table
                    </button>
                </div>

           
                <div id="ytdSalesReportContainer">
                    <x-ytd-sales-report 
                        :prevYear="$prevYear"
                        :currYear="$currYear"
                        :month="$month"
                        :prevYearYTDData="$channel_codes['TOTAL'][$prevYear]['ytd']"
                        :currYearYTDData="$channel_codes['TOTAL'][$currYear]['ytd']"
                    />
                </div>
            
            </div>
        </div>

    </div> --}}

</div>

@endsection

@push('bottom')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>


<script>
   $(function() {

    $('#loading').show(); // Show loading indicator

    // Fetch data after the page has loaded
    $.ajax({
        url: '{{ route("fetch_store_sales") }}', // Update with your fetch route
        method: 'GET',
        success: function(data) {
            $('#loading').hide(); // Hide loading indicator

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
            $('#loading').hide(); // Hide loading indicator
            console.error('Error fetching data:', error);
            // Handle error (e.g., display an error message)
        }
    });

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

    $('#updateTableButton').on('click', function() {
        console.log('work');
        console.log('CSRF Token:', '{{ csrf_token() }}');
        const selectedChannel = $('#channelSelector').val();
        const selectedConcept = $('#conceptSelector').val();

        $.ajax({
            url: '/admin/ytd_update',
            type: 'POST',
            data: {
                channel: selectedChannel,
                concept: selectedConcept,
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function(data) {
                console.log(data);

                // Fallback values
                const currData = {
                    apple: data.currApple || 0,
                    nonApple: data.currNonApple || 0,
                    totalApple: data.currTotalApple || 0,
                };

                const prevData = {
                    apple: data.prevApple || 0,
                    nonApple: data.prevNonApple || 0,
                    totalApple: data.prevTotalApple || 0,
                };

                // Format numbers with two decimal places
                const formatNumber = (num) => {
                    return Number(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                // Calculate increments and percentage changes
                const calculateChanges = (curr, prev) => {
                    const change = curr - prev;
                    const percentageChange = prev ? ((change / prev) * 100) : 0;
                    return {
                        change,
                        percentageChange,
                        roundedValue: Math.round(percentageChange) + '%',
                        style: percentageChange < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : ''
                    };
                };

                const appleChanges = calculateChanges(currData.apple, prevData.apple);
                const nonAppleChanges = calculateChanges(currData.nonApple, prevData.nonApple);
                const totalChanges = calculateChanges(currData.totalApple, prevData.totalApple);

                // Update the DOM
                const updateDOM = (selector, value) => {
                    $(selector).text(formatNumber(value));
                };

                updateDOM('#prevApple', prevData.apple);
                updateDOM('#prevNonApple', prevData.nonApple);
                updateDOM('#prevTotalApple', prevData.totalApple);
                updateDOM('#currApple', currData.apple);
                updateDOM('#currNonApple', currData.nonApple);
                updateDOM('#currTotalApple', currData.totalApple);
                updateDOM('#incDecApple', appleChanges.change);
                updateDOM('#incDecNonApple', nonAppleChanges.change);
                updateDOM('#incDecTotal', totalChanges.change);

                // Update percentage change displays
                $('#percentageChangeApple').text(appleChanges.roundedValue).attr('style', appleChanges.style);
                $('#percentageChangeNonApple').text(nonAppleChanges.roundedValue).attr('style', nonAppleChanges.style);
                $('#percentageChangeTotal').text(totalChanges.roundedValue).attr('style', totalChanges.style);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

    });


});
</script>
@endpush
