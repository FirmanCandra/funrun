<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'payment_methods' => 'array',
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function categories()
    {
        return $this->hasMany(EventCategory::class, 'event_id');
    }

    public function admins()
    {
        return $this->hasMany(User::class, 'event_id')->where('role', 'admin');
    }
}
