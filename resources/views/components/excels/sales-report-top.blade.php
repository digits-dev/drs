@props(['data', 'yearFrom', 'yearTo', 'dataLastThreeDays', 'lastThreeDays', 'lastThreeDaysAsDate'])

@php

    $weeks = [
        'WK01' => 'WEEK 1', 
        'WK02' => 'WEEK 2', 
        'WK03' => 'WEEK 3', 
        'WK04' => 'WEEK 4'
    ];

    $totalSalesOfYearTo = array_sum(array_map(function($item) {
        return $item['sum_of_net_sales'];
    }, $data[$yearTo]));

    $totalSalesOfYearFrom = array_sum(array_map(function($item) {
        return $item['sum_of_net_sales'];
    }, $data[$yearFrom]));

   
    $totalIncDecPercentage = $totalSalesOfYearFrom ? (($totalSalesOfYearTo - $totalSalesOfYearFrom) / $totalSalesOfYearFrom) * 100 : 0;
    $totalRounded = round($totalIncDecPercentage) . "%";
    $totalStyle = $totalRounded < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';


@endphp

<div style="width: 100%; font-family: Arial, sans-serif;">

    <div style="width: 100%;">
        {{-- <table style="width: 100%; border-collapse: collapse;  border-spacing: 0;"> --}}
            <thead>
                <tr>
                    {{-- <th class="bg-white" colspan="4">&nbsp;</th> --}}
                    <th style="font-weight: bold; background-color: white; text-align: left; height: 20px; vertical-align:middle; font-size:12px" colspan="4" rowspan="2">DAILY SALES REPORT</th>
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">CUT OFF</th>
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">CUT OFF</th>
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">CUT OFF</th>
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">CUT OFF</th>
                    <th style="background-color: white; text-align: center;" colspan="3">&nbsp;</th>
                </tr>
                <tr>
                    {{-- <th class="bg-white" style="text-align: left" colspan="4"><b>DAILY SALES REPORT</b></th> --}}
                    {{-- <th class="bg-white" style="text-align: left; font-size:13px;" colspan="4"><b>DAILY SALES REPORT</b></th> --}}
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">1-7</th>
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">8-14</th>
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">15-21</th>
                    <th style="font-weight: bold; background-color: #d9eaf9; color: black; text-decoration: underline; text-align: center; height: 20px; vertical-align:middle;">22 onwards</th>
           
                    {{-- Display Days like Thu, Fri, Sat  --}}
                    @php
                        $lastThreeDays = is_array($lastThreeDays) ? $lastThreeDays : [];

                        $count = count($lastThreeDays);
                        $blanks = 3 - $count; 
                    @endphp

                    @foreach ($lastThreeDays as $day)
                        <th style="font-weight: bold; background-color: white; text-align: center; vertical-align:middle;">{{$day}}</th>
                    @endforeach

                    @for ($i = 0; $i < $blanks; $i++)
                        <th style="font-weight: bold; background-color: white; text-align: center; vertical-align:middle;"></th> 
                    @endfor

         
                </tr>
                <tr>
                    <th style="width:  80px; background-color: #004b87; color: white; font-weight: bold; text-align: center; 10px; vertical-align:middle;">TOTAL</th>
                    <th style="width:  80px; background-color: #004b87; color: white; text-align: center; border-right:1px solid lightgray; vertical-align:middle;">YEAR</th>
                    <th style="width: 10px;">&nbsp;</th>
                    <th style="width: 120px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">RUNNING</th>

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
                    <td style="text-align: center; 10px; vertical-align:middle;">&nbsp;</td>
                    <td style="text-align: center; border-right:1px solid lightgray; vertical-align:middle;">% GROWTH</td>
                    <td style="background-color: white;">&nbsp;</td>
                    <td style="text-align: center; border-left:1px solid lightgray; vertical-align:middle; {{$totalStyle}}">{{$totalRounded}}</td>

                    @foreach (range(0, 3) as $number)

                        @php
                            $sum2024 = $data[$yearTo][$number]['sum_of_net_sales'] ?? 0; // Changed to null
                            $sum2023 = $data[$yearFrom][$number]['sum_of_net_sales'] ?? 0; // Changed to null

                            $incDecPercentage = $sum2023 ? (($sum2024 - $sum2023) / $sum2023 ) * 100 : 0 ;
                        $rounded = round($incDecPercentage);
                        $style = $rounded < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';

                  
                        @endphp

                        <td style="text-align: center; vertical-align:middle; {{$style}}" >{{$rounded}}%</td>
                    @endforeach



                    @foreach (range(0, 2) as $number)
                        @php
                            $sum2024 = $dataLastThreeDays[$yearTo][$number]['sum_of_net_sales'] ?? null;
                            $sum2023 = $dataLastThreeDays[$yearFrom][$number]['sum_of_net_sales'] ?? null;
                    
                            $style = '';
                            $rounded = '';
                    
                            if ($sum2024 !== null && $sum2023 !== null) {
                                $sum2024Date = (new DateTime($dataLastThreeDaysTo[$number]['date_of_the_day']))->format('d-M');
                                $sum2023Date = (new DateTime($dataLastThreeDaysFrom[$number]['date_of_the_day']))->format('d-M');
                                
                                if ($sum2023Date === $sum2024Date) {
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
                    <td style="font-weight: bold; text-align: center; 10px; vertical-align:middle;"><b>TOTAL</b></td>
                    <td style="font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;"><b>{{$yearFrom}}</b></td>
                    <td style="background-color: white; vertical-align:middle;">&nbsp;</td>
                    <td style="font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;"><b>{{$totalSalesOfYearFrom ? number_format($totalSalesOfYearFrom, 2) : ' ' }}</b></td>

                    @foreach (range(0,3) as $number)
                        @php
                            $curr = $data[$yearFrom][$number]['sum_of_net_sales'];
                        @endphp

                        <td style="font-weight: bold; text-align: center; vertical-align:middle;"><b>{{$curr ? number_format($curr, 2) : ""}}</b></td>

                    @endforeach

                    {{-- @foreach (range(0,2) as $number)
                        @php
                            $curr = $dataLastThreeDays[$yearFrom][$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{number_format($curr, 2)}}</b></td>
                    @endforeach --}}


                    @php
                    // Ensure $dataLastThreeDaysFrom is set and is an array
                    if (!is_array($dataLastThreeDays[$yearFrom])) {
                        $dataLastThreeDays[$yearFrom] = []; // Set to empty array if not valid
                    }

                    // Initialize sales data
                    $salesDate = $lastThreeDaysAsDate; // This should contain exactly three date entries
                    $salesData = []; // Initialize as an empty array

                    // Loop through the sales data
                    foreach ($dataLastThreeDays[$yearFrom] as $entry) {
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
                    <td style="font-weight: bold; text-align: center; vertical-align:middle;">
                        <b>
                            {{-- {{ isset($salesData[$i]) ? number_format($salesData[$i], 2) : '' }} --}}
                            {{ isset($salesData[$i]) && $salesData[$i] !== null && $salesData[$i] !== 0 ? number_format($salesData[$i], 2) : '' }}

                        </b>
                    </td>
                @endfor
                
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: center; 10px; vertical-align:middle;"><b>TOTAL</b></td>
                    <td style="font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;"><b>{{$yearTo}}</b></td>
                    <td style="background-color: white; vertical-align:middle;">&nbsp;</td>
                    <td style="font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;"><b>{{ $totalSalesOfYearTo ? number_format($totalSalesOfYearTo, 2) : " "}}</b></td>


                    @foreach (range(0,3) as $number)
                        @php
                            $curr = $data[$yearTo][$number]['sum_of_net_sales'];
                        @endphp

                        <td style="font-weight: bold; text-align: center; vertical-align:middle;"><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                    {{-- @foreach (range(0,2) as $number)
                        @php
                            $curr = $dataLastThreeDays[$yearTo][$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{number_format($curr, 2)}}</b></td>

                    @endforeach --}}



                    @php
                    // Ensure $dataLastThreeDaysFrom is set and is an array
                    if (!is_array($dataLastThreeDays[$yearTo])) {
                        $dataLastThreeDays[$yearTo] = []; // Set to empty array if not valid
                    }

                    // Initialize sales data
                    $salesDate = $lastThreeDaysAsDate; // This should contain exactly three date entries
                    $salesData = []; // Initialize as an empty array

                    // Loop through the sales data
                    foreach ($dataLastThreeDays[$yearTo] as $entry) {
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
                    <td style="font-weight: bold; text-align: center; vertical-align:middle;">
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