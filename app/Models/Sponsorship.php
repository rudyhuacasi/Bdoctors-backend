<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    use HasFactory;

    // relazione many to one con MedicalProfile
    public function MedicalProfile()
    {
        return $this->belongsTo(MedicalProfile::class);
    }

    // relazione one to many con Payment
    public function Payment()
    {
        return $this->hasMany(Payment::class);
    }
}
