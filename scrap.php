<?php
// Scrapper for Zimride and Ridejoy
	include("simple_html_dom.php"); // Using simple_html_dom library

	// Check to see if location is a zipcode
	function isZipcode($str) {
		if (strlen($str) === 5) {
			for ($i = 0; $i < 5; $i++) {
				if (!(is_numeric($str[$i]))) {
					return FALSE;
				}
			}
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	// Check to see if location is 2-letters state
	function isState($str) {
		//echo strlen($str).$str."<br />";
		if (strlen($str) === 2) {
			//echo $str[$i];
			for ($i = 0; $i < 2; $i++) {
				if (!(ctype_alpha($str[$i]))) {
					return FALSE;
				}
				//echo $str[$i];
			}
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	// Getting current date
	date_default_timezone_set('America/Los_Angeles');

	$month = date("m");
	$day = date("d");
	$year = date("Y");

	$zrTravelStatus = $_POST['travelStatus']; //Zimride travel Status
	$rjTravelStatus = $zr_travelStatus; //Ridejoy travel Status
	
	if ($rjTravelStatus === "offer") {
		$rjTravelStatus = "ride_offer";
	}
	else if ($rjTravelStatus === "need") {
		$rjTravelStatus = "ride_request";
	}
	else {
		$rjTravelStatus = "";
	}

	$vehicleType = $_POST['vehicleType'];

	$cur_to = $_POST['currency_to'];

	$start = $_POST['originTextField'];
	$end = $_POST['destinationTextField'];
	
	//Retrive the text in the origin latitude and longitude field from the html page post events
	$lOriginLat = $_POST['originLatitudeTextField'];
	$lOriginLng = $_POST['originLongitudeTextField'];
	//Retrive the text in the destination latitude and longitude field from the html page post events
	$lDestinationLat = $_POST['destinationLatitudeTextField'];
	$lDestinationLng = $_POST['destinationLongitudeTextField'];

	if (isZipcode($start) && isZipcode($end)) {
		$zimride = file_get_html("http://www.zimride.com/search?s=$start&e=$end&date=$month%2F$day%2F$year&filter_type=$travelStatus&filter_vehicle=$vehicleType");
		$ridejoy = file_get_html("http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=$rjTravelStatus&origin=$start&origin_latitude=$lOriginLat&origin_longitude=$lOriginLng&destination=$end&destination_latitude=$lDestinationLat&destination_longitude=$lDestinationLng&date=$month%2F$day%2F$year");
	}
	else {
		// Splitting state and city
		$sterm = preg_split("/[,]+/", $start);
		$eterm = preg_split("/[,]+/", $end);
		
		if (sizeof($sterm) === 2 && sizeof($eterm) === 2) {
			$sstate = trim($sterm[1]);
			$estate = trim($eterm[1]);
			
			if (isState($sstate) && isState($estate)) {
				// Converting to Zimride search query format
				$loc1 = str_replace(' ', '+', trim($sterm[0]));
    				$loc2 = str_replace(' ', '+', trim($eterm[0]));
				
				$zimride = file_get_html("http://www.zimride.com/search?s=$loc1%2C+$sstate&e=$loc2%2C+$estate&date=$month%2F$day%2F$year&filter_type=$zrTravelStatus&filter_frequency=one-time&filter_privacy=public&filter_vehicle=$vehicleType");
				//$zimride = file_get_html("http://www.zimride.com/search?s=Los+Angeles%2C+CA&e=San+Diego%2C+CA&date=11%2F28%2F12&filter_type=&filter_frequency=one-time&filter_privacy=public&filter_vehicle=&program=&s_name=&s_full_text=&s_error_code=&s_address=&s_city=&s_state=&s_zip=&s_country=&s_lat=&s_lng=&s_location_key=&s_user_lat=&s_user_lng=&s_user_country=&e_name=San+Diego%2C+CA&e_full_text=San+Diego%2C+CA%2C+USA&e_error_code=&e_address=San+Diego%2C+CA%2C+USA&e_city=San+Diego&e_state=CA&e_zip=&e_country=US&e_lat=32.7153292&e_lng=-117.15725509999999&e_location_key=&e_user_lat=&e_user_lng=&e_user_country=");
				$ridejoy = file_get_html("http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=$rjTravelStatus&origin=$loc1%2C+$sstate&origin_latitude=$lOriginLat&origin_longitude=$lOriginLng&destination=$loc2%2C+$estate&destination_latitude=$lDestinationLat&destination_longitude=$lDestinationLng");
				//$ridejoy = file_get_html("http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=$rjTravelStatus&origin=$loc1%2C+$sstate&origin_latitude=$lOriginLat&origin_longitude=$lOriginLng&destination=$loc2%2C+$estate&destination_latitude=$lDestinationLat&destination_longitude=$lDestinationLng&date=$month%2F$day%2F$year");
				}
			else {
				$tmp1 = $sword2[1];
				$tmp2 = $eword2[1];
				echo "<script type=\"text/javascript\">window.alert(\"Not a valid 2-letter State abbreviation: $tmp1 and $tmp2\")</script>";;
			}
		}
	}
	
	// Getting the total results from Zimride and Ridejoy (testing purposes)
	$temp1 = $zimride->find('span[class=showing]');
	$temp2 = $ridejoy->find('div[class=search_results_explanation]');
	
	$str = $temp1[0]->childNodes(0)->text()." and ".$temp2[0]->childNodes(0)->text();

	echo "<script type=\"text/javascript\">window.alert(\"$str\")</script>";

	// Multiple result pages scrapping method (not complete)
	/*$zrTag = $zimride->find('span[class=current_page]');
	$zrCurrentTag = $tag[0];

	$zrPageNum = 1;
	do {
		if ($zrCurrentTag->tag == "a") {
			$zrPageNum++;
			//echo $pagelink."<br />";
			$zimride = file_get_html("http://www.zimride.com/search?pageID=$zrPageNum");
	
			$zrTag = $zimride->find('span[class=current_page]');
			$zrCurrentTag = $zrTag[0];
		}*/
		
		// Process Zimride
		foreach ($zimride->find('div[class^=entry]') as $e) {
			$main1 = $e->find('div[class=username]',0)->text();

			$main2 = $e->find('img[alt="Profile Picture"]',0)->getAttribute('src');

			$class = $e->find('span[class=inner]',0)->childNodes(0)->getAttribute('class');

			if ($class == "trip_type round_trip") {
				$main11 = "Round trip";
			}
			else if ($class == "trip_type one_way") {
				$main11 = "One Way";
			}
			else {
				$main11 = "NA";
				echo "<script type=\"text/javascript\">window.alert(\"Error: no trip type\")</script>";
			}
			
			$temp = $e->find('span[class=inner]',0)->innertext;

			$regex = '/(.+?)<span/';
			if (preg_match($regex,$temp,$match)) {
				$main3 = trim($match[1]);
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Failed\")</script>";
			}
		
			$regex = '/span>(.*)/';
			if (preg_match($regex,$temp,$match)) {
				$main4 = trim($match[1]);
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Failed\")</script>";
			}

			$temp = $e->find('h4',0)->innertext;
			$str = explode ("&nbsp; / &nbsp;", $temp);
			$main5 = trim($str[0]);
			for ($i = 1; $i < sizeof($str); $i++) {
				$main5 = $main5." / ".trim($str[$i]);
			}

			$main6 = $e->parent()->getAttribute('href');

			if ($e->parent()->prev_sibling()->tag === "h3") {
				$main7 = $e->parent()->prev_sibling()->innertext;
			}
			
			if ($e->childNodes(0)->getAttribute('class') === "price_box") {
				$main8 = substr(trim($e->find('b',0)->text()), 1);

				if ($cur_to !== "USD") {
					$currency = file_get_html("http://www.gocurrency.com/v2/dorate.php?inV=$main8&from=USD&to=$cur_to&Calculate=Convert");
					$c = $currency->find('div[id=converter_results]');
					$temp = $c[0]->childNodes(0)->childNodes(0)->childNodes(0)->innertext;
						
					$regex = '/=(.+?)\s/';
			
					if (preg_match($regex,$temp,$match)) {
						$main8 = trim($match[1]);
					}
					else {
						echo "<script type=\"text/javascript\">window.alert(\"Failed\")</script>";
					}	
				}

				$main9 = $e->find('span[class=count]',0)->text();
				$main10 = "Driver";	
			}
			else if ($e->childNodes(0)->getAttribute('class') === "passenger_box") {
				$main8 = "NA";
				$main9 = "NA";
				$main10 = "Passenger";
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Error: No passenger or driver\")</script>";
			}
			
			$arr[] = array(
				'source' => "Zimride",
				'name' => $main1,
				'profile' => $main6,
				'from' => $main3,
				'to' => $main4,				
				'info' => $main5,				
				'image' => $main2,
				'traveltype' => $main10,
				'traveltypeicon' => "NA",
				'departuredate' => $main7,
				'price' => $main8,
				'seat' => $main9,
				'triptype' => $main11,
				'currencytype' => $cur_to
			);
		}
		
		// Process Ridejoy
		foreach ($ridejoy->find('div[class=result_main clearfix]') as $e) {
 		       	$main6 = $e->find('a[class=view_details]',0)->getAttribute('href');
	        	
			$main1 = "NA";

			$main2 = $e->find('img[class="profile_thumbnail icon square_50"]',0)->getAttribute('src');

			$main12 = $e->find('div[class="photo ride_icon"]',0)->childNodes(0)->getAttribute('src');
			
			$main11 = "NA";
					
			$main3 = trim($e->find('div[class=origin]',0)->text());
			
			$main4 = trim($e->find('div[class=destination]',0)->text());
			
			$main5 = trim($e->find('div[class=extra_info]',0)->text());
			
			if ($e->parent()->parent()->parent()->getAttribute('class') == "date_results") {
				$main7 = trim($e->parent()->parent()->parent()->prev_sibling()->childNodes(0)->text());
			}
			
			if ($e->find('div[class="photo ride_icon"]',0)->childNodes(0)->getAttribute('alt') == "Driver_icon_color_50x50") {
				$main8 = substr(trim($e->find('div[class=seat_count]',0)->text()), 1);
				if ($cur_to !== "USD") {
					$currency = file_get_html("http://www.gocurrency.com/v2/dorate.php?inV=$main8&from=USD&to=$cur_to&Calculate=Convert");
					$c = $currency->find('div[id=converter_results]');
					$temp = $c[0]->childNodes(0)->childNodes(0)->childNodes(0)->innertext;
						
					$regex = '/=(.+?)\s/';
			
					if (preg_match($regex,$temp,$match)) {
						$main8 = trim($match[1]);
					}
					else {
						echo "<script type=\"text/javascript\">window.alert(\"Failed\")</script>";
					}	
				}

				$main9 = "NA";
				$main10 = "Driver";	
			}
			else if ($e->find('div[class="photo ride_icon"]',0)->childNodes(0)->getAttribute('alt') == "Passenger_icon_color_50x50") {
				$main8 = "NA";
				$main9 = "NA";
				$main10 = "Passenger";
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Error: No passenger or driver\")</script>";
			}
				
			$arr[] = array(
				'source' => "Ridejoy",
				'name' => $main1,
				'profile' => $main6,
				'from' => $main3,
				'to' => $main4,				
				'info' => $main5,				
				'image' => $main2,
				'traveltype' => $main10,
				'traveltypeicon' => $main12,
				'departuredate' => $main7,
				'price' => $main8,
				'seat' => $main9,
				'triptype' => $main11,
				'currencytype' => $cur_to
			);
		}
		
	$filename = 'results.json';

	if (($file = fopen($filename, 'w')) === FALSE) {
		echo "<script type=\"text/javascript\">window.alert(\"Cannot open file: $filename\")</script>";
		exit;
	}

	if (fwrite($file, json_encode($arr)) === FALSE) {
		echo "<script type=\"text/javascript\">window.alert(\"Cannot write to file: $filename\")</script>";
		exit;
	}
    	
	fclose($file);

	//echo "<script type=\"text/javascript\">window.alert(\"$filename successfully created\")</script>";

	$zimride->clear();
	$ridejoy->clear();

	$resultpage = file_get_contents("./output.html", false);
	echo $resultpage;
?>
