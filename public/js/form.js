function get_states() {
    // selected country id
    let country_id = document.getElementById('country').value;

    // if a country has been selected
    if(country_id) {

        // get request to retrieve its states
        axios.get('get_states/' + country_id)
        .then(response => {
            let states = response.data;
            
            // reference to the states dropdown
            let state_select = document.getElementById('state');
            
            if(states.length != 0) {
                // clear current options and add correct states
                state_select.innerHTML = '<option value="" disabled selected>select</option>';
                states.forEach(state => {
                state_select.innerHTML += '<option name="state" value="' + state.id + '">' + state.name + '</option>';
                });
            }
            else {
                state_select.innerHTML = '<option value="" disabled selected>no states</option>';
            }
        })
        .catch(error => {
            console.log(error);
        });
    }

   }

function get_cities() {
// selected state id
let state_id = document.getElementById('state').value;

// if a state has been selected
if(state_id) {

    // get request to retrieve its states
    axios.get('/get_cities/' + state_id)
    .then(response => {
        let cities = response.data;

        // reference to the cities dropdown
        let city_select = document.getElementById('city');
        
        if(cities.length != 0) {
            // clear current options and add correct cities
            city_select.innerHTML = '<option value="" disabled selected>select</option>';
            cities.forEach(city => {
                city_select.innerHTML += '<option value="' + city.id + '">' + city.name + '</option>';
            });
        }
        else {
            city_select.innerHTML = '<option value="" disabled selected>no cities</option>';
        }

        // get state's country
        axios.get('/get_country/' + state_id)
            .then(response => {
                let country = response.data;

                let country_select = document.getElementById('country');

                // update country dropdown
                country_select.value = country.id;
            })
            .catch(error => {
                console.log(error);
            });
    })
    .catch(error => {
        console.log(error);
    });
}
}

function get_state_and_flag() {
// selected city id
let city_id = document.getElementById('city').value;

// if a city has been selected
if(city_id) {

    // get request to retrieve its state
    axios.get('/get_state/' + city_id)
    .then(response => {
        let state = response.data;

        // reference to the states dropdown
        let state_select = document.getElementById('state');

        // update country dropdown
        state_select.value = state.id;  
        
        let prev_flag = document.getElementById('flag');
        if (prev_flag) {
            prev_flag.parentNode.removeChild(prev_flag);
        }
        let prev_timezone_name = document.getElementById('timezone_name');
        if (prev_timezone_name) {
            prev_timezone_name.parentNode.removeChild(prev_timezone_name);
        }
        let prev_timezone_offset = document.getElementById('timezone_offset');
        if (prev_timezone_offset) {
            prev_timezone_offset.parentNode.removeChild(prev_timezone_offset);
        }

        // get request to retrieve its country
        axios.get('/get_country/' + state.id)
        .then(response => {
            let country = response.data;

            // reference to the states dropdown
            let country_select = document.getElementById('country');

            // update country dropdown
            country_select.value = country.id;        
            
            let flag = document.createElement('img');
            flag.id = 'flag';
            flag.src = country.flag;
            let body = document.querySelector('body');
            body.appendChild(flag);


            let prev_phone_code = document.getElementById('phone_code');
            if (prev_phone_code) {
                prev_phone_code.parentNode.removeChild(prev_phone_code);
            }
            let phone_code = document.createElement('p');
            phone_code.id = 'phone_code';
            phone_code.innerHTML = country.phone_code;
            body.appendChild(phone_code);
        })
        .catch(error => {
            console.log(error);
        });

        // get request to retrieve itself
        axios.get('/get_timezone/' + city_id)
        .then(response => {
            let timezone_name = response.data[0];
            let timezone_offset = response.data[1];

            let body = document.querySelector('body');
            let timezone_name_tag = document.createElement('p');
            let timezone_offset_tag = document.createElement('p');
            timezone_name_tag.id = 'timezone_name'; 
            timezone_offset_tag.id = 'timezone_offset';
            timezone_name_tag.innerHTML = timezone_name;
            timezone_offset_tag.innerHTML = timezone_offset;
            body.appendChild(timezone_name_tag);
            body.appendChild(timezone_offset_tag);
        })
        .catch(error => {
            console.log(error);
        });
    })
    .catch(error => {
        console.log(error);
    });
}
}