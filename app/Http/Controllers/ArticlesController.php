<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
    $this->middleware('permission:create article', ['only' => 'store']);
    $this->middleware('permission:read article', ['only' => ['index', 'show']]);
    $this->middleware('permission:update article', ['only' => 'update']);
    $this->middleware('permission:delete article', ['only' => 'delete']);
  }

  public function index(): JsonResponse
  {
    $articles = Article::paginate(10);
    return response()->json($articles);
  }

  public function highlights()
  {
    $articles = Article::where('is_highlighted', true)->orderByDesc('created_at')->get();
    return response()->json($articles);
  }

  public function show($id)
  {
    $article = Article::find($id);
    if (!$article) {
      return response()->json(['message' => 'Article not found!'], Response::HTTP_NOT_FOUND);
    }
    return response()->json($article);
  }

  public function store(Request $request): JsonResponse
  {
    $this->validate($request, [
      'title' => ['required', 'string'],
      'content' => ['required', 'string'],
      'image_url' => ['required', 'string'],
      'is_highlighted' => ['boolean']
    ]);

    $article = Article::create($request->only('title', 'content', 'image_url', 'is_highlighted'));
    $article->refresh();
    return response()->json($article, Response::HTTP_CREATED);
  }

  public function update(Request $request, $id)
  {
    $this->validate($request, [
      'title' => 'string',
      'content' => 'string',
      'image_url' => 'url',
      'is_highlighted' => 'boolean'
    ]);

    $article = Article::find($id);
    if (!$article) {
      return response()->json(['message' => 'Article not found!'], Response::HTTP_NOT_FOUND);
    }

    $article->update($request->only('title', 'content', 'image_url', 'is_highlighted'));
    return response()->json($article);
  }

  public function delete($id)
  {
    $article = Article::find($id);
    if (!$article) {
      return response()->json(['message' => 'Article not found!'], Response::HTTP_NOT_FOUND);
    }
    $article->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }

}
