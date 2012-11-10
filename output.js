$("document").ready(function() {
	var cell = "";
	$.getJSON("results.json", function(data) {
		$.each(data, function(i, item) {
			cell = "<a href=\""+item.profile+"\" target =\"_blank\">";	
				cell += "<div class=\"entry\">";    
					cell += "<div class=\"traveltype_box\">";
						cell += "<p>";
							cell += "<span class=\"icon\">";
              							cell += "<img class=\"icon\" alt=\"Travel Status\" src=\""+item.traveltypeicon+"\"/>";
							cell += "</span><br />";
							cell += "<strong class=\"traveltext\">"+item.traveltype+"</strong>";
							cell += "<span class=\"price\">"+item.price+"/seat</span>";
						cell += "</p>";
					cell += "</div>";
					cell += "<div class=\"userpic\">";
						cell += "<img alt=\"Profile Picture\" src=\""+item.image+"\"/>";
						cell += "<span class=\"passenger\"></span>";
					cell += "</div>";
					cell += "<div class=\"inner_content \">";
						cell += "<h3>";
							cell += "<span class=\"inner\"> "+item.from;
								cell += " <span class=\"travel_type\">&rarr;</span> "+item.to;
							cell += " </span>";
						cell += "</h3>";
					cell += "</div>";
					cell += "<h4 class=\"name\">"+item.name+"</h4>";
				cell += "</div>";
			cell += "</a>";

			$(".ride_list").append(cell);
        	});
	});
});
