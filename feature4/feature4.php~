<?php
	include("simple_html_dom.php");
	//echo "Hi";
	function isZipcode($str) {
		if (strlen($str) == 5) {
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

	function isState($str) {
		//echo strlen($str).$str."<br />";
		if (strlen($str) == 2) {
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

	$start = $_POST['originTextField'];
	$end = $_POST['destinationTextField'];
	//echo $start . $end;
	$sterm = preg_split("/[,]+/", $start);
	$eterm = preg_split("/[,]+/", $end);

	$sword1 = preg_split("/[\s]+/", $sterm[0]);
	$eword1 = preg_split("/[\s]+/", $eterm[0]);

	$sword = preg_split("/[\s]+/", $sterm[1]);
	$eword = preg_split("/[\s]+/", $eterm[1]);
	$sword2 = $sword[1];
	$eword2 = $eword[1];

	//echo sizeof($sword2)." ".sizeof($eword2) . "<br />";
	if (isZipcode($originTextField) && isZipcode($destinationTextField)) {
		$data = file_get_contents("http://ridejoy.com/rides/search?utf8=%E2%9C%93&origin=$loc1%2C+$sword2&origin_latitude=37.3393857&origin_longitude=-121.89495549999998&destination=$loc2%2C+$eword2&destination_latitude=37.7749295&destination_longitude=-122.41941550000001&date=");
	}
	else if (sizeof($sterm) == 2 && sizeof($eterm) == 2) {
		if (isState($sword2) && isState($eword2)) {
			//echo isState($sword2)."<br />";
			$loc1 = $sword1[0];
			for ($i = 1; $i < sizeof($sword1); $i++) {
				$loc1 = $loc1."+".$sword1[$i];
			}
			$loc2 = $eword1[0];
			for ($i = 1; $i < sizeof($eword1); $i++) {
				$loc2 = $loc2."+".$eword1[$i];
			}
			//echo $loc1." ".$loc2."<br />";			
			$data = file_get_html("http://www.zimride.com/search?s=$loc1%2C+$sword2&e=$loc2%2C+$eword2");
			}
		else {
			echo "Not a valid 2-letter State abbreviation: " . $sword2[1] . " and " . $eword2[1];
		}
	}
	else if (sizeof($sterm) == 1 && sizeof($eterm) == 2) {
		$data = file_get_contents('http://ridejoy.com/rides/search?utf8=%E2%9C%93&origin=San+Jose%2C+CA+95133&origin_latitude=37.3716914&origin_longitude=-121.8619539&destination=Los+Angeles%2C+CA&destination_latitude=34.0522342&destination_longitude=-118.2436849&date=11%2F03%2F2012');
	}
	else if (sizeof($sterm) == 2 && sizeof($eterm) == 1) {
		$data = file_get_contents('http://ridejoy.com/rides/search?utf8=%E2%9C%93&origin=San+Jose%2C+CA+95133&origin_latitude=37.3716914&origin_longitude=-121.8619539&destination=Los+Angeles%2C+CA&destination_latitude=34.0522342&destination_longitude=-118.2436849&date=11%2F03%2F2012');
	}
	else if (sizeof($sterm) == 1 && sizeof($eterm) == 1) {
		$data = file_get_contents('http://ridejoy.com/rides/search?utf8=%E2%9C%93&origin=San+Jose%2C+CA+95133&origin_latitude=37.3716914&origin_longitude=-121.8619539&destination=Los+Angeles%2C+CA&destination_latitude=34.0522342&destination_longitude=-118.2436849&date=11%2F03%2F2012');
	}
	else {
		echo "City name more than 3 words";	
	}
	
	/*$regex = '/(.+?) vehicles found/';

	if (preg_match($regex,$data,$match)) {
		echo $match[1];
	}
	else {
		echo "0";
	}*/
	
	/*$temp = $data->find('div[class=search_results_explanation]');
	echo $temp[0];*/

	foreach ($data->find('div[class=passenger_box]') as $e){
        	$main1 = $e->next_sibling()->childNodes(0)->text();
		//echo $main1;
		$main2 = $e->next_sibling()->childNodes(1)->getAttribute('src');
		$temp = $e->next_sibling()->next_sibling()->childNodes(0)->childNodes(0)->innertext;
		//echo $temp."<br />";
		
		$regex = '/(.+?)<span/';
		if (preg_match($regex,$temp,$match)) {
			$main3 = $match[1];
			//echo $match[1]."<br />";
		}
		else {
			echo "Failed<br />";
		}
		
		$regex = '/span>(.*)/';
		if (preg_match($regex,$temp,$match)) {
			$main4 = $match[1];
			//echo $match[1]."<br />";
		}
		else {
			echo "Failed<br />";
		}
		//echo $str[0]."<br />";
		$temp = $e->next_sibling()->next_sibling()->childNodes(01)->innertext;
		$str = explode ("&nbsp; / &nbsp;", $temp);
		$main5 = $str[0];
		for ($i = 1; $i < sizeof($str); $i++) {
			$main5 = $main5." / ".$str[$i];
		}
		//echo $main5."<br />";
		$main6 = $e->parent()->parent()->getAttribute('href');
        	//echo $main6."<br />";
		$arr[] = array(
			'url' => $main6,
			'info' => $main5,
			'to' => $main4,
			'from' => $main3,
			'image' => $main2,
			'name' => $main1
		);
	}

	echo "Displaying Json File:<br />";
	echo json_encode($arr);
	$filename = 'data.json';

	$file = fopen($filename, 'w');
	if (!$file) {
		echo "Cannot open file ($filename)";
		exit;
	}

	if (fwrite($file, json_encode($arr)) === FALSE) {
		echo "Cannot write to file ($filename)";
		exit;
	}
    	fclose($file);

	$data->clear();
?>
