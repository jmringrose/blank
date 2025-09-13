<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class EmailLogController extends Controller
{
    public function index()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return response()->json([]);
        }

        $output = shell_exec("tail -1000 {$logFile}");
        if (!$output) {
            return response()->json([]);
        }

        $lines = explode("\n", $output);
        $logs = [];

        // Parse 3-line pattern logs
        for ($i = 0; $i < count($lines) - 2; $i++) {
            $line1 = $lines[$i] ?? '';
            $line2 = $lines[$i + 1] ?? '';
            $line3 = $lines[$i + 2] ?? '';

            if (strpos($line1, 'Building newsletter email') !== false &&
                strpos($line2, 'Sequence data:') !== false &&
                strpos($line3, 'Newsletter email built successfully') !== false) {

                $logs[] = $this->parseThreeLineLog($line2, $line3);
            }
        }

        // Parse single line email send logs
        foreach ($lines as $line) {
            if (strpos($line, 'Newsletter email sent') !== false || 
                strpos($line, 'Marketing email sent') !== false || 
                strpos($line, 'Question email sent') !== false) {
                
                $logs[] = $this->parseSingleLineLog($line);
            }
        }

        // Sort by time and return last 10
        usort($logs, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return response()->json(array_slice($logs, 0, 10));
    }

    private function parseThreeLineLog($line2, $line3)
    {
        preg_match('/\[(.*?)\]/', $line3, $timeMatch);
        preg_match('/"first":"(.*?)"/', $line2, $firstMatch);
        preg_match('/"last":"(.*?)"/', $line2, $lastMatch);
        preg_match('/"email":"(.*?)"/', $line2, $emailMatch);
        preg_match('/"current_step":(\d+)/', $line2, $stepMatch);

        return [
            'time' => $timeMatch[1] ?? '',
            'who' => trim(($firstMatch[1] ?? '') . ' ' . ($lastMatch[1] ?? '')),
            'email' => $emailMatch[1] ?? '',
            'what' => 'Newsletter Step ' . ($stepMatch[1] ?? '?'),
            'when' => Carbon::parse($timeMatch[1] ?? now())->diffForHumans()
        ];
    }

    private function parseSingleLineLog($line)
    {
        preg_match('/\[(.*?)\]/', $line, $timeMatch);
        preg_match('/"recipient_name":"(.*?)"/', $line, $nameMatch);
        preg_match('/"recipient_email":"(.*?)"/', $line, $emailMatch);
        preg_match('/"step_number":(\d+)/', $line, $stepMatch);
        preg_match('/"step_title":"(.*?)"/', $line, $titleMatch);

        $type = strpos($line, 'Newsletter') !== false ? 'Newsletter' : 
               (strpos($line, 'Question') !== false ? 'Question' : 'Marketing');

        return [
            'time' => $timeMatch[1] ?? '',
            'who' => $nameMatch[1] ?? '',
            'email' => $emailMatch[1] ?? '',
            'what' => $type . ' Step ' . ($stepMatch[1] ?? '') . ': ' . ($titleMatch[1] ?? ''),
            'when' => Carbon::parse($timeMatch[1] ?? now())->diffForHumans()
        ];
    }
}