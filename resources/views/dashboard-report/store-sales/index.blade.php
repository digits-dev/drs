@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

    <style>
        .dashboard {
            padding-top: 10px;
        }

        .export {
            padding: 10px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            border-bottom: 1px solid #ddd;
        }

        .weekly-section {
            background: white;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .charts {
            display: flex;
            gap: 10px;
            padding: 10px;
            width: 100%;
            height: 500px;
        }

        canvas {
            flex: 1 !important;
            height: 100% !important;
            max-width: 50% !important;
            box-sizing: border-box !important;
            border: 1px solid #ccc !important;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
@endpush

@section('content')

<div class="weekly-section">

    <div class="export">
        <a href="{{ route('weekly_export_excel') }}" class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-download" aria-hidden="true"></i> Export to Excel
        </a>
        <a id="exportPDF" href="{{ route('weekly_export_pdf') }}?perChannel=false&category=total" class="btn btn-primary btn-sm pull-right">
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


    <h2 class="text-start" style="margin-top: 40px;">Charts</h2>
    <div class="" style="display: flex; gap:10px; justify-content:start; align-items:flex-start; ">
   
        <div class="data-display-toggle form-group" style="display: inline-block; margin-right: 15px;">
            <label class="control-label">Data Display:</label>
            <div class="radio">
                <label>
                    <input type="radio" name="dataDisplay" value="total" checked>
                    Show Overall Total
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="dataDisplay" value="perChannel">
                    Show Per Channel Breakdown
                </label>
            </div>
        </div>
        
        <div class="form-group" style="display: inline-block; margin-right: 15px;">
            <label   class="control-label" for="categorySelector" style="margin-bottom: 15px;">Select Category:</label>
            <select id="categorySelector" class="form-control">
                <option value="total">Total</option>
                <option value="weekly">Weekly</option>
                <option value="last_three_days">Last Three Days</option>
            </select>
        </div>
    
        <button id="updateChartButton" class="btn btn-primary" style="align-self: center; ">
            <i class="fa fa-refresh" aria-hidden="true"></i> Update Charts
        </button>
    </div>
    

    <div class="chart-channel">
        <h2>Bar Graph</h2>
        <div class="charts">
            <canvas id="myChart3" width="500" height="700"  ></canvas>
            <canvas id="myChart4" width="500" height="700"  ></canvas>
        </div>
    </div>
    
    <div class="chart-channel">
        <h2>Line Graph</h2>
        <div class="charts">
            <canvas id="myChart" width="500" height="700"  ></canvas>
            <canvas id="myChart2" width="500" height="700"  ></canvas>
        </div>
    </div>

    <div class="chart-channel">
        <h2>Pie Chart</h2>
        <div class="charts">
            <canvas id="myChart5" width="500" height="700"  ></canvas>
            <canvas id="myChart6" width="500" height="700"  ></canvas>
        </div>
    </div>

    <div class="chart-total">
        <div class="charts">
            <canvas id="myChart8" width="500" height="700"  ></canvas>
            <canvas id="myChart9" width="500" height="700"  ></canvas>

        </div>
    </div>
    <div class="chart-total">
        <div class="charts">

            <canvas id="myChart7" width="500" height="700"  ></canvas>

        </div>
    </div>


    <div class="chart-channel">
        <h2>Pie Chart1</h2>
        <div class="charts">
            <canvas id="myChart10" width="500" height="700"  ></canvas>
            <canvas id="myChart11" width="500" height="700"  ></canvas>
        </div>
    </div>
    <div class="chart-channel">
        <h2>Pie Chart2</h2>
        <div class="charts">
            <canvas id="myChart12" width="500" height="700"  ></canvas>
            <canvas id="myChart13" width="500" height="700"  ></canvas>
        </div>
    </div>
    <div class="chart-channel">
        <h2>Pie Chart3</h2>
        <div class="charts">
            <canvas id="myChart14" width="500" height="700"  ></canvas>
            <canvas id="myChart15" width="500" height="700"  ></canvas>
        </div>
    </div>
{{-- 
    <div class="chart-total">
        <h2>Total1</h2>
        <div style="width:50%; height:500px; margin:0 auto;">
            <canvas id="myChart7" class="canvas3"></canvas>
        </div>
    </div>
    <div class="chart-total">
        <h2>Total2</h2>

        <div style="width:50%; height:500px; margin:0 auto;">
            <canvas id="myChart8" class="canvas3"></canvas>
        </div>
    </div>
    <div class="chart-total">
        <h2>Total3</h2>

        <div style="width:50%; height:500px; margin:0 auto;">
            <canvas id="myChart9" class="canvas3"></canvas>
        </div>
    </div> --}}

    {{-- <div>
        <h2>All Data</h2>
        <div style="width:100%;">
 
            <canvas id="all" class="canvas2" ></canvas>
            <canvas id="all2" class="canvas2" ></canvas>
            <canvas id="all3" class="canvas2" ></canvas>
        </div>
    </div> --}}

</div>

@endsection

@push('bottom')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
   $(function() {
   

    const prevYear = @json($yearData['previousYear']);
    const currYear = @json($yearData['currentYear']);
    const channelCodes = @json($channel_codes);
    const lastThreeDays = @json($lastThreeDaysDates);
    const chartInstances = {};
    const chartInstancesPie = {};

    const chartConfigs = [
        { year: prevYear, type: 'line', category: 'total', canvasId: 'myChart' },
        { year: currYear, type: 'line', category: 'total', canvasId: 'myChart2' },
        { year: prevYear, type: 'bar', category: 'total', canvasId: 'myChart3' },
        { year: currYear, type: 'bar', category: 'total', canvasId: 'myChart4' },
        { year: prevYear, type: 'pie', category: 'total', canvasId: 'myChart5' },
        { year: currYear, type: 'pie', category: 'total', canvasId: 'myChart6' },
    ];
    const chartConfigs3 = [
        { prevYear: prevYear, currYear: currYear, type: 'line', category: 'total', canvasId: 'myChart7' },
        { prevYear: prevYear, currYear: currYear, type: 'bar', category: 'total', canvasId: 'myChart8' },
        { prevYear: prevYear, currYear: currYear, type: 'pie', category: 'total', canvasId: 'myChart9' },
    ];
    const channelsKey = [
        {key: 'ECOMM', canvasId: 'myChart10'},
        {key: 'RETAIL', canvasId: 'myChart11'},
        {key: 'SC', canvasId: 'myChart12'},
        {key: 'OUT', canvasId: 'myChart13'},
        {key: 'CON', canvasId: 'myChart14'},
        {key: 'FRA', canvasId: 'myChart15'},
    ];

    

    renderPie();

    function renderPie(selectedCategory = 'total') {

        channelsKey.forEach(channelPie => {
            const test = generateDataForPiePerChannel('pie', true, selectedCategory, prevYear, currYear, channelPie.key);
            console.log('test', test);

            // const category = selectedCategory ? selectedCategory : config.category;

            // const chartData = generateDataForOverallTotal(config.type, false, category, config.prevYear, config.currYear);

            // console.log(chartData);

            const ctx = document.getElementById(channelPie.canvasId);

            // Destroy existing chart if it exists
            if (chartInstancesPie[channelPie.canvasId]) {
                chartInstancesPie[channelPie.canvasId].destroy();
            }

            // Create new chart instance
            chartInstancesPie[channelPie.canvasId] = new Chart(ctx, test);
        })
    }

    // document.querySelectorAll('input[name="dataDisplay"]').forEach((radio) => {
    //     radio.addEventListener('change', renderCharts);
    // });

    // const test = generateDataForPiePerChannel('pie',true, 'weekly', prevYear, currYear);


    $('#updateChartButton').on('click', function() {
        const selectedCategory = $('#categorySelector').val();
        const isPerChannel = $('input[name="dataDisplay"]:checked').val() === 'perChannel'; 
        const pdfLink = `{{ route('weekly_export_pdf') }}?perChannel=${isPerChannel}&category=${selectedCategory}`;

        updateCharts(selectedCategory); 
        renderPie(selectedCategory);

        $('#exportPDF').attr('href', pdfLink); 
    });

    // document.getElementById('updateChartButton').addEventListener('click', function() {
    //     const selectedCategory = document.getElementById('categorySelector').value;
    //     const isPerChannel = document.querySelector('input[name="dataDisplay"]:checked').value === 'perChannel'; 
    //     const pdfLink = `{{ route('weekly_export_pdf') }}?perChannel=${isPerChannel}&category=${selectedCategory}`;

    //     updateCharts(selectedCategory);

    //     document.getElementById('exportPDF').setAttribute('href', pdfLink);
    // });

    // Initial rendering of charts
    renderCharts();

  
    function renderCharts(selectedCategory) {
        const isPerChannel = document.querySelector('input[name="dataDisplay"]:checked').value === 'perChannel'; 



        if(isPerChannel){
            $('.canvas1').show();
            $('.canvas3').hide();
            $('.chart-channel').show(); 
            $('.chart-total').hide(); 

            // Calculate maximum values
            const maxValue = calculateMaxValues(selectedCategory)[selectedCategory];
            const buffer = maxValue * 0.15; 
            // const maxValWithBuffer = Math.floor(maxValue + buffer);
            // const maxValWithBuffer = Math.round((maxValue + buffer) / 1000) * 1000;
            const maxValWithBuffer = Math.round((maxValue + buffer) / 1000000) * 1000000;
            

            chartConfigs.forEach(config => {

                const category = selectedCategory ? selectedCategory : config.category;

                const chartData = generateDataPerChannel(config.type, true, category, config.year);

                const ctx = document.getElementById(config.canvasId);
                
                // Destroy existing chart if it exists
                if (chartInstances[config.canvasId]) {
                    chartInstances[config.canvasId].destroy();
                }

                // Create new chart instance
                chartInstances[config.canvasId] = new Chart(ctx, {
                    ...chartData,
                    options: {
                        ...chartData.options,
                        scales:{
                            ...chartData.options.scales,
                            y: {
                                ...chartData.options.scales?.y,
                                max: maxValWithBuffer
                            }
                        }
                    }
                });
            });
        } else {
            $('.canvas1').hide();
            $('.canvas3').show();
            $('.chart-channel').hide();
            $('.chart-total').show(); 

            chartConfigs3.forEach(config => {

                const category = selectedCategory ? selectedCategory : config.category;

                const chartData = generateDataForOverallTotal(config.type, false, category, config.prevYear, config.currYear);

                console.log(chartData);

                const ctx = document.getElementById(config.canvasId);
                
                // Destroy existing chart if it exists
                if (chartInstances[config.canvasId]) {
                    chartInstances[config.canvasId].destroy();
                }

                // Create new chart instance
                chartInstances[config.canvasId] = new Chart(ctx, chartData);
            });
        }

    }

    function updateCharts(selectedCategory) {
        renderCharts(selectedCategory);
    }

    function calculateMaxValues(categoryVal) {
        const maxValues = {};

        const chartConfigs2 = [
            { year: prevYear, type: 'line', category: categoryVal },
            { year: currYear, type: 'line', category: categoryVal },
        ];


        chartConfigs2.forEach(config => {

            const chartData = generateDataPerChannel(config.type, true, config.category, config.year);

            const dataEntries = chartData.data.datasets;

            dataEntries.forEach(dataset => {
                const maxVal = dataset.maxVal || 0;

                if (!maxValues[config.category] || maxVal > maxValues[config.category]) {
                    maxValues[config.category] = maxVal
                }
            });
        });

        return maxValues;
    }

    function generateDataForOverallTotal(chartType, isPerChannel = false, dataCategory = 'total', prevYear, currYear){
        let labels;
        let pieLabels = [];
        const datasets = [];
        const channelCodes = @json($channel_codes);
        const lastThreeDays = @json($lastThreeDaysDates);
        const keyDates = Object.keys(lastThreeDays);

        //LABELS
        switch(dataCategory) {
            case 'total':
                labels = ['TOTAL'];
                break;
            case 'weekly':
                labels =  ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
                break;
            case 'last_three_days':
                labels = keyDates;
                break;
            default:
                labels = [];
        }

        // console.log(channelCodes);

        const prevData = Object.entries(channelCodes).map(channel => {
            const dataStorage = [];
            const channelCode = channel[0]; //ex. ECOMM, RTL
            const weeks = channel[1][prevYear]?.weeks;
            const lastThreeDays = channel[1][prevYear]?.last_three_days;


            if(!isPerChannel && channelCode === 'TOTAL'){
                let keys  = [];

                switch(dataCategory) {
                    case 'total':
                        keys = ['TOTAL'];
                        break;
                    case 'weekly':
                        keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        break;
                    default:
                        keys = [];
                }

                if(dataCategory === 'last_three_days'){
                    lastThreeDays.forEach(day => {
                        const netSales = day?.sum_of_net_sales ?? 0;
                        dataStorage.push(netSales);
                    });
                } else {
                    keys.forEach(key => {
                    const netSales = weeks[key]?.sum_of_net_sales ?? 0;
                    dataStorage.push(netSales);
                    });
                }

                return {
                    label: `${prevYear} ${channelCode}`,
                    data: dataStorage,
                    borderWidth: 2,
        
                }; 
            } 

            return null;

        }).filter(data => data !== null);

        const currData = Object.entries(channelCodes).map(channel => {
            const dataStorage = [];
            const channelCode = channel[0]; //ex. ECOMM, RTL
            const weeks = channel[1][currYear]?.weeks;
            const lastThreeDays = channel[1][currYear]?.last_three_days;


            if(!isPerChannel && channelCode === 'TOTAL'){
                let keys  = [];

                switch(dataCategory) {
                    case 'total':
                        keys = ['TOTAL'];
                        break;
                    case 'weekly':
                        keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        break;
                    default:
                        keys = [];
                }

                if(dataCategory === 'last_three_days'){
                    lastThreeDays.forEach(day => {
                        const netSales = day?.sum_of_net_sales ?? 0;
                        dataStorage.push(netSales);
                    });
                } else {
                    keys.forEach(key => {
                    const netSales = weeks[key]?.sum_of_net_sales ?? 0;
                    dataStorage.push(netSales);
                    });
                }

                return {
                    label: `${currYear} ${channelCode}`,
                    data: dataStorage,
                    borderWidth: 2,
        
                }; 
            } 

            return null;

        }).filter(data => data !== null);


        const pieData = Object.entries(channelCodes).map(channel => {
            const dataStorage = [];
            const channelCode = channel[0]; //ex. ECOMM, RTL
            const weeks = channel[1][prevYear]?.weeks;
            const lastThreeDays = channel[1][prevYear]?.last_three_days;

            const weeks2 = channel[1][currYear]?.weeks;
            const lastThreeDays2 = channel[1][currYear]?.last_three_days;


            if(!isPerChannel && channelCode === 'TOTAL'){
                let keys  = [];

                switch(dataCategory) {
                    case 'total':
                        keys = ['TOTAL'];
                        pieLabels = [prevYear + " TOTAL", currYear + " TOTAL"];
                        break;
                    case 'weekly':
                        keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        pieLabels = [
                            prevYear + " WK01", currYear + " WK01",
                            prevYear + " WK02", currYear + " WK02",
                            prevYear + " WK03", currYear + " WK03",
                            prevYear + " WK04", currYear + " WK04",
                        ];
                        break;
                    default:
                        keys = [];
                }

                if(dataCategory === 'last_three_days'){
                    lastThreeDays.forEach(day => {
                        const netSales = day?.sum_of_net_sales ?? 0;
                        dataStorage.push(netSales);
                    });

                    lastThreeDays2.forEach(day => {
                        const netSales = day?.sum_of_net_sales ?? 0;
                        dataStorage.push(netSales);
                    });

                    keyDates.forEach(date => {
                        pieLabels.push(prevYear + ' ' + date, currYear + ' ' + date) 
                    });
           
                } else {
                    keys.forEach(key => {
                    const netSales = weeks[key]?.sum_of_net_sales ?? 0;
                    dataStorage.push(netSales);
                    });

                    keys.forEach(key => {
                    const netSales = weeks2[key]?.sum_of_net_sales ?? 0;
                    dataStorage.push(netSales);
                    });
                }



                return {
                    label: `${channelCode}`,
                    data: dataStorage,
                    borderWidth: 2,
        
                }; 
            } 

            return null;

        }).filter(data => data !== null);

        // console.log(prevData);
        // console.log(currData);

        // const pieLabels = [`${prevYear}`, `${currYear}`];

        return {
            type: chartType,
            data: {
                labels: chartType == 'pie' ? pieLabels : labels,
                datasets: chartType == 'pie' ? pieData : [...prevData, ...currData]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false, 
                    layout:{
                    padding: 20
                },
                plugins: {
                    title: {
                        display: true,
                        text: `Sales Data`,
                        font:{
                            size: 16,
                        },
                        padding: {
                            top:20,
                            bottom: 20,
                        },
                    },
                
                    legend: {
                        position: 'right',
                        labels:{
                            boxWidth: 10
                        }
                    }
                },
                locale: 'en-PH',
                scales: {
                    y: chartType === 'pie' ? {
                    display: false // Hide y-axis for pie charts
                    } : {
                        ticks: {
                            callback: (value) => new Intl.NumberFormat('en-PH', {
                                style: 'currency',
                                currency: 'PHP',
                                maximumSignificantDigits: 3
                            }).format(value),
                        },
                        beginAtZero: true,
                    }

                }
            }
        }
    }
    function generateDataPerChannel(chartType, isPerChannel = true, dataCategory = 'total', year){

        let labels;
        const datasets = [];
        const channelCodes = @json($channel_codes);
        const lastThreeDays = @json($lastThreeDaysDates);
        const keyDates = Object.keys(lastThreeDays);


        //LABELS
        switch(dataCategory) {
            case 'total':
                labels = ['TOTAL'];
                break;
            case 'weekly':
                labels =  ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
                break;
            case 'last_three_days':
                labels = keyDates;
                break;
            default:
                labels = [];
        }

        // console.log(channelCodes);

        const newData = Object.entries(channelCodes).map(channel => {
            const dataStorage = [];
            let channelCode = channel[0]; //ex. ECOMM, RTL
            const weeks = channel[1][year]?.weeks;
            const lastThreeDays = channel[1][year]?.last_three_days;

            // console.group(channelCode)
            //     console.log(weeks);
            //     console.log(lastThreeDays);
            //     console.log(channel);
            // console.groupEnd();

            switch (channelCode) {
                case 'TOTAL-RTL':
                    channelCode = 'RETAIL';
                    break;
                case 'DLR/CRP':
                    channelCode = 'OUT';
                    break;
                case 'FRA-DR':
                    channelCode = 'FRA';
                    break;
                default:
                    channelCode;
            }

            if (isPerChannel && channelCode && channelCode !== "TOTAL") {
                let keys  = [];

                switch(dataCategory) {
                    case 'total':
                        keys = ['TOTAL'];
                        break;
                    case 'weekly':
                        keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        break;
                    default:
                        keys = [];
                }

                if(dataCategory === 'last_three_days'){
                    lastThreeDays && lastThreeDays.forEach(day => {
                        // const netSales = day?.sum_of_net_sales ?? 0;
                        // dataStorage.push(netSales);
                        const netSales = day.sum_of_net_sales ?? 0;
                        dataStorage.push(netSales);
                    });
                } else {
                    keys.forEach(key => {
                        const netSales = weeks && weeks[key] ? weeks[key]?.sum_of_net_sales : 0;
                        dataStorage.push(netSales);
                    });
                }

          

                const maxVal = dataStorage.length > 0 ? Math.max(...dataStorage) : 0;
                // console.log(maxVal);

                return {
                    label: `${channelCode}`,
                    data: dataStorage,
                    borderWidth: 2,
                    maxVal: maxVal

                }; 
            }


            return null;

        }).filter(data => data !== null);

        // console.log(newData);


        return {
            type: chartType,
            data: {
                labels: labels,
                datasets: newData
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                    layout:{
                    padding: 20
                },
                plugins: {
                    title: {
                        display: true,
                        text: `${year} Sales Data`,
                        font:{
                            size: 16,
                        },
                        padding: {
                            top:20,
                            bottom: 20,
                        },
                    },
                
                    legend: {
                        position: 'right',
                        labels:{
                            boxWidth: 10
                        }
                    }
                },
                locale: 'en-PH',
                scales: {
                    y: chartType === 'pie' ? {
                    display: false // Hide y-axis for pie charts
                    } : {
                        // ticks: {
                        //     callback: (value) => new Intl.NumberFormat('en-PH', {
                        //         style: 'currency',
                        //         currency: 'PHP',
                        //         maximumSignificantDigits: 3
                        //     }).format(value),
                        // },
                        beginAtZero: true,
                    }

                }
            }
        }
    }

    function formatDate(date_of_the_day){
        const date = new Date(date_of_the_day);
        const dayNum = date.getDate().toString().padStart(2, '0'); // Get day and pad if necessary
        const month = date.toLocaleString('en-PH', { month: 'short' }); // Get short month name

        const formattedDate = `${dayNum}-${month}`; // Combine to 'DD-MMM'

        return formattedDate;
    }

    function generateDataForPiePerChannel(chartType = 'pie', isPerChannel = true, dataCategory = 'weekly', prevYear, currYear, key){
        let labels;
        let pieLabels = [];
        const datasets = [];
        const channelCodes = @json($channel_codes);
        const lastThreeDays = @json($lastThreeDaysDates);
        const keyDates = Object.keys(lastThreeDays);

    


        const pieData = Object.entries(channelCodes).map(channel => {
            const dataStorage = [];
            let channelCode = channel[0]; //ex. ECOMM, RTL

            const weeks = channel[1][prevYear]?.weeks;
            const lastThreeDays = channel[1][prevYear]?.last_three_days;

            const weeks2 = channel[1][currYear]?.weeks;
            const lastThreeDays2 = channel[1][currYear]?.last_three_days;

            switch (channelCode) {
                case 'TOTAL-RTL':
                    channelCode = 'RETAIL';
                    break;
                case 'DLR/CRP':
                    channelCode = 'OUT';
                    break;
                case 'FRA-DR':
                    channelCode = 'FRA';
                    break;
                default:
                    channelCode;
            }

            if(isPerChannel && channelCode == key){
                let keys  = [];

                switch(dataCategory) {
                    case 'total':
                        keys = ['TOTAL'];
                        break;
                    case 'weekly':
                        keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        break;
                    default:
                        keys = [];
                }

                if(dataCategory == 'last_three_days'){
                    lastThreeDays.forEach(day => {
                        const netSales = day?.sum_of_net_sales ?? 0;

                        if(netSales != 0) {
                            dataStorage.push(netSales);

                            const formattedDate = formatDate(day?.date_of_the_day); 

                            pieLabels.push(prevYear + ' ' + formattedDate);
                        }
                    });

                    lastThreeDays2.forEach(day => {
                        const netSales = day?.sum_of_net_sales ?? 0;

                        if(netSales != 0) {
                            dataStorage.push(netSales);

                            const formattedDate = formatDate(day?.date_of_the_day); 

                            pieLabels.push(currYear + ' ' + formattedDate);
                        }
                    });
           
                } else {
                    keys.forEach(key => {
                        const netSales = weeks[key]?.sum_of_net_sales ?? 0;

                        if(netSales != 0) {
                            dataStorage.push(netSales);
                            pieLabels.push(prevYear + ' ' + key);
                        }
                    });

                    keys.forEach(key => {
                        const netSales = weeks2[key]?.sum_of_net_sales ?? 0;

                        if(netSales != 0) {
                            dataStorage.push(netSales);
                            pieLabels.push(currYear + ' ' + key);
                        }
                    });
                }

                return {
                    label: `${channelCode}`,
                    data: dataStorage,
                    borderWidth: 2,
                }; 
            } 

            return null;

        }).filter(data => data !== null);

        console.log(pieData);


        return {
            type: chartType,
            data: {
                labels: chartType == 'pie' ? pieLabels : labels,
                datasets: chartType == 'pie' ? pieData : [...prevData, ...currData]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false, 
                    layout:{
                    padding: 20
                },
                plugins: {
                    title: {
                        display: true,
                        text: `${key} Sales Data`,
                        font:{
                            size: 16,
                        },
                        padding: {
                            top:20,
                            bottom: 20,
                        },
                    },
                
                    legend: {
                        position: 'right',
                        labels:{
                            boxWidth: 10
                        }
                    }
                },
                locale: 'en-PH',
                scales: {
                    y: chartType === 'pie' ? {
                    display: false // Hide y-axis for pie charts
                    } : {
                        ticks: {
                            callback: (value) => new Intl.NumberFormat('en-PH', {
                                style: 'currency',
                                currency: 'PHP',
                                maximumSignificantDigits: 3
                            }).format(value),
                        },
                        beginAtZero: true,
                    }

                }
            }
        }
    }

    

    // All Data Charts
    // const all = document.getElementById('all');
    // const alldata = generateDataForOverallTotal('line', false, 'last_three_days', '2021', '2022');
    // new Chart(all, alldata);


    // const all2 = document.getElementById('all2');
    // const alldata2 = generateDataPerChannel('bar', true, 'last_three_days', '2021');
    // new Chart(all2, alldata2);

    // const all3 = document.getElementById('all3');
    // const alldata3 = generateDataPerChannel('bar', true, 'last_three_days', '2022');
    // new Chart(all3, alldata3);

});
</script>
@endpush
