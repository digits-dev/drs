<?php
namespace App\Imports;

use App\Models\AppleCutoff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use CRUDBooster;

class AppleCutoffImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row) {
            // Validate each row before processing
            if (empty($row['sold_date']) || empty($row['day']) || empty($row['apple_fy']) ||
                empty($row['quarter']) || empty($row['week']) || empty($row['from']) || empty($row['to'])) {
                continue; // Skip this row if validation fails
            }

            // Update or insert the data
            AppleCutoff::updateOrInsert(
                ['sold_date' => date('Y-m-d', strtotime($row['sold_date']))],
                [
                    'sold_date' => date('Y-m-d', strtotime($row['sold_date'])),
                    'day_fy' => $row['day'],
                    'year_fy' => $row['apple_fy'],
                    'quarter_fy' => $row['quarter'],
                    'week_fy' => $row['week'],
                    'apple_yr_qtr_wk' => $row['apple_fy'] . ' ' . $row['quarter'] . ' ' . $row['week'],
                    'from_date' => date('Y-m-d', strtotime($row['from'])),
                    'to_date' => date('Y-m-d', strtotime($row['to'])),
                    'apple_week_cutoff' => date('Y-m-d', strtotime($row['from'])) . ' to ' . date('Y-m-d', strtotime($row['to'])),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );
        }
    }
}

?>
