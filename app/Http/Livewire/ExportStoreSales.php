<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Jobs\ExportStoreSalesCreateFileJob;
use App\Jobs\AppendMoreStoreSalesJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use App\Models\StoreSale;

class ExportStoreSales extends Component
{
    
    public $batchId;
    public $exporting = false;
    public $exportFinished = false;

    public function sales(){
   
        $this->exporting = true;
        $this->exportFinished = false;

        $chunkSize = 10000;
        $storeSalesCount = StoreSale::count();
        $numberOfChunks = ceil($storeSalesCount / $chunkSize);

        $folder = now()->toDateString() . '-' . str_replace(':', '-', now()->toTimeString());
    
        $batches = [
            new ExportStoreSalesCreateFileJob($chunkSize, $folder)
        ];
    
        if ($storeSalesCount > $chunkSize) {
            $numberOfChunks = $numberOfChunks - 1;
            for ($numberOfChunks; $numberOfChunks > 0; $numberOfChunks--) {
                $batches[] = new AppendMoreStoreSalesJob($numberOfChunks, $chunkSize, $folder);
            }
        }
    
        Bus::batch($batches)
            ->name('Export Users')
            ->then(function (Batch $batch) use ($folder) {
                $path = "exports/{$folder}/storeSales.csv";
                // upload file to s3
                $file = storage_path("app/{$folder}/storeSales.csv");
                Storage::disk('s3')->put($path, file_get_contents($file));
                // send email to admin
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // send email to admin or log error
            })
            ->finally(function (Batch $batch) use ($folder) {
                // delete local file
                Storage::disk('local')->deleteDirectory($folder);
            })
            ->dispatch();

        $this->batchId = $batch->id;
    }

    public function getExportBatchProperty()
    {
        if (!$this->batchId) {
            return null;
        }

        return Bus::findBatch($this->batchId);
    }

    public function downloadExport()
    {
        return response()->download(storage_path("app/2024-05-02-17-03-11/storeSales.csv"));
        // return Storage::download(storage_path("app/2024-05-02-16-33-11/storeSales.csv"));
    }

    public function updateExportProgress()
    {
        $this->exportFinished = $this->exportBatch->finished();

        if ($this->exportFinished) {
            $this->exporting = false;
        }
    }

    public function render()
    {
        return view('livewire.store-sales');
    }
}
