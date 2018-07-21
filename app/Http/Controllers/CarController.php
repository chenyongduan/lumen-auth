<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Car;
use App\Exceptions\CarNotExistException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

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

        $car = Car::where("car_number", "=", $request->input('carNumber'))->first();
        if ($car) {
            return response()->json([
                'message' => "已有该车牌号的车辆信息！",
            ]);
        }

        // 获取用户信息
        $user = User::where("token", "=", $request->header('token'))->first();

        $car = new Car;
        $car->setAdminId($user->id);
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
        $result = [];
        foreach ($cars as $car) {
            $result[] = $car->toDisplay();
        }
        return response()->json([
            'response' => $result,
        ]);
    }

    public function updateCar(Request $request, $id) {
        $request->merge(['id' => $id]);

        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $car = Car::find(intval($id));
        if (empty($car)) throw new CarNotExistException();

        $updateConfig = array();

        if ($request->has('carNumber')) {
            $car->setCarNumber($request->input('carNumber'));
        }

        if ($request->has('userName')) {
            $car->setUserNumber($request->input('userName'));
        }

        if ($request->has('phone')) {
            $car->setPhone($request->input('phone'));
        }

        if ($request->has('liked')) {
            $car->setLiked($request->input('liked'));
        }

        if ($request->has('checkAt')) {
            $car->setCheckAt($request->input('checkAt'));
        }

        $car->save();

        return response()->json([
            'response' => $car->toDisplay(),
        ]);
    }
}
