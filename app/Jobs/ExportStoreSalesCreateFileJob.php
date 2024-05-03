<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Exports\StoreTestExportBatches;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\StoreSale;
use Illuminate\Support\Facades\Storage;
use Excel;

class ExportStoreSalesCreateFileJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $chunkSize;
    private $folder;
    public function __construct($chunkSize, $folder) {
        $this->chunkSize = $chunkSize;
        $this->folder = $folder;
    }

    public function handle(){
        $storeSales = StoreSale::query()
            ->take($this->chunkSize)
            ->get();

        Storage::disk('local')->makeDirectory($this->folder);

        (new \Rap2hpoutre\FastExcel\FastExcel($this->salesGenerator($storeSales)))
            ->export(storage_path("app/{$this->folder}/storeSales.csv"), function ($sale) {
                return [
                    'id' => $sale->id,
                    'reference_number ' => $sale->reference_number
                ];
            });
    }

    private function salesGenerator($sales){
        foreach ($sales as $sale) {
            yield $sale;
        }
    }
}



