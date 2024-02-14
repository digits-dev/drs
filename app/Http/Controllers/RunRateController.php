<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\StoreSalesReport;

class RunRateController extends Controller
{
    
    public function getYear(Request $request) {
        $tableName = ($request->brand == 'APPLE - WEEKLY') ? 'apple_cutoffs' : 'non_apple_cutoffs';
        $year = $this->fetchDistinctYears($tableName);
        return response()->json($year);
    }
    
    public function getMonth(Request $request) {
        $tableName = ($request->brand == 'APPLE - WEEKLY') ? 'apple_cutoffs' : 'non_apple_cutoffs';
        $month = $this->fetchDistinctMonths($tableName, $request->year);
        return response()->json($month);
    }
    
    public function getCutoffRange(Request $request) {
        $tableName = ($request->brandGroup == 'APPLE - WEEKLY') ? 'apple_cutoffs' : 'non_apple_cutoffs';
        $cutoffRange = $this->fetchCutoffRange($tableName, $request->year, $request->month);
        return response()->json($cutoffRange);
    }
    
    private function fetchDistinctYears($tableName) {
        return DB::table($tableName)
            ->selectRaw('DISTINCT YEAR(sold_date) AS year')
            ->pluck('year');
    }
    
    private function fetchDistinctMonths($tableName, $year) {
        $currentYear = date('Y');
        $currentMonth = date('m');

        $query = DB::table($tableName)->where('sold_date', 'like', '%' . $year . '%');
   
        if ($year == $currentYear) {
            $query->whereMonth('sold_date', '<=', $currentMonth);
        }

        return $query->selectRaw('DISTINCT MONTH(sold_date) AS month')->pluck('month');
    }
    
    private function fetchCutoffRange($tableName, $year, $month) {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDate = date('Y-m-d');
        $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
        
        $query =  DB::table($tableName)
            ->whereRaw('DATE_FORMAT(sold_date, "%Y-%m") = ?', [$year . '-' . $formattedMonth]);
          
        if($year == $currentYear && $month == $currentMonth) {
            $query->whereDate('to_date', '<', $currentDate);
        }

        return $query->distinct(($tableName == 'apple_cutoffs') ? 'apple_week_cutoff' : 'non_apple_week_cutoff')
        ->pluck(($tableName == 'apple_cutoffs') ? 'apple_week_cutoff' : 'non_apple_week_cutoff');

    }

    public function getConcepts(Request $request) {
        $concept_ids = DB::table('customers')
        ->where('channels_id', $request->channel)
        ->pluck('concepts_id');
        $concepts = DB::table('concepts')
        ->whereIn('id', $concept_ids)
        ->orderBy('concept_name', 'asc')
        ->get();
        return response()->json($concepts);
    }
 

    public function getStoreLocation(Request $request) {
        $store_location = DB::table('customers')
        ->where('concepts_id', $request->storeConceptId)
        ->whereRaw('RIGHT(customer_name, 3) = ?', [$request->channelCode])
        ->orderBy('customer_name', 'asc')
        ->get();
        return response()->json($store_location);
    }
}