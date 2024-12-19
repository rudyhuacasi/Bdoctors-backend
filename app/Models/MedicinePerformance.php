<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicinePerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    // public function medicalProfiles()
    // {
    //     return $this->belongsToMany(MedicalProfile::class, 'profile_performances', 'medicine_performance_id', 'medical_profile_id');
    // }
    public function profilePerformances()
    {
        return $this->hasMany(ProfilePerformance::class);
    }
}
