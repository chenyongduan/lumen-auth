<?php

namespace App\Models;

use DateTime;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Car extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'car_number',
        'user_name',
        'phone',
        'liked',
        'check_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['check_at'];

    public function getId() {
        return $this->id;
    }

    public function getAdminId() {
        return $this->admin_id;
    }

    public function getCarNumber() {
        return $this->car_number;
    }

    public function getUserName()
    {
        return $this->user_name;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getLiked()
    {
        return $this->liked;
    }

    public function getCheckAt()
    {
        return $this->check_at;
    }

    public function setAdminId($value) {
        $this->admin_id = $value;
    }

    public function setCarNumber($value) {
        $this->car_number = $value;
    }

    public function setUserName($value) {
        $this->user_name = $value;
    }

    public function setPhone($value) {
        $this->phone = $value;
    }

    public function setLiked($value) {
        $this->liked = $value;
    }

    public function setCheckAt($value) {
        $dt = new DateTime();
        $dt->setTimestamp((int)$value);
        $this->check_at = $dt->format('Y-m-d H:i:s');
    }

    public function toDisplay()
    {
        return [
            'id' => $this->getId(),
            'adminId' => $this->getAdminId(),
            'carNumber' => $this->getCarNumber(),
            'userName' => $this->getUserName(),
            'phone' => $this->getPhone(),
            'liked' => $this->getLiked(),
            'checkAt' => $this->getCheckAt()->timestamp,
        ];
    }
}
