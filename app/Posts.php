<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'pos_title', 'image', 'pos_description','pos_telephone','pos_address','price','discount',
    ];
}
