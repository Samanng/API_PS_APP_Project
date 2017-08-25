<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Posters;
use App\Posts;
use Validator;
use App\Http\Requests;
use App\Users;
use App\file;
use DB;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $get_all_post = DB::table('posts')
            ->join("posters", "posts.posters_id", "=", "posters.id")
            ->join("categories","posts.categories_id", "=", "categories.id")
            ->select('posts.id','posts.pos_title', 'posts.pos_image', 'posts.pos_description', 'posts.pos_telephone','posts.pos_address','posts.price','posts.discount','posters.username','categories.cat_name')
            ->where('posts.pos_status', '=', 1)
            ->orderBy('posts.id', 'desc')
            ->get();
        if($get_all_post == true){
            return response()->json($get_all_post);
        }else{
            echo "You data don't have any record!";
        }
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
    public function update(Request $request,$id){

        $photo = "21040381_335602163565098_580145203_n.jpg";
//        dd($photo);
        $destinationPath = 'images/posters/'; // path to save to, has to exist and be writeable
        $filename = $photo->getClientOriginalName(); // original name that it was uploaded with
        $photo->move($destinationPath,$filename); // moving the file to specified dir with the original name

        $users = DB::table('posts')
            ->join("posters", "posts.posters_id", "=", "posters.id")
            ->join("categories","posts.categories_id", "=", "categories.id")
            ->where('posts.id','=',$id)
            ->update([
                'posts.pos_title' => "T-shirt",
                'posts.pos_description' => $request->input('pos_description'),
                'posts.pos_telephone' => $request->input('pos_telephone'),
                'posts.pos_address' => $request->input('pos_address'),
                'posts.pos_image' => $filename,
                'posts.price' => $request->input('price'),
                'posts.discount' => $request->input('discount'),
            ]);
        if($users == true){
            return response()->json($users);
        }else{
            echo "You data don't have any record!";
        }
    }
//    public function update(Request $request,$id)
//    {
//
//        echo "hello world!!!";
////        $til =  $request->input('discount');
////        dd($til);
//
////        $photo = $request->file('image');
////        $destinationPath = 'images/posters/'; // path to save to, has to exist and be writeable
////        $filename = $photo->getClientOriginalName(); // original name that it was uploaded with
////        $photo->move($destinationPath,$filename); // moving the file to specified dir with the original name
//
////        $get_all_post = DB::table('posts')
////            ->join("posters", "posts.posters_id", "=", "posters.id")
////            ->join("categories","posts.categories_id", "=", "categories.id")
////            ->where([['posts.id', '=', $id]])
////            ->update([
////                'pos_title' => $request->input('pos_title'),
////                'pos_description' => $request->input('pos_description'),
////                'pos_telephone' => $request->input('pos_telephone'),
////                'discount' => $request->input('discount'),
////                'pos_address' => $request->input('pos_address'),
////                'price' => $request->input('price'),
////                'posts.categories_id' => $request->input('categories_id'),
////                'posts.posters_id' => $request->input('posters_id')
////            ]);
////            return response(array(
////                'status' => 'success',
////                'message' =>'post updated successfully',
////            ),200);
////
//////        }
//    }

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
}
