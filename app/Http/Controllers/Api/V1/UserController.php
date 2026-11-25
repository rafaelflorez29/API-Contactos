<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
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
            'message' => 'Usuario creado con exito',
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user()->findOrFail($id);

        return response()->json([
            'user' => [$user],
        ], 200);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return response()->json([
            'message' => 'Usuario actualizado exitosamente.',
            'data' => $user,
        ], 200);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            "message" => "El usuario fue eliminado con exito."
        ], 204);
    }
}
