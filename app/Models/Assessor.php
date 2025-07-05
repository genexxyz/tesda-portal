<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
    ];

    /**
     * Get the user associated with the assessor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
