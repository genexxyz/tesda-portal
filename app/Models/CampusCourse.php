<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusCourse extends Model
{
    protected $table = "campus_course";
    protected $fillable = [
        'campus_id',
        'course_id',
    ];

    public function courses()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    public function campuses()  
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }
}
