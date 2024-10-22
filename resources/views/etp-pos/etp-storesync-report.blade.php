<style>
    table.dataTable tbody tr:first-child {
        background-color: rgba(156, 255, 156, 0.762); 
    }

</style>

<div>
    <button class="btn btn-primary btn-sm" onclick="location.reload()" style="margin-bottom: 15px;"> <i
            class="fa fa-refresh"></i> Refresh</button>

    <table class="table" id="store-sync">
        <thead>
            <tr>
                <th>Store Name</th>
                <th>Last Zread Date</th>
                <th>Last Zread Time</th>
                <th>Store Last DRS Sync</th>
                <th>Inventory Last DRS Sync</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($store_sync_data) && is_array($store_sync_data))
                @foreach ($store_sync_data as $row)
                    <tr>
                        <td>{{ $row->Warehouse }}</td>
                        <td>{{ $row->Date }}</td>
                        <td>{{ $row->Time }}</td>
                        <td>{{ $row->store_last_sync }}</td>
                        <td>{{ $row->inventory_last_sync }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

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

        $('#store-sync').DataTable({
            dom: '<"top"lBf>rt<"bottom"ip><"clear">',
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
            "order": [[1, "desc"], [2, "desc"]], 
            "columnDefs": [
                {
                    "targets": 1, 
                    "type": "date" 
                },
                {
                    "targets": 2, 
                    "type": "time" 
                }
            ],
            initComplete: function() {
                const buttons = $('.dt-buttons').detach();
                $('.top').append(buttons);
            }
        });
    });
</script>
