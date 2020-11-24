function InitMapViewAddress(canvas_id, points, function_click, marker_image) {
    var map = new google.maps.Map(document.getElementById(canvas_id), {
        zoom: 12,
        center: {lat: 35.6892, lng: 51.3890},
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        scaleControl: true,
        mapTypeId: 'roadmap'
    });

    var bounds = new google.maps.LatLngBounds();

    var markers = {};

    points.forEach(function(point) {
        try {
            var shape = {
                coords: [8,0,5,1,4,2,3,3,2,4,2,5,1,6,1,7,0,8,0,14,1,15,1,16,2,17,2,18,3,19,3,20,4,21,5,22,5,23,6,24,7,25,7,27,8,28,8,29,9,30,9,33,10,34,10,40,11,40,11,34,12,33,12,30,13,29,13,28,14,27,14,25,15,24,16,23,16,22,17,21,18,20,18,19,19,18,19,17,20,16,20,15,21,14,21,8,20,7,20,6,19,5,19,4,18,3,17,2,16,1,14,1,13,0,8,0],
                type: 'poly'
            };
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(point.lat, point.lng),
                map: map,
                icon: point.distance == null ? marker_image : null,
                shape: shape
            });
            var infoWindow = new google.maps.InfoWindow({
                content: point.address
            });
            marker.addListener('click', function () {
                if (typeof(point.distance) == typeof(marker_image)) {
                    Object.keys(markers).forEach(function (marker_id) {
                        var marker = markers[marker_id];
                        marker.setAnimation(null);
                    });
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    if(function_click) function_click(marker);
                }
                infoWindow.open(map, marker);
            });
            bounds.extend(marker.getPosition());

            marker.infoWindow = infoWindow;
            marker.id = point.id;
            marker.data = point;
            markers[point.id] = marker;
        }catch(e){}
    });

    if (Object.keys(markers).length > 0) {
        map.setCenter(bounds.getCenter());
        map.fitBounds(bounds);
    }

    this.selectMarker = function (id) {
        Object.keys(markers).forEach(function (marker_id) {
            var marker = markers[marker_id];
            marker.setAnimation(null);
        });

        var marker = markers[id];
        marker.setAnimation(google.maps.Animation.BOUNCE);
        marker.map.setCenter(marker.getPosition());
    };

    this.markers = markers;
    this.map = map;
}