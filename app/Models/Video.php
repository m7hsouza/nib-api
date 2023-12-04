<?php

namespace App\Models;

use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class Video extends Model implements AuthorizableContract
{
  use Authorizable, SoftDeletes;

  protected $table = 'articles';
  protected $fillable = ['title', 'description', 'filename', 'likes'];
  protected $hidden = ['deleted_at', 'filename'];

  protected static function boot(): void
  {
    parent::boot();
    self::retrieved(function (Video $video) {
      $video->file_url = route('video.file', [$video->id]);
    });
    self::creating(function (Video $video) {
      $video->user_id = auth()->id();
      return $video;
    });
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
