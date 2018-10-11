/*Used by shortcode [church_admin_map]*/
function load(lat,lng,xml_url,zoom) {
        var myOptions = {
          center: new google.maps.LatLng(lat, lng),
          zoom: zoom,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map"),myOptions);


      // Change this depending on the name of your PHP file
      downloadUrl(xml_url, function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
        var smallgroup=new Array();

        for (var i = 0; i < markers.length; i++) {
          var infowindow;
		var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng"))
            );
      var pinColor =markers[i].getAttribute("pinColor");
      var id =markers[i].getAttribute("smallgroup_id");
      var details=markers[i].getAttribute("when")+ ' ' +markers[i].getAttribute("small_group_address");

	  if ( markers[i].getAttribute("adults_names")) var adults_names=markers[i].getAttribute("adults_names");
	  if ( markers[i].getAttribute("smallgroup_name"))var smallgroup_name=markers[i].getAttribute("smallgroup_names");
	  var childrens_names='';
	  if ( markers[i].getAttribute("childrens_names")) childrens_names='(' + markers[i].getAttribute("childrens_names")+')'+'<br/>';
	  var address=markers[i].getAttribute("address");
	  var information = adults_names + '<br/>'+ childrens_names + address;


      smallgroup[id]='<img src="https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + pinColor +'"/> '+markers[i].getAttribute("smallgroup_name") + ': <span class="ca-names">' + details +'</span><br/>';


      var pinImage = new google.maps.MarkerImage("https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
            new google.maps.Size(21, 34),
            new google.maps.Point(0,0),
            new google.maps.Point(10, 34));
		var pinShadow = new google.maps.MarkerImage("https://chart.apis.google.com/chart?chst=d_map_pin_shadow",
        new google.maps.Size(40, 37),
        new google.maps.Point(0, 0),
        new google.maps.Point(12, 35));
		function createMarker(information, point) {
       var marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: pinImage,
            shadow: pinShadow,
          });
       google.maps.event.addListener(marker, "click", function() {
         if (infowindow) infowindow.close();
         infowindow = new google.maps.InfoWindow({content:'<span style="color:black">'+ information+'</span>'});
         infowindow.open(map, marker);
       });
       return marker;
	    }
        var marker = createMarker(information, point);




    }
        var sg='<h2>Smallgroups</h2>';
        for(var index in smallgroup) { if(smallgroup[index]) sg= '<p>'+sg + smallgroup[index]+'</p>';}
        var container = document.getElementById("groups");
        container.innerHTML = sg ;


      });






    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}
}
