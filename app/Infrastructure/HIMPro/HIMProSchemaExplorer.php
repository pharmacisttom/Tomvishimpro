<?php

namespace Tomvis\HIMPro\Infrastructure\HIMPro;

use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;
use PDO;
use Exception;

class HIMProSchemaExplorer
{
    protected DatabaseManager $dbManager;

    public function __construct(DatabaseManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function getAllowlist(): array
    {
        return Config::get('his.allowlist_tables', [
            'pt.pt',
            'pt.ptallergy',
            'opd.opd',
            'opd.odiag',
            'opd.drug_order_opd',
            'ipd.ipd',
            'ipd.drug_order_ipd',
            'hosdata.user',
            'result_lab_opd'
        ]);
    }

    public function inspectTableSchema(string $table): array
    {
        $driver = Config::get('his.active_driver', 'mock');

        if ($driver === 'mock') {
            return $this->getMockTableSchema($table);
        }

        $pdo = $this->dbManager->connection('himpro');
        $stmt = $pdo->prepare("DESCRIBE " . $table);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'table' => $table,
            'columns' => $columns,
            'inspected_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function getMockTableSchema(string $table): array
    {
        $schemas = [
            'pt.pt' => [
                ['Field' => 'hn', 'Type' => 'varchar(10)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => null],
                ['Field' => 'fname', 'Type' => 'varchar(50)', 'Null' => 'YES', 'Key' => '', 'Default' => null],
                ['Field' => 'lname', 'Type' => 'varchar(50)', 'Null' => 'YES', 'Key' => '', 'Default' => null],
                ['Field' => 'birthday', 'Type' => 'date', 'Null' => 'YES', 'Key' => '', 'Default' => null],
                ['Field' => 'sex', 'Type' => 'char(1)', 'Null' => 'YES', 'Key' => '', 'Default' => null],
                ['Field' => 'pop_id', 'Type' => 'varchar(13)', 'Null' => 'YES', 'Key' => 'UNI', 'Default' => null],
            ],
            'pt.ptallergy' => [
                ['Field' => 'hn', 'Type' => 'varchar(10)', 'Null' => 'NO', 'Key' => 'MUL', 'Default' => null],
                ['Field' => 'dcode', 'Type' => 'varchar(20)', 'Null' => 'NO', 'Key' => '', 'Default' => null],
                ['Field' => 'dname', 'Type' => 'varchar(100)', 'Null' => 'YES', 'Key' => '', 'Default' => null],
                ['Field' => 'symptom', 'Type' => 'text', 'Null' => 'YES', 'Key' => '', 'Default' => null],
                ['Field' => 'severity', 'Type' => 'varchar(20)', 'Null' => 'YES', 'Key' => '', 'Default' => null],
            ],
            'opd.opd' => [
                ['Field' => 'vn', 'Type' => 'varchar(12)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => null],
                ['Field' => 'hn', 'Type' => 'varchar(10)', 'Null' => 'NO', 'Key' => 'MUL', 'Default' => null],
                ['Field' => 'vdate', 'Type' => 'date', 'Null' => 'NO', 'Key' => '', 'Default' => null],
                ['Field' => 'dep', 'Type' => 'varchar(50)', 'Null' => 'YES', 'Key' => '', 'Default' => null],
            ],
        ];

        return [
            'table' => $table,
            'columns' => $schemas[$table] ?? [
                ['Field' => 'id', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => null]
            ],
            'inspected_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function saveSnapshot(string $table): int
    {
        $schema = $this->inspectTableSchema($table);
        $schemaJson = json_encode($schema, JSON_UNESCAPED_UNICODE);
        $hash = md5($schemaJson);

        $pdo = $this->dbManager->connection('mysql');
        $stmt = $pdo->prepare("INSERT INTO his_schema_snapshots (connection_id, table_name, schema_json, snapshot_hash) VALUES (1, ?, ?, ?)");
        $stmt->execute([$table, $schemaJson, $hash]);

        return (int)$pdo->lastInsertId();
    }

    public function compareSnapshots(int $snapshotId1, int $snapshotId2): array
    {
        $pdo = $this->dbManager->connection('mysql');
        $stmt = $pdo->prepare("SELECT schema_json FROM his_schema_snapshots WHERE id IN (?, ?)");
        $stmt->execute([$snapshotId1, $snapshotId2]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($rows) < 2) {
            return ['status' => 'error', 'message' => 'Snapshots not found'];
        }

        $s1 = json_decode($rows[0], true);
        $s2 = json_decode($rows[1], true);

        return [
            'table' => $s1['table'] ?? 'unknown',
            'identical' => ($rows[0] === $rows[1]),
            'version_1' => $s1,
            'version_2' => $s2,
        ];
    }
}
