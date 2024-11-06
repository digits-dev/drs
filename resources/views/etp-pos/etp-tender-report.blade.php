@extends('crudbooster::admin_template')
@push('head')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        .form-content {
            display: flex;
            background: #fff;
            flex-direction: column;
            font-family: 'Poppins', sans-serif !important;
            border-radius: 10px;
        }

        body {
            overflow: scroll;
        }

        .header-title {
            background: #3C8DBC !important;
            color: #fff !important;
            font-size: 16px;
            font-weight: 500;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .content-panel {
            padding: 15px;
        }

        .inputs-container {
            display: flex;
            gap: 10px;
        }

        .input-container {
            display: flex;
            flex: 1;
            flex-direction: column;
        }

        /* DATE PICKER */

        .date-input {
            padding: 10px;
            border: 1px solid #3C8DBC;
            border-radius: 7px;
        }

        .date-input:focus {
            border: 2px solid #3C8DBC;
            /* Change this to your desired focus color */
            outline: none;
            /* Optional: remove the default outline */
        }


        .select2-container--default .select2-selection--multiple {
            border-color: #3498db;
            border-radius: 7px;
            padding: 6px 0 8px 10px;
        }

        .select2-container--default .select2-selection__choice {
            background-color: #3498db !important;
            color: #ffffff !important;
            border: 1px solid #2980b9;
        }

        .select2-container--default .select2-selection__choice:hover {
            background-color: #2980b9 !important;
            color: #ffffff !important;

        }

        .select2-container {
            width: 100% !important;
        }

        .form-button .btn-submit {
            padding: 9px 20px;
            background: #3C8DBC;
            border: 1.5px solid #1d699c;
            border-radius: 10px;
            color: white;
        }

        .form-button .btn-submit:hover {
            opacity: 0.7;
        }

        .form-button .btn-clear {
            padding: 9px 20px;
            background: rgb(216, 22, 15);
            border: 1.5px solid rgb(216, 22, 15);
            border-radius: 10px;
            color: white;
            margin-right: 10px;
        }

        .btn-clear:hover {
            opacity: 0.7;
        }

        .btn-refresh:hover {
            opacity: 0.7;
        }

        .form-button .btn-refresh {
            padding: 9px 20px;
            background: #0bbb31;
            border: 1.5px solid #0bbb31;
            border-radius: 10px;
            color: white;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        /* General table styling */
        .dataTable {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            background-color: #f9f9f9;
        }

        /* Header styling */
        .dataTable thead {
            background-color: #3C8DBC;
            color: white;
        }

        .dataTable thead th {
            padding: 12px 15px;
            text-align: center;
            font-weight: bold;
            border: 2px solid #3c8dbcc1;
        }

        /* Body styling */
        .dataTable tbody td {
            padding: 10px 15px;
            border: 1px solid #ddd;
            text-align: center;
            cursor: pointer;
        }

        /* Row hover effect */
        .dataTable tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Search box styling */
        .dataTables_wrapper .dataTables_filter input {
            padding: 6px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-left: 10px;
        }

        /* Entries dropdown styling */
        .dataTables_wrapper .dataTables_length select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        /* Info and pagination area styling */
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: inline-block;
            margin-top: 10px;
        }

        .dataTables_wrapper .dataTables_info {
            float: left;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
        }

        .dataTables_wrapper .top {
            display: flex;
            justify-content: space-between;
            /* Aligns all items to the right */
            align-items: center;
            /* Vertically centers items */
            margin-bottom: 10px;
            /* Space between the top and the table */
        }

        .dataTables_wrapper .dt-buttons {
            margin-left: 10px;
            /* Space between buttons */
        }

        .dataTables_wrapper .dataTables_length {
            margin-right: 10px;
            /* Space between "Show entries" dropdown and buttons */
        }

        .dataTables_wrapper .dataTables_filter {
            display: flex;
            align-items: center;
            /* Center search input vertically */
            margin-left: 10px;
            /* Space between buttons and search */
        }

        .dataTables_wrapper .dataTables_filter label {
            margin-right: 5px;
            /* Space between label and input */
        }

        .custom-button {
            background-color: #28a745;
            color: white;
            border: 1px solid transparent;
            border-radius: 4px;
            padding: 6px 7px;
            font-size: 13px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .custom-button:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: whitesmoke;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .custom-button:active {
            background-color: #1e7e34;
            color: white;
            box-shadow: none;
        }

        .copy-note {
            display: inline-block;
            color: green;
            font-size: 12px;
        }

        .inactive {
            border: 1px solid rgba(255, 0, 0, 0.853) !important;
        }

        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            width: 55px;
            height: 55px;
            border: 10px solid rgba(43, 253, 253, 0.312);
            border-left-color: #3C8DBC;
            border-radius: 50%;
            animation: spin 0.5s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 840px) {
            .inputs-container {
                display: flex;
                flex-direction: column;

            }
        }

        .wrapper {
            overflow: hidden;
        }
    </style>
@endpush
@section('content')

    <form class="panel panel-default form-content">
        <div class="panel-heading header-title">Filter Data</div>
        <div class="content-panel">
            <p><span style="color: red">Note:</span> Please fill all the fields</p>
            <div class="inputs-container">
                <div class="input-container" id="channel-input-container">
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Channel<small id="channelRequired" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i class="fa fa-exclamation-circle"></i> Required field! </small> </p>
                    <select class="js-example-basic-multiple" id="channel" name="channel[]" multiple="multiple" onchange="selectOnChange('channel')">
                        <option value ="All">All</option>
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->channel_code }}">{{ $channel->channel_name }}</option>
                        @endforeach
                    </select> 
                    @php
                        $Channels = $channels->pluck('channel_code')->toArray();
                        $allChannels = implode(', ', $Channels);
                    @endphp

                    <textarea id="all_channels" style="display: none">{{ $allChannels }}</textarea>    

                </div>
                <div class="input-container" id="concept-input-container">
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Store Concept<small id="conceptRequired" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i class="fa fa-exclamation-circle"></i> Required field! </small> <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size: 14px; color:#2ad34e; display:none" id="concept-load"></i> </p>
                    <select class="js-example-basic-multiple" id="concept" name="concept[]" multiple="multiple" onchange="selectOnChange('concept')">
                        <option value ="All">All</option>
                        @foreach ($concepts as $concept)
                            <option value="{{ $concept->concept_name }}">{{ $concept->concept_name }}</option>
                        @endforeach
                    </select> 
                    @php
                        $Concepts = $concepts->pluck('concept_name')->toArray();
                        $allConcepts = implode(', ', $Concepts);
                    @endphp

                    <textarea id="all_concepts" style="display: none">{{ $allConcepts }}</textarea>   
                </div>
            </div>
            <div class="inputs-container" style="margin-top: 10px;">
                <div class="input-container" id="customer-input-container">
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Store Name<small id="customerRequired"
                            style="display: none; color: rgba(255, 0, 0, 0.853);"> <i class="fa fa-exclamation-circle"></i>
                            Required field! </small> </p>
                    <select class="js-example-basic-multiple" id="customer" name="customer[]" multiple="multiple" onchange="selectOnChange('customer')">
                    </select>

                    <textarea id="all_customer" style="display: none"></textarea>
                </div>
            </div>
            <div class="inputs-container" style="margin-top: 10px;">      
                <div class="input-container">
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Date From
                        <small id="dateFromRequired" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i
                                class="fa fa-exclamation-circle"></i> Required field! </small>
                        <small id="invalidDateFrom" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i
                                class="fa fa-exclamation-circle"></i> 'Date From' cannot be after 'Date To'.</small>
                        <small id="validDateFrom" style="display: none; color: #0bbb31;"> <i class="fa fa-check"></i> Valid
                            Date From parameter.</small>
                    </p>
                    <input class="date-input" type="date" required placeholder="Select Date" name="date_from"
                        id="date_from" required>
                </div>
                <div class="input-container">
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Date To
                        <small id="dateToRequired" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i
                                class="fa fa-exclamation-circle"></i> Required field! </small>
                        <small id="validDateTo" style="display: none; color: #0bbb31;"> <i class="fa fa-check"></i> Valid
                            Date To parameter.</small>
                    </p>
                    <input class="date-input" type="date" required placeholder="Select Date" name="date_to"
                        id="date_to" required>
                </div>
            </div>

            <div class="pull-right" style="gap: 5px; display:flex">
                <div class="form-button" style="margin-top: 15px;" >
                    <button type="button" class="btn-submit"  id="btn-reset" style="background:#e73131; border: 1px solid #d34040">Reset</button>
                </div>
                <div class="form-button" style="margin-top: 15px;" >
                    <button class="btn-submit" id="btn-submit">Search</button>
                </div>
            </div>
        </div>
    </form>
    <div class="panel panel-default" style="padding: 15px; overflow:hidden; border-radius: 10px; display: none;"
        id="rawData">
        <div>
            <table class="table" id="tender_report">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Store ID</th>
                        <th>Branch</th>
                        <th>Document #</th>
                        <th>Invoice Ref #</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Tender</th>
                        <th>Credit Card Name</th>
                        <th>Credit Card Number</th>
                        <th>Card Type</th>
                        <th>EDC</th>
                        <th>Expiry</th>
                        <th>AP No</th>
                        <th>MDR %</th>
                        <th>MDR Charge</th>
                        <th>Net Amount</th>
                        <th>OP ID</th>
                        <th>Cashier</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $customerMap = [];
                        foreach ($customers as $customer) {
                            $customerMap[str_replace('CUS-', '', $customer->customer_code)] = $customer->cutomer_name;
                        }
                    @endphp
                    @if (!empty($tender_data) && is_array($tender_data))
                        @foreach ($tender_data as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->DATE)->format('Y-m-d') }}</td>
                                <td>{{ $row->TIME }}</td>
                                <td>{{ $row->{'STORE ID'} }}</td>
                                <td>{{ $row->customerName }}</td>
                                <td>{{ $row->{'RECEIPT#'} }}</td>
                                <td>{{ $row->{'Name of Customer'} }}</td>
                                <td>{{ $row->AMOUNT }}</td>
                                <td>{{ $row->TENDER }}</td>
                                <td>{{ $row->{'Credit Card Name'} }}</td>
                                <td>{{ $row->{'Credit Card Number'} }}</td>
                                <td>{{ $row->{'CARD TYPE'} }}</td>
                                <td>{{ $row->EDC }}</td>
                                <td>{{ $row->EXPIRY }}</td>
                                <td>{{ $row->{'AP NO'} }}</td>
                                <td>{{ $row->{'Commission %'} }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ $row->{'OP ID'} }}</td>
                                <td>{{ $row->User }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="spinner-overlay" id="spinner" style="display: none;">
        <div class="spinner">
        </div>
    </div>

@endsection

@push('bottom')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.1/js/dataTables.buttons.min.js"></script>
    <!-- JSZip for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <!-- Buttons for Excel export -->
    <script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                placeholder: "Select Store",
            });

            $('#tender_report').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                // scrollY: '400px', // Adjust the height to your needs
                scrollX: true, // Ensure horizontal scrolling if needed
                scrollCollapse: true,
                paging: true,
                fixedHeader: false,
                buttons: [{
                        extend: 'csv',
                        text: '<i class="fa fa-download"></i> Export CSV',
                        className: 'btn custom-button'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-download"></i> Download Excel',
                        className: 'btn custom-button'
                    }
                ],
                "language": {
                    "emptyTable": 
                        `<div style="text-align: center;">
                            <img src="https://cdn-icons-png.flaticon.com/128/9841/9841554.png" alt="No Data Icon" style="width: 70px; margin-bottom: 10px; margin-top: 10px;">
                            <p style='font-size: 14px; color: #3C8DBC; font-weigth: 700;'>No matching Data found.</p>
                        </div>`
                },
                initComplete: function() {
                    // Move buttons to the right side
                    const buttons = $('.dt-buttons').detach();
                    $('.top').append(buttons);
                }
            });
        });

        function selectOnChange(id) {
            const channelSelect = $(`#${id}`).val();
            
            if (channelSelect && channelSelect.includes('')) {
                $(`#${id} option:not(:first)`).prop('disabled', true);
                $(`#${id} option:first`).prop('disabled', false);
            } else if (channelSelect == 'All') {
                $(`#${id} option:not(:first)`).prop('disabled', true);
                $(`#${id} option:first`).prop('disabled', false);
            } else if (channelSelect === null || channelSelect.length === 0) {
                $(`#${id} option:not(:first)`).prop('disabled', false);
                $(`#${id} option:first`).prop('disabled', false);
            } else {
                $(`#${id} option:not(:first)`).prop('disabled', false);
                $(`#${id} option:first`).prop('disabled', true);
            }
        }

        $('#channel').change(function() {
            let concept = $('#concept').val();
            let channel = $('#channel').val();
            const allCustomers = {!! json_encode($all_customers) !!};
            
            if (channel == 'All') {
                const allchannel = $('#all_channels').val().split(',').map(item => item.trim());
                channel = allchannel;
            }

            if (concept == 'All') {
                const allconcept = $('#all_concepts').val().split(',').map(item => item.trim());
                concept = allconcept;
            }

            // Filter customers based on selected channel and concept
            const filteredCustomers = allCustomers.filter(customer => {
                const matchesChannel = Array.isArray(channel) ? 
                    channel.some(ch => customer.cutomer_name.includes(ch)) : 
                    customer.cutomer_name.includes(channel);

                const matchesConcept = Array.isArray(concept) ? 
                    concept.includes(customer.concept) : 
                    customer.concept === concept;

                return matchesChannel && matchesConcept;
            });

            $('#customer-input-container').show();
            const select = $('#customer');
            select.empty(); 

            // Populate select element with filtered customers
            if (filteredCustomers.length > 0) {
                select.append('<option value="All">All</option>'); 
                filteredCustomers.forEach(function(option) {
                    const optionHtml = `<option value="${option.customer_code}">${option.cutomer_name}</option>`;
                    select.append(optionHtml);
                });
            } else {
                select.append('<option disabled>No customers found</option>');
            }

            // Prepare the textarea for all customer codes
            const customerCodes = filteredCustomers.map(customer => customer.customer_code).join(', ');
            $('#all_customer').val(customerCodes);
            $('#customer option:not(:first-child)').prop('disabled', true);
        });

        $('#concept').change(function() {
            let concept = $('#concept').val();
            let channel = $('#channel').val();
            const allCustomers = {!! json_encode($all_customers) !!};

            if (concept == 'All') {
                const allconcept = $('#all_concepts').val().split(',').map(item => item.trim());
                concept = allconcept;
            }

            if (channel == 'All') {
                const allchannel = $('#all_channels').val().split(',').map(item => item.trim());
                channel = allchannel;
            }

            // Filter customers based on selected concept and channel
            const filteredCustomers = allCustomers.filter(customer => {
                const matchesConcept = Array.isArray(concept) ? 
                    concept.includes(customer.concept) : 
                    customer.concept === concept;

                const matchesChannel = Array.isArray(channel) ? 
                    channel.some(ch => customer.cutomer_name.includes(ch)) : 
                    customer.cutomer_name.includes(channel);

                return matchesConcept && matchesChannel;
            });

       
            const select = $('#customer');
            select.empty(); 

            // Populate select element with filtered customers
            if (filteredCustomers.length > 0) {
                select.append('<option value="All">All</option>'); 
                const customerCodes = filteredCustomers.map(customer => customer.customer_code).join(', ');
                
                filteredCustomers.forEach(function(option) {
                    const optionHtml = `<option value="${option.customer_code}">${option.cutomer_name}</option>`;
                    select.append(optionHtml);
                });
                $('#all_customer').val(customerCodes);
            }
        });

        $('#btn-submit').on('click', function(event) {
            event.preventDefault();

            let customer = $('#customer').val();
            const channel = $('#channel').val();
            const concept = $('#concept').val();
            const dateFrom = $('#date_from').val();
            const dateTo = $('#date_to').val();
            const dateFromObj = new Date(dateFrom);
            const dateToObj = new Date(dateTo);
           
            if (customer == 'All') {
                const allcustomer = $('#all_customer').val().split(',').map(item => item.trim().replace('CUS-', ''));
                customer = allcustomer;
            } else{
                if (customer != null){
                    let Newcustomer = $('#customer').val().map(function(item) {
                    return item.replace('CUS-', '');
                    });
                    customer = Newcustomer;
                }
            }

            // Validate individual fields
            let isCustomerValid = validateField(customer, 'customer-input-container', 'customerRequired');
            let isChannelValid = validateField(channel, 'channel-input-container', 'channelRequired');
            let isConceptValid = validateField(concept, 'concept-input-container', 'conceptRequired');

            let isDateFromValid = validateDate(dateFrom, 'date_from', 'dateFromRequired');
            let isDateToValid = validateDate(dateTo, 'date_to', 'dateToRequired');

            function validateField(field, fieldId, errorId) {
                if (field === null || field.length === 0) {
                    $(`#${fieldId}`).find('.select2-container--default .select2-selection--multiple').attr('style', 'border-color: red !important')
                    $(`#${errorId}`).show();
                    return false;
                } else {
                    $(`#${fieldId}`).find('.select2-container--default .select2-selection--multiple').attr('style', 'border-color: #3498db !important')
                    $(`#${fieldId}`).removeClass('inactive');
                    $(`#${errorId}`).hide();
                    return true;
                }
            }

            function validateDate(field, fieldId, errorId){
                if (field == "") {
                    $(`#${fieldId}`).addClass('inactive');
                    $(`#${errorId}`).show();
                    return false;
                } else {
                    $(`#${fieldId}`).removeClass('inactive');
                    $(`#${errorId}`).hide();
                    return true;
                }
            }

            if (dateFromObj > dateToObj) {
                $('#date_from').addClass('inactive');
                $('#date_to').addClass('inactive');
                $('#invalidDateFrom').show();
            }

            if (!isCustomerValid || !isChannelValid || !isConceptValid || !isDateFromValid || !isDateToValid || dateFromObj > dateToObj) {
                return;
            }

            $('#spinner').show();

            $.ajax({
                url: 'generateTender/report',
                method: 'POST',
                data: {
                    customer: customer,
                    dateFrom: dateFrom,
                    dateTo: dateTo,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    $('#date_from').css('border', '1px solid #3C8DBC');
                    $('#date_to').css('border', '1px solid #3C8DBC');
                    $('#invalidDateFrom').hide();
                    $('#rawData').show();
                    const tbody = $('#tender_report tbody');
                    tbody.empty();

                    response.forEach(function(row) {
                        const tr = '<tr>' +
                            '<td>' + row.DATE + '</td>' +
                            '<td>' + row.TIME + '</td>' +
                            '<td>' + row['STORE ID'] + '</td>' +
                            '<td>' + row.customerName + '</td>' +
                            '<td>' + row['Document#'] + '</td>' +
                            '<td>' + row['Invoice Ref No'] + '</td>' +
                            '<td>' + row['Name of Customer'] + '</td>' +
                            '<td>' + row.AMOUNT + '</td>' +
                            '<td>' + row.TENDER + '</td>' +
                            '<td>' + row['Credit Card Name'] + '</td>' +
                            '<td>' + row['Credit Card Number'] + '</td>' +
                            '<td>' + row['CARD TYPE'] + '</td>' +
                            '<td>' + row.EDC + '</td>' +
                            '<td>' + row.EXPIRY + '</td>' +
                            '<td>' + row['AP NO'] + '</td>' +
                            '<td>' + row['Commission %'] + '</td>' +
                            '<td>' + row.mdrCharge + '</td>' +
                            '<td>' + row.netAmount + '</td>' +
                            '<td>' + row['OP ID'] + '</td>' +
                            '<td>' + row.User + '</td>' +
                            '</tr>';
                        tbody.append(tr); // Add the new row to the table body
                    });

                    //for refresh DataTable instance
                    $('#tender_report').DataTable().clear().rows.add(tbody.find('tr')).draw();

                    $('#spinner').hide();
                },
                error: function(xhr, status, error) {
                    alert.error('Error fetching data:', error);
                    $('#spinner').hide();
                }
            });
        });

        $('#btn-reset').on('click', function(event){
            event.preventDefault();

            $('#channel').val(null).trigger('change');
            $('#concept').val(null).trigger('change');
            $('#customer').val(null).trigger('change');
            $('#date_from').val("");
            $('#date_to').val("");
            $('#customerRequired').hide();
            $('#channelRequired').hide();
            $('#conceptRequired').hide();
            $('#invalidDateFrom').hide();
            $('#dateToRequired').hide();
            $('#dateFromRequired').hide();
            $('.select2-container--default .select2-selection--multiple').attr('style', 'border-color: #3498db !important')
            $('#date_from').removeClass('inactive');
            $('#date_to').removeClass('inactive');

        });
    </script>
@endpush
