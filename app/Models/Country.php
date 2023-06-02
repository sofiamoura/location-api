<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'country';

    protected $fillable = [
        'id', 'name', 'short_name', 'phone_code', 'flag', 'geoname_id'
    ];


    use HasFactory;

    public function states() {
        return State::where('id_country', $this->id)->get();
    }
}