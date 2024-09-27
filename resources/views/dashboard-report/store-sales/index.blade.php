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
    <x-sales-report-top/>
    <x-sales-report channel="Ecomm" message="test" />
    <x-sales-report channel="Retail" />
    <x-sales-report channel="SC" />
    <x-sales-report channel="Out" />
    <x-sales-report channel="Con" />
    <x-sales-report channel="Fra" />
    <x-sales-report channel="Gashapon" />
</div>

@endsection

