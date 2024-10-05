@extends('crudbooster::admin_template')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" integrity="sha256-F2TGXW+mc8e56tXYBFYeucG/SgD6qQt4SNFxmpVXdUk=" crossorigin="anonymous">

    <style>
        /* .dashboard{
            background: white;
            padding: 5px 15px;
        } */

        .export{
            padding:10px;
            display: flex;
            gap:10px;
            justify-content: flex-end;
            border-bottom: 1px solid #ddd;
        }

        .weekly-section{
            background: white;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
@endpush

@section('content')

<div class="weekly-section">

    <div class="export">
        <a href="{{route('weekly_export_excel')}}" id="search-filter" class="btn btn-primary btn-sm pull-right">
            <i class="fa fa-download" aria-hidden="true"></i> Export to Excel
        </a>
        <a href="" id="search-filter" class="btn btn-primary btn-sm pull-right" >
            <i class="fa fa-download" aria-hidden="true"></i> Export to PDF
        </a>
    </div>
    
    <div class="dashboard">
     
        @foreach ($channel_codes as $channel => $channelData)

            @if ($channel == 'OTHER' || $channel == '')
                @continue
            @endif
            
            <x-sales-report 
                :isTopOpen="$loop->first"
                :channel="$channel" 
                :data="$channelData"
                :prevYear="$yearData['previousYear']" 
                :currYear="$yearData['currentYear']"
                :lastThreeDaysDates="$lastThreeDaysDates"

            />
          
        @endforeach

    </div>
</div>







@endsection

