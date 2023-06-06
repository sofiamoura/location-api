<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Timezone;

use GuzzleHttp\Client;
use Carbon\Carbon;

class TimezoneController extends Controller {
    public function so_timezone($lat, $lng) {
        $response_timezones = file_get_contents(__DIR__ . '/timezones.json');
        $timezones = json_decode($response_timezones, true);
        
        $current_date_time = Carbon::now()->subSeconds(60)->utc();

        if($current_date_time->format('m') > 3) {
            $current_date_time = $current_date_time->addHour();
        }
        
        $username = 'sofiam';

        // get city hour
        $response_date_time = Http::get("http://api.geonames.org/timezoneJSON?lat={$lat}&lng={$lng}&username={$username}");
        $date_time = $response_date_time->json();

        $current_city_time = Carbon::createFromFormat('Y-m-d H:i', $date_time['time']);
        $time_diff = $current_city_time->diff($current_date_time);

        $hours = $time_diff->h;
        $minutes = $time_diff->i;

        $time_diff = $hours + ($minutes / 60);

        if($current_city_time < $current_date_time) {
            $time_diff = -$time_diff;
        }


        
        /* $existing_timezone = Timezone::where('name', $name)->first();
        if (!$existing_timezone) {
            DB::insert('INSERT INTO timezone (name, to_gmt, min_lim, max_lim) VALUES (?, ?, ?, ?)', []);
        } */
    }
}