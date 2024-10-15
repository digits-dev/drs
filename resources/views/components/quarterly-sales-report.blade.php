@props([
    'isTopOpen' => false,
    'channel' => 'TOTAL', 
    'data',
    'prevYear', 
    'currYear', 
    'lastThreeDaysDates',
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

    // dump($prevYearQuartersData);
    // dump($currYearQuartersData);

@endphp

<style type="text/css">
    .quarterly-sales-report {
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .quarterly-sales-report table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .quarterly-sales-report th, .quarterly-sales-report td {
        padding: 2px;
        text-align: center;
    }

    .quarterly-sales-report td {
        border: 1px solid #e1e1e1;
    }

    .quarterly-sales-report th {
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
    /* .quarterly-sales-report tr th:nth-child(-n+2) {
        width: 110px;

    } */
    /* width for 4th col to end */
    /* .quarterly-sales-report th:nth-child(n+4),
    .quarterly-sales-report td:nth-child(n+4) {
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

    <div class="quarterly-sales-report">
      
        <table>
            <colgroup>
                <col span="2" style="width: 10%;"> <!-- 25% of the table's width -->
                <col span="5" style="width: 15%;"> <!-- 25% of the table's width -->
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
                    <th class="border-right">YEAR</th>
                    {{-- <th class="none">&nbsp;</th> --}}

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
                    <td class="font-size border-right no-border-right" >% GROWTH</td>
                    {{-- <td class="none" style="width: 10px">&nbsp;</td> --}}

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
                    <td class="border-right"><b>{{$prevYear}}</b></td>
                    {{-- <td class="none" style="width: 10px">&nbsp;</td> --}}

                    @foreach ($quarters as $key => $quarter)
                        @php
                            $curr = $prevYearQuartersData[$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach



                </tr>

                {{-- ROW 3  --}}
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td class="border-right"><b>{{$currYear}}</b></td>
                    {{-- <td class="none" style="width: 10px">&nbsp;</td> --}}


                    @foreach ($quarters as $key => $quarter)
                        @php
                            $curr = $currYearQuartersData[$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                    

                </tr>
            </tbody>
        </table>
    </div>
</div>