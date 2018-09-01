<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use App\Models\User;

class Image extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['admin_id', 'car_id', 'image_name'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function getImages($token) {
        $userModel = new User($token);
        $userId = $userModel->getUserId();
        return $this->where("admin_id", "=", $userId)->get();
    }

    public function deleteImage($carId, $imageName) {
        return $this->where("car_id", "=", $carId)->where("image_name", "=", $imageName)->delete();
    }
}
