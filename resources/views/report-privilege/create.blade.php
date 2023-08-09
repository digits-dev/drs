@extends('crudbooster::admin_template')

@push('head')

@endpush

@section('content')
<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            User Report Privilege Creation
        </div>
        <form action="{{ route('report-privileges.save') }}" method="post" id="user-report">
        @csrf
        <div class="panel-body">
            <div class="col-md-4">

                <div class="form-group">
                    <label for="report_types">Report Type</label>
                    <select name="report_types" id="report_types" class="form-control" required>
                        <option value="">Select Report Type</option>
                        @foreach ($report_types as $report)
                            <option value="{{ $report->id }}">{{ Str::upper($report->report_type) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="cms_privileges">Privilege</label>
                    <select name="cms_privileges" id="cms_privileges" class="form-control" required>
                        <option value="">Select Privilege</option>
                        @foreach ($privileges as $privilege)
                            <option value="{{ $privilege->id }}">{{ Str::upper($privilege->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="table_name">Table Name</label>
                    <select name="table_name" id="table_name" class="form-control" required>
                        <option value="">Select Table</option>
                        @foreach ($tables as $key => $value)
                            <option value="{{ $key }}">{{ Str::upper($value) }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label>Columns</label>
                    <div class="left col-md-4">

                    </div>
                    <div class="mid col-md-4">

                    </div>
                    <div class="right col-md-4">

                    </div>
                </div>
            </div>

        </div>
        <div class="panel-footer">
            <a href='#' class='btn btn-default'>Cancel</a>
            <input type='submit' id="btnSave" class='btn btn-primary pull-right' value='Save' />
        </div>
        </form>
    </div>
</div>

@endsection


@push('bottom')
    <script>
        $(document).ready(function() {

            $("#table_name").change(function () {
                let selectedTable = $(this).val();
                $.ajax({
                    url: "{{ route('report-privileges.getTableColumns') }}",
                    method: "POST",
                    data:{
                        tableName: selectedTable
                    },
                    success : function (data){
                        const length = Object.entries(data).length;

                        $.each(data, function(i,val) {
                            const currentLength = $('.report-headers').get().length;

                            const checkboxElement = $('<input type="checkbox" class="report-headers">').attr({
                                id: 'checkbox_'+i,
                                name: 'table_columns['+i+']',
                                value: val,
                                checked: true
                            });

                            const labelElement = $('<label>').attr('for', 'checkbox_' + i).text(val);

                            if (currentLength < length*(1/3)) {
                                $('.left').append('<br>',checkboxElement, labelElement);
                            }
                            else if (currentLength < length*(2/3)) {
                                $('.mid').append('<br>',checkboxElement, labelElement);
                            }
                            else {
                                $('.right').append('<br>',checkboxElement, labelElement);
                            }

                        });
                    },
                    error   : function(){
                        alert("error");
                    }

                });
            });
        });
    </script>
@endpush
