<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalProfile extends Model
{
    use HasFactory;
    
    // relazione many to one con user
    public function User()
    {
        return $this->belongsTo(User::class);
    }

    // relazione one to many con MedicalProfile
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    // relazione one to many con MedicalProfile
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // relazione one to many con Message
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // relazione one to many con Static
    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

    public function specializations()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

    public function performances()
    {
        return $this->belongsToMany(MedicinePerformance::class, 'profile_performances', 'medical_profile_id', 'medicine_performance_id');
    }

    public function profilePerformances()
    {
        return $this->hasMany(ProfilePerformance::class);
    }
    public function medicinePerformance()
    {
        return $this->belongsTo(MedicinePerformance::class, 'medicine_performance_id');
    }
}