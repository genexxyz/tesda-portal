<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Academic extends Model
{
    protected $fillable = [
        'start_year',
        'end_year',
        'semester',
        'is_active',
        'status',
        'description',
    ];

    /**
     * Get the courses associated with the academic record.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the description attribute for the academic year.
     */
    public function getDescriptionAttribute()
    {
        return $this->start_year . '-' . $this->end_year . ' ' . ucfirst($this->semester);
    }

}
