<?php

namespace App\Http\Controllers;

use App\Models\DigitsSale;
use Illuminate\Http\Request;
use CRUDBooster;

class DigitsSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function show(DigitsSale $digitsSale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function edit(DigitsSale $digitsSale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DigitsSale $digitsSale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DigitsSale  $digitsSale
     * @return \Illuminate\Http\Response
     */
    public function destroy(DigitsSale $digitsSale)
    {
        //
    }

    public function digitsSalesUploadView()
    {
        if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));

        $data = [];
        $data['page_title'] = 'Upload Digits Sales';
        $data['uploadRoute'] = route('digits-sales.upload');
        $data['uploadTemplate'] = route('digits-sales.template');
        $data['nextSeries'] = StoreSale::getNextReference();
        return view('sales.upload',$data);
    }
}
