<?php

namespace Laranet\Http;

class Request{
    public function __construct(
        private string $method,
        private string $uri,
        private array $query,
        private array $body,
        private array $headers
    ){}

    public static function capture(): self{
        return new self(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            query: $_GET,
            body: $_POST,
            headers: self::captureHeaders()
        );
    }

    public function method(): string{
        return $this->method;
    }

    public function uri(): string{
        return $this->uri;
    }

    public function path(): string{
        return parse_url($this->uri, PHP_URL_PATH) ?? '/';
    }

    public function query(?string $key = null): mixed{
        if(!$key)
            return $this->query;

        return $this->query[$key] ?? null;
    }

    public function input(?string $key = null): mixed{
        if(!$key)
            return $this->body;

        return $this->body[$key] ?? null;
    }

    public function header(?string $name = null): mixed{
        if(!$name)
            return $this->headers;

        foreach($this->headers as $key => $value){
            if(strtolower($key) === strtolower($name))
                return $value;
        }

        return null;
    }

    private static function captureHeaders(): array{
        $headers = [];

        foreach($_SERVER as $key => $value){
            if(str_starts_with($key, 'HTTP_')){
                $name = str_replace(
                    ' ', 
                    '-', 
                    ucwords(strtolower(str_replace('_', ' ', substr($key, 5))))
                );

                $headers[$name] = $value;
            }
        }

        return $headers;
    }
}