<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', $request->string('email'))
            ->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke existing tokens for this device name (optional but helps keep token list clean)
        $user->tokens()
            ->where('name', $request->string('device_name'))
            ->delete();

        $token = $user->createToken(
            name: $request->string('device_name'),
            abilities: ['*'],
        );

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(): JsonResponse
    {
        $user = request()->user();

        return response()->json([
            'user' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'email' => $user?->email,
            ],
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }
}
