<?php

namespace Laranet\Routing;

class RouteDefinition{
    public array $constraints = [];
    public ?string $name = null;

    public function __construct(
        public string $method,
        public string $path,
        public mixed $handler
    ){}

    public function where(string $param, string $pattern): self{
        $this->constraints[$param] = $pattern;
        return $this;
    }

    public function name(string $name): self{
        $this->name = $name;
        return $this;
    }
}