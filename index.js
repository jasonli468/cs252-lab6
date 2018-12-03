let places;

function geolocationPosition(position)
{
    searchLocations(position.coords.latitude + ',' + position.coords.longitude);
}
function ipSearch()
{
    $.get('https://ipinfo.io/' + $('#ip').val() + '/json', '', function(position){
        searchLocations(position.loc);
    })
}

function searchLocations(location)
{
    $.get('api/nearbysearch.php', 'location=' + location + '&radius=' + '800', function(data){
        if(data.status === "OK")
        {
            places = data.status[Results];
            let place = places[Math.floor(Math.random() * 20)];
        }
        else
        {

        }
    })
}

$(document).ready()
{
    if(navigator.geolocation)
    {
        navigator.geolocation.getCurrentPosition(geolocationPosition, ipSearch);
    }
    else
    {
        ipSearch();
    }
}