<?php 

namespace Tests\Container;

use Laranet\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase{
    public function testeResolverUmaClasseSimples(): void{
        $container = new Container();
        $instance = $container->make(SimpleService::class);
        $this->assertInstanceOf(SimpleService::class, $instance);
    }

    // stoped here
}