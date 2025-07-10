<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentSchedule extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assessment_id',
        'assessment_center_id',
        'assessor_id',
        'assessment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assessment_date' => 'date',
    ];
    
    /**
     * Get the assessment that owns the schedule.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the assessment center for this schedule.
     */
    public function assessmentCenter(): BelongsTo
    {
        return $this->belongsTo(AssessmentCenter::class);
    }

    /**
     * Get the assessor for this schedule.
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class);
    }
    
    /**
     * Get the results for this assessment schedule.
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
