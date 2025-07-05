<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramHead extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
    ];

    /**
     * Get the user associated with the program head.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with the program head.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
