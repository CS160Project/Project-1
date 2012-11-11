<?php
	// Feature 1: Optional Search Input - Vehicle Type

    // Include the library
    include('../simple_html_dom.php');

	// Obtain the user's vehicle type choice from the feature1.html file
    $type= $_POST['vehicleType'];
	$typePass= $_POST['passengerStatus'];
	

    // Retrieve the DOM from a given URL
	$folder = file_get_html("http://www.zimride.com/search?s=San+Jose%2C+CA&e=San+Francisco%2C+CA&date=11%2F09%2F12&filter_type=$typePass&filter_frequency=one-time&filter_privacy=public&filter_vehicle=$type");
	
	// Scrap all the car sharing postings
	foreach ($folder->find('div[id=results]') as $e) {
		$main2 = $e->childNodes(0)->outertext;
		//echo $main2;
        	$arr[] = array(
        			     'info' => $main2,
               		  );
  	  }

	// Put the scrapped data into a new variable
    $response = $arr;

    $fp = fopen('results.json', 'w');
    fwrite($fp, json_encode($response));
    fclose($fp);

    $folder->clear();
    $homepage = file_get_contents('./feature1.html', false);
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
    <div id="div-my-tabl">This is only a demo. When unified scraping is implemented, a dynamic version will replace this.</div>

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
