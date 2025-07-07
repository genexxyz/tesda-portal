<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentCenter extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];

    public function assessors(): BelongsToMany
    {
        return $this->belongsToMany(Assessor::class, 'assessor_centers');
    }
}