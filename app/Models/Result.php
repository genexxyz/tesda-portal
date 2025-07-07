<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'assessment_id',
        'student_id',
        'competency_type_id',
        'remarks',

    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function competencyType()
    {
        return $this->belongsTo(CompetencyType::class);
    }
    
}
