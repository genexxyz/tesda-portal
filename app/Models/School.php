<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = "school_info";

    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'email',
        'website',
        'logo',
        'tag_line',
        'header_img',
        'created_at',
        'updated_at'
    ];
}
