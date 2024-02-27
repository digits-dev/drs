@extends('crudbooster::admin_template')

@push('head')

@endpush

@section('content')
<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            User Report Privilege Details
        </div>
        <form action="{{ route('report-privileges.save') }}" method="post" id="user-report">
        @csrf
        <div class="panel-body">
            <div class="col-md-4">

                <div class="form-group">
                    <label for="report_types">Report Type</label>
                    <select disabled name="report_types" id="report_types" class="form-control" required>
                        <option value="">{{ $report_type }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cms_privileges">Privilege</label>
                    <select disabled name="cms_privileges" id="cms_privileges" class="form-control" required>
                        <option value="">{{ $privilege }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="table_name">Table Name</label>
                    <select disabled name="table_name" id="table_name" class="form-control" required>
                        <option value="">{{ $report_privilege->table_name }}</option>
                    </select>
                </div>

            </div>

            <div class="col-md-8">
                <div class="form-group">
                    @php
                        $count = ceil(count($reports) / 3);
                        $chunkedReports = array_chunk($reports, $count, true);
                    @endphp
                    @foreach ($chunkedReports as $index => $query)
                    <div class="col-md-4 existing-query">
                        @foreach ($query as $key => $val)
                                <br>
                                <label for="checkbox_{{ $key }}">{{ $val }}</label>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
  
        </form>
    </div>
</div>

@endsection

