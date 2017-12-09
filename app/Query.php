<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $fillable=['id', 'searchEng', 'taskId', 'location', 'website', 'keywords'];
}
