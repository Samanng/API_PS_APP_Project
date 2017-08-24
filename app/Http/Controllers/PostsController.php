<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Foundation\Validation;
//use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Posts;
use App\Users;
use App\file;
//use Rule;
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        dd($request);
//        $post = DB::table('posts')
//            ->join("posters", "posts.posters_id", "=", "posters.id")
//            ->join("categories","posts.categories_id", "=", "categories.id");
//        $post->posters_id = $request->input('posters_id');
//        $post->pos_title = $request->input('pos_title');
//        $post->categories_id = $request->input('categories_id');
//        $post->pos_description = $request->input('pos_description');
//        $post->pos_telephone = $request->input('pos_telephone');
//        $post->price = $request->input('price');
//        $post->save();
//        return response(array(
//            'message' =>'post create failed',
//        ),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Posts::find($id);
//        dd($post);
        if($post){
            return response()->json($post);
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
    public function destroy($id)
    {
        //
    }
}
