<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
  use Authenticatable, Authorizable, HasFactory, HasRoles;

  protected static function boot(): void
  {
    parent::boot();

    self::creating(function (User $user) {
      ['enrollment_number' => $lastEnrollmentNumber] = User::select('enrollment_number')->orderByDesc('id')->first();
      $enrollment_number = $lastEnrollmentNumber ? ++$lastEnrollmentNumber : 0;
      $user->enrollment_number = mb_str_pad($enrollment_number, 6, '0', STR_PAD_LEFT);
      return $user;
    });
  }

  protected $table = 'users';
  protected $fillable = [
    'name', 'email', 'password', 'avatar_url', 'phone', 'birth', 'gender',
    'state', 'password_change_required', 'is_already_baptized', 'already_accepted_term'
  ];
  protected $hidden = [
    'password',
    'remember_token'
  ];

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
