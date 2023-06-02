<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="{{ asset('js/form.js') }}" defer></script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <title>Location API</title>
    </head>

    <body>
        <form action="/" method="post">
            @csrf
            <label for="country">Country: </label>
            <select id="country" name="country" onchange="get_states()">
                <option value="" disabled selected>select</option>
                @foreach($countries as $country)
                <option class="btn-check" name="country" value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>

            <label for="state">State: </label>
            <select id="state" name="state" onchange="get_cities()">
                <option value="" disabled selected>select</option>
                @foreach($states as $state)
                <option class="btn-check" name="state" value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            </select>

            <label for="city">City: </label>
            <select id="city" name="city" onchange="get_state_and_flag()">
                <option value="" disabled selected>select</option>
                @foreach($cities as $city)
                <option class="btn-check" name="city" value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </form>
    </body>
</html>