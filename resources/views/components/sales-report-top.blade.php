@props(['data', 'yearFrom', 'yearTo', 'dataLastThreeDays'])

@php

    $totalSalesOfYearTo = array_sum(array_map(function($item) {
        return $item['sum_of_net_sales'];
    }, $data[$yearTo]));

    $totalSalesOfYearFrom = array_sum(array_map(function($item) {
        return $item['sum_of_net_sales'];
    }, $data[$yearFrom]));


    $totalIncDecPercentage = $totalSalesOfYearFrom ? (($totalSalesOfYearTo - $totalSalesOfYearFrom) / $totalSalesOfYearFrom) * 100 : 0;
    $totalRounded = round($totalIncDecPercentage);
    $totalStyle = $totalRounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';


// dump($dataLastThreeDays);
// dump($dataLastThreeDays[$yearTo]);

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
                    <th class="bg-white">{{$dataLastThreeDays[$yearTo][0]['day']}}</th>
                    <th class="bg-white">{{$dataLastThreeDays[$yearTo][1]['day']}}</th>
                    <th class="bg-white">{{$dataLastThreeDays[$yearTo][2]['day']}}</th>
                </tr>
                <tr>
                    <th class="leftside-width">TOTAL</th>
                    <th class="leftside-width">YEAR</th>
                    <th class="none">&nbsp;</th>
                    <th class="rightside-width">RUNNING</th>
                    <th class="rightside-width">WEEK 1</th>
                    <th class="rightside-width">WEEK 2</th>
                    <th class="rightside-width">WEEK 3</th>
                    <th class="rightside-width">WEEK 4</th>
                    <th class="rightside-width">{{$dataLastThreeDays[$yearTo][0]['date_of_the_day']}}</th>
                    <th class="rightside-width">{{$dataLastThreeDays[$yearTo][1]['date_of_the_day']}}</th>
                    <th class="rightside-width">{{$dataLastThreeDays[$yearTo][2]['date_of_the_day']}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td>% GROWTH</td>
                    <td class="none">&nbsp;</td>
                    <td class="none" style="{{$totalStyle}}">{{$totalRounded}}%</td>

                    @foreach (range(0,3) as $number)

                        @php
                            $sum2024 = $data[$yearTo][$number]['sum_of_net_sales'] ?? 0;
                            $sum2023 = $data[$yearFrom][$number]['sum_of_net_sales'] ?? 0;

                            $incDecPercentage = (($sum2024 - $sum2023) / $sum2023 ) * 100 ;
                            $rounded = round($incDecPercentage);
                            $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                        @endphp

                        <td class="none" style="{{$style}}">{{$rounded}}%</td>
                    @endforeach

                    @foreach (range(0,2) as $number)

                        @php
                            $sum2024 = $dataLastThreeDays[$yearTo][$number]['sum_of_net_sales'] ?? 0;
                            $sum2023 = $dataLastThreeDays[$yearFrom][$number]['sum_of_net_sales'] ?? 0;

                            $incDecPercentage = (($sum2024 - $sum2023) / $sum2023 ) * 100 ;
                            $rounded = round($incDecPercentage);
                            $style = $rounded < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                        @endphp

                        <td class="none" style="{{$style}}">{{$rounded}}%</td>
                    @endforeach
                    
                </tr>
                <tr>
                    <td><b>TOTAL</b></td>
                    <td><b>{{$yearFrom}}</b></td>
                    <td class="none">&nbsp;</td>
                    <td><b>{{number_format($totalSalesOfYearFrom, 2)}}</b></td>

                    {{-- sometimes it has 5 weeks  --}}
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
                    <td class="none">&nbsp;</td>
                    <td><b>{{number_format($totalSalesOfYearTo, 2)}}</b></td>

                    {{-- sometimes it has 5 weeks  --}}

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