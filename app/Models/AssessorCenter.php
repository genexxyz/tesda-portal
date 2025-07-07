<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessorCenter extends Model
{
    protected $fillable = [
        'assessment_center_id',
        'assessor_id',
    ];

    public function assessors()
    {
        return $this->belongsToMany(Assessor::class, 'assessment_center_assessor', 'assessment_center_id', 'assessor_id');
    }

    
}
