<?php
    // Include the library
    include('simple_html_dom.php');

    $type= $_POST['vehicleType'];

    $myFile = "File.json";

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






    // Retrieve the DOM from a given URL

	$folder = file_get_html('http://www.zimride.com/search?s=' . $lOriginLocation .'&e='. $lDestinationLocation . '&date=' . '&s_name='. $lOriginLocation .  '&s_full_text' .$lOriginLocation . '&s_error_code='.	'&s_address='.	$lOriginLocation. '&s_city' .$lOriginLocation.		'&s_zip='.	 '&s_country='.$lOriginLocation.	'&s_lat='. $lOriginLat. '&s_lng='. $lOriginLng .		'&s_location_key='. '&s_user_lat='. '&s_user_lng='. '&s_user_country='.'&e_name='.'$lDestinationLocation .'&e_full_text='.$lDestinationLocation.	'&e_error_code='. '&e_address='.	$lDestinationLocation.	  '&e_city='.	$lDestinationLocation.'&e_zip='.'&e_country='.	$lOriginLocation.	'&e_lat='.    $lDestinationLat .'&e_lng='	.$lDestinationLng. '&e_location_key='. '&e_user_lat='.	'&e_user_lng='. '&e_user_country=');
	//																																	  San+Jose%2C+CA      &s_full_text   San+Jose%2C+CA%2C+USA&s_error_code=	&s_address=		San+Jose%2C+CA%2C+USA	&s_city=San+Jose&s_state=CA	 &s_zip=	  &s_country=	US					 &s_lat=37.3393857		 &s_lng=   -121.89495549999998	 &s_location_key=	&s_user_lat=&s_user_lng=&s_user_country=		    &e_name=    San+Francisco%2C+CA	   &e_full_text=  San+Francisco%2C+CA%2C+USA &e_error_code=	   &e_address=		San+Francisco%2C+CA%2C+USA&e_city=		San+Francisco&e_state=CA&e_zip=	 &e_country=	US					&e_lat=		  37.7749295		 &e_lng=	-122.41941550000001	&e_location_key=	&e_user_lat=	 &e_user_lng=	 &e_user_country=
	
	
	// CURRENT STATUS
	// Scrap all data present on that url created

	foreach ($folder->find('div[id=results]') as $e){
		$main2 = array($e->childNodes(0)->outertext);
        $arr[] = array(
        			     'info' => $main2,
               		  );
    }

    $response = $arr;

    $fp = fopen('results.json', 'w');
    fwrite($fp, json_encode($response));
    fclose($fp);

    $folder->clear();
    $homepage = file_get_contents('./zimride_sample.html', false);
	echo $homepage;

    ?>

<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/
libs/jquery/1.3.0/jquery.min.js"></script>
<style type="text/css">
#div-my-tabl {
font-family: Arial, Helvetica, sans-serif;
background:#FFFFFF;
}

#title{
text-align:center;
font-family:Tahoma, Geneva, sans-serif;
font-size:26px;
text-decoration:underline;
outline-color:#090;
color:#60C;
}

td[id *="1"]{color:#F30;font-size:18px;font-stretch:semi-expanded;font-style:oblique;}
    </style>
    </head>
    <body id="div-my-tabl">
    <div id="div-my-tabl"></div>

    <script>
    $("document").ready(function() {

                        $.getJSON("results.json", function(data) {

                                  $("#div-my-table").text("<table>");
                                  var urlList = "";
                                  $.each(data, function(i, item) {
                                         $("#div-my-tabl").append("<tr>");
                                         $("#div-my-tabl").append("<td id=1>"+item.info+"</td>");
                                         $("#div-my-tabl").append("<br>");

                                         $("#div-my-tabl").append("</tr>");
                                         $("#div-my-tabl").append("<br />");
                                         $("#div-my-tabl").append("<br />");
                                         $("#div-my-tabl").append("<br />");

                                         });

                                  $("#div-my-table").append("</table>");

                                  });
                        });
    </script>
    </body>
    </html>