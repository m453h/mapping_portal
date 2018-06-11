var map, plantationLayer, wardLayer, mapPerspective, mapDataType, lastZoomLevel;


$(document).ready(function() {


	$('.data-level-menu li a').click(function()
	{
		mapPerspective = $(this).attr('id');
		$(this).addClass('active');

		if(mapPerspective=="plantation")
		{
			$('#division').removeClass('active');
			$('#range').removeClass('active');
			$('#compartment').removeClass('active');

		}
		else if(mapPerspective=="division")
		{
				$('#plantation').removeClass('active');
				$('#range').removeClass('active');
				$('#compartment').removeClass('active');
		}
        else if(mapPerspective=="range")
        {
            $('#plantation').removeClass('active');
            $('#division').removeClass('active');
            $('#compartment').removeClass('active');
        }
		else if(mapPerspective=="compartment")
		{
					$('#plantation').removeClass('active');
					$('#range').removeClass('active');
					$('#division').removeClass('active');
		}

		//Clear the map and reload
		map.remove();

        renderMap('map-forestry','region_spatial_data',["region_name","level","region_code"],"country");
	});

	renderMap('map-forestry','region_spatial_data',["region_name","level","region_code"],"country");


});



function renderMap(mapContainerId, route,fields,layerLevel)
{
	
	if ($('#'+mapContainerId).length > 0)
	{
		mapDataType = mapContainerId;
		
		$('#'+mapContainerId).height($(window).height());

		map = L.map(mapContainerId, {
			center: L.latLng(-6.132, 35.092),
			zoom: 7
		});

		fetchMapData(route,null,fields,layerLevel);
	}
}
function fetchMapData(route,value,fields,layerLevel){
	$.ajax(Routing.generate(route), {
		data: {
			value: value
		},
		success: function(data){
			mapData(data,fields,layerLevel);
		},
		error: function(){
			alert('Failed');
		}

	})
}

function mapData(data,fields,layerLevel){

		if(layerLevel=='country')
		{
			//Remove existing map layers
			map.eachLayer(function (layer) {
				//If not the tile layer
				if (typeof layer._url === "undefined") {
					map.removeLayer(layer);
				}
			});
		}

		//Create GeoJSON container object
		var geoJSON = {
			"type": "FeatureCollection",
			"features": []
		};

		//Split data into features
		var dataArray = data.split(", ;");
		dataArray.pop();

		//build GeoJSON features
		dataArray.forEach(function (data) {
			//Split the data up into individual attribute values and the geometry
			data = data.split(", ");

			//feature object container
			var feature = {
				"type": "Feature",
				"properties": {}, //properties object container
				"geometry": JSON.parse(data[fields.length]) //parse geometry
			};

			for (var i = 0; i < fields.length; i++) {
				feature.properties[fields[i]] = data[i];
			}

			geoJSON.features.push(feature);
		});

		var mapDataLayer = L.geoJson(geoJSON, {
			pointToLayer: function (feature, point) {
				var markerStyle = {
					fillColor: "#CC9900",
					color: "#FFF",
					fillOpacity: 0.5,
					opacity: 0.8,
					weight: 1,
					radius: 8
				};

				return L.circleMarker(point, markerStyle);
			},
			onEachFeature: function (feature, layer) {

				layer.on({
					mouseover: highlightFeature,
					mouseout: resetHighlight,
					click: zoomToFeature
				});

				if(mapDataType=='map-forestry')
				{
					addMapForestryInformationLayer(layer, feature.properties);
				}

			}

		});

		if(layerLevel=='country')
		{
			mapDataLayer.addTo(map);
			map.fitBounds(mapDataLayer.getBounds());
		}
		else if(layerLevel=='region')
		{
			plantationLayer = mapDataLayer;
			plantationLayer.addTo(map);
		}
		else if(layerLevel=='constituency')
		{
			wardLayer = mapDataLayer;
			wardLayer.addTo(map);
		}
}

function highlightFeature(e) {
	var layer = e.target;
	var layerLevel = e.target.feature.properties.level;

	if(lastZoomLevel==layerLevel) {

        layer.setStyle({
            weight: 2,
            color: '#ffffff',
            dashArray: ''
            //fillOpacity: 0.4
        });

        //refreshChart(layer.feature.properties);
        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
            layer.bringToFront();
        }
    }

	//info.update(layer.feature.properties);
}

function resetHighlight(e) {

	var layer = e.target;
	var layerLevel = e.target.feature.properties.level;

	if(lastZoomLevel==layerLevel) {
       layer.setStyle({
           weight: 1,
           color: '#FFFFFF',
           dashArray: '',
           fillOpacity: 1,
           opacity: 1
       });

       if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
           layer.bringToFront();
       }
   }



	//info.update();
}

function zoomToFeature(e) {

	map.fitBounds(e.target.getBounds());

	var layerLevel = e.target.feature.properties.level;

	var value = null;
	var route = null;
	var fields = [];

	if(mapDataType=='map-forestry')
	{
		if(layerLevel=='region')
		{
			value = e.target.feature.properties.region_code;
			route = 'compartment_spatial_data';
			fields = ["compartment_name","level","compartment_code"];
		}

	}


	if(map.hasLayer(plantationLayer) && layerLevel=='region')
	{
		map.removeLayer(plantationLayer);
	}

	fetchMapData(route,value,fields,layerLevel);
}

function addMapForestryInformationLayer(layer,properties)
{
	for (var property in properties)
	{
		var html = "";
		var fillColor;
		var featureName = "";

		html += property + ": " + properties[property] + "<br>";

		if(typeof properties.region_name !== 'undefined')
		{
			featureName = properties.region_name;

            fillColor = "#CCCCCC";
            lastZoomLevel = 'region';
        }
		else if(typeof properties.compartment_name !== 'undefined')
		{
			featureName = properties.compartment_name;
			fillColor ="#008C31";
            lastZoomLevel = 'compartment';
        }

		layer.setStyle({
			fillColor: fillColor,
			color: '#ffffff',
			transparent: true,
			weight: 0.6,
			opacity: 1,
			fillOpacity: 1
		});

		/*layer.bindTooltip(featureName, {
			permanent: true,
			direction: 'auto',
			offset: [6, -6],
			zoomAnimation: true
		});*/

		layer.bindPopup(html);
	}
}

function addMapKey(properties)
{

    if(mapPerspective=='presidential')
    {
        if (properties.level == 'region')
        {
            $(".election-results-statistics>h3").hide().html("Summary of presidential election results in " + properties.region_name + " region").fadeIn('slow');
        }
        else if (properties.level == 'constituency')
        {
            $(".election-results-statistics>h3").hide().html("Summary of presidential election results in " + properties.constituency_name + " constituency").fadeIn('slow');
        }

        var data = jQuery.parseJSON(properties.results);

        var html = "";

        var total = 0;

        $.each(data, function( key, value ) {

            html+=
                "<div class='candidate-content'>" +
                "	<img src='/projects/nec/web/file_uploads/candidate_images/"+value.candidate_image+"' class='rounded-image-dark' id='meta' height='80'> " +
                "	<span class='image-title'>"+value.candidate_name+"</span>" +
                "	</div> " +
                "<div class='candidate-content'>" +
                "	<img src='/projects/nec/web/file_uploads/candidate_images/"+value.running_mate_image+"' class='rounded-image-dark' id='meta' height='80'> " +
                "	<span class='image-title'>"+value.running_mate_name+"</span>" +
                "	</div> " +
                "<div class='space-10'></div>" +
                "<div class='container'> " +
                "	<div class='"+value.abbreviation+"'>" +
                "	</div> " +
                "<div class='election-key-details'><span>Total Votes: "+value.total_votes+" ("+value.abbreviation+")</span></div>"+
                "</div>" +
                "<div class='col12 space-20'></div>";

            total += value.total_votes;
        });

        $(".replaceable").hide().html(html).fadeIn('slow');

        $.each(data, function( key, value ) {
            $("."+value.abbreviation).progressbar({"value": (value.total_votes/total)*100});
            $("."+value.abbreviation+">.ui-progressbar-value").css({'background': value.party_color});
        });

    }
    else if(mapPerspective=='parliamentary')
    {
        if (properties.level == 'region')
        {
            $(".election-results-statistics>h3").hide().html("Summary of parliamentary election results in " + properties.region_name + " region").fadeIn('slow');
        }
        else if (properties.level == 'constituency')
        {
            $(".election-results-statistics>h3").hide().html("Summary of parliamentary election results in " + properties.constituency_name + " constituency").fadeIn('slow');
        }

        var data = jQuery.parseJSON(properties.results);

        var html = "";

        var total = 0;

        if(data == null)
        {
            $(".replaceable").hide().html("").fadeIn('slow');
        }
        else
        {
            $.each(data, function (key, value) {
                if (properties.level == 'region')
                {
                    html +=
                        "<div class='candidate-content'>" +
                        "	<img src='/projects/nec/web/file_uploads/political_party_logo/" + value.party_logo + "' height='50'> " +
                        "	<span class='image-title'>" + value.party_name + " (" + value.abbreviation + ")</span>" +
                        "	</div> " +
                        "<div class='space-10'></div>" +
                        "<div class='container'> " +
                        "	<div class='" + value.abbreviation + "'>" +
                        "	</div> " +
                        "<div class='election-key-details'><span>Total Constituencies: (" + value.total_votes + ")</span></div>" +
                        "</div>" +
                        "<div class='col12 space-20'></div>";

                    total += value.total_votes;
                }
                else if(properties.level == 'constituency')
                {
                    html+=
                        "<div class='candidate-content'>" +
                        "	<img src='/projects/nec/web/file_uploads/candidate_images/"+value.candidate_image+"' class='rounded-image-dark' id='meta' height='80'> " +
                        "	<span class='image-title'>"+value.candidate_name+"</span>" +
                        "	</div> " +
                        "<div class='space-10'></div>" +
                        "<div class='container'> " +
                        "	<div class='"+value.abbreviation+"'>" +
                        "	</div> " +
                        "<div class='election-key-details'><span>Total Votes: "+value.total_votes+" ("+value.abbreviation+")</span></div>"+
                        "</div>" +
                        "<div class='col12 space-20'></div>";

                    total += value.total_votes;
                }

            });

            $(".replaceable").hide().html(html).fadeIn('slow');

            $.each(data, function (key, value) {
                $("." + value.abbreviation).progressbar({"value": (value.total_votes / total) * 100});
                $("." + value.abbreviation + ">.ui-progressbar-value").css({'background': value.party_color});
            });
        }

    }
    else if(mapPerspective=='municipality')
    {
        if (properties.level == 'region')
        {
            $(".election-results-statistics>h3").hide().html("Summary of municipality election results in " + properties.region_name + " region").fadeIn('slow');
        }
        else if (properties.level == 'constituency')
        {
            $(".election-results-statistics>h3").hide().html("Summary of municipality election results in " + properties.constituency_name + " constituency").fadeIn('slow');
        }

        var data = jQuery.parseJSON(properties.results);

        var html = "";

        var total = 0;

        if(data == null)
        {
            $(".replaceable").hide().html("").fadeIn('slow');
        }
        else
        {
            $.each(data, function (key, value) {
                if (properties.level == 'region')
                {
                    html +=
                        "<div class='candidate-content'>" +
                        "	<img src='/projects/nec/web/file_uploads/political_party_logo/" + value.party_logo + "' height='50'> " +
                        "	<span class='image-title'>" + value.party_name + " (" + value.abbreviation + ")</span>" +
                        "	</div> " +
                        "<div class='space-10'></div>" +
                        "<div class='container'> " +
                        "	<div class='" + value.abbreviation + "'>" +
                        "	</div> " +
                        "<div class='election-key-details'><span>Total Wards: (" + value.total_votes + ")</span></div>" +
                        "</div>" +
                        "<div class='col12 space-20'></div>";

                    total += value.total_votes;
                }
                else if (properties.level == 'constituency')
                {
                    html +=
                        "<div class='candidate-content'>" +
                        "	<img src='/projects/nec/web/file_uploads/political_party_logo/" + value.party_logo + "' height='50'> " +
                        "	<span class='image-title'>" + value.party_name + " (" + value.abbreviation + ")</span>" +
                        "	</div> " +
                        "<div class='space-10'></div>" +
                        "<div class='container'> " +
                        "	<div class='" + value.abbreviation + "'>" +
                        "	</div> " +
                        "<div class='election-key-details'><span>Total Wards: (" + value.total_votes + ")</span></div>" +
                        "</div>" +
                        "<div class='col12 space-20'></div>";

                    total += value.total_votes;
                }
                else if(properties.level == 'ward')
                {
                    html+=
                        "<div class='candidate-content'>" +
                        "	<img src='/projects/nec/web/file_uploads/candidate_images/"+value.candidate_image+"' class='rounded-image-dark' id='meta' height='80'> " +
                        "	<span class='image-title'>"+value.candidate_name+"</span>" +
                        "	</div> " +
                        "<div class='space-10'></div>" +
                        "<div class='container'> " +
                        "	<div class='"+value.abbreviation+"'>" +
                        "	</div> " +
                        "<div class='election-key-details'><span>Total Votes: "+value.total_votes+" ("+value.abbreviation+")</span></div>"+
                        "</div>" +
                        "<div class='col12 space-20'></div>";

                    total += value.total_votes;
                }

            });

            $(".replaceable").hide().html(html).fadeIn('slow');

            $.each(data, function (key, value) {
                $("." + value.abbreviation).progressbar({"value": (value.total_votes / total) * 100});
                $("." + value.abbreviation + ">.ui-progressbar-value").css({'background': value.party_color});
            });
        }

    }
}
