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
/* 
        td {
            white-space: initial !important;        
            overflow: unset !important;          
            text-overflow: unset !important;   
            padding: 0 !important;
        } */
        
        .sales-report td {
            padding: 3px 2.5px !important; 
        }
    </style>


</head>
<body>
    <div class="dashboard ">
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

    <div class="page-break"></div>

    <div class="dashboard ">
        @foreach ($channel_codes as $channel => $channelData)
            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-monthly-sales-report-pdf 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"
            />
        @endforeach
    </div>

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

    @foreach ($chartUrls  as $chartUrl)
        @if(!empty($chartUrl))
            {{-- page break --}}
            <div class="page-break"></div>
            <div class="content">
                <img src="{{ $chartUrl }}" 
                alt="" 
                style="width:100%; height:700px; border: 1px solid #ccc;">
            </div>

            {{-- <div class="page-break"></div> --}}
        @endif
    @endforeach

</body>
</html>
