<div class="monthly-section">
    <div class="dashboard">
        @php
            $hasDTC = isset($channel_codes['DTC']) && $channel_codes['DTC'];
        @endphp

        @foreach ($channel_codes as $channel => $channelData)

            @if ($hasDTC && ($channel == 'OTHER' || $channel == '' || $channel == 'TOTAL'))
                @continue
            @endif

            @if (!$hasDTC && ($channel == 'OTHER' || $channel == ''))
                @continue
            @endif
            
            <x-monthly-sales-report 
                :isTopOpen="$channel == 'TOTAL' || $channel == 'DTC'"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$prevYear"
                :currYear="$currYear"
            />
        @endforeach
    </div>
</div>