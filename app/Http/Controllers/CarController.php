<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Car;
use App\Exceptions\CarNotExistException;
use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Log;

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
                'message' => "已添加过该车辆",
            ]);
        }

        // 获取用户信息
        $user = User::where("token", "=", $request->header('token'))->first();

        $car = new Car;
        $car->setAdminId($user->id);
        $car->setCarNumber($request->input('carNumber'));
        $car->setUserName($request->input('userName'));
        $car->setPhone($request->input('phone'));
        $car->setCheckPrice($request->input('checkPrice'));
        $car->setCheckAt($request->input('checkAt'));

        if($car->save()){
            return response()->json([
                'response' => $car->toDisplay(),
            ]);
        } else {
            return response()->json([
                'message' => "添加车辆失败！",
            ]);
        }
    }

    public function carList(Request $request) {
        $this->validate($request, [
            "pageSize" => "integer|max:100|min:3",
            "page" => "integer|min:1"
        ]);
        // 获取用户信息
        $user = new User();
        $userId = $user->getUserIdByToken($request->header('token'));
        // 查找所有车辆信息
        $query = Car::where('admin_id', '=', $userId);
        // 分页
        $page = intval($request->input("page", 1));
        $pageSize = intval($request->input("pageSize", 10));
        $total = $query->count();
        $cars = $query->orderBy('check_at')->skip($pageSize * ($page - 1))->take($pageSize)->get();
        $result = [];
        foreach ($cars as $car) {
            $result[] = $car->toDisplay();
        }

        return response()->json([
            'response' => [
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize,
                'result' => $result
            ],
        ]);
    }

    public function updateCar(Request $request, $id) {
        $request->merge(['id' => $id]);

        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $user = new User();
        $userId = $user->getUserIdByToken($request->header('token'));
        
        $car = Car::find(intval($id));
        if (empty($car)) throw new CarNotExistException();

        if ($userId != $car->getAdminId()) throw new InvalidRequestException();

        if ($request->has('carNumber')) {
            $car->setCarNumber($request->input('carNumber'));
        }

        if ($request->has('userName')) {
            $car->setUserName($request->input('userName'));
        }

        if ($request->has('phone')) {
            $car->setPhone($request->input('phone'));
        }

        if ($request->has('liked')) {
            $car->setLiked($request->input('liked'));
        }

        if ($request->has('checkPrice')) {
            $car->setCheckPrice($request->input('checkPrice'));
        }

        if ($request->has('checkAt')) {
            $car->setCheckAt($request->input('checkAt'));
        }

        $car->save();

        return response()->json([
            'response' => $car->toDisplay(),
        ]);
    }

    public function searchCar(Request $request) {
        $this->validate($request, [
            'searchValue' => 'required|string',
        ]);
    
        $searchValue = $request->input('searchValue');
        $likeStr = '%'.$searchValue.'%';

        $user = new User();
        $userId = $user->getUserIdByToken($request->header('token'));
        $cars = Car::where('admin_id', '=', $userId)
        ->where('car_number', 'like', $likeStr)
        ->orWhere('user_name', 'like', $likeStr)->orderBy('check_at')->get();
        
        $result = [];
        foreach ($cars as $car) {
            $result[] = $car->toDisplay();
        }
        return response()->json([
            'response' => $result,
        ]);
    }

    public function deleteCar(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);
    
        $user = new User();
        $userId = $user->getUserIdByToken($request->header('token'));
        $id = $request->input('id');
        $deleteRet = Car::where('admin_id', '=', $userId)->where('id', '=', $id)->delete();

        if (!$deleteRet) {
            throw new CarNotExistException();
        }

        return response()->json([
            'response' => $id,
        ]);
    }
}
