@props(['data', 
'yearFrom', 
'yearTo', 
'dataLastThreeDays', 
'lastThreeDays', 
'lastThreeDaysAsDate'])

@php

    $weeks = [
        'TOTAL' => 'RUNNING',
        'WK01' => 'WEEK 1', 
        'WK02' => 'WEEK 2', 
        'WK03' => 'WEEK 3', 
        'WK04' => 'WEEK 4'
    ];
    // dump($data);

@endphp

<div>

    <div class="sales-report">
        <table>
            <thead>
                <tr>
                    <th class="none" style="text-align: left; font-size:14px;" colspan="4" rowspan="2"><b>DAILY SALES REPORT</b></th>
                    
                    @foreach (range(0,3) as $item)
                        <th class="bg-light-blue underline">CUT OFF</th>
                    @endforeach
           
                    <th class="none" colspan="3">&nbsp;</th>
                </tr>
                <tr>
                    <th class="bg-light-blue">1-7</th>
                    <th class="bg-light-blue">8-14</th>
                    <th class="bg-light-blue">15-21</th>
                    <th class="bg-light-blue">22 onwards</th>
           
                    {{-- Display Days like Thu, Fri, Sat  --}}
                    @php
                        $lastThreeDays = is_array($lastThreeDays) ? $lastThreeDays : [];

                        $count = count($lastThreeDays);
                        $blanks = 3 - $count; 
                    @endphp

                    @foreach ($lastThreeDays as $day)
                        <th class="none">{{$day}}</th>
                    @endforeach

                    @for ($i = 0; $i < $blanks; $i++)
                        <th class="none"></th> 
                    @endfor

                </tr>

                <tr>
                    <th>TOTAL</th>
                    <th>YEAR</th>
                    <th class="none">&nbsp;</th>

                    @foreach ($weeks as $week)
                        <th>{{$week}}</th>
                    @endforeach

                    {{-- Display Date like 01-Oct, 02-Oct  --}}
                    @php
                        $lastThreeDaysAsDate = is_array($lastThreeDaysAsDate) ? $lastThreeDaysAsDate : [];

                        $count = count($lastThreeDaysAsDate);
                        $blanks = 3 - $count; 
                    @endphp

                    @foreach ($lastThreeDaysAsDate as $day)
                        <th>{{$day}}</th>
                    @endforeach

                    @for ($i = 0; $i < $blanks; $i++)
                        <th></th> 
                    @endfor
              
                </tr>
            </thead>
            <tbody>
                {{-- ROW 1 --}}
                <tr>
                    <td>&nbsp;</td>
                    <td class="font-size">% GROWTH</td>
                    <td class="none" style="width: 10px">&nbsp;</td>

                    @foreach ($weeks as $key => $week)
                        @php
                        $currentYear = $data[$yearTo][$key]['sum_of_net_sales'] ?? 0; // Changed to null
                        $previousYear = $data[$yearFrom][$key]['sum_of_net_sales'] ?? 0; // Changed to null

                        $incDecPercentage = $previousYear ? (($currentYear - $previousYear) / $previousYear ) * 100 : 0 ;
                        $rounded = round($incDecPercentage);
                        $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';

                
                        @endphp

                        <td class="none font-size" style="{{$style}}">{{$rounded}}%</td>
                    @endforeach

                    @foreach (range(0, 2) as $number)
                        @php
                            $previousYear = $dataLastThreeDays[$yearFrom][$number]['sum_of_net_sales'] ?? 0;
                            $currentYear = $dataLastThreeDays[$yearTo][$number]['sum_of_net_sales'] ?? null;
                            $style = '';
                            $rounded = '';

                            // Only process if both previous and current year data exist
                            if ($currentYear !== null && $previousYear !== null) {
                                // Extract and format the dates for both years
                                $previousYearDate = (new DateTime($dataLastThreeDays[$yearFrom][$number]['date_of_the_day']))->format('d-M');
                                $currentYearDate = (new DateTime($dataLastThreeDays[$yearTo][$number]['date_of_the_day']))->format('d-M');

                                // Compare the dates
                                if ($previousYearDate === $currentYearDate) {
                                    // Calculate percentage change if both previous and current data are available
                                    $incDecPercentage = $previousYear ? (($currentYear - $previousYear) / $previousYear) * 100 : 0;
                                } elseif ($previousYear === 0) {
                                    // Handle the case where the previous year is 0, to avoid division by zero
                                    $incDecPercentage = 100; // If previousYear is 0, the change is 100%
                                } else {
                                    $incDecPercentage = 0;
                                }

                                // Round the percentage and determine style
                                $rounded = round($incDecPercentage) . '%';
                                $style = $incDecPercentage < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                            }
                        @endphp

                        <td class="none font-size" style="{{ $style }}">{{ $rounded }}</td>
                    @endforeach

                </tr>

                {{-- ROW 2  --}}
                <tr>
                    <td><b>TOTAL</b></td>
                    <td><b>{{$yearFrom}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>

                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $data[$yearFrom][$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ""}}</b></td>

                    @endforeach

                    @php
                        // Ensure $dataLastThreeDaysFrom is set and is an array, defaulting to empty if invalid
                        $dataLastThreeDaysFrom = is_array($dataLastThreeDays[$yearFrom]) ? $dataLastThreeDays[$yearFrom] : [];

                        // Initialize the sales data array, mapping sales dates to sales amounts
                        $salesData = collect($dataLastThreeDaysFrom)->mapWithKeys(function ($entry) use ($lastThreeDaysAsDate) {
                            // Format the date from YYYY-MM-DD to d-M
                            $formattedDate = (new DateTime($entry['date_of_the_day']))->format('d-M');

                            // Check if the formatted date matches any in the $lastThreeDaysAsDate array
                            $index = array_search($formattedDate, $lastThreeDaysAsDate);

                            // Only add to salesData if a matching date is found
                            return $index !== false ? [$index => $entry['sum_of_net_sales']] : [];
                        })->toArray(); 

                    @endphp

                    @for ($i = 0; $i < 3; $i++)
                        <td>
                            <b>
                                {{-- {{ isset($salesData[$i]) ? number_format($salesData[$i], 2) : '' }} --}}

                                {{ isset($salesData[$i]) && $salesData[$i] !== null && $salesData[$i] !== 0 ? number_format($salesData[$i], 2) : '' }}
                            </b>
                        </td>
                    @endfor
                
                </tr>

                {{-- ROW 3 --}}
                <tr>
                    <td><b>TOTAL</b></td>
                    <td><b>{{$yearTo}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>

                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $data[$yearTo][$key]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                    @php
                        // Ensure $dataLastThreeDaysFrom is set and is an array, defaulting to empty if invalid
                        $dataLastThreeDaysTo = is_array($dataLastThreeDays[$yearTo]) ? $dataLastThreeDays[$yearTo] : [];

                        // Initialize the sales data array, mapping sales dates to sales amounts
                        $salesData = collect($dataLastThreeDaysTo)->mapWithKeys(function ($entry) use ($lastThreeDaysAsDate) {
                            // Format the date from YYYY-MM-DD to d-M
                            $formattedDate = (new DateTime($entry['date_of_the_day']))->format('d-M');

                            // Check if the formatted date matches any in the $lastThreeDaysAsDate array
                            $index = array_search($formattedDate, $lastThreeDaysAsDate);

                            // Only add to salesData if a matching date is found
                            return $index !== false ? [$index => $entry['sum_of_net_sales']] : [];
                        })->toArray(); 

                    @endphp

                    @for ($i = 0; $i < 3; $i++)
                        <td>
                            <b>
                                {{-- {{ isset($salesData[$i]) ? number_format($salesData[$i], 2) : '' }} --}}

                                {{ isset($salesData[$i]) && $salesData[$i] !== null && $salesData[$i] !== 0 ? number_format($salesData[$i], 2) : '' }}
                            </b>
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>
</div>