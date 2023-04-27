<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;

use GuzzleHttp\Client;

class LocationController extends Controller {
    public function store_all_locations() {
        $country_controller = new CountryController();
        $country_controller->store();
        $countries = Country::all();

        foreach($countries as $country) {
            $state_controller = new StateController();
            $state_controller->store($country->name);
        }

        $states = State::all();

        foreach($states as $state) {
            $city_controller = new CityController();
            $city_controller->store($state->name);
        }
    }

    public function get_locations() {
        $country = new Country();
        $state = new State();
        $city = new City();
        if(!$country->exists() || !$state->exists() || !$city->exists()) $this->store_all_locations();

        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
    
        return view('location_form', ['countries' => $countries, 'states' => $states, 'cities' => $cities]);
    }

    public function submit_location() {
        $country_name = request('country');
        $state_name = request('state');
        $city = request('city');

        $state = State::where('name', $state_name)->first();

        $location = City::where('name', $city)->where('id_state', $state->id)->first();

        $country = Country::where('name', $country_name)->first();

        echo "<img src='" . $country->flag . "' alt='Country Flag'>";
    }
}