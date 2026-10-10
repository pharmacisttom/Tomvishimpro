<?php

namespace Tomvis\HIMPro\Tests\Sandbox;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Modules\Sandbox\SyntheticPatientGenerator;

class SyntheticGeneratorTest extends TestCase
{
    public function testSyntheticDatasetGeneration()
    {
        $generator = new SyntheticPatientGenerator(42);
        $patient = $generator->getPatientByHn('67000001');

        $this->assertNotNull($patient);
        $this->assertArrayHasKey('hn', $patient);
        $this->assertArrayHasKey('full_name', $patient);
    }
}
