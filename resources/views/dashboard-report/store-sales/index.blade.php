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
            flex: 1;
            height: 100%;
            max-width: 50%;
            /* width: 50%; */
            box-sizing: border-box;
            border: 1px solid #ccc;
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
        <a href="{{ route('weekly_export_pdf') }}" class="btn btn-primary btn-sm pull-right">
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
    
    
    <div>
        <h2>Line Graph</h2>
        <div class="charts">
            <canvas id="myChart" style="width:350px; height:700px;"></canvas>
            <canvas id="myChart2" style="width:350px; height:700px;"></canvas>
        </div>
    </div>

    <div>
        <h2>Bar Graph</h2>
        <div class="charts">
            <canvas id="myChart3" style="width:350px; height:700px;"></canvas>
            <canvas id="myChart4" style="width:350px; height:700px;"></canvas>
        </div>
    </div>

    <div>
        <h2>Pie Chart</h2>
        <div class="charts">
            <canvas id="myChart5" style="width:350px;  height:700px;"></canvas>
            <canvas id="myChart6" style="width:350px;  height:700px;"></canvas>
        </div>
    </div>

    {{-- <div>
        <h2>All Data</h2>
        <div class="charts">
            <canvas id="all" style="width: 100%; height:700px;"></canvas>
            <canvas id="all2" style="width: 100%; height:700px;"></canvas>
        </div>
    </div> --}}

</div>

@endsection

@push('bottom')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const prevYear = @json($yearData['previousYear']);
    const currYear = @json($yearData['currentYear']);
    const channelCodes = @json($channel_codes);
    const lastThreeDays = @json($lastThreeDaysDates);
    const chartInstances = {};

    const chartConfigs = [
        { year: prevYear, type: 'line', category: 'total', canvasId: 'myChart' },
        { year: currYear, type: 'line', category: 'total', canvasId: 'myChart2' },
        { year: prevYear, type: 'bar', category: 'total', canvasId: 'myChart3' },
        { year: currYear, type: 'bar', category: 'total', canvasId: 'myChart4' },
        { year: prevYear, type: 'pie', category: 'total', canvasId: 'myChart5' },
        { year: currYear, type: 'pie', category: 'total', canvasId: 'myChart6' },
    ];

      // document.querySelectorAll('input[name="dataDisplay"]').forEach((radio) => {
    //     radio.addEventListener('change', renderCharts);
    // });

    document.getElementById('updateChartButton').addEventListener('click', function() {
        const selectedCategory = document.getElementById('categorySelector').value;
        updateCharts(selectedCategory);
    });

    // Initial rendering of charts
    renderCharts();


    function renderCharts() {
        // Get selected radio value
        const isPerChannel = document.querySelector('input[name="dataDisplay"]:checked').value === 'perChannel'; 

        // Calculate maximum values for the initial category
        const maxValues = calculateMaxValues('total');

        chartConfigs.forEach(config => {
            const chartData = generateChartData(config.year, config.type, config.category, isPerChannel);
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
                            max: maxValues.total
                        }
                    }
                }
            });
        });
}

    function updateCharts(selectedCategory) {
        // Get selected radio value
        const isPerChannel = document.querySelector('input[name="dataDisplay"]:checked').value === 'perChannel'; 
        
        // Calculate maximum values for the selected category
        const maxValues = calculateMaxValues(selectedCategory);

        chartConfigs.forEach(config => {
            config.category = selectedCategory; // Update category

            const chartData = generateChartData(config.year, config.type, config.category, isPerChannel);
            const ctx = document.getElementById(config.canvasId);

            // Destroy existing chart if it exists
            if (chartInstances[config.canvasId]) {
                chartInstances[config.canvasId].destroy(); 
            }

            chartInstances[config.canvasId] = new Chart(ctx, {
                ...chartData,
                options: {
                    ...chartData.options,
                    scales:{
                    ...chartData.options.scales,
                        y: {
                            ...chartData.options.scales?.y,
                            max: maxValues[selectedCategory] 
                        }
                    }
                
                }
            });
        });

    }

    function generateChartData(year, chartType = 'bar', dataCategory = "total", isPerChannel = true) {
        const datasets = [];
        const weeks = ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
        const keyDates = Object.keys(lastThreeDays);
        const labels = getLabels(year, dataCategory, weeks, keyDates);
        
        // console.log('Generating chart data for:', year, chartType, dataCategory); // Debug log

        const dataEntries = Object.entries(channelCodes).map(([channelCode, channelData]) => {
            const entry = generateDataEntry(channelCode, channelData[year], dataCategory, isPerChannel);
            // console.log('Data Entry:', entry); // Debug log
            return entry;
        }).filter(data => data !== null);


        return {
            type: chartType,
            data: {
                labels: labels,
                datasets: dataEntries,
            },
            options: getChartOptions(year, chartType),
        };
    }

    function getLabels(year, dataCategory, weeks, keyDates) {
        switch (dataCategory) {
            case 'total':
                const channelTotal = new Date().getFullYear() === year ? 'RUNNING' : 'TOTAL';
                return [channelTotal];
            case 'weekly':
                return weeks;
            case 'last_three_days':
                return keyDates;
            default:
                return [];
        }
    }

    function generateDataEntry(channelCode, yearData, dataCategory, isPerChannel) {
        const dataStorage = [];
        const weeks = yearData?.weeks || {};
        const lastThreeDays = yearData?.last_three_days || [];

        let maxVal = 0; // Initialize maxVal

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
           maxVal = fillDataStorage(dataStorage, weeks, dataCategory, lastThreeDays);
            return {
                label: channelCode,
                data: dataStorage,
                borderWidth: 2,
                pointBorderWidth: 5,
                maxVal: maxVal 
            };
        } else if (!isPerChannel && channelCode === 'TOTAL') {
            maxVal =  fillDataStorage(dataStorage, weeks, dataCategory, lastThreeDays);
            const channelTotal = new Date().getFullYear() === yearData.year ? 'RUNNING' : 'TOTAL';
            return {
                label: channelTotal,
                data: dataStorage,
                borderWidth: 2,
                maxVal: maxVal 
            };
        }
        return null;
    }

    function fillDataStorage(dataStorage, weeks, dataCategory, lastThreeDays) {
        const keys = dataCategory === 'total' ? ['TOTAL'] : (dataCategory === 'weekly' ? ['WK01', 'WK02', 'WK03', 'WK04'] : lastThreeDays.map(day => day.date_of_the_day));

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

        return dataStorage.length > 0 ? Math.max(...dataStorage) : 0;
        
    }

    function getChartOptions(year, chartType) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: 20
            },
            plugins: {
                title: {
                    display: true,
                    text: `${year} Sales Data`,
                    font: {
                        size: 16,
                    },
                    padding: {
                        top: 20,
                        bottom: 20,
                    },
                },
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 10
                    }
                },
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
            },
            responsive: true,
            maintainAspectRatio: false,
        };
    }

    function calculateMaxValues(categoryVal) {
        const maxValues = {};

        const chartConfigs2 = [
            { year: prevYear, type: 'line', category: categoryVal, canvasId: 'myChart' },
            { year: currYear, type: 'line', category: categoryVal, canvasId: 'myChart2' },
        ];

        chartConfigs2.forEach(config => {

            const isPerChannel = document.querySelector('input[name="dataDisplay"]:checked').value === 'perChannel'; 

            const chartData = generateChartData(config.year, config.type, config.category, isPerChannel);
            console.log(chartData);
            const dataEntries = chartData.data.datasets;

            dataEntries.forEach(dataset => {
                let maxVal = dataset.maxVal;
                if (!maxValues[config.category] || maxVal > maxValues[config.category]) {
                    const buffer = maxVal * 0.2; // 10% buffer
                    maxVal += buffer;

                    maxValues[config.category] = maxVal ;
                }
            });
        });

        return maxValues;
    }

    // All Data Charts
    // const all = document.getElementById('all');
    // const all2 = document.getElementById('all2');
    // const alldata = generateChartData(prevYear, 'bar');
    // const alldata2 = generateChartData(currYear, 'bar');

    // new Chart(all, alldata);
    // new Chart(all2, alldata2);

</script>
@endpush
