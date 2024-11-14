@extends('crudbooster::admin_template')
@push('head')
    <link rel="stylesheet" href="https://saravanajd.github.io/YearPicker/yearpicker.css">
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


       


        .select2-container--default .select2-selection--multiple {
            border-color: #3498db !important;
            border-radius: 7px;
            padding: 6px 0 8px 10px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection__choice {
            background-color: #3498db !important;
            color: #ffffff !important;
            border: 1px solid #2980b9 !important;
        }

        .select2-container--default .select2-selection__choice:hover {
            background-color: #2980b9 !important;
            color: #ffffff !important;

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

        table.dataTable {
            table-layout: fixed;
            width: 100%;
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

        .top {
            position: sticky;
            top: 0;
            background-color: white;
            /* Ensure it blends with your page */
            z-index: 100;
            /* Ensure it appears above other elements */
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .inactive{
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

        .select-container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .dropdowns {
        border: none;
        appearance: none;
        width: 100%;
        padding: 10px;
        border: 1px solid #3C8DBC;
        border-radius: 7px;
        
    }
    .dropdowns:focus {
        border: 2px solid #3C8DBC;
        outline: none; 
    }

    .icon-container {
        position: absolute;
        right: 20px;
        display: flex;
        justify-content: center;
        color: #3C8DBC;
    }

    .yearpicker-container {
        position: absolute; 
        top: 50px;  
        right: 0px;    
        z-index: 9999;
     
    }

    .yearpicker-container .selected {
        color: #3C8DBC !important;   
        font-weight: bold;     
    }

    .yearpicker-year li:hover {
        color: #3C8DBC !important;                
        cursor: pointer;             
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
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Store Name <small id="customerRequired" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i class="fa fa-exclamation-circle"></i> Required field! </small> <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size: 14px; color:#2ad34e; display:none" id="customer-load"></i> </p>
                    <select class="js-example-basic-multiple" id="customer" name="customer[]" multiple="multiple" onchange="selectOnChange('customer')">
                    </select>
                    <textarea id="all_customer" style="display: none"></textarea> 
                </div>
            </div>
            <div class="inputs-container" style="margin-top: 10px;">
                <div class="input-container">
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Month
                        <small id="monthRequired" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i class="fa fa-exclamation-circle"></i> Required field! </small> 
                    </p>
                    <div class="form-group select-container">
                        <select  class="dropdowns" class="form-control" name="month" id="month">
                            <option value="" selected disabled>Select Month</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>

                        </select>
                        <div class="icon-container">
                            <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                
                </div>
                <div class="input-container" style="position: relative;">
                    <p style="padding: 0; margin:0; font-size:14px; font-weight: 500">Year
                        <small id="yearRequired" style="display: none; color: rgba(255, 0, 0, 0.853);"> <i class="fa fa-exclamation-circle"></i> Required field! </small> 
                    </p>
                    <input type="text" id="year-picker" class="dropdowns" name="year-picker" placeholder="Select Year">
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

    <div class="panel panel-default"
        style="overflow:hidden; padding: 15px; border: none; display: none; border-radius: 10px !important;" id="rawData">

        <table class="table" id="bir_report">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Store Name</th>
                    <th>Date</th>
                    <th>Beginning Invoice Number</th>
                    <th>Ending Invoice Number</th>
                    <th>Net Amount</th>
                    <th>Discount</th>
                    <th>Returns</th>
                    <th>Voids</th>
                    <th>Deductions</th>
                    <th>Gross Amount</th>
                    <th>VATable Sales</th>
                    <th>VAT</th>
                    <th>Sales VAT-Exempt</th>
                    <th>Zero Rated</th>
                  
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <th>Company Name</th>
                        <td>Store Name</td>
                        <td>Date</td>
                        <td>Beginning Balance</td>
                        <td>Ending Balance</td>
                        <td>Net Amount</td>
                        <td>Discount</td>
                        <td>Returns</td>
                        <td>Voids</td>
                        <td>Deductions</td>
                        <td>Gross Amount</td>
                        <td>VATable Sales</td>
                        <td>VAT</td>
                        <td>Sales VAT-Exempt</td>
                        <td>Zero Rated</td>
                       
                    </tr>
            </tbody>
        </table>

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
    {{-- Year Picker --}}
    <script src="https://saravanajd.github.io/YearPicker/yearpicker.js"></script>
    <script>
        $(document).ready(function() {
            $('#channel').select2({
                placeholder: "Select Channel",
            });
            $('#concept').select2({
                placeholder: "Select Concept",
            });
            $('#customer').select2({
                placeholder: "Select Store",
            });

            $("#year-picker").yearpicker({     
                startYear: 1900, 
                endYear: 2100    
            });

            $("#year-picker").blur();

            $('#bir_report').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                scrollX: true,
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
                "order": [[0, "desc"], [1, "asc"]], 
                columnDefs: [
                    { "targets": [1, 4, 5, 6, 7, 8, 9, 10, 11, 13 ], "width": "100px" },  // Set 100px width for multiple columns
                    { "targets": [2, 3], "width": "180px" },
                    { "targets": 12, "width": "150px" },
                    { "targets": 0, "width": "280px" },
                    { "targets": 1, "type": "date" }
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

            $('#bir_report tbody').on('click', 'td', function() {
                const cell = $(this);
                let cellData = cell.text().trim();  
                cellData = cellData.replace("Copied!", "").trim();

                if (cellData !== "") {
                    $('.popover-tooltip').remove();
                    clearTimeout(cell.data('timeout'));

                    cell.css('background-color', 'lightgray').css('color', '#28A745');

                    const popover = $('<small class="popover-tooltip"> <i class="fa fa-check"></i> Copied!</small>');
                    $('body').append(popover); // Append popover to the body

                    // Get the cell's position relative to the document
                    const cellOffset = cell.offset();

                    popover.css({
                        position: 'absolute',
                        top: cellOffset.top - 20 + 'px', 
                        left: cellOffset.left + (cell.outerWidth() / 2) - (popover.outerWidth() / 2) + 'px',
                        backgroundColor: '#28A745',
                        color: '#fff',
                        borderRadius: '5px',
                        padding: '5px 10px',
                        boxShadow: '0px 4px 8px rgba(0, 0, 0, 0.2)',
                        fontSize: '11px',
                        whiteSpace: 'nowrap',
                        display: 'block',
                        opacity: 1,
                        zIndex: 9999 
                    });

                    const timeout = setTimeout(() => {
                        popover.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 500);

                    cell.data('timeout', timeout);

                    // Clipboard API logic
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(cellData)
                            .then(() => {
                                setTimeout(() => {
                                    cell.css('background-color', '').css('color', ''); 
                                }, 1000);
                            })
                            .catch(err => {
                                alert('Failed to copy: ', err);
                            });
                    } else {
                        const textarea = document.createElement('textarea');
                        textarea.value = cellData;
                        document.body.appendChild(textarea);
                        textarea.select();
                        try {
                            if (cellData !== 'Copied!') { 
                                document.execCommand('copy');
                            }
                        } catch (err) {
                            alert('Failed to copy using execCommand: ', err);
                        } finally {
                            document.body.removeChild(textarea);
                        }

                        setTimeout(() => {
                            cell.css('background-color', '').css('color', '');
                        }, 500);
                    }
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

        
        $('#btn-reset').on('click', function(event){
            event.preventDefault();

            $('#channel').val(null).trigger('change');
            $('#concept').val(null).trigger('change');
            $('#customer').val(null).trigger('change');
            $('#month').val(null).trigger('change');
            $('#year-picker').val(null).trigger('change');
            $('#customerRequired').hide();
            $('#channelRequired').hide();
            $('#conceptRequired').hide();
            $('#monthRequired').hide();
            $('#yearRequired').hide();
            $('.select2-container--default .select2-selection--multiple').attr('style', 'border-color: #3498db !important')
            $('#month').css('border-color', '#3498db');
            $('#year-picker').css('border-color', '#3498db');

        });


        $('#btn-submit').on('click', function(event) {
            event.preventDefault();

            let customer = $('#customer').val();
            const channel = $('#channel').val();
            const concept = $('#concept').val();
            const month = $('#month').val();
            const year = $('#year-picker').val();

            if (customer == 'All'){
                const allcustomer = $('#all_customer').val().split(',').map(item => item.trim());
                customer = allcustomer;  
            }

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

            function validateDate(field, fieldId, errorId) {
                if (field === null || field.length === 0) {
                    $(`#${fieldId}`).css('border-color', 'red');
                    $(`#${errorId}`).show();
                    return false;
                } else {
                    $(`#${fieldId}`).css('border-color', ''); // Reset border color or set it to default
                    $(`#${fieldId}`).removeClass('inactive');
                    $(`#${errorId}`).hide();
                    return true;
                }
            }

            // Validate individual fields
            let isCustomerValid = validateField(customer, 'customer-input-container', 'customerRequired');
            let isChannelValid = validateField(channel, 'channel-input-container', 'channelRequired');
            let isConceptValid = validateField(concept, 'concept-input-container', 'conceptRequired');
            let isMonthValid = validateDate(month, 'month', 'monthRequired');
            let isYearValid = validateDate(year, 'year-picker', 'yearRequired');



            if (!isCustomerValid || !isChannelValid || !isConceptValid || !isMonthValid || !isYearValid) {
                return; 
            }

            $('#spinner').show();

            $.ajax({
                url: 'generate_bir/report',
                method: 'POST',
                data: { 
                    customer: customer,
                    month: month,
                    year: year,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    $('#rawData').show();
                    const tbody = $('#bir_report tbody');
                    tbody.empty(); 

                    console.log(response);

                    response.forEach(function(row) {
                        const tr = '<tr>' +
                            '<td>' + row['CompanyName'] + '</td>' +   
                            '<td>' + row['CustomerName'] + '</td>' +               // Store Name
                            '<td>' + row['CreateDate'] + '</td>' +              // Date
                            '<td>' + row['DocRangeFrom'] + '</td>' +            // Beginning Invoice Number 
                            '<td>' + row['DocRangeTo'] + '</td>' +              // Ending Invoice Number
                            '<td>' + row['NetAmount'] + '</td>' +             // Net Amount
                            '<td>' + row['Discount'] + '</td>' +                                  // Discount 
                            '<td>' + row['Returns'] +'</td>' +                                  // Returns
                            '<td>' + row['Voids'] +'</td>' +                                  // Voids
                            '<td>' + row['Deductions'] +'</td>' +                                  // Deductions
                            '<td>' + row['GrossTotalAmt'] + '</td>' +           // Gross Amount 
                            '<td>' + row['VatableTotalAmt'] + '</td>' +         // VATable Sales
                            '<td>' + row['VatTotalAmt'] + '</td>' +             // VAT
                            '<td>' + row['SalesVatExmptAmt'] + '</td>' +        // Sales VAT-Exempt
                            '<td>' + row['ZeroRatedSalesAmt'] + '</td>' +       // Zero Rated
                            '</tr>';
                        tbody.append(tr);
                    });

                    // If you want to refresh the DataTable instance
                    $('#bir_report').DataTable().clear().rows.add(tbody.find('tr')).draw();

                    $('#spinner').hide();
                },
                error: function(xhr, status, error) {
                    alert('Error fetching data:', error);
                    $('#spinner').hide();
                }
            });
        });
    </script>
@endpush
