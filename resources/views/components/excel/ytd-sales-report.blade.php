@props([
    'prevYear', 
    'currYear',
    'month',
    'prevYearYTDData',
    'currYearYTDData',
    'channelName' => '',
    'storeConcept' => ''
])

@php
    $monthName = strtoupper(date('F', mktime(0, 0, 0, $month - 1, 1)));

@endphp

<div style="width: 100%; font-family: Arial, sans-serif; ">

    <div style="width: 100%;">
        {{-- <table> --}}
            <thead>
                

                <tr>
                    <th  style="font-weight: bold; background-color: white; text-align: left; height: 50px; vertical-align:middle; font-size:12px;" colspan="7" ><b>YTD SALES REPORT</b></th>
                </tr>

                <tr>
                    <th  style="background-color: white; text-align: left; height: 20px; vertical-align:middle; font-size:11px;" colspan="7" ><b>CHANNEL: {{$channelName}}</b></th>
                </tr>

                <tr>
                    <th  style="background-color: white; text-align: left; height: 30px; vertical-align:middle; font-size:11px;" colspan="7" ><b>STORE CONCEPT: {{$storeConcept}}</b></th>
                </tr>

                <tr>
                    <th style="width: 80px; background-color: #004b87; color: white; text-align: center; border-right:1px solid lightgray; vertical-align:middle;" colspan="2">GROUP</th>
                    <th style="width: 10px;">&nbsp;</th>

                    <th style="width: 140px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">{{$currYear}}YTD {{$monthName}}</th>
                    <th style="width: 140px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">{{$prevYear}}YTD {{$monthName}}</th>

                    <th style="width: 120px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">INC/(DEC)</th>
                    <th style="width: 120px; background-color: #004b87; color: white; text-align: center;  vertical-align:middle;">% INC/(DEC)</th>
                   
                </tr>
            </thead>
            <tbody>

                {{-- ROW 1  --}}
            
                <tr>
                    <td style="text-align: center; border-right:1px solid lightgray; vertical-align:middle;" colspan="2"><b>APPLE</b></td>
                    <th style="background-color: white;">&nbsp;</th>

                    @php
                        $prevApple = $prevYearYTDData['APPLE']['sum_of_net_sales'] ?? 0;
                        $currApple = $currYearYTDData['APPLE']['sum_of_net_sales'] ?? 0;
                        $incDecApple = $currApple - $prevApple;

                        $incDecPercentage = $prevApple ? (($incDecApple) / $prevApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';
                    @endphp

                    <td id="currApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $currApple ? number_format($currApple, 2) : '' }}</td>
                    <td id="prevApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $prevApple ? number_format($prevApple, 2) : '' }}</td>
                    <td id="incDecApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $incDecApple ? number_format($incDecApple, 2) : '' }}</td>
                    <td id="percentageChangeApple" style="text-align: center; border-left:1px solid lightgray; vertical-align:middle; {{$style}}">{{$roundedVal}}</td>

                </tr>

                {{-- ROW 2 --}}
                <tr>
                    <td style="color:black; font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;" colspan="2"><b>NON APPLE</b></td>
                    <th style="color:black; background-color: white; vertical-align:middle;">&nbsp;</th>

                    @php
                        $prevNonApple = $prevYearYTDData['NON-APPLE']['sum_of_net_sales'] ?? 0;
                        $currNonApple = $currYearYTDData['NON-APPLE']['sum_of_net_sales'] ?? 0;
                        $incDecNonApple = $currNonApple - $prevNonApple; 

                        $incDecPercentage = $prevNonApple ? (($incDecNonApple) / $prevNonApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';

                    @endphp

                    <td id="currNonApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $currNonApple ? number_format($currNonApple, 2) : '' }}</td>
                    <td id="prevNonApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $prevNonApple ? number_format($prevNonApple, 2) : '' }}</td>
                    <td id="incDecNonApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $incDecNonApple ? number_format($incDecNonApple, 2) : '' }}</td>
                    <td id="percentageChangeNonApple" style="text-align: center; border-left:1px solid lightgray; vertical-align:middle; {{$style}}">{{$roundedVal}}</td>

                </tr>


                 {{-- ROW 3 --}}
                 <tr>
                    <td style="color:black; font-weight: bold; text-align: center; border-right:1px solid lightgray; vertical-align:middle;" colspan="2"><b>Grand Total</b></td>
                    <th style="color:black; background-color: white; vertical-align:middle;">&nbsp;</th>

                    @php
                        $prevTotalApple = $prevYearYTDData['TOTAL']['sum_of_net_sales'] ?? 0;
                        $currTotalApple = $currYearYTDData['TOTAL']['sum_of_net_sales'] ?? 0;
                        $incDecTotal = $currTotalApple - $prevTotalApple;

                        $incDecPercentage = $prevTotalApple ? (($incDecTotal) / $prevTotalApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background-color:#f7c2c2; color:#880808;' : '';

                    @endphp

                    <td id="currTotalApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $currTotalApple ? number_format($currTotalApple, 2) : '' }}</td>
                    <td id="prevTotalApple" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $prevTotalApple ? number_format($prevTotalApple, 2) : '' }}</td>
                    <td id="incDecTotal" style="color:black; font-weight: bold; text-align: center; border-left:1px solid lightgray; vertical-align:middle;">{{ $incDecTotal ? number_format($incDecTotal, 2) : '' }}</td>
                    <td id="percentageChangeTotal"  style="text-align: center; border-left:1px solid lightgray; vertical-align:middle; {{$style}}">{{$roundedVal}}</td>

                </tr>
            </tbody>
        {{-- </table> --}}
    </div>
</div>