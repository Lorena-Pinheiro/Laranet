<?php

namespace App\Interfaces;

interface ILogger{
    public function log(string $msg): void;
}