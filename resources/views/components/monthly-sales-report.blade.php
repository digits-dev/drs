@props([
    'isTopOpen' => false,
    'channel' => 'TOTAL', 
    'data',
    'prevYear', 
    'currYear', 
])

@php

    $months = [
        'TOTAL' => 'RUNNING',
        'M01' => 'JANUARY',
        'M02' => 'FEBRUARY',
        'M03' => 'MARCH',
        'M04' => 'APRIL',
        'M05' => 'MAY',
        'M06' => 'JUNE',
        'M07' => 'JULY',
        'M08' => 'AUGUST',
        'M09' => 'SEPTEMBER',
        'M10' => 'OCTOBER',
        'M11' => 'NOVEMBER',
        'M12' => 'DECEMBER',
    ];

    //Previous Year Data
    $prevYearMonthsData = $data[$prevYear]['months'];

    //Current Year Data
    $currYearMonthsData = $data[$currYear]['months'];

@endphp

<div>

    <div class="sales-report">
    
        <table>
            {{-- <colgroup>
                <col span="2" style="width:80px;">
                <col style="width:10px;">
                <col span="8">
            </colgroup> --}}

            <colgroup>
                <col span="2" >
                <col style="width:10px;">
                <col span="8" >
            </colgroup>

            <thead>
                
                @if ($isTopOpen)
               
                    <tr>
                        <th  class="none" style="text-align: left; font-size:14px; height:50px;" colspan="8"><b>MONTHLY SALES REPORT</b></th>
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

                    {{-- Display Months like JANUARY, FEB, etc.  --}}
                    @foreach ($months as $month)
                        <th>{{$month}}</th>
                    @endforeach

                 
                </tr>
            </thead>
            <tbody>
                {{-- ROW 1  --}}
                <tr>
                    <td>&nbsp;</td>
                    <td class="font-size" >% GROWTH</td>
                    <th class="none">&nbsp;</th>

                    @foreach ($months as $key => $month)
                        @php
                            $previousYear =  $prevYearMonthsData[$key]['sum_of_net_sales'] ?? 0;
                            $currentYear = $currYearMonthsData[$key]['sum_of_net_sales'] ?? 0;

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

                    @foreach ($months as $key => $month)
                        @php
                            $curr = $prevYearMonthsData[$key]['sum_of_net_sales'];

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

                    @foreach ($months as $key => $month)
                        @php
                            $curr = $currYearMonthsData[$key]['sum_of_net_sales'];
                      
                            $formattedVal = $curr ? number_format($curr, 2) : '';
                        @endphp

                        <td title="{{$formattedVal}}"><b>{{$formattedVal}}</b></td>

                    @endforeach
                    
                </tr>
            </tbody>
        </table>
    </div>
</div>