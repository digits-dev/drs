
@extends('crudbooster::admin_template')

@push('head')
<style>
    .highlight-ltr{
        color: yellow;
    }
</style>
@endpush

@section('content')

<div class='panel panel-default'>
    <div class='panel-body'>

        @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        @endif

        @if (session()->has('failures'))
        <div class="alert alert-danger" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <table class="table table-bordered">
                <tr>
                    <th>Row</th>
                    <th>Attribute</th>
                    <th>Errors</th>
                    <th>Value</th>
                </tr>

                @foreach (session()->get('failures') as $validation)
                    <tr>
                        <td>{{ $validation->row() }}</td>
                        <td>{{ $validation->attribute() }}</td>
                        <td>
                            <ul>
                                @foreach ($validation->errors() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            {{ $validation->values()[$validation->attribute()] }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @endif

    <form method='post' id='form' enctype='multipart/form-data' action='{{$uploadRoute}}'>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="box-body">
                <div class='callout callout-success'>
                <h4>Welcome to Data Importer Tool</h4>
                Before uploading a file, please read below instructions : <br/>
                * File format should be : XLSX file format<br/>
                * Refence number should be unique.<br/>
                * Kindly <b class="highlight-ltr">trim()</b> value in sold quantity.<br/>
                * Date format should be "<b class="highlight-ltr">YYYYMMDD</b>".<br/>
                * Do not upload items with decimal value in inventory quantity.<br/>
                * Do not upload the file with blank row in between records.<br/>
                * Do not double click upload inventory button.<br/>
                * Please limit your file size to "<b class="highlight-ltr">20mb</b>" per upload.<br/>

                </div>

                <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Next Reference Series</th>
                        <th scope="col">Inventory as of Date</th>
                        <th scope="col">Import Template File</th>
                        <th scope="col">File to Import</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input tabindex="-1" class="form-control" required readonly id="next_series" type="text" value="{{$nextSeries}}">
                        </td>
                        <td>
                            <div class="input-group date">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input tabindex="-1" class="form-control date_picker" required readonly name="inventory_date" id="datepicker" type="text" value="">
                            </div>
                        </td>
                        <td>
                            <a href='{{ $uploadTemplate }}' class='btn btn-primary' role='button'>Download Template</a>
                        </td>
                        <td>
                            <input type='file' name='import_file' id='file_name' class='form-control' required accept='.xlsx' />
                            <div class='help-block'>File type supported only : xlsx</div>
                        </td>
                    </tr>

                </table>

            </div>
    </div>
    <div class='panel-footer'>
        <a href='#' class='btn btn-default'>Cancel</a>
        <input type='submit' id="btnUpload" class='btn btn-primary pull-right' value='Upload Inventory' />
    </div>
    </form>
</div>

@endsection

@push('bottom')
<script type="text/javascript">
    $(document).ready(function() {

        $('.date_picker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            endDate: '+0d'
        });

        $("#btnUpload").on("click", function(event) {
            event.preventDefault();

            $("#btnUpload").prop("disabled", true);
            $("#form").submit();
        });

    });
</script>
@endpush
