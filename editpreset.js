let minPrice = 1;
let maxPrice = 4;
let distance = 0;
let searchMap = null;
let searchMarker = null;
let circle = null;
let loc = null;
let floatCoords = true;
let processing = false;

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
    loc = {lat: parseFloat($('#lat').val()), lng: parseFloat($('#lng').val())}
    initSearchMap();
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

    // Generate the price slider
    $('#priceSlider').slider({
        range: true,
        min: 1,
        max: 4,
        step: 1,
        values: [parseInt($('#minPrice').val()), parseInt($('#maxPrice').val())],
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
            processing = true;    
            $('#submit').val('Processing...');
            $('#submit').prop('disabled', true);

            let lat = floatCoords ? loc.lat : loc.lat();
            let lng = floatCoords ? loc.lng : loc.lng();
            $.post('api/editpreset.php', 'lat=' + lat + '&lng=' + lng + '&radius=' + distance + '&minPrice=' + minPrice + '&maxPrice=' + maxPrice + '&open=' + 
                $('#open').prop('checked') + '&name=' + encodeURIComponent($('#name').val()), function(data){
                if(data.status === "Success")
                {
                    window.location.href = "presets.php";
                }
                else
                {
                    $('#submit').val('ERROR: Preset already set to this');
                    $('#submit').prop('disabled', false);
                    processing = false;
                }
            })
        }
        return false;
    })
})