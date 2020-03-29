<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PinImports extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'category', 'privacy', 'latitude',
        'longitude' ,'address','city','country'
    ];
}
