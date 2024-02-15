@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css"
        integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">
@endpush

@section('content')
    <p class="noprint">
        <a title='Return' href="{{ CRUDBooster::mainPath() }}">
            <i class='fa fa-chevron-circle-left '></i> &nbsp;
            {{ trans('crudbooster.form_back_to_list', ['module' => CRUDBooster::getCurrentModule()->name]) }}
        </a>
    </p>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-eye"></i><strong> Details {{ CRUDBooster::getCurrentModule()->name }}</strong>
        </div>
        <div class="panel-body">
            <div class="row">
                <label class="col-md-2 label-control">Batch</label>
                <div class="col-md-4">
                    <p>{{ $item->batch }}</p>
                </div>
                <label class="col-md-2 label-control">File Name</label>
                <div class="col-md-4">
                    <p>{{ $item->file_name }}</p>
                </div>
            </div>
            <div class="row">
                <label class="col-md-2 label-control">Created By</label>
                <div class="col-md-4">
                    <p>{{ $item->name }}</p>
                </div>
                <label class="col-md-2 label-control">Created At</label>
                <div class="col-md-4">
                    <p>{{ $item->created_at }}</p>
                </div>
            </div>
            <div class="row">
                <label class="col-md-2 label-control">Inventory Date</label>
                <div class="col-md-4">
                    <p>{{ $item->from_date }}</p>
                </div>
            </div>
            <div class="row">
                <label class="col-md-2 label-control">Row Count</label>
                <div class="col-md-4">
                    <p>{{ $item->row_count }}</p>
                </div>
                @if (json_decode($item->errors))
                <label class="col-md-2 label-control">Errors</label>
                <div class="col-md-4">
                    @foreach (json_decode($item->errors) as $error)
                    <label for="" class="label label-danger">{{ substr($error, 0, 55) . (strlen($error) > 55 ? '...' : '') }}</label>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="row">
                <label class="col-md-2 label-control">Importing Started</label>
                <div class="col-md-4">
                    <p>{{ date('Y-m-d H:i:s', $item->started_at) }}</p>
                </div>
                <label class="col-md-2 label-control">Importing Finished</label>
                <div class="col-md-4">
                    @if ($item->finished_at)
                    <p>{{ date('Y-m-d H:i:s', $item->finished_at) }}</p>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <form method="get" class="pull-right" style="display:inline-block;width: 260px;"
                        action="{{ CRUDBooster::mainPath() . '/detail/' . $item->id }}">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $search_term }}"
                                class="form-control input-sm pull-right" placeholder="Search">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <div class="table-responsive" >
                @if (sizeof($store_inventories))
                <table id="table_dashboard" class="table table-hover table-striped table-bordered" id="store-sales-table">
                    <thead>
                        <tr class="active">
                            @foreach (explode(',', $user_report->report_header) as $th)
                                <th >{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($store_inventories as $inventory)
                            <tr>
                                @foreach (explode("`,`",$user_report->report_query) as $col_name)
                                    <td>{{ $inventory->$col_name }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-bold text-center">No data to show</p>
                @endif            </div>
            <div class="col-md-12">
                {{ $store_inventories->links() }}
                <p>Showing {{ $store_inventories->firstItem() ?? 0 }} to {{ $store_inventories->lastItem() ?? 0 }} of {{ $store_inventories->total() }} items.</p>        
            </div>
        </div>
        <div class="panel-footer">
            <a href='{{ CRUDBooster::mainpath() }}' class='btn btn-default'>Back</a>
        </div>
    </div>
@endsection

@push('bottom')
@endpush
