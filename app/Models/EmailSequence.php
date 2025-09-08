<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailSequence extends Model
{
    use HasFactory; // Ensure this line is present

    protected $fillable = [
        'first',
        'last',
        'email',
        'current_step',
        'unsub_token',
        'next_send_at',
        'ip_address',
        'location'];

    // No casts for next_send_at to prevent timezone conversion


}
