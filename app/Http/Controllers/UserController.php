<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * name show
     * desc
     *
     * @urlParam name required Example: admin
     */
    public function show(Request $request, $id)
    {
        // if(!Gate::allows(GateAbility::, $id)){
        //     return ['code' => 403];
        // }
        // return TABLE::where('roomid', $id)->get();
        $is_login = false;

        try {
            $auth_header = $request->header('Authorization');
            $auth = explode(":", str_replace("Basic ", "", $auth_header));
            $user_id = base64_decode($auth[0]);
            $password = $auth[1];

            $userinfo = User::where(['user_id' => $user_id, 'password' => $password]);
            if (!$userinfo->exists()) {
                return response(["message" => "Authentication Failed"], 401);
            }
            $is_login = $userinfo->exists();
        } catch (Exception $e) {

        }

        $target = User::where('user_id', $request->user_id);
        if (!$target->exists()) {
            return response([
                "message" => "No User found",
            ], 404);
        }

        $u = $target->get()[0];
        if ($is_login == false) {
            return [
                "message" => "User details by user_id",
                "user" => [
                    "user_id" => $u->user_id,
                    "nickname" => $u->nickname,
                ],
            ];
        } else {
            return [
                "message" => "User details by user_id",
                "user" => [
                    "user_id" => $u->user_id,
                    "nickname" => $u->nickname,
                    "comment" => $u->comment,
                ],
            ];
        }
    }

    /**
     * name update
     * desc
     *
     * @urlParam name required Example: admin
     * @queryParam name
     * @queryParam description
     */
    public function update(Request $request, $id)
    {
        $is_login = false;

        $auth_header = $request->header('Authorization');
        $auth = explode(":", str_replace("Basic ", "", $auth_header));
        Log::debug($auth);
        $user_id = base64_decode($auth[0]);
        $password = $auth[1];

        $userinfo = User::where(['user_id' => $user_id, 'password' => $password]);
        if (!$userinfo->exists()) {
            return response(["message" => "Authentication Failed"], 401);
        }
        $is_login = $userinfo->exists();

        try {
        } catch (Exception $e) {

        }

        $valid_dict = [
            'nickname' => [],
            'comment' => [],
        ];
        $request->validate($valid_dict);
        $data = $request->only(array_keys($valid_dict));

        $target = User::where('user_id', $request->user_id);
        if (!$target->exists()) {
            return response([
                "message" => "No User found",
            ], 404);
        }

        if (!$request->has("nickname") && !$request->has('comment')) {
            return response([
                "message" => "User updation failed",
                "cause" => "required nickname or comment",
            ], 400);
        }

        if ($request->has('user_id') || $request->has('password')) {
            return response([
                "message" => "User updation failed",
                "cause" => "not updatable user_id and password",
            ], 400);
        }

        $u = $target->get()[0];

        if ($is_login == false || $user_id != $request->user_id) {
            return response([
                "message" => "No Permission for Update",
            ], 403);
        } else {
            $u->update($data);
            return [
                "message" => "User successfully updated",
                "recipe" => [
                    [
                        "nickname" => $request->nickname,
                        "comment" => $request->comment,
                    ],
                ],
            ];
        }
    }
}
