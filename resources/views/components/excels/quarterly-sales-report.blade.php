@props([
    'isTopOpen' => false,
    'channel' => 'TOTAL', 
    'data',
    'prevYear', 
    'currYear', 
])

@php

    $quarters = [
        'TOTAL' => 'RUNNING',
        'Q1' => 'QUARTER 1', 
        'Q2' => 'QUARTER 2', 
        'Q3' => 'QUARTER 3', 
        'Q4' => 'QUARTER 4'
    ];

    //Previous Year Data
    $prevYearQuartersData = $data[$prevYear]['quarters'];

    //Current Year Data
    $currYearQuartersData = $data[$currYear]['quarters'];

@endphp

<div style="width: 100%; font-family: Arial, sans-serif; ">

    <div style="width: 100%;">
        {{-- <table> --}}
            <thead>
                
                @if ($isTopOpen)
               
                    <tr>
                        <th style="font-weight: bold; background-color: white; text-align: left; height: 50px; vertical-align:middle; font-size:12px;" colspan="7"><b>QUARTERLY SALES REPORT</b></th>
                    </tr>

                @endif

                @php
                    switch ($channel) {
                        case 'TOTAL-RTL':
                            $channelCode = 'RETAIL';
                            break;
                        case 'DLR/CRP':
                            $channelCode = 'OUT';
                            break;
                        case 'FRA-DR':
                            $channelCode = 'FRA';
                            break;
                        default:
                            $channelCode = $channel;
                    }
                @endphp

                <tr>
                    <th style="width: 80px; background-color: #004b87; color: white; font-weight: bold; text-align: center; vertical-align:middle;">{{strtoupper($channelCode) }}</th>
                    <th style="width: 80px; background-color: #004b87; color: white; text-align: center; border-right:1px solid lightgray; vertical-align:middle;">YEAR</th>
                    <th style="width: 10px;">&nbsp;</th>

                    {{-- Display months like JANUARY, FEB, etc.  --}}
                    @foreach ($quarters as $quarter)
                        <th style="width: 120px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">{{$quarter}}</th>
                    @endforeach

                </tr>
            </thead>
            <tbody>
                {{-- ROW 1  --}}
                <tr>
                    <td style="text-align: center; vertical-align:middle;">&nbsp;</td>
                    <td style="text-align: center; border-right:1px solid lightgray; vertical-align:middle;">% GROWTH</td>
                    <td style="background-color: white;">&nbsp;</td>

                    @foreach ($quarters as $key => $quarter)
                        @php
                            $previousYear =  $prevYearQuartersData[$key]['sum_of_net_sales'] ?? 0;
                            $currentYear = $currYearQuartersData[$key]['sum_of_net_sales'] ?? 0;

                            $incDecPercentage = $previousYear ? (($currentYear - $previousYear) / $previousYear ) * 100 : 0 ;
                            $roundedVal = round($incDecPercentage);
                            $style = $roundedVal < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';
                        @endphp
            
                        <td style="text-align: center; border-left:1px solid lightgray; vertical-align:middle; {{$style}}">{{$roundedVal}}%</td>
                    @endforeach

                </tr>

                {{-- ROW 2  --}}
                <tr>
                    <td style="color:black; font-weight: bold; text-align: center; vertical-align:middle;"><b>{{strtoupper($channel) }}</b></td>
                    <td style="color:black; font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;"><b>{{$prevYear}}</b></td>
                    <td style="color:black; background-color: white; vertical-align:middle;">&nbsp;</td>

                    @foreach ($quarters as $key => $quarter)
                        @php
                            $curr = $prevYearQuartersData[$key]['sum_of_net_sales'];
                        @endphp

                        <td style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;"><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                </tr>

                {{-- ROW 3  --}}
                <tr>
                    <td style="color:black; font-weight: bold; text-align: center; vertical-align:middle;"><b>{{strtoupper($channel) }}</b></td>
                    <td style="color:black; font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;"><b>{{$currYear}}</b></td>
                    <td style="color:black; background-color: white; vertical-align:middle;">&nbsp;</td>


                    @foreach ($quarters as $key => $quarter)
                        @php
                            $curr = $currYearQuartersData[$key]['sum_of_net_sales'];
                        @endphp

                        <td style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;"><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                </tr>
            </tbody>
        {{-- </table> --}}
    </div>
</div>