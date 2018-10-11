// top of index.js
var controller;
var language;
var app = {
    // Application Constructor
    initialize: function() {
        if (navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/)) {
            document.addEventListener("deviceready", this.onDeviceReady, false);
        } else {
            this.onDeviceReady();
        }
    },

    onDeviceReady: function() {
    	$(function() {
    		FastClick.attach(document.body);
		});



	var storage = window.localStorage;
  
  var churchURL = window.localStorage.getItem('churchURL');


    controller = new Controller();

}
};

app.initialize();
