@props(['channel' => 'Total', 'data', 'yearFrom', 'yearTo', 'dataFrom', 'dataTo', 'dataLastThreeDaysFrom', 'dataLastThreeDaysTo'])

<style type="text/css">
    .sales-report {
        width: 100%;
        margin: 20px auto;
        font-family: Arial, sans-serif;
    }

    .sales-report table {
        width: 100%;
        border-collapse: collapse;
    }

    .sales-report th, .sales-report td {
        /* border: 1px solid #ccc; */
        /* border: 1px solid red; */
        padding: 8px;
        text-align: center;
    }

    .sales-report th {
        background-color: #004b87;
        color: white;
        font-weight: bold;
    }


    .sales-report tbody tr:nth-child(odd) {
        background-color: white;
    }

    .sales-report tbody tr:nth-child(even) {
        background-color: white;
    }

    .sales-report td {
        font-size: 14px;
    }

    .sales-report tbody tr td:first-child {
        font-weight: bold;
    }

    .sales-report td {
        padding: 12px;
        border: 1px solid #e1e1e1;
    }

    /* Add green background ONLY to the last three header cells */
    .sales-report th:nth-last-child(-n+3) {
        background-color: #d4edda; /* Light green */
        color: #155724; /* Dark green text */
    }

    .none {
        background-color: white !important;
        border: none !important;
    }


    .bg-white{
        background: white !important;
        color:black !important;
        border: none;       
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


    .leftside-width{
        width: 110px;
    }
    .rightside-width{
        width: 125px;
    }

    th,tr,td,td b{
        font-size: 13px;
    }
    .font-size{
        font-size: 13px !important;
    }
    
</style>

@php
    $weeks = ['WK01', 'WK02', 'WK03', 'WK04'];
    // dump($dataTo);
    // dump($dataFrom);

    $totalSalesOfDataTo = 0; // Default value

    if (!empty($dataTo)) { // Check if dataTo has data
        $totalSalesOfDataTo = array_sum(array_map(function($item) {
            // Return the value if it's not null or empty
            return !empty($item['sum_of_net_sales']) ? $item['sum_of_net_sales'] : 0;
        }, $dataTo));
    }


    $totalSalesOfDataFrom = 0; // Default value

    if (!empty($dataFrom)) { // Check if dataTo has data
        $totalSalesOfDataFrom = array_sum(array_map(function($item) {
            // Return the value if it's not null or empty
            return !empty($item['sum_of_net_sales']) ? $item['sum_of_net_sales'] : 0;
        }, $dataFrom));
    }
    
    // $totalSalesOfDataTo = array_sum(array_map(function($item) {
    //     return $item['sum_of_net_sales'];
    // }, $dataTo));

    // // dump($totalSalesOfDataTo);

    // $totalSalesOfDataFrom = array_sum(array_map(function($item) {
    //     return $item['sum_of_net_sales'];
    // }, $dataFrom));

    // dump($totalSalesOfDataFrom);

// dd('stop');

    $totalIncDecPercentage = $totalSalesOfDataFrom ? (($totalSalesOfDataTo - $totalSalesOfDataFrom) / $totalSalesOfDataFrom) * 100 : 0;
    // dump($totalIncDecPercentage);

    $totalRounded = round($totalIncDecPercentage);
    // dump($totalRounded);

    $totalStyle = $totalRounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';


    // dump($dataLastThreeDaysFrom);
    // dump($dataLastThreeDaysTo);
@endphp
<div>

    <div class="sales-report">
        <table>
            <thead>
                <tr>
                    <th class="leftside-width" >{{strtoupper($channel) }}</th>
                    <th class="leftside-width" >YEAR</th>
                    <th class="none">&nbsp;</th>
                    <th class="rightside-width">RUNNING</th>
                    <th class="rightside-width">WEEK 1</th>
                    <th class="rightside-width">WEEK 2</th>
                    <th class="rightside-width">WEEK 3</th>
                    <th class="rightside-width">WEEK 4</th>
                    <th class="rightside-width">{{$dataLastThreeDaysTo[0]['date_of_the_day']}}</th>
                    <th class="rightside-width">{{$dataLastThreeDaysTo[1]['date_of_the_day']}}</th>
                    <th class="rightside-width">{{$dataLastThreeDaysTo[2]['date_of_the_day']}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td class="font-size">% GROWTH</td>
                    <td class="none" style="width: 10px">&nbsp;</td>
                    <td class="none font-size" style="{{$totalStyle}}">{{$totalRounded}}%</td>

                    @foreach ($weeks as $week)

                        @php
                            $sum2024 = $dataTo[$week]['sum_of_net_sales'] ?? 0;
                            $sum2023 = $dataFrom[$week]['sum_of_net_sales'] ?? 0;

                            $incDecPercentage = $sum2023 ? (($sum2024 - $sum2023) / $sum2023 ) * 100 : 0;

                            $rounded = round($incDecPercentage);
                            $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                        @endphp

                        <td class="none font-size" style="{{$style}}">{{$rounded}}%</td>
                    @endforeach

                    @foreach (range(0,2) as $number)

                        @php
                            $sum2024 = $dataLastThreeDaysTo[$number]['sum_of_net_sales'] ?? 0;
                            $sum2023 = $dataLastThreeDaysFrom[$number]['sum_of_net_sales'] ?? 0;


                            $incDecPercentage = $sum2023 ? (($sum2024 - $sum2023) / $sum2023 ) * 100 : 0;
                            $rounded = round($incDecPercentage);
                            $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                        @endphp

                        <td class="none font-size" style="{{$style}}">{{$rounded}}%</td>
                    @endforeach
                </tr>
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$yearFrom}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>
                    <td><b>{{number_format($totalSalesOfDataFrom, 2)}}</b></td>

                    @foreach ($weeks as $week)
                        @php
                            $curr = $dataFrom[$week]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                    @foreach (range(0,2) as $number)
                        @php
                            $curr = $dataLastThreeDaysFrom[$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                </tr>
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$yearTo}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>
                    <td><b>{{number_format($totalSalesOfDataTo, 2)}}</b></td>

                    @foreach ($weeks as $week)
                        @php
                            $curr = $dataTo[$week]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                    @foreach (range(0,2) as $number)
                        @php
                            $curr = $dataLastThreeDaysTo[$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>