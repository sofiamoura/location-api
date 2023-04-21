<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use GuzzleHttp\Client;

class LocationController extends Controller {
    public function get_locations() {
        $countries = $this->get_countries();
        
        $states = [];
        $cities = [];
        $i = 0;
        foreach($countries as $country) {
            if($i == 20) break;
            $country_states = $this->get_country_states($country->name);
            
            $country_cities = $this->get_country_cities($country->name);
            foreach($country_cities as $city) {
                array_push($cities, $city);
            }
            
            foreach($country_states as $state) {
                array_push($states, $state);
                
                $state_cities = $this->get_state_cities($state->name);
                foreach($state_cities as $city) {
                    array_push($cities, $city);
                }
            }   

            $i++;
        }

        return view('location_form', ['countries' => $countries, 'states' => $states, 'cities' => $cities]);
    }

    public function get_countries() {
        // get each country name and flag
        $response = Http::withOptions([
            'verify' => true,
            'curl' => [
                CURLOPT_CAINFO => "C:\Users\sofia\Downloads\cacert.pem",
            ],
        ])->get('https://restcountries.com/v3.1/all?fields=name,flags');

        // for each country
         foreach($response->json() as $country) {
            // save name and flag
            $name = $country['name']['official'];
            $short_name = $country['name']['common'];
            $flag = $country['flags']['png'];
            
            $existing_country = Country::where('name', $name)->first();
            if (!$existing_country) {
                DB::insert('INSERT INTO country (name, short_name, phone_code, flag) VALUES (?, ?, "+351", ?)', [$name, $short_name, $flag]);
            }
        }
        return Country::all();
    }

    public function get_country_states(string $country) {
        $client_token = new Client();

        // get api_token
        $response_token = $client_token->request('GET', 'https://www.universal-tutorial.com/api/getaccesstoken', [
            'headers' => [
                'Accept' => 'application/json',
                'api-token' => 'eh61RL3cO4dYIu7ZtT3SXL67rUDxYl5Dv9rScsBi9MZkF3paI4-NJAQT6cLyWNW3q5s',
                'user-email' => 'sofia.smoura.sm@gmail.com'
            ],
            'verify' => "C:\Users\sofia\Downloads\cacert.pem"
        ]);

        $data = json_decode($response_token->getBody());

        $ut_token = $data->auth_token;

        // and get its states
        $states_url = 'https://www.universal-tutorial.com/api/states/' . $country;

        $ut_headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Email: sofia.smoura.sm@gmail.com',
            'Authorization: Bearer '.$ut_token
        );
        $states_options = array(
            CURLOPT_URL => $states_url,
            CURLOPT_HTTPHEADER => $ut_headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        );
        $states_curl = curl_init();
        curl_setopt($states_curl, CURLOPT_CAINFO, "C:\Users\sofia\Downloads\cacert.pem");
        curl_setopt_array($states_curl, $states_options);
        $response_states = curl_exec($states_curl);
        if (curl_errno($states_curl)) {
            echo 'Error: ' . curl_error($states_curl);
            exit;
        }
        curl_close($states_curl);
        $states_array = json_decode($response_states, true);

        $country_model = Country::where('name', $country)->first();
        $id_country = $country_model->id;

        if($states_array == []) {
            return [];
        }

        foreach($states_array as $state) {
            $existing_state = State::where('name', $state['state_name'])->first();
            if (!$existing_state) {
                DB::insert('INSERT INTO state (name, id_country) VALUES (?, ?)', [$state['state_name'], $id_country]);
            }
        }

        return State::where('id_country', $id_country)->get();
    }

    public function get_state_cities(string $state) {
        // criar model para states e guardar la cada um
        $client_token = new Client();

        // get api_token
        $response_token = $client_token->request('GET', 'https://www.universal-tutorial.com/api/getaccesstoken', [
            'headers' => [
                'Accept' => 'application/json',
                'api-token' => 'eh61RL3cO4dYIu7ZtT3SXL67rUDxYl5Dv9rScsBi9MZkF3paI4-NJAQT6cLyWNW3q5s',
                'user-email' => 'sofia.smoura.sm@gmail.com'
            ],
            'verify' => "C:\Users\sofia\Downloads\cacert.pem"
        ]);

        $data = json_decode($response_token->getBody());

        $ut_token = $data->auth_token;
        
        $cities_url = 'https://www.universal-tutorial.com/api/cities/'. $state;

        $ut_headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Email: sofia.smoura.sm@gmail.com',
            'Authorization: Bearer '.$ut_token
        );

        $cities_options = array(
            CURLOPT_URL => $cities_url,
            CURLOPT_HTTPHEADER => $ut_headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        );

        $cities_curl = curl_init();
        curl_setopt($cities_curl, CURLOPT_CAINFO, "C:\Users\sofia\Downloads\cacert.pem");
        curl_setopt_array($cities_curl, $cities_options);
        $response_cities = curl_exec($cities_curl);
        if (curl_errno($cities_curl)) {
            echo 'Error: ' . curl_error($cities_curl);
            exit;
        }
        curl_close($cities_curl);
        $cities_array = json_decode($response_cities, true);

        $state_model = State::where('name', $state)->first();
        $id_state = $state_model->id;

        foreach($cities_array as $city) {
            $existing_city = City::where('name', $city['city_name'])->first();
            if (!$existing_city) {
                DB::insert('INSERT INTO city (name, id_state) VALUES (?, ?)', [$city['city_name'], $id_state]);
            }
        }

        return City::where('id_state', $id_state)->get();
    }

    // para os paises que nao tem estados
    public function get_country_cities(string $country) {
        // criar model para states e guardar la cada um
        $client_token = new Client();

        // get api_token
        $response_token = $client_token->request('GET', 'https://www.universal-tutorial.com/api/getaccesstoken', [
            'headers' => [
                'Accept' => 'application/json',
                'api-token' => 'eh61RL3cO4dYIu7ZtT3SXL67rUDxYl5Dv9rScsBi9MZkF3paI4-NJAQT6cLyWNW3q5s',
                'user-email' => 'sofia.smoura.sm@gmail.com'
            ],
            'verify' => "C:\Users\sofia\Downloads\cacert.pem"
        ]);

        $data = json_decode($response_token->getBody());

        $ut_token = $data->auth_token;
        
        $cities_url = 'https://www.universal-tutorial.com/api/cities/'. $country;

        $ut_headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Email: sofia.smoura.sm@gmail.com',
            'Authorization: Bearer '.$ut_token
        );

        $cities_options = array(
            CURLOPT_URL => $cities_url,
            CURLOPT_HTTPHEADER => $ut_headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        );

        $cities_curl = curl_init();
        curl_setopt($cities_curl, CURLOPT_CAINFO, "C:\Users\sofia\Downloads\cacert.pem");
        curl_setopt_array($cities_curl, $cities_options);
        $response_cities = curl_exec($cities_curl);
        if (curl_errno($cities_curl)) {
            echo 'Error: ' . curl_error($cities_curl);
            exit;
        }
        curl_close($cities_curl);
        $cities_array = json_decode($response_cities, true);

        $country_model = Country::where('name', $country)->first();
        $id_country = $country_model->id;

        foreach($cities_array as $city) {
            $existing_city = City::where('name', $city['city_name'])->first();
            if (!$existing_city) {
                DB::insert('INSERT INTO city (name, id_country) VALUES (?, ?)', [$city['city_name'], $id_country]);
            }
        }

        return City::where('id_country', $id_country)->get();
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