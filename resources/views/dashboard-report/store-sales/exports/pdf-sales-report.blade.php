<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>


    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <style>
        @page {
            margin: 10px; /* Default margin */
        }

        .page-break {
            page-break-before: always;
            /* Custom margin for the next page */
        }


        .content {
            height: 100%;
            width: 100%;
        }
        * {
            font-size: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        img {
            width: 50%;
            height: auto;
        }

        .center{
            display: grid;
            place-content: center;
        }
        .dashboard{
            margin-top: 90px;
        }

        .sales-report td {
            padding: 3px 2.5px !important; 
        }
    </style>


</head>
<body>

    @php
        $prevYear = $yearData['previousYear'];
        $currYear = $yearData['currentYear'];
        $month = $yearData['month'];
    @endphp

    {{-- DAILY SALES REPORT  --}}
    <div class="dashboard ">
        @foreach ($channel_codes as $channel => $channelData)
            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-pdf.daily-sales-report 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"
                :lastThreeDaysDates="$lastThreeDaysDates"
            />
        @endforeach
    </div>

    {{-- MONTHLY SALES REPORT  --}}
    <div class="page-break"></div>
    <div class="dashboard ">
        @foreach ($channel_codes as $channel => $channelData)
            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-pdf.monthly-sales-report
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"
            />
        @endforeach
    </div>

    {{-- QUARTERLY SALES REPORT  --}}
    <div class="page-break"></div>
    <div class="dashboard ">
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

    {{-- YTD SALES REPORT  --}}
    <div class="page-break"></div>
    <div class="dashboard ">
        @php
            $channelName = $channel_codes['TOTAL'][$currYear]['ytd']['SELECTED']['channel_name'];
            $conceptName = $channel_codes['TOTAL'][$currYear]['ytd']['SELECTED']['concept_name'];
        @endphp

        <x-pdf.ytd-sales-report
            :channelName="$channelName ? $channelName : 'ALL'"
            :storeConcept="$conceptName ? $conceptName : 'ALL'"
            :prevYear="$yearData['previousYear']" 
            :currYear="$yearData['currentYear']"
            :month="$yearData['month']"
            :prevYearYTDData="$channel_codes['TOTAL'][$prevYear]['ytd']"
            :currYearYTDData="$channel_codes['TOTAL'][$currYear]['ytd']"
        />
    </div>

    {{-- CHARTS  --}}

    {{-- @foreach ($chartUrls  as $chartUrl)
        @if(!empty($chartUrl))
            <div class="page-break"></div>
            <div class="content">
                <img src="{{ $chartUrl }}" 
                alt="" 
                style="width:100%; height:700px; border: 1px solid #ccc;">
            </div>

        @endif
    @endforeach --}}

</body>
</html>