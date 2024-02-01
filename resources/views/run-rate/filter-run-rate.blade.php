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
            <div class="table-responsive">
                <table id="table_dashboard" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr class="active">
                            <th>Digits Code</th>
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

                {{ $rows->links() }}
            </div>
        </div>
        <div class="panel-footer">
            <a class='btn btn-default' href="{{ CRUDBooster::mainPath() }}">Back</a>
        </div>
    </div>
</div>

@endsection


@push('bottom')

@endpush
