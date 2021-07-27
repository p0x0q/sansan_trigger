<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignUpController extends Controller
{
    /**
     * name store
     * desc
     *
     * @queryParam title string Example: イベントタイトル
     */
    public function store(Request $request)
    {
        $valid_dict = [
            'user_id' => ['required', 'min:8', 'max:20', 'unique:user,user_id'],
            'password' => ['required', 'min:8', 'max:20'],
        ];

        if (!$request->has('user_id') || !$request->has('password')) {
            return response([
                "message" => "Account creation failed",
                "cause" => "required user_id and password",
            ], 400);
        }

        if (User::where('user_id', $request->user)) {
            $validator = Validator::make($request->all(), $valid_dict);
        }
        if ($validator->fails()) {
            return response([
                "message" => "Account creation failed",
                "cause" => "error",
            ], 400);
        }
        $request->validate($valid_dict);
        $data = $request->only(array_keys($valid_dict));
        $data['nickname'] = $request->user_id;
        User::insert($data);
        return [
            "message" => "Account successfully created",
            "user" => [
                "user_id" => $request->user_id,
                "nickname" => $request->user_id,
            ],
        ];
    }
}
