@props([
    'isTopOpen' => false,
    'channel' => 'TOTAL', 
    'data',
    'prevYear', 
    'currYear', 
    'lastThreeDaysDates',
])

@php
    $weeks = [
        'TOTAL' => 'RUNNING',
        'WK01' => 'WEEK 1', 
        'WK02' => 'WEEK 2', 
        'WK03' => 'WEEK 3', 
        'WK04' => 'WEEK 4'
    ];

    //Previous Year Data
    $prevYearWeeksData = $data[$prevYear]['weeks'];
    $prevYearLastThreeDays = $data[$prevYear]['last_three_days'];

    //Current Year Data
    $currYearWeeksData = $data[$currYear]['weeks'];
    $currYearLastThreeDays = $data[$currYear]['last_three_days'];

@endphp

<div>
    <div class="sales-report">
        <table>
            {{-- <colgroup>
                <col span="2" style="width:110px;">
                <col style="width:15px;">
                <col span="8" style="width:125px;">
            </colgroup> --}}

            <colgroup>
                <col span="2" >
                <col style="width:15px;">
                <col span="8" >
            </colgroup>

            <thead>
                
                @if ($isTopOpen)
               
                    <tr>
                        <th  class="none" style="text-align: left; font-size:14px;" colspan="4" rowspan="2"><b>DAILY SALES REPORT</b></th>
                        
                        @foreach (range(0,3) as $item)
                            <th class="bg-light-blue underline ">CUT OFF</th>
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
                            $lastThreeDaysDates = is_array($lastThreeDaysDates) ? $lastThreeDaysDates : [];
                            $count = count($lastThreeDaysDates);
                            $blanks = 3 - $count; 
                        @endphp

                        {{-- Display if it has data  --}}
                        @foreach ($lastThreeDaysDates as $key => $day)
                            <th class="none">{{$day}}</th>
                        @endforeach

                        {{-- Display the blanks th --}}
                        @for ($i = 0; $i < $blanks; $i++)
                            <th class="none"></th> 
                        @endfor

                    </tr>
                @endif

                @php
                    switch ($channel) {
                        case 'RTL':
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

                    {{-- Display Weeks like WEEK1, WEEK2, etc.  --}}
                    @foreach ($weeks as $week)
                        <th>{{$week}}</th>
                    @endforeach

                    {{-- Display Date like 01-Oct, 02-Oct  --}}
                    @php
                        $lastThreeDaysDates = is_array($lastThreeDaysDates) ? $lastThreeDaysDates : [];

                        $count = count($lastThreeDaysDates);
                        $blanks = 3 - $count; 
                    @endphp

                    {{-- Display if it has data  --}}
                    @foreach ($lastThreeDaysDates as $key => $day)
                        <th class="bg-light-green">{{$key}}</th>
                    @endforeach

                    {{-- Display the blanks th --}}
                    @for ($i = 0; $i < $blanks; $i++)
                        <th class="bg-light-green"></th> 
                    @endfor
                </tr>
            </thead>
            <tbody>
                {{-- ROW 1  --}}
                <tr>
                    <td>&nbsp;</td>
                    <td class="font-size" >% GROWTH</td>
                    <th class="none">&nbsp;</th>

                    @foreach ($weeks as $key => $week)
                        @php
                            $previousYear =  $prevYearWeeksData[$key]['sum_of_net_sales'] ?? 0;
                            $currentYear = $currYearWeeksData[$key]['sum_of_net_sales'] ?? 0;

                            $incDecPercentage = $previousYear ? (($currentYear - $previousYear) / $previousYear ) * 100 : 0 ;
                            $roundedVal = round($incDecPercentage);
                            $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                        @endphp
            
                        <td class="none font-size" style="{{$style}}">{{$roundedVal}}%</td>
                    @endforeach


                    @foreach (range(0, 2) as $number)
                        @php
                            $previousYear = $prevYearLastThreeDays[$number]['sum_of_net_sales'] ?? 0;
                            $currentYear = $currYearLastThreeDays[$number]['sum_of_net_sales'] ?? null;
                            $style = '';
                            $rounded = '';

                            // Only process if both previous and current year data exist
                            if ($currentYear !== null && $previousYear !== null) {
                                // Extract and format the dates for both years
                                $currentYearDate = (new DateTime($currYearLastThreeDays[$number]['date_of_the_day']))->format('d-M');
                                $previousYearDate = (new DateTime($prevYearLastThreeDays[$number]['date_of_the_day']))->format('d-M');

                                // Compare the dates
                                if ($previousYearDate === $currentYearDate) {
                                    // Calculate percentage change if both previous and current data are available
                                    $incDecPercentage = $previousYear ? (($currentYear - $previousYear) / $previousYear) * 100 : 0;
                                } elseif ($previousYear === 0) {
                                    // Handle the case where the previous year is 0, to avoid division by zero
                                    $incDecPercentage = 0; // If previousYear is 0, the change is 100%
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
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$prevYear}}</b></td>
                    <th class="none">&nbsp;</th>

                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $prevYearWeeksData[$key]['sum_of_net_sales'];
                            
                            $formattedVal = $curr ? number_format($curr, 2) : '';
                        @endphp

                        <td title="{{$formattedVal}}"><b>{{$formattedVal}}</b></td>

                    @endforeach


                    @php
                        // Ensure $prevYearLastThreeDays is set and is an array, defaulting to empty if invalid
                        $prevYearLastThreeDays = is_array($prevYearLastThreeDays) ? $prevYearLastThreeDays : [];

                        // Initialize the sales data array, mapping sales dates to sales amounts
                        $salesData = collect($prevYearLastThreeDays)->mapWithKeys(function ($entry) use ($lastThreeDaysDates) {
                            // dump($entry);

                            // Format the date from YYYY-MM-DD to d-M
                            $formattedDate = (new DateTime($entry['date_of_the_day']))->format('d-M');

                            return isset($lastThreeDaysDates[$formattedDate]) ? [$formattedDate => $entry['sum_of_net_sales']] : [];
                        })->toArray(); 

                    @endphp

                    @foreach ($salesData as $saleData)
                        @php
                            $val = $saleData !== 0 ? number_format($saleData, 2) : '';
                        @endphp

                        <td title="{{$val}}"><b>{{$val}}</b></td>
                    @endforeach

                    @if (empty($salesData))
                        @foreach (range(0,2) as $blank)
                            <td><b>{{''}}</b></td>
                        @endforeach
                    @endif

                </tr>

                {{-- ROW 3  --}}
                <tr>
                    <td><b>{{strtoupper($channel) }}</b></td>
                    <td><b>{{$currYear}}</b></td>
                    <td class="none">&nbsp;</td>

                    @foreach ($weeks as $key => $week)
                        @php
                            $curr = $currYearWeeksData[$key]['sum_of_net_sales'];

                            $formattedVal = $curr ? number_format($curr, 2) : '';
                        @endphp

                        <td title="{{$formattedVal}}"><b>{{$formattedVal}}</b></td>

                    @endforeach

                    @php
                        // Ensure $prevYearLastThreeDays is set and is an array, defaulting to empty if invalid
                        $currYearLastThreeDays = is_array($currYearLastThreeDays) ? $currYearLastThreeDays : [];

                        // Initialize the sales data array, mapping sales dates to sales amounts
                        $salesData = collect($currYearLastThreeDays)->mapWithKeys(function ($entry) use ($lastThreeDaysDates) {
                            // dump($entry);
                            // Format the date from YYYY-MM-DD to d-M
                            $formattedDate = (new DateTime($entry['date_of_the_day']))->format('d-M');

                            return isset($lastThreeDaysDates[$formattedDate]) ? [$formattedDate => $entry['sum_of_net_sales']] : [];
                        })->toArray();  
                    @endphp

                    @foreach ($salesData as $saleData)
                        @php
                            $val = $saleData !== 0 ? number_format($saleData, 2) : '';
                        @endphp

                        <td title="{{$val}}"><b>{{$val}}</b></td>
                    @endforeach

                    @if (empty($salesData))
                        @foreach (range(0,2) as $blank)
                            <td><b>{{''}}</b></td>
                        @endforeach
                    @endif

                </tr>
            </tbody>
        </table>
    </div>
</div>