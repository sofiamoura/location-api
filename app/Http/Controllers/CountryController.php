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