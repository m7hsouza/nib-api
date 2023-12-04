<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
  use Authenticatable, Authorizable, HasFactory, HasRoles;

  protected static function boot(): void
  {
    parent::boot();
    self::retrieved(function (User $user) {
      $user->avatar_url = route('user.avatar', ['user_id' => $user->id]);
    });
    self::creating(function (User $user) {
      ['enrollment_number' => $lastEnrollmentNumber] = User::select('enrollment_number')->orderByDesc('id')->first();
      $enrollment_number = $lastEnrollmentNumber ? ++$lastEnrollmentNumber : 0;
      $user->enrollment_number = mb_str_pad($enrollment_number, 6, '0', STR_PAD_LEFT);
      return $user;
    });
  }

  protected $table = 'users';
  protected $fillable = [
    'name', 'email', 'phone', 'birth', 'gender', 'password', 'avatar_filename',
    'is_active', 'password_change_required', 'is_already_baptized', 'already_accepted_term'
  ];
  protected $hidden = [
    'password',
    'remember_token',
    'email_verified_at',
    'avatar_filename'
  ];

  public function scopeActive(Builder $query): void
  {
    $query->where('is_active', true);
  }

  public function password(): Attribute
  {
    return Attribute::set(fn ($value) => Hash::make($value));
  }

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims()
  {
    return [];
  }
}
