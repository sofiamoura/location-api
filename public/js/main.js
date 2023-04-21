 import {cityTimezones} from 'city-timezones'
//var cityTimezones = require('city-timezones');

// Get the form element
const form = document.querySelector('form');

// Add a submit event listener to the form
form.addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent form submission

    // Get the selected city from the form
    const selected_city = document.getElementById('city').value;
    console.log(selected_city)

    // Retrieve the timezone information for the selected city
    const city = cityTimezones.lookupViaCity(selected_city);

    console.log(city)
    
    if (city) {
        // Extract the timezone from the city object
        const timezone = city.timezone;

        // Display the timezone in an alert or use it as needed
        console.log("The timezone of ${selected_city} is ${timezone}");
    } else {
        // If city not found, display an error message
        console.log('City not found');
    }
});