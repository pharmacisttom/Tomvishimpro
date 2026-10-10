<?php

namespace Tomvis\HIMPro\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Config\Config;

class NonInvasiveDeploymentTest extends TestCase
{
    public function testDeploymentScriptsContainNoApacheRestartCommands()
    {
        $scripts = [
            dirname(__DIR__, 2) . '/scripts/deploy.sh',
            dirname(__DIR__, 2) . '/scripts/deploy.ps1',
            dirname(__DIR__, 2) . '/scripts/rollback.sh',
            dirname(__DIR__, 2) . '/scripts/preflight.php',
        ];

        $forbidden = ['systemctl restart apache2', 'systemctl reload apache2', 'service apache2 restart', 'apachectl graceful', 'a2enmod'];

        foreach ($scripts as $s) {
            if (file_exists($s)) {
                $content = file_get_contents($s);
                foreach ($forbidden as $cmd) {
                    $this->assertStringNotContainsString($cmd, $content, "Script [{$s}] must not contain forbidden command: {$cmd}");
                }
            }
        }
    }

    public function testFrontControllerFallbackRoutingWithQueryParam()
    {
        $request = new Request(['route' => '/dashboard'], [], ['REQUEST_METHOD' => 'GET']);
        $this->assertEquals('/dashboard', $request->getUri());
    }

    public function testSubdirectoryBasePathResolution()
    {
        Config::set('app.url', 'http://192.168.111.240/tomvishimpro/public');

        $request = new Request([], [], [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/tomvishimpro/public/patients/search'
        ]);

        $this->assertEquals('/patients/search', $request->getUri());
    }

    public function testRootIndexFileExists()
    {
        $rootIndex = dirname(__DIR__, 2) . '/index.php';
        $this->assertFileExists($rootIndex);
    }
}
