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
        ]);
        $user = User::where("admin_name", "=", $request->input('adminName'))->first();
        if ($user) {
            // return $user->token;
            return response()->json([
                'response' => $user->token,
            ]);
        } else {
            $user = new User;
            $user->admin_name = $request->input('adminName');
            $user->token = str_random(60);

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
