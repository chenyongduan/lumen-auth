<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;

class UserController extends Controller
{
    private $salt;
    public function __construct()
    {
        $this->salt="userlogin";
    }

    public function login(Request $request){
        $this->validate($request, [
            'adminName' => 'required',
            'password' => 'required',
        ]);
        
        $adminName = $request->input('adminName');
        $password = $request->input('password');

        $user = User::where("admin_name", "=", $adminName)->first();
        if ($user) {
            $isLogin = User::where("admin_name", "=", $adminName)->where('password', '=', $password)->exists();
            if (!$isLogin) {
                return response()->json([
                    'message' => '密码错误',
                ]);
            } 
            return response()->json([
                'response' => $user->token,
            ]);
        } else {
            $user = new User;
            $user->setAdminName($adminName);
            $user->setPassword($password);
            $user->setToken(str_random(60));

            if($user->save()){
                return response()->json([
                    'response' => $user->token,
                ]);
            } else {
                return response()->json([
                    'message' => "用户注册失败！",
                ]);
            }
        }
    }

    public function userInfo(Request $request) {
        $user = User::where("token", "=", $request->header('token'))->first();
        return response()->json([
            'response' => $user,
        ]);
    }
}
