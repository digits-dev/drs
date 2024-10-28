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
                                    <select class="form-control select2" id="type" name="types[]" multiple='multiple'>
                                        <option value="all" selected>ALL</option>
                                        <option value="line" >LINE GRAPH </option>
                                        <option value="bar" >BAR GRAPH</option>
                                        <option value="pie" >PIE CHART</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="yearFrom">Year From:</label>
                                    <select class="form-control" id="yearFrom" name="year_from">
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
                                    <select class="form-control" id="monthFrom" name="month_from" >
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
                                    <select class="form-control select2" id="store" name="stores[]" multiple='multiple'>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->name}}</option>
                                        @endforeach
            
                                    </select>
                                </div>
                                
                           
                            
                                <div class="form-group">
                                    <label for="brand">Brand:</label>
                                    <select class="form-control select2" id="brand" name="brands[]"  multiple='multiple'>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($brands as $brand)
                                            <option>{{$brand->name}}</option>
                                        @endforeach
                                    
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label for="category">Category:</label>
                                    <select class="form-control select2" id="category" name="categories[]" multiple='multiple'>
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
                                    <select class="form-control select2" id="channel" name="channels[]"  multiple='multiple'>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($channels as $channel)
                                            <option value="{{$channel->id}}">{{$channel->name}}</option>
                                        @endforeach
                            
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="yearTo">Year To:</label>
                                    <select class="form-control" id="yearTo" name="year_to">
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
                                    <select class="form-control" id="monthTo" name="month_to">
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
                                    <select class="form-control select2" id="storeConcept" name="concepts[]"  multiple='multiple'>
                                        <option value="all" selected>ALL</option>
            
                                        @foreach ($concepts as $concept)
                                            <option value="{{$concept->id}}">{{$concept->name}}</option>
                                        @endforeach
            
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label for="mall">Mall:</label>
                                    <select class="form-control select2" id="mall" name="malls[]" multiple='multiple'>
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
 
    
</div>

@endsection

@push('bottom')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">

$(function() {
    // Initialize Select2
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
    
});







</script>



</script>
@endpush
