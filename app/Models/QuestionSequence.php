<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'first',
        'last', 
        'email',
        'question_step_id',
        'sent',
        'unsubscribed',
        'email_history',
        'unsub_token'
    ];

    protected $casts = [
        'sent' => 'boolean',
        'unsubscribed' => 'boolean',
        'email_history' => 'array'
    ];

    public function questionStep()
    {
        return $this->belongsTo(QuestionStep::class);
    }
}