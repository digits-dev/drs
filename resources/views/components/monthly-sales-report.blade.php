@props([
    'isTopOpen' => false,
    'channel' => 'TOTAL', 
    'data',
    'prevYear', 
    'currYear', 
    'lastThreeDaysDates',
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

    // dump($prevYearMonthsData);
    // dump($currYearMonthsData);

@endphp

<style type="text/css">
    .monthly-sales-report {
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .monthly-sales-report table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .monthly-sales-report th, .monthly-sales-report td {
        padding: 2px;
        text-align: center;
    }

    .monthly-sales-report td {
        border: 1px solid #e1e1e1;
    }

    .monthly-sales-report th {
        background-color: #004b87;
        color: white;
        font-weight: bold;
    }


    .none {
        background-color: white !important;
        border: none !important;
        color:black !important;
    }

    .bg-light-blue{
        background-color: #d9eaf9 !important;
        color: black !important;
    }

    .underline {
        position: relative; 
    }
        
    .underline::after {
        content: ""; 
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 2px; 
        height: 1px; 
        width: 50%;
        background: black; 
    }

    
    /* width for first two th  */
    /* .monthly-sales-report tr th:nth-child(-n+2) {
        width: 110px;

    } */
    /* width for 4th col to end */
    /* .monthly-sales-report th:nth-child(n+4),
    .monthly-sales-report td:nth-child(n+4) {
        width: 125px;
    } */


    .border-right {
    position: relative;
}

.border-right::after {
    content: ' ';
    position: absolute;
    top: 0;
    right: -2.5px;
    height: 105%;
    width: 10px; /* Corrected from 'widows' to 'width' */
    border-left: 1px solid #e1e1e1;
    border-right: 1px solid #e1e1e1;
    background: white;
}

.no-border-right {
    border-right: unset !important;
}

.no-border-right::after {
    border-right: unset !important;
}




</style>

<div>

    <div class="monthly-sales-report">
    
        <table>
{{-- 
                <colgroup>
                    <col span="2" style="width: 100px;"> 
                   <col style="width: 10px;"> 
                    <col >             
                </colgroup> --}}
            <thead>
                
                @if ($isTopOpen)
               
                    <tr>
                        <th  class="none" style="text-align: left; font-size:14px; height:50px;" colspan="12"><b>MONTHLY SALES REPORT</b></th>
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
                    <th class="border-right">YEAR</th>

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
                    <td class="font-size border-right no-border-right" >% GROWTH</td>

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
                    <td class="border-right"><b>{{$prevYear}}</b></td>

                    @foreach ($months as $key => $month)
                        @php
                            $curr = $prevYearMonthsData[$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach
                </tr>

                {{-- ROW 3  --}}
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td class="border-right"><b>{{$currYear}}</b></td>


                    @foreach ($months as $key => $month)
                        @php
                            $curr = $currYearMonthsData[$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach
                    
                </tr>
            </tbody>
        </table>
    </div>
</div>