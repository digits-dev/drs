
<div class="weekly-section">
    <div class="dashboard">
        @foreach ($channel_codes as $channel => $channelData)
            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif

            <x-sales-report 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$prevYear"
                :currYear="$currYear"
                :lastThreeDaysDates="$lastThreeDaysDates"
            />
        @endforeach
    </div>
</div>