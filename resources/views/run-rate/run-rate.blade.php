@extends('crudbooster::admin_template')


@section('content')
<form method='post' target='_blank' action=""autocomplete="off">
    <div class="modal-body">
  

        <div class="row">

            <div class="col-md-3">
                Brand Group
                <div class="form-group">
                <select name="brand" id="brand" class="form-control" title="brand">
                    <option value="" selected disabled>Please select brand group</option>
                    <option value="APPLE - WEEKLY">APPLE - WEEKLY</option>
                    <option value="NON-APPLE - WEEKLY">NON-APPLE - WEEKLY</option>
                    <option value="NON-APPLE - MONTHLY">NON-APPLE - MONTHLY</option>
        
                </select>
                </div>
            </div>
            <div class="col-md-3">
                Year
                <div class="form-group">
                <select name="sales_year" id="sales_year" class="form-control" title="Year" disabled>
                    <option value="" selected disabled>Please select year</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->sales_year }}">{{ $year->sales_year }}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="col-md-3">
                Month
                <div class="form-group">
                <select name="sales_month" id="sales_month" class="form-control" title="Month" disabled>
                    <option value="" selected disabled>Please select month</option>
                </select>
                </div>
            </div>
            <div class="col-md-3">
                Cutoff Range
                <div class="form-group">
                <select name="cutoff" id="cutoff" class="form-control" title="cutoff" disabled>
                    <option value="" selected disabled>Please select cutoff</option>
                </select>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-3">
                Channel
                <div class="form-group">
                <select name="channel_name" id="channel" class="form-control channel" title="Channel">
                    <option value="" selected disabled>Please select channel</option>
                    @foreach ($channels as $channel)
                        <option data-id="{{ $channel->id }}" value="{{ $channel->channel_name }}">{{ $channel->channel_name }}</option>
                    @endforeach
                </select>
                </div>
            </div>

            <div class="col-md-3">
                Store Concept
                <div class="form-group">
                <select name="store_concept_name" id="store_concept_name" class="form-control" disabled>
                    <option value="" selected disabled>Select a store concept</option>
                </select>
                </div>
            </div>

            <div class="col-md-3">
                Per Store / Location
                <div class="form-group">
                <select name="customer_location" id="customer_location" class="form-control" disabled>
                    <option value="" selected disabled>Select a store / location</option>
                </select>
                </div>
            </div>
            <div class="col-md-3">
                <button class='btn btn-primary btn-submit' type='submit'>Search</button>
            </div>
        </div>
    </div>


    </form>
@endsection

@push('bottom')
    <script>

        $('#brand').change(function() {
            $('#sales_year').removeAttr('disabled');
            $('#sales_year').val('');
            $('#sales_month').attr("disabled", true)
            $('#sales_month').val('');
            $('#cutoff').attr("disabled", true)
            $('#cutoff').val('');
        })
        
        $('#sales_year').change(function() {
    
            let year = $(this).val()
            $.ajax({
            url: "{{ route('get-month') }}",
            type: "GET",
            data: {
                'year': year,
                },
            success: function(result)
            {
                console.log(result);

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
                $('#cutoff').removeAttr('disabled')
            }
            })
        })
        
        $('#channel').change(function() {
            let channelId = $(this).find(':selected').data('id');
            $.ajax({
                url: "{{ route('run-rate-concepts') }}",
            type: "GET",
            data: {
                'channel': channelId,
                },
            success: function(result)
            {
                console.log(result);
                let i;
                let showConcept = [];
    
                showConcept[0] = "<option selected disabled value=''>Select a store concept</option>";
                for (i = 0; i < result.length; ++i) {
                    showConcept[i+1] = "<option data-id='"+result[i].id+"' value='"+result[i].concept_name+"'>"+result[i].concept_name+"</option>";
                }
                $('#store_concept_name').removeAttr("disabled")
                $('#store_concept_name').find('option').remove();
                jQuery("#store_concept_name").html(showConcept); 

                $('#customer_location').attr("disabled", true)
                $('#customer_location').val("");
            }
            })
        })

        $('#store_concept_name').change(function() {
            let storeConceptId = $(this).find(':selected').data('id');

            $.ajax({
                url: "{{ route('run-rate-store') }}",
            type: "GET",
            data: {
                'storeConceptId': storeConceptId,
                },
            success: function(result)
            {
                console.log(result);
                let i;
                let showStore = [];
    
                showStore[0] = "<option selected disabled value=''>Select a store</option>";
                for (i = 0; i < result.length; ++i) {
                    showStore[i+1] = "<option value='"+result[i]+"'>"+result[i]+"</option>";
                }
                $('#customer_location').removeAttr("disabled")
                $('#customer_location').find('option').remove();
                jQuery("#customer_location").html(showStore); 
            }
            })
        })
    </script>
@endpush
