<!DOCTYPE html>
<html>
<head>
    <title>Document</title>
    <style>


        *{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        
        .page-break {
            page-break-after: always;
        }

        .chart-container {
            height: 100%; 
            display: block;
            /* position: relative; */
        }

        .push-img-center{
            margin-bottom: 110px;
        }
        .chart-container img {
            display: block;
            margin: 20px auto;
            object-fit: contain;
        }
        /* .table-container{
            height: 200px;
        } */

        .chart-container table {
            /* position: absolute;  */
            /* bottom: 0;  */
            height: 100%;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 40px;
        }

        .chart-container th, .chart-container td {
            padding: 10px;
            text-align: center;
            border: 1px solid #e1e1e1;
            font-size: 12px;
        }

        .chart-container td {
            text-overflow: ellipsis; 
            overflow: hidden;    
        }

        .chart-container th {
            background-color: #004b87;
            color: white;
            font-weight: bold;
        }

        .firstTable{
            height: 100%;
            padding: 10px;
        }

        .firstTable table{
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .firstTable th, .firstTable td {
            padding: 10px;
            text-align: left;
            border: 1px solid #e1e1e1;
            font-size: 12px;
        }

        .firstTable td:nth-child(2) {
            text-transform: uppercase;
        }

        .firstTable td {
            text-overflow: ellipsis; 
            overflow: hidden;    
        }

        .firstTable th {
            background-color: #004b87;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- @php
        dump($chartsData);
        dump($formData);
        dump($chartsDataCacheKey);
        dump($chartsDataTable);
    @endphp --}}
    
    <div class="firstTable">
        <h4 style="margin-bottom:20px; font-weight:bold;" >The Selected Parameters for Chart Creation: </h4>

        <table >
            <colgroup>
                <col style="width: 150px;">
                <col>
            </colgroup>
 
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>Year From</td>
                    <td>{{$formData['Year From']}}</td>
                </tr>

                <tr>
                    <td>Year To</td>
                    <td>{{$formData['Year To']}}</td>
                </tr>

                <tr>
                    <td>Month From</td>
                    <td>{{$formData['Month From']}}</td>
                </tr>

                <tr>
                    <td>Month To</td>
                    <td>{{$formData['Month To']}}</td>
                </tr>
                
                <tr>
                    <td>Store</td>
                    <td>{{$formData['Store']}}</td>
                </tr>

                <tr>
                    <td>Channel</td>
                    <td>{{$formData['Channel']}}</td>
                </tr>

                <tr>
                    <td>Brand</td>
                    <td>{{strtoupper($formData['Brand'])}}</td>
                </tr>

                <tr>
                    <td>Category</td>
                    <td>{{strtoupper($formData['Category'])}}</td>
                </tr>

                <tr>
                    <td>Store Concept</td>
                    <td>{{$formData['Store Concept']}}</td>
                </tr>

                <tr>
                    <td>Mall</td>
                    <td>{{strtoupper($formData['Mall'])}}</td>
                </tr>

                <tr>
                    <td>Square Meters</td>
                    <td>{{$formData['Square Meters']}}</td>
                </tr>

                <tr>
                    <td>Group</td>
                    <td>{{$formData['Group']}}</td>
                </tr>
            </tbody>
        </table>

    </div>

    @foreach ($chartsData as $chart)
        <div class="page-break"></div>

        <div class="chart-container">

            @if ($hasManyValues)
                <div class="push-img-center">&nbsp;</div>
            @endif

            <img src="{{ $chart['img'] }}" alt="Chart" >

            @if ($chart['type'] == 'pie')
                <div class="table-container">
                    <table id="myTable">
                        <colgroup>
                            <col>
                        </colgroup>
                        <thead>
                            <tr>

                                @foreach ($chartsDataTable['pieData'] as $channel => $data)
                                    @if ($data[$chart['year']])
                                        <th>{{$channel}}</th>
                                    @endif
                                @endforeach
                            
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($chartsDataTable['pieData'] as $channel => $data)
                                    @if ($data[$chart['year']])
                                        @php
                                            $val = $data[$chart['year']] ? number_format($data[$chart['year']], 0) : ' ';
                                        @endphp

                                        <td>{{$val}}</td>
                                    @endif
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($chart['type'] == 'line' || $chart['type'] == 'bar')
                <div class="table-container">
                    <table id="myTable">
                        <colgroup>
                            <col>
                        </colgroup>
                        <thead>
                            @php
                                $months = [
                                    'M1' => 'January', 
                                    'M2' => 'February', 
                                    'M3' => 'March', 
                                    'M4' => 'April', 
                                    'M5' => 'May', 
                                    'M6' => 'June', 
                                    'M7' => 'July', 
                                    'M8' => 'August', 
                                    'M9' => 'September', 
                                    'M10' => 'October', 
                                    'M11' => 'November', 
                                    'M12' => 'December', 
                                ];

                            @endphp
                            <tr>
                                <th>Year</th>

                                @foreach ($chartsDataTable['lineBarData'] as $month => $data)
                                    @if ($data)
                                        <th>{{$months[$month]}}</th>
                                    @endif
                                @endforeach
                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($years as $year)
                                <tr>
                                    <td>{{$year}}</td>
                                    @foreach ($chartsDataTable['lineBarData'] as $month => $data)
                                        @if ($data)
                                            @php
                                                $val = $data[$year] ? number_format($data[$year], 0) : ' ';
                                            @endphp

                                            <td>{{$val}}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

    @endforeach


</body>
</html>
