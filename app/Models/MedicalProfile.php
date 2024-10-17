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

    // relazione one to many con MedicalSpecialization
    public function medicalspecializations()
    {
        return $this->hasMany(MedicalSpecialization::class);
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

}
