<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SyncsToOnline;

class Client extends Model
{
    use SyncsToOnline;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'code', 'adresse', 'email', 'phone', 'country', 'city','tax_number'

    ];

    protected $casts = [
        'code' => 'integer',
    ];
}
