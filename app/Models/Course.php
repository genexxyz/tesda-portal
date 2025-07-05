<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'campus_id',
    ];

    /**
     * Get the students associated with the course.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the campus associated with the course.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
    public function programHead()
    {
        return $this->hasOne(ProgramHead::class);
    }
}
