<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Record extends Model
{
    use HasFactory;
    protected $fillable = [
        "app_id",
        "module_name",
        "verb",
        "event",
        "email",
        "user_id",
        "role",
        "name",
        "result",
        "user_ip",
        "location",
        "when_event",
    ];

    protected $table = "recordstore";

    protected $hidden = [

    ];
    protected $dates = [
        "created_at",
        "updated_at",
        'when_event',
    ];

    protected $casts = [
        //'when_event' => 'datetime:m/d/Y H:i:s',
        //'score' => 'string',
    ];

}
