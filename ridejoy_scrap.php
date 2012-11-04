<?php
    include('simple_html_dom.php');
    $articles = array();
    getArticles('http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=ride_offer&origin=&origin_latitude=&origin_longitude=&destination=Los+Angeles%2C+CA&destination_latitude=34.0522342&destination_longitude=-118.2436849&date=');

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
    $lFilePath = fopen('result.json', 'w');
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