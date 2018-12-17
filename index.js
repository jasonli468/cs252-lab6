let places = [];
let minPrice = 1;
let maxPrice = 4;
let distance = 0;
let searchMap = null;
let searchMarker = null;
let circle = null;
let placeMap = null;
let placeMarker = null;
let place = null;
let loc = null;
let floatCoords = true;
let shownPlaces = [];

// Functions to get coordinates of  the user
function geolocationPosition(position){
    // Get coordinates based on geolocation
    loc = {lat: position.coords.latitude, lng: position.coords.longitude};
    initSearchMap();
}
function ipSearch(){
    // Get coordinates from a public API based on IP
    $.get('https://ipinfo.io/' + $('#ip').val() + '/json', '', function(position){
        let splitLoc = position.loc.split(',');
        loc = {lat: parseFloat(splitLoc[0]), lng: parseFloat(splitLoc[1])};
        initSearchMap();
    })
}

// Initialize the map for the search functionality
function initSearchMap(){
    // Initialize the map, marker, and circle with the default values
    searchMap = new google.maps.Map(document.getElementById("searchMap"), {
        center: loc,
        zoom:15
    });
    searchMarker = new google.maps.Marker({
        position: loc,
        animation: google.maps.Animation.DROP,
        map: searchMap
    });
    circle = new google.maps.Circle({
        strokeColor: '#131f28',
        strokeOpacity: 0.25,
        strokeWeight: 1,
        fillColor: '#131f28',
        map: searchMap,
        center: loc,
        radius: distance
    })

    // Set onclick listeners to move the stored coordinates, map, marker, and circle whenever a user clicks on the map
    searchMap.addListener('click', function(event){
        loc = event.latLng;
        searchMap.setCenter(loc);
        searchMarker.setPosition(loc);
        circle.setCenter(loc);
        floatCoords = false;
    })
    circle.addListener('click', function(event){
        loc = event.latLng;
        searchMap.setCenter(loc);
        searchMarker.setPosition(loc);
        circle.setCenter(loc);
        floatCoords = false;
    })
}

function initPlaceMap(){
    // Initialize the map, marker, and circle with the default values. Offset the map to make room for the place information overlay.
    let mapOffset = {lat: place.geometry.location.lat, lng: place.geometry.location.lng - .011};
    placeMap = new google.maps.Map(document.getElementById("placeMap"), {
        center: mapOffset,
        zoom:15
    });
    placeMarker = new google.maps.Marker({
        position: place.geometry.location,
        animation: google.maps.Animation.DROP,
        map: placeMap
    });
}
function updatePlaceMap(){
    let mapOffset = {lat: place.geometry.location.lat, lng: place.geometry.location.lng - .011};
    placeMap.setCenter(mapOffset);
    placeMarker.setPosition(place.geometry.location);
}
function updatePlaceDetails(){
    $('#placeImg').attr('src', '');
    let priceRepresentation = place.price_level > 0 ? '$'.repeat(place.price_level) : 'Not available';
    place.opening_hours.open_now ? $('#placeName').html(place.name) : $('#placeName').html(place.name + " (CLOSED NOW)");
    $('#placeDetails').html(place.vicinity + '<br>' + place.rating + '<br>' + priceRepresentation);
    $('#place').attr('class', '');
    if(place.photos.length > 0)
    {
        $('#placeImg').attr('src', 'api/getpicture.php?reference=' + place.photos[0].photo_reference);
    }
}
// Get and display a random restaurant within the search radius
function getPlace(){
    let stringLoc = floatCoords ? loc.lat + ',' + loc.lng : loc.lat() + ',' + loc.lng();
    // Call Google Map's Nearby Places API to get a list of nearby restaurants. Do initial call through PHP rather than through JS directly to protect API key
    $.get('api/nearbysearch.php', 'location=' + stringLoc + '&radius=' + distance + '&minPrice=' + minPrice + '&maxPrice=' + maxPrice + '&open=' + $('#open').prop('checked'), function(data){
        if(data.status === "OK")
        {
            // Get the list of restaurants the user has blacklisted, or nothing if they are not logged in
            $.get('api/getblacklist.php', '', function(blacklistData){
                // Filter through the list of places, only adding them if they are not in a user's blacklist
                if(blacklistData.status === "Success")
                {
                    // Binary search each place to see if it's in the blacklist (blacklist assumed to be sorted by SQL)
                    data.results.forEach(function (place){
                        let min = 0;
                        let max = blacklistData.placeIDs.length - 1;
                        let mid;
                        let add = true;
                        while(min <= max)
                        {
                            mid = Math.floor((max - min) / 2) + min;
                            
                            if(blacklistData.placeIDs[mid] === place.place_id)
                            {
                                add = false;
                                break;
                            }
                            else if(place.place_id < blacklistData.placeIDs[mid])
                                max = mid - 1;
                            else
                                min = mid + 1;
                        }
                        if(add === true)
                            places.push(place);
                    });
                }
                // If they aren't logged in or have no places blacklisted, accept all of the results
                else
                {
                    places = data.results;
                }

                if(places.length > 0)
                {
                    // Pick a random place from the list of places
                    shownPlaces = [];
                    let i = Math.floor(Math.random() * places.length);
                    shownPlaces.push(i);
                    place = places[i];
                    if(shownPlaces.length >= places.length)
                        $('#showAnother').attr('class', 'hidden');
                    else
                        $('#showAnother').attr('class', 'rightButton');
                    $('#message').html('Recommended Place:');
                    if(placeMap === null)
                        initPlaceMap();
                    else
                        updatePlaceMap();
                    updatePlaceDetails();
                }
                else
                {
                    $('#message').html("ERROR: No results that aren't blacklisted");
                    $('#place').attr('class', 'hidden');
                }
                $('#back').attr('class', '');
            })
        }
        else
        {
            $('#message').html('ERROR: ' + data.status);
            $('#back').attr('class', '');
        }
    })
}

$(document).ready(function(){
    // Get the distance selected for map initialization (could differ depending on what the user's setting is if they are logged in)
    distance = parseFloat($('#distance').find(":selected").val());
    // If the browser supports geolocation, request it, using that location if accepted or going off of IP for coordinates if denied
    if(navigator.geolocation)
    {
        navigator.geolocation.getCurrentPosition(geolocationPosition, ipSearch);
    }
    // If browser does not support geolocation, go off of IP for coorindates
    else
    {
        ipSearch();
    }

    // Generate the price slider
    $('#priceSlider').slider({
        range: true,
        min: 1,
        max: 4,
        step: 1,
        values: [1, 4],
        change: function(event, ui){
            minPrice = ui.values[0];
            maxPrice = ui.values[1];
        }
    })
    // Generate the price slider labels
    for (let i = 0; i <= 3; i++) {
        let label = $('<label>'+'$'.repeat(i + 1)+'</label>').css('left',(i/3*97)+'%');
        $("#priceSlider").append(label);
    }

    $('#preset').on('change', function(){
        let name = this.value;
        
        // Reset coordinates
        floatCoords = true;
        loc = {lat: parseFloat($('#' + name + 'Lat').val()), lng: parseFloat($('#' + name + 'Lng').val())};
        searchMap.setCenter(loc);
        searchMarker.setPosition(loc);
        circle.setCenter(loc);

        // Reset distance and map zoom
        distance = parseFloat($('#' + name + 'Dist').val());
        circle.setRadius(distance);
        switch(distance)
        {
            case 804.672:
                searchMap.setZoom(15);
                break;
            case 1609.344:
                searchMap.setZoom(14);
                break;
            case 4032.36:
                searchMap.setZoom(13);
                break
            case 8046.72:
                searchMap.setZoom(12);
                break;
            case 16093.44:
                searchMap.setZoom(11);
                break;
        }

        // Open now checkbox and price slider
        $('#open').prop('checked', $('#' + name + 'Open').val() === '1')
        $('#priceSlider').slider('values', [parseInt($('#' + name + 'MinPrice').val()), parseInt($('#' + name + 'MaxPrice').val())])
    })

    // Whenever the distance changes, change the zoom of the map and radius of the circle accordingly
    $('#distance').on('change', function(){
        distance = parseFloat(this.value);
        circle.setRadius(distance);
        switch(distance)
        {
            case 804.672:
                searchMap.setZoom(15);
                break;
            case 1609.344:
                searchMap.setZoom(14);
                break;
            case 4032.36:
                searchMap.setZoom(13);
                break
            case 8046.72:
                searchMap.setZoom(12);
                break;
            case 16093.44:
                searchMap.setZoom(11);
                break;
        }
    })
        
    $('#filters').submit(function(){
        $('#filters').attr('class', 'hidden');
        $('#message').html('Loading...');
        getPlace();
        return false;
    })

    // Go back to search screen from results screen
    $('#back').click(function(){
        $('#message').html('Select your location, choose your options, and hit Search to get a random restaurant to eat at!');
        $('#filters').attr('class', '');
        $('#place').attr('class', 'hidden');
        $('#back').attr('class', 'hidden');
        places = [];
    })

    $('#showAnother').click(function(){
        let i = Math.floor(Math.random() * places.length);
        while(shownPlaces.includes(i))
        {
            i = Math.floor(Math.random() * places.length);
        }
        shownPlaces.push(i);
        place = places[i];
        updatePlaceMap();
        updatePlaceDetails();
        if(shownPlaces.length >= places.length)
            $('#showAnother').attr('class', 'hidden');
    })

    $('#blacklist').click(function(){
        // Add the place to the blacklist and then show another place. encodeURIComponent ensures the name gets passed properly if there are special characters in it
        $.post('api/addtoblacklist.php', 'placeID=' + place.place_id + '&placeName=' + encodeURIComponent(place.name), function(){

        })
        if(shownPlaces.length < places.length)
        {
            $('#showAnother').click();
        }
        else
        {
            $('#message').html("ERROR: No results that aren't blacklisted");
            $('#place').attr('class', 'hidden');
        }
    })
});