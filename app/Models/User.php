<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    public function __construct($token = null)
    {
        $this->token = $token;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['admin_name', 'token'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function setAdminName($value) {
        $this->admin_name = $value;
    }

    public function setPassword($value) {
        $this->password = $value;
    }

    public function setToken($value) {
        $this->token = $value;
    }


    public function getUser() {
        return $this->where("token", "=", $this->token)->first();
    }

    public function getUserId() {
        $userInfo = $this->where("token", "=", $this->token)->first();
        return $userInfo->id;
    }

    public function getUserIdByToken($token) {
        if (Cache::has($token)) {
            return Cache::get($token);
        }
        $user = $this->where("token", "=", $token)->first();
        Log::info($user);
        if ($user) {
            $userId = $user->id;
            Cache::forever($token, $userId);
            return $userId;
        }
        return null;
    }
}
