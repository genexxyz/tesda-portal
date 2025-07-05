<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'student_id',
        'uli',
        'course_id',
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
}
