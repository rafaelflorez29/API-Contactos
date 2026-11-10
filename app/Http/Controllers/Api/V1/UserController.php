<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\StoreUserRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(){
        return response()->json([
            'user' => User::all(),
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            ...$request->validated(),
        ]);

        return response()->json([
            "message" => "Usuario creado con exito"
        ], 201);
    }
}
