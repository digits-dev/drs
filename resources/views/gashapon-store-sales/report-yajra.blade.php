@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">
    @livewireStyles
    <style type="text/css">

        table{
            border-collapse: collapse;
        }

        .noselect {
        -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Old versions of Firefox */
                -ms-user-select: none; /* Internet Explorer/Edge */
                    user-select: none; /* Non-prefixed version, currently supported by Chrome, Edge, Opera and Firefox */
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }

        label.error {
            color: red;
        }
        .search-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 8px;
        }
        .search-bar {
        padding: 5px 10px;
        width: 400px;
        outline: none;
        margin-right: 8px;
        border-radius: 5px;
        border: 1px solid #919191;
        }

        .progress-bar{
            border-radius: 40px !important;
            -webkit-box-shadow: none !important;
            -moz-box-shadow: none !important;
            box-shadow: none !important;
        }

        .progress{
            border-radius: 40px !important;
            background-color: white !important;

            /* Changes below */
            -webkit-box-shadow: inset 0 0 0 2px #337AB7 !important;
            -moz-box-shadow: inset 0 0 0 2px #337AB7 !important;
            box-shadow: inset 0 0 0 2px #337AB7 !important;
            border: none;
        }

        .marquee {
            height: 25px;
            width: 420px;

            overflow: hidden;
            position: relative;
        }

        .marquee div {
            display: block;
            width: 200%;
            height: 30px;

            position: absolute;
            overflow: hidden;

            animation: marquee 5s linear infinite;
        }

        .marquee span {
            float: left;
            width: 50%;
        }

        @keyframes marquee {
            0% { left: 0; }
            100% { left: -100%; }
        }

        /* Custom loading spinner */
        .spinner {
            display: none;
            margin: 0 auto;
            width: 40px;
            height: 40px;
            border: 6px solid #ccc;
            border-top: 6px solid #1d72b8;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .span_processing{
            font-size: 1em;
            font-weight: bold;
        }

        .dataTables_wrapper .dataTables_processing {
            height: 100px !important;
            opacity: 5 !important;
        }

    </style>

@endpush

@section('content')
@livewireScripts
<div class="box">

    <div class="panel panel-default">
        <div class="panel-heading clearfix">

            <a href="javascript:showSalesReportExport()" id="export-sales" class="btn btn-primary btn-sm">
                <i class="fa fa-download"></i> Export Sales
            </a>
            <a href="javascript:showSalesFilteredReportExport()" style="display: none" id="export-filtered-sales" class="btn btn-primary btn-sm">
                <i class="fa fa-download"></i> Export Filtered Sales
            </a>
            @include('partial.progress-bar')
            @include('partial.download-btn')
            <a href="javascript:showFilter()" id="search-filter" class="btn btn-info btn-sm pull-right">
                <i class="fa fa-filter" aria-hidden="true"></i> Search Filter
            </a>
        </div>
        <div class="panel-body">
            {{-- <form action="{{ route('store-sales.filter') }}">
                <div class="search-container">
                    <input
                        class="search-bar"
                        autofocus
                        type="text"
                        name="search"
                        placeholder="Search"
                        value="{{ $searchval }}"
                    />
                    <div class="search-btn-container">
                        <button class="btn btn-info btn-sm pull-right" type="submit">
                            <i class="fa fa-search"></i>  Search
                        </button>
                    </div>
                </div>
            </form> --}}
           
            <table class="table table-striped table-bordered" id="sales-report-table" style="width:100%">
                <thead>
                    <tr>
                    <th>Reference #</th>
                    <th>System</th>
                    <th>Org</th>
                    <th>Report Type</th>
                    <th>Channel</th>
                    <th>Customer Location</th>
                    <th>Concept</th>
                    <th>Receipt #</th>
                    <th>Sales Date</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                {{-- @foreach($rows as $row)
                    <tr>
                    <td>{{ $row->reference_number }}</td>
                    <td>{{ $row->system_name }}</td>
                    <td>{{ $row->organization_name }}</td>
                    <td>{{ $row->report_type }}</td>
                    <td>{{ $row->channel_code }}</td>
                    <td>{{ $row->customer_location }}</td>
                    <td>{{ $row->store_concept_name }}</td>
                    <td>{{ $row->receipt_number }}</td>
                    <td>{{ $row->sales_date }}</td>

                    <td>

                        @if(CRUDBooster::isRead())
                        <a class='btn-detail' title="Detail" href='{{CRUDBooster::mainpath("detail/$row->id")}}'><i class='fa fa-eye'></i></a>
                        @endif
                    </td>
                    </tr>
                @endforeach --}}
                </tbody>
            </table>
           {{-- <div style="display: flex; justify-content: space-between; align-items: center;">
                <p>Showing {{ $result->firstItem() ?? 0 }} to {{ $result->lastItem() ?? 0 }} of {{ $result->total() }} items.</p>
                {{ $result->links() }}
            </div> --}}
        </div>
    </div>

</div>

<div class='modal fade' tabindex='-1' role='dialog' id='modal-sales-export'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-download'></i> Export Sales</h4>
            </div>

            <form method='post' target='_blank' action="{{ route('store-sales.export') }}" autocomplete="off">
            <input type='hidden' name='_token' value="{{ csrf_token() }}">
            {!! CRUDBooster::getUrlParameters() !!}
            @if(!empty($filters))
                @foreach ($filters as $keyfilter => $valuefilter )
                    <input type="hidden" name="{{ $keyfilter }}" value="{{ $valuefilter }}">
                @endforeach

            @endif
            <div class='modal-body'>
                <div class='form-group'>
                    <label>File Name</label>
                    <input type='text' name='filename' class='form-control' required value='Export {{ CRUDBooster::getCurrentModule()->name }} - {{ date('Y-m-d H:i:s') }}'/>
                </div>
            </div>
            <div class='modal-footer' align='right'>
                <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                <button class='btn btn-primary btn-submit' type='submit'>Submit</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-filter">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-search'></i> Filter</h4>
            </div>
            <form method='post' autocomplete="off">

            <input type='hidden' name='_token' value="{{ csrf_token() }}">

            <div class="modal-body">
                <div class="row">

                    <div class='col-sm-6'>
                        Date From
                        <div class="form-group">
                            <div class='input-group date' id='datefrom'>
                                <input type='text' name="datefrom" class="form-control date_picker" id="start_date" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>

                    </div>

                    <div class='col-sm-6'>
                        Date To
                        <div class="form-group">
                            <div class='input-group date' id='dateto'>
                                <input type='text' name="dateto" class="form-control date_picker" id="end_date" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        Channel
                        <div class="form-group">
                        <select name="channels_id" id="channel" class="form-control channel" title="Channel">
                            <option value="">Please select channel</option>
                            @foreach ($channels as $channel)
                                <option data-id="{{ $channel->id }}" value="{{ $channel->id }}">{{ $channel->channel_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        Concept
                        <div class="form-group">
                        <select name="concepts_id" id="concept" class="form-control concept" title="Concept" disabled>
                            <option value="" selected disabled>Please select channel first</option>
                            {{-- @foreach ($concepts as $concept)
                                <option value="{{ $concept->concept_name }}">{{ $concept->concept_name }}</option>
                            @endforeach --}}
                        </select>
                        </div>
                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">
                        Receipt #
                        <div class="form-group">
                        <input type="text" class="form-control" name="receipt_number" id="receipt_number">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                <button class='btn btn-primary btn-submit' type='submit' id="filter">Search</button>

            </div>
            </form>
        </div>
    </div>
</div>

<div class='modal fade' tabindex='-1' role='dialog' id='modal-order-export'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-download'></i> Export Orders</h4>

            </div>

            {{-- <form method='post' action="{{ CRUDBooster::mainpath("export")}}" id="exportForm"> --}}
        <form method='post' target='_blank' action="{{ CRUDBooster::mainpath("export")}}">
            <input type='hidden' name='_token' value="{{ csrf_token()}}">
            {!! CRUDBooster::getUrlParameters() !!}
            <div class='modal-body'>
                <div class='form-group'>
                    <label>File Name</label>
                    <input type='text' name='filename' class='form-control' required value="Export {{ CRUDBooster::getCurrentModule()->name }} - {{ date('Y-m-d H:i:s')}}"/>
                </div>
            </div>
            <div class='modal-footer' align='right'>
                <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                {{-- <button class='btn btn-primary btn-submit' type='submit' id="exportBtn">Submit</button> --}}
                <button class='btn btn-primary btn-submit' type='submit'>Submit</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class='modal fade' tabindex='-1' role='dialog' id='export-filtered-report'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-download'></i> Export Orders</h4>
            </div>

            {{-- <form method='post' target='_blank' id="exportForm"> --}}
        <form method='post' target='_blank' action="{{ route('gashapon-store-sales.export') }}">
            <input type='hidden' name='_token' value="{{ csrf_token()}}">
            <input type='hidden' name='receipt_number' id="receiptNumber">
            <input type='hidden' name='channels_id' id="channelId">
            <input type='hidden' name='datefrom' id="date_from">
            <input type='hidden' name='dateto' id="date_to">
            <input type='hidden' name='concepts_id' id="conceptId">
            {!! CRUDBooster::getUrlParameters() !!}
            <div class='modal-body'>
                <div class='form-group'>
                    <label>File Name</label>
                    <input type='text' name='filename' class='form-control' required value="Export {{ CRUDBooster::getCurrentModule()->name }} - {{ date('Y-m-d H:i:s')}}"/>
                </div>
            </div>
            <div class='modal-footer' align='right'>
                <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                {{-- <button class='btn btn-primary btn-submit' type='submit' id="exportBtn">Submit</button> --}}
                <button class='btn btn-primary btn-submit' type='submit'>Submit</button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@push('bottom')
    <script>

        $(document).ready(function(){
            $('.search').on("click", function() {
            });
            // $("#sales-report-table").dataTable({
            //     responsive: true,
            //     "bPaginate": false,
            //     "bInfo": false,
            //     "bFilter": false,
            // });
            load_data();

            function load_data(start_date = '', end_date = '', channel_id = '', concept_id = '', receipt_number = '') {
                $('#sales-report-table').DataTable({
                    processing: true,
                    serverSide: true,
                    language: {
                        processing: '<div class="spinner" id="spinner"></div> <span class="span_processing">Processing... Please wait...</span>' // Custom processing text with spinner
                    },
                    ajax: {
                        url: '{{ route("gashapon-store-sales.filter") }}',
                        data: {
                            datefrom: start_date,
                            dateto: end_date
                        },
                        beforeSend: function() {
                            $('#spinner').show(); // Show spinner before the request
                        },
                        complete: function() {
                            $('#spinner').hide(); // Hide spinner after the request completes
                        }
                    },
                    columns: [
                        {data: 'reference_number', name: 'reference_number'},
                        {data: 'system_name', name: 'systems.system_name'},
                        {data: 'organization_name', name: 'organizations.organization_name'},
                        {data: 'report_type', name: 'report_types.report_type'},
                        {data: 'channel_code', name: 'channels.channel_code'},
                        {data: 'customer_location', name: 'customers.customer_location'},
                        {data: 'concept_name', name: 'concepts.concept_name'},
                        {data: 'receipt_number', name: 'receipt_number'},
                        {data: 'sales_date', name: 'sales_date'},
                        {data: 'action'}
                    ],
                    order: [[0, 'desc']],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100]
                });
            }

            $('#filter').click(function(e) {
                e.preventDefault();
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var channel_id = $('#channel').val();
                var concept_id = $('#concept').val();
                var receipt_number = $('#receipt_number').val();
                $('#date_from').val(start_date);
                $('#date_to').val(end_date);
                $('#channelId').val(channel_id);
                $('#conceptId').val(concept_id);
                $('#receiptNumber').val(receipt_number);
                $('#modal-filter').modal('hide');
                $('#export-sales').hide();
                $('#export-filtered-sales').show();
                if(start_date != '' && end_date !='') {
                    $('#sales-report-table').DataTable().destroy(); 
                    load_data(start_date, end_date, channel_id, concept_id, receipt_number);
                } else {
                    alert('Both Date is required');
                }
            });

        });

        $('.date_picker').datepicker({
                    format: "yyyy-mm-dd",
        });

        function showFilter() {
            $('#modal-filter').modal('show');
        }

        function showSalesReportExport() {
            $('#modal-order-export').modal('show');
        }

        function showSalesFilteredReportExport() {
            $('#export-filtered-report').modal('show');
        }

        $('#channel').change(function() {
            var channelId = $(this).find(':selected').data('id');
            $.ajax({
                url: "{{ route('gashapon-store-concepts') }}",
            type: "POST",
            data: {
                'channel': channelId,
                },
            success: function(result)
            {
                let i;
                let showData = [];

                showData[0] = "<option selected disabled value=''>Please select Concept</option>";
                for (i = 0; i < result.length; ++i) {
                        showData[i+1] = "<option value='"+result[i].id+"'>"+result[i].concept_name+"</option>";
                }
                $('#concept').removeAttr("disabled")
                $('#concept').find('option').remove();
                jQuery("#concept").html(showData);
            }
            })
        })

        $('#exportBtn').click(function(e) {
            e.preventDefault();
            $('#modal-order-export').modal('hide');
            $('#export-sales').hide();
            $('.download-file').hide();
            $('#export-sales').hide();
            $('.progress-div').show();
            $.ajax({
                url: '{{ route("gashapon-store-sales.export") }}',
                type: 'POST',
                data: $('#exportForm').serialize(),
                success: function(result){
                    var return_id = '';
                    var fileName = '';
                    if(result.batch_id){
                        return_id = result.batch_id;
                    }
                    if(result.folder){
                        fileName = result.folder;
                    }
                    progressBar(return_id, fileName);

                }
            });
        });


    </script>
@endpush