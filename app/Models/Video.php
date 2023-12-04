<?php

namespace App\Models;

use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class Video extends Model implements AuthorizableContract
{
  use Authorizable, SoftDeletes;

  protected $table = 'videos';
  protected $fillable = ['title', 'description', 'filename', 'likes'];
  protected $hidden = ['deleted_at', 'filename'];
  protected $appends = ['url'];

  protected static function boot(): void
  {
    parent::boot();
    self::creating(function (Video $video) {
      $video->user_id = auth()->id();
      return $video;
    });
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
  
  public function url(): Attribute
  {
    return Attribute::get(fn () => route('video.file', ['video_id' => $this->id]));
  }
}
