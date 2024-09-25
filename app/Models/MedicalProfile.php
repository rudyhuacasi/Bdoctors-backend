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
    public function Review()
    {
        return $this->hasMany(Review::class);
    }

    // relazione one to many con MedicalSpecialization
    public function MedicalSpecialization()
    {
        return $this->hasMany(MedicalSpecialization::class);
    }

    // relazione one to many con MedicalProfile
    public function Sponsorship()
    {
        return $this->hasMany(Sponsorship::class);
    }

    // relazione one to many con MedicalProfile
    public function Payment()
    {
        return $this->hasMany(Payment::class);
    }

    // relazione one to many con Message
    public function Message()
    {
        return $this->hasMany(Message::class);
    }

    // relazione one to many con Static
    public function Static()
    {
        return $this->hasMany(Static::class);
    }

}
