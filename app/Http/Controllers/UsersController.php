<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\User;

class UsersController extends Controller
{
  public function __construct()
  {
  }

  public function index()
  {
    $users = User::paginate(10);
    return response()->json($users);
  }

  public function show($id)
  {
    $user = User::find($id);
    if (!$user) {
      return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);
    }
    return response()->json($user);
  }

  public function me()
  {
    return response()->json(auth()->user());
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => ['required', 'string'],
      'email' => ['required', 'string', 'unique:users,email'],
      'password' => ['required', 'string'],
    ]);
    $user = User::create($request->only('name', 'email', 'password'));
    $user->refresh();
    return $user;
  }

  public function update(Request $request, $id)
  {
    $this->validate($request, [
      'name' => 'string',
      'email' => 'string',
      'password' => 'string',
      'password_change_required' => 'boolean'
    ]);
    $user = User::find($id);
    if (!$user) {
      return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);
    }
    $itDifferentEmail = $request->email && $user->email !== $request->email;
    if ($itDifferentEmail && User::whereEmail($request->email)->exists) {
      return response()->json(['message' => 'Email already exists'], Response::HTTP_CONFLICT);
    }
    $user->update($request->only('name', 'email', 'password'));
    return $user;
  }

  public function delete($id)
  {

    $user = User::find($id);
    if (!$user) {
      return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);
    }
    $user->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }

}
