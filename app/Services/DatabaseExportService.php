<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseExportService
{
    /**
     * Export database as a streamed SQL download response.
     */
    public function exportStream(?string $filename = null): StreamedResponse
    {
        $filename = $filename ?? 'backup-litelearning-' . now()->format('Y-m-d-His') . '.sql';

        return response()->streamDownload(function () {
            $this->dumpToOutput();
        }, $filename, [
            'Content-Type' => 'text/sql; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Dump the entire database SQL statements directly to output stream.
     */
    public function dumpToOutput(): void
    {
        $driver = config('database.default');
        $connection = DB::connection();
        $pdo = $connection->getPdo();

        echo "-- --------------------------------------------------------\n";
        echo "-- LiteLearning Database Backup\n";
        echo "-- Driver: " . $driver . "\n";
        echo "-- Charset: utf8mb4\n";
        echo "-- Exported at: " . now()->toIso8601String() . "\n";
        echo "-- --------------------------------------------------------\n\n";

        if ($driver === 'sqlite') {
            $this->dumpSqlite($connection, $pdo);
        } else {
            $this->dumpMysql($connection, $pdo);
        }
    }

    private function dumpMysql($connection, \PDO $pdo): void
    {
        // Ensure the active connection reads with utf8mb4
        $connection->statement("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

        echo "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        echo "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        echo "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        echo "/*!40101 SET NAMES utf8mb4 */;\n";
        echo "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
        echo "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
        echo "START TRANSACTION;\n\n";

        $rawTables = Schema::getTableListing();
        $tables = array_map(function ($table) {
            if (str_contains($table, '.')) {
                $parts = explode('.', $table);

                return end($parts);
            }

            return $table;
        }, $rawTables);

        foreach ($tables as $table) {
            echo "-- --------------------------------------------------------\n";
            echo "-- Table structure for table `{$table}`\n";
            echo "-- --------------------------------------------------------\n";
            echo "DROP TABLE IF EXISTS `{$table}`;\n";

            $createTableResult = $connection->select("SHOW CREATE TABLE `{$table}`");
            if (! empty($createTableResult)) {
                $firstObj = (array) $createTableResult[0];
                $createTableSql = $firstObj['Create Table'] ?? array_values($firstObj)[1] ?? null;
                if ($createTableSql) {
                    echo $createTableSql . ";\n\n";
                }
            }

            echo "-- Dumping data for table `{$table}`\n";
            $connection->table($table)->orderByRaw('1')->chunk(200, function ($rows) use ($table, $pdo) {
                if ($rows->isEmpty()) {
                    return;
                }

                $columns = array_keys((array) $rows->first());
                $columnList = implode('`, `', $columns);

                $valuesList = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ((array) $row as $value) {
                        if ($value === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = $pdo->quote((string) $value);
                        }
                    }
                    $valuesList[] = '(' . implode(', ', $rowValues) . ')';
                }

                echo "INSERT INTO `{$table}` (`{$columnList}`) VALUES\n" . implode(",\n", $valuesList) . ";\n";
            });

            echo "\n";
        }

        echo "COMMIT;\n";
        echo "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
        echo "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
        echo "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        echo "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        echo "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
    }

    private function dumpSqlite($connection, \PDO $pdo): void
    {
        echo "PRAGMA foreign_keys = OFF;\n";
        echo "BEGIN TRANSACTION;\n\n";

        $tables = $connection->select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

        foreach ($tables as $tableItem) {
            $table = $tableItem->name;
            $createSql = $tableItem->sql;

            echo "-- --------------------------------------------------------\n";
            echo "-- Table structure for table \"{$table}\"\n";
            echo "-- --------------------------------------------------------\n";
            echo "DROP TABLE IF EXISTS \"{$table}\";\n";
            echo $createSql . ";\n\n";

            echo "-- Dumping data for table \"{$table}\"\n";
            $connection->table($table)->orderByRaw('1')->chunk(200, function ($rows) use ($table, $pdo) {
                if ($rows->isEmpty()) {
                    return;
                }

                $columns = array_keys((array) $rows->first());
                $columnList = implode('", "', $columns);

                $valuesList = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ((array) $row as $value) {
                        if ($value === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = $pdo->quote((string) $value);
                        }
                    }
                    $valuesList[] = '(' . implode(', ', $rowValues) . ')';
                }

                echo "INSERT INTO \"{$table}\" (\"{$columnList}\") VALUES\n" . implode(",\n", $valuesList) . ";\n";
            });

            echo "\n";
        }

        echo "COMMIT;\n";
        echo "PRAGMA foreign_keys = ON;\n";
    }
}
