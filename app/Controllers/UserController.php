<?php

namespace App\Controllers;

use App\Services\UserService;
use Laranet\Http\Request;
use Laranet\Http\Response;

class UserController{
    public function __construct(
        private UserService $users
    ){}

    public function index(Request $request): Response{
        return new Response($this->users->message());
    }

    public function show(Request $request, string $id): Response{
        return new Response("User id = {$id}");
    }
}