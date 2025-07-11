<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualificationType extends Model
{
    

    protected $fillable = [
        'code',
        'name',
        'level',
        'description',
    ];
    
    /**
     * Get the courses associated with the qualification type.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_qualification');
    }
    
    /**
     * Get the results associated with the qualification type.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }
    
    public function getDescriptionAttribute()
    {
        return $this->code . ' - ' . $this->level;
    }
}
