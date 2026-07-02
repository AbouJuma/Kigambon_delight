<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SyncsToOnline;

class Category extends Model
{
    use SyncsToOnline;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'code', 'name',
    ];

}
