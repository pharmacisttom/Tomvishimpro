<?php

namespace Tomvis\HIMPro\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Core\Validation\Validator;

class ValidatorTest extends TestCase
{
    public function testHnValidationPasses()
    {
        $v = Validator::make(['hn' => '67000123'], ['hn' => 'required|hn']);
        $this->assertTrue($v->passes());
    }

    public function testHnValidationFailsOnInvalid()
    {
        $v = Validator::make(['hn' => 'abc123'], ['hn' => 'required|hn']);
        $this->assertTrue($v->fails());
    }
}
