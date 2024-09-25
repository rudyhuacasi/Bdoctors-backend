<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // relazione many to one con MedicalProfile
    public function MedicalProfile()
    {
        return $this->belongsTo(MedicalProfile::class);
    }

    // relazione many to one con Sponsorship
    public function Sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }
}
