//used for address form drop and drag
jQuery(document).ready(function($){
	
	//create map at beginLat,beginLng
	console.log(beginLat + ' '+ beginLng);
	var map;
		var marker=null;
  	var latlng = new google.maps.LatLng(beginLat, beginLng);
  	var zoom=8;
  	if(beginLat!=51.50351129583287){zoom=17;}
  	var myOptions = {zoom: zoom,center: latlng,mapTypeId: google.maps.MapTypeId.ROADMAP     };
  	map = new google.maps.Map(document.getElementById("map"), myOptions); 
  	
  	if(beginLat!=51.50351129583287)
  	{//already geolocated so pop a marker on
  		var location = latlng;
      		console.log("Location "+location);
      		map.setCenter(location);
      		// clear previous markers
    		if(marker!=null)marker.setMap(null);
      		marker = new google.maps.Marker({
  				draggable: false,
  				position: location, 
  				map: map,
  				title: "Your location"
  			});
  			map.setZoom(17);//set the coordinates once map has centred
  			var coords = marker.getPosition();
  	
  	}
  	
	//look for click on #geocode_address
	$('body').on('click','#geocode_address',function(e){
		e.preventDefault();//don't reload page
		var geocoder = new google.maps.Geocoder();
  		var address = $('#address').val();//grab entered address
  		geocoder.geocode({'address' : address}, function(result, status){
      
      	if(status!='ZERO_RESULTS')
      	{// this returns a latlng
      
      		var location = result[0].geometry.location;
      		console.log("Location "+location);
      		map.setCenter(location);
      		// clear previous markers
    		if(marker!=null)marker.setMap(null);
      		marker = new google.maps.Marker({
  				draggable: true,
  				position: location, 
  				map: map,
  				title: "Your location"
  			});
  			map.setZoom(17);//set the coordinates once map has centred
  			var coords = marker.getPosition();
      		$("#lat").val(coords.lat());
     		$("#lng").val(coords.lng());
     		//listen for marker being dragged and set new coords
  			google.maps.event.addListener(marker,'dragend',function(overlay,point){
     			coords = marker.getPosition();
      			$("#lat").val(coords.lat());
     			$("#lng").val(coords.lng());
     		});
	  	
	  	}else{alert("Google maps couldn't find the address, please adjust and try again");}
            
    	});
 
  		

});

});