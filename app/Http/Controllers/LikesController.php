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

class LikesController extends Controller
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
     * @param Request $request
     * @param $userId
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLike(Request $request,$userId,$postId){
        $like = Likes::where(array('users_id' => $userId,"posts_id" => $postId))->first();
        if($like){
            $like->like_status = 0 ;
            $like->save();
            return response()->json(array('status' => 'success'));
        }else{
            $like = new Likes();
            $like->users_id = $userId;
            $like->posts_id = $postId;
            $like->like_status = 1;
            $like->save();
            return response()->json(array('status' => 'success'));
        }
    }
}
