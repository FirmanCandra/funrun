<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
