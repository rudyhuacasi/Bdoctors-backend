<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePerformance extends Model
{
    use HasFactory;

    public function medicinePerformance()
    {
        return $this->belongsTo(MedicinePerformance::class, 'medicine_performance_id');
    }

    public function medicalProfile()
    {
        return $this->belongsTo(MedicalProfile::class, 'medical_profile_id');
    }


}
