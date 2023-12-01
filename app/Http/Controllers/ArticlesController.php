<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Ramsey\Uuid\Uuid;
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
    $articles = Article::with('author:id,name,avatar_url')->orderByDesc('updated_at')->cursorPaginate(10);
    return response()->json($articles);
  }

  public function highlights()
  {
    $articles = Article::with('author:id,name,avatar_url')->where('is_highlighted', true)->orderByDesc('updated_at')->get();
    return response()->json($articles);
  }

  public function show($id)
  {
    $article = Article::findOrFail($id);
    return response()->json($article);
  }

  public function store(Request $request): JsonResponse
  {
    $this->validate($request, [
      'title' => ['required', 'string'],
      'content' => ['required', 'string'],
      'image' => ['required', 'file'],
      'is_highlighted' => ['boolean']
    ]);
    $filename = Uuid::uuid4()->toString() . '.' . $request->file('image')->getClientOriginalExtension();
    $request->file('image')->move(base_path('public/uploads/articles'), $filename);
    $article = Article::create([
      'image_url' => "/uploads/articles/$filename",
      ...$request->only('title', 'content', 'is_highlighted'),
    ]);
    $article->refresh();
    return response()->json($article, Response::HTTP_CREATED);
  }

  public function update(Request $request, $id)
  {
    $this->validate($request, [
      'title' => 'string',
      'content' => 'string',
      'is_highlighted' => 'boolean'
    ]);
    $article = Article::findOrFail($id);
    $article->update($request->only('title', 'content', 'is_highlighted'));
    return response()->json($article);
  }

  public function delete($id)
  {
    $article = Article::findOrFail($id);
    $article->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }
}
