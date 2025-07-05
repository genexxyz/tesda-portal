<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'exam_type_id',
        'competency_type_id',
        'campus_id',
        'course_id',
        'academic_year_id',
        'assessment_center',
        'assessor_id',
        'assessment_date',
    ];


    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function competencyType()
    {
        return $this->belongsTo(CompetencyType::class);
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
}
