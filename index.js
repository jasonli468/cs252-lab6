let places = [];
let minPrice = 0;
let maxPrice = 0;
let distance = 0;
let searchMap = null;
let searchMarker = null;
let circle = null;
let placeMap = null;
let placeMarker = null;
let loc = null;

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
    })
}

function initPlaceMap(){
    // Initialize the map, marker, and circle with the default values
    placeMap = new google.maps.Map(document.getElementById("placeMap"), {
        center: loc,
        zoom:15
    });
    marker = new google.maps.Marker({
        position: loc,
        animation: google.maps.Animation.DROP,
        map: placeMap
    });
}

function searchLocations(){
    stringLoc = loc.lat + ',' + loc.lng;
    // Call Google Map's Nearby Places API to get a list of nearby restaurants. Do initial call through PHP rather than through JS directly to protect API key
    $.get('api/nearbysearch.php', 'location=' + stringLoc + '&radius=' + distance, function(data){
        console.log(data);
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

                // Pick a random place from the list of places
                let place = places[Math.floor(Math.random() * 20)];
                $('#message').html('Recommended Place:');
                $('#result').attr('class', '');
                console.log(place);
            })
        }
        else
        {
            $('#message').html('ERROR: ' + data.status);
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

    $('#priceSlider').slider({
        range: true,
        min: 0,
        max: 4,
        step: 1,
        values: [0, 4],
        change: function(event, ui){
            min = ui.values[0];
            max = ui.values[1];
        }
    })

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
        searchLocations();
        return false;
    })
});