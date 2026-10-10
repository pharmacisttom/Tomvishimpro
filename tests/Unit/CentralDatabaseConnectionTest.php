<?php

namespace Tomvis\HIMPro\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Core\Config\DotEnv;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\DatabaseManager;

class CentralDatabaseConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        DotEnv::load(dirname(__DIR__, 2) . '/.env');
        Config::loadFromDir(dirname(__DIR__, 2) . '/config');
    }

    public function testDatabaseManagerResolvesNamedConnections()
    {
        $db = new DatabaseManager();
        $pdo = $db->connection();
        $this->assertNotNull($pdo);
    }

    public function testCentralHostConfiguration()
    {
        $host = Config::get('database.connections.mysql.host');
        $this->assertNotEmpty($host);
    }
}
