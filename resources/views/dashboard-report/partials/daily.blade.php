
<div class="weekly-section">
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

            <x-daily-sales-report 
                :isTopOpen="$channel == 'TOTAL' || $channel == 'DTC'"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$prevYear"
                :currYear="$currYear"
                :lastThreeDaysDates="$lastThreeDaysDates"
            />
        @endforeach
    </div>
</div>