@extends('crudbooster::admin_template')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
    .content {
     display: flex;
     align-items: center;
     justify-content: center;
    }
    .dropdowns {
        border: none;
        border-radius: 5px;
        appearance: none;
        padding: 10px 50px 10px 30px;
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
        width: 100%;
    }
    label {
        display: inline;
        font-weight: 600;
    }
 
    .select-container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .icon-container {
        position: absolute;
        right: 20px;
        display: flex;
        justify-content: center;
        color: #555555;
    }
   
    .form-container {
        background-color: white;
        border: none;
        border-radius: 5px;
        padding: 25px 50px;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        width: 75%;
        margin-top: 20px;
    }
    .disabled-color {
        background-color: #bbb9b9;
    }
    .arterisk {
        color: red;
    }
    .btn-submit {
        margin: 20px 10px 0 0;
        background-color: #1c84ff;
        color: white;
        font-size: 18px;
        padding: 8px 15px;
        border-radius: 5px;
        border: none;
        align-self: flex-end;
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    }

    .select2-container--default .select2-selection--multiple{
        font-size: 15px;
        border: none;
        border-radius: 5px;
        appearance: none;
        padding: 6px 0 8px 10px;
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgb(221, 221, 221); 
        color: black;
        border: none;
        font-size: 15px;
    }

</style>
@endpush

@section('content')


<div class="form-container">
    <form method='get' target='_blank' action="{{ route('run-rate.filter-run-rate') }}" autocomplete="off">
        @csrf
                <div>
                <div class="col-md-6">
                        <label>Brand Group <span class="arterisk">*</span></label>
                        <div class="form-group select-container">
                            <select  class="dropdowns" name="brand" id="brand" class="form-control" title="brand" required>
                                <option value="" selected disabled>Please select brand group</option>
                                <option value="APPLE - WEEKLY">APPLE - WEEKLY</option>
                                <option value="NON-APPLE - WEEKLY">NON-APPLE - WEEKLY</option>
                                <option value="NON-APPLE - MONTHLY">NON-APPLE - MONTHLY</option>
                                <option value="GASHAPON - WEEKLY">GASHAPON - WEEKLY</option>
                                <option value="GASHAPON - MONTHLY">GASHAPON - MONTHLY</option>
                            </select>
                                <div class="icon-container">
                                    <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                    <label>Year <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                        <select class="dropdowns" name="sales_year" id="sales_year" class="form-control" title="Year" disabled required>
                            <option value="" selected disabled>Please select year</option>
                        </select>
                    <div class="icon-container">
                        <i class="fa fa-caret-down"></i>
                    </div>
                    </div>
                    <label> Month <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                        <select class="dropdowns" name="sales_month" id="sales_month" class="form-control" title="Month" disabled required>
                            <option value="" selected disabled>Please select month</option>
                        </select>
                    <div class="icon-container">
                        <i class="fa fa-caret-down"></i>
                    </div>
                    </div>
                    <label>Cutoff Range <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                        <select class="dropdowns" name="cutoff" id="cutoff" class="form-control" title="cutoff" disabled required>
                            <option value="" selected disabled>Please select cutoff</option>
                        </select>
                    <div class="icon-container">
                        <i class="fa fa-caret-down"></i>
                    </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Channel <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                    {{-- <select class="dropdowns" name="channels_id" id="channel" class="form-control channel" title="Channel" required>
                        <option selected disabled>Please select channel</option>
                        @foreach ($channels as $channel)
                            <option data-code="{{ $channel->channel_code }}" value="{{ $channel->id }}">{{ $channel->channel_name }}</option>
                        @endforeach
                    </select> --}}
                    <select name="channels_id[]" id="channel" class="js-example-basic-multiple" multiple="multiple" required>
                        <option selected value="">All</option>
                        @foreach ($channels as $channel)
                        <option data-code="{{ $channel->channel_code }}" value="{{ $channel->id }}">{{ $channel->channel_name }}</option>
                        @endforeach
                    </select> 
                    <div class="icon-container">
                        <i class="fa fa-caret-down"></i>
                    </div>
                    </div>
                    <label>Store Concept</span></label>
                    <div class="form-group select-container">
                    <select name="concepts_id[]" id="store_concept_name" class="js-example-basic-multiple" multiple="multiple" required>
                        <option selected value="">All</option>
                        @foreach ($concepts as $concept)
                        <option value="{{ $concept->id }}">{{ $concept->concept_name }}</option>
                        @endforeach
                    </select> 
                    <div class="icon-container">
                        <i class="fa fa-caret-down"></i>
                    </div>
                    </div>
                    <label>Per Store / Location</label>
                    <div class="form-group select-container">
                     <select name="customers_id[]" id="customer_location" class="js-example-basic-multiple" multiple="multiple" required>
                        <option selected value="">All</option>
                        @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select> 
                    
                    <div class="icon-container">
                        <i class="fa fa-caret-down"></i>
                    </div>
                    </div>
                </div>  
                </div>
                <div>
                    <div class="col-md-6">
                    </div>
                    <div class="pull-right" >
                        <button class="btn-submit" type='submit'>Search</button>
                    </div>
                </div>
                           
            </div>
    </form>
</div>

@endsection

@push('bottom')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>

         $(document).ready(function(){
            $('.js-example-basic-multiple').select2({
                width: '100%'
            });
            
            $('#sales_year, #sales_month,#cutoff')
            .addClass('disabled-color');
        
     
            $('#channel option:not(:first-child)').prop('disabled', true);
            $('#store_concept_name option:not(:first-child)').prop('disabled', true);
            $('#customer_location option:not(:first-child)').prop('disabled', true);

        });
        

        $('#brand').change(function() {
            let brand = $(this).val()
            $.ajax({
            url: "{{ route('get-year') }}",
            type: "GET",
            data: {
                'brand': brand,
                },
            success: function(result)
            {
                let i;
                let showYear = [];
    
                showYear[0] = "<option selected disabled value=''>Please select year</option>";
                for (i = 0; i < result.length; ++i) {
                    showYear[i+1] = "<option value='"+result[i]+"'>"+result[i]+"</option>";
                }
                jQuery("#sales_year").html(showYear); 
                $('#sales_year').removeAttr('disabled');
                $('#sales_year,#sales_month,#cutoff').val('');
                $('#sales_year').removeClass('disabled-color')
                $('#sales_month').attr("disabled", true)
                $('#cutoff').attr("disabled", true)
                $('#sales_month,#cutoff')
                .addClass('disabled-color')
            }
            })
        })
        
        $('#sales_year').change(function() {
    
            let year = $(this).val()
            let brand = $('#brand').val();
            $.ajax({
            url: "{{ route('get-month') }}",
            type: "GET",
            data: {
                'year': year,
                'brand': brand,
                },
            success: function(result)
            {

                let showMonths = [];
                let monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                showMonths.push("<option selected disabled value=''>Please select month</option>");
                
                for (let i = 0; i < result.length; ++i) {
                    let monthIndex = result[i] - 1; 
                    showMonths.push("<option value='" + result[i] + "'>" + monthNames[monthIndex] + "</option>");
                }
                    jQuery("#sales_month").html(showMonths); 
                    $('#sales_month').removeAttr('disabled');
                    $('#cutoff').attr("disabled", true)
                    $('#cutoff').val('');
                    $('#sales_month').removeClass('disabled-color')
                    $('#cutoff')
                    .addClass('disabled-color')
                }
                })
        })

        
        $('#sales_month').change(function() {
           const year =  $('#sales_year').val();
           const brandGroup = $('#brand').val();
           const month = $(this).val();

           $.ajax({
            url: "{{ route('get-cutoff-range') }}",
            type: "GET",
            data: {
                'year': year,
                'brandGroup': brandGroup,
                'month': month,

                },
            success: function(result)
            {   
                console.log(result)
                let i;
                let showCutoffRange = [];
    
                showCutoffRange[0] = "<option selected disabled value=''>Please select cutoff</option>";
                for (i = 0; i < result.length; ++i) {
                    showCutoffRange[i+1] = "<option value='"+result[i]+"'>"+result[i]+"</option>";
                }
            
                jQuery("#cutoff").html(showCutoffRange); 
                if(brandGroup != 'NON-APPLE - MONTHLY' && brandGroup != 'GASHAPON - MONTHLY') {
                $('#cutoff').removeAttr('disabled')
                $('#cutoff').removeClass('disabled-color')
                }
            }
            })
        })
        
        function handleSelect2Event(selector) {
        $(selector).on('select2:unselecting', function (e) {
            var unSelectedValue = e.params.args.data.id;
            if (unSelectedValue === '') {
                $(selector + ' option').prop('disabled', false);
            }
        });

        $(selector).on('select2:selecting', function (e) {
        var selectedValue = e.params.args.data.id;
        if (selectedValue === '') {
            $(this).val(['']);
            $(selector + ' option:not(:first-child)').prop('disabled', true);
        } else {
            $(selector + ' option').prop('disabled', false);
        }
        });
    }
    handleSelect2Event('#channel');
    handleSelect2Event('#store_concept_name');
    handleSelect2Event('#customer_location');

    </script>
@endpush
