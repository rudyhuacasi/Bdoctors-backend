<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    // relazione one to many con MedicalSpecialization
    public function MedicalSpecialization()
    {
        return $this->hasMany(MedicalSpecialization::class);
    }
}
