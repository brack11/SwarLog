<!DOCTYPE html>
<html>
  <head>
    <title>Navigation functions (heading)</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
      #panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=geometry"></script>
    <script>
var poly;
var geodesicPoly;
var marker1;
var marker2;
var lat1 = {{ Conf::withUser(Sentry::getUser()->id)->withName('uLat')->first()->value }};
var lon1 = {{ Conf::withUser(Sentry::getUser()->id)->withName('uLon')->first()->value }};
var lat2 = {{ $lat }};
var lon2 = {{ $lon }};

function initialize() {
  var mapOptions = {
    zoom: 3,
    center: new google.maps.LatLng((lat1+Math.abs(lat2))/2, (lon1+Math.abs(lon2))/2)
  };

  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  map.controls[google.maps.ControlPosition.TOP_CENTER].push(
      document.getElementById('info'));

  marker1 = new google.maps.Marker({
    map: map,
    position: new google.maps.LatLng(lat1, lon1)
  });

  marker2 = new google.maps.Marker({
    map: map,
    position: new google.maps.LatLng(lat2,lon2)
  });

  // var bounds = new google.maps.LatLngBounds(marker2.getPosition(),
  //     marker1.getPosition());
  // map.fitBounds(bounds);
  var bounds = new google.maps.LatLngBounds();
  bounds.extend(marker1.getPosition());
  bounds.extend(marker2.getPosition());
  map.fitBounds(bounds);


  var geodesicOptions = {
    // path: [marker1.getPosition(), marker2.getPosition()],
    strokeColor: '#CC0099',
    strokeOpacity: 1.0,
    strokeWeight: 3,
    geodesic: true,
    map: map
  };
  geodesicPoly = new google.maps.Polyline(geodesicOptions);

  update();
}

function update() {
  var path = [marker1.getPosition(), marker2.getPosition()];
  geodesicPoly.setPath(path);
  var heading = google.maps.geometry.spherical.computeHeading(path[0],
      path[1]);
  var distance = google.maps.geometry.spherical.computeDistanceBetween(path[0],
      path[1]);
  document.getElementById('distance').value = (distance/1000).toFixed(2);
  document.getElementById('heading').value = heading.toFixed(2);
  // document.getElementById('destination').value = path[1].toString();
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
    <div id="panel" style="margin-left:-270px">
      Heading: <input type="text" readonly id="heading">
      Distance: <input type="text" readonly id="distance"> km
    </div>
  </body>
</html>