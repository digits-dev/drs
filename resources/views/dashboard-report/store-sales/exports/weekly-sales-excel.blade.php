
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

     .export{
            padding:10px;
            display: flex;
            gap:10px;
            justify-content: flex-end;
            border-bottom: 1px solid #ddd;
        }

        .weekly-section{
            background: white;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <x-excels.sales-report-top 
            :data="$summary" 
            :yearFrom="$yearMonthData['year1']" 
            :yearTo="$yearMonthData['year2']" 
            :dataLastThreeDays="$summary_last_three_days"
            :lastThreeDaysAsDate="$lastThreeDaysAsDate"
            :lastThreeDays="$lastThreeDays"
        />

        @foreach ($channel_codes as $channel => $years)
            @php
                $dataFrom = $years[$yearMonthData['year1']]['weeks'];
                $dataTo = $years[$yearMonthData['year2']]['weeks'];
            @endphp

            @if ($channel == 'OTHER')
                @continue
            @endif
            
            <x-excels.sales-report :channel="$channel" 
                :dataFrom="$dataFrom" 
                :dataTo="$dataTo" 
                :yearFrom="$yearMonthData['year1']" 
                :yearTo="$yearMonthData['year2']"
                :dataLastThreeDaysFrom="$years[$yearMonthData['year1']]['last_three_days']"
                :dataLastThreeDaysTo="$years[$yearMonthData['year2']]['last_three_days']"
                :lastThreeDaysAsDate="$lastThreeDaysAsDate"
            />
        @endforeach

    </div>
</body>
</html>



