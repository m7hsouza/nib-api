<?php

namespace App\Models;

use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class Card extends Model implements AuthorizableContract
{
  use Authorizable, SoftDeletes;

  protected $table = 'cards';
  protected $fillable = ['title', 'filename'];
  protected $hidden = ['deleted_at', 'filename'];

  protected static function boot(): void
  {
    parent::boot();
    self::retrieved(function (Card $card) {
      $card->image_url = route('card.image', [$card->id]);
    });
    self::creating(function (Card $card) {
      $card->user_id = auth()->id();
      return $card;
    });
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
