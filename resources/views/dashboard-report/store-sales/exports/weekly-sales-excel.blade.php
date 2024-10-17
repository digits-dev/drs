
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
    <div class="dashboard">

        <table>
     
            @foreach ($channel_codes as $channel => $channelData)

                @if ($channel == 'OTHER' || $channel == '')
                    @continue
                @endif
                
                <x-excels.sales-report 
                    :isTopOpen="$loop->first"
                    :channel="$channel" 
                    :data="$channelData"
                    :prevYear="$yearData['previousYear']" 
                    :currYear="$yearData['currentYear']"
                    :lastThreeDaysDates="$lastThreeDaysDates"

                />
            
            @endforeach

        </table>

    </div>

    <div class="dashboard">
     
        <table>

        @foreach ($channel_codes as $channel => $channelData)

            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-excels.monthly-sales-report 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"

            />
          
        @endforeach

        </table>


    </div>

    <div class="dashboard">

        <table>

     
        @foreach ($channel_codes as $channel => $channelData)

            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-excels.quarterly-sales-report 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"

            />
          
        @endforeach


        </table>

    </div>
</body>
</html>



