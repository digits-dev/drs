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

    if($totalSalesOfYearFrom !== 0 && $totalSalesOfYearTo !== 0){
        $totalIncDecPercentage = $totalSalesOfYearFrom ? (($totalSalesOfYearTo - $totalSalesOfYearFrom) / $totalSalesOfYearFrom) * 100 : 0;
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
                <tr>
                    <th class="bg-white" colspan="4">&nbsp;</th>
                    <th class="bg-light-blue underline">CUT OFF</th>
                    <th class="bg-light-blue underline">CUT OFF</th>
                    <th class="bg-light-blue underline">CUT OFF</th>
                    <th class="bg-light-blue underline">CUT OFF</th>
                    <th class="bg-white" colspan="3">&nbsp;</th>
                </tr>
                <tr>
                    <th class="bg-white" style="text-align: left" colspan="4"><b>DAILY SALES REPORT</b></th>
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
                        <th class="bg-white">{{$day}}</th>
                    @endforeach

                    @for ($i = 0; $i < $blanks; $i++)
                        <th class="bg-white"></th> 
                    @endfor

         
                </tr>
                <tr>
                    <th class="leftside-width">TOTAL</th>
                    <th class="leftside-width">YEAR</th>
                    <th class="none">&nbsp;</th>
                    <th class="rightside-width">RUNNING</th>

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

                    @foreach (range(0, 3) as $number)

                        @php
                            $sum2024 = $data[$yearTo][$number]['sum_of_net_sales'] ?? null; // Changed to null
                            $sum2023 = $data[$yearFrom][$number]['sum_of_net_sales'] ?? null; // Changed to null

                            // Check if both sums are not null before calculating
                            if ($sum2023 !== null && $sum2024 !== null) {
                                $incDecPercentage = (($sum2024 - $sum2023) / $sum2023) * 100;
                                $rounded = round($incDecPercentage);
                                $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                                $output = "{$rounded}%";
                            } else {
                                $style = ''; // No specific style needed for blank
                                $output = ''; // Leave blank if there's no partner
                            }
                        @endphp

                        <td class="none font-size" style="{{$style}}">{{$output}}</td>
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
                                        if($sum2024 == 0 &&  $sum2023 == 0){
                                            $style = '';
                                            $rounded = ''; 
                                        }else if ($sum2024 == 0){
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
                    <td><b>TOTAL</b></td>
                    <td><b>{{$yearFrom}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>
                    <td><b>{{$totalSalesOfYearFrom ? number_format($totalSalesOfYearFrom, 2) : ''}}</b></td>

                    @foreach (range(0,3) as $number)
                        @php
                            $curr = $data[$yearFrom][$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                    @foreach (range(0,2) as $number)
                        @php
                            $curr = $dataLastThreeDays[$yearFrom][$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>
                    @endforeach
                
                </tr>
                <tr>
                    <td><b>TOTAL</b></td>
                    <td><b>{{$yearTo}}</b></td>
                    <td class="none" style="width: 10px">&nbsp;</td>
                    <td><b>{{$totalSalesOfYearTo ? number_format($totalSalesOfYearTo, 2) : ''}}</b></td>


                    @foreach (range(0,3) as $number)
                        @php
                            $curr = $data[$yearTo][$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach

                    @foreach (range(0,2) as $number)
                        @php
                            $curr = $dataLastThreeDays[$yearTo][$number]['sum_of_net_sales'];
                        @endphp

                        <td><b>{{$curr ? number_format($curr, 2) : ''}}</b></td>

                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>