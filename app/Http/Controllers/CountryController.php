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
        $username = 'mariaaa';

        // get countries array
        $response_countries = Http::get("http://api.geonames.org/countryInfoJSON?username={$username}");
        $countries = $response_countries->json();
        
        // get phone codes array
        $response_codes = Http::get("http://country.io/phone.json");
        $phone_codes = $response_codes->json();

        // get each country name and flag
        $response_flags = Http::withOptions([
            'verify' => true,
            'curl' => [
                CURLOPT_CAINFO => "C:\Users\sofia\Downloads\cacert.pem",
            ],
        ])->get('https://restcountries.com/v3.1/all?fields=name,flags');
        $flags = $response_flags->json();
        
        
        // save each country and infos
        foreach ($countries as $countries_array) {
            if(is_array($countries_array)) {
                foreach($countries_array as $country) {
                    if(isset($country['countryName'])) {
                        $name = $country['countryName'];
                        $short_name = $country['countryCode'];
                        $country_flag = "No flag";
                        if($phone_codes[$short_name] == "")
                            $phone_code = "";
                        else if($phone_codes[$short_name][0] != '+')
                            $phone_code = '+' . $phone_codes[$short_name];
                        else
                            $phone_code = $phone_codes[$short_name];

                        $geoname_id = intval($country['geonameId']);
                        foreach($flags as $flag) {
                            if($flag['name']['common'] == $name) {
                                $country_flag = $flag['flags']['png'];
                            }                            
                        }

                        $existing_country = Country::where('name', $name)->first();
                        if (!$existing_country) {
                            DB::insert('INSERT INTO country (name, short_name, phone_code, flag, geoname_id) VALUES (?, ?, ?, ?, ?)', [$name, $short_name, $phone_code, $country_flag, $geoname_id]);
                        }
                    } 
                }
            }
        }
    }
}