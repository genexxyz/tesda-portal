<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Get the students associated with the course.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    
    /**
     * Get the campuses associated with the course.
     */
    public function campuses()
    {
        return $this->belongsToMany(Campus::class, 'campus_course');
    }
    
    /**
     * Get the qualification types associated with the course.
     */
    public function qualificationTypes()
    {
        return $this->belongsToMany(QualificationType::class, 'course_qualification');
    }
    
    public function programHead()
    {
        return $this->hasOne(ProgramHead::class);
    }
}
