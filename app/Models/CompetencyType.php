<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetencyType extends Model
{
    
    protected $fillable = [
        'name', // 1. Competent, 2. Not Yet Competent, 3. Absent, 4. Dropped
        'description',
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
