// The querying to Google.com is slow down so marker will takes awhile. Wait for a message of completion
var addressesFrom = [];
var addressesTo = [];
var profileName = [];
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
var myCalendar;

$("document").ready(function() {
	// Setting up Calendar for Date field
	myCalendar = new dhtmlXCalendarObject(["date"]);
	myCalendar.hideTime();
	var d = new Date();
	var cur_date = d.getDate();
	var cur_month = d.getMonth() + 1;
	var cur_year = d.getFullYear();
	var date = cur_year+"-"+cur_month+"-"+cur_date;
	myCalendar.setSensitiveRange(date, null);

	// Setting up loading message
	$("#mainframe").attr("style", "position:relative; visibility: hidden");
	$("#loading").empty();
	$("#loading").append('<span>Loading results and map ...</span>');
	
	// Obtaining and processing data from json file
	$.getJSON("results.json", function(data) {
		// Checking for empty data file
		if (data.length > 0) {
			processData(data);
			showMap();
		}
		else {
			$("#results").empty();
			currentPosition = new google.maps.LatLng(37.3041, -121.8727);
	
			var mapOptions = {
				zoom: 8,
			center: currentPosition,
			mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
			
			$("#loading").empty();
			$("#loading").append("<span>There was " + addressesFrom.length + " results</span>");
			$("#mainframe").attr("style", "position:relative; visibility: visible") 
		}
	});
});

// Processing the json data
function processData(data) {
	// Emptying the div in preparation
	$("#results").empty();

	// Iterating through each element of the data
	$.each(data, function(i, item) {
		createCell((i + 1), item);
	});

	// Scroll to top of list
	$("div#c1").parent().get(0).scrollIntoView();
	//var tmp = $("div#c1").attr("id").substring(1) - 1;
	//alert(tmp);
	// Creating hover mouse event for results
	for (var i = 0; i < addressesFrom.length; i++) {
		var infowindow;
		var index = i + 1;
		$("div#c"+index).hover(function() {
				$(this).css({"background-color": "#29598E"});
				var tmp = $(this).attr("id").substring(1) - 1;
				//alert(tmp);
				map.panTo(markers[tmp].getPosition());
				infowindow = new google.maps.InfoWindow({
					content: "<span width=20px height=20px>"+$.trim(markers[tmp].getTitle())+"</span><br /><span width=20px height=20px><strong>"+$.trim(profileName[tmp])+"</strong></span><br /><span width=20px height=20px>"+$.trim(addressesFrom[tmp])+"</span><span width=20px height=20px> to "+$.trim(addressesTo[tmp])+"</span>"
				});

				infowindow.open(map, markers[tmp]);
				//alert(marker[tmp[1]].getTitle());
			}, function() {
				$(this).css({"background-color": "white"});
				infowindow.close();
			}
		);
	}
}

// Creating individual results
function createCell(i, item) {
	var cell = "<a id=\"a"+i+"\" href=\""+item.profile+"\" target =\"_blank\">";
		cell += "<div class=\"result_box\" id=\"c"+i+"\">";
			cell += "<div class=\"headline\">";
				cell += "<strong class=\"source\">"+i+" - "+item.sourcesite+"</strong><br />";
				cell += "<span class=\"date\">"+item.departuredate+"</span>";
			cell += "</div>";
			cell += "<div class=\"entry\">";
				cell += "<div class=\"traveltype_box\">";
					cell += "<p>";
						cell += "<span class=\"icon\">";
        	      					cell += "<img class=\"icon\" alt=\"Travel Status\" src=\""+item.traveltypeicon+"\"/>";
						cell += "</span><br />";
						cell += "<strong class=\"traveltext\">"+item.traveltype+"</strong>";
						if (item.traveltype == "Driver") {
							cell += "<span class=\"price\">"+item.price+" "+item.currencytype+" per seat</span><br />";
							cell += "<span class=\"numberofseat\">"+item.seat+" seat remaining</span>";
						}
					cell += "</p>";
				cell += "</div>";
				cell += "<div class=\"userpic\">";
					cell += "<img class=\"profile\" alt=\"Profile Picture\" src=\""+item.image+"\"/>";
					cell += "<br /><strong class=\"name\">"+item.personname+"</strong>";
				cell += "</div>";
				cell += "<div class=\"inner_content \">";
					cell += "<h3>";
						cell += "<span class=\"inner\"> "+item.from;
							cell += " <span class=\"travel_type\">&rarr;</span> "+item.to;
						cell += "</span>";
					cell += "</h3>";
				cell += "</div>";
			cell += "</div>";
		cell += "</div>";
	cell += "</a>";

	$("#results").append(cell);

	// Storing addressesFrom, addressesTo and profileName for Markers and Infowindows
	if (item.from != "") {
		addressesFrom.push(item.from);
		addressesTo.push(item.to);
		profileName.push(item.personname);
	}
	else {
		addressesFrom.push("N/A");
		addressesTo.push("N/A");
		profileName.push("N/A");
	}
	//alert("address length: "+addressesFrom.length);
	//alert("cell length: "+cell.length);
}

// Displaying the Map
function showMap() {
	//directionsDisplay = new google.maps.DirectionsRenderer();
	currentPosition = new google.maps.LatLng(37.3041, -121.8727);
	geocoder = new google.maps.Geocoder();
	
	var mapOptions = {
		zoom: 8,
		center: currentPosition,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

	//directionsDisplay.setMap(map);

	// Current Location
	/*if(navigator.geolocation) {
		// timeout at 60000 milliseconds (60 seconds)
		var options = {timeout:60000};
		navigator.geolocation.getCurrentPosition(function (position) {
			currentPosition = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			map.setCenter(currentPosition);

			google.maps.event.addListener(map, "center_changed", function() {
			// 3 seconds after the center of the map has changed, pan back to the marker.
				window.setTimeout(function() {
				map.panTo(marker.getPosition());
				}, 3000);
			});
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
	}*/

	// This is to slow down the querying to Google.com
	geoIndex = 0;
	geoTimer = setInterval(function(){ geocodeAddress();}, 1100);
	//alert("markers length: "+markers.length);
}

// Use geoCode to get the location on the map to place the marker
function geocodeAddress() {
	// Processing addresses in one interval
	//It doesnt loop right now since it is set to 1 but in case we want to increase it later, the loop is left as it is
	var requestPerInterval = 1;
	for (var k = 0; k < requestPerInterval && geoIndex < addressesFrom.length; ++k) {
		codeAddress(geoIndex);
		geoIndex++;
	}

	if (geoIndex >= addressesFrom.length) {
		clearInterval(geoTimer);
		//alert(markers.length + ", " + addressesFrom.length);
		//var markerCluster = new MarkerClusterer(map, markers);

		// Showing map and result
		$("#loading").empty();
		$("#loading").append("<span>Done processing " + addressesFrom.length + " results</span>");
		$("#mainframe").attr("style", "position:relative; visibility: visible") 

		// Adjusting the map to new bounding box
		map.fitBounds(bounds);

		//alert("Done placing " + addressesFrom.length + " markers");
	}
}

// Processing addresses with Geocode
function codeAddress(index) {
	if (addressesFrom[index] != "N/A") {
		geocoder.geocode( {'address': addressesFrom[index]}, function(results, status) {
			processGeocode(results, status, (index + 1));
		});
	}
	else {
		markers.push(null);
	}
}

// Processing result from Geocode
function processGeocode(results, status, index) {
	if (status == google.maps.GeocoderStatus.OK) {
		var place = results[0].geometry.location;

      		marker = new google.maps.Marker({
			map: map,
			position: place,
			title: index.toString()
		});

		// Storing markers in an array
		markers.push(marker);

		// Extending the bounds object with each LatLng
       		bounds.extend(place);

           	// Adjusting the map to new bounding box
       		//map.fitBounds(bounds);

		// Click event for marker: Zoom in
		google.maps.event.addListener(marker, "click", function() {
			map.setZoom((map.getZoom() + 2));
			map.setCenter(marker.getPosition());
		});

		// Mouseover event for marker: Highlight result and scroll it into the middle of the result box
		google.maps.event.addListener(marker, 'mouseover', function() {
			// Scrolling to the selected cell
			$("div#c"+index).parent().get(0).scrollIntoView();
			$("div#c"+index).css({"background-color": "#29598E"});
			$("div#results").scrollTop($("div#c"+index).parent().offset().top - $("div#results").offset().top + $("div#results").scrollTop() - 243);
			//alert($("div#c"+index).parent().offset().top+", "+$("div#results").offset().top+", "+$("div#results").scrollTop());
		});

		// Mouseout event for marker: Remove highlight of result
		google.maps.event.addListener(marker, 'mouseout', function() {
			//var cell = document.getElementById("c"+index);
			$("div#c"+index).css({"background-color": "white"});
		});
	}
	else {
		alert("Geocode was not successful for the following reason: " + status);
	}
}

// Price Sort functions
jQuery.fn.sort = function() {
	return this.pushStack([].sort.apply(this, arguments), []);
};

function sortLocationAscending(pFObject, pSObject) {
    var city1 = pFObject.from.toUpperCase();
    var city2 = pSObject.from.toUpperCase();
    return (city1 < city2) ? -1 : (city1 > city2) ? 1 : 0;
}; // sortLocationAscending

function sortLocationDescending(pFObject, pSObject) {
    var city1 = pFObject.from.toUpperCase();
    var city2 = pSObject.from.toUpperCase();
    return (city1 > city2) ? -1 : (city1 < city2) ? 1 : 0;
}; // sortLocationDescending

function sortPriceDescending(pFObject, pSObject) {
	if (parseFloat(pFObject.price) == parseFloat(pSObject.price) || pFObject == pSObject) {
		return 0;
	}
	else if (pFObject.price == "N/A") {
		return 1;
	}
	else if (pSObject.price == "N/A") {
		return -1;
	}
	else {
		return parseFloat(pFObject.price) > parseFloat(pSObject.price) ? -1 : 1;
	}
};  // sortPriceDescending

function sortPriceAscending(pFObject, pSObject) {
	if(parseFloat(pFObject.price) == parseFloat(pSObject.price)  || pFObject == pSObject) {
		return 0;
	}
	else if (pFObject.price == "N/A") {
		return -1;
	}
	else if (pSObject.price == "N/A") {
		return 1;
	}
	else {
		return parseFloat(pFObject.price) > parseFloat(pSObject.price) ? 1 : -1;
	}
};  // sortPriceAscending

// Loading json data for sort filtering
function loadJSON() {
	$.getJSON("results.json", function(json) {filter(json);});
}  // loadJSON

function filter(pData) {
	var lJsonObject = pData;

	var lSortOption = document.getElementById("cmbSort").value;

	var lObject = $(lJsonObject).sort(sortPriceAscending);

	switch(lSortOption)
	{
		case "Price -- Ascending":
		lObject = $(lJsonObject).sort(sortPriceAscending);
		break;

		case "Price -- Descending":
		lObject = $(lJsonObject).sort(sortPriceDescending);
		break;

		case "Starting Location -- Ascending":
		lObject = $(lJsonObject).sort(sortLocationAscending);
		break;

		case "Starting Location -- Descending":
		lObject = $(lJsonObject).sort(sortLocationDescending);
		break;

		default:
		break;
	}  // switch

	processData(lObject);
	showMap();
}
