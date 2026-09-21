<?php

namespace Laranet\Http;

class Response{
    public function __construct(
        private string $body = '',
        private int $status = 200,
        private array $headers = []
    ){}

    public function send(): void{
        http_response_code($this->status);

        foreach($this->headers as $name => $value){
            header("$name: $value");
        }

        echo $this->body;
    }

    public function body(): string{
        return $this->body;
    }

    public function status(): int{
        return $this->status;
    }

    public function headers(): array{
        return $this->headers;
    }
}