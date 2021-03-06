<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Posters;
use App\Posts;
use Validator;
use App\Comments;
use App\Likes;
use Rule;
use DB;
use App\Http\Requests;
use App\Users;
use App\file;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page,$userLogin)
    {
        $offset = ( $page - 1)* 5;


        if($userLogin != 0){

            $get_all_post = DB::select("
            
              select
               L.count_like AS numlike,
               L.users_id AS user_like_id,
                C.count_comment AS numcmt,
               F.count_favorite AS numfavorite,
               F.users_id AS user_fav_id,
                username,image,posters.email as posterEmail,
                posts.*
                
        from ps_app_db.posts 
        
          inner join ps_app_db.posters on posters.id = posts.posters_id
           left join 
          (SELECT users_id,posts_id,count(likes.users_id) as count_like from ps_app_db.likes  where users_id = $userLogin and like_status = 1  group by posts_id) AS L
          on L.posts_id = posts.id AND  L.posts_id = posts.id 
           left join 
                (select posts_id, count(comments.users_id) as count_comment from ps_app_db.comments group by posts_id) AS C
          on C.posts_id = posts.id AND  C.posts_id = posts.id 
          left join (select users_id,posts_id, count(favorites.users_id) as count_favorite from ps_app_db.favorites where users_id = $userLogin group by posts_id) as F
           on F.posts_id = posts.id AND  F.posts_id = posts.id 
                where posts.pos_status = 1
                order by posts.id DESC 
              
                limit 10 offset $offset
      		
                
                
            ");
        }else{

            $get_all_post = DB::select("
            
              select
               L.count_like AS numlike,
               L.users_id AS user_like_id,
                C.count_comment AS numcmt,
               F.count_favorite AS numfavorite,
               F.users_id AS user_fav_id,
                username,image,posters.email as posterEmail,
                posts.*
                
        from ps_app_db.posts 
        
          inner join ps_app_db.posters on posters.id = posts.posters_id
           left join 
          (SELECT users_id,posts_id,count(likes.users_id) as count_like from ps_app_db.likes where like_status = 1 group by posts_id) AS L
          on L.posts_id = posts.id AND  L.posts_id = posts.id 
           left join 
                (select posts_id, count(comments.users_id) as count_comment from ps_app_db.comments group by posts_id) AS C
          on C.posts_id = posts.id AND  C.posts_id = posts.id 
          left join (select users_id,posts_id, count(favorites.users_id) as count_favorite from ps_app_db.favorites group by posts_id) as F
           on F.posts_id = posts.id AND  F.posts_id = posts.id 
                where posts.pos_status = 1
                order by posts.id DESC 
              
                limit 10 offset $offset
      		
                
                
            ");


        }


        if($get_all_post == true){
            return response()->json(array('status' => 'success','data' => $get_all_post));
        }else{
            return response()->json(array('status' => 'fail'));
        }
       

    }

	   /**
     * This method is used to view post by each categories
     * @author never care
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
	public function view_each_category($id){
		$get_all_post = DB::select('
          
          select
           L.count_like AS numlike,
           L.users_id AS user_like_id,
           	C.count_comment AS numcmt,
           F.count_favorite AS numfavorite,
           F.users_id AS user_fav_id,
            username,image,
            posts.*
            
	from ps_app_db.posts 
	
      inner join ps_app_db.posters on posters.id = posts.posters_id
       left join 
      (SELECT users_id,posts_id,count(likes.users_id) as count_like from ps_app_db.likes where like_status = 1 group by posts_id) AS L
      on L.posts_id = posts.id AND  L.posts_id = posts.id 
       left join 
            (select posts_id, count(comments.users_id) as count_comment from ps_app_db.comments group by posts_id) AS C
      on C.posts_id = posts.id AND  C.posts_id = posts.id 
      left join (select users_id,posts_id, count(favorites.users_id) as count_favorite from ps_app_db.favorites group by posts_id) as F
       on F.posts_id = posts.id AND  F.posts_id = posts.id 
         
            where posts.categories_id = "'.$id.'"
            order by posts.id DESC 
            
            
        ');
        if($get_all_post == true){
            return response()->json(array('status' => 'success','data' => $get_all_post));
        }else{
            return response()->json(array('status' => 'fail'));
        }
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
                        'posts.price' => $request->input('price'),
                        'posts.discount' => $request->input('discount'),
                        'posts.pos_image' => $fileName,

                ]);
            if($post){
                return response()->json(array( 'status' => 'success', 'data' => $post ));
            }else{
                return response()->json(array(
                    'status' => 'fail',
                    'message' =>'post create failed',
                ),400);
            }

        }
    }

    public function postOldDataUpdate($id)
    {

        $posts = DB::table('posts')
            ->select('*')
            ->where('posts.id',$id)->get();
        if($posts){
            return response()->json(array('status' => 'success', 'postInfo' => $posts));
        }else{
            return response()->json(array(
                'status' => 'fail','message' =>'No record', ),200);
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
        $post =  DB::select("
       
          select
           L.count_like AS numlike,
           L.users_id AS user_like_id,
           	C.count_comment AS numcmt,
           F.count_favorite AS numfavorite,
           F.users_id AS user_fav_id,
            username,image,posters.email as posterEmail,
            posts.*
            
	from ps_app_db.posts 
	
      inner join ps_app_db.posters on posters.id = posts.posters_id
       left join 
      (SELECT users_id,posts_id,count(likes.users_id) as count_like from ps_app_db.likes where like_status = 1 group by posts_id) AS L
      on L.posts_id = posts.id AND  L.posts_id = posts.id 
       left join 
            (select posts_id, count(comments.users_id) as count_comment from ps_app_db.comments group by posts_id) AS C
      on C.posts_id = posts.id AND  C.posts_id = posts.id 
      left join (select users_id,posts_id, count(favorites.users_id) as count_favorite from ps_app_db.favorites group by posts_id) as F
       on F.posts_id = posts.id AND  F.posts_id = posts.id 
           where posts.id = $id
           
       ");

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateInfoPost(Request $request, $id){

        $users = DB::table('posts')
            ->join("posters", "posts.posters_id", "=", "posters.id")
            ->join("categories","posts.categories_id", "=", "categories.id")
            ->where('posts.id','=',$id)
            ->update([
                'posts.pos_title' => $request->input('pos_title'),
                'posts.categories_id' => $request->input('categories_id'),
                'posts.pos_description' => $request->input('pos_description'),
                'posts.pos_telephone' => $request->input('pos_telephone'),
                'posts.pos_address' => $request->input('pos_address'),
                'posts.price' => $request->input('price'),
                'posts.discount' => $request->input('discount'),
            ]);
        if($users){
            return response()->json(array(
                'status' => 'success',
                'message' =>'Update post successfully',
            ),200);
        }else{
            return response()->json(array(
                'status' => 'fail',
                'message' =>'Update post failed no record found',
            ),200);
        }
    }


    public function uploadImage(Request $request){
        dd($request->all());
        $photo = $request->file('image');
        dd($photo);
        $destinationPath = 'images/postUpdate/'; // path to save to, has to exist and be writeable
        $filename = $photo->getClientOriginalName(); // original name that it was uploaded with
        $photo->move($destinationPath,$filename); // moving the file to specified dir with the original name
        $user = new Posts();
        $user->image = $filename;
        if($user == true){
            return response()->json($user);
        }else{
            echo "You data don't have any record!";
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
           L.count_like AS numlike,
           L.users_id AS user_like_id,
           	C.count_comment AS numcmt,
           F.count_favorite AS numfavorite,
           F.users_id AS user_fav_id,
            username,image,
            posts.*
            
	from ps_app_db.posts 
	
      inner join ps_app_db.posters on posters.id = posts.posters_id
       left join 
      (SELECT users_id,posts_id,count(likes.users_id) as count_like from ps_app_db.likes where like_status = 1 group by posts_id) AS L
      on L.posts_id = posts.id AND  L.posts_id = posts.id 
       left join 
            (select posts_id, count(comments.users_id) as count_comment from ps_app_db.comments group by posts_id) AS C
      on C.posts_id = posts.id AND  C.posts_id = posts.id 
      left join (select users_id,posts_id, count(favorites.users_id) as count_favorite from ps_app_db.favorites group by posts_id) as F
       on F.posts_id = posts.id AND  F.posts_id = posts.id 
      
         
          
            where posts.pos_title like "%'.$param.'%" and posts.pos_status = 1
               order by posts.id DESC 
            
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
