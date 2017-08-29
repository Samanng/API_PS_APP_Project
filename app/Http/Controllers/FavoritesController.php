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
    public function index($userId)
    {
        $get_all_poster = DB::table('favorites')
            ->join("users", "favorites.users_id", "=", "users.id")
            ->join("posts","favorites.posts_id", "=", "posts.id")
            ->join("posters","posts.posters_id","=","posters.id")
            ->where('favorites.users_id', '=', $userId)
            ->select('posters.id as postId','users.id as userId','posters.username','posts.pos_title', 'posts.pos_image', 'posts.pos_description', 'posts.pos_telephone','posts.pos_address','posts.price','posts.discount')
            ->get();
        if($get_all_poster == true){
            return response()->json($get_all_poster);
        }else{
            echo "You data don't have any record!";
        }
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
        $favorite->save();

        if($favorite){
            $favorite->save();
            return response()->json(['status'=> 'success','favorite' =>$favorite]);
        }else{
            return response()->json(['status'=> 'fail']);
        }
    }


}
