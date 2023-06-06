<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use GuzzleHttp\Client;
use Carbon\Carbon;

class CityController extends Controller {
    public function store(int $state_geoname_id) {
        $username = 'sofiam';
        
        $state_model = State::where('geoname_id', $state_geoname_id)->first();
        $id_state = $state_model->id;

        $response_cities = Http::get("http://api.geonames.org/childrenJSON?geonameId={$state_geoname_id}&username={$username}");
        $cities = $response_cities->json();

        // Save each city
        foreach ($cities as $cities_array) {
            if(is_array($cities_array)) {
                foreach($cities_array as $city) {
                    if(isset($city['toponymName'])) {
                        $name = $city['toponymName'];
                        $lat = $city['lat'];
                        $lng = $city['lng'];
                        $geoname_id = $city['geonameId'];

                        $existing_city = City::where('name', $name)->where('id_state', $id_state)->first();
                        //$timezone = $this->get_timezone($lat, $lng);
                        if (!$existing_city) {
                            DB::insert('INSERT INTO city (name, lat, lng, timezone, timezone_offset, geoname_id, id_state) VALUES (?, ?, ?, ?, ?, ?, ?)', [$name, $lat, $lng, /* $timezone[0] */"", /* $timezone[1] */"", $geoname_id, $id_state]);
                        }
                    }
                }
            }
        }
        
        if($cities == []) {
            return [];
        }
    }

    public function store_timezone($lat, $lng) {        
        $timezone_defined = City::where('lat', $lat)->where('lng', $lng)->first();
        if($timezone_defined->timezone_name == "") {
            $username = 'sofiam';

            // get city hour
            $response_date_time = Http::get("http://api.geonames.org/timezoneJSON?lat={$lat}&lng={$lng}&username={$username}");
            $date_time = $response_date_time->json();

            if(!isset($date_time['time'])) {
                var_dump($date_time);
                print($lat);
                print($lng);
                exit;
            }

            $current_city_time = Carbon::createFromFormat('Y-m-d H:i', $date_time['time']);
            
            $gmt_offset = $date_time['gmtOffset'];

            $response_timezones = file_get_contents(__DIR__ . '/timezones.json');
            $timezones = json_decode($response_timezones, true);

            $timezone_name = "";
            $timezone_offset = "";

            foreach($timezones as $timezone) {
                if($timezone['dif'] == $gmt_offset) {
                    $timezone_name = $timezone['name'];
                    $timezone_offset = $timezone['to_gmt'];
                }
            }
            
            DB::update('UPDATE city SET timezone_name = ?, timezone_offset = ? WHERE lat = ? AND lng = ?', [$timezone_name, $timezone_offset, $lat, $lng]);
            //return [$timezone_name, $timezone_offset];
        }
    }
}