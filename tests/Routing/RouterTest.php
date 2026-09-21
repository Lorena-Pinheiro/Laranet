<?php

namespace Tests\Routing;

use Laranet\Http\Request;
use Laranet\Http\Response;
use Laranet\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase{
    public function testeEncontraRota(): void{
        $router = new Router();

        $router->get('/', function(){
            return new Response('Hello World');
        });

        $request = new Request(
            method: 'GET', 
            uri: '/', 
            query: [],
            body: [],
            headers: []
        );

        $response = $router->dispatch($request);

        $this->assertSame(200, $response->status());
        $this->assertSame('Hello', $response->body());
    }
}