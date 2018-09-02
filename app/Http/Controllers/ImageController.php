<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    private $salt;
    public function __construct()
    {
        $this->salt = "image";
    }

    public function uploadImage(Request $request) {
        $this->validate($request, [
            'carId' => 'required',
        ]);

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
        $userModel = new User();
        $userId = $userModel->getUserIdByToken($token);
        $carId = $request->input('carId');
        $dir = 'images'.'/'.$userId.'/'.$carId;
        // 保存图片到public/images/目录下，名字随机生存
        $path = $imageFile->store($dir);

        $imageModel = new Image;
        $imageModel->admin_id = $userId;
        $imageModel->image_name = $path;
        $imageModel->car_id = $carId;
        $imageModel->save();

        return response()->json([
            'response' => $path,
        ]);
    }

    // public function imageList(Request $request) {
    //     // 获取用户信息
    //     $token = $request->header('token');
    //     $imageModel = new Image();
    //     return response()->json([
    //         'response' => $imageModel->getImages($token),
    //     ]);
    // }

    public function deleteImage(Request $request) {
        $this->validate($request, [
            'imageName' => 'required',
            'carId' => 'required',
        ]);

        $token = $request->header('token');
        $userModel = new User();
        $userId = $userModel->getUserIdByToken($token);

        $imageName = $request->input('imageName');
        $carId = $request->input('carId');

        if(!Storage::delete($imageName)){
            return response()->json([
                'message' => '图片不存在！',
            ]);
        }

        $imageModel = new Image();
        $ret = $imageModel->deleteImage($userId, $carId, $imageName);

        return response()->json([
            'response' => $ret,
        ]);
    }
}
