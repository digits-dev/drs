@extends('crudbooster::admin_template')
@push('head')
    <style>
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
            justify-content: space-between; /* Aligns all items to the right */
            align-items: center; /* Vertically centers items */
            margin-bottom: 10px; /* Space between the top and the table */
        }

        .dataTables_wrapper .dt-buttons {
            margin-left: 10px; /* Space between buttons */
        }

        .dataTables_wrapper .dataTables_length {
            margin-right: 10px; /* Space between "Show entries" dropdown and buttons */
        }

        .dataTables_wrapper .dataTables_filter {
            display: flex;
            align-items: center; /* Center search input vertically */
            margin-left: 10px; /* Space between buttons and search */
        }

        .dataTables_wrapper .dataTables_filter label {
            margin-right: 5px; /* Space between label and input */
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
    </style>
@endpush
@section('content')
    <div class="panel panel-default" style="border-radius: 10px">
        <div class="panel-body">

            <div class="panel panel-default">
                <div class="panel-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-4">

                                <label for="">Param1</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="p1" id="p1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                
                        <label for="">Param1</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="p2" id="p2">
                        </div>
                            </div>
                            <div class="col-md-4">
                                <label for="">Action</label>
                                <div class="input-group">
                                    <button class="btn btn-primary btn-sm form-control" id="sBtn">Filter</button>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>

            <div class="panel panel-default" style="padding: 0px; border: none; display: none;" id="rawData">
                <div style="overflow-x: scroll;">
                    <table class="table" id="tender_report">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Store ID</th>
                                <th>Receipt#</th>
                                <th>Name of Customer</th>
                                <th>Amount</th>
                                <th>Tender</th>
                                <th>Credit Card Name</th>
                                <th>Credit Card Number</th>
                                <th>Card Type</th>
                                <th>EDC</th>
                                <th>Expiry</th>
                                <th>AP No</th>
                                <th>Commission %</th>
                                <th>OP ID</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($tender_data) && is_array($tender_data))
                                @foreach ($tender_data as $row)
                                    <tr>
                                        <td>{{ $row->DATE }}</td>
                                        <td>{{ $row->{'STORE ID'} }}</td>
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
                                        <td>{{ $row->{'OP ID'} }}</td>
                                        <td>{{ $row->User }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="spinner-overlay" id="spinner" style="display: none;">
        <div class="spinner">
        </div>
    </div>

@endsection

@push('bottom')
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.3.1/js/dataTables.buttons.min.js"></script>
<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<!-- Buttons for Excel export -->
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tender_report').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                buttons: [
                    {
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
                initComplete: function() {
            // Move buttons to the right side
            const buttons = $('.dt-buttons').detach();
            $('.top').append(buttons);
        }
            });
        });

        $(document).ready(function() {
            const table = $('#tender_report').DataTable();

            $('#tender_report tbody').on('click', 'td', function() {
                const cellData = $(this).text();

                $(this).css('background-color', '#c8e6c9');

                // Check if the Clipboard API is supported
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(cellData)
                        .then(() => {
                            // Create a note element
                            const note = $(
                            '<br><span class="copy-note"> <i class="fa fa-check"></i> Copied </span>');

                            // Append the note to the clicked cell
                            $(this).append(note);

                            // Hide note after 1 second
                            setTimeout(() => {
                                note.remove(); // Remove the note
                            }, 1000);

                            // Reset cell color after 1 second
                            setTimeout(() => {
                                $(this).css('background-color', ''); // Reset background color
                            }, 1000);
                        })
                        .catch(err => {
                            console.error('Failed to copy: ', err);
                        });
                } else {
                    // Fallback for older browsers
                    const textarea = document.createElement('textarea');
                    textarea.value = cellData;
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');

                        // Create a note element
                        const note = $(
                            '<br><span class="copy-note"> <i class="fa fa-check"></i> Copied </span>');

                        // Append the note to the clicked cell
                        $(this).append(note);

                        // Hide note after 1 second
                        setTimeout(() => {
                            note.remove(); // Remove the note
                        }, 1000);

                        // Reset cell color after 1 second
                        setTimeout(() => {
                            $(this).css('background-color', ''); // Reset background color
                        }, 1000);
                    } catch (err) {
                        console.error('Failed to copy using execCommand: ', err);
                    } finally {
                        document.body.removeChild(textarea);
                    }
                }
            });
        });

        $('#sBtn').on('click', function(event) {
            event.preventDefault(); 
            
            var p1 = $('#p1').val();
            var p2 = $('#p2').val();

            $('#spinner').show();

            $.ajax({
                url: 'generateTender/report',
                method: 'POST',
                data: { 
                    p1: p1,
                    p2: p2,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    $('#rawData').show();
                    var tbody = $('#tender_report tbody');
                    tbody.empty(); 

                    response.forEach(function(row) {
                        var tr = '<tr>' +
                            '<td>' + row.DATE + '</td>' +
                            '<td>' + row['STORE ID'] + '</td>' +
                            '<td>' + row['RECEIPT#'] + '</td>' +
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
                            '<td>' + row['OP ID'] + '</td>' +
                            '<td>' + row.User + '</td>' +
                            '</tr>';
                        tbody.append(tr); // Add the new row to the table body
                    });

                    // If you want to refresh the DataTable instance
                    $('#tender_report').DataTable().clear().rows.add(tbody.find('tr')).draw();
                    
                    $('#spinner').hide();
                },
                error: function(xhr, status, error) {
                    alert.error('Error fetching data:', error);
                    $('#spinner').hide();
                }
            });
        });

    </script>
@endpush
