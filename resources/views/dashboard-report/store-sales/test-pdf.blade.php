<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>



    <style>
        * {
            font-size: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        /* .charts {
            display: flex;
            justify-content: space-around;
        } */
        img {
            width: 50%;
            height: auto;
        }

        .center{
            display: grid;
            place-content: center;
        }
        .dashboard{
            margin-top: 100px;
        }
    </style>

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard">
        @foreach ($channel_codes as $channel => $channelData)
            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            <x-sales-report-pdf 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"
                :lastThreeDaysDates="$lastThreeDaysDates"
            />
        @endforeach
    </div>

    <div class="page-break"></div>
    
        {{-- <img src="{{$test1}}" alt="" style="display:block; margin-top:100px; width:1000px; height:700px;"> --}}
    

    {{-- <div class="page-break"></div>

    
    <img src="{{$test2}}" alt="" style="display:block; width:800px; height:500px;">
    

    <div class="page-break"></div>

    
    <img src="{{$test3}}" alt="" style="display:block; width:800px; height:500px;">

    


    <div class="page-break"></div>

    
    <img src="{{$test4}}" alt="" style="display:block; width:800px; height:500px;"> --}}

    
        
    

    {{-- <div class="charts">
        <img src="{{$test1}}" alt="" style="width:1000px; height:700px;">
    <div class="page-break"></div>
    <img src="{{$test2}}" alt="" style="width:1000px; height:700px;">


        <img id="canvasImage1" class="canvas-image" />
        <img id="canvasImage2" class="canvas-image" />
    </div> --}}


    {{-- <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        const ctx2 = document.getElementById('myChart2').getContext('2d');
        const chart1data = generateChartData(2024);
        const chart2data = generateChartData(2023);
        
        console.log(`https://quickchart.io/chart?c=${chart2data}`);
        new Chart(ctx, chart1data);
        new Chart(ctx2, chart2data);

        function generateChartData(year) {
            let datasets = [];
            const lastThreeDays = @json($lastThreeDaysDates);
            const keyDates = Object.keys(lastThreeDays);
            const weeks = ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
            const labels = weeks;

            const channelCodes = @json($channel_codes);
            const newData = Object.entries(channelCodes).map(channel => {
                const channelCode = channel[0];
                const storage = [];
                const weeks = channel[1][year].weeks;

                if (channelCode && channelCode !== "TOTAL") {
                    ['WK01', 'WK02', 'WK03', 'WK04'].forEach(week => {
                        const netSales = weeks[week]?.sum_of_net_sales ?? 0;
                        storage.push(netSales);
                    });

                    return {
                        label: channel[0],
                        data: storage,
                        borderWidth: 1
                    }; 
                }
                return null;
            }).filter(data => data !== null);

            return {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: newData
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    animations:{
                        onComplete: function() {
                            URI = chart.toBase64Image();
                        }
                    }
                }
            };
        }

        // Convert canvas to Base64 and set it to image
        function exportCanvasToImage() {
            const canvas1 = document.getElementById('myChart');
            const canvas2 = document.getElementById('myChart2');

            if (canvas1 && canvas2) {
                const img1 = canvas1.toDataURL('image/png');
                const img2 = canvas2.toDataURL('image/png');

                document.getElementById('canvasImage1').src = img1;
                document.getElementById('canvasImage2').src = img2;
            } else {
                console.error('Canvas elements not found');
            }
        }

        window.onload = function() {
            // Call export function after charts are rendered
            exportCanvasToImage();
        };
    </script> --}}
</body>
</html>
