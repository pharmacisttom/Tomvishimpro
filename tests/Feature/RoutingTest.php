<?php

namespace Tomvis\HIMPro\Tests\Feature;

use PHPUnit\Framework\TestCase;
use Tomvis\HIMPro\Core\Container\Container;
use Tomvis\HIMPro\Core\Routing\Router;
use Tomvis\HIMPro\Core\Request\Request;
use Tomvis\HIMPro\Core\Response\Response;

class RoutingTest extends TestCase
{
    public function testRouteDispatching()
    {
        $container = new Container();
        $router = new Router($container);

        $router->get('/ping', function () {
            return new Response('pong');
        });

        $request = new Request([], [], ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/ping']);
        $response = $router->dispatch($request);

        $this->assertInstanceOf(Response::class, $response);
    }
}
