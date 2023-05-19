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
        
        // get request to retrieve its country
        axios.get('/get_country/' + state.id)
        .then(response => {
            let country = response.data;

            // reference to the states dropdown
            let country_select = document.getElementById('country');

            // update country dropdown
            country_select.value = country.id;        
            
            let prev_flag = document.getElementById('flag');
            if (prev_flag) {
                prev_flag.parentNode.removeChild(prev_flag);
            }
            let flag = document.createElement('img');
            flag.id = 'flag';
            flag.src = country.flag;
            let body = document.querySelector('body');
            body.appendChild(flag);
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