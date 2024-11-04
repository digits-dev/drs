
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
     *{
        font-size: 10px;
     }
     .dashboard  {
        margin-top: 50px;
     }
    </style>
</head>
<body>

    @php
        $hasDTC = isset($channel_codes['DTC']) && $channel_codes['DTC'];
        $prevYear = $yearData['previousYear'];
        $currYear = $yearData['currentYear'];
        $month = $yearData['month'];
    @endphp


    {{-- DAILY SALES REPORT  --}}
    <div class="dashboard">
        <table>
            @foreach ($channel_codes as $channel => $channelData)

                @if ($hasDTC && ($channel == 'OTHER' || $channel == '' || $channel == 'TOTAL'))
                    @continue
                @endif

                @if (!$hasDTC && ($channel == 'OTHER' || $channel == ''))
                    @continue
                @endif
                
                <x-excel.daily-sales-report 
                    :isTopOpen="$channel == 'TOTAL' || $channel == 'DTC'"
                    :channel="$channel" 
                    :data="$channelData"
                    :prevYear="$yearData['previousYear']" 
                    :currYear="$yearData['currentYear']"
                    :lastThreeDaysDates="$lastThreeDaysDates"

                />
            @endforeach
        </table>
    </div>


    {{-- MONTHLY SALES REPORT  --}}
    <div class="dashboard">
        <table>
            @foreach ($channel_codes as $channel => $channelData)

                @if ($hasDTC && ($channel == 'OTHER' || $channel == '' || $channel == 'TOTAL'))
                    @continue
                @endif

                @if (!$hasDTC && ($channel == 'OTHER' || $channel == ''))
                    @continue
                @endif
                
                <x-excel.monthly-sales-report 
                    :isTopOpen="$channel == 'TOTAL' || $channel == 'DTC'"
                    :channel="$channel" 
                    :data="$channelData"
                    :prevYear="$yearData['previousYear']" 
                    :currYear="$yearData['currentYear']"

                />
            @endforeach
        </table>
    </div>

    {{-- QUARTERLY SALES REPORT  --}}
    <div class="dashboard">
        <table>
            @foreach ($channel_codes as $channel => $channelData)
            
                @if ($hasDTC && ($channel == 'OTHER' || $channel == '' || $channel == 'TOTAL'))
                    @continue
                @endif

                @if (!$hasDTC && ($channel == 'OTHER' || $channel == ''))
                    @continue
                @endif
                
                <x-excel.quarterly-sales-report 
                    :isTopOpen="$channel == 'TOTAL' || $channel == 'DTC'"
                    :channel="$channel" 
                    :data="$channelData"
                    :prevYear="$yearData['previousYear']" 
                    :currYear="$yearData['currentYear']"

                />
            @endforeach
        </table>
    </div>


    {{-- YTD SALES REPORT  --}}
    <div class="dashboard">
        @php
            $channelName = $channel_codes['TOTAL'][$currYear]['ytd']['SELECTED']['channel_name'];
            $conceptName = $channel_codes['TOTAL'][$currYear]['ytd']['SELECTED']['concept_name'];
        @endphp
        <table>
            <x-excel.ytd-sales-report 
                :channelName="$channelName ? $channelName : 'ALL'"
                :storeConcept="$conceptName ? $conceptName : 'ALL'"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"
                :month="$yearData['month']"
                :prevYearYTDData="$channel_codes['TOTAL'][$prevYear]['ytd']"
                :currYearYTDData="$channel_codes['TOTAL'][$currYear]['ytd']"

            />
        </table>
    </div>
</body>
</html>



