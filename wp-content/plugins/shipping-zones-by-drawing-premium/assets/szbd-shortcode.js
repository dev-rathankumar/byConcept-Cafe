jQuery(document).ready(function($) {
	if (typeof szbd_map !== 'undefined') {
		initialize();
		$('.szbdshortcode_id_title').html(szbd_map.title);
	}
});

function initialize() {
	var color = 	szbd_map.color !== '' ? szbd_map.color : '#53c853';
	var bounds;
	var interactive = szbd_map.interactive == 1 ? true : false;
	if( typeof szbd_map.maps[0] !== 'undefined'){
	var mapOptions = {
		center: new google.maps.LatLng(szbd_map.maps[0].lat, szbd_map.maps[0].lng),
		zoom: 15,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		disableDefaultUI: !interactive,
	};
	var new_map = new google.maps.Map(document.getElementById("szbdshortcode_id"), mapOptions);
	bounds = new google.maps.LatLngBounds();
	_.each(szbd_map.maps, function(map_, j) {
		var path = [];
		_.each(map_.array_latlng, function(map__, i) {
			path.push(new google.maps.LatLng(map__[0], map__[1]));
			bounds.extend(path[i]);
		});

		new google.maps.Polygon({
			map: new_map,
			paths: path,
			strokeColor: color,
			strokeOpacity: 0.35,
			//strokeWeight: 3,
			fillColor: color,
			fillOpacity: 0.35
		});

		new_map.fitBounds(bounds);
	});
}}
