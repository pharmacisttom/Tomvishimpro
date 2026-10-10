<?php

namespace Tomvis\HIMPro\Tests\Unit;

use PHPUnit\Framework\TestCase;

class GitIgnoreSecretsTest extends TestCase
{
    public function testDotEnvIsIgnoredInGit()
    {
        $gitignore = file_get_contents(dirname(__DIR__, 2) . '/.gitignore');
        $this->assertStringContainsString('.env', $gitignore);
    }
}
