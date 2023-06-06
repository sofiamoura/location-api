<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'timezone';

    protected $fillable = [
        'id', 'name', 'to_gmt', 'min_lim', 'max_lim'
    ];

    use HasFactory;
}