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

        //  $photo = $request->file('image');
        // $destinationPath = 'images/users/'; // path to save to, has to exist and be writeable
        // $filename = $photo->getClientOriginalName(); // original name that it was uploaded with
        // $photo->move($destinationPath,$filename); // moving the file to specified dir with the original name

        $user = new Users();
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = sha1($request->input('password')); //encrypt password
        //$user->image = $filename;
        $user->status = 1;
        $user->save();
        //response message
        return response()->json(array('status'=> 'success','users' => $user));
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
        $userData = Users::find($id);
        if($userData){
            return response()->json(array('status' => 'success', 'posterProfile' => $userData));
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
           ->select('favorites.users_id as userId','favorites.posts_id as postId','posts.pos_image','posts.pos_title')
           ->join("posts", "posts.id", "=", "favorites.posts_id")
           ->where('favorites.users_id','=',$id)
           ->get();
        if($user){
            return response()->json(array('status' => 'success', 'users' => $user));
        }else{
            return response()->json(array(
                'status' => 'fail','message' =>'No record', ),200);
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
            if($oldCover != "dj.png"){
                File::delete('images/users/'.$userID->covers);
            }

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
     * This method is used to change profile image of poster
     * @author sreymom
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function profile(Request $request,$id){

        $userID = Users::find($id);
        //$userID = new Users();
        $oldProfile = $userID->image;
        if($request->file('image')) {
            if($oldProfile != "dj.png"){
                File::delete('images/users/'.$oldProfile);
            }

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

}
