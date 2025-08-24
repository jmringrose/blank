<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'title',
        'notes', 
        'draft',
        'filename'
    ];

    public function questionSequences()
    {
        return $this->hasMany(QuestionSequence::class);
    }

    protected $casts = [
        'draft' => 'boolean'
    ];
}