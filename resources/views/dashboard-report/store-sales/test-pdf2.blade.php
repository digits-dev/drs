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
            margin-top: 60px;
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
    @if ($hasManyValues)
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
                    <td>Store</td>
                    <td>{{$data['Store']}}</td>
                </tr>

                <tr>
                    <td>Channel</td>
                    <td>{{$data['Channel']}}</td>
                </tr>

                <tr>
                    <td>Brand</td>
                    <td>{{strtoupper($data['Brand'])}}</td>
                </tr>

                <tr>
                    <td>Category</td>
                    <td>{{strtoupper($data['Category'])}}</td>
                </tr>

                <tr>
                    <td>Store Concept</td>
                    <td>{{$data['Store Concept']}}</td>
                </tr>

                <tr>
                    <td>Mall</td>
                    <td>{{strtoupper($data['Mall'])}}</td>
                </tr>

                <tr>
                    <td>Square Meters</td>
                    <td>{{$data['Square Meters']}}</td>
                </tr>

                <tr>
                    <td>Group</td>
                    <td>{{$data['Group']}}</td>
                </tr>
            </tbody>
        </table>

    </div>
    @endif


    @foreach ($chartImages as $chartImage)
        <div class="page-break"></div>

        <div class="chart-container">

            @if ($hasManyValues)
                <div class="push-img-center">&nbsp;</div>
            @endif

            {{-- <img src="{{ $chartImage }}" alt="Chart" style="{{ !$hasManyValues ? 'margin-bottom: 200px;' : '' }}"> --}}
            <img src="{{ $chartImage }}" alt="Chart" >


            @if (!$hasManyValues)
                <div class="table-container">
                    <table id="myTable">
                        <colgroup>
                            <col>
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Channel</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Store Concept</th>
                                <th>Mall</th>
                                <th>Square Meters</th>
                                <th>Group</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$data['Store']}}</td>
                                <td>{{$data['Channel']}}</td>
                                <td>{{strtoupper($data['Brand'])}}</td>
                                <td>{{strtoupper($data['Category'])}}</td>
                                <td>{{$data['Store Concept']}}</td>
                                <td>{{strtoupper($data['Mall'])}}</td>
                                <td>{{$data['Square Meters']}}</td>
                                <td>{{$data['Group']}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

        </div>


    @endforeach


</body>
</html>
