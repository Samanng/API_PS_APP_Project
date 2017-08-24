<?php

namespace App\Http\Controllers;

use App\Comments;
use App\Likes;
use Illuminate\Http\Request;

use Illuminate\Foundation\Validation;
use Rule;
use Validator;

use DB;

use App\Http\Requests;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * This method is used to insert comment of post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentPost(Request $request){
        $validator = Validator::make($request->all(),[
            'message' => 'required',
        ]);

        if ($validator->passes()){
            $cmt = new Comments();

            $cmt->users_id = $request->input('users_id');
            $cmt->posts_id = $request->input('posts_id');
            $cmt->message = $request->input('message');
            $cmt->save();
            return response()->json(array('status' => 'success'));

        }else{return response()->json(array('status' => 'fail','errors'=>$validator->errors()));}
    }
}
