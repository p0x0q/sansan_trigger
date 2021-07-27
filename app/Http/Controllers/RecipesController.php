<?php

namespace App\Http\Controllers;

use App\Models\recipes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RecipesController extends Controller
{
    /**
     * name
     * desc
     */
    public function index(){
        return [
            'recipes' => recipes::all()
        ];
    }
    /**
     * name show
     * desc
     *
     * @urlParam name required Example=>admin
     */
    public function show($id)
    {
        if((int)$id == 0){
            $id = 1;
        }
        return [
            'message' => 'Recipe details by id',
            'recipe' => [recipes::find($id)]
        ];
    }
    /**
     * name store
     * desc
     *
     * @queryParam title string Example=>イベントタイトル
     */
    public function store(Request $request)
    {
        $valid_dict = [
            'title' => [],
            'making_time' => [],
            'serves' => [],
            'ingredients' => [],
            'cost' => [],
        ];

        if(!$request->has('title')||!$request->has('making_time')||!$request->has('serves')||!$request->has('ingredients')||!$request->has('cost')){
            return [
                "message"=> "Recipe creation failed!",
                "required"=> "title, making_time, serves, ingredients, cost"
            ];
        }

        $request->validate($valid_dict);
        $request = $request->only(array_keys($valid_dict));

        $id = DB::transaction(function () use ($request) {
            recipes::insert($request);
            $file_id = DB::getPdo()->lastInsertId(); // file_id
            return $file_id;
        });
        $data = recipes::find($id);

        return [
            "message" => "Recipe successfully created!",
            "recipe"=> [
            //   [
            //     "id"=>3,
            //     "title"=> $request->title,
            //     "making_time"=> $request->making_time,
            //     "serves"=>$request->serves,
            //     "ingredients"=>$request->ingredients,
            //     "cost"=>$request->cost,
            //     "created_at"=>time(),
            //     "updated_at"=>time(),
            //   ],
            $data
            ]
            ];
    }
    /**
     * name update
     * desc
     *
     * @urlParam name required Example=>admin
     * @queryParam name
     * @queryParam description
     */
    public function update(Request $request, $id)
    {
        $valid_dict = [
            'title' => [],
            'making_time' => [],
            'serves' => [],
            'ingredients' => [],
            'cost' => [],
        ];
        $request->validate($valid_dict);
        $request = $request->only(array_keys($valid_dict));
        $r = recipes::where('id',$id);
        $r->update($request);


        $data = recipes::find($id);

        return   [
            "message"=>"Recipe successfully updated!",
            "recipe"=>$data
            ];
    }
    /**
     * name delete
     * desc
     *
     * @urlParam name required Example:
     */
    public function destroy($id)
    {
        $r = recipes::where('id',$id);
        if($r->exists()){
            $r->delete();
            return ["message"=> "Recipe successfully removed!"];
        }else{
            return ["message"=> "No Recipe found"];
        }
    }
}
