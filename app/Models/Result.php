<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $fillable = [
        'assessment_schedule_id',
        'student_id',
        'competency_type_id',
        'remarks',
        'created_by'
    ];

    /**
     * Get the assessment schedule associated with this result
     */
    public function assessmentSchedule(): BelongsTo
    {
        return $this->belongsTo(AssessmentSchedule::class);
    }

    /**
     * Get the assessment through the schedule (accessor)
     */
    public function getAssessmentAttribute()
    {
        if ($this->assessmentSchedule && $this->assessmentSchedule->assessment) {
            return $this->assessmentSchedule->assessment;
        }
        return null;
    }

    /**
     * Get the student associated with this result
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the competency type associated with this result
     */
    public function competencyType(): BelongsTo
    {
        return $this->belongsTo(CompetencyType::class);
    }
}
