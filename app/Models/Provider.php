<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SyncsToOnline;

class Provider extends Model
{
    use SyncsToOnline;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'code', 'adresse', 'phone', 'country', 'email', 'city','tax_number'
    ];

    protected $casts = [
        'code' => 'integer',
    ];

}
