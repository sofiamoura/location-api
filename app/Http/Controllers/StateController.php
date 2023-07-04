<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use GuzzleHttp\Client;

class StateController extends Controller {
    public function store(int $country_geoname_id) {
        $username = 'sofiam';

        $country_model = Country::where('geoname_id', $country_geoname_id)->first();
        $id_country = $country_model->id;
        $response_states = Http::get("http://api.geonames.org/childrenJSON?geonameId={$country_geoname_id}&username={$username}");
        $states = $response_states->json();

        // Save each state
        foreach ($states as $states_array) {
            if(is_array($states_array)) {
                foreach($states_array as $state) {
                    if(isset($state['name'])) {
                        $existing_state = State::where('name', $state['name'])->where('id_country', $id_country)->first();
                        if (!$existing_state) {
                            State::create([
                                'name' => $state['name'],
                                'id_country' => $id_country,
                                'geoname_id' => $state['geonameId'],
                            ]);
                        }
                    }
                }
            }
        }
        
        if(empty($states)) {
            return [];
        }
    }
}