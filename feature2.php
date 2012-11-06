<!--Feature 2 Scrap PHP File-->
<?php
    include('simple_html_dom.php');

    $lOriginLocation = $_POST['originTextField'];
    
    $lOriginLocation = str_replace(', ', '%2C+', $lOriginLocation);
    $lOriginLocation = str_replace(' ', '+', $lOriginLocation);

    $lOriginLat = $_POST['originLatitudeTextField'];
    $lOriginLng = $_POST['originLongitudeTextField'];

    $lDestinationLocation = $_POST['destinationTextField'];
    
    $lDestinationLocation = str_replace(',', '%2C+', $lDestinationLocation);
    $lDestinationLocation = str_replace(' ', '+', $lDestinationLocation);

    $lDestinationLat = $_POST['destinationLatitudeTextField'];
    $lDestinationLng = $_POST['destinationLongitudeTextField'];

    $Search = 'http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=ride_offer&origin=' . $lOriginLocation . '&origin_latitude=' . $lOriginLat . '&origin_longitude=' . $lOriginLng . '&destination=' . $lDestinationLocation . '&destination_latitude=' . $lDestinationLat . '&destination_longitude=' . $lDestinationLng . '&date=';
    echo $Search;
    $articles = array();
    getArticles($Search);

function getArticles($page) {
    global $articles, $descriptions;

    $html = new simple_html_dom();
    $html->load_file($page);
    $items = $html->find('div[class=clearfix ride_info]');
	foreach($items as $post) {
      
		$lOrigin = $post->find('div[class=clearfix route route_with_extra]',0) -> childNodes(0) -> find('div[class=origin]',0) ->innertext;
        $lDestination = $post->find('div[class=clearfix route route_with_extra]',0) -> childNodes(0) -> find('div[class=destination]',0) -> innertext;
        
        $lResult[] = array('origin' => $lOrigin,
                           'destination' => $lDestination
                          );
	}  // foreach
    
    $response = $lResult;
    $lFilePath = fopen('feature2_result.json', 'w');
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