<!--Feature 2 Scrap PHP File        -->
<!--Created By: Kai - Ting(Danil) Ko-->
<!--Modified By: None               -->
<?php
    // Include the necessary simple_html_dom.php to use its functions
    include('simple_html_dom.php');

    // Retrive the text in the origin text field from the html page post events
    $lOriginLocation = $_POST['originTextField'];
    
    // Replace ' ,' in the string to '%2C+' to fulfill the URL requirement for the scrap target website
    $lOriginLocation = str_replace(', ', '%2C+', $lOriginLocation);
    // Replace ' ' in the string to '+' to fulfill the URL requirement for the scrap target website
    $lOriginLocation = str_replace(' ', '+', $lOriginLocation);

    // Retrive the text in the origin latitude and longitude field from the html page post events
    $lOriginLat = $_POST['originLatitudeTextField'];
    $lOriginLng = $_POST['originLongitudeTextField'];

    // Retrive the similar info but for destination
    $lDestinationLocation = $_POST['destinationTextField'];
    
    $lDestinationLocation = str_replace(',', '%2C+', $lDestinationLocation);
    $lDestinationLocation = str_replace(' ', '+', $lDestinationLocation);

    $lDestinationLat = $_POST['destinationLatitudeTextField'];
    $lDestinationLng = $_POST['destinationLongitudeTextField'];

    // Concate all retrive information to a vaild url for the scrap target website
    $Search = 'http://ridejoy.com/rides/search?utf8=%E2%9C%93&type=ride_offer&origin=' . $lOriginLocation . '&origin_latitude=' . $lOriginLat . '&origin_longitude=' . $lOriginLng . '&destination=' . $lDestinationLocation . '&destination_latitude=' . $lDestinationLat . '&destination_longitude=' . $lDestinationLng . '&date=';
    // Print out the url for testing purpose
    echo $Search;
    echo "<br/>";
    // Create array to store retrived object
    $articles = array();
    // Call the getArticles with the URL
    getArticles($Search);

// getArticle
// Input: $page The url representation of the target scrap page
// Output: None
// To save the target URL page and scrap it for useful information.
// Save results in JSON file
function getArticles($page) {
    // Used the global declared variables instead of local decalared one
    global $articles, $descriptions;

    // Set up new simple_html_dom to scrap the target page
    $html = new simple_html_dom();
    // Set the URL
    $html->load_file($page);
    // Scrap the page to find all div tags with class=clearfix ride_info
    $items = $html->find('div[class=clearfix ride_info]');
    // For each objects in the result, find the correct children in it and stored into array
	foreach($items as $post) {
        // Get origin location by find the div tag with class=clearfix route route_with_extra
        // In it, find the div tag with class=origin
        // Copy the inner text that is in the tag
		$lOrigin = $post->find('div[class=clearfix route route_with_extra]',0) -> childNodes(0) -> find('div[class=origin]',0) ->innertext;
        // Retrive similar information for destination
        $lDestination = $post->find('div[class=clearfix route route_with_extra]',0) -> childNodes(0) -> find('div[class=destination]',0) -> innertext;
        // Stored the retrived information and given them respect names in an object in the array
        $lResult[] = array('origin' => $lOrigin,
                           'destination' => $lDestination
                          );
        // Print out the result for testing purpose
        echo Origin;
        echo $lOrigin;
        echo "<br/>";
        echo Destination;
        echo $lDestination;
        echo "<br/>";
        echo "<br/>";
	}  // foreach
    
    // Print out no result message if there is no item in the result
    if(count($items) == 0)
    {
        echo "Sorry, no result returned based on input"; 
    }  // if
    else{
        // Store the result into response
        $response = $lResult;
        // Create a file stream based on parameters for written
        $lFilePath = fopen('feature2_result.json', 'w');
        // Encode the array with JSON format and write it into the created file
        fwrite($lFilePath, json_encode($response));
        // Close the file stream
        fclose($lFilePath);
        // Clear to erase the memory created for temp scrap page
    }  // else
    $html -> clear();
}
?>
<!--HTML File to show url for testing purpose-->
<html>
<body>
    <div id="main">
    </div>
</body>
</html>