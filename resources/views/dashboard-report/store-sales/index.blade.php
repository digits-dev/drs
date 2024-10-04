@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

    <style>
        .dashboard{
            background: white;
            padding: 5px 15px;
        }
    </style>
@endpush

@section('content')

{{-- 'yearMonthData' => [
    'year1' => $years[0]['year'],
    'month1' => $years[0]['month'],
    'year2' => $years[1]['year'],
    'month2' => $years[1]['month'],
], --}}

<div class="dashboard">
    <x-sales-report-top 
        :data="$summary" 
        :yearFrom="$yearMonthData['year1']" 
        :yearTo="$yearMonthData['year2']" 
        :dataLastThreeDays="$summary_last_three_days"
        :lastThreeDaysAsDate="$lastThreeDaysAsDate"
        :lastThreeDays="$lastThreeDays"
    />

    @foreach ($channel_codes as $channel => $years)
        @php
            $dataFrom = $years[$yearMonthData['year1']]['weeks'];
            $dataTo = $years[$yearMonthData['year2']]['weeks'];
        @endphp

        @if ($channel == 'OTHER')
            @continue
        @endif
        
        <x-sales-report :channel="$channel" 
            :dataFrom="$dataFrom" 
            :dataTo="$dataTo" 
            :yearFrom="$yearMonthData['year1']" 
            :yearTo="$yearMonthData['year2']"
            :dataLastThreeDaysFrom="$years[$yearMonthData['year1']]['last_three_days']"
            :dataLastThreeDaysTo="$years[$yearMonthData['year2']]['last_three_days']"
            :lastThreeDaysAsDate="$lastThreeDaysAsDate"
        />
    @endforeach

</div>

@endsection

