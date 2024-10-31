@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

{{-- <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> --}}
<style>

    #hidden_charts_storage_for_export {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        visibility: hidden;
        position: fixed;
        z-index: -9999;
        bottom: 0;
        pointer-events: none;
    }
    #charts_container {
        width: 100%;
        display: flex; 
        flex-wrap: wrap; 
    }
    .chart {
        flex: 1 1 auto;
        width: 100%;
        min-width: 300px; 
        height: 600px; /* column chart size */
        margin-bottom: 20px;
        position: relative; 
    }
    #loading {
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        border-radius: 5px;
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
    }

    .loader {
        border: 8px solid lightgray;
        border-top: 8px solid white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
        color: white;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgb(221, 221, 221); 
        color: black;
        border: none;
        font-size: 15px;
    }

    .custom-modal {
        max-width: 70%;
        width: 100%;
        margin: 0 auto;
    }

    #divForSelectedInputsInForm{
        margin-top: 20px; 
        width:100%;
        padding:10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: white;
        display: none;
        position:relative;
    }
   
    .no-data-container {
        text-align: center;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        width: 100%;
        height: 250px;
        display: grid;
        place-content: center;
    }
    .no-data-icon {
        font-size: 50px;
        color: #0793ff;
        margin-bottom: 15px;
    }
    .no-data-message {
        font-size: 20px;
        color: #333;
    }

    .text-danger {
        color: red;
        margin-top: 5px;
        font-size: 0.875em;
    }

</style>

@endpush

@section('content')

<div class="main-content">

    <br>

    <!-- Button to trigger modal -->
    <button  class="btn btn-sm btn-primary" data-toggle="modal" data-target="#chartModal">
        <i class="fa fa-bar-chart" aria-hidden="true"></i>  Generate Chart
    </button>

    <button id="saveChartBtn" class="btn btn-sm btn-info pull-right" > 
        <i class="fa fa-download" aria-hidden="true"></i>  Download Chart
    </button>

   {{-- MODAL  --}}
    <div class="modal fade" id="chartModal" tabindex="-1" role="dialog" aria-labelledby="chartModalLabel">
        <div class="modal-dialog custom-modal" role="document">
            <div class="modal-content">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="chartModalLabel">Generate Chart</h4>
                </div>

                <div class="modal-body">
                    <form id="chartForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Chart Type:</label>
                                    <select class="form-control select2" id="type" name="types[]" multiple='multiple' required>
                                        <option value="pie" selected >PIE CHART</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="yearFrom">Year From:</label>
                                    <select class="form-control" id="yearFrom" name="year_from" required>
                                        <option value="" disabled selected>Select Year</option>
                                        @php
                                            $startYear = 2019;
                                            $currentYear = date('Y');
                                        @endphp
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="monthFrom">Month From:</label>
                                    <select class="form-control" id="monthFrom" name="month_from" required >
                                        <option value="" disabled selected>Select Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label for="store">Store:</label>
                                    <select class="form-control select2" id="store" name="stores[]" multiple='multiple' required>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->name}}</option>
                                        @endforeach
            
                                    </select>
                                </div>
                            
                                <div class="form-group">
                                    <label for="brand">Brand:</label>
                                    <select class="form-control select2" id="brand" name="brands[]"  multiple='multiple' required>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($brands as $brand)
                                            <option>{{$brand->name}}</option>
                                        @endforeach
                                    
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label for="category">Category:</label>
                                    <select class="form-control select2" id="category" name="categories[]" multiple='multiple' required>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($categories as $category)
                                            <option>{{$category->name}}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="group">Group:</label>
                                    <select class="form-control" id="group" name="group" >
                                        <option value="" disabled selected>Select Group</option>
                                        <option value="group1">Group 1</option>
                                        <option value="group2">Group 2</option>
                                        <option value="group3">Group 3</option>
                                    </select>
                                </div> 
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="channel">Channel:</label>
                                    <select class="form-control select2" id="channel" name="channels[]"  multiple='multiple' required>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($channels as $channel)
                                            <option value="{{$channel->id}}">{{$channel->name}}</option>
                                        @endforeach
                            
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="yearTo">Year To:</label>
                                    <select class="form-control" id="yearTo" name="year_to" required>
                                        <option value="" disabled selected>Select Year</option>
                                        @php
                                            $startYear = 2019;
                                            $currentYear = date('Y');
                                        @endphp
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="monthTo">Month To:</label>
                                    <select class="form-control" id="monthTo" name="month_to" required>
                                        <option value="" disabled selected>Select Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                
                                <div class="form-group">
                                    <label for="storeConcept">Store Concept:</label>
                                    <select class="form-control select2" id="storeConcept" name="concepts[]"  multiple='multiple' required>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($concepts as $concept)
                                            <option value="{{$concept->id}}">{{$concept->name}}</option>
                                        @endforeach
            
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label for="mall">Mall:</label>
                                    <select class="form-control select2" id="mall" name="malls[]" multiple='multiple' required>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($malls as $mall)
                                            <option>{{$mall->name}}</option>
                                        @endforeach
                                
                                    </select>
                                </div>
                
                                <div class="form-group">
                                    <label for="sqm">Square Meters (sqm):</label>
                                    <select class="form-control" id="sqm" name="sqm" >
                                        <option value="" disabled selected>Select SQM</option>
                                        <option value="50">50 sqm</option>
                                        <option value="100">100 sqm</option>
                                        <option value="150">150 sqm</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div  style="display: flex; gap:20px; justify-content:center; margin-top:20px;">
                                <button id="clearForm" type="reset" class="btn btn-secondary" style="width:80px;">Reset</button>
                                <button type="submit" class="btn btn-primary" style="width:80px;">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="divForSelectedInputsInForm">
        <h5 style="margin-bottom:20px; font-weight:bold;" >The Selected Parameters for Chart Creation: </h5>
        <table id="selectedValues" class="table table-bordered table-striped" style="display: none; margin-top: 10px; ">
            <colgroup>
                <col style="width: 180px;">
                <col>
            </colgroup>

            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>

            <tbody>
                <!-- Selected values will be populated here -->
            </tbody>
        </table>

        <div id="loading" class="text-center" style="display: none">
            <div class="loader"></div>
            <p>Loading, please wait...</p>
        </div>
    </div>

    <div id="noDataMessage" class="no-data-container">
        <i class="fa fa-bar-chart no-data-icon" aria-hidden="true" ></i>
        
        <div class="no-data-message" style="margin-bottom:10px;">
            <strong>No Chart Available.</strong>
        </div>
        <p>Please click on the "Generate Chart" button to create charts. Fill up the fields and click "Submit".</p>
    </div>

    <br>

    {{-- Container for what we see in the page. --}}
    <div id="charts_container"></div>
    
    {{-- Use for exporting charts that have a default size, so it won't break when it downloads.  --}}
    <div id="hidden_charts_storage_for_export"></div>
    
</div>

@endsection

@push('bottom')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">

$(function() {

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages': ['corechart']});

    const chartImagesToDownload = [];

    const monthNames = [
        'January', 'February', 'March', 'April',
        'May', 'June', 'July', 'August',
        'September', 'October', 'November', 'December'
    ];

    //show modal for the initial render
    $('#chartModal').modal('show');

    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: '100%',
        tags: true
    });

    // Handle the "ALL" option
    const selectors = ['#store', '#channel', '#brand', '#category', '#storeConcept', '#mall', '#type'];
    selectors.forEach(selector => {
        $(selector + ' option:not(:first-child)').prop('disabled', true);
        handleSelect2Event(selector);
    });

    // Dynamic option for the chart type
    $('#channel').on('change', function() {
        const channelValue = $(this).val();

        if(channelValue){
            const condition = channelValue[0];
            const $select = $('#type');
            $select.empty();

            if (condition === 'all') {
                $select.append(new Option('PIE CHART', 'pie'));
            } else {
                $select.append(new Option('ALL', 'all'));
                $select.append(new Option('LINE GRAPH', 'line'));
                $select.append(new Option('BAR GRAPH', 'bar'));
            }

            $select.val($select.find('option:first').val()).trigger('change');
        }
    });

    $('#chartForm').on('submit', function(e) {
        e.preventDefault(); 

        hideModalAndShowLoading();
        clearChartContainer();

        const formData = collectFormData();

        $.ajax({
            url: '{{ route("charts") }}', 
            method: 'POST', 
            data: formData,
            success: handleSuccessResponse,
            error: handleErrorResponse
        });

        clearPreviousRows();
        populateDivForSelectedInputsInForm(formData);
    });

    $('#saveChartBtn').click(downloadCharts);

    $('#clearForm').on('click', function() {
        resetForm();
    });

    function handleSelect2Event(selector) {
        $(selector).on('select2:select', function (e) {
            const selectedValue = e?.params?.data?.id;

            // If "ALL" is selected, disable other options
            if (selectedValue === 'all') {
                $(selector).find('option:not(:first-child)').prop('disabled', true);
                $(selector).val('all').trigger('change'); // Ensure "ALL" is selected
            } else {
                // If another option is selected, unselect "ALL"
                $(selector).find('option[value="all"]').prop('selected', false).trigger('change');
            }
        });

        $(selector).on('select2:unselecting', function (e) {
            const unselectedValue = e?.params?.args?.data?.id;

            // If "ALL" is unselected, re-enable all options
            if (unselectedValue === 'all') {
                $(selector).find('option').prop('disabled', false);
            }
        });
    }

    function hideModalAndShowLoading() {
        $('#chartModal').modal('hide');
        $('#loading').show();
    }

    function clearChartContainer() {
        $('#charts_container').empty();
        $('#hidden_charts_storage_for_export').empty();
        $('#divForSelectedInputsInForm').hide();
    }

    function collectFormData() {
        return {
            types: $('#type').val(),
            yearFrom: $('#yearFrom').val(),
            yearTo: $('#yearTo').val(),
            monthFrom: $('#monthFrom').val(),
            monthTo: $('#monthTo').val(),
            stores: $('#store').val(),
            channels: $('#channel').val(),
            brands: $('#brand').val(),
            categories: $('#category').val(),
            concepts: $('#storeConcept').val(),
            mall: $('#mall').val(),
            sqm: $('#sqm').val(),
            group: $('#group').val(),
        };
    }

    function handleSuccessResponse(data) {
        $('#noDataMessage').hide();

        if (data.chartData) {
            processChartData(data);
        } else {
            showNoDataMessage();
        }

        resetForm();
        $('#loading').hide();
    }

    function processChartData(data) {
        const types = $('#type').val();


        if (data.multipleChannel) {
            drawChartMultipleChannel(data.chartData, data.years);
            data.years.forEach(year => drawChartMultipleChannelByYear(data.chartData, year));
        } else {
            const chartTypes = ['line', 'bar'];
            const selectedTypes = types[0] === 'all' ? chartTypes : types;

            selectedTypes.forEach(type => {
                drawChartWithDynamicMonths(data.chartData, data.years, type);
            });
        }
    }

    function showNoDataMessage() {
        $('#noDataMessage p').text('No data was found based on the current inputs. Please ensure all fields are filled out correctly and click "Generate Chart" to try again.');
        $('#noDataMessage').show();
    }

    function handleErrorResponse(xhr) {
        $('#loading').hide();
        console.error('Error fetching data:', xhr);

        if (xhr?.status === 422) {
            handleValidationErrors(xhr.responseJSON.errors);
            repopulateFormFields();
        }

        setTimeout(() => {
            $('#chartModal').modal('show');
        }, 300);
    }

    function handleValidationErrors(errors) {
        $('.form-group .text-danger').remove();
        $.each(errors, function(key, messages) {
            const input = $(`#${key}`);
            input.parent().append(`<div class="text-danger">${messages.join(', ')}</div>`);
        });
    }

    function repopulateFormFields() {
        const formData = collectFormData();

        $('#type').val(formData.types);
        $('#yearFrom').val(formData.yearFrom);
        $('#yearTo').val(formData.yearTo);
        $('#monthFrom').val(formData.monthFrom);
        $('#monthTo').val(formData.monthTo);
        $('#store').val(formData.stores).trigger('change'); 
        $('#channel').val(formData.channels).trigger('change');
        $('#brand').val(formData.brands).trigger('change');
        $('#category').val(formData.categories).trigger('change');
        $('#storeConcept').val(formData.concepts).trigger('change');
        $('#mall').val(formData.mall).trigger('change');
        $('#sqm').val(formData.sqm);
        $('#group').val(formData.group);
    }

    function clearPreviousRows() {
        $('#selectedValues tbody').empty();
    }

    function populateDivForSelectedInputsInForm(formData) {
        let channelValues = $('#channel').val().map(value => $('#channel option[value="' + value + '"]').text()).join(', ');
        let chartType = channelValues === "ALL" ? 'PIE CHART' : $('#type').val().map(value => $('#type option[value="' + value + '"]').text()).join(', ');

        let values = {
            "Chart Type:": chartType,
            "Year From": $('#yearFrom option:selected').text(),
            "Year To": $('#yearTo option:selected').text(),
            "Month From": $('#monthFrom option:selected').text(),
            "Month To": $('#monthTo option:selected').text(),
            "Store": $('#store').val().map(value => $('#store option[value="' + value + '"]').text()).join(', '),  
            "Channel": channelValues,
            "Brand": $('#brand').val().join(', '), 
            "Category": $('#category').val().join(', '), 
            "Store Concept": $('#storeConcept').val().map(value => $('#storeConcept option[value="' + value + '"]').text()).join(', '), 
            "Mall": $('#mall').val().join(', '), 
            "Square Meters": $('#sqm').val() || 'N/A',
            "Group": $('#group').val() || 'N/A'
        };
        
        //use for download charts, this is where it will get the table data.
        localStorage.setItem('formData', JSON.stringify(values));
        
        $.each(values, function(field, value) {
            $('#selectedValues tbody').append(`
                <tr>
                    <td>${field}</td>
                    <td>${value.toUpperCase()}</td>
                </tr>
            `);
        });

        $('.no-data-container').hide();
        $('#divForSelectedInputsInForm').show();
        $('#selectedValues').show();
    }

    function resetForm(){

        // Reset the form to its initial state
        $('#chartForm')[0].reset(); 

        $('#type').val('all').trigger('change');
        $('#store').val('all').trigger('change');
        $('#channel').val('all').trigger('change');
        $('#brand').val('all').trigger('change');
        $('#category').val('all').trigger('change');
        $('#storeConcept').val('all').trigger('change');
        $('#mall').val('all').trigger('change');

        //Clear the error texts
        $('.form-group .text-danger').remove();
    }

    function drawChartWithDynamicMonths(months, years, type) {
        const dataArray = [['Month', ...years]];

        // Find the latest year and relevant months
        const latestYear = Math.max(...years.map(Number));
        const relevantMonths = getRelevantMonths(months, years);

        // Filter months to include only those available up to the last month of the year
        const lastMonthKey = getLastMonthKey(relevantMonths);
        const filteredMonths = relevantMonths.filter(([monthKey]) => {
            const monthIndex = parseInt(monthKey.replace('M', ''));
            return monthIndex <= lastMonthKey;
        });

        // Populate dataArray with filtered months
        populateDataArray(filteredMonths, years, dataArray);

        // Check if dataArray has enough data to draw the chart
        if (!hasValidData(dataArray)) return;

        const data = google.visualization.arrayToDataTable(dataArray);
        const options = createBarLineChartOptions();

        // Draw the first chart that will be render in the view.
        const chartDiv1 = createChartDiv('charts_container');
        drawBarLineChart(type, chartDiv1, data, options);

        // Draw the second chart, adding default size for chart export so it won't break.
        const options2 = { ...options, width: 1200, height: 600 };
        const chartDiv2 = createChartDiv('hidden_charts_storage_for_export');
        const channelChart2 = drawBarLineChart(type, chartDiv2, data, options2);
      
        // Get the chart as an image and store it
        storeChartImage(channelChart2);
    }


    function drawChartMultipleChannel(channelCodes, years) {

        const dataArray = [['Channel', 'data']];

        Object.keys(channelCodes).forEach(channel => {

            // const convertedChannel = getSwitchChannel(channel);
            
            years.forEach(year => {
                dataArray.push([`${channel} ${year}`, channelCodes[channel][`Y${year}`]|| 0]); 
            });
        });

        // Check if dataArray has enough data to draw the chart
        if (!hasValidData(dataArray)) return;

        const data = google.visualization.arrayToDataTable(dataArray);
        const monthFrom = $('#monthFrom option:selected').text();
        const monthTo = $('#monthTo option:selected').text();

        const options = createPieChartOptions(monthFrom, monthTo);

        // Draw the first chart that will be render in the view.
        const chartDiv1 = createChartDiv('charts_container');
        drawPieChart(chartDiv1, data, options);

        // Draw the second chart, adding default size for chart export so it won't break.
        const options2 = { ...options, width: 1200, height: 600 };
        const chartDiv2 = createChartDiv('hidden_charts_storage_for_export');
        const channelChart2 = drawPieChart(chartDiv2, data, options2);

        // Get the chart as an image and store it
        storeChartImage(channelChart2);
        
    }

    function drawChartMultipleChannelByYear(channelCodes, year) {

        const dataArray = [['Channel', 'data']];

        Object.keys(channelCodes).forEach(channel => {
            // const convertedChannel = getSwitchChannel(channel);
            
            dataArray.push([`${channel}`, channelCodes[channel][`Y${year}`]|| 0]); 
        });

        // Check if dataArray has enough data to draw the chart
        if (!hasValidData(dataArray)) return;

        const data = google.visualization.arrayToDataTable(dataArray);
        const monthFrom = $('#monthFrom option:selected').text();
        const monthTo = $('#monthTo option:selected').text();

        const options = createPieChartOptions(monthFrom, monthTo, year);

        // Draw the first chart that will be render in the view.
        const chartDiv1 = createChartDiv('charts_container');
        drawPieChart(chartDiv1, data, options);

        // Draw the second chart, adding default size for chart export so it won't break.
        const options2 = { ...options, width: 1200, height: 600 };
        const chartDiv2 = createChartDiv('hidden_charts_storage_for_export');
        const channelChart2 = drawPieChart(chartDiv2, data, options2);

        // Get the chart as an image and store it
        storeChartImage(channelChart2);
    }

    function getSwitchChannel(channel){
        
        switch(channel){
            case 'RTL':
                channel = 'TOTAL-RTL';
                break;
            case 'ONL':
                channel = 'ECOMM';
                break;
            case 'FRA ':
                channel = 'FRA-DR';
                break;
            case 'DLR':
            case 'CRP':
                channel = 'OUT';
                break;
            case 'SVC':
                channel = 'SC';
                break;
            case 'CON':
                channel = 'CON';
                break;
        }

        return channel;
    }

    // Save chart function
    function downloadCharts() {

        // Retrieve form data from local storage, the setter is in populateDivForSelectedInputsInForm
        const storedData = localStorage.getItem('formData');
        const formData = storedData ? JSON.parse(storedData) : {};


        fetch('/admin/save_chart', {
            method: 'POST',
            body: JSON.stringify({ images: chartImagesToDownload, data: formData }), 
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.blob(); // Change to blob for downloading
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'generated-charts.pdf'; 
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => console.error('Error:', error));
    }

    function createPieChartOptions(monthFrom, monthTo, year = '') {
        return {
            title: `Sales Report from ${monthFrom} to ${monthTo} ${year}`,
            is3D: true,
            pieSliceText: 'value',
            chartArea: {
                width: '50%',
                height: '50%',
                top: 100,
                left: 250,
                right: 0,
                bottom: 0,
            },
            legend: { position: 'right', textStyle: { fontSize: 13 }, alignment: 'center' },
        };
    }

    function createBarLineChartOptions() {
        return {
            title: 'Sales Report',
            hAxis: { title: 'Months' },
            vAxis: { title: 'Sales' },
            isStacked: false,
            chartArea: {
                width: '100%',
                height: '100%',
                top: 100,
                left: 150,
                right: 50,
                bottom: 50,
            },
            legend: { position: 'top', textStyle: { fontSize: 13 }, alignment: 'end' },
            annotations: {
                textStyle: {
                    color: '#000',
                    fontSize: 12 
                }
            }
        };
    }

    function drawPieChart(chartDiv, data, options) {
        const chart = new google.visualization.PieChart(chartDiv);
        chart.draw(data, options);
        
        // Add resize event listener
        window.addEventListener('resize', () => {
            chart.draw(data, options);
        });
        
        return chart;
    }

    function drawBarLineChart(type, chartDiv, data, options) {
        let chart 
        console.log(type);
        switch (type) {
            case 'bar':
                chart = new google.visualization.ColumnChart(chartDiv);
                break;
            case 'line':
                chart = new google.visualization.LineChart(chartDiv);
                break;
            default:
                throw new Error('Unsupported chart type: ' + type);
        }
        
        chart.draw(data, options);
        
        // Add resize event listener
        window.addEventListener('resize', () => {
            chart.draw(data, options);
        });
        
        return chart;
    }

    function createChartDiv(containerId) {
        const chartDiv = document.createElement('div');
        chartDiv.className = 'chart';
        document.getElementById(containerId).appendChild(chartDiv);
        return chartDiv;
    }

    function storeChartImage(chart) {
        const imgUri = chart.getImageURI();
        chartImagesToDownload.push(imgUri);
    }

    function getRelevantMonths(months, years) {
        return Object.entries(months).filter(([_, monthData]) => {
            return years.some(year => monthData[`Y${year}`] !== undefined);
        });
    }

    function getLastMonthKey(relevantMonths) {
        return Math.max(
            ...relevantMonths.map(([monthKey]) => parseInt(monthKey.replace('M', '')))
        );
    }

    function populateDataArray(filteredMonths, years, dataArray) {
        filteredMonths.forEach(([monthKey, monthData]) => {
            const monthIndex = parseInt(monthKey.replace('M', '')) - 1; // Adjust month index
            const rowData = [`${monthNames[monthIndex]}`]; // Start row with month name

            years.forEach(year => {
                rowData.push(monthData[`Y${year}`] || 0); 
            });

            dataArray.push(rowData);
        });
    }

    function hasValidData(dataArray) {
        if (dataArray.length <= 1) {
            if (!document.getElementById('chartErrorMessage')) {
                $('#noDataMessage p').text('No data was found based on the current inputs. Please ensure all fields are filled out correctly and click "Generate Chart" to try again.');
                $('#noDataMessage').show();
            }
            return false;
        }
        return true;
    }
    
});

</script>



</script>
@endpush
