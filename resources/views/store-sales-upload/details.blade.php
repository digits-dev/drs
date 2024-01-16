@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">
@endpush

@section('content')
<p class="noprint">
    <a title='Return' href="{{ CRUDBooster::mainPath() }}">
        <i class='fa fa-chevron-circle-left '></i> &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}
    </a>
</p>
<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa-eye"></i><strong> Details {{CRUDBooster::getCurrentModule()->name}}</strong>
    </div>
    <div class="panel-body">
        <h4 class="text-center text-bold">BATCH DETAILS</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <th>Batch</th>
                        <td>{{ $item->batch }}</td>
                        <th>File Name</th>
                        <td>{{ $item->file_name }}</td>
                    </tr>
                    <tr>
                        <th>Row Count</th>
                        <td>{{ $item->row_count }}</td>
                        
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ $item->name }}</td>
                        <th>Created Date</th>
                        <td>{{ $item->created_at }}</td>
                    </tr>
                    <tr>
                        <th>Importing Started At</th>
                        <td>{{ date('Y-m-d H:i:s', $item->started_at) }}</td>
                        <th>Importing Finished At</th>
                        <td>{{ date('Y-m-d H:i:s', $item->finished_at) }}</td>
                    </tr>
                </tbody>
            </table>
            <hr>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form method="get" class="pull-right" style="display:inline-block;width: 260px;" action="{{ route('store_sales.detail', $item->id) }}">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ $search_term }}" class="form-control input-sm pull-right" placeholder="Search">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="store-sales-table">
                <thead>
                    <tr>
                        @foreach (explode(',', $user_report->report_header) as $th)
                        <th>{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($store_sales as $sale)
                    <tr>
                        @foreach(array_values($sale->toArray()) as $value)
                        <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $store_sales->links() }}
    </div>
    <div class="panel-footer">
        <a href='{{ CRUDBooster::mainpath() }}' class='btn btn-default'>Back</a>    </div>
</div>
@endsection

@push('bottom')
@endpush
