<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'first',
        'last',
        'email',
        'current_step',
        'unsub_token',
        'next_send_at'
    ];

    public function getNextSendAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}