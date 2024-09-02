<?php
namespace App\Imports;

use App\Models\NonAppleCutoff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use CRUDBooster;

class NonAppleCutoffImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row) {
            // Validate each row before processing
            if (empty($row['sold_date']) || empty($row['day']) || empty($row['apple_cy']) ||
                empty($row['quarter']) || empty($row['week']) || empty($row['from']) || empty($row['to'])) {
                continue; // Skip this row if validation fails
            }

            // Update or insert the data
            NonAppleCutoff::updateOrInsert(
                ['sold_date' => date('Y-m-d', strtotime($row['sold_date']))],
                [
                    'sold_date' => date('Y-m-d', strtotime($row['sold_date'])),
                    'day_cy' => $row['day'],
                    'year_cy' => $row['apple_cy'],
                    'month_cy' => $row['quarter'],
                    'week_cy' => $row['week'],
                    'non_apple_yr_mon_wk' => $row['apple_cy'] . ' ' . $row['quarter'] . ' ' . $row['week'],
                    'from_date' => date('Y-m-d', strtotime($row['from'])),
                    'to_date' => date('Y-m-d', strtotime($row['to'])),
                    'non_apple_week_cutoff' => date('Y-m-d', strtotime($row['from'])) . ' to ' . date('Y-m-d', strtotime($row['to'])),
                    'created_by' => CRUDBooster::myId(),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );
        }
    }
}

?>
