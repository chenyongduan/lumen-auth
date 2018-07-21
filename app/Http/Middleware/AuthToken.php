<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class AuthToken
{
    public function handle($request, Closure $next)
    {
        $user = new User;
        $userId = $user->getUserIdByToken($request->header('token'));
        if($userId){
            return $next($request);
        }else{
            return response()->json([
                'code' => 401,
                'message' => "用户未认证！",
            ]);
        }
    }
}