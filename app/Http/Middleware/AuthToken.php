<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class AuthToken
{
    public function handle($request, Closure $next)
    {
        $user = User::where("token", "=", $request->header('token'))->first();
        if($user){
            return $next($request);
        }else{
            abort(401);
        }
    }
}