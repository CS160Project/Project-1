<?php
// Scrapper for Zimride and Ridejoy
	include("simple_html_dom.php"); // Using simple_html_dom library

	$arr = array();

	// Getting date
	$date = $_POST['dateselected'];
	//echo "<script type=\"text/javascript\">window.alert(\"Scrap: $date\")</script>";
	$zrTravelStatus = $_POST['travelStatus']; //Zimride travel Status
	//echo "<script type=\"text/javascript\">window.alert(\"Scrap: $zrTravelStatus\")</script>";
	if ($zrTravelStatus === "offer") {
		$rjTravelStatus = "ride_request";
	}
	else if ($zrTravelStatus === "need") {
		$rjTravelStatus = "ride_offer";
	}
	else {
		$rjTravelStatus = "";
	}

	$vehicleType = $_POST['vehicleType'];
	//echo "<script type=\"text/javascript\">window.alert(\"Scrap: $vehicleType\")</script>";
	$currencyType = $_POST['currencytype'];
	//echo "<script type=\"text/javascript\">window.alert(\"$currencyType\")</script>";
	$start = $_POST['originTextField'];
	$end = $_POST['destinationTextField'];
	
	//Retrive the text in the origin latitude and longitude field from the html page post events
	$lOriginLat = $_POST['originLatitudeTextField'];
	$lOriginLng = $_POST['originLongitudeTextField'];
	//Retrive the text in the destination latitude and longitude field from the html page post events
	$lDestinationLat = $_POST['destinationLatitudeTextField'];
	$lDestinationLng = $_POST['destinationLongitudeTextField'];

	// Check to see if location is a zipcode
	function isZipcode($str) {
		if (strlen($str) === 5) {
			for ($i = 0; $i < 5; $i++) {
				if (!(is_numeric($str[$i]))) {
					return false;
				}
			}
			return true;
		}
		else {
			return false;
		}
	}

	// Check to see if location is 2-letters state
	function isState($str) {
		//echo strlen($str).$str."<br />";
		if (strlen($str) === 2) {
			//echo $str[$i];
			for ($i = 0; $i < 2; $i++) {
				if (!(ctype_alpha($str[$i]))) {
					return false;
				}
				//echo $str[$i];
			}
			return true;
		}
		else {
			return false;
		}
	}

	function searchDuplicate($name, $from, $to, $travelType, $tripType) {
		global $arr;

		foreach ($arr as $a) {
			if ($a['personname'] === $name && $a['from'] === $from && $a['to'] === $to
				&& $a['traveltype'] === $travelType && $a['triptype'] === $tripType) {
				//echo "<script type=\"text/javascript\">window.alert(\"$name is a duplicate\")</script>";
				return true;
			}
		}

		return false;
	}

	function zimrideScrap ($link) {
		global $arr;
		global $currencyType;
		//echo "<script type=\"text/javascript\">window.alert(\"$link\")</script>";
		$zimride = file_get_html($link);
		
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
				$main11 = "N/A";
				echo "<script type=\"text/javascript\">window.alert(\"Zimride error: no trip type\")</script>";
			}
			
			$temp = $e->find('span[class=inner]',0)->innertext;

			$regex = '/(.+?)<span/';
			if (preg_match($regex,$temp,$match)) {
				$main3 = trim($match[1]);
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Zimride from regex failed\")</script>";
			}
		
			$regex = '/span>(.*)/';
			if (preg_match($regex,$temp,$match)) {
				$main4 = trim($match[1]);
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Zimride to regex failed\")</script>";
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

				// Converting currency
				if ($currencyType !== "USD") {
					$currency = file_get_html("http://www.gocurrency.com/v2/dorate.php?inV=$main8&from=USD&to=$currencyType&Calculate=Convert");
					$c = $currency->find('div[id=converter_results]');
					$temp = $c[0]->childNodes(0)->childNodes(0)->childNodes(0)->innertext;

					$regex = '/=(.+?)\s/';
			
					if (preg_match($regex,$temp,$match)) {
						$main8 = trim($match[1]);
					}
					else {
						echo "<script type=\"text/javascript\">window.alert(\"Zimride converter price regex failed\")</script>";
					}	
				}

				$main9 = $e->find('span[class=count]',0)->text();
				$main10 = "Driver";
				$main12 = "./driver_icon_color_50x50.png";	
			}
			else if ($e->childNodes(0)->getAttribute('class') === "passenger_box") {
				$main8 = "N/A";
				$main9 = "N/A";
				$main10 = "Passenger";
				$main12 = "./passenger_icon_color_50x50.png";
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Zimride error: No passenger or driver\")</script>";
				$main8 = "N/A";
				$main9 = "N/A";
				$main10 = "N/A";
			}
			
			if (searchDuplicate($main1, $main3, $main4, $main10, $main11) === false) {
				$arr[] = array (
					'sourcesite' => "Zimride",
					'personname' => $main1,
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
					'currencytype' => $currencyType
				);
			}
			else {
				//echo "<script type=\"text/javascript\">window.alert(\"$main1 is a duplicate for Zimride\")</script>";
			}
		}

		/*$zrTag = $zimride->find('span[class=current_page]');
		$zrCurrentTag = $zrTag[0];

		if ($zrCurrentTag->next_sibling()->tag === "a") {
			$zimrideLink = $zrCurrentTag->next_sibling()->getAttribute('href');
			//echo "<script type=\"text/javascript\">window.alert(\"$zimrideLink\")</script>";
			$zimride->clear();
			return $zimrideLink;
		}
		else {
			//echo "<script type=\"text/javascript\">window.alert(\"$zrCurrentTag\")</script>";
			$zimride->clear();
			return false;
		}*/

		$zimride->clear();
	}

	function ridejoyScrap($link) {
		global $arr;
		global $currencyType;

		$ridejoy = file_get_html($link);

		foreach ($ridejoy->find('div[class=result_main clearfix]') as $e) {
 			$main6 = $e->find('a[class=view_details]',0)->getAttribute('href');
		        	
			$profile = file_get_html($main6);
			$main1 = $profile->find('div[class="top-row row clearfix"]',0)->childNodes(1)->innertext;
	
			$main2 = $e->find('img[class="profile_thumbnail icon square_50"]',0)->getAttribute('src');
	
			//$main12 = $e->find('div[class="photo ride_icon"]',0)->childNodes(0)->getAttribute('src');
			
			$main11 = "N/A";
					
			$main3 = trim($e->find('div[class=origin]',0)->text());
			
			$main4 = trim($e->find('div[class=destination]',0)->text());
			
			$main5 = trim($e->find('div[class=extra_info]',0)->text());
			
			if ($e->parent()->parent()->parent()->getAttribute('class') == "date_results") {
				$main7 = "Departing ".trim($e->parent()->parent()->parent()->prev_sibling()->childNodes(0)->text());
			}
			
			if ($e->find('div[class="photo ride_icon"]',0)->childNodes(0)->getAttribute('alt') == "Driver_icon_color_50x50") {
				$main8 = substr(trim($e->find('div[class=seat_count]',0)->text()), 1);
				
				// Converting currency
				if ($currencyType !== "USD") {
					$currency = file_get_html("http://www.gocurrency.com/v2/dorate.php?inV=$main8&from=USD&to=$currencyType&Calculate=Convert");
					$c = $currency->find('div[id=converter_results]');
					$temp = $c[0]->childNodes(0)->childNodes(0)->childNodes(0)->innertext;
						
					$regex = '/=(.+?)\s/';
			
					if (preg_match($regex,$temp,$match)) {
						$main8 = trim($match[1]);
					}
					else {
						echo "<script type=\"text/javascript\">window.alert(\"Ridejoy Converter price regex failed\")</script>";
					}	
				}
	
				$s = $profile->find('img[class=square_50]',0)->parent()->next_sibling()->text();

				$regex = '/offering\s(.+?)\s/';
			
				if (preg_match($regex,$s,$match)) {
					$main9 = trim($match[1]);
				}
				else {
					echo "<script type=\"text/javascript\">window.alert(\"Ridejoy Converter number of seat regex failed\")</script>";
				}

				$main10 = "Driver";
				$main12 = "./driver_icon_color_50x50.png";		
			}
			else if ($e->find('div[class="photo ride_icon"]',0)->childNodes(0)->getAttribute('alt') == "Passenger_icon_color_50x50") {
				$main8 = "N/A";
				$main9 = "N/A";
				$main10 = "Passenger";
				$main12 = "./passenger_icon_color_50x50.png";	
			}
			else {
				echo "<script type=\"text/javascript\">window.alert(\"Ridejoy error: No passenger or driver\")</script>";
				$main8 = "N/A";
				$main9 = "N/A";
				$main10 = "N/A";
			}
			
			if (searchDuplicate($main1, $main3, $main4, $main10, $main11) === false) {
				$arr[] = array(
					'sourcesite' => "Ridejoy",
					'personname' => $main1,
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
					'currencytype' => $currencyType
				);		
			}
			else {
				//echo "<script type=\"text/javascript\">window.alert(\"$main1 is a duplicate for Ridejoy\")</script>";
			}	
		}

		$ridejoy->clear();
	}

	if (isZipcode($start) === true && isZipcode($end) === true) {
		$zimrideLink = "http://www.zimride.com/search?s=$start&e=$end&date=$month%2F$day%2F$year&filter_type=$travelStatus&filter_vehicle=$vehicleType";
		$ridejoyLink = "http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=$rjTravelStatus&origin=$start&origin_latitude=$lOriginLat&origin_longitude=$lOriginLng&destination=$end&destination_latitude=$lDestinationLat&destination_longitude=$lDestinationLng&date=$month%2F$day%2F$year";
	}
	else {
		// Splitting state and city
		$sterm = preg_split("/[,]+/", $start);
		$eterm = preg_split("/[,]+/", $end);
		
		if (sizeof($sterm) === 2 && sizeof($eterm) === 2) {
			$sstate = trim($sterm[1]);
			$estate = trim($eterm[1]);
			
			if (isState($sstate) === true && isState($estate) === true) {
				// Converting to Zimride search query format
				$loc1 = str_replace(' ', '+', trim($sterm[0]));
    				$loc2 = str_replace(' ', '+', trim($eterm[0]));

				
				if ($date == "") {
					//echo "<script type=\"text/javascript\">window.alert(\"Scrap: empty date\")</script>";
					date_default_timezone_set('America/Los_Angeles');

					$month = date("m");
					$day = date("d");
					$year = date("Y");
					//echo "<script type=\"text/javascript\">window.alert(\"Scrap: $year $month $day\")</script>";
					$ridejoyLink = "http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=$rjTravelStatus&origin=$loc1%2C+$sstate&origin_latitude=$lOriginLat&origin_longitude=$lOriginLng&destination=$loc2%2C+$estate&destination_latitude=$lDestinationLat&destination_longitude=$lDestinationLng&date=";
				}
				else {
					$date = str_replace('-', ' ', $date);
					$date = explode(" ", $date);
					$year = $date[0];
					$month = $date[1];
					$day = $date[2];
					//echo "<script type=\"text/javascript\">window.alert(\"Scrap: $year $month $day\")</script>";
					$ridejoyLink = "http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=$rjTravelStatus&origin=$loc1%2C+$sstate&origin_latitude=$lOriginLat&origin_longitude=$lOriginLng&destination=$loc2%2C+$estate&destination_latitude=$lDestinationLat&destination_longitude=$lDestinationLng&date=$month%2F$day%2F$year";
				}

				$zimrideLink = "http://www.zimride.com/search?s=$loc1%2C+$sstate&e=$loc2%2C+$estate&date=$month%2F$day%2F$year&filter_type=$zrTravelStatus&filter_frequency=one-time&filter_privacy=public&filter_vehicle=$vehicleType";
			}
			else {
				$tmp1 = $sword2[1];
				$tmp2 = $eword2[1];
				echo "<script type=\"text/javascript\">window.alert(\"Not a valid 2-letter State abbreviation: $tmp1 and $tmp2\")</script>";
			}
		}
	}

	// Multiple result pages scrapping method for zimride (ridejoy doesn't have multiple pages) (Not working)
	/*$continueScrap = false;
	do {		
		$continueScrap = zimrideScrap();

		if ($continueScrap !== false) {
			$zimrideLink = $continueScrap;
			$continueScrap = true;
			echo "<script type=\"text/javascript\">window.alert(\"$zimrideLink\")</script>";
		}
		
	} while ($continueScrap == true);*/
	
	// Process Zimride
	$temp = zimrideScrap($zimrideLink);
	
	/*$zrLink = "http://www.zimride.com/search?s=$loc1%2C+$sstate&e=$loc2%2C+$estate&date=&filter_type=$zrTravelStatus&filter_frequency=one-time&filter_privacy=public&filter_vehicle=$vehicleType&pageID=2";
	$temp = zimrideScrap($zrLink);	
	echo "<script type=\"text/javascript\">window.alert(\"$zrLink\")</script>";*/

	// Process Ridejoy
	ridejoyScrap($ridejoyLink);
	
	$filename = 'results.json';

	if (($file = fopen($filename, 'w')) === false) {
		echo "<script type=\"text/javascript\">window.alert(\"Cannot open file: $filename\")</script>";
		exit;
	}

	if (fwrite($file, json_encode($arr)) === false) {
		echo "<script type=\"text/javascript\">window.alert(\"Cannot write to file: $filename\")</script>";
		exit;
	}
    	
	fclose($file);

	//echo "<script type=\"text/javascript\">window.alert(\"$filename successfully created\")</script>";

	$resultpage = file_get_contents("./output.html", false);
	echo $resultpage;
?>
