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
@php
    // dump($last_three_days);
    // dd($channel_codes);
@endphp


@section('content')

<div class="dashboard">
    <x-sales-report-top :data="$summary" yearFrom="2023" yearTo="2024" :dataLastThreeDays="$summary_last_three_days"/>


    @foreach ($channel_codes as $channel => $years)
        {{-- @dump($channel)
        @dump($years) --}}
        <x-sales-report :channel="$channel" 
        :dataFrom="$years['2023']['weeks']" 
        :dataTo="$years['2024']['weeks']" 
        yearFrom="2023" 
        yearTo="2024"  
        :dataLastThreeDaysFrom="$years['2023']['last_three_days']"
        :dataLastThreeDaysTo="$years['2024']['last_three_days']"
        
        
        />
    @endforeach

    {{-- @foreach ($channel_code as $channel => $weeks)
      
        <x-sales-report :channel="$channel" :data="$weeks" yearFrom="2023" yearTo="2024" />
    @endforeach --}}
    {{-- <x-sales-report channel="Ecomm" message="test" />
    <x-sales-report channel="Retail" />
    <x-sales-report channel="SC" />
    <x-sales-report channel="Out" />
    <x-sales-report channel="Con" />
    <x-sales-report channel="Fra" />
    <x-sales-report channel="Gashapon" /> --}}
</div>

@endsection

