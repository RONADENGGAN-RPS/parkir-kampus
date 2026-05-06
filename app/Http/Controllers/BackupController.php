<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    private $backupPath;

    public function __construct()
    {
        // Hanya superadmin
        if (auth()->user()->role->slug !== 'superadmin') {
            abort(403, 'Akses ditolak.');
        }

        $this->backupPath = storage_path('backup/database');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Tampilkan daftar file backup .sql
     */
    public function index()
    {
        $files = [];
        if (File::exists($this->backupPath)) {
            $files = collect(File::files($this->backupPath))
                ->filter(fn($f) => $f->getExtension() === 'sql')
                ->map(fn($f) => [
                    'name'     => $f->getFilename(),
                    'size'     => round($f->getSize() / 1024, 2),
                    'modified' => Carbon::createFromTimestamp($f->getMTime())->format('d/m/Y H:i'),
                ])
                ->sortByDesc('modified')
                ->values();
        }

        return view('backup.index', ['backups' => $files]);
    }

    /**
     * Buat backup database (PHP murni, tanpa mysqldump)
     */
    public function create()
    {
        try {
            $db = config('database.connections.mysql');
            $dump = $this->generateDump($db);

            $filename = 'backup-' . now()->format('Ymd-His') . '.sql';
            $path = $this->backupPath . '/' . $filename;

            File::put($path, $dump);

            return response()->json([
                'success' => true,
                'message' => "Backup berhasil: $filename"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file backup
     */
    public function download($filename)
    {
        $path = $this->backupPath . '/' . basename($filename);
        if (!File::exists($path)) {
            abort(404);
        }
        return Response::download($path);
    }

    /**
     * Hapus backup
     */
    public function destroy($filename)
    {
        $path = $this->backupPath . '/' . basename($filename);
        if (File::exists($path)) {
            File::delete($path);
            return response()->json(['success' => true, 'message' => 'File dihapus.']);
        }
        return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
    }

    /**
     * Restore database dari backup SQL
     */
    public function restore(Request $request, $filename)
    {
        $request->validate(['confirm' => 'required|in:YA']);

        $path = $this->backupPath . '/' . basename($filename);
        if (!File::exists($path)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
        }

        try {
            $sql = File::get($path);
            DB::unprepared($sql);
            return response()->json(['success' => true, 'message' => 'Restore berhasil!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Restore gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate SQL dump menggunakan PHP
     */
    private function generateDump(array $dbConfig): string
    {
        $host = $dbConfig['host'];
        $port = $dbConfig['port'] ?? 3306;
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];

        // Buat koneksi PDO sementara
        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
        $pdo = new \PDO($dsn, $username, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        $dump = "-- Backup database $database\n";
        $dump .= "-- Generated: " . now()->toDateTimeString() . "\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Ambil semua tabel
        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            // DROP TABLE IF EXISTS
            $dump .= "DROP TABLE IF EXISTS `$table`;\n";

            // SHOW CREATE TABLE
            $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
            $dump .= $create['Create Table'] . ";\n\n";

            // Data
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll();
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';
                foreach ($rows as $row) {
                    $values = array_map(function ($val) use ($pdo) {
                        if (is_null($val)) return 'NULL';
                        return $pdo->quote($val);
                    }, array_values($row));
                    $dump .= "INSERT INTO `$table` ($columnList) VALUES (" . implode(', ', $values) . ");\n";
                }
                $dump .= "\n";
            }
        }

        $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $dump;
    }
}