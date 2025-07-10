<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'exam_type_id',
        'qualification_type_id',
        'campus_id',
        'course_id',
        'academic_year_id',
        'created_by',
        'status',
    ];


    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function qualificationType()
    {
        return $this->belongsTo(QualificationType::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(Academic::class, 'academic_year_id');
    }

    /**
     * Get the schedules for this assessment.
     */
    public function schedules()
    {
        return $this->hasMany(AssessmentSchedule::class);
    }

    /**
     * Get the results associated with the assessment.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }
    
    /**
     * Get the primary schedule for this assessment (backward compatibility)
     */
    public function primarySchedule()
    {
        return $this->schedules()->latest()->first();
    }
    
    /**
     * Get the assessor from the primary schedule (backward compatibility)
     */
    public function assessor()
    {
        return $this->primarySchedule()?->assessor();
    }

    /**
     * Get the assessment center from the primary schedule (backward compatibility)
     */
    public function assessmentCenter()
    {
        return $this->primarySchedule()?->assessmentCenter();
    }
    
    /**
     * Get the assessment date attribute (backward compatibility)
     */
    public function getAssessmentDateAttribute()
    {
        return $this->primarySchedule()?->assessment_date;
    }
    
    /**
     * Get the assessor ID attribute (backward compatibility)
     */
    public function getAssessorIdAttribute()
    {
        return $this->primarySchedule()?->assessor_id;
    }
    
    /**
     * Get the assessment center ID attribute (backward compatibility)
     */
    public function getAssessmentCenterIdAttribute()
    {
        return $this->primarySchedule()?->assessment_center_id;
    }
}
