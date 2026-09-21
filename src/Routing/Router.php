<?php

namespace Laranet\Routing;

use Laranet\Container\Container;
use Laranet\Http\Request;
use Laranet\Http\Response;
use Laranet\Routing\RouteDefinition;

class Router{
    private array $routes = [];
    private string $groupPrefix = '';

    public function __construct(
        private Container $container
    ){}

    public function get(string $path, callable|array|string $handler): RouteDefinition{
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array|string $handler): RouteDefinition{
        return $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array|string $handler): RouteDefinition{
        return $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable|array|string $handler): RouteDefinition{
        return $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|array|string $handler): RouteDefinition{
        return $this->add('DELETE', $path, $handler);
    }

    public function group(string $prefix, callable $callback): void{
        $prevPrefix = $this->groupPrefix;

        $this->groupPrefix .= $this->normalizePath($prefix);
        $callback($this);
        $this->groupPrefix = $prevPrefix;
    }

    public function dispatch(Request $request): Response{
        foreach($this->routes as $route){
            if($route->method !== $request->method()) continue;

            $params = $this->match($route, $request->path());

            if($params === null) continue;

            return $this->callHandler($route->handler, $request, $params);
        }

        return new Response(body: 'Not Found', status: 404);
    }

    private function add(string $method, string $path, callable|array|string $handler): RouteDefinition{
        $route = new RouteDefinition (
            method: $method,
            path: $this->groupPrefix . $this->normalizePath($path),
            handler: $handler
        );

        $this->routes[] = $route;
        return $route;
    }

    private function match(RouteDefinition $route, string $path): ?array{
        $paramNames = [];

        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function(array $matches) use (&$paramNames, $route): string{
                $name = $matches[1];
                $paramNames[] = $name;
                return '(' . ($route->constraints[$name] ?? '[^/]+') . ')';
            },
            $route->path
        );

        $pattern = '#^' . $pattern . '/?$#';

        if(!preg_match($pattern, $path, $matches)) return null;

        array_shift($matches);
        $params = [];

        foreach($paramNames as $index => $name){
            $params[$name] = $matches[$index];
        }

        return $params;
    }

    private function callHandler(callable|array|string $handler, Request $request, array $params): Response{
        if(is_array($handler)){
            [$controller, $method] = $handler;

            if(is_string($controller)) $controller = $this->container->make($controller);

            return $controller->{$method}($request, ...array_values($params));
        }

        if(is_string($handler)){
            $controller = $this->container->make($handler);

            return $controller($request, ...array_values($params));
        }

        return $handler($request, ...array_values($params));
    }

    private function normalizePath(string $path): string{
        if($path === '/') return '';

        return '/' . trim($path, '/');
    }
}