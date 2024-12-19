<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    
    public function medicalProfiles()
    {
        return $this->hasMany(MedicalProfile::class, 'medical_specializations', 'specialization_id', 'medical_profile_id');
    }
}

