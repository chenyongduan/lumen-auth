<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CarNotExistException extends HttpException
{
    protected $message = '车辆信息不存在';
    protected $code = 'car.not.exist';

    public function __construct()
    {
        parent::__construct(404, $this->message);
    }
}
