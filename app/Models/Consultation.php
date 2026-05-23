<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'status',
        'symptoms',
        'urgency',
        'escalation_level',
        'category',
        'ai_summary',
        'started_at',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function messages()
    {
        return $this->hasMany(ConsultationMessage::class);
    }
}
