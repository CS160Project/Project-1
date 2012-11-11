<?php
    
    // Feature 5. Converts the amount from one currency to another currency 
    // Include the library
    include('simple_html_dom.php');
    
    $amt= $_POST['amount'];
    $cur_from = $_POST['currency_from'];
    $cur_to = $_POST['currency_to'];
	
    //validates the amount entry for empty string
    if(!$_POST['amount'])
    {
        echo('<b>Error:</b> <span style="color:red;" />Amount cannot be empty</span>');
    }
    else
    //checks amount digits
    if(!is_numeric($amt))
    {
       echo('<b>Error:</b> <span style="color:red;" />Amount can only be numbers</span>');
    }
    
    
    
    // Retrieve the DOM from a given URL
    
    $folder = file_get_html("http://www.gocurrency.com/v2/dorate.php?inV=$amt&from=$cur_from&to=$cur_to&Calculate=Convert");
    
    //returns the converted currencies along with unit exchange rates for each currencies
    foreach ($folder->find('div[id=converter_results]') as $e){
        $main2 = array($e->childNodes(0)->outertext);
        $arr2[] = array(
                        'rate' => $main2,
                        );
    }
        
        
    $response = $arr2;
    
    //writes the result to json file
    $fp = fopen('results.json', 'w');
    fwrite($fp, json_encode($response));
    fclose($fp);
    
    
    $folder->clear();
    $homepage = file_get_contents('./feature5.html', false);
    echo $homepage."<br />";               
    
    ?>

<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/
libs/jquery/1.3.0/jquery.min.js"></script>
<!-- outputs the result and applies the CSS format to the output results -->
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

td[id *="1"]{color:#000;font-size:18px;font-stretch:semi-expanded;font-style:oblique;}
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
