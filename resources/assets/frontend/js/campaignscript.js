var map;

function initMap() {
    // var myLatLng =  new google.maps.LatLng(-33.8665, 151.1956);
    var myLatLng = { lat: 16.066787, lng: 108.213882 };

    map = new google.maps.Map(document.getElementById('map'), {
        center: myLatLng,
        zoom: 14,
        scrollwheel: false//set to true to enable mouse scrolling while inside the map area
    });

    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
    });
}
