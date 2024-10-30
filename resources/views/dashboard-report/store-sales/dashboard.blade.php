@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

{{-- <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> --}}
<style>
    #charts_container {
        width: 100%;
        display: flex; 
        flex-wrap: wrap; 
    }
    /* .chart {
        flex: 1 1 auto;
        width: 100%;
        min-width: 300px; 
        height: 700px; 
        height: 550px; 
        margin-bottom: 20px;
        position: relative; 
    } */
    .chart {
        flex: 1 1 auto;
        width: 100%;
        min-width: 300px; 
        height: 600px; /* column chart size */
        margin-bottom: 20px;
        position: relative; 
    }
    .container{
        background-color: #fff;
        border-radius: 5px;
        padding: 20px;
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

    #formSelectedValues{
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

    <div id="formSelectedValues">
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
  
    <div id="loading" class="text-center" style="display: none">
        <div class="loader"></div>
        <p>Loading, please wait...</p>
    </div>

    <br>

    <div id="charts_container"></div>
    
</div>

@endsection

@push('bottom')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">

$(function() {

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages': ['corechart']});

    let chartImagesToDownload = [];

    const monthNames = [
        'January', 'February', 'March', 'April',
        'May', 'June', 'July', 'August',
        'September', 'October', 'November', 'December'
    ];

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

    $('#saveChartBtn').click(saveChart);

    $('#chartForm').on('submit', function(e) {
        e.preventDefault(); 

        $('#chartModal').modal('hide');
        $('#charts_container').empty();
        $('#formSelectedValues').hide();
        $('#loading').show();

        const formData = {
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
        }

        // console.log(formData);


        $.ajax({
            url: '{{ route("charts") }}', 
            method: 'POST', 
            data: formData,
            success: function(data) {

                chartImagesToDownload = [];

                const chartTypes = ['line', 'bar', 'pie'];

                if (data.chartData) {
                    $('#noDataMessage').hide(); 

                    if(data.multipleChannel){
                        drawChartMultipleChannel(data.chartData, data.years);

                        data.years.forEach(year => drawChartMultipleChannelByYear(data.chartData, year));

                    } else {

                    console.log(formData.types);

                        if(formData.types[0] === 'all'){
                            chartTypes.forEach(type => {

                                if(type !== 'pie'){
                                    drawChartWithDynamicMonths(data.chartData, data.years, type);
                                } 
                            });

                        } else {
                            formData?.types.forEach(type => {
                                if(type !== 'pie'){
                                    drawChartWithDynamicMonths(data.chartData, data.years, type);
                                } 
                            });
                        }

                    }


                } else {

                    $('#noDataMessage p').text('No data was found based on the current inputs. Please ensure all fields are filled out correctly and click "Generate Chart" to try again.');
                    $('#noDataMessage').show();
                }

                console.log('ito yung mga chart images', chartImagesToDownload);

                resetForm();

                $('#loading').hide();

            },
            error: function(xhr) {
                $('#loading').hide();

                console.error('Error fetching data:', xhr);

                // Check if the response contains validation errors
                if (xhr?.status === 422) { 
                    const errors = xhr?.responseJSON?.errors;

                    // Clear previous error messages
                    $('.form-group .text-danger').remove();

                    // Loop through each error and display it
                    $.each(errors, function(key, messages) {
                        // Find the relevant input field and append the error message
                        const input = $(`#${key}`);
                        input.parent().append(`<div class="text-danger">${messages.join(', ')}</div>`);
                    });

                    // Populate fields with submitted values to retain user input
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

                //add delay so the modal doesn't break
                setTimeout(() => {
                    $('#chartModal').modal('show');
                }, 300);

            

            }
        });

        // Clear previous rows
        $('#selectedValues tbody').empty();

        let channelValues = $('#channel').val().map(function(value) {
            return $('#channel option[value="' + value + '"]').text();
        }).join(', ');

        let chartType = channelValues == "ALL" ? 'PIE CHART' : $('#type').val().map(function(value) {
            return $('#type option[value="' + value + '"]').text();
        }).join(', ');
        
        let values = {
            "Chart Type:": chartType,
            "Year From": $('#yearFrom option:selected').text(),
            "Year To": $('#yearTo option:selected').text(),
            "Month From": $('#monthFrom option:selected').text(),
            "Month To": $('#monthTo option:selected').text(),
            "Store": $('#store').val().map(function(value) {
                return $('#store option[value="' + value + '"]').text();
            }).join(', '),  
            "Channel": channelValues,
            "Brand": $('#brand').val().join(', '), 
            "Category": $('#category').val().join(', '), 
            "Store Concept": $('#storeConcept').val().map(function(value) {
                return $('#storeConcept option[value="' + value + '"]').text();
            }).join(', '), 
            "Mall": $('#mall').val().join(', '), 
            "Square Meters": $('#sqm').val() ?? 'N/A',
            "Group": $('#group').val() ?? 'N/A'
        };
        
        localStorage.setItem('formData', JSON.stringify(values));

        
        // Populate the table
        $.each(values, function(field, value) {
            $('#selectedValues tbody').append(`
                <tr>
                    <td>${field}</td>
                    <td>${value.toUpperCase()}</td>
                </tr>
            `);
        });

        $('.no-data-container').hide();

        $('#formSelectedValues').show();

        // Show the table
        $('#selectedValues').show();

        
    });

    $('#clearForm').on('click', function() {
        resetForm();
    });

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

        $('.form-group .text-danger').remove();

    }

    function drawChart(months, years, type) {
        const dataArray = [['Month', ...years]];

        Object.entries(months).forEach(([monthKey, monthData]) => {
            const monthIndex = parseInt(monthKey.replace('M', '')) - 1; // Adjust month index
            const rowData = [`${monthNames[monthIndex]}`]; // Start row with month name

            // Loop through years to dynamically add sales data
            years.forEach(year => {
                rowData.push(monthData[`Y${year}`] || 0); 
            });

            // Push the row data to the array
            dataArray.push(rowData);
        });

        const data = google.visualization.arrayToDataTable(dataArray);

        const options = {
            title: ` Sales Report`,
            hAxis: { title: 'Months' },
            vAxis: { title: 'Sales' },
            isStacked: false,
            chartArea: {
                width:'100%',
                height:'100%',
                top: 100, 
                left: 150, 
                right:50,
                bottom:50,
            },
            legend:{position: 'top', textStyle: { fontSize: 13}, alignment:'end'},
            annotations: {
                textStyle: {
                    color: '#000', 
                    fontSize: 12 
                }
            }
        };

        // Create a new div for each chart
        const chartDiv = document.createElement('div');
        chartDiv.className = 'chart';
        document.getElementById('charts_container').appendChild(chartDiv);

        let channelChart;

        switch(type){
            case 'bar':
                channelChart = new google.visualization.ColumnChart(chartDiv);
            break;
            case 'line':
                channelChart = new google.visualization.LineChart(chartDiv);
            break;
        }
         
        channelChart.draw(data, options);
        
        // Add resize event listener
        window.addEventListener('resize', () => {
            channelChart.draw(data, options);
        });

        // Get the chart as an image and store it
        const imgUri = channelChart.getImageURI();
        chartImagesToDownload.push(imgUri);
    }

    function drawChartWithDynamicMonths(months, years, type) {
        const dataArray = [['Month', ...years]];

        // Find the latest year from the years array
        const latestYear = Math.max(...years.map(year => parseInt(year)));

        // Gather months that have data for any of the years
        const relevantMonths = Object.entries(months).filter(([monthKey, monthData]) => {
            return years.some(year => monthData[`Y${year}`] !== undefined);
        });

        // Determine the last month available in any year
        const lastMonthKey = Math.max(
            ...relevantMonths.map(([monthKey]) => parseInt(monthKey.replace('M', '')))
        );

        // Filter to only include months up to the last month of the available years
        const filteredMonths = relevantMonths.filter(([monthKey]) => {
            const monthIndex = parseInt(monthKey.replace('M', ''));
            return monthIndex <= lastMonthKey;
        });

        // Populate dataArray with filtered months
        filteredMonths.forEach(([monthKey, monthData]) => {
            const monthIndex = parseInt(monthKey.replace('M', '')) - 1; // Adjust month index
            const rowData = [`${monthNames[monthIndex]}`]; // Start row with month name

            // Loop through years to dynamically add sales data
            years.forEach(year => {
                rowData.push(monthData[`Y${year}`] || 0); 
            });

            // Push the row data to the array
            dataArray.push(rowData);
        });

        console.log(dataArray);

        const data = google.visualization.arrayToDataTable(dataArray);

        const options = {
            title: ` Sales Report`,
            hAxis: { title: 'Months' },
            vAxis: { title: 'Sales' },
            isStacked: false,
            chartArea: {
                width:'100%',
                height:'100%',
                top: 100, 
                left: 150, 
                right:50,
                bottom:50,
            },
            legend:{position: 'top', textStyle: { fontSize: 13}, alignment:'end'},
            annotations: {
                textStyle: {
                    color: '#000', 
                    fontSize: 12 
                }
            }
        };

        // Create a new div for each chart
        const chartDiv = document.createElement('div');
        chartDiv.className = 'chart';
        document.getElementById('charts_container').appendChild(chartDiv);

        let channelChart;

        switch(type){
            case 'bar':
                channelChart = new google.visualization.ColumnChart(chartDiv);
            break;
            case 'line':
                channelChart = new google.visualization.LineChart(chartDiv);
            break;
            case 'pie':
                channelChart = new google.visualization.PieChart(chartDiv);
            break;
        }
         
        channelChart.draw(data, options);
        
        // Add resize event listener
        window.addEventListener('resize', () => {
            channelChart.draw(data, options);
        });

        // Get the chart as an image and store it
        const imgUri = channelChart.getImageURI();
        chartImagesToDownload.push(imgUri);
    }

    function drawChartWithDynamicMonthsInPie(months, years) {
        const dataArray = [['Month', 'Data']];

        // Find the latest year from the years array
        const latestYear = Math.max(...years.map(year => parseInt(year)));

        // Gather months that have data for any of the years
        const relevantMonths = Object.entries(months).filter(([monthKey, monthData]) => {
            return years.some(year => monthData[`Y${year}`] !== undefined);
        });
        
        // Determine the last month available in any year
        const lastMonthKey = Math.max(
            ...relevantMonths.map(([monthKey]) => parseInt(monthKey.replace('M', '')))
        );

        // Filter to only include months up to the last month of the available years
        const filteredMonths = relevantMonths.filter(([monthKey]) => {
            const monthIndex = parseInt(monthKey.replace('M', ''));
            return monthIndex <= lastMonthKey;
        });

        // Populate dataArray with filtered months
        filteredMonths.forEach(([monthKey, monthData]) => {
            const monthIndex = parseInt(monthKey.replace('M', '')) - 1; // Adjust month index

            // Loop through years to dynamically add sales data
            years.forEach(year => {
                dataArray.push([`${monthNames[monthIndex]} ${year}`, monthData[`Y${year}`] || 0]); // Add sales for the year, default to 0 if undefined
            });
  
        });

        const data = google.visualization.arrayToDataTable(dataArray);

        const options = {
             title: `Sales Report`,
             is3D: true,
             pieSliceText: 'value',
             chartArea: {
                 width:'50%',
                 height:'50%',
                 top: 100, 
                 left: 250, 
                 right:0,
                 bottom:0,
             },
             legend:{position: 'right', textStyle: { fontSize: 13}, alignment:'center'},
         };

        // Create a new div for each chart
        const chartDiv = document.createElement('div');
        chartDiv.className = 'chart';
        document.getElementById('charts_container').appendChild(chartDiv);

        const channelChart = new google.visualization.PieChart(chartDiv);

        channelChart.draw(data, options);
        
        // Add resize event listener
        window.addEventListener('resize', () => {
            channelChart.draw(data, options);
        });

        // Get the chart as an image and store it
        const imgUri = channelChart.getImageURI();
        chartImagesToDownload.push(imgUri);
    }

    function drawChartMultipleChannel(channelCodes, years) {

        const dataArray = [['Channel', 'data']];

        Object.keys(channelCodes).forEach(channel => {

            years.forEach(year => {
                dataArray.push([`${channel} ${year}`, channelCodes[channel][`Y${year}`]|| 0]); 
            });

        });

        const data = google.visualization.arrayToDataTable(dataArray);

        const monthFrom = $('#monthFrom option:selected').text();
        const monthTo = $('#monthTo option:selected').text();

         const options = {
             title: `Sales Report from ${monthFrom} to ${monthTo}`,
             is3D: true,
             pieSliceText: 'value',
             chartArea: {
                 width:'50%',
                 height:'50%',
                 top: 100, 
                 left: 250, 
                 right:0,
                 bottom:0,
             },
             legend:{position: 'right', textStyle: { fontSize: 13}, alignment:'center'},
         };

        // Create a new div for each chart
        const chartDiv = document.createElement('div');
        // chartDiv.id = `2024-chart`;
        chartDiv.className = 'chart';
        document.getElementById('charts_container').appendChild(chartDiv);

        const channelChart = new google.visualization.PieChart(chartDiv);
         
        channelChart.draw(data, options);
        
        // Add resize event listener
        window.addEventListener('resize', () => {
            channelChart.draw(data, options);
        });

        // Get the chart as an image and store it
        const imgUri = channelChart.getImageURI();
        chartImagesToDownload.push(imgUri);
    }

    function drawChartMultipleChannelByYear(channelCodes, year) {

        const dataArray = [['Channel', 'data']];

        Object.keys(channelCodes).forEach(channel => {
            dataArray.push([`${channel}`, channelCodes[channel][`Y${year}`]|| 0]); 
        });

        const data = google.visualization.arrayToDataTable(dataArray);

        const monthFrom = $('#monthFrom option:selected').text();
        const monthTo = $('#monthTo option:selected').text();

         const options = {
             title: `Sales Report from ${monthFrom} to ${monthTo} ${year}`,
             is3D: true,
             pieSliceText: 'value',
             chartArea: {
                 width:'50%',
                 height:'50%',
                 top: 100, 
                 left: 250, 
                 right:0,
                 bottom:0,
             },
             legend:{position: 'right', textStyle: { fontSize: 13}, alignment:'center'},
         };

        // Create a new div for each chart
        const chartDiv = document.createElement('div');
        // chartDiv.id = `2024-chart`;
        chartDiv.className = 'chart';
        document.getElementById('charts_container').appendChild(chartDiv);

        const channelChart = new google.visualization.PieChart(chartDiv);
         
        channelChart.draw(data, options);
        
        // Add resize event listener
        window.addEventListener('resize', () => {
            channelChart.draw(data, options);
        });

        // Get the chart as an image and store it
        const imgUri = channelChart.getImageURI();
        chartImagesToDownload.push(imgUri);
    }
    // Save chart function
    function saveChart() {

        // Retrieve form data from local storage
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
});

</script>



</script>
@endpush
