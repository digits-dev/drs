

<div class="ytd-section">
    <h2 class="text-start" style="margin-top:25px;">YTD SALES REPORT</h2>

    <div class="" style="display: flex; flex-wrap:wrap; gap:15px; justify-content:flex-start; align-items:ceneter; margin:20px 0px 8px;">

        <div class="form-group" >
            <label class="control-label" for="channelSelector" >Channel:</label>
            
            <select id="channelSelector" class="form-control" style="width:150px;">
                <option value="all">All</option>

                @foreach ($channels as $channel)
                    <option value="{{$channel->id}}">{{$channel->channel_name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" >
            <label class="control-label" for="conceptSelector" >Store Concept:</label>
            <select id="conceptSelector" class="form-control" style="width:250px;">
                <option value="all">All</option>

                @foreach ($concepts as $concept)
                    <option value="{{$concept->id}}">{{$concept->concept_name}}</option>
                @endforeach
            </select>
        </div>
    
        <button id="updateTableButton" class="btn btn-primary" style="align-self: center; margin-top:8px; height:30px;">
            <i class="fa fa-refresh" aria-hidden="true"></i> Update Table
        </button>
    </div>

    <div id="loading2" class="text-center"  style="display:none;">
        <div class="loader"></div>
        <p>Loading, please wait...</p>
    </div>

    <div id="ytdSalesReportContainer">
        <x-ytd-sales-report 
            :prevYear="$prevYear"
            :currYear="$currYear"
            :month="$month"
            :prevYearYTDData="$channel_codes['TOTAL'][$prevYear]['ytd']"
            :currYearYTDData="$channel_codes['TOTAL'][$currYear]['ytd']"
        />
    </div>

</div>

<script>
 
    $('#channelSelector').select2();
    $('#conceptSelector').select2();

    $('#updateTableButton').on('click', function() {

        $('#loading2').show();
        $('#ytdSalesReportContainer').hide();
        
        const selectedChannel = $('#channelSelector').val();
        const selectedConcept = $('#conceptSelector').val();

        $.ajax({
            url: '/admin/ytd_update',
            type: 'POST',
            data: {
                channel: selectedChannel,
                concept: selectedConcept,
                _token: '{{ csrf_token() }}' 
            },
            success: function(data) {
                $('#loading2').hide();


                console.log(data);

                // Fallback values
                const currData = {
                    apple: data.currApple || 0,
                    nonApple: data.currNonApple || 0,
                    totalApple: data.currTotalApple || 0,
                };

                const prevData = {
                    apple: data.prevApple || 0,
                    nonApple: data.prevNonApple || 0,
                    totalApple: data.prevTotalApple || 0,
                };

                // Format numbers with two decimal places
                const formatNumber = (num) => {
                    return Number(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                // Calculate increments and percentage changes
                const calculateChanges = (curr, prev) => {
                    const change = curr - prev;
                    const percentageChange = prev ? ((change / prev) * 100) : 0;
                    return {
                        change,
                        percentageChange,
                        roundedValue: Math.round(percentageChange) + '%',
                        style: percentageChange < 0 ? 'background:#FEC8CE !important; color:darkred !important;' : ''
                    };
                };

                const appleChanges = calculateChanges(currData.apple, prevData.apple);
                const nonAppleChanges = calculateChanges(currData.nonApple, prevData.nonApple);
                const totalChanges = calculateChanges(currData.totalApple, prevData.totalApple);

                // Update the DOM
                const updateDOM = (selector, value) => {
                    $(selector).text(value ? formatNumber(value) : '');
                };

                updateDOM('#prevApple', prevData.apple);
                updateDOM('#prevNonApple', prevData.nonApple);
                updateDOM('#prevTotalApple', prevData.totalApple);
                updateDOM('#currApple', currData.apple);
                updateDOM('#currNonApple', currData.nonApple);
                updateDOM('#currTotalApple', currData.totalApple);
                updateDOM('#incDecApple', appleChanges.change);
                updateDOM('#incDecNonApple', nonAppleChanges.change);
                updateDOM('#incDecTotal', totalChanges.change);

                // Update percentage change displays
                $('#percentageChangeApple').text(appleChanges.roundedValue).attr('style', appleChanges.style);
                $('#percentageChangeNonApple').text(nonAppleChanges.roundedValue).attr('style', nonAppleChanges.style);
                $('#percentageChangeTotal').text(totalChanges.roundedValue).attr('style', totalChanges.style);

                $('#ytdSalesReportContainer').show();

            },
            error: function(xhr, status, error) {
                $('#loading2').hide();
                $('#ytdSalesReportContainer').show();

                console.error('Error:', error);
            }
        });

    });
</script>