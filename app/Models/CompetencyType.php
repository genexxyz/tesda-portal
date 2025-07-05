<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetencyType extends Model
{
    
    protected $fillable = [
        'code',
        'name',
        'level',
        'description',
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
