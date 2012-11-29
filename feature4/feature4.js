var geocoder;
var map;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var marker;
var currentPosition;

window.onload = function() {
	directionsDisplay = new google.maps.DirectionsRenderer();
	var chicago = new google.maps.LatLng(41.850033, -87.6500523);		
	geocoder = new google.maps.Geocoder();
	
	var mapOptions = {
		zoom: 8,
		center: chicago,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

	directionsDisplay.setMap(map);
		
	if(navigator.geolocation) {
		// timeout at 60000 milliseconds (60 seconds)
		var options = {timeout:60000};
		navigator.geolocation.getCurrentPosition(function (position) {
			currentPosition = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			map.setCenter(currentPosition);

			/*google.maps.event.addListener(map, "center_changed", function() {
			// 3 seconds after the center of the map has changed, pan back to the marker.
				window.setTimeout(function() {
				map.panTo(marker.getPosition());
				}, 3000);
			});*/
		}, 
		function (err) {
			if(err.code == 1) {
				alert("Error: Access is denied!");
			}
			else if(err.code == 2) {
				alert("Error: Position is unavailable!");
			}
			else {
				alert("Error: Unknown");
			}
		},options);
	}
	else {
		alert("Sorry, browser does not support geolocation!");
	}

	var input = document.getElementById("address");
	var autoOptions = {
	bounds: currentPosition
	//types: ['establishment']  dont mention it we need to get both bussiness andaddress
	};
	autocomplete = new google.maps.places.Autocomplete(input, autoOptions);
};

function codeAddress() {
	var address = document.getElementById("address").value;

	geocoder.geocode( {'address': address}, function(results, status) {
		//alert(address);
		if (status == google.maps.GeocoderStatus.OK) {
			//map.setCenter(results[0].geometry.location);

      		  	marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location,
				title: "Click to zoom"
			});

			google.maps.event.addListener(marker, "click", function() {
				map.setZoom((map.getZoom() + 2));
				map.setCenter(marker.getPosition());
			});
		}
		else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
}

function codeLatLng() {
	var input = document.getElementById("latlng").value;
	var latlngStr = input.split(",",2);
	var lat = parseFloat(latlngStr[0]);
	var lng = parseFloat(latlngStr[1]);
	var latlng = new google.maps.LatLng(lat, lng);

	geocoder.geocode({'latLng': latlng}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[1]) {
				map.setZoom(11);

				marker = new google.maps.Marker({
				position: latlng,
				map: map,
				title: "Click to zoom"
				});
				
				google.maps.event.addListener(marker, "click", function() {
					map.setZoom((map.getZoom() + 2));
					map.setCenter(marker.getPosition());
				});
			
				infowindow.setContent(results[1].formatted_address);
				infowindow.open(map, marker);
			}
		}
		else {
			alert("Geocoder failed due to: " + status);
		}
	});
}

function calcRoute() {
	var start = document.getElementById("start").value;
	var end = document.getElementById("end").value;

	var request = {
		origin:start,
		destination:end,
		travelMode: google.maps.DirectionsTravelMode.DRIVING
	};

	directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
		}
		else {
			alert("Route direction failed");
		}
	});
}