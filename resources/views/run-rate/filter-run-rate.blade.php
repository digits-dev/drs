@extends('crudbooster::admin_template')

@push('head')

@endpush

@section('content')
<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading text-center text-bold">
            RUN RATE RESULT
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <form target="_blank" id="export-form" action="{{ route('run-rate.export-run-rate') }}" method="get" style="display:inline-block;width: 260px;">
                        {!! CRUDBooster::getUrlParameters(['search', 'page']) !!}
                        <button class="btn btn-primary"><i class="fa fa-download"></i> Export</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form id="search-form" method="get" class="pull-right" style="display:inline-block;width: 260px;">
                        {!! CRUDBooster::getUrlParameters(['search']) !!}
                        <div class="input-group">
                            <input type="text" name="search" value="{{ $search }}"
                                class="form-control input-sm pull-right" placeholder="Search">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table id="table_dashboard" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Total</th>
                                <th> </th>
                                @foreach ($cutoff_columns as $column)
                                @php
                                    $value = 0;
                                    foreach ($col_totals as $total) {
                                        if ($total[$column_name] === $column) {
                                            $value = $total['total'];
                                            break;
                                        }
                                    }
                                @endphp
                                <th>{{ $value }}</th>
                                @endforeach
                            </tr>
                            <tr class="active">
                                <th>Digits Code</th>
                                <th>Initial WRR Date</th>
                                @foreach ($cutoff_columns as $column)
                                <th>{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                            <tr>
                                @foreach ($row->toArray() as $col => $val)
                                    <td>{{ $val }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    {{ $rows->links() }}
                    <p>Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} items.</p>        
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <a class='btn btn-default' href="{{ CRUDBooster::mainPath() }}">Back</a>
        </div>
    </div>
</div>

@endsection


@push('bottom')
<script>
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        const currentUrl = window.location.href;
        const searchValue = $(this).find('[name="search"]').val();
        location.assign(`${currentUrl}&search=${searchValue}`);
    });
</script>
@endpush
