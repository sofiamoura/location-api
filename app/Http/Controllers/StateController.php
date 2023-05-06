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
    public function store(string $country) {
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

        $country_model = Country::where('short_name', $country)->first();
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
    }
}