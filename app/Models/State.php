<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'state';

    protected $fillable = [
        'id', 'name', 'id_country'
    ];

    use HasFactory;

    public function cities() {
        return City::where('id_state', $this->id)->get();
    }
}