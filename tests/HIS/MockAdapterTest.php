<?php

namespace Tomvis\HIMPro\Tests\HIS;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter;

class MockAdapterTest extends TestCase
{
    public function testGetPatientByHn()
    {
        $adapter = new MockHISAdapter();
        $patient = $adapter->getByHn('67000001');

        $this->assertNotNull($patient);
        $this->assertEquals('67000001', $patient->hn);
    }
}
