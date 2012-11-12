// The querying to Google.com is slow down so marker will takes awhile. Wait for a message of completion
var addresses = [];
var geoIndex;
var marker;
var markers = [];
var geoTimer;
var geocoder;
var map;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var currentPosition;
var bounds = new google.maps.LatLngBounds();

$("document").ready(function() {
	$.getJSON("results.json", function(data) {
		processData(data);
	});
});

function processData(data) {
	$.each(data, function(i, item) {
		createCell(i, item);
	});

	$("div#c0").parent().get(0).scrollIntoView();
		
	for (var i = 0; i < addresses.length; i++) {
		var infowindow;
		$("div#c"+i).hover(function() {				
				$(this).css({"background-color": "#29598E"});
				var tmp = $(this).attr("id");
				map.panTo(markers[tmp.substring(1)].getPosition());
				infowindow = new google.maps.InfoWindow({
					content: "<span width=20px height=20px>"+$.trim(markers[tmp.substring(1)].getTitle())+"</span><br /><span width=20px height=20px>"+$.trim(addresses[tmp.substring(1)])+"</span>"
				});

				infowindow.open(map, markers[tmp.substring(1)]);
				//alert(marker[tmp[1]].getTitle());
			}, function() {
				$(this).css({"background-color": "white"});
				infowindow.close();
			}
		);
	}

	showMap();
	// This is to slow down the querying to Google.com
	geoIndex = 0;
	geoTimer = setInterval(function(){ geocodeAddress();}, 1000);
	//alert("markers length: "+markers.length);
}

function createCell(i, item) {
	var cell = "<a id=\"a"+i+"\" href=\""+item.profile+"\" target =\"_blank\">";	
		cell += "<div class=\"entry\" id=\"c"+i+"\">";    
			cell += "<div class=\"traveltype_box\">";
				cell += "<p>";
					cell += "<span class=\"icon\">";
              					cell += "<img class=\"icon\" alt=\"Travel Status\" src=\""+item.traveltypeicon+"\"/>";
					cell += "</span><br />";
					cell += "<strong class=\"traveltext\">"+item.traveltype+"</strong>";
					cell += "<span class=\"price\">"+item.price+"/seat</span>";
				cell += "</p>";
			cell += "</div>";
			cell += "<div class=\"userpic\">";
				cell += "<img alt=\"Profile Picture\" src=\""+item.image+"\"/>";
				cell += "<span class=\"passenger\"></span>";
			cell += "</div>";
			cell += "<div class=\"inner_content \">";
				cell += "<h3>";
					cell += "<span class=\"inner\"> "+item.from;
						cell += " <span class=\"travel_type\">&rarr;</span> "+item.to;
					cell += " </span>";
				cell += "</h3>";
			cell += "</div>";
			cell += "<h4 class=\"name\">"+i+" - "+item.name+"</h4>";
		cell += "</div>";
	cell += "</a>";

	$("#results").append(cell);

	addresses.push(item.from);
	//alert("address length: "+addresses.length);
	//alert("cell length: "+cell.length);
}

function showMap() {
	directionsDisplay = new google.maps.DirectionsRenderer();
	var sj = new google.maps.LatLng(37.3041, -121.8727);		
	geocoder = new google.maps.Geocoder();
	
	var mapOptions = {
		zoom: 8,
		center: sj,
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
	//types: ['establishment']  // Comment out if we need to get both bussiness and address
	};
	autocomplete = new google.maps.places.Autocomplete(input, autoOptions);
}

function codeAddress(index) {
	geocoder.geocode( {'address': addresses[index]}, function(results, status) {
		processGeocode(results, status, index);
	});
}

function processGeocode(results, status, index) {
	if (status == google.maps.GeocoderStatus.OK) {
		var place = results[0].geometry.location;

      		marker = new google.maps.Marker({
			map: map,
			position: results[0].geometry.location,
			title: index.toString()
		});

		markers.push(marker);

		google.maps.event.addListener(marker, "click", function() {
			map.setZoom((map.getZoom() + 2));
			map.setCenter(marker.getPosition());
		});
		
		google.maps.event.addListener(marker, 'mouseover', function() {
			//var cell = document.getElementById("c"+index);
			// Scrolling to the selected cell
			$("div#c"+index).parent().get(0).scrollIntoView();
			$("div#c"+index).css({"background-color": "#29598E"});
			$("div#results").scrollTop($("div#c"+index).parent().offset().top - $("div#results").offset().top + $("div#results").scrollTop() - 243);
			//alert($("div#c"+index).parent().offset().top+", "+$("div#results").offset().top+", "+$("div#results").scrollTop());
		});

		google.maps.event.addListener(marker, 'mouseout', function() {
			//var cell = document.getElementById("c"+index);
			$("div#c"+index).css({"background-color": "white"});
		});

		// Extending the bounds object with each LatLng 
       		bounds.extend(place); 

           	// Adjusting the map to new bounding box 
       		map.fitBounds(bounds);
	}
	else {
		alert("Geocode was not successful for the following reason: " + status);
	}
}

function geocodeAddress() {
	for (var k = 0; k < 1 && geoIndex < addresses.length; ++k) {
		codeAddress(geoIndex);
		geoIndex++;
	}

	if (geoIndex >= addresses.length) {
		clearInterval(geoTimer);
		//alert(markers.length+", "+addresses.length);
		//var markerCluster = new MarkerClusterer(map, markers);
		alert("Done placing markers");
	}
}
