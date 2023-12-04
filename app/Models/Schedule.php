<?php

namespace App\Models;

use App\Enums\Shifts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class Schedule extends Model
{
  use SoftDeletes;

  protected $table = 'schedules';
  protected $fillable = [
    'date', 'state', 'shift', 'door_id',
  ];
  protected $casts = [
    'shift' => Shifts::class,
  ];
  protected $hidden = ['deleted_at'];

  public function door(): BelongsTo
  {
    return  $this->belongsTo(Door::class);
  }

  public function leaders(): BelongsToMany
  {
    return $this->belongsToMany(
      User::class,
      'schedule_user',
      'schedule_id',
      'leader_id'
    );
  }

  public function tasks(): HasMany
  {
    return $this->hasMany(Task::class);
  }
}
