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
    public function store(string $state) {
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

        if(!is_null($cities_array)) {
            foreach($cities_array as $city) {
                $existing_city = City::where('name', $city['city_name'])->first();
                if (!$existing_city) {
                    DB::insert('INSERT INTO city (name, id_state) VALUES (?, ?)', [$city['city_name'], $id_state]);
                }
            }
        }
    }
}