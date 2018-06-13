<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Car;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    private $salt;
    public function __construct()
    {
        $this->salt = "car";
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
        $car->setAdminName($user->id);
        $car->setCarNumber($request->input('carNumber'));
        $car->setUserName($request->input('userName'));
        $car->setPhone($request->input('phone'));
        $car->setCheckAt($request->input('checkAt'));

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
        $cars[0]->createAt = $cars[0]->created_at->timestamp;
        return response()->json([
            'response' => $cars,
        ]);
    }

    public function updateCar(Request $request) {
        $this->validate($request, [
            'carId' => 'required',
        ]);

        $updateConfig = array();

        if ($request->has('carNumber')) {
            $updateConfig['car_number'] = $request->input('carNumber');
        }

        if ($request->has('userName')) {
            $updateConfig['user_name'] = $request->input('userName');
        }

        if ($request->has('checkAt')) {
            // 时间戳转换为时间
            $dt = new DateTime();
            $dt->setTimestamp((int)$request->input('checkAt'));
            $updateConfig['check_at'] = $dt->format('Y-m-d H:i:s');
        }

        if ($request->has('phone')) {
            $updateConfig['phone'] = $request->input('phone');
        }

        if ($request->has('liked')) {
            $updateConfig['liked'] = $request->input('liked');
        }

        $carId = Car::where('id', '=', $request->input('carId'))->update($updateConfig);

        if ($carId === 0) {
            return response()->json([
                'message' => '该车辆不存在！',
            ]);
        }

        return response()->json([
            'response' => $carId,
        ]);
    }
}
