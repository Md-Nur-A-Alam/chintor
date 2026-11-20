<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Reads the live database structure (works on MySQL/MariaDB and SQLite)
 * and turns it into an ER diagram for the developer tools.
 */
class DatabaseSchema
{
    /**
     * Framework tables that clutter the business diagram.
     */
    private const SYSTEM_TABLES = ['migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions', 'password_reset_tokens', 'sqlite_sequence'];

    /**
     * @return list<array{name: string, columns: list<array{name: string, type: string, nullable: bool, primary: bool}>, foreign_keys: list<array{column: string, references: string, foreign_column: string}>}>
     */
    public static function tables(bool $includeSystem = false): array
    {
        $tables = [];

        // Only the application database: on MySQL/MariaDB getTables() would also list other databases on the server.
        foreach (Schema::getTables(Schema::getCurrentSchemaName()) as $table) {
            $name = $table['name'];

            if (! $includeSystem && in_array($name, self::SYSTEM_TABLES, true)) {
                continue;
            }

            $primaryColumns = collect(Schema::getIndexes($name))->firstWhere('primary', true)['columns'] ?? [];

            $tables[] = [
                'name' => $name,
                'columns' => collect(Schema::getColumns($name))->map(fn (array $column) => [
                    'name' => $column['name'],
                    'type' => $column['type_name'],
                    'nullable' => $column['nullable'],
                    'primary' => in_array($column['name'], $primaryColumns, true),
                ])->all(),
                'foreign_keys' => collect(Schema::getForeignKeys($name))->map(fn (array $foreignKey) => [
                    'column' => $foreignKey['columns'][0],
                    'references' => $foreignKey['foreign_table'],
                    'foreign_column' => $foreignKey['foreign_columns'][0],
                ])->all(),
            ];
        }

        return $tables;
    }

    /**
     * Mermaid "erDiagram" source. With $focus, only that table and the
     * tables it is directly linked to are drawn.
     *
     * @param  list<array{name: string, columns: list<array{name: string, type: string, nullable: bool, primary: bool}>, foreign_keys: list<array{column: string, references: string, foreign_column: string}>}>  $tables
     */
    public static function mermaid(array $tables, ?string $focus = null): string
    {
        if ($focus !== null) {
            $neighbours = collect($tables)
                ->filter(fn (array $table) => $table['name'] === $focus || collect($table['foreign_keys'])->contains('references', $focus))
                ->pluck('name')
                ->merge(collect(collect($tables)->firstWhere('name', $focus)['foreign_keys'] ?? [])->pluck('references'))
                ->unique();

            $tables = array_values(array_filter($tables, fn (array $table) => $neighbours->contains($table['name'])));
        }

        $lines = ['erDiagram'];

        foreach ($tables as $table) {
            $lines[] = '    '.self::entity($table['name']).' {';

            foreach ($table['columns'] as $column) {
                $isForeign = collect($table['foreign_keys'])->contains('column', $column['name']);
                $key = $column['primary'] ? ' PK' : ($isForeign ? ' FK' : '');
                $lines[] = '        '.Str::slug($column['type'], '_').' '.$column['name'].$key;
            }

            $lines[] = '    }';
        }

        $names = array_column($tables, 'name');

        foreach ($tables as $table) {
            foreach ($table['foreign_keys'] as $foreignKey) {
                if (in_array($foreignKey['references'], $names, true)) {
                    $lines[] = '    '.self::entity($foreignKey['references']).' ||--o{ '.self::entity($table['name']).' : "'.$foreignKey['column'].'"';
                }
            }
        }

        return implode("\n", $lines);
    }

    private static function entity(string $table): string
    {
        return strtoupper($table);
    }
}
