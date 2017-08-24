<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $fillable = [
        'pos_title', 'pos_image', 'pos_description','pos_telephone','pos_address','price','discount',
    ];
}
