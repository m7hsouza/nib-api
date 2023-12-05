<?php

namespace App\Http\Controllers;

use Ramsey\Uuid\Uuid;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\{Request, JsonResponse};
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends Controller
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
    $articles = Article::with('author:id,name,avatar_filename')->orderByDesc('updated_at')->cursorPaginate(10);
    return response()->json($articles);
  }

  public function recentArticles(): JsonResponse
  {
    $articles = Article::with('author:id,name,avatar_filename')
      ->orderByDesc('updated_at')
      ->limit(6)
      ->get();
    return response()->json($articles);
  }

  public function show($article_id)
  {
    $article = Article::with('author:id,name,avatar_filename')->findOrFail($article_id);
    return response()->json($article);
  }

  public function store(Request $request): JsonResponse
  {
    $this->validate(
      $request,
      [
        'title' => 'required|string',
        'content' => 'required|string',
        'image' => 'required|mimes:jpg,png,jpeg',
      ],
      [
        'title.required' => 'O titulo é obrigatório',
        'content.required' => 'A descrição é obrigatória',
        'image' => [
          'required' => 'O arquivo é obrigatório.',
          'mimes' => 'Formato do arquivo inválido.'
        ]
      ]
    );
    $file = $request->file('image');
    $filename = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
    Storage::disk('articles')->put($filename, $file->getContent());
    $article = Article::create([
      ...compact('filename'),
      ...$request->only('title', 'content'),
    ]);
    $article->refresh();
    return response()->json($article, Response::HTTP_CREATED);
  }

  public function update(Request $request, $article_id)
  {
    $this->validate($request, [
      'title' => 'string',
      'content' => 'string',
    ]);
    $article = Article::findOrFail($article_id);
    $article->update($request->only('title', 'content'));
    return response()->json($article);
  }

  public function delete($article_id)
  {
    $article = Article::findOrFail($article_id);
    $article->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }

  public function image($article_id)
  {
    $article = Article::select('filename')->findOrFail($article_id);
    return response()->file(Storage::disk('articles')->path($article->filename));
  }
}
