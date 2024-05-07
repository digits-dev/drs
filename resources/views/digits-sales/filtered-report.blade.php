@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

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

    </style>

@endpush

@section('content')

<div class="box">

    <div class="panel panel-default">
        <div class="panel-heading clearfix">
   
            <a href="javascript:showSalesReportExport()" id="export-sales" class="btn btn-primary btn-sm pull-right">
                <i class="fa fa-download"></i> Export Sales
            </a>

            <div class="progress-div" style="display: none">
                <div class="marquee">
                    <div>
                        <span class="text-danger">Please wait while generating file...</span>
                        <span class="text-danger">Please don't leave or reload page...</span>
                    </div>
                </div>
                <div class="progress-bar progress-bar-striped bg-info" id="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            {{-- @if(file_exists(storage_path("app/" . session()->get("folderSalesDigits") . "/ExportDigitsSales.csv"))) --}}
                <div class="download-file" style="display: none">
                    <span style="font-size: bold">Click here to download: </span><a id="downloadBtn"> Generated file</a> 
                </div>
            {{-- @endif --}}
            {{-- <div class="page-reload-msg" style="display: none">
                <span>Please wait you can download file after page reload...</span>
            </div> --}}
  
        </div>
        <div class="panel-body">
            <form action="{{ route('digits-sales.filter') }}">
                <div class="search-container">
                    <input type='hidden' name='receipt_number' value="{{ $receipt_number }}">
                    <input type='hidden' name='channels_id' value="{{ $channels_id }}">
                    <input type='hidden' name='datefrom' value="{{ $datefrom }}">
                    <input type='hidden' name='dateto' value="{{ $dateto }}">
                    <input type='hidden' name='concepts_id' value="{{ $concepts_id }}">
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
            </form>
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
                @foreach($result as $row)
                    <tr>
                    <td>{{ $row->reference_number }}</td>
                    <td>{{ $row->system_name }}</td>
                    <td>{{ $row->organization_name }}</td>
                    <td>{{ $row->report_type }}</td>
                    <td>{{ $row->channel_name }}</td>
                    <td>{{ $row->customer_location }}</td>
                    <td>{{ $row->store_concept_name }}</td>
                    <td>{{ $row->receipt_number }}</td>
                    <td>{{ $row->sales_date }}</td>
                    <td>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <p>Showing {{ $result->firstItem() ?? 0 }} to {{ $result->lastItem() ?? 0 }} of {{ $result->total() }} items.</p>      
                {{ $result->links() }}
            </div>
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

            <form method='post' action="{{ route('digits-sales.export') }}" autocomplete="off">
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

<div class='modal fade' tabindex='-1' role='dialog' id='modal-order-export'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-download'></i> Export Orders</h4>
            </div>

            <form method='post' >
            <input type='hidden' name='_token' value="{{ csrf_token()}}" id="exportForm">
            <input type='hidden' name='receipt_number' value="{{ $receipt_number }}">
            <input type='hidden' name='channels_id' value="{{ $channels_id }}">
            <input type='hidden' name='datefrom' value="{{ $datefrom }}">
            <input type='hidden' name='dateto' value="{{ $dateto }}">
            <input type='hidden' name='concepts_id' value="{{ $concepts_id }}">
            {!! CRUDBooster::getUrlParameters() !!}
            <div class='modal-body'>
                <div class='form-group'>
                    <label>File Name</label>
                    <input type='text' name='filename' class='form-control' required value="Export {{ CRUDBooster::getCurrentModule()->name }} - {{ date('Y-m-d H:i:s')}}"/>
                </div>
            </div>
            <div class='modal-footer' align='right'>
                <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                <button class='btn btn-primary btn-submit' type='submit' id="exportBtn">Submit</button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@push('bottom')
    <script>
        $(document).ready(function(){
            var isSessionBatch = '{{session()->get("lastDigitSalesBatchId")}}';
            if(isSessionBatch){
                progressBar();
            }
            $('.search').on("click", function() {
            });
            $("#sales-report-table").dataTable({
                responsive: true,
                "bPaginate": false,
                "bInfo": false,
                "bFilter": false,
            });
        });

        function showSalesReportExport() {
            $('#modal-order-export').modal('show');
        }

        $('#exportBtn').click(function(e) {
            e.preventDefault();
            $('#modal-order-export').modal('hide');
            $('#export-sales').hide();
            $('.download-file').hide();
            $('#export-sales').hide();
            $('.progress-div').show();
            $.ajax({
                url: '{{ route("digits-sales.export") }}',
                type: 'POST',
                data: $('#exportForm').serialize(),
                success: function(result){
                    if (result.status === 'success') {
                        var return_id = '';
                        var fileName = '';
                        if(result.batch_id){
                            return_id = result.batch_id;
                        }
                        if(result.folder){
                            fileName = result.folder;
                        }
                        progressBar(return_id, fileName);
                    
                    } else if (result.status === 'error') {
                        swal({
                            type: result.status,
                            title: result.msg,
                        });
                        e.preventDefault();
                        location.reload();
                        return false;
                    }
                }
            });
        });

        function progressBar(data, file){
            var myInterval = setInterval(function () {
                $.ajax({
                    url: '{{ route("digits-sales-progress-export") }}',
                    type: 'POST',
                    data: {
                        batchId: data ? data : '{{session()->get("lastDigitSalesBatchId")}}'
                    },
                    success: function(response){
                        let totalJobs = parseInt(response.total_jobs);
                        let pendingJobs = parseInt(response.pending_jobs);
                        let completeJobs = totalJobs - pendingJobs;
                        let progressPercentage = 0;
                        if(pendingJobs == 0){
                            progressPercentage = 100;
                        }else{
                            progressPercentage = parseInt(completeJobs/totalJobs*100).toFixed(0);
                        }
                        
                        $('#export-sales').hide();
                        $('.progress-div').show();
                        $('#progress-bar').text(`${progressPercentage}%`);
                        $('#progress-bar').attr('style',`width:${progressPercentage}%`);
                        $('#progress-bar').attr('aria-valuenow',progressPercentage);
                        
                        if(parseInt(progressPercentage) >= 100){
                            const url_download = '{{CRUDBooster::adminpath("digits_sales/download/")}}';
                            const folder = file ? file : '{{session()->get("folderSalesDigits")}}';
                            $('#downloadBtn').attr('href',url_download+'/'+folder);
                            $('.progress-div').hide();
                            $('#export-sales').show();
                            $('#page-reload-msg').show();
                            downloadBtn(file);
                            $('.download-file').show();
                            clearInterval(myInterval);
                        }
                        
                        if(response.finished_at){
                            $('.progress-div').hide();
                            $('#export-sales').show();
                            $('#page-reload-msg').show();
                            downloadBtn(file);
                            $('.download-file').show();
                            clearInterval(myInterval);
                        }
                    }
                });
            },2000); 
        }

        function  downloadBtn(data){
            const url_download = '{{CRUDBooster::adminpath("digits_sales/download/")}}';
            const folder = data ? data : '{{session()->get("folderSalesDigits")}}';
            $('#downloadBtn').attr('href',url_download+'/'+folder);
        }
    </script>
@endpush
