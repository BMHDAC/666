<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Structs\User\UserStruct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasUlids, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
//        'class',
//        'student',
//        'position',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims() : array
    {
        return [];
    }
    public function struct() :UserStruct
    {
        return new UserStruct($this->getAttributes());
    }

    public static function checkExists(array $credentials): bool
    {
        return self::query()
            ->where(function ($query) use ($credentials) {
                foreach ($credentials as $key => $value) {
                    $query->orWhere($key, $value);
                }
            })
            ->exists();
    }
    public static function addUserGetID(array $data): string
    {
        return self::query()->insertGetId($data);
    }
    public static function getUserByName(string $name, array $filter): Builder|Model|null
    {
        return self::query()
            ->where(function ($query) use ($name) {
                $query
                    ->orWhere('username', $name)
                    ->orWhere('email', $name);
            })
            ->distinct()
            ->first($filter);
    }
    public static function getUser(string $id, $select = ['*'], array $opts = []): ?object
    {
        if (!Str::isUlid($id)) {
            return null;
        }

        return self::query()
            ->select(['name', 'email'])
            ->where('id', $id)
            ->distinct()
            ->first();
    }
    public static function getUserByCredentials(array $credentials, array $filter): Model|Builder|null
    {
        return self::query()
            ->where(function ($query) use ($credentials) {
                foreach ($credentials as $key => $value) {
                    $query->where($key, $value);
                }
            })->distinct()->first($filter);
    }
}
