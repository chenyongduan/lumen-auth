<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;
use App\Models\User;
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
        $this->validate($request, [
            'username' => 'required',
        ]);
        $user = User::where("username", "=", $request->input('username'))->first();
        if ($user) {
            // return $user->token;
            return response()->json([
                'response' => $user->token,
            ]);
        } else {
            $user = new User;
            $user->username = $request->input('username');
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

    public function info(Request $request) {
        $user = User::where("token", "=", $request->header('token'))->first();
        return response()->json([
            'response' => $user,
        ]);
    }
}
