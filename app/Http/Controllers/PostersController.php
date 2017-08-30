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
use File;
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
        $get_all_poster = DB::table('posters')
            ->where('status', '=', 1)
            ->get();
        if($get_all_poster == true){
            return response()->json($get_all_poster);
        }else{
            echo "You data don't have any record!";
        }
    }

    /**
     * This method is use to register of seller
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        ///set all field are required
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:posters',
        ]);

        //if validation = false show message error
        if($validator->fails()){
            return response()->json(array('status' => 'fail','errors'=>$validator->errors()));
        }else{

            $seller = new Posters();
            $seller->username = $request->input('username');
            $seller->email = $request->input('email');
            $seller->password = sha1($request->input('password')); //encrypt password
            $seller->status = 1;
            $seller->save();
            //response message
            return response()->json(array('status'=> 'success','users' => $seller));
        }
    }


    /**
     * This method is used for seller login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        //dd($request);
        $validator = Validator::make($request->all(), [//check validation required
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(array('status' => 'fail','errors'=>$validator->errors()));//return message error
        }else{
            $email = $request->email;
            $password = $request->password;
            $login = DB::select('
                select * from posters where posters.email = "'.$email.'" and posters.password = "'.sha1($password).'"
            ');
            if(count($login) > 0){//check is true or not
                return response()->json(array(
                    'status'=>"success",
                    'sms'=> 'Login successfully!!',
                    'data'=>$login
                ));
            }else{
                return response()->json(array(
                    'status'=> "fail",
                    'sms'=> 'Login not success, Please try again!!'
                ));
            }
        }
    }

    /**
     * This method is used to change profile image of poster
     * @author sreymom
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMail(Request $request)
    {
        $email = $request->input('email');
        $check_email = DB::table('posters')->select('*')
            ->where('email', $email)->get();
        if($check_email>0){
            $digits = 4;
            $code =  rand(pow(10, $digits-1), pow(10, $digits)-1);//would produce a secret code of 4 chars.
            $update_users_info = DB::table('posters')
                ->where('email', $email)
                ->update(['confirmcode' => $code]);
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
        $pass = sha1($request->input('password'));
        $verifyCode = $request->input('confirmcode');
        $check_code = DB::table('posters')->select('*')
            ->where('email','=',$email)
            ->Where('confirmcode','=', $verifyCode)
            ->get();
        if($check_code){
            $reset_pass = DB::table('posters')
                ->where('email', $email)
                ->update([ 'confirmcode' => '','password' => $pass ]);
            if($reset_pass){
                return response()->json(array('status' => 'success', 'Update successfully' => $reset_pass,));
            }else{
                return response()->json(array('status' => 'fail', 'sms'=> 'Update failed '));
            }
        }else{
            return response()->json(array('status' => 'fail', 'sms'=> 'Update failed of Else'));
        }
    }

    /**
     * This method is used to display user info in their profile
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sellerProfile($id)
    {
        $userData = Posters::find($id);
        if($userData){
            return response()->json(array('status' => 'success', 'posterProfile' => $userData));
        }else{
            return response()->json(array(
                'status' => 'fail','message' =>'No record', ),200);
        }
    }

    public function viewPosterPost($id)
    {
        $poster = DB::table('posters')
            ->join("posts", "posters.id", "=", "posts.posters_id")
            ->select('*')
            ->where('posters.id',$id)->get();
        $poster = DB::select('
            select 
            (select count(likes.users_id) from ps_app_db.likes where likes.posts_id = posts.id) as numlike,
            (select count(comments.users_id) from ps_app_db.comments where comments.posts_id = posts.id) as numcmt,
            (select count(favorites.users_id) from ps_app_db.favorites where favorites.posts_id = posts.id) as numfavorite,
            username,image,
            posts.*
            from ps_app_db.posters
            inner join ps_app_db.posts
            on posters.id = posts.posters_id
            where posters.id = "'.$id.'"  and posts.pos_status = 1
            
        ');
        if($poster){
            return response()->json(array('status' => 'success', 'posterpost' => $poster,));
        }else{
            return response(array('status' => 'failed','message' =>'No record',),200);

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
    public function updatePosterInfo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'regex:/^[\pL\s\-]+$/u',
            'email'=> 'email|unique:posters,email,$id',
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);//return message error
        }else{

            $update_poster_info = DB::table('posters')
                ->where('posters.id', $id)
                ->update([
                    'username' => $request ->input('username'),
                    'email' => $request ->input('email')
                ]);
            if($update_poster_info){
                return response()->json(array('status' => 'success', 'Update successfully' => $update_poster_info,));
            }else{
                return response(array('status' => 'failed','message' =>'Update failed!',),200);
            }

        }
    }

    /**
     * update seller info
     * @author sreymom
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserInfo(Request $request,$id){

        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[\pL\s\-]+$/u',
            'email' => "required|email|unique:users,email,$id",
        ]);

        // Validator is true
        if ($validator->fails()) {
            return response()->json(array(
                'status' => "fail",
                'error' => $validator->errors()
            ));
        } else {

            $data = Posters::find($id);
            if ($data) {
                $data->username = $request->input('username');
                $data->email = $request->input('email');
                $data->save();

                $userNewData = \DB::table('posters')->select('*')->where('id','=',$id)->get();

                return response()->json(array('status' => 'success', 'sms' => 'Edit successfully', 'user' => $userNewData));
            } else {
                return response()->json(array('status' => 'fail', 'sms' => 'Invalid id'), 404);
            }
        }

    }


    /**
     * This method is used to change cover image of poster
     * @author sreymom
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function changeCover(Request $request,$id){
        $seller = Posters::find($id);
        $oldCover = $seller->covers;
        if($request->file('covers')) {
            if($oldCover != "dj.png"){
                File::delete('images/posters/'.$oldCover);
            }
            $image = $request->file('covers');
            $fileName = $image->getClientOriginalName();
            $image->move('images/posters/', $fileName);
            $seller->covers = $fileName;
            $seller->save();

            return response()->json(array('status' => 'success'));
        }else{
            return response()->json(array('status' => 'fail'));
        }
    }

    /**
     * This method is used to change profile image of poster
     * @author sreymom
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function profile(Request $request,$id){

        $buyer = Posters::find($id);
        //$userID = new Users();
        $oldProfile = $buyer->image;
        if($request->file('image')) {
            if($oldProfile != "dj.png"){
                File::delete('images/posters/'.$oldProfile);
            }

            $image = $request->file('image');
            $fileName = $image->getClientOriginalName();
            $image->move('images/posters/', $fileName);
            $buyer->image = $fileName;
            $buyer->save();

            return response()->json(array('status' => 'success'));
        }else{
            return response()->json(array('status' => 'fail'));
        }

    }


    /**
     * Update the specified password poster.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request,$id){
//        $dd = "1321654987";
//        dd($request->input("password"));
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);//return message error
        }else{
            $userID = Posters::find($id);
            $userID->password = sha1($request->input('password'));
            $userID->save();
            if($userID){
                return response(array( 'status' => 'success', 'message' =>'Change Password Successfully',
                ),200);
            }else{
                return response(array( 'status' => 'failed', 'message' =>'Change Password failed',
                ),200);
            }
        }
    }

}
