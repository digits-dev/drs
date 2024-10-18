<div class="monthly-section">
    <div class="dashboard">
        @foreach ($channel_codes as $channel => $channelData)
            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-monthly-sales-report 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$prevYear"
                :currYear="$currYear"
            />
        @endforeach
    </div>
</div>