<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'code', //MA, BG, etc.
        'name',// MALOLOS, etc.
        'number', // e.g. 1, 2, etc.
        'color', // e.g. #FF5733
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Get the courses associated with the campus.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'campus_course');
    }
}
