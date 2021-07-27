<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            'user_id' => ['required', 'min:8', 'max:20'],
            'password' => ['required', 'min:8', 'max:20'],
        ];

        if (!$request->has('user_id') || !$request->has('password')) {
            return response([
                "message" => "Account creation failed",
                "cause" => "required user_id and password",
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
