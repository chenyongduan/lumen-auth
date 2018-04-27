<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Car;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

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

    public function uploadImage(Request $request) {
        if (!$request->hasFile('image')) {
            return response()->json([
                'message' => '图片不存在！',
            ]);
        }

        $imageFile = $request->file('image');

        $type = $imageFile->getClientMimeType();
        if ($type !== 'image/png' && $type !== 'image/jpeg' && $type !== 'image/jpg') {
            return response()->json([
                'message' => '只能上传图片！',
            ]);     
        }
        if (!$imageFile->isValid()) {
            return response()->json([
                'message' => '上传的图片无效！',
            ]);
        }

        // 获取用户信息
        $token = $request->header('token');
        $userModel = new User($token);
        $userInfo = $userModel->getUser();
        
        // 保存图片到public/images/目录下，名字随机生存
        $path = $imageFile->store('images');

        $imageModel = new Image;
        $imageModel->admin_id = $userInfo->id;
        $imageModel->image_name = $path;
        $imageModel->save();

        return response()->json([
            'response' => '保存图片成功！',
        ]);
    }

    public function imageList(Request $request) {
        // 获取用户信息
        $token = $request->header('token');
        $imageModel = new Image($token);
        return response()->json([
            'response' => $imageModel->getImages(),
        ]);
    }
}
