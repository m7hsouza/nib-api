<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use Symfony\Component\HttpFoundation\Response;

use App\Models\User;

class UsersController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
    $this->middleware('permission:user.create', ['only' => 'store']);
    $this->middleware('permission:user.read', ['only' => ['index', 'show']]);
    $this->middleware('permission:user.update', ['only' => 'update']);
    $this->middleware('permission:user.delete', ['only' => 'delete']);
  }

  public function index(): JsonResponse
  {
    $users = User::paginate(10);
    return response()->json($users);
  }

  public function show($id): JsonResponse
  {
    $user = User::findOrFail($id);
    return response()->json($user);
  }

  public function me(): JsonResponse
  {
    return response()->json(auth()->user());
  }

  public function store(Request $request): JsonResponse
  {
    $this->validate($request, [
      'name' => ['required', 'string'],
      'email' => ['required', 'string', 'unique:users,email'],
      'password' => ['required', 'string'],
    ]);
    $user = User::create($request->only('name', 'email', 'password'));
    $user->refresh();
    return response()->json($user, Response::HTTP_CREATED);
  }

  public function update(Request $request, $id): JsonResponse
  {
    $this->validate($request, [
      'name' => 'string',
      'email' => 'string',
      'password' => 'string',
      'password_change_required' => 'boolean'
    ]);
    $user = User::findOrFail($id);
    $itDifferentEmail = $request->email && $user->email !== $request->email;
    if ($itDifferentEmail && User::whereEmail($request->email)->exists) {
      return response()->json(['message' => 'Email already exists'], Response::HTTP_CONFLICT);
    }
    $user->update($request->only('name', 'email', 'password'));
    return response()->json($user);
  }

  public function delete($id): JsonResponse
  {
    $user = User::findOrFail($id);
    $user->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }
}
