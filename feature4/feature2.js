// initialize
// Input: None
// Output: None
// The intialize function that will be called after intial script loading complete
// The function will create autocomplete objects to help user to input locations
// The objects will attch listeners, so when user click on one of autocomplete opinions
// The latitude and longitude for the location will be filled into the hidden field
function initialize() {
	// Create object reference to the input origin location
	var lOriginInput = document.getElementById('originTextField');
	// Create the autocomplete for the input origin location
	var lOriginAutocomplete = new google.maps.places.Autocomplete(lOriginInput);

	// Add listener to the autcomplete
	// Once the user click on one of autocomplete opinions, the latitude and longitude
	// for the location will be filled into the hidden field 
	google.maps.event.addListener(lOriginAutocomplete, 'place_changed', 
		function () {
			// Set the origin input as none
			lOriginInput.className = '';
			// Retrive the location based on the current autcomplete
			var lPlace = lOriginAutocomplete.getPlace();
			// If the returned place is not on the map, stop performing the process
			if (!lPlace.geometry) {
				lOriginInput.className = 'not found';
				return;
			}  // if
			// If the location is valid, set the longitude and latitude to the hidden fields
			var lLocation = lPlace.geometry.location;
			document.getElementById('originLatitudeTextField').value = lLocation.lat();
			document.getElementById('originLongitudeTextField').value = lLocation.lng();
                }  // lambda function
                );  // google.maps.event.addListener

                // Create object reference to the input destination location
                var lDestinationInput = document.getElementById('destinationTextField');
                var lDestinationAutocomplete = new google.maps.places.Autocomplete(lDestinationInput);

                // Add listener to the autcomplete
                // Once the user click on one of autocomplete opinions, the latitude and longitude
                // for the location will be filled into the hidden field 
                google.maps.event.addListener(lDestinationAutocomplete, 'place_changed',
                function () {
			// Set the destination input as none
			lDestinationInput.className = '';
			// Retrive the location based on the current autcomplete
			var lPlace = lDestinationAutocomplete.getPlace();
			// If the returned place is not on the map, stop performing the process
			if (!lPlace.geometry) {
				lDestinationInput.className = 'not found';
				return;
			}  // if
			// If the location is valid, set the longitude and latitude to the hidden fields
			var lLocation = lPlace.geometry.location;
			document.getElementById('destinationLatitudeTextField').value = lLocation.lat();
			document.getElementById('destinationLongitudeTextField').value = lLocation.lng();
                }  // lambda function
                );  // google.maps.event.addListener
}  // function initialize()

// Set the google map event listener to start to active once the load function completes and call initialize function
google.maps.event.addDomListener(window, 'load', initialize);
