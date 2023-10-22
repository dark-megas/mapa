<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event_map extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'data_id',
        'data_link',
        'lat',
        'lng',
        'source',
    ];
}
