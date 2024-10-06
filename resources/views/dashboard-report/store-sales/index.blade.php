@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

    <style>
        /* .dashboard{
            background: white;
            padding: 5px 15px;
        } */

        .export{
            padding:10px;
            display: flex;
            gap:10px;
            justify-content: flex-end;
            border-bottom: 1px solid #ddd;
        }

        .weekly-section{
            background: white;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .charts {
            display: flex;             /* Use flexbox for layout */
            gap: 10px;                /* Space between the canvases */
            padding: 10px;            /* Padding around the container */
            width: 100%;              /* Full width of the parent container */
            height: 700px;
        }

        canvas {
            flex: 1;                  /* Allow canvases to grow equally */
            height: 700px;           /* Set a fixed height for the canvases */
            max-width: 50%;          /* Each canvas should take up 50% of the width */
            box-sizing: border-box;  /* Include padding and border in the element's total width/height */
            border: 1px solid #ccc;  /* Optional: border for visibility */
        }
  
    </style>
@endpush

@section('content')

<div class="weekly-section">

    <div class="export">
        <a href="{{route('weekly_export_excel')}}" id="search-filter" class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-download" aria-hidden="true"></i> Export to Excel
        </a>
        <a href="" id="search-filter" class="btn btn-primary btn-sm pull-right" >
            <i class="fa fa-download" aria-hidden="true"></i> Export to PDF
        </a>
    </div>
    
    <div class="dashboard">
     
        @foreach ($channel_codes as $channel => $channelData)

            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-sales-report 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"
                :lastThreeDaysDates="$lastThreeDaysDates"

            />
          
        @endforeach

    </div>

    <div>
    <h3>Line Graph</h3>

    <div class="charts">
        <canvas id="myChart" style="width: 50%; height:100%;"></canvas>
        <canvas id="myChart2" style="width: 50%; height:100%;"></canvas>
      </div>
    </div>
    <div>
    <h3>Bar Graph</h3>

    <div class="charts">
        <canvas id="myChart3" style="width: 50%; height:100%;"></canvas>
        <canvas id="myChart4" style="width: 50%; height:100%;"></canvas>
      </div>
    </div>

</div>



@endsection

@push('bottom')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');
    const ctx2 = document.getElementById('myChart2');
    const ctx3 = document.getElementById('myChart3');
    const ctx4 = document.getElementById('myChart4');
    const chart1data = generateChartData(2023, 'line');
    const chart2data = generateChartData(2024, 'line');
    const chart3data = generateChartData(2023, 'bar');
    const chart4data = generateChartData(2024, 'bar');
    console.log(chart1data);
    console.log(chart2data);

    console.log(`https://quickchart.io/chart?c=${JSON.stringify(chart2data)}`);
    new Chart(ctx, chart1data);
    new Chart(ctx2, chart2data);

    new Chart(ctx3, chart3data);
    new Chart(ctx4, chart4data);
    function generateChartData(year, chartType){
        console.log(year);
        let datasets = [];

        const lastThreeDays = @json($lastThreeDaysDates);
        const keyDates = Object.keys(lastThreeDays);
        const weeks = ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
        // const labels = weeks.concat(keyDates);
        const labels = weeks;

        const channelCodes = @json($channel_codes);

        const newData = Object.entries(channelCodes).map(channel => {
            
            const channelCode = channel[0];
            const storage = [];
            const weeks = channel[1][year].weeks;
            // const lastThreeDays = channel[1]['2024'].last_three_days;

            if(channelCode && channelCode !== "TOTAL"){
                // ['TOTAL', 'WK01', 'WK02', 'WK03', 'WK04']
                ['WK01', 'WK02', 'WK03', 'WK04'].forEach(week => {

                    const netSales = weeks[week]?.sum_of_net_sales ?? 0;
                
                    storage.push(netSales);

                });

                // lastThreeDays.forEach(day => {
                //     const netSales = day?.sum_of_net_sales ?? 0;

                //     storage.push(netSales);
                // });

                return {
                    label: channel[0],
                    data: storage,
                    borderWidth:2,
        
                }; 
            }


            return null;

        }).filter(data => data !== null);


        return {
        type: chartType,
        data: {
            labels: labels,
            datasets: newData
        },
        options: { 
            plugins:{
                title: {
        display: true,
        text: `${year} Sales Data`,
      }
            }
            
            
           ,
            locale:'en-PH',
            scales: {
                y: {
                    ticks:{
                        callback: (value, index, values) => {
                            return new Intl.NumberFormat('en-PH', {
                                style:'currency',
                                currency: 'PHP',
                                maximumSignificantDigits: 3
                            }).format(value);
                        }
                    },
                    beginAtZero: true
                }
            }
        }
        }
    }

//     let datasets = [];

// const lastThreeDays = @json($lastThreeDaysDates);
// const keyDates = Object.keys(lastThreeDays);
// const weeks = ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
// // const labels = weeks.concat(keyDates);
// const labels = weeks;

// const channelCodes = @json($channel_codes);

// const newData = Object.entries(channelCodes).map(channel => {
    
//     const channelCode = channel[0];
//     const storage = [];
//     const weeks = channel[1]['2024'].weeks;
//     // const lastThreeDays = channel[1]['2024'].last_three_days;

//     if(channelCode && channelCode !== "TOTAL"){
//         // ['TOTAL', 'WK01', 'WK02', 'WK03', 'WK04']
//         ['WK01', 'WK02', 'WK03', 'WK04'].forEach(week => {

//             const netSales = weeks[week]?.sum_of_net_sales ?? 0;
        
//             storage.push(netSales);

//         });

//         // lastThreeDays.forEach(day => {
//         //     const netSales = day?.sum_of_net_sales ?? 0;

//         //     storage.push(netSales);
//         // });

//         return {
//             label: channel[0],
//             data: storage,
//             borderWidth:1
//         }; 
//     }


//     return null;

// }).filter(data => data !== null);

// console.log(newData);


    //testing 

  </script>
@endpush
