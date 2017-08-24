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

class CategoriesController extends Controller
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

}
