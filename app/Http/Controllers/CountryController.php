<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use GuzzleHttp\Client;

class CountryController extends Controller {
    public function store() {
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
        $url = 'https://www.universal-tutorial.com/api/countries/';

        $ut_headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Email: sofia.smoura.sm@gmail.com',
            'Authorization: Bearer '.$ut_token
        );
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $ut_headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CAINFO, "C:\Users\sofia\Downloads\cacert.pem");
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
            exit;
        }
        curl_close($curl);
        $countries_array = json_decode($response, true);

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
    }
}