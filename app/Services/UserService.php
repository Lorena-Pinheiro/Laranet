<?php

namespace App\Services;

use App\Interfaces\ILogger;

class UserService{
    public function __construct(
        private ILogger $logger
    ){}
    public function message(): string{
        $this->logger->log('users requested');
        return 'User service message';
    }
}