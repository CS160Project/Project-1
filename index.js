var lOriginLocation;
var lOriginLocationName;
var lDestinationLocation;
var lDestinationLocationName;

// initialize
// Input: None
// Output: None
// The intialize function that will be called after intial script loading complete
// The function will create autocomplete objects to help user to input locations
// The objects will attch listeners, so when user click on one of autocomplete opinions
// The latitude and longitude for the location will be filled into the hidden field
function initialize() {
	// Setting up Calendar for Date field
	myCalendar = new dhtmlXCalendarObject(["date"]);
	myCalendar.hideTime();
	var d = new Date();
	var cur_date = d.getDate();
	var cur_month = d.getMonth() + 1;
	var cur_year = d.getFullYear();
	var date = cur_year+"-"+cur_month+"-"+cur_date;
	//alert(cur_month);
	//myCalendar.setDate(date);
	myCalendar.setSensitiveRange(date, null);

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
		    // Retrieve the location based on the current autcomplete
		    var lPlace = lOriginAutocomplete.getPlace();
		    // If the returned place is not on the map, stop performing the process
		    if (!lPlace.geometry) {
		        lOriginInput.className = 'not found';
		        return;
		    }  // if
		    // If the location is valid, set the longitude and latitude to the hidden fields
		    var lLocation = checkSingleAddressIsInUSA(lPlace, "origin");
		    if (lLocation != null) 
            		{
		        lOriginLocation = lLocation.geometry.location;
		        
		        document.getElementById('originLatitudeTextField').value = lOriginLocation.lat();
		        document.getElementById('originLongitudeTextField').value = lOriginLocation.lng();
		    	}  // if
		}  // lambda function
                );     // google.maps.event.addListener

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
			// Retrieve the location based on the current autcomplete
			var lPlace = lDestinationAutocomplete.getPlace();
			// If the returned place is not on the map, stop performing the process
			if (!lPlace.geometry) {
				lDestinationInput.className = 'not found';
				return;
			}  // if
	    // If the location is valid, set the longitude and latitude to the hidden fields
            var lLocation = checkSingleAddressIsInUSA(lPlace, "destination");
		    if (lLocation != null) 
            {
			lDestinationLocation = lLocation.geometry.location;

			document.getElementById('destinationLatitudeTextField').value = lDestinationLocation.lat();
			document.getElementById('destinationLongitudeTextField').value = lDestinationLocation.lng();
            }  // if
          }  // lambda function
       );  // google.maps.event.addListener
}  // function initialize()

// Set the google map event listener to start to active once the load function completes and call initialize function
google.maps.event.addDomListener(window, 'load', initialize);

// validateInput
// Input: None
// Output: True if all input is valid, false if not
// Check if the input is correct before sending 
function validateInput()
{
    var lCorrect = true;
    $("#message_tag").empty();
    if (document.getElementById('originLatitudeTextField').value == "")
    {    
        lCorrect = false;  
        if (lOriginLocation != null) {
            if(document.getElementById('originTextField').value != '' && lOriginLocationName.toLowerCase() == document.getElementById('originTextField').value.toLowerCase())
            {
                    setOriginInput();
                    lCorrect = true;
            }  // if
            else{
                $("#message_tag").append('<p>Invalid input for original location!</p>');
                $("#message_tag").append('<p>Do you mean <a href ="#" onClick="setOriginInput()">' + lOriginLocationName + '</a>?</p>');
            }  // else
        } // if
        else {
                $("#message_tag").append('<p>Invalid input for original location! Please try again.</p>');
        }  // else
    }  // if

    if(document.getElementById('destinationLatitudeTextField').value  == "")
    {
        lCorrect = false;
        if (lDestinationLocation != null) {
            if(document.getElementById('destinationTextField').value != '' && lDestinationLocationName.toLowerCase() == document.getElementById('destinationTextField').value.toLowerCase())
            {
                setDestinationInput();
                lCorrect = true;
            }  // if
            else{
                    $("#message_tag").append('<p>Invalid input for destination location!</p>');
                    $("#message_tag").append('<p>Do you mean <a href="#" onClick="setDestinationInput()">' + lDestinationLocationName + '</a>?</p>');
            }  // else
        } // if
        else {
            $("#message_tag").append('<p>Invalid input for destination location! Please try again.</p>');
        }
    }  // if

    return lCorrect;
}  // function validateInput()

// setOriginInput
// Input: None
// Output: None
// Find possible origin location according to user input
function onChangeOriginInput()
{ 
    document.getElementById('originLatitudeTextField').value = "";
    document.getElementById('originLongitudeTextField').value = "";
    var lGeocoder = new google.maps.Geocoder();
    var lAddress = document.getElementById('originTextField').value;
    lGeocoder.geocode({ 'address': lAddress },
    function (pResults, pStatus) {
        if (pStatus == google.maps.GeocoderStatus.OK) {
            var lResult = checkAddressIsInUSA(pResults, "origin");
            if (lResult != null) {
                lOriginLocation = lResult.geometry.location;
            } // if
        } // if
    }  // lambda function
    );
}  // function onChangeOriginInput

// setDestinationInput
// Input: None
// Output: None
// Find possible destination location according to user input
function onChangeDestinationInput()
{
    document.getElementById('destinationLatitudeTextField').value = "";
    document.getElementById('destinationLongitudeTextField').value = "";
        var lGeocoder = new google.maps.Geocoder();
        var lAddress = document.getElementById('destinationTextField').value;
        lGeocoder.geocode({ 'address': lAddress },
        function (pResults, pStatus) {
            if (pStatus == google.maps.GeocoderStatus.OK) {
                var lResult = checkAddressIsInUSA(pResults, "destination");
                if (lResult != null) {
                    lDestinationLocation = lResult.geometry.location;
                } // if
            } // if
        }  // lambda function
        );
}  // function onChangeDestinationInput

// setOriginInput
// Input: None
// Output: None
// Set the original input fields to the suggestion location
function setOriginInput()
{
    document.getElementById('originTextField').value = lOriginLocationName;
    document.getElementById('originLatitudeTextField').value = lOriginLocation.lat();
    document.getElementById('originLongitudeTextField').value = lOriginLocation.lng();

    validateInput();
}  // function setOriginInput

// setDestinationInput
// Input: None
// Output: None
// Set the destination input fields to the suggestion location
function setDestinationInput()
{ 
    document.getElementById('destinationTextField').value = lDestinationLocationName;
    document.getElementById('destinationLatitudeTextField').value = lDestinationLocation.lat();
    document.getElementById('destinationLongitudeTextField').value = lDestinationLocation.lng();

    validateInput();
}  // function setDestinationInput

// checkAddressIsInUSA
// Input: pResults Inputted GeocoderResults
// Output: None
// The function will loop through all results in inputted GeocoderResults
// It will return the first result that is in US and has a city component
// or null if there is no match
function checkAddressIsInUSA(pResults, type) {
    var lValidResult = null;
    
    $.each(pResults, function (pResultKey, pResultValue) {
        lValidResult = checkSingleAddressIsInUSA(pResultValue, type);

	if (lValidResult != null) {
            return;
        } // if
    });  // each

    return lValidResult;
}  // function checkAddressIsInUSA

// checkSingleAddressIsInUSA
// Input: pResults Inputted GeocoderResult
// Output: None
// The function will check the GeocoderResult for correct country and city
// It will return the result if it is in US and has a city component
// or null if there is no match
function checkSingleAddressIsInUSA(pResult, type)
{ 
    var city;
    var state;
    var lValidResult = null;
    var lCountry = false;
    var lCity = false;
    var lState = false;
      $.each(pResult.address_components, function (pComponentKey, pComponentValue) {
            $.each(pComponentValue.types, function (pTypeKey, pTypeValue) {
                if (pTypeValue == 'country') {
                    if (pComponentValue.short_name == 'US') {
                        lCountry = true;
                        if (lCity == true && lState == true) {
                            return;
                        }  // if
                    }  // if
                }  // if
                if (pTypeValue == 'locality') {
                    lCity = true;
                    city = pComponentValue.long_name;
		    if (lCountry == true && lState == true) {
                        return;
                    }  // if
                }  // if
		if (pTypeValue == 'administrative_area_level_1') {
                    lState = true;
		    state = pComponentValue.short_name;
		    if (lCity == true && lCountry == true) {
                            return; 
                    }  // if
                }  // if
            });  // each

            if (lCountry == true && lCity == true && lState == true) {
                //alert(city + ", " + state);
		if (type == "origin") {
			lOriginLocationName = city + ", " + state;
		}
		else {
			lDestinationLocationName = city + ", " + state;
		}

		lValidResult = pResult;                
		return;
            }  // if
    });  // each
    
    return lValidResult;
}  // checkSingleAddressIsInUSA
