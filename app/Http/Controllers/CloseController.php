<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class CloseController extends Controller
{
    /**
     * name store
     * desc
     *
     * @queryParam title string Example: イベントタイトル
     */
    public function store(Request $request)
    {
        $is_login = false;

        try {
            $auth_header = $request->header('Authorization');
            $auth = explode(":", base64_decode(str_replace("Basic ", "", $auth_header)));
            $user_id = $auth[0];
            $password = $auth[1];

            $userinfo = User::where(['user_id' => $user_id, 'password' => $password]);
            if (!$userinfo->exists()) {
                return response(["message" => "Authentication Faild"], 401);
            }
            $userinfo->delete();
            return [
                "message" => "Account and user successfully removed",
            ];
            // $is_login = $userinfo->exists();
        } catch (Exception $e) {
            return response(["message" => "Authentication Faild"], 401);
        }
    }

}
