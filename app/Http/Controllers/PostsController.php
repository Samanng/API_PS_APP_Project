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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_post(Request $request)
    {
        //for validate
        $validator = Validator::make($request->all(), [

            'pos_title' => 'required',
            'pos_description' => 'required',
            'pos_telephone'=>'required',
        ]);
        // if validation it not yet fill
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);//return message error
        }else{
            //for upload image
            $pro_pic = $request->file('post_image');// name of input file
            $imgPath = 'images/posts';    //create varriable for path
            $fileName = $pro_pic->getClientOriginalName();//originalname that it was uploaded with
            $pro_pic->move($imgPath,$fileName);
            $post = DB::table('posts')
            ->insert(
                    ['posts.posters_id' => $request->input('posters_id'),
                    'posts.categories_id' => $request->input('categories_id'),
                    'posts.pos_title' => $request->input('pos_title'),
                    'posts.pos_description' => $request->input('pos_description'),
                    'posts.pos_telephone' => $request->input('pos_telephone'),
                    'posts.pos_image' => $fileName,
                    'posts.price' => $request->input('price'),
                    'posts.discount' => $request->input('discount')]

                );
            if($post == true){
                return response(array(
                    'status' => 'success',
                    'message' =>'post create successfully',
                ),200);
            }else{
                return response(array(
                    'status' => 'failed',
                    'message' =>'post create failed',
                ),400);
            }

        }
    }
    public function postDetail($id)
    {
        $post = Posts::find($id);
        if($post){
            return response()->json(array('status' => 'success', 'posts' => $post));
        }else{
            return response(array(
                'status' => 'fail',
                'message' =>'No record',
            ),200);
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
    public function deletePost($id)
    {
        $update_status = DB::table('posts')
            ->where([
                ['posts.id', '=', $id],
                ['posts.pos_status', '=', 1]
            ])
            ->update(['posts.pos_status' => 0]);
        if($update_status){
            return response(array(
                'message' =>'post deleted successfully',
            ),200);
        }else{
            return response(array(
                'message' =>'post delete failed',
            ),200);
        }

    }


//    -----------------------------------------------------mom------------------------------------------------------
    /**
     * This method is used to search post
     * @param $param
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($param){

        $result = \DB::table('posts')
            ->select('posters.username','posts.*','posters.image' )
            ->join('posters', 'posts.posters_id', '=', 'posters.id')
            ->where('posts.pos_title','like',$param.'%')
            ->orWhere('posters.username', 'like',$param.'%')
            ->where('posts.pos_status','=',1)
            ->get();

        if($result){
            return response()->json(array('status' => 'success', 'posts' => $result));
        }else{
            return response()->json(array('status' => 'false'));
        }

    }

    /**
     * This method is used to list categories
     * @return \Illuminate\Http\JsonResponse
     */
    public function categoriesList(){
        $categories = \DB::table("categories")->select("*")->get();
        if($categories){
            return response()->json(array('status' => "success",'categories' => $categories));
        }else{
            return response()->json(array('status' => 'fail'));
        }
    }

    /**
     * This method is use to list all product in each category
     * @param $catId
     * @return \Illuminate\Http\JsonResponse
     */
    public function productEachCat($catId){
        $category = \DB::table("posts")->select("*")->where("posts.categories_id","=",$catId)->get();
        if($category){
            return response()->json(array('status' => 'success','category' => $category));
        }else{  return response()->json(array('status' => 'fail'));}
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
