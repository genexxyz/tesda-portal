<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'assessment_id',
        'student_id',
        'qualification_type_id',
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
    public function qualificationType()
    {
        return $this->belongsTo(QualificationType::class);
    }
    
}
