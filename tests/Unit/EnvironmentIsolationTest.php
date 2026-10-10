<?php

namespace Tomvis\HIMPro\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Core\Config\DotEnv;
use Tomvis\HIMPro\Core\Config\Config;
use Tomvis\HIMPro\Core\Database\EnvironmentGuard;
use Exception;

class EnvironmentIsolationTest extends TestCase
{
    protected function setUp(): void
    {
        DotEnv::load(dirname(__DIR__, 2) . '/.env');
        Config::loadFromDir(dirname(__DIR__, 2) . '/config');
        Config::set('app.env', 'local');
    }

    public function testLocalBlockedFromProductionDatabase()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Local environment/i');

        EnvironmentGuard::validateDatabaseConnection('mysql', [
            'database' => 'tomvishimpro', // Production DB name
            'driver' => 'mysql',
            'host' => '192.168.111.240'
        ]);
    }

    public function testProductionBlockedFromDevDatabase()
    {
        // Mock app.env to production
        Config::set('app.env', 'production');
        Config::set('app.secret', 'valid-production-secret-key-32chars');
        Config::set('app.debug', false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Production environment/i');

        try {
            EnvironmentGuard::validateDatabaseConnection('mysql', [
                'database' => 'tomvishimpro_dev', // Dev DB name
                'driver' => 'mysql',
                'host' => '192.168.111.240'
            ]);
        } finally {
            // Restore local
            Config::set('app.env', 'local');
            Config::set('app.debug', true);
        }
    }
}
