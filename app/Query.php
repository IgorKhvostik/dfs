<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $fillable=['id', 'search-eng', 'location', 'website', 'keywords'];
}
