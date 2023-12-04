<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\{Request, JsonResponse};
use Symfony\Component\HttpFoundation\Response;

class VideosController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
    $this->middleware('permission:video.create', ['only' => 'store']);
    $this->middleware('permission:video.read', ['only' => ['index', 'show']]);
    $this->middleware('permission:video.update', ['only' => 'update']);
    $this->middleware('permission:video.delete', ['only' => 'delete']);
  }

  public function index(): JsonResponse
  {
    $videos = Video::with('user:id,name')->orderByDesc('updated_at')->cursorPaginate(10);
    return response()->json($videos);
  }

  public function show($video_id)
  {
    $video = Video::findOrFail($video_id);
    return response()->json($video);
  }

  public function store(Request $request): JsonResponse
  {
    $this->validate(
      $request,
      [
        'title' => 'required|string',
        'description' => 'required|string',
        'video' => 'required|mimetypes:video/*',
      ],
      [
        'title.required' => 'O titulo é obrigatório',
        'description.required' => 'A descrição é obrigatória',
        'video' => [
          'required' => 'O video é obrigatório.',
          'mimetypes' => 'Formato do video inválido.'
        ]
      ]
    );
    $video = $request->file('video');
    $filename = Uuid::uuid4()->toString() . '.' . $video->getClientOriginalExtension();
    Storage::disk('videos')->put($filename, $video->getContent());
    $video = Video::create([
      ...compact('filename'),
      ...$request->only('title', 'description'),
    ]);
    $video->refresh();
    return response()->json($video, Response::HTTP_CREATED);
  }

  public function update(Request $request, $video_id)
  {
    $this->validate($request, [
      'title' => 'string',
      'description' => 'string',
    ]);
    $video = Video::findOrFail($video_id);
    $video->update($request->only('title', 'description'));
    return response()->json($video);
  }

  public function delete($video_id)
  {
    $video = Video::findOrFail($video_id);
    $video->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }

  public function file($video_id)
  {
    $video = Video::select('filename')->findOrFail($video_id);
    return response()->file(Storage::disk('videos')->path($video->filename));
  }
}
