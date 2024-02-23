@extends('crudbooster::admin_template')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

</style>
@endpush

@section('content')


<div class="form-container">
    <form method='get' target='_blank' action="{{ route('run-rate.filter-run-rate') }}" autocomplete="off">
        @csrf
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <label>Trade Item/Gashapon<span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                        <select  class="dropdowns" name="item" id="item" class="form-control" title="Item" required>
                            <option value="" selected disabled>Please select</option>
                            <option value="trade-item">Trade item</option>
                            <option value="gashapon">Gashapon</option>
                        </select>
                            <div class="icon-container">
                                <i class="fas fa-caret-down"></i>
                            </div>
                        </div>
                </div>
            </div>
            <div>
                <div>
                <div class="col-md-6">
                        <label>Brand Group <span class="arterisk">*</span></label>
                        <div class="form-group select-container">
                            <select  class="dropdowns" name="brand" id="brand" class="form-control" title="brand" required disabled>
                                <option value="" selected disabled>Please select brand group</option>
                                <option value="APPLE - WEEKLY">APPLE - WEEKLY</option>
                                <option value="NON-APPLE - WEEKLY">NON-APPLE - WEEKLY</option>
                                <option value="NON-APPLE - MONTHLY">NON-APPLE - MONTHLY</option>
                            </select>
                                <div class="icon-container">
                                    <i class="fas fa-caret-down"></i>
                                </div>
                            </div>
                    <label>Year <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                        <select class="dropdowns" name="sales_year" id="sales_year" class="form-control" title="Year" disabled required>
                            <option value="" selected disabled>Please select year</option>
                        </select>
                    <div class="icon-container">
                        <i class="fas fa-caret-down"></i>
                    </div>
                    </div>
                    <label> Month <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                        <select class="dropdowns" name="sales_month" id="sales_month" class="form-control" title="Month" disabled required>
                            <option value="" selected disabled>Please select month</option>
                        </select>
                    <div class="icon-container">
                        <i class="fas fa-caret-down"></i>
                    </div>
                    </div>
                    <label>Cutoff Range <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                        <select class="dropdowns" name="cutoff" id="cutoff" class="form-control" title="cutoff" disabled required>
                            <option value="" selected disabled>Please select cutoff</option>
                        </select>
                    <div class="icon-container">
                        <i class="fas fa-caret-down"></i>
                    </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Channel <span class="arterisk">*</span></label>
                    <div class="form-group select-container">
                    <select class="dropdowns" name="channels_id" id="channel" class="form-control channel" title="Channel" required disabled>
                        <option selected disabled>Please select channel</option>
                        @foreach ($channels as $channel)
                            <option data-code="{{ $channel->channel_code }}" value="{{ $channel->id }}">{{ $channel->channel_name }}</option>
                        @endforeach
                    </select>
                    <div class="icon-container">
                        <i class="fas fa-caret-down"></i>
                    </div>
                    </div>
                    <label>Store Concept</span></label>
                    <div class="form-group select-container">
                    <select class="dropdowns" name="concepts_id" id="store_concept_name" class="form-control" disabled>
                        <option value="" selected disabled>Select a store concept</option>
                    </select>
                    <div class="icon-container">
                        <i class="fas fa-caret-down"></i>
                    </div>
                    </div>
                    <label>Per Store / Location</label>
                    <div class="form-group select-container">
                    <select class="dropdowns" name="customers_id" id="customer_location" class="form-control" disabled>
                        <option value="" selected disabled>Select a store / location</option>
                    </select>
                    <div class="icon-container">
                        <i class="fas fa-caret-down"></i>
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
    <script>

         $(document).ready(function(){
            $('#brand, #channel, #sales_year, #sales_month,#store_concept_name,#customer_location,#cutoff')
            .addClass('disabled-color');
        
        });
        
        $('#item').change(function () {
            $('.trade-container').fadeIn(1000);
            $('#brand,#sales_year,#sales_month,#cutoff, #channel, #store_concept_name, #customer_location').val('');
            $('#sales_year, #sales_month,#store_concept_name,#customer_location,#cutoff')
            .addClass('disabled-color');
            $('#sales_year, #sales_month,#store_concept_name,#customer_location,#cutoff')
            .attr("disabled", true)
            $('#brand, #channel').removeAttr('disabled');
            $('#brand, #channel').removeClass('disabled-color');

            if ($('#channel').find('option[value=""]').length === 0) {
                var allChannelsOption = "<option selected value=''>-- All Channels --</option>";
                $('#channel').find('option').eq(1).before(allChannelsOption);
            }

             
            
            if ($(this).val() == 'gashapon') {
                $("#brand option[value='APPLE - WEEKLY']").remove();
            } else {
                if ($("#brand option[value='APPLE - WEEKLY']").length == 0) {
                    $("#brand option[value='']").after("<option value='APPLE - WEEKLY'>APPLE - WEEKLY</option>");
                }
            }
        })

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
                if(brandGroup != 'NON-APPLE - MONTHLY') {
                $('#cutoff').removeAttr('disabled')
                $('#cutoff').removeClass('disabled-color')
                }
            }
            })
        })
        
        $('#channel').change(function() {
            let channelId = $(this).val();
            if($(this).val() == '') {
                $('#store_concept_name, #customer_location').val('');
                $('#customer_location, #store_concept_name').attr("disabled", true)
                $('#customer_location, #store_concept_name').addClass('disabled-color')
            }else {
                $.ajax({
                url: "{{ route('run-rate-concepts') }}",
            type: "GET",
            data: {
                'channel': channelId,
                },
            success: function(result)
            {
                let i;
                let showConcept = [];
    
                showConcept[0] = "<option selected value=''>-- All Store Concepts --</option>";
                for (i = 0; i < result.length; ++i) {
                    showConcept[i+1] = "<option value='"+result[i].id+"'>"+result[i].concept_name+"</option>";
                }
                $('#store_concept_name').removeAttr("disabled")
                $('#store_concept_name').find('option').remove();
                jQuery("#store_concept_name").html(showConcept); 

                $('#customer_location').attr("disabled", true)
                $('#customer_location').val("");
                $('#store_concept_name').removeClass('disabled-color')
                $('#customer_location').addClass('disabled-color')
            }
            })
            }
        })

        $('#store_concept_name').change(function() {
            let storeConceptId = $(this).val();
            let channelCode = $('#channel').find(':selected').data('code');

            $.ajax({
                url: "{{ route('run-rate-store') }}",
            type: "GET",
            data: {
                'storeConceptId': storeConceptId,
                'channelCode': channelCode,
                },
            success: function(result)
            {
                console.log(result);
                let i;
                let showStore = [];
    
                showStore[0] = "<option selected value=''>-- All Stores --</option>";
                for (i = 0; i < result.length; ++i) {
                    showStore[i+1] = "<option value='"+result[i].id+"'>"+result[i].customer_name+"</option>";
                }
                $('#customer_location').removeAttr("disabled")
                $('#customer_location').find('option').remove();
                jQuery("#customer_location").html(showStore); 

                $('#customer_location').removeClass('disabled-color')
            }
            })
        })
    </script>
@endpush
