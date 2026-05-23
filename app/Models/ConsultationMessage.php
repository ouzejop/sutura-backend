<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationMessage extends Model
{
    protected $fillable = [
        'consultation_id',
        'sender_type',
        'sender_id',
        'content',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
