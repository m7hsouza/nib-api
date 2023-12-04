<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Enums\Shifts;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Http\{Request, JsonResponse};
use Symfony\Component\HttpFoundation\Response;

class SchedulesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
    $this->middleware('permission:schedule.create', ['only' => 'store']);
    $this->middleware('permission:schedule.all', ['only' => 'index']);
    $this->middleware('permission:schedule.delete', ['only' => 'delete']);
    $this->middleware(
      'permission:schedule.update',
      [
        'only' => [
          'update',
          'createTask',
          'updateTask',
          'deleteTask'
        ]
      ]
    );
  }

  public function index(): JsonResponse
  {
    $schedules = Schedule::paginate(10);
    return response()->json($schedules);
  }

  public function store(Request $request): JsonResponse
  {
    $now = date('Y-m-d');
    $this->validate(
      $request,
      [
        'date' => "required|date|after:$now",
        'state' => 'required',
        'shift' => ['required', new Enum(Shifts::class)],
        'door_id' => 'required|exists:doors,id',
        'leaders' => 'required|array|min:1',
        'leaders.*' => 'required|exists:users,id',
      ]
    );
    $existsScheduleInSameDate = Schedule::where(
      $request->only('date','shift', 'door_id')
    )->exists();
    if ($existsScheduleInSameDate) {
      return response()->json(
        ['message' => 'There is already a schedule for this entry at the same time'],
        Response::HTTP_CONFLICT
      );
    }
    DB::transaction(function () use ($request) {
      $schedule = Schedule::create($request->only('date', 'door_id', 'state', 'shift'));
      $schedule->leaders()->sync(...$request->leaders);
    });
    return response()->json(status: Response::HTTP_CREATED);
  }

  public function show($schedule_id): JsonResponse
  {
    $schedule = Schedule::with([
      'leaders:id,name,email,avatar_url',
      'door:id,name',
      'tasks',
    ])->findOrFail($schedule_id);
    return response()->json($schedule);
  }

  public function update(Request $request, $schedule_id): JsonResponse
  {
    $now = date('Y-m-d');
    $this->validate(
      $request,
      [
        'date' => "date|after:$now",
        'shift' => [new Enum(Shifts::class)],
        'door_id' => 'exists:doors,id',
        'leaders' => 'array|min:1',
        'leaders.*' => 'exists:users,id',
      ]
    );

    $schedule = Schedule::findOrFail($schedule_id);
    $existsScheduleInSameDate = Schedule::whereNot('id', $schedule_id)
      ->where('date', $request->date ?? $schedule->date)
      ->where('shift', $request->shift ?? $schedule->shift)
      ->where('door_id', $request->door_id ?? $schedule->door_id)
      ->exists();

    if ($existsScheduleInSameDate) {
      return response()->json(
        ['message' => 'There is already a schedule for this entry at the same time'],
        Response::HTTP_CONFLICT
      );
    }
    DB::transaction(function () use ($request, $schedule) {
      $schedule->update($request->only('date', 'door_id', 'shift'));
      if ($request->leaders) {
        $schedule->leaders()->sync($request->leaders);
      }
    });
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }

  public function delete($schedule_id): JsonResponse
  {
    $schedule = Schedule::findOrFail($schedule_id);
    $schedule->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }

  public function createTask(Request $request, $schedule_id): JsonResponse
  {
    $request->merge(compact('schedule_id'));
    $this->validate($request, [
      'description' => 'required|string',
      'schedule_id' => 'required|exists:schedules,id',
      'responsible_id' => 'required|exists:users,id',
    ]);
    $createTaskParams = $request->only('description', 'schedule_id', 'responsible_id');
    $task = Task::create($createTaskParams);
    return response()->json($task, status: Response::HTTP_CREATED);
  }

  public function updateTask(Request $request, $task_id): JsonResponse
  {
    $this->validate($request, [
      'description' => 'string',
      'responsible_id' => 'exists:users,id',
    ]);
    $task = Task::findOrFail($task_id);
    $createTaskParams = $request->only('description', 'responsible_id');
    $task->update($createTaskParams);
    return response()->json($task);
  }

  public function deleteTask($task_id): JsonResponse
  {
    $task = Task::findOrFail($task_id);
    $task->delete();
    return response()->json(status: Response::HTTP_NO_CONTENT);
  }
}
