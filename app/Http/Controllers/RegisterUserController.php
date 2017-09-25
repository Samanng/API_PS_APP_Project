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
use File;
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
     * Store a newly created resource in storage.
     * @author samnag
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
        return response()->json(array('status' => 'fail','errors'=>$validator->errors()));
    }else{
        $user = new Users();
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = sha1($request->input('password')); //encrypt password
        //$user->image = $filename;
        $user->status = 1;
        $user->save();
        //response message
        if($user->save()){
            return response()->json(array('status'=> 'success','users' => $user));
        }else{
            //response message
            return response()->json(array('status'=> 'failed'));
        }
    }
}


    /**
     * This method is used for buyer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
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
                select * from users where users.email = "'.$email.'" and users.password = "'.sha1($password).'"
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
     * This email is used to display user info in their profile
     * @author Chhin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile($id)
    {

        $users = DB::table('users')
            ->select('*')
            ->where('users.id',$id)->get();
        if($users){
            return response()->json(array('status' => 'success', 'posterProfile' => $users));
        }else{
            return response()->json(array(
                'status' => 'fail','message' =>'No record', ),200);
        }
    }

    /**
     * This method is used to view user's favorite
     * @author Chhin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewUserFavorite($id)
    {
       $user = \DB::table('favorites')
           ->select('favorites.id','favorites.users_id','favorites.posts_id','posts.pos_image','posts.pos_title','posts.posters_id')
           ->join("posts", "posts.id", "=", "favorites.posts_id")
           ->where('favorites.users_id','=',$id)
           ->orderBy('favorites.id','desc')
           ->get();
        if($user){
            return response()->json(array('status' => 'success', 'users' => $user));
        }else{
            return response()->json(array(
                'status' => 'fail','message' =>'No record', ),200);
        }
    }

    /**
     * Update user info
     * @author chhin
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateUserInfo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'regex:/^[\pL\s\-]+$/u',
            'email'=> "unique:users,email,$id",
        ]);
        if($validator->fails()){
            return response()->json(array('status' => "existingEmail",'validate' =>$validator->errors()));//return message error

        }else{

            $update_users_info = DB::table('users')
                ->where('users.id', $id)
                ->update([
                    'username' => $request ->input('username'),
                    'email' => $request ->input('email'),
                    'phone' => $request ->input('phone'),
                    'address' => $request->input('address')
                ]);

                return response()->json(array('status' => 'success', 'Update successfully' => $update_users_info,));

        }
    }

    /**
     * This method is used to change cover of register user
     * @author sreymom
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function changeCover(Request $request,$id){
        $userID = Users::find($id);
        $oldCover = $userID->covers;
        if($request->file('covers')) {
//            if($oldCover != "dj.png"){
//                File::delete('images/users/'.$userID->covers);
//            }

            $image = $request->file('covers');
            $fileName = $image->getClientOriginalName();
            $image->move('images/users/', $fileName);
            $userID->covers = $fileName;
            $userID->save();

            return response()->json(array('status' => 'success'));
        }else{
            return response()->json(array('status' => 'fail'));
        }

    }

    /**
     * Update user profile
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request,$id){

        $userID = Users::find($id);
        //$userID = new Users();
        $oldProfile = $userID->image;
        if($request->file('image')) {

//            if($oldProfile != "dj.png"){
//                File::delete('images/users/'.$oldProfile);
//            }

            $image = $request->file('image');
            $fileName = $image->getClientOriginalName();
            $image->move('images/users/', $fileName);
            $userID->image = $fileName;
            $userID->save();

            return response()->json(array('status' => 'success'));
        }else{
            return response()->json(array('status' => 'fail'));
        }
    }



    /**
     * This method is used to sendMail
     * @author chhin
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMail(Request $request)
    {
        $email = $request->input('email');
        $check_email = DB::table('users')->select('*')
            ->where('email', $email)->get();
        if($check_email>0){
            $digits = 4;
            $code =  rand(pow(10, $digits-1), pow(10, $digits)-1);//would produce a secret code of 4 chars.
             $update_users_info = DB::table('users')
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
            return response()->json(array('status' => 'fail'));
        }
    }

    /**
     * Forgot password method
     * @author chhin
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetForgotPass(Request $request)
    {
        $email = $request->input('email');
        $pass = sha1($request->input('password'));
        $verifyCode = $request->input('confirmcode');
        $check_code = DB::table('users')->select('*')
            ->where('email','=',$email)
            ->Where('confirmcode','=', $verifyCode)
            ->get();
        if($check_code){
            $reset_pass = DB::table('users')
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
     * confirm email
     * @author sreymom
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public  function confirmUserEmail(Request $request,$id){

        $result = \DB::table('users')->select('*')->where([
            ['email', '=', $request->input('email')],
            ['id', '=',$id],
        ])->get();
        // To check if login is success or fail

        if (count($result) > 0) {
            return response()->json(array(
                'status' => 'success',
                'data' => $result
            ));
        } else {
            return response()->json(array('status' => 'fail'));
        }

    }

    /**
     * Update the specified password poster.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status' => 'fail','errors'=>$validator->errors()]);//return message error
        }else{

            $currentPassword = $request->input("currentpass");
            $newPassword = $request->input("password");

            $verify = DB::select('
                select * from users where users.id = "'.$id.'" and users.password = "'.sha1($currentPassword).'"
            ');

            if(count($verify) > 0){
                $userID = Users::find($id);
                $userID->password = sha1($newPassword);
                $userID->save();
                return response(array( 'status' => 'success', 'message' =>'Change Password Successfully',
                ),200);
            }else{
                return response(array( 'status' => 'fail', 'message' =>'Change Password failed'));
            }
        }
    }

}
