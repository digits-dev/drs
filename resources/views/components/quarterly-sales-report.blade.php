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

<div>

    <div class="sales-report">
      
        <table>
            {{-- <colgroup>
                <col span="2" style="width:100px;">
                <col style="width:10px;">
                <col span="5" style="width:125px;">
            </colgroup> --}}

            <colgroup>
                <col span="2" >
                <col style="width:15px;">
                <col span="5" >
            </colgroup>

            <thead>
                
                @if ($isTopOpen)
               
                    <tr>
                        <th  class="none" style="text-align: left; font-size:14px; height:50px;" colspan="7" ><b>QUARTERLY SALES REPORT</b></th>
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
                    <th>{{strtoupper($channelCode) }}</th>
                    <th>YEAR</th>
                    <th class="none">&nbsp;</th>

                    {{-- Display quarters like quarter1, quarter2, etc.  --}}
                    @foreach ($quarters as $quarter)
                        <th>{{$quarter}}</th>
                    @endforeach

                   
                </tr>
            </thead>
            <tbody>
                {{-- ROW 1  --}}
                <tr>
                    <td>&nbsp;</td>
                    <td class="font-size" >% GROWTH</td>
                    <th class="none">&nbsp;</th>

                    @foreach ($quarters as $key => $quarter)
                        @php
                            $previousYear =  $prevYearQuartersData[$key]['sum_of_net_sales'] ?? 0;
                            $currentYear = $currYearQuartersData[$key]['sum_of_net_sales'] ?? 0;

                            $incDecPercentage = $previousYear ? (($currentYear - $previousYear) / $previousYear ) * 100 : 0 ;
                            $roundedVal = round($incDecPercentage);
                            $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                        @endphp
            
                        <td class="none font-size" style="{{$style}}">{{$roundedVal}}%</td>
                    @endforeach


                    

                </tr>

                {{-- ROW 2  --}}
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$prevYear}}</b></td>
                    <th class="none">&nbsp;</th>

                    @foreach ($quarters as $key => $quarter)
                        @php
                            $curr = $prevYearQuartersData[$key]['sum_of_net_sales'];

                            $formattedVal = $curr ? number_format($curr, 2) : '';
                        @endphp

                        <td title="{{$formattedVal}}"><b>{{$formattedVal}}</b></td>

                    @endforeach



                </tr>

                {{-- ROW 3  --}}
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$currYear}}</b></td>
                    <th class="none">&nbsp;</th>

                    @foreach ($quarters as $key => $quarter)
                        @php
                            $curr = $currYearQuartersData[$key]['sum_of_net_sales'];

                            $formattedVal = $curr ? number_format($curr, 2) : '';
                        @endphp

                        <td title="{{$formattedVal}}"><b>{{$formattedVal}}</b></td>

                    @endforeach

                </tr>
            </tbody>
        </table>
    </div>
</div>