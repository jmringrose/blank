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
        'next_send_at'];

    /**
     * Accessor to format the next_send_at field.
     */
    public function getNextSendAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s'); // Change the date format as needed
    }


}
