<?php

namespace App\Http\Controllers\ApiPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogReportController extends Controller
{
    /**
     * Sumber log yang tersedia.
     */
    private array $sources = ['obe', 'simortua', 'divisi', 'mahasiswa'];

    /**
     * Tampilkan halaman report log JSON.
     */
    public function index(Request $request)
    {
        $source = $request->query('source', 'obe');
        $date = $request->query('date', now()->format('Y-m'));

        if (! in_array($source, $this->sources, true)) {
            $source = 'obe';
        }

        $entries = $this->readEntries($source, $date);

        return view('api_panel.log_report', [
            'entries' => $entries,
            'sources' => $this->sources,
            'source' => $source,
            'date' => $date,
        ]);
    }

    /**
     * Endpoint JSON untuk AJAX / integrasi lain.
     */
    public function data(Request $request)
    {
        $source = $request->query('source', 'obe');
        $date = $request->query('date', now()->format('Y-m'));

        if (! in_array($source, $this->sources, true)) {
            return response()->json(['error' => 'Invalid source'], 422);
        }

        return response()->json($this->readEntries($source, $date));
    }

    // ---------------------------------------------------------------

    private function readEntries(string $source, string $date): array
    {
        $path = storage_path("logs/json/{$source}-{$date}.json");

        if (! file_exists($path)) {
            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $entries = [];

        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded !== null) {
                $entries[] = $decoded;
            }
        }

        // Terbaru di atas
        return array_reverse($entries);
    }
}
