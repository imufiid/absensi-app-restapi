<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get by user
        try {
            $userId = request('id');

            if ($userId == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed Bad Request"
                    ),
                    'data' => null
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $user = User::where("id", $userId)->first();
                if (empty($user)) {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_NOT_FOUND,
                            "failed",
                            "User Not Found"
                        ),
                        'data' => null
                    ], Response::HTTP_NOT_FOUND);
                } else {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_OK,
                            "success",
                            "This data user"
                        ),
                        'data' => $user
                    ], Response::HTTP_OK);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_EXPECTATION_FAILED,
                    "error",
                    "Error Server "
                ),
                'data' => null
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $userId = request("id");
        $userImg = request("image");
        if ($userId == null) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "failed",
                    "Failed Bad Request"
                ),
                'data' => null
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $pathImg = $_SERVER['DOCUMENT_ROOT'] . "/img/image_user/";

            $currentData = User::where("id", $userId)->first();
            $userName = (request("name") == null)
                ? $currentData->name
                : request("name");
            $password = (request("password") == null)
                ? $currentData->password
                : bcrypt(request("password"));

            if ($userImg != null) {
                unlink($pathImg . $currentData->profile_image);
                $userImgName = time() . "_" . $userImg->getClientOriginalName();
                $destSave = 'img/image_user';
                $userImg->move($destSave, $userImgName);
            } else {
                $userImgName = $currentData->profile_image;
            }

            $data = [
                'name'      => $userName,
                'password'  => $password,
                "profile_image"     => $userImgName
            ];

            $dataUpdate = User::where("id", $userId)
                ->update($data);

            if ($dataUpdate > 0) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_OK,
                        "success",
                        "Success Update Data"
                    ),
                    'data' => $dataUpdate
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        "failed",
                        "Failed Update Data"
                    ),
                    'data' => null
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }
}
