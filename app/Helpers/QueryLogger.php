<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class QueryLogger
{
    public static function logQuery($query)
    {
        // Get the SQL query and bindings
        $querySql = $query->toSql();
        $bindings = $query->getBindings();

        // Format the SQL query for better readability and uppercase keywords
        $formattedQuery = preg_replace_callback(
            '/\b(SELECT|FROM|JOIN|WHERE|GROUP BY|ORDER BY|LEFT JOIN|RIGHT JOIN|HAVING|ON|AND|OR)\b/i',
            function ($matches) {
                return strtoupper($matches[0]); // Convert the matched keyword to uppercase
            },
            $querySql
        );

        // Further format by adding line breaks before SQL keywords
        $formattedQuery = preg_replace(
            '/\s+(SELECT|FROM|JOIN|WHERE|GROUP BY|ORDER BY|LEFT JOIN|RIGHT JOIN|HAVING|ON|AND|OR)/i',
            "\n$1",
            $formattedQuery
        );

        // Format with indentation for better structure
        $formattedQuery = preg_replace(
            '/(\n)(\s*)(SELECT|FROM|JOIN|WHERE|GROUP BY|ORDER BY|LEFT JOIN|RIGHT JOIN|HAVING|ON|AND|OR)/i',
            "\n$2    $3", // Indent the keywords
            $formattedQuery
        );

        // Log the formatted query and bindings
        Log::info(json_encode([
            'SQL Query' => ['query' => $formattedQuery, 'bindings' => $bindings],
        ], JSON_PRETTY_PRINT));
    }
}
