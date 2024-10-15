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
            flex-wrap: wrap;
            gap: 15px;
            padding: 10px;
            width: 100%;
            /* height: 500px; */
            opacity: 1;
            transition: opacity 0.5s ease; 
        }

        canvas {
            flex: 1 !important;
            height: 100% !important;
            max-width: 49% !important;
            max-height: 500px !important;
            box-sizing: border-box !important;
            border: 1px solid #ccc !important;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

       
        .fade-out {
            opacity: 0; /* Start fading out */
        }

        .fade-in {
            opacity: 1; /* Fade in */
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
    
    {{-- Charts Container  --}}
    <div class="charts"></div>

    {{-- <div class="chart-channel">
        <h2>Bar Graph</h2>
        <div class="charts">
            <canvas id="myChart3" width="500" height="700"  ></canvas>
            <canvas id="myChart4" width="500" height="700"  ></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4"></script>

<script>
   $(function() {

    const prevYear = @json($yearData['previousYear']);
    const currYear = @json($yearData['currentYear']);
    const channelCodes = @json($channel_codes);
    const lastThreeDays = @json($lastThreeDaysDates);
    const chartInstances = {};
    const chartInstancesPieByChannel = {};

    const chartConfigsByChannel = [
        { type: 'line', category: 'total', year: prevYear },
        { type: 'line', category: 'total', year: currYear },
        { type: 'bar', category: 'total', year: prevYear },
        { type: 'bar', category: 'total', year: currYear },
        { type: 'pie', category: 'total', year: prevYear },
        { type: 'pie', category: 'total', year: currYear },
    ];

    const chartConfigsOverallTotal = [
        { type: 'line', category: 'total', prevYear: prevYear, currYear: currYear },
        { type: 'bar', category: 'total', prevYear: prevYear, currYear: currYear },
        { type: 'pie', category: 'total', prevYear: prevYear, currYear: currYear },
    ];
    
    const chartConfigsPieChartByChannel = [
        { key: 'ECOMM' },
        { key: 'RETAIL' },
        { key: 'SC' },
        { key: 'OUT' },
        { key: 'CON' },
        { key: 'FRA' },
    ];

    
    // Initial rendering of charts
    renderCharts();

    //Updating the charts data
    $('#updateChartButton').on('click', function() {
        const selectedCategory = $('#categorySelector').val();
        const isPerChannel = $('input[name="dataDisplay"]:checked').val() === 'perChannel'; 
        const pdfLink = `{{ route('weekly_export_pdf') }}?perChannel=${isPerChannel}&category=${selectedCategory}`;

        renderCharts(selectedCategory);

        $('#exportPDF').attr('href', pdfLink); 
    });
  
    function renderPieChartsByChannel(selectedCategory = 'total') {

        chartConfigsPieChartByChannel.forEach(channel => {
            const chartData = generatePieChartDataByChannel('pie', true, selectedCategory, prevYear, currYear, channel.key);

            // Assign a unique ID to each config
            if (!channel.canvasId) {
                channel.canvasId = generateUniqueId(); 
            }
       
            if(chartData?.data?.datasets[0]?.data?.length !== 0){

                const canvasId = `${channel.canvasId}`;
                let canvas = document.getElementById(canvasId);

                if (!canvas) {
                    canvas = $('<canvas>', {
                        id: canvasId,
                        width: 500,
                        height: 700
                    });

                    $('.charts').append(canvas);
                }

                // Destroy existing chart if it exists
                if (chartInstancesPieByChannel[canvasId]) {
                    chartInstancesPieByChannel[canvasId].destroy();
                }

                // Create new chart instance
                chartInstancesPieByChannel[canvasId] = new Chart(canvas, chartData);

            } 
        })
    }

    function renderChannelCharts(selectedCategory = 'total'){
        // Calculate maximum values
        const maxValue = calculateMaxValuesInData(selectedCategory)[selectedCategory];
        const buffer = maxValue * 0.15; 
        // const maxValWithBuffer = Math.round((maxValue + buffer) / 1000) * 1000; //to make it look like, ex. xxx,xxx,000
        const maxValWithBuffer = Math.round((maxValue + buffer) / 1000000) * 1000000; //to make it look like, ex. xxx,000,000

        
        chartConfigsByChannel.forEach(config => {

            const category = selectedCategory ? selectedCategory : config.category;
            const chartData = generateChannelData(config.type, true, category, config.year);

            // Assign a unique ID to each config
            if (!config.canvasId) {
                config.canvasId = generateUniqueId(); 
            }

            const canvasId = `${config.canvasId}`;

            let canvas = document.getElementById(canvasId);

            //Create new canvas, if it doesn't have it.
            if (!canvas) {
                canvas = $('<canvas>', {
                    id: canvasId,
                    width: 500,
                    height: 700
                });

                $('.charts').append(canvas);
            }

            // Destroy existing chart if it exists
            if (chartInstances[canvasId]) {
                chartInstances[canvasId].destroy();
            }

            // Create new chart instance with overriding the scales.y.max = value
            chartInstances[canvasId] = new Chart(canvas, {
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

    }

    function renderOverallTotalChart(selectedCategory = 'total'){
        
        chartConfigsOverallTotal.forEach(config => {
            const category = selectedCategory ? selectedCategory : config.category;
            const chartData = generateOverallTotalData(config.type, false, category, config.prevYear, config.currYear);

            // Assign a unique ID to each config
            if (!config.canvasId) {
                config.canvasId = generateUniqueId(); 
            }

            const canvasId = `${config.canvasId}`;

            let canvas = document.getElementById(canvasId);

            if (!canvas) {
                canvas = $('<canvas>', {
                    id: canvasId,
                    width: 500,
                    height: 700
                });

                $('.charts').append(canvas);
            }

            // Destroy existing chart if it exists
            if (chartInstances[canvasId]) {
                chartInstances[canvasId].destroy();
            }

            // Create new chart instance
            chartInstances[canvasId] = new Chart(canvas, chartData);
        });
    }
    
    function renderCharts(selectedCategory) {
        const isPerChannel = document.querySelector('input[name="dataDisplay"]:checked').value === 'perChannel'; 

        $('.charts').addClass('fade-out');


        setTimeout(() => {
        
            $('.charts').empty();

            if(isPerChannel){
                //Render Chart By Year: line, bar, pie 
                renderChannelCharts(selectedCategory);

                //Render Pie Chart For Each Channel
                renderPieChartsByChannel(selectedCategory);

            } else {
                //Render Charts For Overall Total
                renderOverallTotalChart(selectedCategory);
            }

            // Trigger a reflow for the fade-in
            $('.charts').removeClass('fade-out').addClass('fade-in');
          
            // Remove the temporary class after fade-in
            setTimeout(() => {
                $('.charts').removeClass('fade-in');
            }, 300); 
           
        }, 500); 

    }

    function generateChannelData(chartType, isPerChannel = true, dataCategory = 'total', year){

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

        const newData = Object.entries(channelCodes).map(channel => {
            const dataStorage = [];
            let channelCode = channel[0]; //ex. ECOMM, RTL
            const weeks = channel[1][year]?.weeks;
            const lastThreeDays = channel[1][year]?.last_three_days;

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

                return {
                    label: `${channelCode}`,
                    data: dataStorage,
                    borderWidth: 2,
                    maxVal: maxVal

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
    
    function calculateMaxValuesInData(categoryVal) {
        const maxValues = {};

        const configToGetMaxVal = [
            { type: 'line', category: categoryVal, year: prevYear},
            { type: 'line', category: categoryVal, year: currYear},
        ];

        configToGetMaxVal.forEach(config => {

            const chartData = generateChannelData(config.type, true, config.category, config.year);

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

    function generateOverallTotalData(chartType, isPerChannel = false, dataCategory = 'total', prevYear, currYear){
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

    function generatePieChartDataByChannel(chartType = 'pie', isPerChannel = true, dataCategory = 'weekly', prevYear, currYear, key){
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
                        const netSales = weeks && weeks[key] ? weeks[key]?.sum_of_net_sales  :0;

                        if(netSales != 0) {
                            dataStorage.push(netSales);
                            pieLabels.push(prevYear + ' ' + key);
                        }
                    });

                    keys.forEach(key => {
                        const netSales = weeks2 && weeks2[key] ? weeks2[key]?.sum_of_net_sales : 0;

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

        // console.log(pieData);


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

    function generateUniqueId(prefix = 'canvas') {
        return `${prefix}-${Math.random().toString(36).substr(2, 9)}`;
    }
    

    // All Data Charts
    // const all = document.getElementById('all');
    // const alldata = generateOverallTotalData('line', false, 'last_three_days', '2021', '2022');
    // new Chart(all, alldata);


    // const all2 = document.getElementById('all2');
    // const alldata2 = generateChannelData('bar', true, 'last_three_days', '2021');
    // new Chart(all2, alldata2);

    // const all3 = document.getElementById('all3');
    // const alldata3 = generateChannelData('bar', true, 'last_three_days', '2022');
    // new Chart(all3, alldata3);


});
</script>
@endpush
