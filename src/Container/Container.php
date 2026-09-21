<?php

namespace Laranet\Container;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

class Container{
    private array $bindings = [];
    private array $instances = [];
    private array $singletons = [];

    public function bind(string $abstract, string|object $concrete): void{
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, string|object $concrete): void{
        $this->bindings[$abstract] = $concrete;
        $this->singletons[$abstract] = true;
    }

    public function instance(string $abstract, object $concrete): void{
        $this->instances[$abstract] = $concrete;
    }

    public function make(string $abstract): object{
        if(isset($this->instances[$abstract])) return $this->instances[$abstract];

        $concrete = $this->bindings[$abstract] ?? $abstract;

        if(is_object($concrete)) return $concrete;

        $instance = $this->build($concrete);

        if(isset($this->singletons[$abstract])) 
            $this->instances[$abstract] = $instance;

        return $instance;
    }

    public function build(string $class): object{
        if(!class_exists($class)) 
            throw new RuntimeException("Classe '{$class}' não existe.");

        $reflection = new ReflectionClass($class);

        if(!$reflection->isInstantiable()) 
            throw new RuntimeException("Não foi possivel instanciar a classe '{$class}'");

        $constructor = $reflection->getConstructor();

        if($constructor === null) return $reflection->newInstance();

        $dependencies = [];

        foreach($constructor->getParameters() as $param){
            $type = $param->getType();

            if(!$type instanceof ReflectionNamedType)
                throw new RuntimeException("Não foi possivel resolver o parâmetro '{$param->getName()}' da classe '{$class}'");

            if($type->isBuiltin()){
                if($param->isDefaultValueAvailable()){
                    $dependencies[] = $param->getDefaultValue();
                    continue;
                }

                throw new RuntimeException("Não foi possivel resolver o parâmetro interno '{$param->getName()}' da classe '{$class}'");
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}