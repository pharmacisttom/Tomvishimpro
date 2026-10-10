<?php

namespace Tomvis\HIMPro\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Core\Container\Container;

class ContainerTest extends TestCase
{
    public function testSingletonBinding()
    {
        $container = new Container();
        $container->singleton('test_service', function () {
            return new \stdClass();
        });

        $obj1 = $container->make('test_service');
        $obj2 = $container->make('test_service');

        $this->assertSame($obj1, $obj2);
    }
}
