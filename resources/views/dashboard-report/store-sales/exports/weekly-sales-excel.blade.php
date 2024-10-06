
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
    </style>
</head>
<body>
    <div class="dashboard">
     
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

    </div>
</body>
</html>



