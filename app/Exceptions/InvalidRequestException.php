<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRequestException extends HttpException
{
    protected $message = '非法请求';
    protected $code = 'invalid.request';

    public function __construct()
    {
        parent::__construct(401, $this->message);
    }
}
