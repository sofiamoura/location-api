<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'city';

    protected $fillable = [
        'id', 'name', 'timezone', 'id_state'
    ];

    use HasFactory;
}