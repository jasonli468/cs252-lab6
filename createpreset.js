let minPrice = 1;
let maxPrice = 4;
let distance = 0;
let searchMap = null;
let searchMarker = null;
let circle = null;
let loc = null;
let floatCoords = true;
let processing = false;

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
        if(!processing)
        {
            if($('#name').val())
            {
                let invalidNameRegex = /[\[\]!\s*'\(\)]/;
                if(!invalidNameRegex.test($('#name').val()))
                {
                    processing = true;    
                    $('#submit').val('Processing...');
                    $('#submit').prop('disabled', true);

                    let lat = floatCoords ? loc.lat : loc.lat();
                    let lng = floatCoords ? loc.lng : loc.lng();
                    $.post('api/addpreset.php', 'lat=' + lat + '&lng=' + lng + '&radius=' + distance + '&minPrice=' + minPrice + '&maxPrice=' + maxPrice + '&open=' + 
                        $('#open').prop('checked') + '&name=' + encodeURIComponent($('#name').val()), function(data){
                        if(data.status === "Success")
                        {
                            window.location.href = "presets.php";
                        }
                        else
                        {
                            $('#submit').val('ERROR: Name already being used');
                            $('#submit').prop('disabled', false);
                            processing = false;
                        }
                    })
                }
                else
                    $('#submit').val("Name can't contain spaces or any of the following characters: [ ] ! * \ ( )");
            }
            else
            {
                $('#submit').val('ERROR: Name cannot be empty');
            }
        }
        return false;
    })
})