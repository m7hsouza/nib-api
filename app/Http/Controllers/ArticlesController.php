<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticlesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  public function index(): JsonResponse
  {
    $articles = Article::paginate(10);
    return response()->json($articles);
  }

  public function highlights()
  {
    $articles = Article::where('is_highlighted', true)->paginate(10);
    return response()->json($articles);
  }

  public function show($request, $id)
  {
    $article = Article::findOrFail($id);
    return response()->json($article);
  }

  public function store(Request $request)
  {
    $request->validator([
      'title' => ['required', 'string'],
      'content' => ['required', 'string'],
      'image_url' => ['required', 'string'],
    ]);

    $article = Article::create($request->only('title', 'content', 'image_url'));

    return response()->json($article);
  }

  public function update()
  {

  }

  public function delete()
  {

  }
}
