<div class="ytd-section">
    <h2 class="text-start" style="margin-top:25px;">YTD SALES REPORT</h2>

    <div class="" style="display: flex; flex-wrap:wrap; gap:15px; justify-content:flex-start; align-items:ceneter; margin:20px 0px 8px;">

        <div class="form-group" >
            <label class="control-label" for="channelSelector" >Channel:</label>
            
            <select id="channelSelector" class="form-control">
                <option value="all">All</option>

                @foreach ($channels as $channel)
                    <option value="{{$channel->id}}">{{$channel->channel_name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" >
            <label class="control-label" for="conceptSelector" >Store Concept:</label>
            <select id="conceptSelector" class="form-control">
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