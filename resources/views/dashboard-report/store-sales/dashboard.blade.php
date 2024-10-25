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
    .chart {
        flex: 1 1 auto;
        width: 100%;
        min-width: 300px; 
        height: 700px; /*pie chart size*/ 
        height: 550px; /* column chart size */
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
        background: rgba(0, 0, 0, 0.4);

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
        max-width: 70%; /* Adjust this value as needed */
        width: 100%;
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
    {{-- <div class="container">
        <h4 style="margin-bottom: 20px;">Generate Charts</h4>
        <form id="chartForm" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="yearFrom">Month Year From:</label>
                        <input type="month" class="form-control" id="yearFrom" name="date_from" >
                    </div>

                    <div class="form-group">
                        <label for="store">Store:</label>
                        <select class="form-control" id="store" name="stores[]" multiple='multiple' >
                            <option value="all" selected>ALL</option>

                            @foreach ($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}}</option>
                            @endforeach

                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="channel">Channel:</label>
                        <select class="form-control" id="channel" name="channels[]"  multiple='multiple' >
                            <option value="all" selected>ALL</option>

                            @foreach ($channels as $channel)
                                <option value="{{$channel->id}}">{{$channel->name}}</option>
                            @endforeach
                
                        </select>
                    </div>
                    
                    
                
                    <div class="form-group">
                        <label for="brand">Brand:</label>
                        <select class="form-control" id="brand" name="brands[]"  multiple='multiple' >
                            <option value="all" selected>ALL</option>

                            @foreach ($brands as $brand)
                                <option>{{$brand->name}}</option>
                            @endforeach
                       
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select class="form-control" id="category" name="categories[]" multiple='multiple'>
                            <option value="all" selected>ALL</option>

                            @foreach ($categories as $category)
                                <option>{{$category->name}}</option>
                            @endforeach
                          
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="yearTo">Month Year To:</label>
                        <input type="month" class="form-control" id="yearTo" name="date_to" >
                    </div>
    
                    <div class="form-group">
                        <label for="storeConcept">Store Concept:</label>
                        <select class="form-control" id="storeConcept" name="concepts[]"  multiple='multiple' >
                            <option value="all" selected>ALL</option>

                            @foreach ($concepts as $concept)
                                <option value="{{$concept->id}}">{{$concept->name}}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mall">Mall:</label>
                        <select class="form-control" id="mall" name="mall" >
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
            </div>
            <div class="row">
                <div  style="display: flex; gap:20px; justify-content:center; margin-top:20px;">
                    <button type="reset" class="btn btn-secondary" style="width:80px;">Reset</button>
                    <button type="submit" class="btn btn-primary" style="width:80px;">Submit</button>
                </div>
            </div>
        </form>
    </div> --}}

    
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
                                <label for="channel">Channel:</label>
                                <select class="form-control select2" id="channel" name="channels[]"  multiple='multiple' required>
                                    <option value="all" selected>ALL</option>
        
                                    @foreach ($channels as $channel)
                                        <option value="{{$channel->id}}">{{$channel->name}}</option>
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
                        </div>

                        <div class="col-md-6">
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

    {{-- <div id="chart_div"></div> --}}

    <div id="charts_container"></div>
    

</div>

@endsection

@push('bottom')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script type="text/javascript">

$(function() {

    // $('select').select2({
    //     width:'100%',
    // });

    $('#chartModal').modal('show');


    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true,
        width:'100%'
    });

    function handleSelect2Event(selector) {
        $(selector).change(function () {
            const options = this.options;
            const isAllSelected = Array.from(options).some(item => item.selected && $(item).text() === "ALL");

            $.each(options, function (i, item) {
                const isNotAll = $(item).text() !== "ALL";

                if (isAllSelected) {
                    // Deselect other options and disable all
                    if (isNotAll) {
                        item.selected = false; 
                    }
                    $(item).prop("disabled", true);
                } else {
                    // Enable all options if "All" is not selected
                    $(item).prop("disabled", false);
                }
            });

            // Trigger a change event to update the UI
            $(this).trigger('change.select2'); // If using Select2
        });
    }

    // Apply the event handler to multiple selectors
    const selectors = ['#store', '#channel', '#brand', '#category', '#storeConcept', '#mall'];
    selectors.forEach(selector => handleSelect2Event(selector));

    // Trigger the change event for initialization
    selectors.forEach(selector => $(selector).change());


    $('#chartForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const yearFrom = $('#yearFrom').val();
        const yearTo = $('#yearTo').val();
        const monthFrom = $('#monthFrom').val();
        const monthTo = $('#monthTo').val();
        const stores = $('#store').val();
        const channels = $('#channel').val();
        const brands = $('#brand').val();
        const categories = $('#category').val();
        const concepts = $('#storeConcept').val();
        const mall = $('#mall').val();
        const sqm  = $('#sqm').val();
        const group = $('#group').val();

        console.log(stores);
        console.log(concepts);

        $('#charts_container').empty();
        $('#formSelectedValues').hide();
        $('#chartModal').modal('hide');
        $('#loading').show();



        $.ajax({
            url: '{{ route("charts") }}', // Adjust this to your route
            method: 'POST', // or 'POST' depending on your backend setup
            data: {
                yearFrom,
                yearTo,
                monthFrom,
                monthTo,
                stores,
                channels,
                brands,
                categories,
                concepts,
                mall,
                sqm,
                group,
            },
            success: function(data) {
                // Populate form fields or do something with the data
                // For example, if you want to fill the store select:
                // console.log(data);

                console.log(data);

                if (data.chartData) {
                    $('#noDataMessage').hide(); // Hide the no data message

                    if(data.multipleChannel){
                        drawChartMultipleChannel(data.chartData, data.years);

                    } else {
                        // Populate charts with the data
                        drawChart(data.chartData, data.years, 'bar');
                        drawChart(data.chartData, data.years, 'line');

                        drawChartWithDynamicMonths(data.chartData, data.years, 'bar');
                        drawChartWithDynamicMonths(data.chartData, data.years, 'line');
                    }


                    // Populate charts with the data
                    // drawChart(data.chartData, data.years, 'bar');
                    // drawChart(data.chartData, data.years, 'line');

                    // drawChartWithDynamicMonths(data.chartData, data.years, 'bar');
                    // drawChartWithDynamicMonths(data.chartData, data.years, 'line');
                } else {

                    $('#noDataMessage p').text('No data was found based on the current inputs. Please ensure all fields are filled out correctly and click "Generate Chart" to try again.');
                    $('#noDataMessage').show(); // Show the no data message
                }

                resetForm();

                $('#loading').hide();



                // drawChart(data.chartData, data.years, 'bar');
                // drawChart(data.chartData, data.years, 'line');

                // drawChartWithDynamicMonths(data.chartData, data.years, 'bar')
                // drawChartWithDynamicMonths(data.chartData, data.years, 'line')

    

                // drawChartWithControlledLastMonth(data.chartData, data.years, 'bar');
                // drawChartWithControlledLastMonth(data.chartData, data.years, 'line');


                
                // Do the same for channels, brands, categories, concepts, malls, etc.
            },
            error: function(xhr) {
                $('#chartModal').modal('show');
                $('#loading').hide();

                console.error('Error fetching data:', xhr);

                // Check if the response contains validation errors
                if (xhr.status === 422) { // 422 Unprocessable Entity status for validation errors
                    const errors = xhr.responseJSON.errors; // Get the errors from the response

                    // Clear previous error messages
                    $('.form-group .text-danger').remove();

                    // Loop through each error and display it
                    $.each(errors, function(key, messages) {
                        // Find the relevant input field and append the error message
                        const input = $(`#${key}`);
                        input.parent().append(`<div class="text-danger">${messages.join(', ')}</div>`);
                    });

                     // Populate fields with submitted values to retain user input
                    $('#yearFrom').val(yearFrom);
                    $('#yearTo').val(yearTo);
                    $('#monthFrom').val(monthFrom);
                    $('#monthTo').val(monthTo);
                    $('#store').val(stores).trigger('change'); // Trigger change for select2
                    $('#channel').val(channels).trigger('change');
                    $('#brand').val(brands).trigger('change');
                    $('#category').val(categories).trigger('change');
                    $('#storeConcept').val(concepts).trigger('change');
                    $('#mall').val(mall).trigger('change');
                    $('#sqm').val(sqm);
                    $('#group').val(group);
                } else {
                    // Handle other types of errors
                    // alert('An error occurred while processing your request. Please try again.');
                }
            }
        });



        // Clear previous rows
        $('#selectedValues tbody').empty();
        
        let values = {
            "Year From": $('#yearFrom option:selected').text(),
            "Year To": $('#yearTo option:selected').text(),
            "Month From": $('#monthFrom option:selected').text(),
            "Month To": $('#monthTo option:selected').text(),
            "Store": $('#store').val().map(function(value) {
                return $('#store option[value="' + value + '"]').text();
            }).join(', '),  
            "Channel": $('#channel').val().map(function(value) {
                return $('#channel option[value="' + value + '"]').text();
            }).join(', '), 
            "Brand": $('#brand').val().join(', '), 
            "Category": $('#category').val().join(', '), 
            "Store Concept": $('#storeConcept').val().map(function(value) {
                return $('#storeConcept option[value="' + value + '"]').text();
            }).join(', '), 
            "Mall": $('#mall').val().join(', '), 
            "Square Meters": $('#sqm').val() ?? 'N/A',
            "Group": $('#group').val() ?? 'N/A'
        };
        
        // Populate the table
        $.each(values, function(field, value) {
            $('#selectedValues tbody').append(`
                <tr>
                    <td>${field}</td>
                    <td>${value || 'ALL'}</td>
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

        // If you're using Select2, refresh the select elements
        $('#store').val('all').trigger('change');
        $('#channel').val('all').trigger('change');
        $('#brand').val('all').trigger('change');
        $('#category').val('all').trigger('change');
        $('#storeConcept').val('all').trigger('change');
        $('#mall').val('all').trigger('change');
    }


    const prevYear = @json($yearData['previousYear']);
    const currYear = @json($yearData['currentYear']);
    const channelCodes = @json($channel_codes);
    const lastThreeDays = @json($lastThreeDaysDates);

    console.group('backendData');
    console.log(prevYear);
    console.log(currYear);
    console.log(channelCodes);
    console.log(lastThreeDays);
    console.groupEnd();

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages': ['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    // google.charts.setOnLoadCallback(drawChart);


    // function renderCharts() {
    //     drawChart(prevYear); 
    //     drawChart(currYear); 
    // }

    // google.charts.setOnLoadCallback(renderCharts);

    // Variable to hold the chart instance
    let chart;

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart2() {
        var data = google.visualization.arrayToDataTable([
            ['Month', '2021', '2022'],
            ['January', 1031230, 1213120],
            ['February', 201230, 1321380],
            ['March', 301230, 312220],
            ['April', 421300, 381240],
            ['May', 100123, 121240],  // Fixed duplicate month
            ['June', 2012310, 180124], // Fixed duplicate month
            ['July', 300123, 321230],  // Fixed duplicate month
            ['August', 4001241, 380123], // Fixed duplicate month
            ['September', 100132, 121230], // Fixed duplicate month
            ['October', 201240, 181230], // Fixed duplicate month
            ['November', 30042, 32124], // Fixed duplicate month
            ['December', 400124, 381240] // Fixed duplicate month
        ]);

        var options = {
            title: 'Channel',
            hAxis: {title: 'Categories'},
            vAxis: {title: 'Values'},
            isStacked: false
        };

        chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);

        // Get the chart as an image
        var imgUri = chart.getImageURI();
        console.log(imgUri); // This is the image URI to use in PDF
    }

    function drawChart3() {
        // Prepare the data for the chart
        const dataArray = [['Month', `${prevYear}`, `${currYear}`]];

        // Get the current month (0-11) and create the months array dynamically
        const currentMonth = new Date().getMonth(); // 0 = January, 11 = December
        const months = [
            "January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"
        ].slice(0, currentMonth + 1); // Get only months up to the current month
        
        months.forEach((month, index) => {
            const prevSales = channelCodes['ECOMM'][prevYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
            const currSales = channelCodes['ECOMM'][currYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
            dataArray.push([month, prevSales, currSales]);
        });

        // Create the DataTable
        const data = google.visualization.arrayToDataTable(dataArray);

        const options = {
            title: 'Sales Comparison',
            // hAxis: { title: 'Months' },
            // vAxis: { title: 'Sales' },
            isStacked: false
        };

        chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);

        // Get the chart as an image
        const imgUri = chart.getImageURI();
        console.log(imgUri); // This is the image URI to use in PDF
    }

    // function drawChart() {
    //     const currentMonth = new Date().getMonth(); // 0 = January, 11 = December
    //     const months = [
    //         "January", "February", "March", "April", "May", "June", 
    //         "July", "August", "September", "October", "November", "December"
    //     ].slice(0, currentMonth + 1); // Get only months up to the current month

    //     // Array to store chart images
    //     const chartImages = [];

    //     // Loop through channel codes
    //     Object.keys(channelCodes).forEach(channel => {
    //         const dataArray = [['Month', `${channel} Previous Year`, `${channel} Current Year`]];

    //         months.forEach((month, index) => {
    //             const prevSales = channelCodes[channel][prevYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
    //             const currSales = channelCodes[channel][currYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
    //             dataArray.push([month, prevSales, currSales]);
    //         });

    //         // Create the DataTable
    //         const data = google.visualization.arrayToDataTable(dataArray);

    //         const options = {
    //             title: `${channel} Sales Comparison`,
    //             hAxis: { title: 'Months' },
    //             vAxis: { title: 'Sales' },
    //             isStacked: false
    //         };

    //         // Create a new div for each chart
    //         const chartDiv = document.createElement('div');
    //         chartDiv.id = `${channel}-chart`;
    //         chartDiv.className = 'chart';
    //         document.getElementById('charts_container').appendChild(chartDiv);

    //         chart = new google.visualization.ColumnChart(chartDiv);
    //         chart.draw(data, options);

       
    //         // Get the chart as an image and store it
    //         const imgUri = chart.getImageURI();
    //         chartImages.push(imgUri); // Store image URI for PDF generation
    //     });

    //     // Now you can call a function to generate the PDF with the chart images
    //     // generatePDF(chartImages);
    // }


    // Set up the button click event
//     document.getElementById('saveChartBtn').addEventListener('click', saveChart);

//     function saveChart() {
//     if (!chart) {
//         console.error('Chart is not initialized.');
//         return;
//     }

//     var imgUri = chart.getImageURI();

//     fetch('/admin/save_chart', {
//         method: 'POST',
//         body: JSON.stringify({ image: imgUri }),
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': '{{ csrf_token() }}'
//         }
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error('Network response was not ok ' + response.statusText);
//         }
//         return response.blob(); // Change to blob for downloading
//     })
//     .then(blob => {
//         const url = window.URL.createObjectURL(blob);
//         const a = document.createElement('a');
//         a.href = url;
//         a.download = 'document.pdf'; // Specify the filename
//         document.body.appendChild(a);
//         a.click();
//         a.remove();
//         window.URL.revokeObjectURL(url); // Clean up
//     })
//     .catch(error => console.error('Error:', error));
// }

    // Set up the button click event
   
    document.getElementById('saveChartBtn').addEventListener('click', saveChart);

    const charts = {}; // Object to store chart instances
    const chartImages = [];

    //monthly comparison per channel
    // function drawChart() {
    //     const currentMonth = new Date().getMonth(); // 0 = January, 11 = December
    //     const months = [
    //         "January", "February", "March", "April", "May", "June", 
    //         "July", "August", "September", "October", "November", "December"
    //     ].slice(0, currentMonth + 1); // Get only months up to the current month

    //     // Loop through channel codes
    //     Object.keys(channelCodes).forEach(channel => {
    //         const dataArray = [['Month', `${prevYear}`, `${currYear}`]];

    //         months.forEach((month, index) => {
    //             const prevSales = channelCodes[channel][prevYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
    //             const currSales = channelCodes[channel][currYear]['months'][`M${String(index + 1).padStart(2, '0')}`]?.sum_of_net_sales || 0;
                
    //             dataArray.push([month, prevSales, currSales]);
    //         });

    //         // Create the DataTable
    //         const data = google.visualization.arrayToDataTable(dataArray);

    //         //bar chart and line chart option 
    //         const options = {
    //             // colors: ['#76A7FA', '#33FF57', '#3357FF'], 
                
    //             title: `${channel} Sales Report`,
    //             hAxis: { title: 'Months' },
    //             vAxis: { title: 'Sales' },
    //             isStacked: false,
    //             chartArea: {
    //                 width:'100%',
    //                 height:'100%',
    //                 top: 100, 
    //                 left: 150, 
    //                 right:50,
    //                 bottom:50,
    //             },
    //             legend:{position: 'top', textStyle: { fontSize: 13}, alignment:'end'},
    //             annotations: {
    //                 textStyle: {
    //                     color: '#000', 
    //                     fontSize: 12 
    //                 }
    //             }
    //         };

    //         //pie chart option
    //         // const options = {
    //         //     title: `${channel} Sales Report`,
    //         //     is3D: true,
    //         //     pieSliceText: 'value',
    //         //     chartArea: {
    //         //         width:'50%',
    //         //         height:'50%',
    //         //         top: 100, 
    //         //         left: 250, 
    //         //         right:0,
    //         //         bottom:0,
    //         //     },
    //         //     legend:{position: 'right', textStyle: { fontSize: 13}, alignment:'center'},
    //         // };


    //         // Create a new div for each chart
    //         const chartDiv = document.createElement('div');
    //         chartDiv.id = `${channel}-chart`;
    //         chartDiv.className = 'chart';
    //         document.getElementById('charts_container').appendChild(chartDiv);

    //         // Create and store the chart instance
    //         const channelChart = new google.visualization.LineChart(chartDiv);
    //         channelChart.draw(data, options);
            
    //         // Store the chart instance for later use
    //         charts[channel] = channelChart;

    //         // Add an event listener for window resizing
    //         window.addEventListener('resize', () => {
    //             // Redraw the chart
    //             channelChart.draw(data, options); // You might need to access the chart instances stored in `charts`
    //         });

    //         // Get the chart as an image and store it
    //         const imgUri = channelChart.getImageURI();
    //         chartImages.push(imgUri); // Store image URI for PDF generation
    //     });


     

    //     // Now you can call a function to generate the PDF with the chart images
    //     // generatePDF(chartImages);
    // }

    const monthNames = [
        'January', 'February', 'March', 'April',
        'May', 'June', 'July', 'August',
        'September', 'October', 'November', 'December'
    ];

    function drawChart(months, years, type) {
        const dataArray = [['Month', ...years]];

        console.log(months);
        
        // Assuming channelCodes is accessible
        Object.entries(months).forEach(([monthKey, monthData]) => {
            const monthIndex = parseInt(monthKey.replace('M', '')) - 1; // Adjust month index
            const rowData = [`${monthNames[monthIndex]}`]; // Start row with month name

              // Loop through years to dynamically add sales data
            years.forEach(year => {
                rowData.push(monthData[`Y${year}`] || 0); // Add sales for the year, default to 0 if undefined
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
        // chartDiv.id = `2024-chart`;
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
        chartImages.push(imgUri);
    }

    function drawChartWithDynamicMonths(months, years, type) {
        const dataArray = [['Month', ...years]];

        console.log(months);
        
        // Find the latest year from the years array
        const latestYear = Math.max(...years.map(year => parseInt(year)));

        // Filter months for the latest year
        const monthsForLatestYear = Object.entries(months).filter(([monthKey, monthData]) => {
            const yearKey = monthData[`Y${latestYear}`] !== undefined; // Check if data exists for the latest year
            return yearKey; // Include only those months which have data for the latest year
        });
        
        // Determine the last month available in the latest year
        const lastMonthKey = Math.max(...monthsForLatestYear.map(([monthKey]) => parseInt(monthKey.replace('M', ''))));

        // Filter to only include months up to the last month of the latest year
        const filteredMonths = monthsForLatestYear.filter(([monthKey]) => {
            const monthIndex = parseInt(monthKey.replace('M', ''));
            return monthIndex <= lastMonthKey;
        });

        // Populate dataArray with filtered months
        filteredMonths.forEach(([monthKey, monthData]) => {
            const monthIndex = parseInt(monthKey.replace('M', '')) - 1; // Adjust month index
            const rowData = [`${monthNames[monthIndex]}`]; // Start row with month name

            // Loop through years to dynamically add sales data
            years.forEach(year => {
                rowData.push(monthData[`Y${year}`] || 0); // Add sales for the year, default to 0 if undefined
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
        chartDiv.id = `2024-chart`;
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
        chartImages.push(imgUri);
    }

    function drawChartMultipleChannel(channelCodes, years) {

        const dataArray = [['Channel', 'data']];

        Object.keys(channelCodes).forEach(channel => {

            years.forEach(year => {
                dataArray.push([`${channel} ${year}`, channelCodes[channel][`Y${year}`]|| 0]); 
            });

        });

        console.log(dataArray);

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
        chartImages.push(imgUri);
    }



    function drawChart4() {

        const dataArray = [['Channel', `Data`]];
            
    

        // Loop through channel codes
        Object.keys(channelCodes).forEach(channel => {

            if(channel !== 'TOTAL'){
                const currSales = channelCodes[channel][currYear]['months'][`TOTAL`]?.sum_of_net_sales || 0;
                
                dataArray.push([channel, currSales]);
            }


        });

            // Create the DataTable
            const data = google.visualization.arrayToDataTable(dataArray);

            //pie chart option
            const options = {
                title: `2024 Channels Sales Report`,
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
            chartDiv.id = `${channel}-chart`;
            chartDiv.className = 'chart';
            document.getElementById('charts_container').appendChild(chartDiv);

            // Create and store the chart instance
            const channelChart = new google.visualization.PieChart(chartDiv);
            channelChart.draw(data, options);
            
            // Store the chart instance for later use
            charts[channel] = channelChart;

            // Add an event listener for window resizing
            window.addEventListener('resize', () => {
                // Redraw the chart
                channelChart.draw(data, options); // You might need to access the chart instances stored in `charts`
            });

            // Get the chart as an image and store it
            const imgUri = channelChart.getImageURI();
            chartImages.push(imgUri); // Store image URI for PDF generation
      


     

        // Now you can call a function to generate the PDF with the chart images
        // generatePDF(chartImages);
    }

    // Save chart function
    function saveChart(event) {
        event.preventDefault();
        console.log('Button clicked!');

        const chartImages = []; // Array to hold all chart images
        const jsonChannels = @json($channel_codes); // Fetch channel codes from backend
        const channelCodes = Object.keys(jsonChannels); // Extract channel keys

        // Loop through each channel and get its chart image
        channelCodes.forEach(channel => {
            if (charts[channel]) { // Check if the chart instance exists
                const imgUri = charts[channel].getImageURI(); // Use the existing chart instance
                chartImages.push(imgUri); // Store the image URI
            } else {
                console.warn(`Chart instance for ${channel} not found.`);
            }
        });

        fetch('/admin/save_chart', {
            method: 'POST',
            body: JSON.stringify({ images: chartImages }), // Send the array of images
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
            a.download = 'document.pdf'; // Specify the filename
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url); // Clean up
        })
        .catch(error => console.error('Error:', error));
    }
});



</script>



</script>
@endpush
