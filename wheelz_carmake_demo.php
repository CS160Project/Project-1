<?php
    include('simple_html_dom.php');

    $articles = array();
 	getArticles('http://www.wheelz.com/vehicles/search?vehicle_search%5Baddress%5D=West+Los+Angeles,+CA');

	// URLs that will give 1 or more car postings
    // http://www.wheelz.com/vehicles/search?vehicle_search%5Baddress%5D=San+Francisco,+CA
    // http://www.wheelz.com/vehicles/search?vehicle_search%5Baddress%5D=Berkeley,+CA
	// http://www.wheelz.com/vehicles/search?vehicle_search%5Baddress%5D=Palo+Alto,+CA
	// http://www.wheelz.com/vehicles/search?vehicle_search%5Baddress%5D=Los+Angeles,+CA
	// http://www.wheelz.com/vehicles/search?vehicle_search%5Baddress%5D=West+Los+Angeles,+CA
	// http://www.wheelz.com/vehicles/search?vehicle_search%5Baddress%5D=Claremont,+CA

	function getArticles($page)
	{
		global $articles, $descriptions;

		$html = new simple_html_dom();
		$html->load_file($page);
		$items = $html->find('div[class=details]');

		foreach($items as $post)
		{
			$articles[] = array($post->children(0)->outertext);
		}

		if($next = $html->find('a[class=nextpostslink]', 0))
		{
			$URL = $next->href;
			echo "going on to $URL <<<\n";
			# memory leak clean up
			$html->clear();
			unset($html);
			getArticles($URL);
		}
	}
?>

<html>
<body>
    <h5>Type in a car make</h5>
	<div class='fieldRow'>
	<input class="location" id="vehicle_search_address" name="vehicle_search[address]" placeholder="Enter an address or location" size="30" type="text" value="Honda" />
	</div>
    <div id="main">
		<?php
   			 foreach($articles as $item)
   			 {
   			    if($item )
   			    {
   			  		echo $item[0];
   			  	}
   			 }
		?>
    </div>
</body>
</html>