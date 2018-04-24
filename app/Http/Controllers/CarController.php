<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Car;

class CarController extends Controller
{
    private $salt;
    public function __construct()
    {
        $this->salt="car";
    }

    public function createCar(Request $request){
        $this->validate($request, [
            'carNumber' => 'required',
            'userName' => 'required',
            'checkAt' => 'required',
        ]);
        
        // 获取用户信息
        $user = User::where("token", "=", $request->header('token'))->first();
        // 时间戳转换为时间
        $dt = new DateTime();
        $dt->setTimestamp((int)$request->input('checkAt'));

        $car = new Car;
        $car->admin_id = $user->id;
        $car->car_number = $request->input('carNumber');
        $car->user_name = $request->input('userName');
        $car->check_at = $dt->format('Y-m-d H:i:s');

        if($car->save()){
            return response()->json([
                'message' => "添加车辆成功！",
            ]);
        } else {
            return response()->json([
                'message' => "添加车辆失败！",
            ]);
        }
    }

    public function carList(Request $request) {
        // 获取用户信息
        $user = User::where("token", "=", $request->header('token'))->first();

        $cars = Car::where('admin_id', '=', $user->id)->paginate(10);

        return response()->json([
            'response' => $cars,
        ]);
    }
}
