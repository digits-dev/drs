<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\StoreSalesReport;

class RunRateController extends Controller
{

    public function getMonth(Request $request) {
        $months = StoreSalesReport::where('sales_year', $request->year)
        ->distinct('sales_month')
        ->pluck('sales_month');

        return response()->json($months);
    }

    public function getCutoffRange(Request $request) {
        if ($request->brandGroup == 'APPLE - WEEKLY') {
            $cutoffRange = StoreSalesReport::where('sales_year', $request->year)->where('sales_month', $request->month)
            ->distinct('apple_week_cutoff')
            ->pluck('apple_week_cutoff');
      
        }else {
            $cutoffRange = StoreSalesReport::where('sales_year', $request->year)->where('sales_month', $request->month)
            ->distinct('non_apple_week_cutoff')
            ->pluck('non_apple_week_cutoff');
        }
        return response()->json($cutoffRange);
    }

    public function getConcepts(Request $request) {
        $concept_ids = DB::table('customers')
        ->where('channels_id', $request->channel)
        ->pluck('concepts_id');
        $concepts = DB::table('concepts')
        ->whereIn('id', $concept_ids)
        ->get();
        return response()->json($concepts);
    }
 

    public function getStoreLocation(Request $request) {
        $store_location = DB::table('customers')
        ->where('concepts_id', $request->storeConceptId)
        ->pluck('customer_name');
        return response()->json($store_location);
    }
}