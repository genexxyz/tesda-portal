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
        'assessment_center_id',
        'assessor_id',
        'assessment_date',
        'created_by',
        'status',
    ];

    protected $casts = [
        'assessment_date' => 'date',
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

    public function assessor()
    {
        return $this->belongsTo(Assessor::class);
    }

    /**
     * Get the assessment center associated with the assessment.
     */
    public function assessmentCenter()
    {
        return $this->belongsTo(AssessmentCenter::class);
    }

    /**
     * Get the results associated with the assessment.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
