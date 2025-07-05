<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    protected $fillable = [
        'type',
        'description',
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
