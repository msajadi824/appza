function InitMapSelectDragAddress(canvas_id, lat_id, lng_id) {
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

    google.maps.event.addListener(marker, 'dragend', function (event) {
        document.getElementById(lat_id).value = event.latLng.lat();
        document.getElementById(lng_id).value = event.latLng.lng();
    });

    this.marker = marker;
    this.map = map;
}