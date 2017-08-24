<?php

namespace App\Http\Controllers;

use Validator;
use DB;
use Illuminate\Http\Request;
use App\Posters;
use Illuminate\Foundation\Validation;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Rule;
use App\file;
use App\Posts;
use Illuminate\Support\Facades\Crypt;

class PostersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    cp .env.example .env
//    php artisan key:generate when laravel No supported encrypter found. The cipher and / or key length are invalid

    public function index()
    {
        echo"YES Yes yes!!!!";
    }
    //function for login
    public function login(Request $request){
        $validator = Validator::make($request->all(), [//check validation required
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);//return message error
        }else{
            $email = $request->email;
            $password = $request->password;
            $login = DB::table('posters')->select('*')->where([
                ['email','=',$email],
                ['password','=',sha1($password)]
            ])->get();
            if(count($login) > 0){//check is true or not
                return response()->json(array(
                    'status'=>"success",
                    'message'=> 'Login successfully!!',
                    'data'=>$login
                ));
            }else{
                return response()->json(array(
                    'status'=> "error",
                    'message'=> 'Login not success, Please try again!!'
                ));
            }
        }
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
    public function store(Request $request)
    {
        ///set all field are required
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:posters',
        ]);

        //if validation = false show message error
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            $photo = $request->file('image');
            $destinationPath = 'images/posters/'; // path to save to, has to exist and be writeable
            $filename = $photo->getClientOriginalName(); // original name that it was uploaded with
            $photo->move($destinationPath,$filename); // moving the file to specified dir with the original name

            $poster = new Posters();
            $poster->username = $request->input('username');
            $poster->email = $request->input('email');
            $poster->password = sha1($request->input('password')); //encrypt password
            $poster->image = $filename;
            $poster->phone = $request->input('phone');
            $poster->address = $request->input('address');
            $poster->save();

            //response message
            return response()->json([$poster,'type'=> 'success']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
