@props([
    'channel' => 'Total', 
    'data', 
    'yearFrom', 
    'yearTo', 
    'dataFrom', 
    'dataTo', 
    'dataLastThreeDaysFrom', 
    'dataLastThreeDaysTo',
    'lastThreeDaysAsDate'
])

<style type="text/css">
    .sales-report {
        width: 100%;
        margin: 0 auto;
        font-family: Arial, sans-serif;
    }

    .sales-report table {
        width: 100%;
        border-collapse: collapse;
    }

    .sales-report th, .sales-report td {
        padding: 3px;
        text-align: center;
    }

    .sales-report td {
        padding: 3px;
        border: 1px solid #e1e1e1;
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


    .sales-report tbody tr td:first-child {
        font-weight: bold;
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
        font-size: 12px;
    }
    .font-size{
        font-size: 12px !important;
    }
    
</style>

@php
    $weeks = [
        'WK01' => 'WEEK 1', 
        'WK02' => 'WEEK 2', 
        'WK03' => 'WEEK 3', 
        'WK04' => 'WEEK 4'
    ];

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
    
    if($totalSalesOfDataFrom !== 0 && $totalSalesOfDataTo !== 0){
    $totalIncDecPercentage = $totalSalesOfDataFrom ? (($totalSalesOfDataTo - $totalSalesOfDataFrom) / $totalSalesOfDataFrom) * 100 : 0;

    $totalRounded = round($totalIncDecPercentage) . "%";

    $totalStyle = $totalRounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
    } else {
        $totalRounded = '';
        $totalStyle = '';
    }


@endphp
<div>

    <div class="sales-report">
        <table>
            <thead>
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
                    <th class="leftside-width" >{{strtoupper($channelCode) }}</th>
                    <th class="leftside-width" >YEAR</th>
                    <th class="none">&nbsp;</th>
                    <th class="rightside-width">RUNNING</th>

                    {{-- Display Weeks like WEEK1, WEEK2, etc.  --}}

                    @foreach ($weeks as $week)
                        <th class="rightside-width">{{$week}}</th>
                    @endforeach


                    {{-- Display Date like 01-Oct, 02-Oct  --}}
                    @php
                        $lastThreeDaysAsDate = is_array($lastThreeDaysAsDate) ? $lastThreeDaysAsDate : [];

                        $count = count($lastThreeDaysAsDate);
                        $blanks = 3 - $count; 
                    @endphp

                    @foreach ($lastThreeDaysAsDate as $day)
                        <th class="rightside-width">{{$day}}</th>
                    @endforeach

                    @for ($i = 0; $i < $blanks; $i++)
                        <th class="rightside-width"></th> 
                    @endfor

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td class="font-size">% GROWTH</td>
                    <td class="none" style="width: 10px">&nbsp;</td>
                    <td class="none font-size" style="{{$totalStyle}}">{{$totalRounded}}</td>

                    @foreach ($weeks as $key => $week)
                        @php
                            // Get sums with fallback to null

                            $sum2024 = $dataTo[$key]['sum_of_net_sales'] ?? null;
                            $sum2023 = $dataFrom[$key]['sum_of_net_sales'] ?? null;

                            if ($sum2023 !== null && $sum2024 !== null) {
                                $incDecPercentage = (($sum2024 - $sum2023) / $sum2023) * 100;
                                $rounded = round($incDecPercentage) . "%";
                                $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                            } else {
                                $style = ''; // No specific style needed for blank
                                $rounded = '';
                            }
                        
                        @endphp

                        <td class="none font-size" style="{{$style}}">{{$rounded}}</td>
                    @endforeach


                    @foreach (range(0, 2) as $number)
                        @php
                            $sum2024 = $dataLastThreeDaysTo[$number]['sum_of_net_sales'] ?? null;
                            $sum2023 = $dataLastThreeDaysFrom[$number]['sum_of_net_sales'] ?? null;

                    
                            $style = '';
                            $rounded = '';
                    
                            if ($sum2024 !== null && $sum2023 !== null) {
                                $sum2024Date = (new DateTime($dataLastThreeDaysTo[$number]['date_of_the_day']))->format('d-M');
                                $sum2023Date = (new DateTime($dataLastThreeDaysFrom[$number]['date_of_the_day']))->format('d-M');
                                
                                if ($sum2023Date === $sum2024Date) {
                                        if($sum2024 == 0 &&  $sum2023 == 0){
                                            $style = '';
                                            $rounded = '';
                                        } else if ($sum2024 == 0){
                                            $style = '';
                                            $rounded = '';
                                        }else if ($sum2023 == 0){
                                            $style = '';
                                            $rounded = '';
                                        }
                                        
                                        else{
                                            $incDecPercentage = $sum2023 ? (($sum2024 - $sum2023) / $sum2023) * 100 : 0;
                                    $rounded = round($incDecPercentage) . '%';
                                    $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                                }
                                
                                } 
                            } 
                    
                        @endphp
                
                        <td class="none font-size" style="{{$style}}">{{$rounded}}</td>
                    @endforeach

                </tr>
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$yearFrom}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>
                    <td><b>{{$totalSalesOfDataFrom ? number_format($totalSalesOfDataFrom, 2) : ''}}</b></td>


                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $dataFrom[$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach


    
                    @php
                        // Ensure $dataLastThreeDaysFrom is set and is an array
                        if (!is_array($dataLastThreeDaysFrom)) {
                            $dataLastThreeDaysFrom = []; // Set to empty array if not valid
                        }

                        // Initialize sales data
                        $salesDate = $lastThreeDaysAsDate; // This should contain exactly three date entries
                        $salesData = []; // Initialize as an empty array

                        // Loop through the sales data
                        foreach ($dataLastThreeDaysFrom as $entry) {
                            // Format the date from YYYY-MM-DD to DD-MMM
                            $formattedDate = (new DateTime($entry['date_of_the_day']))->format('d-M');

                            foreach ($salesDate as $index => $date) {
                                // Compare formatted date
                                if ($formattedDate === $date) {
                                    $salesData[$index] = $entry['sum_of_net_sales'];
                                    break; 
                                }
                            }
                        }
                    @endphp


                    @for ($i = 0; $i < 3; $i++)
                        <td>
                            <b>
                                {{ isset($salesData[$i]) && $salesData[$i] !== null && $salesData[$i] !== 0 ? number_format($salesData[$i], 2) : '' }}
                            </b>
                        </td>
                    @endfor

                </tr>
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$yearTo}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>

                    <td><b>{{$totalSalesOfDataTo ? number_format($totalSalesOfDataTo, 2) : ''}}</b></td>

                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $dataTo[$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                       @php
                       // Ensure $dataLastThreeDaysFrom is set and is an array
                       if (!is_array($dataLastThreeDaysTo)) {
                           $dataLastThreeDaysTo = []; // Set to empty array if not valid
                       }

                       // Initialize sales data
                       $salesDate = $lastThreeDaysAsDate; // This should contain exactly three date entries
                       $salesData = []; // Initialize as an empty array

                       // Loop through the sales data
                       foreach ($dataLastThreeDaysTo as $entry) {
                           // Format the date from YYYY-MM-DD to DD-MMM
                           $formattedDate = (new DateTime($entry['date_of_the_day']))->format('d-M');

                           foreach ($salesDate as $index => $date) {
                               // Compare formatted date
                               if ($formattedDate === $date) {
                                   $salesData[$index] = $entry['sum_of_net_sales'];
                                   break; 
                               }
                           }
                       }
                   @endphp

                    @for ($i = 0; $i < 3; $i++)
                        <td>
                            <b>
                                {{ isset($salesData[$i]) && $salesData[$i] !== null && $salesData[$i] !== 0 ? number_format($salesData[$i], 2) : '' }}
                            </b>
                        </td>
                    @endfor

                </tr>
            </tbody>
        </table>
    </div>
</div>