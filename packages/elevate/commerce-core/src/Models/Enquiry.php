<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
