function InitMapSelectClickAddress(canvas_id, lat_id, lng_id, function_change) {
    var center = {
        lat: parseFloat(document.getElementById(lat_id).value ? document.getElementById(lat_id).value : 35.6892),
        lng: parseFloat(document.getElementById(lng_id).value ? document.getElementById(lng_id).value : 51.3890)
    };

    var map = new google.maps.Map(document.getElementById(canvas_id), {
        zoom: 12,
        center: center,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        scaleControl: true,
        mapTypeId: 'roadmap'
    });

    var marker = new google.maps.Marker({
        position: center,
        map: map,
        draggable: true
    });

    google.maps.event.addListener(map, 'click', function (event) {
        marker.setPosition(event.latLng);
        document.getElementById(lat_id).value = event.latLng.lat();
        document.getElementById(lng_id).value = event.latLng.lng();
        if(function_change) function_change(event);
    });
    google.maps.event.addListener(marker, 'dragend', function (event) {
        google.maps.event.trigger(map, 'click', event);
    });

    this.marker = marker;
    this.map = map;
}