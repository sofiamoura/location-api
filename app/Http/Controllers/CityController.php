<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use Carbon\Carbon;

class CityController extends Controller {
    public function store(int $state_geoname_id) {
        $username = 'sofiasm';
        
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
                            City::create([
                                'name' => $name,
                                'lat' => $lat,
                                'lng' => $lng,
                                'timezone_name' => "",
                                'timezone_code' => "",
                                'timezone_offset' => "",
                                'geoname_id' => $geoname_id,
                                'id_state' => $id_state,
                            ]);
                        } 
                    }
                }
            }
        }
        
        if(empty($cities)) {
            return [];
        }
    }

    public function store_timezone($id, $lat, $lng) {        
        $timezone_defined = City::where('lat', $lat)->where('lng', $lng)->first();
        /* if($timezone_defined->timezone_code == "") { */
            $username = 'sofiasm';

            // get city hour
            $response_date_time = Http::get("http://api.geonames.org/timezoneJSON?lat={$lat}&lng={$lng}&username={$username}");
            $date_time = $response_date_time->json();
           
            if(!isset($date_time['gmtOffset'])) {
                var_dump($date_time);
                exit;
            }

            $currentDate = Carbon::now();
/* 
            $previousDSTStatus = $yourPreviousDSTStatus; // Store the previous DST status somewhere

            if ($currentDate->isDST() !== $previousDSTStatus) {
            // DST status has changed
            $event = new DaylightSavingTimeChanged($currentDate->isDST());
            event($event);

            // Update the stored DST status for the next check */

            $offset = $date_time['gmtOffset'];   

            $response_timezones = file_get_contents(__DIR__ . '/timezones.json');
            $timezones = json_decode($response_timezones, true);

            $timezone_code = "";
            $timezone_offset = "";

            foreach($timezones as $timezone) {
                if($timezone['dif'] == $offset) {
                    $timezone_code = $timezone['name'];
                    $timezone_offset = $timezone['to_gmt'];
                }
            }
            
            DB::update('UPDATE city SET timezone_name = ?, timezone_code = ?, timezone_offset = ? WHERE id = ?', [$date_time['timezoneId'], $timezone_code, $timezone_offset, $id]);
            //return [$timezone_code, $timezone_offset];
        /* } */
    }
}