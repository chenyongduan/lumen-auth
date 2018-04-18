<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends Controller
{
    private $salt;
    public function __construct()
    {
        $this->salt="userloginregister";
    }

    public function login(Request $request){
        if ($request->has('username')) {
            $user = User::where("username", "=", $request->input('username'))->first();
            if ($user) {
                return $user->token;
            } else {
                $user = new User;
                $user->username = $request->input('username');
                $user->token = str_random(60);

                if($user->save()){
                    return $user->token; 
                } else {
                    return "用户注册失败！";
                }
            }
        } else {
            return "登录信息不完整，请输入用户名和密码登录！";
        }
    }

    public function info(Request $request) {
        $user = User::where("token", "=", $request->header('token'))->first();
        return $user->id.'='.$user->username;
    }
}
