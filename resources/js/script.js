function initMap() {
    var options = {
        zoom: 8,
        center: {lat: 40.4, lng: -74.5}     
    }
	window.alert("TESTING");
    var map = new google.maps.Map(document.getElementById('map'),options);
	var lat, lon;
	
	var geocoder = new google.maps.Geocoder();

	//var address = document.getElementById("address").value;
	geocoder.geocode( { 'address': "Bregen County, NJ"}, function(results, status) {
	  if (status == google.maps.GeocoderStatus.OK)
	  {
		lat = results[0].geometry.location.lat()
		lon = results[0].geometry.location.lng()
	  }
	});
    
    // Create and store Marker values
    var markers = [
        {lat:lat, lng: lon}
    ];
    
    //loop through markers
    for(var i = 0; i < markers.length; i++) {
        addMarker(markers[i]);
    }

    //add markers
    function addMarker(coords){
        var marker = new google.maps.Marker({
        position:coords,
        map:map,
        });
    } 
}