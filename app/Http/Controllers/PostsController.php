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

class PostsController extends Controller
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
     * This method is used to post product
     * @author chhin
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create_post(Request $request)
    {
        //for validate
        $validator = Validator::make($request->all(), [
            'pos_title' => 'required',
            'pos_description' => 'required',
            'pos_image'=>'required',
        ]);
        // if validation it not yet fill
        if ($validator->fails()) {
            return response()->json(array('status' => 'fail','errors'=>$validator->errors()));//return message error
        }else{
            // file upload
            $image = $request->file('pos_image');
            $fileName = $image->getClientOriginalName();
            $image->move('images/posts/', $fileName);

            $post = DB::table('posts')
            ->insert([
                        'posts.posters_id' => $request->input('posters_id'),
                        'posts.categories_id' => $request->input('categories_id'),
                        'posts.pos_title' => $request->input('pos_title'),
                        'posts.pos_description' => $request->input('pos_description'),
                        'posts.pos_telephone' => $request->input('pos_telephone'),
                        'posts.pos_address' => $request->input('pos_address'),
                        'posts.pos_image' => $fileName,
                        'posts.price' => $request->input('price'),
                        'posts.discount' => $request->input('discount')
                ]);
            if($post == true){
                return response()->json(array(
                    'status' => 'success',
                    'message' =>'post create successfully',
                ),200);
            }else{
                return response()->json(array(
                    'status' => 'fail',
                    'message' =>'post create failed',
                ),400);
            }

        }
    }

    /**
     * This method is used to delete post
     * @author chhin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDetail($id)
    {
        $post =  DB::select('
            select 
            (select count(likes.users_id) from ps_app_db.likes where likes.posts_id = posts.id) as numlike,
            (select count(comments.users_id) from ps_app_db.comments where comments.posts_id = posts.id) as numcmt,
            (select count(favorites.users_id) from ps_app_db.favorites where favorites.posts_id = posts.id) as numfavorite,
            username,image,
            posts.*
            from ps_app_db.posters
            inner join ps_app_db.posts
            on posters.id = posts.posters_id
            where posts.id = "'.$id.'" 
            
        ');

        if($post){
            return response()->json(array('status' => 'success', 'posts' => $post));
        }else{
            return response()->json(array(
                'status' => 'fail',
                'message' =>'No record',
            ),200);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePost($id)
    {
        $update_status = DB::table('posts')
            ->where('posts.id', '=', $id)
            ->update(['posts.pos_status' => 0]);
        if($update_status){
            return response()->json(array(
                'status' => 'success',
                'message' =>'post deleted successfully',
            ),200);
        }else{
            return response()->json(array(
                'status' => 'fail',
                'message' =>'post delete failed',
            ),200);
        }

    }


    /**
     * This method is used to search post
     * @author Sreymom
     * @param $param
     * @return \Illuminate\Http\JsonResponse
     */

    public function search($param){

        $result = \DB::select('
        
            select 
            (select count(likes.users_id) from ps_app_db.likes where likes.posts_id = posts.id) as numlike,
            (select count(comments.users_id) from ps_app_db.comments where comments.posts_id = posts.id) as numcmt,
            (select count(favorites.users_id) from ps_app_db.favorites where favorites.posts_id = posts.id) as numfavorite,
            username,image,
            posts.*
            from ps_app_db.posters
            inner join ps_app_db.posts
            on posters.id = posts.posters_id
            where (posters.username like "'.$param.'%" or posts.pos_title like "'.$param.'%") and posts.pos_status = 1
            
        ');

        if($result){
            return response()->json(array('status' => 'success', 'posts' => $result));
        }else{
            return response()->json(array('status' => 'fail'));
        }


    }

    //    public function search($param){
//
//        $result = \DB::table('posts')
//            ->select('posters.username','posts.*','posters.image' )
//            ->join('posters', 'posts.posters_id', '=', 'posters.id')
//            ->where('posts.pos_title','like',$param.'%')
//            ->orWhere('posters.username', 'like',$param.'%')
//            ->where('posts.pos_status','=',1)
//            ->get();
//
//        if($result){
//            return response()->json(array('status' => 'success', 'posts' => $result));
//        }else{
//            return response()->json(array('status' => 'false'));
//        }
//
//    }







}
