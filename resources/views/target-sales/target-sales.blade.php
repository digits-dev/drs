
@extends('crudbooster::admin_template')

@section('content')
<style>
    .box-body {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .upload-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        border: 1px solid #ccc;
        border-radius: 10px;
        width: 70%;
        padding: 20px;
    }
    .inner-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        height: 100%;
    }
    .left-container {
        width: 50%;
        border: 2px dashed #ccc;
        border-radius: 10px;
        height: 200px;
    }
    .right-container {
        display: flex;
        flex-direction: column;
        width: 50%;
        height: 100%;
        gap: 20px;
    }
    .upload-file-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .image-upload {
        width: 200px;
    }
    .image-view {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        cursor: pointer;
        height: 200px;
    }
    .image-view p {
        font-size: 12px;
        color: #777;
        margin-top: 15px;
        }
    .selected-files {
        display: flex;
        flex-direction: column;
        background-color: #f9fafb;
        padding: 20px;
        border-radius: 10px;
        border: none;
    }
    .download-btn {
    display: inline-block;
    font-weight: 600;
    color: black;
    text-align: center;
    text-decoration: none;
    }
    .download-btn {
        padding: 10px 20px;
        background-color: #f0f0f0; /* Light grey background */
        border-radius: 5px; /* Rounded corners */
    }
    .download-btn:hover {
        background-color: #e0e0e0; 
        color: black;
    }
    .download {
        width: 100%;
        height: 40px;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .upload {
        background-color: black;
        color: white;
    }
    #drop-area {
            width: 100%;
    }

    #drop-area.drag-over 
    {
    background-color: #e0e7ff; /* Light blue background */
    border-color: #3b82f6; /* Darker blue border */
    }
</style>


<div id='box_main' class="box box-primary">

    @if ($message = Session::get('success_import'))
    <div class="alert alert-success" role="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('success_import') }}
    </div>
    @endif 
    @if ($message = Session::get('error_import'))
    <div class="alert alert-danger" role="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Errors Found
        <li>
            {!! Session::get('error_import') !!}
        </li>
    </div>
    @endif

    <form method='post' id="form" enctype="multipart/form-data" action="{{ route('import-target-sales') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="box-body">

            {{-- <div class='callout callout-success'>
                <h4>Welcome to Data Importer Tool</h4>
                Before uploading a file, please read below instructions : <br/>
                1. System will accept blank unless the columns have dependencies with each other.<br/>
                2. File format should be : CSV file format<br/>
            </div>

            <label class='col-sm-2 control-label'>Import Template File: </label>
            <div class='col-sm-4'>
                <a href="{{ route('upload-breakeven-template') }}" class="btn btn-primary" role="button">Download Template</a>
            </div>
            <br/>
            <br/>

            <label for='import_file' class='col-sm-2 control-label'>File to Import: </label>
            <div class='col-sm-4'>
                <input type='file' name='import_file' class='form-control' required accept=".csv"/>
                <div class='help-block'>File type supported only : CSV</div>
            </div> --}}

            <div class="upload-container">
                <p style="font-size: 20px; font-weight: 600;">Upload a File</p>
                    <div class="inner-container">
                        <div class="left-container">
                            <div class="upload-file-container">
                                    <label for="import_file" id="drop-area">
                                        <input type='file' id="import_file" name='import_file' required accept=".csv"
                                        style="position: absolute; z-index: -1; ">
                                        <div class="image-view" id="image-view">
                                            <svg style="color:#777" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-upload h-12 w-12 text-gray-400 mb-4" data-id="8"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="m16 16-4-4-4 4"></path></svg>
                                            <p>Drag and drop or click here to upload</p>
                                        </div>
                                    </label>
                            </div>
                        </div>
                        <div class="right-container">
                            <div class="selected-files">
                                <span style="font-weight: bold;">Selected Files:</span>
                                <div style="display: flex; justify-content: space-between; align-items:center;">
                                    <span id="file-name-display" style="color: #777">No files selected</span>
                                    <button type="button" id="remove-file" style="display: none;background: none; border: none; cursor: pointer; font-size: 16px; font-weight: bold;">&times;</button>
                                </div>
                            </div>
                            <a href="{{ route('upload-target-template') }}" class="download-btn">
                                <i class="fa fa-download" aria-hidden="true"></i> Download Template
                            </a>
                            <button type="submit" id="btnSubmit" class="download upload">Upload File</button>
                        </div>
                    </div>
            </div>
            

        </div><!-- /.box-body -->

        <div class="box-footer">
            <a href="{{ CRUDBooster::mainpath() }}" class='btn btn-default pull-left'>Cancel</a>
            {{-- <button class="btn btn-primary pull-right" type="submit" id="btnSubmit"> <i class="fa fa-save" ></i> Upload</button> --}}
            
        </div><!-- /.box-footer-->
    </form>
</div><!-- /.box -->

@endsection

@push('bottom')

<script type="text/javascript">
$(document).ready(function() {
    $("#btnSubmit").click(function() {
        $(this).prop("disabled", true);
        $("#form").submit();
    });

    $('#import_file').on('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : "No files selected";
        // var fileName = e.target.files[0].name;
        console.log(fileName)
        $('#file-name-display').text(fileName);
        $('#remove-file').show(); 
    });
    $('#remove-file').on('click', function() {
        $('#import_file').val(''); // Reset the file input
        $('#file-name-display').text("No files selected"); // Reset the display text
        $(this).hide(); // Hide the remove button
    });

    var dropArea = $('#drop-area');

    // Highlight drop area on dragover
    dropArea.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.addClass('drag-over');
    });

    // Remove highlight on dragleave
    dropArea.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.removeClass('drag-over');
    });

    // Handle file drop
    dropArea.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.removeClass('drag-over');

        // Get the dropped file
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#import_file')[0].files = files; // Assign dropped files to the input
            var fileName = files[0].name;
            $('#file-name-display').text(fileName); // Update file name display
            $('#remove-file').show(); // Show the remove button
        }
    });
    });
</script>
@endpush