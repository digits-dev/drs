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
                    <th  class="none" style="text-align: left; font-size:14px; height:50px;" colspan="6" ><b>YTD SALES REPORT</b></th>
                </tr>
                <tr>
                    <th  class="none" style="text-align: left; font-size:12px;" colspan="6" ><b>CHANNEL: {{$channelName}} </b></th>
                </tr>
                <tr>
                    <th  class="none" style="text-align: left; font-size:12px; height:20px;" colspan="6" ><b>STORE CONCEPT: {{$storeConcept}}</b></th>
                </tr>

                
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
                        $incDecApple = $currApple - $prevApple;

                        $incDecPercentage = $prevApple ? (($incDecApple) / $prevApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                    @endphp

                    <td id="currApple">{{ $currApple ? number_format($currApple, 2) : '' }}</td>
                    <td id="prevApple">{{ $prevApple ? number_format($prevApple, 2) : '' }}</td>
                    <td id="incDecApple">{{ $incDecApple ? number_format($incDecApple, 2) : '' }}</td>
                    <td id="percentageChangeApple" style="{{$style}}">{{$roundedVal}}</td>

                </tr>

                {{-- ROW 2 --}}
                <tr>
                    <td><b>NON APPLE</b></td>
                    <th class="none">&nbsp;</th>

                    @php
                        $prevNonApple = $prevYearYTDData['NON-APPLE']['sum_of_net_sales'] ?? 0;
                        $currNonApple = $currYearYTDData['NON-APPLE']['sum_of_net_sales'] ?? 0;
                        $incDecNonApple = $currNonApple - $prevNonApple; 

                        $incDecPercentage = $prevNonApple ? (($incDecNonApple) / $prevNonApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                    @endphp

                    <td id="currNonApple">{{ $currNonApple ? number_format($currNonApple, 2) : '' }}</td>
                    <td id="prevNonApple">{{ $prevNonApple ? number_format($prevNonApple, 2) : '' }}</td>
                    <td id="incDecNonApple">{{ $incDecNonApple ? number_format($incDecNonApple, 2) : '' }}</td>
                    <td id="percentageChangeNonApple" style="{{$style}}">{{$roundedVal}}</td>

                </tr>

                {{-- ROW 3 --}}
                <tr>
                    <td><b>Grand Total</b></td>
                    <th class="none">&nbsp;</th>

                    @php
                        $prevTotalApple = $prevYearYTDData['TOTAL']['sum_of_net_sales'] ?? 0;
                        $currTotalApple = $currYearYTDData['TOTAL']['sum_of_net_sales'] ?? 0;
                        $incDecTotal = $currTotalApple - $prevTotalApple;

                        $incDecPercentage = $prevTotalApple ? (($incDecTotal) / $prevTotalApple ) * 100 : 0 ;
                        $roundedVal = round($incDecPercentage) . '%';
                        $style = $roundedVal < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : '';
                    @endphp

                    <td id="currTotalApple" >{{ $currTotalApple ? number_format($currTotalApple, 2) : '' }}</td>
                    <td id="prevTotalApple" >{{ $prevTotalApple ? number_format($prevTotalApple, 2) : '' }}</td>
                    <td id="incDecTotal" >{{ $incDecTotal ? number_format($incDecTotal, 2) : '' }}</td>
                    <td id="percentageChangeTotal"  style="{{$style}}">{{$roundedVal}}</td>

                </tr>
            </tbody>
        </table>
    </div>
</div>