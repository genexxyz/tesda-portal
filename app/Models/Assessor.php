<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Assessor extends Model
{
    protected $fillable = [
        'name',
    ];

    public function assessmentCenters(): BelongsToMany
    {
        return $this->belongsToMany(AssessmentCenter::class, 'assessor_centers');
    }

    
}