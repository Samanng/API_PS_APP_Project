<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        ///set all field are required
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users',
        ]);

        //if validation = false show message error
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            $photo = $request->file('image');
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
            return response()->json([$user,'type'=> 'success']);
        }
    }

    public function userProfile($id)
    {
        $users = DB::table('users')
            ->select('*')
            ->where('users.id',$id)->get();
        if($users){
            return response()->json(array('status' => 'success', 'posterProfile' => $users,));
        }else{
            return response(array(
                'status' => 'failed','message' =>'No record', ),200);
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
            return response()->json(array('status' => 'success', 'viewUserFavorite' => $user,));
        }else{
            return response(array(
                'status' => 'failed','message' =>'No record', ),200);
        }
           }

    public function updateUserInfo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'regex:/^[\pL\s\-]+$/u',
            'email'=> 'email|unique:users,email,$id',
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);//return message error
        }else{

            $update_users_info = DB::table('users')
                ->where('users.id', $id)
                ->update([
                    'username' => $request ->input('username'),
                    'email' => $request ->input('email')
                ]);
            if($update_users_info){
                return response()->json(array('status' => 'success', 'Update successfully' => $update_users_info,));
            }else{
                return response(array('status' => 'failed','message' =>'Update failed!',),200);
            }
        }
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
    public function sendMail(Request $request)
    {
        $email = $request->input('email');
        $check_email = DB::table('users')->select('*')
            ->where('email', $email)->get();
        if($check_email>0){
            $digits = 4;
            $code =  rand(pow(10, $digits-1), pow(10, $digits)-1);//would produce a secret code of 5 chars.
//            dd($code);
//            die;
             $update_users_info = DB::table('users')
                    ->where('email', $email)
                    ->update([ 'confirmcode' => $code]);
             if($update_users_info){
                   return response()->json(array('status' => 'success', 'Update successfully' => $update_users_info,));
             }else{
                   return response(array('status' => 'failed','message' =>'Update failed!',),200);
             }
           /* Mail::send('emails.send', ['title' => $title, 'content' => $content], function ($message)
            {
                $message->from('samnang.chhorm96@gmail.com', 'Samnang');
                $message->to('chhin1chhoeurb@gmail.com');
            });
*/
            return response()->json(array('status' => 'success', 'Update successfully' => $check_email,));
        }else{
            return response()->json(array('status' => 'failed'));
        }
    }
    public function resetForgotPass(Request $request)
    {
        $email = $request->input('email');
        $pass = $request->input('password');
        $verifyCode = $request->input('confirmcode');
        $check_code = DB::table('users')->select('*')
            ->where('email', $email and 'confirmcode',$verifyCode )->get();
        if($check_code>0){
            $reset_pass = DB::table('users')
                ->where('email', $email)
                ->update([ 'confirmcode' => '','password' => "123456" ]);
            if($reset_pass){
                return response()->json(array('status' => 'success', 'Update successfully' => $reset_pass,));
            }else{
                return response()->json(array('status' => 'success', 'Update failed'));
            }
        }
    }
}
