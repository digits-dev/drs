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

class AppendMoreStoreSalesJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $chunkIndex;
    private $chunkSize;
    private $folder;
    public function __construct($chunkIndex, $chunkSize,$folder) {
        $this->chunkIndex = $chunkIndex;
        $this->chunkSize = $chunkSize;
        $this->folder = $folder;
    }

    public function handle()
    {
        $storeSales = StoreSale::query()
            ->skip($this->chunkIndex * $this->chunkSize)
            ->take($this->chunkSize)
            ->get()
            ->map(function ($sale) {
                return [
                    $sale->id,
                    $sale->reference_number,
                ];
            });

        $file = storage_path("app/{$this->folder}/storeSales.csv");
        $open = fopen($file, 'a+');
        foreach ($storeSales as $sale) {
            fputcsv($open, $sale);
        }
        fclose($open);
    }
}
