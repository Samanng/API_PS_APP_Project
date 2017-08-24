<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Favorites;
use App\Http\Requests;

class FavoritesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        echo "Hello oun";
        $get_all_poster = DB::table('favorites')
            ->join("users", "favorites.users_id", "=", "users.id")
            ->join("posts","favorites.posts_id", "=", "posts.id")
            ->join("posters","posts.posters_id","=","posters.id")
            ->where('posts.pos_status', '=', 1)
            ->select('posters.id','posters.username','posts.pos_title', 'posts.pos_image', 'posts.pos_description', 'posts.pos_telephone','posts.pos_address','posts.price','posts.discount')
            ->get();
        if($get_all_poster == true){
            return response()->json($get_all_poster);
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
        $favorite = new Favorites();
        $favorite->users_id = $request->input('users_id');
        $favorite->posts_id = $request->input('posts_id');
        if($request->input('users_id')==""){
            return response()->json(['status'=> 'error','user ID' => 'Is Empty']);
        }elseif($request->input('posts_id')==""){
            return response()->json(['status'=> 'error','post ID' => 'Is Empty']);
        }elseif($favorite){
            $favorite->save();
            return response()->json([$favorite,'status'=> 'success']);
        }else{
            return response()->json(['status'=> 'error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
