
// google map
var infoWindow = new google.maps.InfoWindow();
var geocoder =  new google.maps.Geocoder();
var map;

function mw_google_map(mapid, addr)
{
    map = new google.maps.Map(document.getElementById(mapid), {
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    geocoder.geocode( {'address': addr }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            popInfoWindow(map.getCenter(), addr);

        }
    });

    google.maps.event.addListener(map, 'click', function(event) {
        var locate = event.latLng;
        geocoder.geocode( {'latLng': locate}, function(results, status) {
            str = '';
            /*for (i=0; i<results.length; i++) {
                str += results[i].formatted_address+"<br>";
            }*/
            str = results[1].formatted_address+"<br>";
            popInfoWindow(locate, str);
        });
    });
}

function popInfoWindow(latLng, content)
{
    infoWindow.setContent(content);
    infoWindow.setPosition(latLng);
    infoWindow.open(map);
}


