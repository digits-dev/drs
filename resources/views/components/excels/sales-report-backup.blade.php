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
    
  
    $totalIncDecPercentage = $totalSalesOfDataFrom ? (($totalSalesOfDataTo - $totalSalesOfDataFrom) / $totalSalesOfDataFrom) * 100 : 0;

    $totalRounded = round($totalIncDecPercentage) . "%";

    $totalStyle = $totalRounded < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';
  
    // dump($dataLastThreeDaysFrom);
    // dump($dataLastThreeDaysTo);
    // dump($lastThreeDaysAsDate);



@endphp
<div style="width: 100%; font-family: Arial, sans-serif; ">

    <div style="width: 100%;">
        {{-- <table style="width: 100%; border-collapse: collapse;  border-spacing: 0;"> --}}
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
                    <th style="width: 80px; background-color: #004b87; color: white; font-weight: bold; text-align: center; vertical-align:middle;">{{strtoupper($channelCode) }}</th>
                    <th style="width: 80px; background-color: #004b87; color: white; text-align: center; border-right:1px solid lightgray; vertical-align:middle;" >YEAR</th>
                    <th style="width: 10px;">&nbsp;</th>
                    <th style="width: 120px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">RUNNING</th>

                    {{-- Display Weeks like WEEK1, WEEK2, etc.  --}}

                    @foreach ($weeks as $week)
                        <th style="width: 120px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">{{$week}}</th>
                    @endforeach


                    {{-- Display Date like 01-Oct, 02-Oct  --}}
                    @php
                        $lastThreeDaysAsDate = is_array($lastThreeDaysAsDate) ? $lastThreeDaysAsDate : [];

                        $count = count($lastThreeDaysAsDate);
                        $blanks = 3 - $count; 
                    @endphp

                    @foreach ($lastThreeDaysAsDate as $day)
                        <th style="width: 120px; background-color: #d4edda; color: 155724; text-align: center;  vertical-align:middle;"><b>{{$day}}</b></th>
                    @endforeach

                    @for ($i = 0; $i < $blanks; $i++)
                        <th style="width: 120px; background-color: #d4edda; color: 155724; text-align: center;  vertical-align:middle;"></th> 
                    @endfor

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; vertical-align:middle;">&nbsp;</td>
                    <td style="text-align: center; border-right:1px solid lightgray; vertical-align:middle;">% GROWTH</td>
                    <td style="background-color: white;">&nbsp;</td>
                    <td style="text-align: center; border-left:1px solid lightgray; vertical-align:middle; {{$totalStyle}}">{{$totalRounded}}</td>

                    @foreach ($weeks as $key => $week)

                        @php
                        $sum2024 = $dataTo[$key]['sum_of_net_sales'] ?? 0;
                        $sum2023 =  $dataFrom[$key]['sum_of_net_sales'] ?? 0;
                
                        $incDecPercentage = $sum2023 ? (($sum2024 - $sum2023) / $sum2023 ) * 100 : 0 ;
                        $rounded = round($incDecPercentage);
                        $style = $rounded < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';
                    @endphp
            
                    <td style="text-align: center; vertical-align:middle; {{$style}}">{{$rounded}}%</td>
                    @endforeach


                    @foreach (range(0, 2) as $number)
                        @php
                 
                            $sum2024 = $dataLastThreeDaysTo[$number]['sum_of_net_sales'] ?? null;
                            $sum2023 = $dataLastThreeDaysFrom[$number]['sum_of_net_sales'] ?? 0;

                    
                            $style = '';
                            $rounded = '';
                    
                            if ($sum2024 !== null && $sum2023 !== null) {
                                $sum2024Date = (new DateTime($dataLastThreeDaysTo[$number]['date_of_the_day']))->format('d-M');
                                $sum2023Date = (new DateTime($dataLastThreeDaysFrom[$number]['date_of_the_day']))->format('d-M');
                                
                                if ($sum2023Date === $sum2024Date) {
                                    $incDecPercentage = $sum2023 ? (($sum2024 - $sum2023) / $sum2023) * 100 : 0;
                                            $rounded = round($incDecPercentage) . '%';
                                            $style = $rounded < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';
                                } else if ($sum2024 !== null && $sum2023 === 0){
                                    $incDecPercentage = $sum2023 ? (($sum2024 - $sum2023) / $sum2023) * 100 : 0;
                                            $rounded = round($incDecPercentage) . '%';
                                            $style = $rounded < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';
                                }
                            } 
                    
                        @endphp
                
                        <td style="text-align: center; vertical-align:middle; {{$style}}">{{$rounded}}</td>
                    @endforeach


                </tr>
                <tr>
                    <td style="color:black; font-weight: bold; text-align: center; vertical-align:middle;"><b>{{strtoupper($channel) }}</b></td>
                    <td style="color:black; font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;"><b>{{$yearFrom}}</b></td>
                    <td style="color:black; background-color: white; vertical-align:middle;">&nbsp;</td>
                    <td style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;"><b>{{$totalSalesOfDataFrom ? number_format($totalSalesOfDataFrom, 2) : ''}}</b></td>


                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $dataFrom[$key]['sum_of_net_sales'];
                        @endphp

                        <td style="color:black; font-weight: bold; text-align: center; vertical-align:middle;"><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

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
                        <td style="font-weight: bold; text-align: center; vertical-align:middle; color:black;">
                            <b>
                                {{-- {{ isset($salesData[$i]) ? number_format($salesData[$i], 2) : '' }} --}}
                                {{ isset($salesData[$i]) && $salesData[$i] !== null && $salesData[$i] !== 0 ? number_format($salesData[$i], 2) : '' }}
                            </b>
                        </td>
                    @endfor

                </tr>
                <tr>
                    <td style="color:black; font-weight: bold; text-align: center; vertical-align:middle;"><b>{{strtoupper($channel) }}</b></td>
                    <td style="color:black; font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;"><b>{{$yearTo}}</b></td>
                    <td style="color:black; background-color: white; vertical-align:middle;">&nbsp;</td>

                    <td style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;"><b>{{ $totalSalesOfDataTo ? number_format($totalSalesOfDataTo, 2) : ""}}</b></td>

                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $dataTo[$key]['sum_of_net_sales'];
                        @endphp

                        <td style="color:black; font-weight: bold; text-align: center; vertical-align:middle;"><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

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
                        <td style="color:black; font-weight: bold; text-align: center; vertical-align:middle;">
                            <b>
                                {{-- {{ isset($salesData[$i]) ? number_format($salesData[$i], 2) : '' }} --}}
                                {{ isset($salesData[$i]) && $salesData[$i] !== null && $salesData[$i] !== 0 ? number_format($salesData[$i], 2) : '' }}

                            </b>
                        </td>
                    @endfor

                </tr>
            </tbody>
        {{-- </table> --}}
    </div>
</div>