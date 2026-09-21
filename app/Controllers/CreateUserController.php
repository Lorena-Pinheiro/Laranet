<?php

namespace App\Controllers;

use Laranet\Http\Request;
use Laranet\Http\Response;

class CreateUserController{
    public function __invoke(Request $request): Response{
        return new Response('Create User');
    }
}