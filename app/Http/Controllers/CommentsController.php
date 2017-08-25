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
     * @author sreymom
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

    public function viewComment($posId){

        $result = \DB::select(  "
        
            select 
            (select count(likes.users_id) from ps_app_db.likes where likes.posts_id = posts.id) as numlike,
            (select count(comments.users_id) from ps_app_db.comments where comments.posts_id = posts.id) as numcmt,
            (select count(favorites.users_id) from ps_app_db.favorites where favorites.posts_id = posts.id) as numfavorite,
            username,image,covers,
            posts.*
            from ps_app_db.posters
            inner join ps_app_db.posts
            on posters.id = posts.posters_id
            where posts.id = $posId 
            
      ");

        if($result){
            return response()->json(array('status' => 'success', 'posts' => $result));
        }else{
            return response()->json(array('status' => 'false'));
        }

    }

    /**
     * this method is used to list all comment for each post
     * @author sreymom
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function listComment($postId){

        $result = \DB::select(  "select users.username,users.image, comments.message,comments.created_at from comments
        inner join posts on comments.posts_id = posts.id
        inner join users on users.id = comments.users_id
        where comments.posts_id = $postId
            
      ");

        if($result){
            return response()->json(array('status' => 'success', 'posts' => $result));
        }else{
            return response()->json(array('status' => 'false'));
        }

    }

}
