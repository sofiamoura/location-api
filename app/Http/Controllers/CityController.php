<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use GuzzleHttp\Client;

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
                        $existing_city = City::where('name', $city['toponymName'])->where('id_state', $id_state)->first();
                        if (!$existing_city) {
                            DB::insert('INSERT INTO city (name, id_state) VALUES (?, ?)', [$city['toponymName'], $id_state]);
                        }
                    }
                }
            }
        }
        
        if($cities == []) {
            return [];
        }
    }
}