<?php
    // Include the library
    include('simple_html_dom.php');

    $type= $_POST['vehicleType'];

    $myFile = "File.json";

    // Retrieve the DOM from a given URL

    $folder = file_get_html("http://www.zimride.com/search?filterSearch=true&filter_vehicle=$type");

	echo $folder;

	foreach ($folder->find('div[class=ride_list]') as $e){
        $main2 = array($e->childNodes(0)->outertext);
    }

    $response = $main2;

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
                                         $("#div-my-tabl").append("<td id=1>"+item.rate+"</td>");
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