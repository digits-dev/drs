<!DOCTYPE html>
<html>
<head>
    <title>Document</title>
    <style>
        .chart-container {
            height: 100%; 
            display: block;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            position: relative;
        }

        img {
            display: block;
            margin: 20px auto;
            margin-bottom: 200px;
        }

        table {
            position: absolute; 
            bottom: 0; 
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #e1e1e1;
            font-size: 12px;
        }

        td {
            text-overflow: ellipsis; 
            overflow: hidden;    
        }

        th {
            background-color: #004b87;
            color: white;
            font-weight: bold;
        }

        #conditionalElement {
            display: block; /* Hidden by default */
            margin-top: 20px; /* Add some spacing */
            text-align: center;
            font-weight: bold;
            color: red; /* Optional styling */
        }
    </style>
</head>
<body>
    @foreach ($chartImages as $chartImage)
        <div class="chart-container">
            <img src="{{ $chartImage }}" alt="Chart">

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
            </div>
        @endif
    @endforeach


</body>
</html>
