<?php

namespace App\Http\Controllers\Admin\Developer;

use App\Http\Controllers\Controller;
use App\Models\DeveloperQueryLog;
use App\Support\DatabaseSchema;
use App\Support\SqlStatements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * Run one or more SQL statements against the application database.
 *
 * Reads always run. Writes (INSERT/UPDATE/DELETE/ALTER…) need "write mode",
 * the developer's own password, and config('chintor.developer.allow_write_queries').
 * Every statement is recorded in developer_query_logs.
 */
class DatabaseConsoleController extends Controller
{
    public function show(): View
    {
        return $this->page((string) old('sql', ''));
    }

    public function run(Request $request): View
    {
        $validated = $request->validate([
            'sql' => ['required', 'string', 'max:20000'],
            'write_mode' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string'],
        ]);

        $statements = SqlStatements::split($validated['sql']);
        $hasWrites = collect($statements)->contains(fn (string $statement) => ! SqlStatements::isRead($statement));

        if ($hasWrites) {
            $this->ensureWritesAreAllowed($request);
        }

        $results = array_map(fn (string $statement) => $this->execute($statement, $request), $statements);

        return $this->page($validated['sql'], $results);
    }

    /**
     * @param  list<array<string, mixed>>  $results
     */
    private function page(string $sql, array $results = []): View
    {
        return view('admin.developer.database', [
            'sql' => $sql,
            'results' => $results,
            'tables' => collect(DatabaseSchema::tables(includeSystem: true))->pluck('name'),
            'writesAllowed' => config('chintor.developer.allow_write_queries'),
            'history' => DeveloperQueryLog::query()->with('user')->latest('id')->limit(20)->get(),
        ]);
    }

    private function ensureWritesAreAllowed(Request $request): void
    {
        if (! config('chintor.developer.allow_write_queries')) {
            throw ValidationException::withMessages(['sql' => 'এই সার্ভারে write কোয়েরি বন্ধ (CHINTOR_DEV_SQL_WRITES)। শুধু SELECT/SHOW চলবে।']);
        }

        if (! $request->boolean('write_mode')) {
            throw ValidationException::withMessages(['sql' => 'ডেটা বদলানোর কোয়েরি আছে — “Write মোড” চালু করে পাসওয়ার্ড দিন।']);
        }

        if (! Hash::check((string) $request->input('password'), $request->user()->password)) {
            throw ValidationException::withMessages(['password' => 'পাসওয়ার্ড সঠিক নয়।']);
        }
    }

    /**
     * @return array{statement: string, is_write: bool, columns: list<string>, rows: list<array<string, mixed>>, truncated: bool, total: int, affected: ?int, error: ?string, duration_ms: int}
     */
    private function execute(string $statement, Request $request): array
    {
        $isWrite = ! SqlStatements::isRead($statement);
        $maxRows = config('chintor.developer.max_result_rows');
        $started = hrtime(true);
        $rows = [];
        $affected = null;
        $error = null;

        try {
            if ($isWrite) {
                $affected = DB::affectingStatement($statement);
            } else {
                $rows = array_map(fn ($row) => (array) $row, DB::select($statement));
            }
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }

        $durationMs = (int) round((hrtime(true) - $started) / 1_000_000);

        DeveloperQueryLog::create([
            'user_id' => $request->user()->id,
            'statement' => $statement,
            'is_write' => $isWrite,
            'affected_rows' => $affected,
            'duration_ms' => $durationMs,
            'error' => $error,
            'ip_address' => $request->ip(),
        ]);

        return [
            'statement' => $statement,
            'is_write' => $isWrite,
            'columns' => array_keys($rows[0] ?? []),
            'rows' => array_slice($rows, 0, $maxRows),
            'truncated' => count($rows) > $maxRows,
            'total' => count($rows),
            'affected' => $affected,
            'error' => $error,
            'duration_ms' => $durationMs,
        ];
    }
}
