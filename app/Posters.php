<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posters extends Model
{
    protected $fillable = [
        'username', 'image', 'email','password','confirmcode','phone','address',
    ];
}
