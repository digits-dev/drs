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

    </style>

@endpush

@section('content')

<div class="box">

    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <a href="{{ route('warehouse-inventory.upload-view') }}" id="upload-inventory" class="btn btn-success btn-sm">
                <i class="fa fa-upload"></i> Upload Inventory
            </a>

            <a href="javascript:showInventoryReportExport()" id="export-inventory" class="btn btn-primary btn-sm">
                <i class="fa fa-download"></i> Export Inventory
            </a>

            <a href="javascript:showFilter()" id="search-filter" class="btn btn-info btn-sm pull-right">
                <i class="fa fa-search"></i> Search Filter
            </a>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered" id="inventory-report-table" style="width:100%">
                <thead>
                    <tr>
                    <th>Reference #</th>
                    <th>System</th>
                    <th>Org</th>
                    <th>Report Type</th>
                    <th>Channel</th>
                    <th>Customer Location</th>
                    <th>Concept</th>
                    <th>Inventory Date</th>
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
                    <td>{{ $row->inventory_date }}</td>

                    <td>

                        @if(CRUDBooster::isRead())
                        <a class='btn-detail' title="Detail" href='{{CRUDBooster::mainpath("detail/$row->id")}}'><i class='fa fa-eye'></i></a>
                        @endif
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class='modal fade' tabindex='-1' role='dialog' id='modal-inventory-export'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-download'></i> Export inventory</h4>
            </div>

            <form method='post' target='_blank' action="{{ route('store-inventory.export') }}" autocomplete="off">
            <input type='hidden' name='_token' value="{{ csrf_token() }}">
            {{ CRUDBooster::getUrlParameters() }}
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
            <form method='post' target='_blank' action="#" autocomplete="off">

            <input type='hidden' name='_token' value="{{ csrf_token() }}">

            <div class="modal-body">
                <div class="row">

                    <div class='col-sm-6'>
                        Date From
                        <div class="form-group">
                            <div class='input-group date' id='datefrom'>
                                <input type='text' name="datefrom" class="form-control" />
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
                                <input type='text' name="dateto" class="form-control" />
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
                        <select name="channel" id="channel" class="form-control channel" title="Channel">
                            <option value="">Please select channel</option>
                            @foreach ($channels as $channel)
                                <option value="{{ $channel->channel_code }}">{{ $channel->channel_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        System
                        <div class="form-group">
                        <select name="system" id="system" class="form-control system" title="system">
                            <option value="">Please select system</option>
                            @foreach ($systems as $system)
                                <option value="{{ $system->system_name }}">{{ $system->system_name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class='btn btn-default' type='button' data-dismiss='modal'>Close</button>
                <button class='btn btn-primary btn-submit' type='submit'>Search</button>

            </div>
            </form>
        </div>
    </div>
</div>

<div class='modal fade' tabindex='-1' role='dialog' id='modal-inventory-export'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button class='close' aria-label='Close' type='button' data-dismiss='modal'>
                    <span aria-hidden='true'>×</span></button>
                <h4 class='modal-title'><i class='fa fa-download'></i> Export Orders</h4>
            </div>

            <form method='post' target='_blank' action="{{ CRUDBooster::mainpath("export")}}">
            <input type='hidden' name='_token' value="{{ csrf_token()}}">
            {{ CRUDBooster::getUrlParameters()}}
            <div class='modal-body'>
                <div class='form-group'>
                    <label>File Name</label>
                    <input type='text' name='filename' class='form-control' required value="Export {{ CRUDBooster::getCurrentModule()->name }} - {{ date('Y-m-d H:i:s')}}"/>
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
@endsection

@push('bottom')
    <script>
        $(document).ready(function(){

            $('.search').on("click", function() {

            });

            $("#inventory-report-table").dataTable({
                responsive: true,
            });
        });

        function showFilter() {
            $('#modal-filter').modal('show');
        }

        function showInventoryReportExport() {
            $('#modal-inventory-export').modal('show');
        }
    </script>
@endpush
