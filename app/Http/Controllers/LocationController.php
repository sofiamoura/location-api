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
        if(!$country->exists()) $this->store_all_locations();

        $countries = Country::orderBy('short_name', 'asc')->get();
    
        return view('location_form', ['countries' => $countries, 'states' => State::orderBy('name', 'asc')->get(), 'cities' => City::orderBy('name', 'asc')->get()]);
    }

    public function get_states(int $country_id) {
        $state = new State();
        if(!$state->exists()) $this->store_all_locations();

        $country = Country::find($country_id);
        $states = $country->states();

        return response()->json($states);
    }

    public function get_cities(int $state_id) {
        $city = new City();
        if(!$city->exists()) $this->store_all_locations();

        $state = State::find($state_id);
        $cities = $state->cities();

        return response()->json($cities);
    }

    public function get_country(int $state_id) {
        $state = State::find($state_id);
        $country = Country::find($state->id_country);

        return response()->json($country);
    }

    public function get_state(int $city_id) {
        $city = City::find($city_id);
        $state = State::find($city->id_state);

        return response()->json($state);
    }
}