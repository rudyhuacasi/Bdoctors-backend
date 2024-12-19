<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'medical_profile_id',
        'sponsorship_id',
        'start_date',
        'end_date',
        'state',
    ];

    // relazione many to one con MedicalProfile
    public function medicalProfile()
    {
        return $this->belongsTo(MedicalProfile::class);
    }

    // relazione many to one con Sponsorship
    public function Sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }
}
