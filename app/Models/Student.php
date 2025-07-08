<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'uli',
        'course_id',
        'academic_year_id',
    ];

    /**
     * Get the user associated with the student.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with the student.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the academic year associated with the student.
     */
    public function academicYear()
    {
        return $this->belongsTo(Academic::class, 'academic_year_id');
    }

    /**
     * Get the full name of the student from user relationship.
     */
    public function getFullNameAttribute()
    {
        if ($this->user) {
            return trim($this->user->first_name . ' ' . ($this->user->middle_name ? $this->user->middle_name . ' ' : '') . $this->user->last_name);
        }
        return 'N/A';
    }

    /**
     * Get first name from user
     */
    public function getFirstNameAttribute()
    {
        return $this->user?->first_name;
    }

    /**
     * Get last name from user
     */
    public function getLastNameAttribute()
    {
        return $this->user?->last_name;
    }

    /**
     * Get middle name from user
     */
    public function getMiddleNameAttribute()
    {
        return $this->user?->middle_name;
    }

    /**
     * Get the results associated with the student.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }
}