<?php
    // Include the library
    include('simple_html_dom.php');

	$lOriginLocation = $_POST['originTextField'];
    
    $lOriginLocation = str_replace(', ', '%2C+', $lOriginLocation);
    $lOriginLocation = str_replace(' ', '+', $lOriginLocation);
	$lOriginLocation = str_replace('"', $lOriginLocation,'"');
    $lOriginLat = $_POST['originLatitudeTextField'];
    $lOriginLng = $_POST['originLongitudeTextField'];

    $lDestinationLocation = $_POST['destinationTextField'];
    
    $lDestinationLocation = str_replace(',', '%2C+', $lDestinationLocation);
    $lDestinationLocation = str_replace(' ', '+', $lDestinationLocation);

    $lDestinationLat = $_POST['destinationLatitudeTextField'];
    $lDestinationLng = $_POST['destinationLongitudeTextField'];






    // Retrieve the DOM from a given URL

$Search = 'http://www.zimride.com/search?s=' . $lOriginLocation .'&e='. $lDestinationLocation . '&date=' . '&s_name='. $lOriginLocation .  '&s_full_text=' .$lOriginLocation .	 '&s_error_code='.	'&s_address='.	$lOriginLocation.		'&s_city=' .$lOriginLocation.		'&s_zip='.	 '&s_country='. $lOriginLocation.	'&s_lat='. $lOriginLat. '&s_lng='. $lOriginLng.			'&s_location_key='. '&s_user_lat='. '&s_user_lng='. '&s_user_country='.		'&e_name='.	$lDestinationLocation.	  '&e_full_text='.	$lDestinationLocation.		'&e_error_code='. '&e_address='.	$lDestinationLocation.		'&e_city='.		$lDestinationLocation.	'&e_zip='.	'&e_country='.	$lOriginLocation.	'&e_lat='.    $lDestinationLat .	'&e_lng='	.$lDestinationLng. '&e_location_key='. '&e_user_lat='.	'&e_user_lng='. '&e_user_country=';
	//																																	  San+Jose%2C+CA      &s_full_text   San+Jose%2C+CA%2C+USA&s_error_code=	  &s_address=	San+Jose%2C+CA%2C+USA	 &s_city=	San+Jose&s_state=CA		 &s_zip=	  &s_country=	US					 &s_lat=37.3393857		 &s_lng=   -121.89495549999998	 &s_location_key=	 &s_user_lat=    &s_user_lng=    &s_user_country=		 &e_name=    San+Francisco%2C+CA	   &e_full_text=	San+Francisco%2C+CA%2C+USA	 &e_error_code=	   &e_address=		  San+Francisco%2C+CA%2C+USA &e_city=		San+Francisco&e_state=CA &e_zip=	 &e_country=	US					 &e_lat=		  37.7749295		 &e_lng=	-122.41941550000001	&e_location_key=	&e_user_lat=	 &e_user_lng=	 &e_user_country=
	
	
	echo $Search;
    $articles = array();
    getArticles($Search);
	print "test";
	print_r($articles);

function getArticles($page) {
    global $articles, $descriptions;

    $html = new simple_html_dom();
    $html->load_file($page);
    $items = $html->find('div[class=clearfix ride_info]');
	foreach($items as $post) {
      
	//	$lOrigin = 		$post->find('div[class=clearfix route route_with_extra]',0) -> childNodes(0) -> find('div[class=origin]',0) ->innertext;
		$lOrigin = 		$post->find('div[class=entry]',0) -> childNodes(0) -> find('span[class=inner]',0) ->innertext;
		
													
      //  $lDestination = $post->find('div[class=clearfix route route_with_extra]',0) -> childNodes(0) -> find('div[class=destination]',0) -> innertext;
	 $lDestination = $post->find('div[class=entry]',0) -> childNodes(0) -> find('span[class=trip]',0) -> innertext;
	 
        $lResult[] = array('s' => $lOrigin,
                           'e' => $lDestination
                          );
	}  // foreach
    
	
    $response = $lResult;
    $lFilePath = fopen('test.json', 'w');
    fwrite($lFilePath, json_encode($response));
    fclose($lFilePath);

    $html -> clear();
}
?>

<html>
<body>
    <div id="main">
    </div>
</body>
</html>