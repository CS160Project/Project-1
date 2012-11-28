jQuery.fn.sort = function()
{
	return this.pushStack([].sort.apply(this, arguments), []);
};

function sortPriceDescending(pFObject, pSObject)
{
    if (parseFloat(pFObject.price) == parseFloat(pSObject.price))
	{
		return 0;
	}  // if
	
    return parseFloat(pFObject.price) > parseFloat(pSObject.price) ? -1 : 1;
};  // sortPriceDescending

function sortPriceAscending(pFObject, pSObject)
{	
    if(parseFloat(pFObject.price) == parseFloat(pSObject.price))
	{
		return 0;
	}  // if
	
	return parseFloat(pFObject.price) > parseFloat(pSObject.price) ? 1 : -1;
};  // sortPriceAscending

function sortDistanceDescending(pFObject, pSObject)
{
    if(parseFloat(pFObject.distance) == parseFloat(pSObject.distance))
	{
		return 0;
	}  // if
	
    return parseFloat(pFObject.distance) > parseFloat(pSObject.distance) ? -1 : 1;
};  // sortDistanceDescending

function sortDistanceAscending(pFObject, pSObject)
{	
    if(parseFloat(pFObject.distance) == parseFloat(pSObject.distance))
	{
		return 0;
	}  // if
	
    return parseFloat(pFObject.distance) > parseFloat(pSObject.distance) ? 1 : -1;
};  // sortDistanceAscending

function loadJSON()
{
    $.getJSON("result.html", function(json) { filter(json);});
}  // loadJSON

function filter(pData)
{
	var lFilterYear = document.getElementById("cmbFilterYear").value;
	if(lFilterYear == "Default")
	{
		lFilterYear = "";
	}  // if
	var lFilterBrand = document.getElementById("cmbFilterBrand").value;
	if(lFilterBrand == "Default")
	{
		lFilterBrand = "";
	}  // if

	var lJsonObject = pData;
	
	var lSortOption = document.getElementById("cmbSort").value;

	var lObject = $(lJsonObject).sort(sortPriceAscending);
	
	switch(lSortOption)
	{
		case "Default Price -- Ascending":
		lObject = $(lJsonObject).sort(sortPriceAscending);
		break;
		
		case "Price -- Descending":
		lObject = $(lJsonObject).sort(sortPriceDescending);
		break;
		
		case "Distance -- Descending":
		lObject = $(lJsonObject).sort(sortDistanceDescending);
		break;

		case "Distance -- Ascending":
		lObject = $(lJsonObject).sort(sortDistanceAscending);
		break;
		
		default:
		break;
	}  // switch
	
	$(".contentlist").empty();

	for(var lIndex = 0; lIndex < lObject.length; lIndex++)
	{

		if((lObject[lIndex].year > lFilterYear) && (lObject[lIndex].brand == lFilterBrand || lFilterBrand == ""))
		{
			var lLI = document.createElement("li");
			$(".contentlist").append(lLI);
			$(lLI).append("<h> YEAR: <h>");
			$(lLI).append($("<h>", { text: lObject[lIndex].year }));
			$(lLI).append("<h> BRAND: <h>");
			$(lLI).append($("<h>", { text: lObject[lIndex].brand }));
			$(lLI).append("<h> PRICE: <h>");
			$(lLI).append($("<h>", { text: lObject[lIndex].price }));
			$(lLI).append("<h> DISTANCE: <h>");
			$(lLI).append($("<h>", {text: lObject[lIndex].distance}));
		}  // if
	}  // for
};


$(document).ready(function(){loadJSON();});
