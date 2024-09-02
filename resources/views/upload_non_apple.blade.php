@extends('crudbooster::admin_template')
@section('content')

<div id='box_main' class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Upload a File</h3>
        <div class="box-tools"></div>
    </div>
    
    @if ($message = Session::get('success_import'))
    <div class="alert alert-success" role="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('success_import') }}
    </div>
    @endif 
    @if ($message = Session::get('error_import'))
    <div class="alert alert-danger" role="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Errors Found !
        <li>
            {{ Session::get('error_import') }}
        </li>
    </div>
    @endif

    <form method='post' id="form" enctype="multipart/form-data" action="{{ route('upload.createNonApple') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden"name='upload_option' value="1">
        <div class="box-body">

            <div class='callout callout-success'>
                <h4>Welcome to Data Importer Tool</h4>
                Before uploading a file, please read below instructions : <br/>
                1. Blank value is not allowed.<br/>
                2. File format should be : CSV file format<br/>
            </div>

            <label class='col-sm-2 control-label'>Import Template File: </label>
            <div class='col-sm-4'>
                <a href='{{ CRUDBooster::mainpath() }}/import-template' class="btn btn-primary" role="button">Download Template</a>
            </div>
            <br/>
            <br/>

            <label for='import_file' class='col-sm-2 control-label'>File to Import: </label>
            <div class='col-sm-4'>
                <input type='file' name='import_file' class='form-control' required accept=".csv"/>
                <div class='help-block'>File type supported only : CSV</div>
            </div>

        </div><!-- /.box-body -->

        <div class="box-footer">
            <div class='pull-right'>
                <a href='{{ CRUDBooster::mainpath() }}' class='btn btn-default'>Cancel</a>
                <input type='submit' class='btn btn-primary' name='submit' value='Upload'/>
            </div>
        </div><!-- /.box-footer-->
    </form>
</div><!-- /.box -->

@endsection