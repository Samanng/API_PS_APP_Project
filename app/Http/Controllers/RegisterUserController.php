<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;
use Rule;
use App\file;
use Illuminate\Support\Facades\Crypt;

class RegisterUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo "string";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
//        //set all field are required
//        $validator = Validator::make($request->all(), [
//            'email'    => 'required|email|unique:users',
//        ]);
//
//        //if validation = false show message error
//        if($validator->fails()){
//            return response()->json(array('status' => 'fail','errors'=>$validator->errors()));
//        }else{
            $photo = $request->file('image');
            dd($photo);
            $destinationPath = 'images/users/'; // path to save to, has to exist and be writeable
            $filename = $photo->getClientOriginalName(); // original name that it was uploaded with
            $photo->move($destinationPath,$filename); // moving the file to specified dir with the original name

            $user = new Users();
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->password = sha1($request->input('password')); //encrypt password
            $user->image = $filename;
            $user->status = 1;
            $user->save();

            //response message
            return response()->json(array('status'=> 'success','users' => $user));
//        }
    }

    public function userProfile($id)
    {
        $users = DB::table('users')
            ->select('users.id','users.username','users.image','users.email','users.password','users.address')
            ->where('users.id',$id)->get();
        if($users){
            return response()->json(array('status' => 'success', 'posterProfile' => $users));
        }else{
            return response()->json(array(
                'status' => 'fail','message' =>'No record', ),200);
        }
    }
    public function viewUserFavorite($id)
    {
       $user = DB::table('users')
           ->join("favorites", "users.id", "=", "favorites.users_id")
           ->join("posts", "posts.id", "=", "favorites.posts_id")
           ->select('posts.id','posts.posters_id','posts.pos_image','posts.pos_title')
           ->where('users.id',$id)
           ->get();
        if($user){
            return response()->json(array('status' => 'success', 'users' => $user,));
        }else{
            return response()->json(array(
                'status' => 'fail','message' =>'No record', ),200);
        }
           }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
