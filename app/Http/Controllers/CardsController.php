<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\{Request, JsonResponse};
use Symfony\Component\HttpFoundation\Response;

class CardsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
    $this->middleware('permission:article.create', ['only' => 'store']);
    $this->middleware('permission:article.read', ['only' => ['index', 'show']]);
    $this->middleware('permission:article.update', ['only' => 'update']);
    $this->middleware('permission:article.delete', ['only' => 'delete']);
  }

  public function index(): JsonResponse
  {
    $articles = Card::orderByDesc('updated_at')->cursorPaginate(10);
    return response()->json($articles);
  }

  public function show($card_id)
  {
    $card = Card::findOrFail($card_id);
    return response()->json($card);
  }

  public function store(Request $request): JsonResponse
  {
    $this->validate(
      $request,
      [
        'title' => 'required|string',
        'image' => 'required|mimes:jpg,png,jpeg',
      ],
      [
        'title.required' => 'O titulo é obrigatório',
        'image' => [
          'required' => 'O arquivo é obrigatório.',
          'mimes' => 'Formato do arquivo inválido.'
        ]
      ]
    );
    $file = $request->file('image');
    $filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
    Storage::disk('articles')->put($filename, $file->getContent());
    $card = Card::create([
      ...compact('filename'),
      ...$request->only('title'),
    ]);
    $card->refresh();
    return response()->json($card, Response::HTTP_CREATED);
  }

  public function update(Request $request, $card_id)
  {
    $this->validate($request, ['title' => 'string']);
    $card = Card::findOrFail($card_id);
    $card->update($request->only('title'));
    return response()->json($card);
  }

  public function delete($card_id)
  {
    $card = Card::findOrFail($card_id);
    $card->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }

  public function image($card_id)
  {
    $card = Card::select('filename')->findOrFail($card_id);
    return response()->file(Storage::disk('articles')->path($card->filename));
  }
}
