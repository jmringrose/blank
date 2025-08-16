<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'title',
        'filename',
        'draft'
    ];

    protected $casts = [
        'draft' => 'boolean'
    ];
}
