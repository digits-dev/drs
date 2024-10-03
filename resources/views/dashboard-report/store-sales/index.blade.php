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
            :dataLastThreeDaysFrom="$years['2023']['last_three_days']"
            :dataLastThreeDaysTo="$years['2024']['last_three_days']"
            :lastThreeDaysAsDate="$lastThreeDaysAsDate"
        />
    @endforeach

</div>

@endsection

