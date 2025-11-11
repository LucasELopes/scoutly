<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedTokenController extends Controller
{


    /**
    *  @OA\Post(
    *      path="/api/login",
    *      summary="Login",
    *      description="Endpoints para autenticação e login.",
    *      tags={"Login/Logout"},
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\MediaType(
    *               mediaType="multipart/form-data",
    *               @OA\Schema(
    *                    required={"email", "password"},
    *                    @OA\Property(property="email", type="string", example="test@example.com"),
    *                    @OA\Property(property="password", type="string", example="password"),
    *               ),
    *           ),
    *          @OA\MediaType(
    *              mediaType="application/json",
    *              @OA\Schema(
    *                  type="object",
    *                  required={"email", "password"},
    *                  @OA\Property(property="email", type="string", example="test@example.com"),
    *                  @OA\Property(property="password", type="string", example="password"),
    *              ),
    *          ),
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Login realizado com sucesso",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
    *              @OA\Property(property="token_type", example="Bearer"),
    *              @OA\Property(property="expires_in", example="7200")
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Credenciais inválidas",
    *          @OA\JsonContent(
    *              type="object",
    *              @OA\Property(property="message", type="string", example="Credenciais inválidas"),
    *          )
    *      )
    *  )
    */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $token = $user->createToken('auth-token', ['*'], now()->addHours(2))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 7200,
        ], Response::HTTP_OK);
    }

    /**
    *  @OA\Post(
    *      path="/api/logout",
    *      summary="Logout",
    *      description="Endpoints para autenticação e login.",
    *      tags={"Login/Logout"},
    *      security={{"bearerAuth": {}}},
    *      @OA\Response(
    *          response=200,
    *          description="Logout realizado com sucesso",
    *          @OA\JsonContent(
    *              type="array",
    *              type="object",
    *              @OA\Property(property="message", type="string", example="Logout realizado"),
    *          )
    *      )
    *  )
    */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete(); // Revoga todos os tokens do usuário
        }

        return response()->json(['message' => 'Logout realizado'], Response::HTTP_OK);
    }
}
