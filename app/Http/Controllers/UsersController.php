<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
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

  public function show($user_id): JsonResponse
  {
    $user = User::findOrFail($user_id);
    return response()->json($user);
  }

  public function me(): JsonResponse
  {
    return response()->json(auth()->user());
  }

  public function updateMyProfile(Request $request): JsonResponse
  {
    $user = auth()->user();
    $request->merge(['phone' => preg_replace('/\D/', '', $request->phone)]);
    $this->validate($request, [
      'name' => 'string',
      'email' => 'string|email',
      'password' => 'string|min:8',
      'phone' => 'string|regex:/\d{11}/',
      'birth' => 'date|date_format:Y-m-d',
      'gender' => 'in:male,female',
      'is_already_baptized' => 'boolean',
      'already_accepted_term' => 'boolean',
      'is_active' => 'boolean'
    ]);
    $itDifferentEmail = $request->email && $user->email !== $request->email;
    if ($itDifferentEmail && User::whereEmail($request->email)->exists()) {
      return response()->json(['message' => 'Email already exists'], Response::HTTP_CONFLICT);
    }
    $user->update($request->only(
      'name',
      'email',
      'password',
      'phone',
      'birth',
      'gender',
      'is_already_baptized',
      'already_accepted_term',
      'is_active'
    ));
    return response()->json($user);

  }

  public function updateAvatar(Request $request): JsonResponse
  {
    $this->validate(
      $request,
      [
        'avatar' => 'required|mimes:jpg,png,jpeg',
      ],
      [
        'avatar' => [
          'required' => 'O arquivo é obrigatório.',
          'mimes' => 'Formato do arquivo inválido.'
        ]
      ]
    );
    $file = $request->file('avatar');
    $avatar_filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
    $avatarDisk = Storage::disk('avatar');
    $avatarDisk->put($avatar_filename, $file->getContent());
    $user = auth()->user();
    $oldAvatarFilename = $user->avatar_filename;
    $user->update(compact('avatar_filename'));
    $user->refresh();
    if ($oldAvatarFilename) {
      $avatarDisk->delete($oldAvatarFilename);
    }
    return response()->json(auth()->user());
  }

  public function getAvatar(Request $request, $user_id)
  {
    $user = User::select('avatar_filename')->findOrFail($user_id);
    return response()->file(Storage::disk('avatar')->path($user->avatar_filename));
  }

  public function store(Request $request): JsonResponse
  {
    $request->merge(['phone' => preg_replace('/\D/', '', $request->phone)]);
    $this->validate($request, [
      'name' => ['required', 'string'],
      'email' => ['required', 'string', 'email', 'unique:users,email'],
      'password' => ['required', 'string', 'min:8'],
      'phone' => 'string|regex:/\d{11}/',
      'birth' => ['required', 'date'],
      'gender' => ['required', 'in:male,female'],
      'is_already_baptized' => ['required', 'boolean'],
      'already_accepted_term' => ['required', 'boolean'],
      'is_active' => 'boolean'
    ]);
    $user = User::create($request->only(
      'name',
      'email',
      'password',
      'phone',
      'birth',
      'gender',
      'is_already_baptized',
      'already_accepted_term',
      'is_active'
    ));
    $user->refresh();
    return response()->json($user, Response::HTTP_CREATED);
  }

  public function update(Request $request, $user_id): JsonResponse
  {
    $this->validate($request, [
      'name' => 'string',
      'email' => 'string',
      'password' => 'string|min:8',
      'phone' => 'string|regex:/\d{11}/',
      'birth' => 'date',
      'gender' => 'in:male,female',
      'is_already_baptized' => 'boolean',
      'already_accepted_term' => 'boolean',
      'password_change_required' => 'boolean',
      'is_active' => 'boolean'
    ]);
    $user = User::findOrFail($user_id);
    $itDifferentEmail = $request->email && $user->email !== $request->email;
    if ($itDifferentEmail && User::whereEmail($request->email)->exists()) {
      return response()->json(['message' => 'Email already exists'], Response::HTTP_CONFLICT);
    }
    $user->update($request->only(
      'name',
      'email',
      'password',
      'phone',
      'birth',
      'gender',
      'is_already_baptized',
      'already_accepted_term',
      'password_change_required',
      'is_active'
    ));
    return response()->json($user);
  }

  public function delete($user_id): JsonResponse
  {
    $user = User::findOrFail($user_id);
    $user->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }
}
