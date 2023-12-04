<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use  App\Models\User;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout']]);
  }

  public function login(Request $request)
  {
    $this->validate(
      $request,
      [
        'enrollment_number' => 'required|string|digits:6',
        'password' => 'required|string',
      ],
      [
        'password.required' => 'A senha é obrigatória.',
        'enrollment_number' => [
          'required' => 'A matrícula é obrigatória.',
          'digits' => 'Matrícula no formato inválido.'
        ]
      ]
    );

    $user = User::active()->whereEnrollmentNumber($request->enrollment_number)->first();
    if (!$user) {
      return response()->json(['message' => 'Matrícula ou senha inválida.'], Response::HTTP_UNAUTHORIZED);
    }

    $credentials = $request->only(['enrollment_number', 'password']);

    if (!$token = Auth::attempt($credentials)) {
      return response()->json(['message' => 'Matrícula ou senha inválida.'], Response::HTTP_UNAUTHORIZED);
    }

    return $this->respondWithToken($token);
  }

  public function logout()
  {
    auth()->logout();

    return response()->json(['message' => 'Successfully logged out']);
  }

  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  }

  protected function respondWithToken($token)
  {
    $user = auth()->user()->load('roles.permissions', 'permissions');
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'user' =>  $user,
      'expires_in' => auth()->factory()->getTTL() * 60 * 24
    ]);
  }
}
