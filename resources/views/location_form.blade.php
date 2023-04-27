<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="https://cdn.jsdelivr.net/npm/city-timezones@1.1.1/citytimezones.min.js"></script>
        <script src="{{ asset('js/main.js') }}" defer></script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <title>Location API</title>
    </head>

    <body>
        <form action="/" method="post">
            @csrf
            <label for="country">Country: </label>
            <select id="country" name="country">
                <option value="" disabled selected>select</option>
                @foreach($countries as $country)
                <option class="btn-check" name="country" value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>

            <label for="state">State: </label>
            <select id="state" name="state">
                <option value="" disabled selected>select</option>
            </select>

            <label for="city">City: </label>
            <select id="city" name="city">
                <option value="" disabled selected>select</option>
            </select>

            <button type="submit" name="submit">Search</button>
        </form>

        <script>
            $(document).ready(function() {
                $('#country').change(function(event) {
                    var id_country = this.value
                    $('#state').html('')
                    
                    $.ajax({
                        url: "{{url('get-states')}}",
                        type: "POST",
                        daatType: 'json',
                        data: {country_id: id_country,_token:"{{ csrf_token() }}"},
                        success: function(response) {
                            console.log(response)
                        }
                    })
                })
            })
        </script>
    </body>
</html>