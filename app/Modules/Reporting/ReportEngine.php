<?php

namespace Tomvis\HIMPro\Modules\Reporting;

class ReportEngine
{
    public static function getThaiFiscalYear(string $date): int
    {
        $time = strtotime($date);
        $month = (int)date('n', $time);
        $yearBE = (int)date('Y', $time) + 543;

        // Thai fiscal year starts in October (Month 10)
        return ($month >= 10) ? ($yearBE + 1) : $yearBE;
    }

    public static function exportCsv(array $data, array $headers): string
    {
        $output = fopen('php://temp', 'r+');
        // Add UTF-8 BOM for Thai Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers);

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
