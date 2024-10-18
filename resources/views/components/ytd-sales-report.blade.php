@props([
    'prevYear', 
    'currYear',
    'month',
    'prevYearYTDData',
    'currYearYTDData'
])

@php
    $monthName = strtoupper(date('F', mktime(0, 0, 0, $month, 1)));
@endphp


<div>

    <div class="sales-report">
      
        <table>
            {{-- <colgroup>
                <col style="width:150px;" >
                <col style="width:10px;">
                <col span="4" style="width:125px;">
            </colgroup> --}}

            <colgroup>
                <col >
                <col style="width:15px;">
                <col span="4" >
            </colgroup>

            <thead>
                
                <tr>
                    <th>GROUP</th>
                    <th class="none">&nbsp;</th>

                    <th>{{$currYear}}YTD {{$monthName}}</th>
                    <th>{{$prevYear}}YTD {{$monthName}}</th>

                    <th>INC/(DEC)</th>
                    <th>% INC/(DEC)</th>
                   
                </tr>
            </thead>
            <tbody>
                {{-- ROW 1  --}}
               
                <tr>
                    <td><b>APPLE</b></td>
                    <th class="none">&nbsp;</th>

                    @php
                        $prevApple = $prevYearYTDData['APPLE']['sum_of_net_sales'] ?? 0;
                        $currApple = $currYearYTDData['APPLE']['sum_of_net_sales'] ?? 0;

                        $incDecPercentage = $prevApple ? (($currApple - $prevApple) / $prevApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                    @endphp

                    <td id="currApple">{{ number_format($currApple, 2) }}</td>
                    <td id="prevApple">{{ number_format($prevApple, 2) }}</td>
                    <td id="incDecApple">{{ number_format($currApple - $prevApple, 2) }}</td>
                    <td id="percentageChangeApple" style="{{$style}}">{{$roundedVal}}</td>

                </tr>

                {{-- ROW 2 --}}
                <tr>
                    <td><b>NON APPLE</b></td>
                    <th class="none">&nbsp;</th>

                    @php
                        $prevNonApple = $prevYearYTDData['NON-APPLE']['sum_of_net_sales'] ?? 0;
                        $currNonApple = $currYearYTDData['NON-APPLE']['sum_of_net_sales'] ?? 0;

                        $incDecPercentage = $prevNonApple ? (($currNonApple - $prevNonApple) / $prevNonApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                    @endphp

                    <td id="currNonApple">{{ number_format($currNonApple, 2) }}</td>
                    <td id="prevNonApple">{{ number_format($prevNonApple, 2) }}</td>
                    <td id="incDecNonApple">{{ number_format($currNonApple - $prevNonApple, 2) }}</td>
                    <td id="percentageChangeNonApple" style="{{$style}}">{{$roundedVal}}</td>

                </tr>

                {{-- ROW 3 --}}
                <tr>
                    <td><b>Grand Total</b></td>
                    <th class="none">&nbsp;</th>

                    @php
                        $prevTotalApple = $prevYearYTDData['TOTAL']['sum_of_net_sales'] ?? 0;
                        $currTotalApple = $currYearYTDData['TOTAL']['sum_of_net_sales'] ?? 0;

                        $incDecPercentage = $prevTotalApple ? (($currTotalApple - $prevTotalApple) / $prevTotalApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                    @endphp

                    <td id="currTotalApple" >{{ number_format($currTotalApple, 2) }}</td>
                    <td id="prevTotalApple" >{{ number_format($prevTotalApple, 2) }}</td>
                    <td id="incDecTotal" >{{ number_format($currTotalApple - $prevTotalApple, 2) }}</td>
                    <td id="percentageChangeTotal"  style="{{$style}}">{{$roundedVal}}</td>

                </tr>
            </tbody>
        </table>
    </div>
</div>