<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class ImportSqlDoc extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string|UnitEnum|null $navigationGroup = 'Settings Documentation';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Import SQL';

    protected static ?string $title = 'Import SQL';

    protected string $view = 'filament.pages.import-sql-doc';

    public static function getSlug(\Filament\Panel $panel = null): string
    {
        return 'import-sql';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('sql_file')
                    ->label('File SQL')
                    ->helperText('Upload file .sql berisi data (VALUES) tabel categories dan api_docs. Hanya statement INSERT INTO yang diproses; DDL ditolak. Data lama akan di-truncate lalu di-import.')
                    ->maxSize(2048)
                    ->storeFiles(false)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function import(): void
    {
        $data = $this->form->getState();
        $path = $data['sql_file'] ?? null;

        if (! $path || ! is_file($path)) {
            Notification::make()
                ->title('File SQL tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        $sql = (string) file_get_contents($path);

        try {
            $result = $this->parseAndImport($sql);

            Notification::make()
                ->title('Import selesai')
                ->body(
                    'Kategori: '.$result['categories'].' | API Docs: '.$result['api_docs']
                    .($result['rejected'] > 0 ? ' | Statement ditolak: '.$result['rejected'] : ''),
                )
                ->success()
                ->send();

            $this->form->fill();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Import gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () {
            echo $this->generateSql();
        }, 'doc.sql', ['Content-Type' => 'application/sql']);
    }

    /**
     * Generate file SQL berisi data categories & api_docs saat ini.
     */
    public function generateSql(): string
    {
        $escape = fn ($value) => $value === null
            ? 'NULL'
            : "'".str_replace(['\\', "'"], ['\\\\', "\\'"], (string) $value)."'";

        $lines = [];
        $lines[] = '-- =====================================================================';
        $lines[] = '-- SISKA API Documentation — Data Dump';
        $lines[] = '-- Tabel: categories & api_docs';
        $lines[] = '-- Hanya berisi data (VALUES), bukan struktur tabel.';
        $lines[] = '-- =====================================================================';
        $lines[] = '';

        $categories = DB::table('categories')->orderBy('id')->get();
        $values = $categories->map(fn ($r) => '('
            .$escape($r->id).', '
            .$escape($r->name).', '
            .$escape($r->description).', '
            .$escape($r->created_at).', '
            .$escape($r->updated_at)
            .')')->all();
        $lines[] = 'INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES';
        $lines[] = implode(','.PHP_EOL, $values).';';
        $lines[] = '';

        $docs = DB::table('api_docs')->orderBy('id')->get();
        $values = $docs->map(fn ($r) => '('
            .$escape($r->id).', '
            .$escape($r->category_id).', '
            .$escape($r->judul).', '
            .$escape($r->description).', '
            .$escape($r->endpoint).', '
            .$escape($r->response).', '
            .$escape($r->created_at).', '
            .$escape($r->updated_at)
            .')')->all();
        $lines[] = 'INSERT INTO `api_docs` (`id`, `category_id`, `judul`, `description`, `endpoint`, `response`, `created_at`, `updated_at`) VALUES';
        $lines[] = implode(','.PHP_EOL, $values).';';
        $lines[] = '';

        return implode(PHP_EOL, $lines);
    }

    /**
     * Parse & import hanya statement INSERT INTO untuk whitelist tabel.
     *
     * @return array{categories:int, api_docs:int, rejected:int}
     */
    private function parseAndImport(string $sql): array
    {
        $allowedTables = ['categories', 'api_docs'];
        $counts = ['categories' => 0, 'api_docs' => 0];
        $rejected = 0;

        // Bersihkan komentar (hanya di luar string literal)
        $sql = $this->stripComments($sql);

        // Split per statement (;) di luar string literal
        $statements = $this->splitStatements($sql);

        // Seleksi statement INSERT yang valid untuk whitelist tabel
        $validInserts = [];
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '') {
                continue;
            }

            if (! preg_match('/^INSERT\s+INTO\s+`?([a-z_]+)`?\s*/i', $statement, $m)) {
                $rejected++;

                continue;
            }

            $table = strtolower($m[1]);
            if (! in_array($table, $allowedTables, true)) {
                $rejected++;

                continue;
            }

            $validInserts[$table][] = $statement;
        }

        // Tidak ada INSERT valid sama sekali -> tolak tanpa menyentuh data
        if ($validInserts === []) {
            throw new \RuntimeException(
                'File tidak mengandung statement INSERT INTO yang valid untuk tabel categories/api_docs. Tidak ada data yang diubah.',
            );
        }

        DB::beginTransaction();

        try {
            // Truncate hanya tabel yang benar-benar ada INSERT-nya di file
            // (urutkan child dulu karena FK category_id)
            if (isset($validInserts['api_docs'])) {
                DB::table('api_docs')->delete();
            }
            if (isset($validInserts['categories'])) {
                DB::table('categories')->delete();
            }

            foreach ($validInserts as $table => $statements) {
                foreach ($statements as $statement) {
                    $counts[$table] += $this->executeInsert($statement);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return [
            'categories' => $counts['categories'],
            'api_docs' => $counts['api_docs'],
            'rejected' => $rejected,
        ];
    }

    /**
     * Hapus komentar SQL (--, #, /* *​/) hanya di luar string literal,
     * agar isi data yang mengandung '#'/'--' (mis. markdown '## GET ...') tidak rusak.
     */
    private function stripComments(string $sql): string
    {
        $result = '';
        $inString = false;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];

            if ($inString) {
                $result .= $char;

                if ($char === '\\' && $i + 1 < $len) {
                    $result .= $sql[++$i];

                    continue;
                }

                if ($char === "'") {
                    $inString = false;
                }

                continue;
            }

            // -- comment (butuh spasi/akhir baris setelah --, sesuai MySQL)
            if ($char === '-' && $i + 1 < $len && $sql[$i + 1] === '-') {
                while ($i < $len && $sql[$i] !== "\n" && $sql[$i] !== "\r") {
                    $i++;
                }

                continue;
            }

            // # comment
            if ($char === '#') {
                while ($i < $len && $sql[$i] !== "\n" && $sql[$i] !== "\r") {
                    $i++;
                }

                continue;
            }

            // /* block comment */
            if ($char === '/' && $i + 1 < $len && $sql[$i + 1] === '*') {
                $i += 2;
                while ($i + 1 < $len && ! ($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                    $i++;
                }
                $i++;

                continue;
            }

            if ($char === "'") {
                $inString = true;
            }

            $result .= $char;
        }

        return $result;
    }

    /**
     * Split statement SQL per titik koma, tidak membelah yang ada di dalam string.
     *
     * @return string[]
     */
    private function splitStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inString = false;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];

            if ($inString) {
                $buffer .= $char;

                if ($char === '\\' && $i + 1 < $len) {
                    $buffer .= $sql[++$i];

                    continue;
                }

                if ($char === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($char === "'") {
                $inString = true;
                $buffer .= $char;

                continue;
            }

            if ($char === ';') {
                $statements[] = $buffer;
                $buffer = '';

                continue;
            }

            $buffer .= $char;
        }

        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }

        return $statements;
    }

    /**
     * Parsing baris VALUES dari statement INSERT dan insert via query builder.
     *
     * Format didukung:
     *   INSERT INTO `table` (`col1`, `col2`) VALUES ('a', 'b'), ('c', NULL);
     */
    private function executeInsert(string $statement): int
    {
        if (! preg_match('/^INSERT\s+INTO\s+`?([a-z_]+)`?\s*\(([^)]*)\)\s*VALUES\s*(.*)$/is', $statement, $m)) {
            return 0;
        }

        $table = strtolower($m[1]);
        $columnsRaw = $m[2];
        $valuesRaw = $m[3];

        $columns = array_map(fn ($c) => trim($c, " `"), explode(',', $columnsRaw));
        $rows = $this->parseValues($valuesRaw);

        foreach ($rows as $row) {
            $record = [];
            foreach ($columns as $i => $column) {
                $record[$column] = $row[$i] ?? null;
            }

            DB::table($table)->insert($record);
        }

        return count($rows);
    }

    /**
     * Parse tuple VALUES: (..), (..)
     *
     * @return array<int, array<int, mixed>>
     */
    private function parseValues(string $valuesRaw): array
    {
        $rows = [];
        $tuples = $this->splitTopLevel($valuesRaw, '(', ')');

        foreach ($tuples as $tuple) {
            $content = trim($tuple, " \t\r\n");
            if ($content === '') {
                continue;
            }

            $row = $this->parseTupleValues($content);
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Split karakter top-level (tidak di dalam string/urutan kurung).
     *
     * @return string[]
     */
    private function splitTopLevel(string $text, string $open, string $close): array
    {
        $parts = [];
        $buffer = '';
        $depth = 0;
        $inString = false;
        $len = strlen($text);

        for ($i = 0; $i < $len; $i++) {
            $char = $text[$i];

            if ($inString) {
                $buffer .= $char;

                if ($char === '\\' && $i + 1 < $len) {
                    $buffer .= $text[++$i];

                    continue;
                }

                if ($char === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($char === "'") {
                $inString = true;
                $buffer .= $char;

                continue;
            }

            if ($char === $open) {
                $depth++;
                if ($depth === 1) {
                    $buffer = '';

                    continue;
                }
            }

            if ($char === $close) {
                $depth--;
                if ($depth === 0) {
                    $parts[] = $buffer;
                    $buffer = '';

                    continue;
                }
            }

            $buffer .= $char;
        }

        return $parts;
    }

    /**
     * Parse nilai literal dalam satu tuple: 'abc', 123, NULL
     *
     * @return array<int, mixed>|null
     */
    private function parseTupleValues(string $content): ?array
    {
        $values = [];
        $buffer = '';
        $inString = false;
        $len = strlen($content);

        for ($i = 0; $i < $len; $i++) {
            $char = $content[$i];

            if ($inString) {
                $buffer .= $char;

                if ($char === '\\' && $i + 1 < $len) {
                    $buffer .= $content[++$i];

                    continue;
                }

                if ($char === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($char === "'") {
                $inString = true;
                $buffer .= $char;

                continue;
            }

            if ($char === ',') {
                $values[] = $this->literalToValue($buffer);
                $buffer = '';

                continue;
            }

            $buffer .= $char;
        }

        $values[] = $this->literalToValue($buffer);

        return $values;
    }

    /**
     * Konversi literal SQL ke nilai PHP.
     */
    private function literalToValue(string $literal): mixed
    {
        $literal = trim($literal);

        if ($literal === '') {
            return null;
        }

        if (strtoupper($literal) === 'NULL') {
            return null;
        }

        if (preg_match('/^[+-]?\d+$/', $literal)) {
            return (int) $literal;
        }

        if (preg_match('/^[+-]?\d*\.\d+$/', $literal)) {
            return (float) $literal;
        }

        if ($literal[0] === "'" && str_ends_with($literal, "'")) {
            $inner = substr($literal, 1, -1);

            return str_replace(["\\\\", "\\'"], ["\\", "'"], $inner);
        }

        return $literal;
    }
}