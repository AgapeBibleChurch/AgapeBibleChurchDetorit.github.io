var Controller = function() {


    var controller = {
        self: null,
        initialize: function() {
            self = this;
            this.bindEvents();
            var churchURL = 'https://www.churchadminplugin.com';

			var myaudio;
			console.log('Initializing fired');
			if(typeof(FCMPlugin)!=="undefined"&& churchURL)
			{
				console.log('FCMplugin is working')
				FCMPlugin.onNotification(function(data){
				console.log(data);
				switch(data.type)
				{
					case 'prayer':
						    var prayer_items=window.localStorage.getItem('prayer-badge');
								if(prayer_items!= null){prayer_items=parseInt(prayer_items)+1;}else{prayer_items=1;}
								console.log('Prayer Items '+ prayer_items);
								window.localStorage.setItem('prayer-badge', prayer_items);
								$("#page #prayer-badge").html(' ' +prayer_items);
								$("#page #prayer-badge").show();
								window.localStorage.setItem('Notification','prayer');

								cordova.plugins.notification.badge.increase(1, function (badge) {});
						break;
						case 'bible':
								var bible_items=window.localStorage.getItem('bible-badge');
								if(bible_items!= null){bible_items=parseInt(bible_items)+1;}else{bible_items=1;}
								console.log('Bible Items '+ bible_items);
								window.localStorage.setItem('bible-badge', bible_items);
								$("#page #bible-badge").html(' ' +bible_items);
							$("#page #bible-badge").show();
							window.localStorage.setItem('Notification','bible');
							cordova.plugins.notification.badge.increase(1, function (badge) {});
						break;
						case 'news':
							var news=window.localStorage.getItem('news');
								if(news!= null){news=parseInt(news)+1;}else{news=1;}
								console.log('News '+ news);
								window.localStorage.setItem('news', news);
								$("#page #news-badge").html(' ' +news);
								$("#page #news-badge").show();
								window.localStorage.setItem('Notification','news');
								cordova.plugins.notification.badge.increase(1, function (badge) {});
							break;
						}
						var language=JSON.parse(window.localStorage.getItem('language'));
						if(data.wasTapped)
						{//background
								console.log('Background type:'+data.type);
								//Notification was received on device tray and tapped by the user.
								switch(data.type)
								{
									case 'prayer':self.renderPrayerView();break;
									case 'bible':self.renderBibleView();break;
									case 'news':self.renderNewsView();break;
								}
						}else{
							switch(data.type)
							{
								case 'prayer':console.log('Prayer in foreground'); navigator.notification.confirm(language['view-new-prayer'],self.renderPrayerView,language['prayer-request']);break;
								case 'bible': navigator.notification.confirm(language['view-new-bible'],self.renderBibleView,'View bible reading');break;
								case 'news': navigator.notification.confirm(language['view-new-news'],self.renderPostsView,language['news']);break;
							}

						}
				});
			}
            if(churchURL=== null)
            {

            	this.languageSet;

            	self.renderChooseChurch();
            }
            else
            {

            	this.languageSet;
							self.renderHomeView();



        	}


        },

        languageSet:function(){
        	   //language translation

        	//language strings
        	var en = {"translation":"Bible Translation","version":"Bible Version","moderation-queue":"Submitted prayer requests  are put in a moderation queue before publishing","send-prayer-request":"Send a prayer request","sent":"Prayer Request Sent","send":"Send","prayer-request":"Prayer request","title":"Title","disconnected":"You are not connected to the internet","home": "Home","address": "Address","account": "Account","bible": "Bible","calendar":"Calendar","giving":"Giving","groups":"Groups","group-meeting":"Group Meeting","media":"Media","meeting":"Meeting","news":"News","prayer":"Prayer","rotas":"Rotas","rota":"Rota","please-login":"Please Login","login":"Login","logout":"Logout", "password":"Password","username":"Username","older-news":"Older News","enter":"Enter Username/Email","reset-password":"Reset Password","forgotten-password":"Forgotten Password","latest-news":"Latest News","reset":"Please enter your username or email to receive a password reset email","posted-by":"Posted by","posted":"Posted","search-address-list":"Search Address List","search":"Search","your-address":"Your Address", "people-in-your-household":"People in your household","add-someone":"Add someone","person-edit":"Person Edit","first-name":"First Name","last-name":"Last Name","email-addres":"Email Address","mobile":"Mobile","delete":"Delete","save":"Save","phone":"Phone","address":"Address","search-yield":"Your search yielded","no-results":"No results found, please try again","address-list":"Address List","who":"Who?","calendar-not-setup":"Church calendar is not yet set up","latest-sermons":"Latest Sermons","listen":"Listen to a sermon","rota-setup":"The Church rota hasn't been set up yet.","my-rota":"My rota","my-group":"My Group","change-date":"Change Date","bible-readings":"Bible Readings","expand":"Click passage to expand","language":"Change Language"};
 			var locale=window.localStorage.getItem('locale');

 			if(locale==null||locale==undefined){locale='en';}

 			//check locale is 2 characters long and lower case
 			if(locale.length==2 && locale==locale.toLowerCase()){var languageFile='app-'+locale+'.php';}
 			else
 			{
 				navigator.notification.alert("Language not recognised, sticking with English", null, null, "Close");
 				var languageFile='app-en.php';
 			}

    		if(navigator.onLine)
    		{

    			var languageUrl='https://www.churchadminplugin.com/'+languageFile;

    			$.getJSON(languageUrl,function(data){


        			window.localStorage.setItem('language',JSON.stringify(data));


        		})
        		.done(function() {
        			//adjust menu language
        			$("#page .languagespecificHTML").each(function(){
    						var language=JSON.parse(window.localStorage.getItem('language'));
    						$(this).html(language[$(this).data("text")]);
        			});

        		})
				.fail(function(jqXHR, textStatus, errorThrown)
				{
					window.localStorage.setItem('language',JSON.stringify(en));
					navigator.notification.alert("Can't download language at the moment, so sticking with English", null, null, "Close");
				})

    		}
    		else
    		{//not online, so default to English
    			navigator.notification.alert("Can't download language at the moment, so sticking with English", null, null, "Close");
    			window.localStorage.setItem('language',en);
    			//adjust menu language
    			$("#page .languagespecificHTML").each(function(){
    				var language=JSON.parse(window.localStorage.getItem('language'));
    				$(this).html(language[$(this).data("text")]);
        		});
    		}






        },
		bindEvents: function() {

        	$('#page #menu').on('click',function(){$('.menu').toggle()});

        	//Buttons and links rendered in the DOM
					$('#page .menu').on('click','.tab-button', this.onMenuClick);
					$('#page #rendered').on('click', '#add-prayer', this.addPrayer);
					$('#page #rendered').on('click', '.prayer-answered', this.prayerAnswered);
					$('#page #rendered').on('click', '.check-in', this.checkInClass);
          $('#page #rendered').on('click', '#classes', this.renderClassesView);
        	$('#page #rendered').on('click', '.newsItem', this.onNewsClick);
        	$('#page #rendered').on('click', '.sermon', this.onSermonClick);
        	$('#page #rendered').on('change', '#serviceSelect', this.onRotaSelect);
        	$('#page #rendered').on('change', '#dateSelect', this.onDateSelect);
        	$('#page #rendered').on('click', '#login',this.login);
        	$('#page #rendered').on('click', '#search', this.search);
        	$('#page #rendered').on('click', '#forgottenProcess', this.forgotten);
        	$('#page #rendered').on('click', '#forgotten',this.ForgottenView);
        	$('#firstRun #rendered').on('change', '.churchUrl',this.churchSave);
        	$('#firstRun #rendered').on('keyup', '#church',this.churchSelect);
        	//$('#firstRun #rendered').on('click', '#choose',this.churchSelect);
        	$('#page #rendered').on('click', '#logout',this.logout);
        	$('#page #rendered').on('click', '#myrota',this.myRota);
        	$('#page #rendered').on('click', '#mygroup',this.myGroup);

        	$('#page #rendered').on('click','.datepicker',this.onBibleClick );
        	$('#page #rendered').on('click','.people_edit',this.onPeopleEdit );
        	$('#page #rendered').on('click','.address_edit',this.onAddressEdit );
        	$('#page #rendered').on('click', '#save_address_edit',this.saveAddressEdit);
        	$('#page #rendered').on('click', '#save_people_edit',this.savePeopleEdit);
        	$('#page #rendered').on('click', '#send_prayer_request',this.sendPrayerRequest);
        	$('#page #rendered').on('click', '#delete_people',this.savePeopleDelete);
        	$('#page #rendered').on('click', '#languageSave',this.languageSave);
        	$('#page #rendered').on('click', '#bibleSave',this.bibleSave);
        	$('#page #rendered').on('click','#refresh',this.refresh);
        	$('#page').on('click', '#rendered',	function(){$('.menu').hide() });
        },
				prayerAnswered:function()
				{
					var prayerItemIndex=$(this).data('index');
					var currPrayerItems=JSON.parse(window.localStorage.getItem('prayer-list'+(new Date()).getDay()));
					var answered = currPrayerItems.splice(prayerItemIndex, 1);
					var currAnswered=JSON.parse(window.localStorage.getItem('answered-prayer'));
					var newAnswered=answered.concat(currAnswered);
					window.localStorage.setItem('answered-prayer',JSON.stringify(newAnswered));
					window.localStorage.setItem('prayer-list'+(new Date()).getDay(),JSON.stringify(currPrayerItems));
					self.renderMyPrayerView();
				},
				addPrayer:function()
				{
						console.log('AddPrayer function');
						var prayerItem=$('#prayer-list-item').val();
						var days = [];
					 	$('.days:checked').each(function() {

						       days.push($(this).val());
						     });


						var prayerForDay;
						$.each(days , function(index, value) {

								if(value)
								{
	  							prayerForDay=JSON.parse(window.localStorage.getItem('prayer-list'+value));

									if(prayerForDay){prayerForDay.push(prayerItem);}else{prayerForDay=[prayerItem];}
									window.localStorage.setItem('prayer-list'+value,JSON.stringify(prayerForDay));
								}
						});

						self.renderMyPrayerView();
				},
        setBadge: function()
        {
        	var news=parseInt(window.localStorage.getItem('news-badge'));
        	if(!news)news=0;
        	var bible_items=parseInt(window.localStorage.getItem('bible-badge'));
        	if(!bible_items)bible_items=0;
        	var prayer_items=parseInt(window.localStorage.getItem('prayer-badge'));
        	if(!prayer_items)prayer_items=0;
        	var total= news+bible_items+prayer_items;
        	console.log('Total items for badge' + total);
        	cordova.plugins.notification.badge.set(total);
        },
        render: function(){

        	var where=window.localStorage.getItem('notification');
        	console.log('render function:'+where);
        	switch(where)
        	{
        		case'news':self.renderPostsView();break;
        		case'bible':self.renderBibleView();break;
        		case'prayer':self.renderPrayerView();break;
        	}
        },
        onMenuClick: function(e) {
            var tab = $(this).data('tab');
           	$('.menu').hide();
            switch(tab)
            {
            	case'#classes':self.renderClassesView();break;
            	case'#home':self.renderHomeView();break;
            	case'#address':self.renderAddressView();break;
            	case'#media':self.renderMediaView();break;
            	case'#calendar':self.renderCalendarView();break;
            	case'#news':self.renderNewsView();break;
            	case'#smallgroup':self.renderGroupView();break;
            	case'#bible':self.renderBibleView();break;
            	case'#rota':self.renderRotaView(null);break;
            	case'#giving':self.renderGivingView();break;
            	case'#login':self.login();break;
							case'#myprayer':self.renderMyPrayerView();break;
            	case'#search':self.renderSearchView();break;
            	case'#refresh':self.refresh();break;
            	case'#prayer':self.renderPrayerView();break;
            	case'#logout':self.logout();break;
            	case'#settings':self.renderSettingsView();break;
            	case'#account':self.renderAccountView('&nbsp;');break;
            }
        },
        //on first run, users need to select which church they want the app to run with
        //This function saves that selection
        churchSave: function(){
        	var churchURL = $(this).val();
        	console.log('church url:'+churchURL);
        	var storage=window.localStorage;
        	var locale =$('#languageSelect').val();
        	storage.setItem('locale',locale);
        	self.languageSet();
        	if(churchURL!='')
        	{
        		//store churchURL in local storage
        		storage.setItem('churchURL', churchURL);
        		self.renderHomeView();


        	}
        	else
        	{
        		this.renderChooseChurch;
        	}
        },
        //On first run or after logout, pull down list of churches subscribed for the app and allow user to choose.
        renderChooseChurch: function(){
        	// Hide whatever page is currently shown.
			$('#page').hide();
			$('#firstRun').show();



        	var storage=window.localStorage;
        	this.languageSet;
        	//add languages drop down from latest set of languages
           	$.ajax({
  						url: "https://www.churchadminplugin.com/app-languages.php",
  						context: document.body
						}).done(function(result) {$( '#firstRun #rendered #languageSelect' ).append( result);
			});
		},
		churchSelect:function(){
        		var church=$('#firstRun #rendered #church').val();
        		console.log('Value: '+church);
        		var url='https://www.churchadminplugin.com/wp-admin/admin-ajax.php';
        		var action = 'ca_church_autocomplete';
        		var args={'action':action,'church':church}
        		$.ajax({
        			url:url,
        			data:args,
        			success: function (data){
  						console.log('Data:'+data);
						$( '#firstRun #rendered #church_url' ).html(data);
					},
					type: 'GET'
				});

        },
        languageSave:function(){

        	var locale =$('#page #rendered #languageSelect').val();

        	window.localStorage.setItem('locale',locale);

        	self.languageSet();
        	self.renderSettingsView();

        },
        bibleSave:function(){

        	var version =$('#page #rendered #versionSelect').val();
        	window.localStorage.setItem('version',version);
        	self.renderSettingsView();

        },
        checkInClass:function(){
        	var storage = window.localStorage;
            var token = storage.getItem('token');
            var churchURL = 'https://www.churchadminplugin.com';
        	var people_id=[];
        	var class_id=$(this).data("class-id");
        	var date=$("#dateChooser.class"+class_id).val();
        	$('.student.class'+class_id).each(function() {
    			people_id.push($(this).data("people-id"));
			});

			var args={ action: "ca_class_checkin", token:token,people_id:people_id,class_id:class_id , date:date};
			console.log(args);
			$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args, function(data)
           		{
           			console.log(data);
           			var language=JSON.parse(storage.getItem('language'));
           			if(data.error==='login required'){self.renderLoginView('account');}
           			else if(data.error==='You are not a class leader')
           			{
           			var html = '<h2>'+ language['checkin'] +'</h2>';
           				html= html + '<p>'+ language['not-class-leader']+'</p>';
           				html= html+'<p><button class="button"  id="classes">'+  language['classes'] +'</button> </p>';
           				$("#page #rendered").html(html);
           			}
           			else if(data.success==='true')
           			{
           				var html = '<h2>';
                  if(data.class_name)html=html+'"'+data.class_name+'" ';
                  html=html+ language['checked-in'] ;
                  if(data.date) html=html+' '+ data.date;
                  html=html+'<h2>';
           				html=html+'<p><button class="button"  id="classes">'+ language['classes'] +'</button> </p>';
           				$("#page #rendered").html(html);
           			}
           			else
           			{
           				var html = '<h2>'+ language['checkin'] +'<h2>';
           				html=html+'<p><button class="button"  id="classes">Huh?!</button> </p>';
           				$("#page #rendered").html(html);
           			}
           		});

        },
        //forgotten password form
         ForgottenView: function(data){
        	$('#firstRun').hide();
        	$('#page').show();
        	if(navigator.onLine)
            {
            	$('#title').html('Please login');
            	var html='<div id="content">';
            	var message=data.error;
            	if(message === undefined) var message=data.message;
            	if(message === undefined) var message=' ';
            	html= html +message;
            	html= html + '<p  class="languagespecificHTML" data-text="reset">Please enter your username or email to receive a password reset email</p>';
            	html=html+'<input id="user_login" type="text" placeholder="Enter Username/Email" autocorrect="off" autocapitalize="none" class="languagespecificPLACEHOLDER" data-text="enter"/>';
            	html=html+'<button class="ui-btn" data-tab="#forgottenProcess" id="forgottenProcess"><span class="languagespecificHTML" data-text="reset-password">Reset Password</button></div>';
        	}
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        	}
        	$("#page #rendered").html(html);

        },
        //send of forgotten password data
        forgotten: function(){
			$('#firstRun').hide();
        	$('#page').show();
        	var user_login = $('#user_login').val();

        	var data={'error':'Please enter a value'};
        	if(user_login===''){self.ForgottenView(data);}//no value entered
        	else
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		var args={ action: "ca_forgotten_password", user_login: user_login };
        		var storage=window.localStorage;
        		var churchURL = 'https://www.churchadminplugin.com';
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processForgotten);
        		function processForgotten(data)
        		{

        			if(data.error!=''){self.ForgottenView(data);}//error given
        			else
        			{
        				var html= data.message;
        				$("#page #rendered").html(html);

        			}

        		}

        	}

        },
        refresh: function(){
        	$('#firstRun').hide();
        	$('#page').show();

        	window.localStorage.setItem('downloaded', 0);
        	var where = $(this).data('tab');
        	switch(where)
        	{
        		case 'home':self.renderHomeView();break;
        		case 'giving':self.renderGivingView();break;
        		case 'group':self.renderGroupView();break;
        		case 'bible':self.renderBibleView();break;
        		case 'calendar':self.renderCalendarView();break;
        		default: self.renderHomeView();break;
        	}
        },
        search: function(){


        	var search = $('#s').val();
        	self.renderSearchView(search);

        },
        login: function(){

			$('#firstRun').hide();
        	$('#page').show();

        	if(navigator.onLine)
        	{
        		var storage = window.localStorage;
        		//reset badges
        		storage.setItem('news', 0);
        		storage.setItem('prayer-badge', 0);
        		$("#page #prayer-badge").hide();
        		$("#page #news-badge").hide();
        		cordova.plugins.notification.badge.clear();

        		var username = $('#username').val();
        		var password = $('#password').val();
        		var whereNext = $('#whereNext').val();
        		var token = $.md5(username+ $.md5(password));

				storage.setItem('token', token);
				var args={ action: "ca_login", username: username,password: password,UUID:token };
        		var churchURL = 'https://www.churchadminplugin.com';
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processLogin);
        		function processLogin(data)
        		{

        			if(data.login== true) {
        				switch(whereNext)
        				{
        					case'address': 		self.renderAddressView();break;
        					case'classes':		self.renderClassesView();break;
        					case'prayer': 		self.renderPrayerView();break;
        					case'mygroup': 		self.renderMyGroupView();break;
        					case'myrota': 		self.renderMyRotaView();break;
                  case'rota': 		self.renderRotaView();break;
        					case'account': 		self.renderAccountView();break;
        					default:self.renderAddressView();break;
        				}
        			}
        			else{self.renderLoginView(whereNext);}

        		}
        	}
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}

        },
        logout: function(e){
        	//unsubscribe from push notifications
        	var church_id=window.localStorage.getItem('church_id');
        	FCMPlugin.unsubscribeFromTopic('church'+ church_id);
        	window.localStorage.removeItem('churchURL');//clear local storage
        	window.localStorage.removeItem('church_id');
        	window.localStorage.removeItem('token');
        	window.localStorage.removeItem('downloaded');
        	self.renderChooseChurch();

        },
        myRota: function(e){
        	if(navigator.onLine)
        	{
        		self.renderMyRotaView();
        	}
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
        },
        myGroup: function(e){
        	if(navigator.onLine)
        	{
        		self.renderMyGroupView();
        	}
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
        },
        onDateSelect: function(e) {
			//show individual blog post
			e.preventDefault();
			var date =$('#dateSelect').val();

			self.renderCalendarView(date);
		},
        onRotaSelect: function(e) {
			//show individual blog post
			e.preventDefault();
			var rota_id =$('#serviceSelect').val();

			self.renderRotaView(rota_id);
		},
        onSermonClick: function(e) {
			//play individual sermon
			e.preventDefault();
			var ID =$(this).data('tab');
			if(navigator.onLine)
        	{
				self.renderSermonView(ID);
			}
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
		onNewsClick: function(e) {
			//show individual blog post
			e.preventDefault();
			var ID =$(this).data('tab');
			if(navigator.onLine)
        	{
				self.renderPostView(ID);
			}
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
		onPeopleEdit: function() {
			//show individual blog post

			if(navigator.onLine)
        	{
				var people_id =$(this).data('tab');
				self.renderPeopleEditView(people_id);
			}
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
		savePeopleDelete:function(){


			var storage = window.localStorage;
            var token = storage.getItem('token');
			var people_id= $('#people_id').val();
			var first_name = $('#first_name').val();
			var last_name = $('#last_name').val();
			var email = $('#email').val();
			var mobile = $('#mobile').val();
			var args={ action: "ca_delete_people", token:token, people_id:people_id };

        	var churchURL = 'https://www.churchadminplugin.com';
        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processDeletePeople);
        		function processDeletePeople(data)
        		{
        			if(data.error==='login required'){self.renderLoginView('account');}
           			else{
           				self.renderAccountView(first_name + ' deleted');
           			}
        		}
        	}
        	else
        	{
        		var language=JSON.parse(storage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
		savePeopleEdit:function(){


			var storage = window.localStorage;
            var token = storage.getItem('token');
			var people_id= $('#people_id').val();
			var first_name = $('#first_name').val();
			var last_name = $('#last_name').val();
			var email = $('#email').val();
			var mobile = $('#mobile').val();
			var args={ action: "ca_save_people_edit", token:token,first_name:first_name,last_name:last_name,mobile:mobile,email:email, people_id:people_id };

        	var churchURL = 'https://www.churchadminplugin.com';
        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processSavePeople);
        		function processSavePeople(data)
        		{
        			if(data.error==='login required'){self.renderLoginView('account');}
           			else{
           				self.renderAccountView(first_name + ' saved');
           			}
        		}
        	}
        	else
        	{
        		var language=JSON.parse(storage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
		sendPrayerRequest:function(){
			var storage = window.localStorage;
            var token = storage.getItem('token');
			var prayer_request= $('#prayer-request').val();
			var prayer_title= $('#prayer-title').val();
			var churchURL = 'https://www.churchadminplugin.com';

			var args={action: "ca_send_prayer_request", token:token,content:prayer_request,prayer_title:prayer_title};

        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processSendPrayerRequest);
        		function processSendPrayerRequest(data)
        		{
        			if(data.error==='login required'){self.renderLoginView('prayer');}
           			else{
           				self.renderPrayerView('Sent');
           			}
        		}
        	}
        	else
        	{
        		var language=JSON.parse(storage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
		onAddressEdit: function() {
			//show individual blog post

			if(navigator.onLine)
        	{
				var household_id =$(this).data('tab');
				self.renderAddressEditView(household_id);
			}
        	else
        	{
				var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
		saveAddressEdit:function(){


			var storage = window.localStorage;
            var token = storage.getItem('token');
			var household_id= $('#household_id').val();
			var address = $('#address').val();
			var phone = $('#phone').val();

			var args={ action: "ca_save_address_edit", token:token,address:address,phone,phone, household_id:household_id };
			if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		var churchURL = 'https://www.churchadminplugin.com';
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processSaveAddress);
        		function processSaveAddress(data)
        		{
        			if(data.error==='login required'){self.renderLoginView('account');}
           			else{
           				self.renderAccountView('Address saved');
           			}
        		}
        	}
        	else
        	{
        		var language=JSON.parse(storage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
		},
        onBibleClick:function(e){
        	//date picker
			e.preventDefault();
			var options = {
    			date: new Date(),
    			mode: 'date'
			};
			datePicker.show(options, dateonSuccess, dateonError);
			function dateonSuccess(date) {
    			self.renderBibleView(date);

			}

			function dateonError(error) { // Android only
    			alert('Error: ' + error);
			}

        },
        renderSettingsView: function(){
        	var storage = window.localStorage;
        	var language=JSON.parse(storage.getItem('language'));
        	var html='<h2>'+ language['account']+'</h2>';
					//Language
           		    html=html+'<h3>'+ language['language']+'</h3>';
        			html= html+'<p><select class="tab-button" data-tab="#language" id="languageSelect">';
        			html= html+'<option value="en">English</option>';
        			html=html+'</select></p>';
        			html=html+'<p><button class="button" data-tab="#languageSave" id="languageSave">'+language['save']+'</button></p>';
           			//Bible version
           			var version=storage.getItem('versionName');
           			html=html+'<h3>'+ language['translation']+'</h3>';
           			if(version){html= html + language['version']+ ': '+ version + '</p>';}
           			html= html+'<p><select class="tab-button" data-tab="#version" id="versionSelect">';
           			var version=storage.getItem('version');
           			if(version)html=html+'<option value="'+version+'">'+version+'<option>';
           			html=html+'<option class="spacer" value="SCH2000">&nbsp;</option><option class="lang" value="KJ21">—English (EN)—</option><option value="KJ21">21st Century King James Version (KJ21)</option><option value="ASV">American Standard Version (ASV)</option><option value="AMP">Amplified Bible (AMP)</option><option value="AMPC">Amplified Bible, Classic Edition (AMPC)</option><option value="BRG">BRG Bible (BRG)</option><option value="CSB">Christian Standard Bible (CSB)</option><option value="CEB">Common English Bible (CEB)</option><option value="CJB">Complete Jewish Bible (CJB)</option><option value="CEV">Contemporary English Version (CEV)</option><option value="DARBY">Darby Translation (DARBY)</option><option value="DLNT">Disciples’ Literal New Testament (DLNT)</option><option value="DRA">Douay-Rheims 1899 American Edition (DRA)</option><option value="ERV">Easy-to-Read Version (ERV)</option><option value="ESV">English Standard Version (ESV)</option><option value="ESVUK">English Standard Version Anglicised (ESVUK)</option><option value="EXB">Expanded Bible (EXB)</option><option value="GNV">1599 Geneva Bible (GNV)</option><option value="GW">GOD’S WORD Translation (GW)</option><option value="GNT">Good News Translation (GNT)</option><option value="HCSB">Holman Christian Standard Bible (HCSB)</option><option value="ICB">International Children’s Bible (ICB)</option><option value="ISV">International Standard Version (ISV)</option><option value="PHILLIPS">J.B. Phillips New Testament (PHILLIPS)</option><option value="JUB">Jubilee Bible 2000 (JUB)</option><option value="KJV">King James Version (KJV)</option><option value="AKJV">Authorized (King James) Version (AKJV)</option><option value="LEB">Lexham English Bible (LEB)</option><option value="TLB">Living Bible (TLB)</option><option value="MSG">The Message (MSG)</option><option value="MEV">Modern English Version (MEV)</option><option value="MOUNCE">Mounce Reverse-Interlinear New Testament (MOUNCE)</option><option value="NOG">Names of God Bible (NOG)</option><option value="NABRE">New American Bible (Revised Edition) (NABRE)</option><option value="NASB">New American Standard Bible (NASB)</option><option value="NCV">New Century Version (NCV)</option><option value="NET">New English Translation (NET Bible)</option><option value="NIRV">New International Readers Version (NIRV)</option><option value="NIV">New International Version (NIV)</option><option value="NIVUK">New International Version - UK (NIVUK)</option><option value="NKJV">New King James Version (NKJV)</option><option value="NLV">New Life Version (NLV)</option><option value="NLT">New Living Translation (NLT)</option><option value="NMB">New Matthew Bible (NMB)</option><option value="NRSV">New Revised Standard Version (NRSV)</option><option value="NRSVA">New Revised Standard Version, Anglicised (NRSVA)</option><option value="NRSVACE">New Revised Standard Version, Anglicised Catholic Edition (NRSVACE)</option><option value="NRSVCE">New Revised Standard Version Catholic Edition (NRSVCE)</option><option value="NTE">New Testament for Everyone (NTE)</option><option value="OJB">Orthodox Jewish Bible (OJB)</option><option value="TPT">The Passion Translation (TPT)</option><option value="RSV">Revised Standard Version (RSV)</option><option value="RSVCE">Revised Standard Version Catholic Edition (RSVCE)</option><option value="TLV">Tree of Life Version (TLV)</option><option value="VOICE">The Voice (VOICE)</option><option value="WEB">World English Bible (WEB)</option><option value="WE">Worldwide English (New Testament) (WE)</option><option value="WYC">Wycliffe Bible (WYC)</option><option value="YLT">Youngs Literal Translation (YLT)</option><option class="lang" value="AMU">—Amuzgo de Guerrero (AMU)—</option><option value="AMU">Amuzgo de Guerrero (AMU)</option><option class="spacer" value="AMU">&nbsp;</option><option class="lang" value="ERV-AR">—العربية (AR)—</option><option value="ERV-AR">Arabic Bible: Easy-to-Read Version (ERV-AR)</option><option value="NAV">Ketab El Hayat (NAV)</option><option class="spacer" value="NAV">&nbsp;</option><option class="lang" value="ERV-AWA">—अवधी (AWA)—</option><option value="ERV-AWA">Awadhi Bible: Easy-to-Read Version (ERV-AWA)</option><option class="spacer" value="ERV-AWA">&nbsp;</option><option class="lang" value="BG1940">—Български (BG)—</option><option value="BG1940">1940 Bulgarian Bible (BG1940)</option><option value="BULG">Bulgarian Bible (BULG)</option><option value="ERV-BG">Bulgarian New Testament: Easy-to-Read Version (ERV-BG)</option><option value="CBT">Библия, нов превод от оригиналните езици (с неканоничните книги) (CBT)</option><option value="BOB">Библия, синодално издание (BOB)</option><option value="BPB">Библия, ревизирано издание (BPB)</option><option class="spacer" value="BPB">&nbsp;</option><option class="lang" value="CCO">—Chinanteco de Comaltepec (CCO)—</option><option value="CCO">Chinanteco de Comaltepec (CCO)</option><option class="spacer" value="CCO">&nbsp;</option><option class="lang" value="APSD-CEB">—Cebuano (CEB)—</option><option value="APSD-CEB">Ang Pulong Sa Dios (APSD-CEB)</option><option class="spacer" value="APSD-CEB">&nbsp;</option><option class="lang" value="CHR">—ᏣᎳᎩ ᎦᏬᏂᎯᏍ (CHR)—</option><option value="CHR">Cherokee New Testament (CHR)</option><option class="spacer" value="CHR">&nbsp;</option><option class="lang" value="CKW">—Cakchiquel Occidental (CKW)—</option><option value="CKW">Cakchiquel Occidental (CKW)</option><option class="spacer" value="CKW">&nbsp;</option><option class="lang" value="B21">—Čeština (CS)—</option><option value="B21">Bible 21 (B21)</option><option value="SNC">Slovo na cestu (SNC)</option><option class="spacer" value="SNC">&nbsp;</option><option class="lang" value="BWM">—Cymraeg (CY)—</option><option value="BWM">Beibl William Morgan (BWM)</option><option class="spacer" value="BWM">&nbsp;</option><option class="lang" value="BPH">—Dansk (DA)—</option><option value="BPH">Bibelen på hverdagsdansk (BPH)</option><option value="DN1933">Dette er Biblen på dansk (DN1933)</option><option class="spacer" value="DN1933">&nbsp;</option><option class="lang" value="HOF">—Deutsch (DE)—</option><option value="HOF">Hoffnung für Alle (HOF)</option><option value="LUTH1545">Luther Bibel 1545 (LUTH1545)</option><option value="NGU-DE">Neue Genfer Übersetzung (NGU-DE)</option><option value="SCH1951">Schlachter 1951 (SCH1951)</option><option value="SCH2000">Schlachter 2000 (SCH2000)</option><option class="spacer" value="YLT">&nbsp;</option><option class="lang" value="LBLA">—Español (ES)—</option><option value="LBLA">La Biblia de las Américas (LBLA)</option><option value="DHH">Dios Habla Hoy (DHH)</option><option value="JBS">Jubilee Bible 2000 (Spanish) (JBS)</option><option value="NBD">Nueva Biblia al Día (NBD)</option><option value="NBLH">Nueva Biblia Latinoamericana de Hoy (NBLH)</option><option value="NTV">Nueva Traducción Viviente (NTV)</option><option value="NVI">Nueva Versión Internacional (NVI)</option><option value="CST">Nueva Versión Internacional (Castilian) (CST)</option><option value="PDT">Palabra de Dios para Todos (PDT)</option><option value="BLP">La Palabra (España) (BLP)</option><option value="BLPH">La Palabra (Hispanoamérica) (BLPH)</option><option value="RVA-2015">Reina Valera Actualizada (RVA-2015)</option><option value="RVC">Reina Valera Contemporánea (RVC)</option><option value="RVR1960">Reina-Valera 1960 (RVR1960)</option><option value="RVR1977">Reina Valera 1977 (RVR1977)</option><option value="RVR1995">Reina-Valera 1995 (RVR1995)</option><option value="RVA">Reina-Valera Antigua (RVA)</option><option value="SRV-BRG">Spanish Blue Red and Gold Letter Edition (SRV-BRG)</option><option value="TLA">Traducción en lenguaje actual (TLA)</option><option class="spacer" value="TLA">&nbsp;</option><option class="lang" value="R1933">—Suomi (FI)—</option><option value="R1933">Raamattu 1933/38 (R1933)</option><option class="spacer" value="R1933">&nbsp;</option><option class="lang" value="BDS">—Français (FR)—</option><option value="BDS">La Bible du Semeur (BDS)</option><option value="LSG">Louis Segond (LSG)</option><option value="NEG1979">Nouvelle Edition de Genève – NEG1979 (NEG1979)</option><option value="SG21">Segond 21 (SG21)</option><option class="spacer" value="SG21">&nbsp;</option><option class="lang" value="TR1550">—Κοινη (GRC)—</option><option value="TR1550">1550 Stephanus New Testament (TR1550)</option><option value="WHNU">1881 Westcott-Hort New Testament (WHNU)</option><option value="TR1894">1894 Scrivener New Testament (TR1894)</option><option value="SBLGNT">SBL Greek New Testament (SBLGNT)</option><option class="spacer" value="SBLGNT">&nbsp;</option><option class="lang" value="HHH">—עברית (HE)—</option><option value="HHH">Habrit Hakhadasha/Haderekh (HHH)</option><option value="WLC">The Westminster Leningrad Codex (WLC)</option><option class="spacer" value="WLC">&nbsp;</option><option class="lang" value="ERV-HI">—हिन्दी (HI)—</option><option value="ERV-HI">Hindi Bible: Easy-to-Read Version (ERV-HI)</option><option class="spacer" value="ERV-HI">&nbsp;</option><option class="lang" value="HLGN">—Ilonggo (HIL)—</option><option value="HLGN">Ang Pulong Sang Dios (HLGN)</option><option class="spacer" value="HLGN">&nbsp;</option><option class="lang" value="HNZ-RI">—Hrvatski (HR)—</option><option value="HNZ-RI">Hrvatski Novi Zavjet – Rijeka 2001 (HNZ-RI)</option><option value="CRO">Knijga O Kristu (CRO)</option><option class="spacer" value="CRO">&nbsp;</option><option class="lang" value="HCV">—Kreyòl ayisyen (HT)—</option><option value="HCV">Haitian Creole Version (HCV)</option><option class="spacer" value="HCV">&nbsp;</option><option class="lang" value="KAR">—Magyar (HU)—</option><option value="KAR">Hungarian Károli (KAR)</option><option value="ERV-HU">Hungarian Bible: Easy-to-Read Version (ERV-HU)</option><option value="NT-HU">Hungarian New Translation (NT-HU)</option><option class="spacer" value="NT-HU">&nbsp;</option><option class="lang" value="HWP">—Hawai‘i Pidgin (HWC)—</option><option value="HWP">Hawai‘i Pidgin (HWP)</option><option class="spacer" value="HWP">&nbsp;</option><option class="lang" value="ICELAND">—Íslenska (IS)—</option><option value="ICELAND">Icelandic Bible (ICELAND)</option><option class="spacer" value="ICELAND">&nbsp;</option><option class="lang" value="BDG">—Italiano (IT)—</option><option value="BDG">La Bibbia della Gioia (BDG)</option><option value="CEI">Conferenza Episcopale Italiana (CEI)</option><option value="LND">La Nuova Diodati (LND)</option><option value="NR1994">Nuova Riveduta 1994 (NR1994)</option><option value="NR2006">Nuova Riveduta 2006 (NR2006)</option><option class="spacer" value="NR2006">&nbsp;</option><option class="lang" value="JLB">—日本語 (JA)—</option><option value="JLB">Japanese Living Bible (JLB)</option><option class="spacer" value="JLB">&nbsp;</option><option class="lang" value="JAC">—Jacalteco, Oriental (JAC)—</option><option value="JAC">Jacalteco, Oriental (JAC)</option><option class="spacer" value="JAC">&nbsp;</option><option class="lang" value="KEK">—Kekchi (KEK)—</option><option value="KEK">Kekchi (KEK)</option><option class="spacer" value="KEK">&nbsp;</option><option class="lang" value="KLB">—한국어 (KO)—</option><option value="KLB">Korean Living Bible (KLB)</option><option class="spacer" value="KLB">&nbsp;</option><option class="lang" value="VULGATE">—Latina (LA)—</option><option value="VULGATE">Biblia Sacra Vulgata (VULGATE)</option><option class="spacer" value="VULGATE">&nbsp;</option><option class="lang" value="MAORI">—Māori (MI)—</option><option value="MAORI">Maori Bible (MAORI)</option><option class="spacer" value="MAORI">&nbsp;</option><option class="lang" value="MNT">—Македонски (MK)—</option><option value="MNT">Macedonian New Testament (MNT)</option><option class="spacer" value="MNT">&nbsp;</option><option class="lang" value="ERV-MR">—मराठी (MR)—</option><option value="ERV-MR">Marathi Bible: Easy-to-Read Version (ERV-MR)</option><option class="spacer" value="ERV-MR">&nbsp;</option><option class="lang" value="MVC">—Mam, Central (MVC)—</option><option value="MVC">Mam, Central (MVC)</option><option class="spacer" value="MVC">&nbsp;</option><option class="lang" value="MVJ">—Mam, Todos Santos (MVJ)—</option><option value="MVJ">Mam de Todos Santos Chuchumatán (MVJ)</option><option class="spacer" value="MVJ">&nbsp;</option><option class="lang" value="REIMER">—Plautdietsch (NDS)—</option><option value="REIMER">Reimer 2001 (REIMER)</option><option class="spacer" value="REIMER">&nbsp;</option><option class="lang" value="ERV-NE">—नेपाली (NE)—</option><option value="ERV-NE">Nepali Bible: Easy-to-Read Version (ERV-NE)</option><option class="spacer" value="ERV-NE">&nbsp;</option><option class="lang" value="NGU">—Náhuatl de Guerrero (NGU)—</option><option value="NGU">Náhuatl de Guerrero (NGU)</option><option class="spacer" value="NGU">&nbsp;</option><option class="lang" value="HTB">—Nederlands (NL)—</option><option value="HTB">Het Boek (HTB)</option><option class="spacer" value="HTB">&nbsp;</option><option class="lang" value="DNB1930">—Norsk (NO)—</option><option value="DNB1930">Det Norsk Bibelselskap 1930 (DNB1930)</option><option value="LB">En Levende Bok (LB)</option><option class="spacer" value="LB">&nbsp;</option><option class="lang" value="ERV-OR">—ଓଡ଼ିଆ (OR)—</option><option value="ERV-OR">Oriya Bible: Easy-to-Read Version (ERV-OR)</option><option class="spacer" value="ERV-OR">&nbsp;</option><option class="lang" value="ERV-PA">—ਪੰਜਾਬੀ (PA)—</option><option value="ERV-PA">Punjabi Bible: Easy-to-Read Version (ERV-PA)</option><option class="spacer" value="ERV-PA">&nbsp;</option><option class="lang" value="NP">—Polski (PL)—</option><option value="NP">Nowe Przymierze (NP)</option><option value="SZ-PL">Słowo Życia (SZ-PL)</option><option value="UBG">Updated Gdańsk Bible (UBG)</option><option class="spacer" value="UBG">&nbsp;</option><option class="lang" value="NBTN">—Nawat (PPL)—</option><option value="NBTN">Ne Bibliaj Tik Nawat (NBTN)</option><option class="spacer" value="NBTN">&nbsp;</option><option class="lang" value="ARC">—Português (PT)—</option><option value="ARC">Almeida Revista e Corrigida 2009 (ARC)</option><option value="NTLH">Nova Traduҫão na Linguagem de Hoje 2000 (NTLH)</option><option value="NVI-PT">Nova Versão Internacional (NVI-PT)</option><option value="OL">O Livro (OL)</option><option value="VFL">Portuguese New Testament: Easy-to-Read Version (VFL)</option><option class="spacer" value="VFL">&nbsp;</option><option class="lang" value="MTDS">—Quichua (QU)—</option><option value="MTDS">Mushuj Testamento Diospaj Shimi (MTDS)</option><option class="spacer" value="MTDS">&nbsp;</option><option class="lang" value="QUT">—Quiché, Centro Occidenta (QUT)—</option><option value="QUT">Quiché, Centro Occidental (QUT)</option><option class="spacer" value="QUT">&nbsp;</option><option class="lang" value="RMNN">—Română (RO)—</option><option value="RMNN">Cornilescu 1924 - Revised 2010, 2014 (RMNN)</option><option value="NTLR">Nouă Traducere În Limba Română (NTLR)</option><option class="spacer" value="NTLR">&nbsp;</option><option class="lang" value="NRT">—Русский (RU)—</option><option value="NRT">New Russian Translation (NRT)</option><option value="CARS">Священное Писание (Восточный Перевод) (CARS)</option><option value="CARST">Священное Писание (Восточный перевод), версия для Таджикистана (CARST)</option><option value="CARSA">Священное Писание (Восточный перевод), версия с «Аллахом» (CARSA)</option><option value="ERV-RU">Russian New Testament: Easy-to-Read Version (ERV-RU)</option><option value="RUSV">Russian Synodal Version (RUSV)</option><option class="spacer" value="RUSV">&nbsp;</option><option class="lang" value="NPK">—Slovenčina (SK)—</option><option value="NPK">Nádej pre kazdého (NPK)</option><option class="spacer" value="NPK">&nbsp;</option><option class="lang" value="SOM">—Somali (SO)—</option><option value="SOM">Somali Bible (SOM)</option><option class="spacer" value="SOM">&nbsp;</option><option class="lang" value="ALB">—Shqip (SQ)—</option><option value="ALB">Albanian Bible (ALB)</option><option class="spacer" value="ALB">&nbsp;</option><option class="lang" value="ERV-SR">—Српски (SR)—</option><option value="ERV-SR">Serbian New Testament: Easy-to-Read Version (ERV-SR)</option><option class="spacer" value="ERV-SR">&nbsp;</option><option class="lang" value="SVL">—Svenska (SV)—</option><option value="SVL">Nya Levande Bibeln (SVL)</option><option value="SV1917">Svenska 1917 (SV1917)</option><option value="SFB">Svenska Folkbibeln (SFB)</option><option value="SFB15">Svenska Folkbibeln 2015 (SFB15)</option><option class="spacer" value="SFB15">&nbsp;</option><option class="lang" value="SNT">—Kiswahili (SW)—</option><option value="SNT">Neno: Bibilia Takatifu (SNT)</option><option class="spacer" value="SNT">&nbsp;</option><option class="lang" value="ERV-TA">—தமிழ் (TA)—</option><option value="ERV-TA">Tamil Bible: Easy-to-Read Version (ERV-TA)</option><option class="spacer" value="ERV-TA">&nbsp;</option><option class="lang" value="TNCV">—ภาษาไทย (TH)—</option><option value="TNCV">Thai New Contemporary Bible (TNCV)</option><option value="ERV-TH">Thai New Testament: Easy-to-Read Version (ERV-TH)</option><option class="spacer" value="ERV-TH">&nbsp;</option><option class="lang" value="FSV">—Tagalog (TL)—</option><option value="FSV">Ang Bagong Tipan: Filipino Standard Version (FSV)</option><option value="ABTAG1978">Ang Biblia (1978) (ABTAG1978)</option><option value="ABTAG2001">Ang Biblia, 2001 (ABTAG2001)</option><option value="ADB1905">Ang Dating Biblia (1905) (ADB1905)</option><option value="SND">Ang Salita ng Diyos (SND)</option><option value="MBBTAG">Magandang Balita Biblia (MBBTAG)</option><option value="MBBTAG-DC">Magandang Balita Biblia (with Deuterocanon) (MBBTAG-DC)</option><option class="spacer" value="MBBTAG-DC">&nbsp;</option><option class="lang" value="NA-TWI">—Twi (TWI)—</option><option value="NA-TWI">Nkwa Asem (NA-TWI)</option><option class="spacer" value="NA-TWI">&nbsp;</option><option class="lang" value="UKR">—Українська(UK)—</option><option value="UKR">Ukrainian Bible (UKR)</option><option value="ERV-UK">Ukrainian New Testament: Easy-to-Read Version (ERV-UK)</option><option class="spacer" value="ERV-UK">&nbsp;</option><option class="lang" value="ERV-UR">—اردو (UR)—</option><option value="ERV-UR">Urdu Bible: Easy-to-Read Version (ERV-UR)</option><option class="spacer" value="ERV-UR">&nbsp;</option><option class="lang" value="USP">—Uspanteco (USP)—</option><option value="USP">Uspanteco (USP)</option><option class="spacer" value="USP">&nbsp;</option><option class="lang" value="VIET">—Tiêng Viêt (VI)—</option><option value="VIET">1934 Vietnamese Bible (VIET)</option><option value="BD2011">Bản Dịch 2011 (BD2011)</option><option value="NVB">New Vietnamese Bible (NVB)</option><option value="BPT">Vietnamese Bible: Easy-to-Read Version (BPT)</option><option class="spacer" value="BPT">&nbsp;</option><option class="lang" value="CCB">—汉语 (ZH)—</option><option value="CCB">Chinese Contemporary Bible (Simplified) (CCB)</option><option value="CCBT">Chinese Contemporary Bible (Traditional) (CCBT)</option><option value="ERV-ZH">Chinese New Testament: Easy-to-Read Version (ERV-ZH)</option><option value="CNVS">Chinese New Version (Simplified) (CNVS)</option><option value="CNVT">Chinese New Version (Traditional) (CNVT)</option><option value="CSBS">Chinese Standard Bible (Simplified) (CSBS)</option><option value="CSBT">Chinese Standard Bible (Traditional) (CSBT)</option><option value="CUVS">Chinese Union Version (Simplified) (CUVS)</option><option value="CUV">Chinese Union Version (Traditional) (CUV)</option><option value="CUVMPS">Chinese Union Version Modern Punctuation (Simplified) (CUVMPS)</option><option value="CUVMPT">Chinese Union Version Modern Punctuation (Traditional) (CUVMPT)</option></select>';
           			html=html+'</select></p>';
        			html=html+'<p><button class="button" data-tab="#bibleSave" id="bibleSave">'+language['save']+'</button></p>';

        			$("#page #rendered").html(html);
        			//add languages drop down from latest set of languages
           			$.ajax({
  							url: "https://www.churchadminplugin.com/app-languages.php",
  							context: document.body
							}).done(function(result) {$( '#page #rendered #languageSelect' ).append( result);
							});
        },
        renderAccountView: function(message) {
			$('#firstRun').hide();
        	$('#page').show();

            var storage = window.localStorage;
            var token = storage.getItem('token');

            var html='';
           	if(message!=undefined && message !=null && message!='&nbsp;') html=html+'<p><em>'+ message +'</em></p>';
        	var churchURL = 'https://www.churchadminplugin.com';
        	if(navigator.onLine)
        	{//online
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');

        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',{ action: "ca_account", 'token': token },checkAccount);
           		function checkAccount(data)
           		{
           			var language=JSON.parse(storage.getItem('language'));
           			if(data.error==='login required'){self.renderLoginView('account');}
           			else if(data.error==='Your user identity is not connected to a church user profile.'){self.renderLoginView('account');}
           			else{
           		     console.log(data);
           			//address
           			var address=data.address;

        			html=html+'<h3>'+language["your-address"]+'</h3><ul class="account ui-listview">';
        			html= html +'<li class="address" >';
        			html = html +'<div  class="address_edit ui-btn ui-btn-icon-right ui-icon-edit" id="'+ address.household_id +'" data-tab="'+ address.household_id +'" data-target=".address_edit">';
        			html = html+address.address+'</li>';
           			html= html +'<li class="address">';
        			html = html +'<div  class="address_edit ui-btn ui-btn-icon-right ui-icon-edit" id="'+ address.household_id +'" data-tab="'+ address.household_id +'" data-target=".address_edit">';
        			html = html+address.phone+'</li></ul>';

           			html=html+'<h3>'+ language["people-in-your-household"] + '</h3><ul class="account ui-listview">';
           			var people=data.people;
					for(var count = 0; count < people.length; count++)
        			{
						var item=people[count];

            			html = html + '<li >';
            			html = html +'<div  class="people_edit ui-btn ui-btn-icon-right ui-icon-edit" id="'+ item.people_id +'" data-tab="'+ item.people_id +'" data-target=".people_edit">';
            			html = html + item.name + ' </li>';

        			}
        			html = html + '<li  id="0" data-tab="0" data-target=".people_edit">';
            		html = html +'<div  class="people_edit ui-btn ui-btn-icon-right ui-icon-edit">';
            		html = html + language["add-someone"] +'</li>';
        			html=html+'</ul>';


           			$("#page #rendered").html(html);

           		}
           	}
           	}//not online
        	else
        	{
        		navigator.notification.alert(contactItem.name+ ' is already in contacts', null, null, "Close");
        		var language=JSON.parse(storage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}

        },
        renderAddressView: function() {
			$('#firstRun').hide();
        	$('#page').show();


            var storage = window.localStorage;
            var token = storage.getItem('token');

        	var churchURL = 'https://www.churchadminplugin.com';

        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',{ action: "ca_check_token", 'token': token },checkToken);
           		function checkToken(data)
           		{
           			var language=JSON.parse(storage.getItem('language'));
           			if(data.error==='login required'){self.renderLoginView('address');}
           			else{
           				var html='<h2>' +language["search-address-list"] +'</h2>';
           				html=html+'<p><input id="s" type="text" placeholder="'+ language['who'] +'"/></p>';
           				html = html + '<p><button id="search" data-tab="#search" class="button">' + language["search"]+ '</button></p>';

           			$("#page #rendered").html(html);
           			}
           		}
           	}//not online
        	else
        	{
        		var language=JSON.parse(storage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}

        },
        renderAddressEditView: function(household_id){
        	$('#firstRun').hide();
        	$('#page').show();


            var storage = window.localStorage;
            var token = storage.getItem('token');
           if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        	var churchURL = 'https://www.churchadminplugin.com';
        	$.getJSON(churchURL+'/wp-admin/admin-ajax.php',{ action: "ca_address_edit", 'token': token,'household_id':household_id },addressEdit);
           	function addressEdit(data)
           	{

           		if(data.error==='login required'){self.renderLoginView('account');}

           		else{
           			var html='<h2><span class="languagespecificHTML" data-text="address-edit">Address Edit</h2><div class="ui-content">';
            		html=html+'<input type="hidden" value="'+household_id+'" id="household_id"/>';
					html=html+'<p>Address<br/><textarea id="address"  placeholder="Address" class="languagespecificPLACEHOLDER" data-text="address" autocorrect="off" autocapitalize="true">'+ data.address+'</textarea></p>';
					html=html+'<p>Home phone<br/><input id="phone" type="text" placeholder="Phone" class="languagespecificPLACEHOLDER" data-text="phone" autocorrect="off" autocapitalize="none" value="'+ data.phone+'"/></p>';
					html=html+'<p><button class="button" data-tab="#save_address_edit" id="save_address_edit" class="languagespecificHTML" data-text="save">Save</button> </p>';
					html=html+'</div>';
           			$("#page #rendered").html(html);

           		}
           	}
           	}//not online
        	else
        	{
        		var language=JSON.parse(storage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		$("#page #rendered").html(html);
        	}
        },
        renderBibleView: function(date){
			$('#firstRun').hide();
        	$('#page').show();


            //get day number which works for leap years too
            if(date){var now=new Date(date);}else{var now = new Date();}


			var shown= now.toDateString();//readable date

			//retrieve readings
			var storage=window.localStorage;
            var token = storage.getItem('token');
            var churchURL = 'https://www.churchadminplugin.com';
            var version=storage.getItem('version');
            storage.setItem('bible-badge', 0);
        	self.setBadge();
        	$("#page #bible-badge").hide();
            if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
				var args={ action: "ca_bible_readings", date: now,token:token ,version:version};

				$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,function(data)
        		{

        			var language=JSON.parse(storage.getItem('language'));

        			var html='<h2>'+language["bible-readings"]+'</h2>';
        			html=html+'<p class="dateShown">'+shown+'</p>';
        			html=html+'<span class="datepicker" style="text-decoration:underline">'+language["change-date"]+'</span>';

        			if(data.length>0)
        			{
        				for(var count = 0; count < data.length; count++)
        				{
        					html=html+data[count];
          		  		}
           	 		}

            		html= html + '<i class="fa fa-refresh tab-button"  id="refresh" data-tab="bible"  data-tap-toggle="false" aria-hidden="true"></i>';
            		$("#page #rendered").html(html);
            		$("#page #rendered .passage-toggle").on('click',function()
            		{
            			var ID=$(this).data('target');
            			$('#page #rendered .bible-text').hide();
            			$('#page #rendered #passage'+ID).toggle();

            			$('html,body').animate({
        					scrollTop: $('#page #rendered #passage'+ID).offset().top-150},
        					'slow');
            		});

        		})
        		.fail(function(jqXHR, textStatus, errorThrown)
				{
					navigator.notification.alert("Can't download Bible at the moment", null, null, "Close");
				})

        	}
        	else
            {
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	html= html + '<i class="fa fa-refresh tab-button"  id="refresh" data-tab="bible"  data-tap-toggle="false" aria-hidden="true"></i>';
            	$("#page #rendered").html(html);
            }

        },
        renderCalendarView: function(date) {
			$('#firstRun').hide();
        	$('#page').show();

           	var storage = window.localStorage;
            var token = storage.getItem('token');
            var args={ action: "ca_cal", date: date,'token':token };
            if(navigator.onLine)
        	{
           		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
           		var churchURL = 'https://www.churchadminplugin.com';
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args, processCalendar);

           		function processCalendar(data){
           		console.log(data);
            		var language=JSON.parse(storage.getItem('language'));
            		if(data.error== "There are no events this week in the calendar.") {$('#page #rendered').html('<p>'+language["no-events"]+'</p>');}
           			else
           			{
           			//o/p structure

           			var html='<h2>'+language["calendar"]+'</h2><div id="datePicker" class="ui-field-contain">';
           			//datepicker
           			var datepicker=data.dates;
           			html= html+'<select class="tab-button" data-tab="#calendar" id="dateSelect">';
           			$.each(datepicker, function(arrayIndex, userObject){
  						html= html+'<option value="' + userObject.mysql+'" >'+ 'w/c '+userObject.friendly + '</option>';
					});
					html = html+ '</select>';
           			var calendar=data.cal;
           			html = html+'</div><ul class="calendar ui-listview" data-inset="true">';

           			for(var count = 0; count < calendar.length; count++)
        			{
        				var item=calendar[count];


        				html = html +	'<li  class="calItem" id="'+ count + '">';
        				html = html +	'<div  class="ui-btn ui-btn-icon-right ui-icon-carat-r">';
						html = html +	'	<h3 class="ui-li-heading">'+ item.title+'</h3>';
						html = html +	'	<p class="ui-li-desc"><strong>'+ item.start_date+' '+ item.start_time+'-'+ item.end_time +'</strong><br/>';
						html = html +	item.description+'<br/>';
						html = html +	item.location+'</p>';
						html = html +	'</div>';
        				html = html + '</li>';
        			}
        			html = html +'</ul>';
        			$("#page #rendered").html(html);

        			$("#page #rendered").on('click', '.calItem', function()
        			{
        				//grab event
        				var count=$(this).attr('id');
        				var item = calendar[count];
        				//build calendar details
        				//start date
        				var iso = item.iso_date;
        				var sd=iso.split('-');
        				var st=item.start_time.split(":");
        				var startDate = new Date(sd[0],sd[1]-1,sd[2],st[0],st[1],0,0,0); // beware: month 0 = january, 11 = december
  						//always one day events, need to add end time to start date.
  						var end = item.end_time;
        				var et=end.split(":");
        				var endDate  = new Date(sd[0],sd[1]-1,sd[2],et[0],et[1],0,0,0);
  						var title = item.title;
  						var notes ='';
  						var eventLocation = item.location;
  						//only add event to calendar if not already in it.
  						var findSuccess=function(message)
  						{ if(message!='')
  							{
  								//event in calendar
  								navigator.notification.alert('Event already in calendar', null, null, "Close");
  							}
  							else
  							{
  								//add event to calendar
  								var calendarSuccess = function(message) { navigator.notification.alert('Added to calendar', null, null, "Close"); };
  								var calendarError = function(message) { alert("Error: " + message); };
        						window.plugins.calendar.createEvent(title,eventLocation,notes,startDate,endDate,calendarSuccess,calendarError);

  							}
  					 	};
  						var findError=function(message) { alert("Found Error: " + JSON.stringify(message)); };
  						window.plugins.calendar.findEvent(title,eventLocation,notes,startDate,endDate,findSuccess,findError);


        			});
        			};//calendar else
           	};
           	}//not online
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		html= html + '<i class="fa fa-refresh tab-button"  id="refresh" data-tab="calendar"  data-tap-toggle="false" aria-hidden="true"></i>';
        		$("#page #rendered").html(html);
        	}
        },
        renderClassesView:function(){
        	$('#firstRun').hide();
        	$('#page').show();

            var storage=window.localStorage;
            var token = storage.getItem('token');
           	var language=JSON.parse(window.localStorage.getItem('language'));
            var churchURL = 'https://www.churchadminplugin.com';
         	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
           		var args={ action: "ca_classes", token:token  };
           		console.log(token);
           		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args, function(data)
           		{
           			console.log('Classes');

           			var language=JSON.parse(storage.getItem('language'));
           			if(data.error==='No classes yet')
           			{
           				var html='<p>'+language["no-classes"]+'</p>';
            			$("#page #rendered").html(html);
            		}
           			else if (data.length<1){$("#page #rendered").html(language['no-classes']);}
           			else
           			{//good to go...
           				var students='';
           				var html='<h2>'+ language['classes']+'</h2>';
           				html = html+'</div><ul class="calendar ui-listview" data-inset="true">';
           				for(var count = 0; count < data.length; count++)
        				{
        					var item=data[count];
                  var class_id=item.class_id;
      						html = html +	'<li  class="courseItem" id="'+ class_id + '">';
        					html = html +	'	<h3 class="ui-li-heading">'+ item.name+'</h3>';
							html = html +	'<p ><strong>' + item.dates + ' ' + item.times + '</strong><br/>';
							html = html +	item.description+'<br/>';
							html = html + language['next-date'] + ': <span class="date" data-date="' +item.sqldate + '">' + item.date + '</span></p>';

							console.log(item);
							if (item.students)
							{
                //if students is populated, user has access
                var dates=item.all_dates;
                console.log(dates);
                html=html+'<p>'+language['checkin']+'<select class="class'+class_id+'" id="dateChooser">';
                var datesCount=0;
                while(dates[datesCount])
                  {
                    html=html+'<option value="'+dates[datesCount]+'">'+dates[datesCount]+'</option>';
                    datesCount++;
                  }
                  html=html+'</select>';

								students=item.students;
								if (students.length>0)
								{
									html =html+'<ul class="students" id="'+ class_id + '">';
									for( var scount=0; scount<students.length; scount++)
									{
										var student=students[scount];

										html = html + '<li ><input type="checkbox" class="student class'+item.class_id+'" data-people-id="'+student.people_id +'"/> ' + student.name +'</li>';
									}
									html= html + '</ul>';
									html=html+'<p><button class="button check-in" data-class-id="'+item.class_id +'"  >'+language['checkin']+'</button> </p>';
								}
							}
							html = html + '</li>';
							students='';
        				}
        				html = html +'</ul>';
        				$("#page #rendered").html(html);

           			}

        		})
        		.fail(function(jqXHR, textStatus, errorThrown)
				{

					navigator.notification.alert("Can't download classes at the moment", null, null, "Close");
					$("#page #rendered").html(language['no-classes']);
				})
        	}
        	else
        	{//not online

            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }

        },
        renderGivingView: function() {
			$('#firstRun').hide();
        	$('#page').show();

            var html='<p>App not setup yet</p>';
            var storage = window.localStorage;
            var giving=storage.getItem('giving');
            console.log('giving');
            var language=JSON.parse(storage.getItem('language'));
            if(giving ==='false'||giving ===false)
        	{
        			storage.setItem('home', 'App content not yet setup');
        			html=html + 'App not yet setup';
        	}
        	else {
            	html = '<p><img src="'+storage.getItem('logo')+'"/></p><p>'+ giving + '</p>';
            	html= html + '<i class="fa fa-refresh tab-button"  id="refresh" data-tab="giving"  data-tap-toggle="false" aria-hidden="true"></i>';
            }
            $("#page #rendered").html(html);

        },
        renderGroupView: function() {
			$('#firstRun').hide();
        	$('#page').show();

            var storage=window.localStorage;
            var token = storage.getItem('token');
            var churchURL = 'https://www.churchadminplugin.com';
        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',{ action: "ca_groups",token:token },processGroups);
        		function processGroups(data)
        		{
        			var language=JSON.parse(storage.getItem('language'));
           			if (data.length>0)
        			{
        				var html='';

           				var groupInfo=window.localStorage.getItem('groups')
           				if(groupInfo!='false')html=html+groupInfo
           				if(token)html=html +'<p><button id="mygroup" data-tab="#mygroup" class="button">'+language["my-group"]+'</button></p>';

           				html=html+'<ul class="groups">';
   						for(var count = 0; count < data.length; count++)
        				{
            				var group = data[count];
            				html = html + '<li><h3>' + group.name + '</h3><p>' + group.whenwhere + '<br/>'+ group.address +'</p></li>';
        				}
        				html= html+'</ul>';
        				html= html + '<i class="fa fa-refresh tab-button"  id="refresh" data-tab="group"  data-tap-toggle="false" aria-hidden="true"></i>';
        			}
        			else
        			{
        				html=language['no-groups'];
        			}
        			$("#page #rendered").html(html);
           		}
           	}//not online
        	else
        	{
        		var language=JSON.parse(window.localStorage.getItem('language'));
        		var html='<p>'+language["disconnected"]+'</p>';
        		html= html + '<i class="fa fa-refresh tab-button"  id="refresh" data-tab="group"  data-tap-toggle="false" aria-hidden="true"></i>';
        		$("#page #rendered").html(html);
        	}
        },
       	renderHomeView: function(){

   			$('#firstRun').hide();
        	$('#page').show();

            var html='<p>App not setup yet</p>';
            var storage = window.localStorage;

            var churchURL = 'https://www.churchadminplugin.com';
           	var date = new Date();
           	var today=date.getDate();
            var token = storage.getItem('token');
						var language=JSON.parse(storage.getItem('language'));
						if (storage.getItem('downloaded')== today && storage.getItem('home') && storage.getItem('logo'))
						{//if downloaded today use stored data to save bandwidth
								var menu = storage.getItem('menu');
								$("#page .menu").html(menu);
								var logo = storage.getItem('logo');
            		var home =storage.getItem('home');
								html='<div class="content">';

								if(logo)html=html+'<img src="'+logo+'" class="img-responsive"/>';
								if(home ==='false'||home ===false)
        				{
        					storage.setItem('home', 'App content not yet setup');
        					html=html + 'App not yet setup';
        				}
        				else {html=html+ '<p>'+home+'</div>';}
								html = html + '<p><button id="logout" data-tab="#logout" class="button">'+language['logout']+'</button></p>';
								html= html + '<i class="fa fa-refresh tab-button"  id="refresh" data-tab="home"  data-tap-toggle="false" aria-hidden="true"></i>';
								$("#page #rendered").html(html);
								var title=storage.getItem('menu_title');
								if(!Boolean(title)){title=language['home'];}
           			$("#page #church").html(title);
                var style=storage.getItem('style');
                if(style)$("body #dynamicStyles").html(style);
						}
						else
            {//download fresh copy of data
            	$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');

            	var args={'action':'ca_home','token':token};
        			$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,function (data)
           		{

           			self.languageSet();
           			var language=JSON.parse(storage.getItem('language'));

           			storage.setItem('menu_title', data.menu_title);
           			if(data.menu_title==undefined || data.menu_title ==null ){storage.setItem('menu_title', 'Home');}
           			storage.setItem('home', data.home);
        				storage.setItem('giving', data.giving);
        				storage.setItem('groups',data.groups);
        				storage.setItem('logo',data.logo);
        				storage.setItem('church_id',data.church_id);

                if(data.style){
                  storage.setItem('style',data.style);

                  $("body #dynamicStyles").html(data.style);
                }else
                {
                  storage.removeItem('style');
                  $("body #dynamicStyles").html('');
                }
                if(!data.menu)
                {
                  data.menu='<li id="home-tab-button" class="tab-button" data-tab="#home"><i class="fa fa-home fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="home">Home</span></li><li id="account-tab-button" class="tab-button" data-tab="#account"  data-tap-toggle="false"><i class="fa fa-user fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="account">Account</span></li><li id="address-tab-button" class="tab-button" data-tab="#address" data-tap-toggle="false"><i class="fa fa-phone fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="address">Address</span></li><li id="bible-tab-button" class="tab-button" data-tab="#bible"  data-tap-toggle="false"><i class="fa fa-book fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="bible">Bible</span><span id="bible-badge"></span></li><li id="calendar-tab-button" class="tab-button" data-tab="#calendar"  data-tap-toggle="false"><i class="fa fa-calendar fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="calendar">Calendar</span></li><li id="classes-tab-button" class="tab-button" data-tab="#classes"  data-tap-toggle="false"><i class="fa fa-lightbulb fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="classes">Classes</span></li><li id="giving-tab-button" class="tab-button" data-tab="#giving" data-tap-toggle="false"><i class="fa fa-credit-card fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="giving">Giving</span></li><li id="group-tab-button" class="tab-button" data-tab="#smallgroup" data-tap-toggle="false"><i class="fa fa-user fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="groups">Groups</span></li><li id="media-tab-button" class="tab-button" data-tab="#media" data-tap-toggle="false"><i class="fa fa-headphones fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="media">Media</span></li><li id="news-tab-button" class="tab-button" data-tab="#news"  data-tap-toggle="false"><i class="fa fa-newspaper-o fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="news">News</span> <span id="news-badge"></span></li><li id="prayer-tab-button" class="tab-button" data-tab="#prayer"  data-tap-toggle="false"><i class="fa fa-child  fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="prayer">Prayer</span> <span id="prayer-badge"></span></li><li id="my-prayer-tab-button" class="tab-button" data-tab="#myprayer"  data-tap-toggle="false"><i class="fa fa-child  fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="my-prayer-list">My Prayer List</span> <span id="prayer-badge"></span></li><li id="rota-tab-button" class="tab-button" data-tab="#rota" data-tap-toggle="false"><i class="fa fa-file-text-o fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="rotas">Rotas</span></li><li id="settings-tab-button" class="tab-button" data-tab="#settings" data-tap-toggle="false"><i class="fa fa-cog fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="settings">Settings</span></li><li id="logout-tab-button" class="tab-button" data-tab="#logout"  data-tap-toggle="false"><i class="fa fa-sign-out fa-2x" aria-hidden="true"></i></i> <span class="languagespecificHTML" data-text="logout">Logout</span></li>';
                }
                storage.setItem('menu',data.menu);
								$("#page .menu").html(data.menu);
        				var today = new Date();
								storage.setItem('downloaded', today.getDate());
        				html='<div class="content">'+'<img src="'+data.logo+'" class="img-responsive"/><p>'+data.home+'</div>';
          	 		//logout button if logged in
          		  var token = storage.getItem('token');
          		  var church_id= storage.getItem('church_id');

           	 		//finish output

            		html = html + '<p><button id="logout" data-tab="#logout" class="button">'+language['logout']+'</button></p>';

            		$("#page #rendered").html(html);
           			var title=storage.getItem('menu_title');
								if(data.menu_title===false){ title=language['home'];}

           			$("#page #church").html(title);
                //styling
                var style=storage.getItem('style');
                if(style){
                  console.log(style);
                  $("body #dynamicStyles").html(style);
                }else
                {
                  $("body #dynamicStyles").html('');
                }

								if(typeof FCMPlugin !== "undefined")
								{

										FCMPlugin.getToken(function(pushToken){window.localStorage.setItem('token',pushToken);});
										FCMPlugin.subscribeToTopic('church'+ data.church_id);

								}
            	})
            	.fail(function(jqXHR, textStatus, errorThrown)
							{
								navigator.notification.alert("Can't download data from " + churchURL, null, null, "Close");
							})
            }

		},
		renderLoginView: function(whereNext){
			$('#firstRun').hide();
        	$('#page').show();
        	var language=JSON.parse(window.localStorage.getItem('language'));
            var html='<h2>'+language["please-login"]+'</h2><div class="ui-content">';
            html=html+'<input type="hidden" value="'+whereNext+'" id="whereNext"/>';
			html=html+'<p><input id="username"  type="text" placeholder="' + language['username'] + '" autocorrect="off" autocapitalize="none"/></p>';
			html=html+'<p><input id="password"  type="password" placeholder="' + language['password'] + '"/></p>';
			html=html+'<p><button class="button" data-tab="#login" id="login" class="languagespecificHTML" data-text="login" >Login</button></p>';
			html=html+'<p><button class="button" data-tab="#forgotten" id="forgotten" class="languagespecificHTML" data-text="forgotten" >Forgotten Password</button></p>';
			html=html+'</div>';
           	$("#page #rendered").html(html);
        },
 		renderMediaView: function() {
			$('#firstRun').hide();
        	$('#page').show();

            var storage = window.localStorage;
            var token = storage.getItem('token');
            var churchURL = 'https://www.churchadminplugin.com';
            var args={ action: "ca_sermons", 'token': token};
        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processMedia);
           		function processMedia(data)
           		{
           			var language=JSON.parse(storage.getItem('language'));
           			var html='<h2>'+language["latest-sermons"]+'</h2>';
           			if(data.length>0)
           			{

           				html=html+'<ul class="sermons ui-listview">';

   						for(var count = 0; count < data.length; count++)
        				{

            				var sermon=data[count];
            				html = html + '<li class="sermon" id="'+ sermon.id +'" data-tab="'+sermon.id+'" data-target=".sermon" >';
            				html = html +	'<div  class="ui-btn ui-btn-icon-right ui-icon-carat-r">';
            				html= html+ '<h3>' + sermon.title + '</h3><p>' + sermon.description + '<br/>'+ sermon.speaker+' - '+ sermon.pub_date + '</p>';
            				html = html+ '</div></li>';

        				}
        				html= html+'</ul>';
        			}
        			else
        			{
        				html=html+language['no-media'];
        			}
        			$("#page #rendered").html(html);
				}
			}
			else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
        },
        renderMyGroupView:function(){
        	$('#firstRun').hide();
        	$('#page').show();
			var storage=window.localStorage;
            var token = storage.getItem('token');
            var churchURL = 'https://www.churchadminplugin.com';
            var language=JSON.parse(storage.getItem('language'));
        	$("#page #rendered").html('<h2>'+language["my-group"]+'</h2>');
        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
            var args={ action: "ca_my_group",token:token,version:2.6 };
            console.log(args);
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,function(data) {
        		console.log(data);

        		var language=JSON.parse(storage.getItem('language'));
            var html='<h2>'+language['my-groups']+'</h2>';
           		if(data.error==='login required'){self.renderLoginView('myrota');}
              else {
              if(data.error==='No results'){html = html + language['no-groups'];}
              else
           		{
                //array of multiple groups...
                if(data.length == 0)
             		{
             			html = html + language['no-groups'];
             		}
                else {
                  for(var count = 0; count < data.length; count++)
                  {
                     var item=data[count];
                     html=html + '<h3>' + item.group_name +'</h3>';
               			 html = html + '<p>'+language["meeting"]+' '+  item.when_where+'</p>';
                     html= html+'<ul class="my-group ui-listview">';
                     var people=item.people;
                  	for(var newcount = 0; newcount < people.length; newcount++)
               			{
               					var peopleitem=people[newcount];
               					html = html +	'<li class="addItem" id="'+ newcount + '">';
               					html = html +	'<div  class="ui-btn ui-btn-icon-right ui-icon-carat-r">';
               					html = html +'<h3>'+ peopleitem.name+'</h3><p>';
               					html = html +	 peopleitem.address + '<br/>';
               					if(peopleitem.phone){html = html + '<a href="tel:'+peopleitem.phone+'">'+peopleitem.phone +'</a><br/>';}
               					if(peopleitem.mobile)html = html + '<a href="tel:'+peopleitem.mobile+'">'+peopleitem.mobile + '</a><br/>';
               					if(peopleitem.email)html = html + '<a href="mailto:'+peopleitem.email+'">'+peopleitem.email + '</a><br/>';
               					html = html + '</p></div></li>';
               			}
                    html=html+'</ul>';
                  }

                }
              }

        			$("#page #rendered").html(html);
        		}
        		});
        	}
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
        },
				renderMyPrayerView:function()
				{
					$('#firstRun').hide();
        	$('#page').show();
					var storage=window.localStorage;
					self.languageSet();
					var language=JSON.parse(storage.getItem('language'));
					//prayerItems is an array of items for prayer for a particular day
					var jsonPrayerItems=storage.getItem('prayer-list'+(new Date()).getDay());
					if(jsonPrayerItems)var prayerItemsArray=JSON.parse(jsonPrayerItems);

					if(!prayerItemsArray)
					{

						var prayerItemsArray=[ 'thank-god-for-today' ];
						storage.setItem('prayer-list'+(new Date()).getDay(),JSON.stringify(prayerItemsArray));
					}
					console.log(prayerItemsArray);
					var prayerItems='';
					$.each(prayerItemsArray, function(key,value) {
            prayerItems=prayerItems + '<li class="prayeritem"><input type="checkbox" class="prayer-answered"  data-index="'+key+'"/>'+value+'</li>';
        	});
					console.log('Prayer Items:'+prayerItems);
					var day= [language["sunday"],language["monday"], language["tuesday"], language["wednesday"],language["thursday"], language["friday"],language["Saturday"]];

					var html='<h2>'+ language['my-prayer-list-for']+' '+ day[(new Date()).getDay()]+'</h2>';
					html=html+'<ul id="list-items" class="ui-listview">'+prayerItems+'</ul>';
					html=html+'<h3>'+language['add-an-item']+'</h3>';
					html=html+'<p><input type="text"  id="prayer-list-item" placeholder="'+ language['prayer-list-item']+'"?"/></p>';
					html=html+'<p>'+language['pray-which-days']+'</p>';
					//html=html+'<p><input type="checkbox" class="days" id="prayer-all" checked="checked"/>'+language['pray-every-day']+'<br/>';
					html=html+'<input type="checkbox" class="days"  value=0 />'+language['sunday']+'<br/>';
					html=html+'<input type="checkbox" class="days"   value="1"/>'+language['monday']+'<br/>';
					html=html+'<input type="checkbox" class="days"   value="2"/>'+language['tuesday']+'<br/>';
					html=html+'<input type="checkbox" class="days"   value="3"/>'+language['wednesday']+'<br/>';
					html=html+'<input type="checkbox" class="days"   value="4"/>'+language['thursday']+'<br/>';
					html=html+'<input type="checkbox" class="days"   value="5"/>'+language['friday']+'<br/>';
					html=html+'<input type="checkbox" class="days"  value="6"/>'+language['saturday']+'</p>';
					html=html+'<p><button class="button" data-tab="#add-prayer" id="add-prayer" >'+language['add']+'</button></p>';
					$("#page #rendered").html(html);
				},
        renderMyRotaView:function(){
					$('#firstRun').hide();
        	$('#page').show();

            var storage=window.localStorage;
            var token = storage.getItem('token');

            var churchURL = 'https://www.churchadminplugin.com';
         	if(navigator.onLine)
        	{
           		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
           		var args={ action: "ca_my_rota", token:token  };
           		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args, function(data) {
           		var language=JSON.parse(storage.getItem('language'));
           			if(data.error==='login required'){self.renderLoginView('myrota');}
           			else
           			{
           				var html='<h2>'+ language['my-rota']+'</h2>';
           				for(var count =0; count < data.length; count++)
           				{

           					var date=data[count][0].date;
           					html=html+'<h3>'+date+'</h3>';
           					$.each(data[count], function(arrayIndex, userObject){
  							html=html+userObject.job+'<br/>';
							});
						}
						$('#page #rendered').html(html);
           			}

           		});
           	}
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
		},
 		renderNewsView: function() {
			$('#firstRun').hide();
        	$('#page').show();


          	var storage=window.localStorage;
						window.localStorage.removeItem('Notification');

        	var churchURL = 'https://www.churchadminplugin.com';
        	console.log(churchURL);
            var token = storage.getItem('token');
            var args={'action':'ca_posts','page':1,'token':token};
        	if(navigator.onLine)
        	{
        	$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        	$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processNews);
           	function processNews(data)
           	{
           		var language=JSON.parse(storage.getItem('language'));
           		var html='<h2>' + language["latest-news"] +'</h2>';
           		if(data.length == 0)
           		{
           			html = html + language['no-news'];
           		}
           		else
           		{

           			storage.setItem('news', 0);
           			self.setBadge();
           			$("#page #news-badge").hide();


           			html=html+'<ul class="news  ui-listview">';

   					for(var count = 0; count < data.length; count++)
        			{
            			var title = data[count][0];
            			var link = data[count][1];
            			var date = data[count][2];
            			var image = data[count][3];
            			if (!Boolean(image))image=storage.getItem('logo');
            			var id=data[count][4];

            			html = html + '<li class="newsItem" id="'+ id +'" data-tab="'+id+'" data-target=".newsitem">';
            			html = html +'<div  class="ui-btn ui-btn-icon-right ui-icon-carat-r">';
            				//show image if available
            			if (Boolean(image)) {html = html +'<img height="100" width="150" class="alignleft" src="' + image + '">';}
            			html=html+'<h3>' + title + '</h3><p>' + date + '<br style="clear:left;"/></p></li>';

        			}
        			html= html+'</ul>';
        			html=html+'<p><input type="hidden" id="paged" value="2"/><button class="button" id="more-news">' + language["older-news"] +'</button></p>';
        		}
        		$("#page #rendered").html(html);

			}
			$("#page #rendered").on('click', '#more-news', function()
        	{//process older posts request
        		var page=parseInt($('#paged').val());
        		var args={'action':'ca_posts','page':page,'token':token};
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,function(data){
        			var html='';
        			for(var count = 0; count < data.length; count++)
        			{
            			var title = data[count][0];
            			var link = data[count][1];
            			var date = data[count][2];
            			var image = data[count][3];
            			if (!Boolean(image))image=storage.getItem('logo');
            			var id=data[count][4];

            			html = html + '<li class="newsItem" id="'+ id +'" data-tab="'+id+'" data-target=".newsitem">';
            			html = html +'<div  class="ui-btn ui-btn-icon-right ui-icon-carat-r">';
            			if (Boolean(image)) {html = html +'<img height="100" width="150" class="alignleft" src="' + image + '">';}
            			html = html +'<h3>' + title + '</h3><p>' + date + '<br style="clear:left;"/></p></li>';

        			}
        			$("#page #rendered ul").append(html);
        			$("#page #rendered #paged").val(page+1);
        		});
        	});
        	}
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }

        },
        renderPostView:function(ID){
			$('#firstRun').hide();
        	$('#page').show();

					window.localStorage.removeItem('Notification');
        	var storage=window.localStorage;
        	var churchURL = 'https://www.churchadminplugin.com';
        	 var token = storage.getItem('token');
          	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',{ action: "ca_post",'ID':ID, 'token':token },processPost);
        		function processPost(data)
        		{
        			var language=JSON.parse(storage.getItem('language'));

        			var html='<h3>'+data.title+'</h3>'+data.content+'<hr/>' + language['posted-by'] +' : '+data.author+' '+data.date;
        			$("#page #rendered").html(html);
        		}
        	}
        	else
            {//not online
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
        },
        renderPrayerView:function(message){

          $('#firstRun').removeClass('visible');
        	$('#page').addClass('visible');
					window.localStorage.removeItem('Notification');
        	cordova.plugins.notification.badge.set(0);
           	var storage = window.localStorage;

           	$("#page #prayer-badge").hide();

           	storage.setItem('prayer-badge', 0);
           	self.setBadge();
            var token = storage.getItem('token');
        	var churchURL = 'https://www.churchadminplugin.com';
        	if(navigator.onLine)
        	{
				$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
				$.getJSON(churchURL+'/wp-admin/admin-ajax.php',{ action: "ca_prayer",token:token },processPrayer);
        		function processPrayer(data)
        		{
        	 		var language=JSON.parse(storage.getItem('language'));
        			if(data.error==='login required')self.renderLoginView('prayer');
           			else{
        				var html='<h2>' + language['send-prayer-request'] + '</h2>';
        				html = html + '<p>'+language['moderation-queue']+'</p>';
        				html=html+'<div class="request"><p><input id="prayer-title" type="text" placeholder="'+ language["title"]+'"  autocorrect="off" autocapitalize="none" /></p>';

        				html=html+'<p><textarea id="prayer-request" autocorrect="off" data-text="prayer-request" placeholder="'+language["prayer-request"]+'"></textarea></p>';
						html=html+'<p><button class="button" data-tab="#send_prayer_request" id="send_prayer_request">'+language['send']+'</button> </p></div>';
						html=html+'<h2>'+language['prayer']+'</h2>';
						html=html+'<ul class="prayer">';
           				for(var count = 0; count < data.length; count++)
        				{
            				var prayer=data[count]
            				html = html + '<li class="prayeritem"><h3>' + prayer.title + '</h3><p><em>' + language['posted'] +': ' + prayer.date + '</em></p><p>'+prayer.content+'</li>';
        				}
        				html= html+'</ul>';
        				$("#page #rendered").html(html);

        				storage.setItem('prayer-badge', 0);
        				cordova.plugins.notification.badge.decrease(1, function (badge) {});
        			}
        		}
        	}
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
        	var language=JSON.parse(storage.getItem('language'));
        	if(message!=undefined && message !=null && message!='&nbsp;') $('#page #rendered .request').html(language['sent']);
        },
        renderPeopleEditView: function(people_id){
        	$('#firstRun').hide();
        	$('#page').show();


            var storage = window.localStorage;
            var token = storage.getItem('token');
        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		var churchURL = 'https://www.churchadminplugin.com';
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',{ action: "ca_people_edit", 'token': token,'people_id':people_id },peopleEdit);
           		function peopleEdit(data)
           		{
           			var language=JSON.parse(storage.getItem('language'));
           			if(data.error==='login required'){self.renderLoginView('account');}

           			else{
           			var html='<h2>' + language["person-edit"] +'</h2><div class="ui-content">';
            		html=html+'<input type="hidden" value="'+people_id+'" id="people_id"/>';
					html=html+'<p>First Name<br/><input id="first_name" type="text" placeholder="' + language["first-name"] +'" autocorrect="off" autocapitalize="none" value="'+ data.first_name+'"/></p>';
					html=html+'<p>Last Name<br/><input id="last_name" type="text" placeholder="' + language["last-name"] +'" autocorrect="off" autocapitalize="none" value="'+ data.last_name+'"/></p>';
					html=html+'<p>Email<br/><input id="email" type="text" placeholder="'+ language["email-address"] +'" autocorrect="off" autocapitalize="none" value="'+ data.email+'"/></p>';
					html=html+'<p>Cellphone<br/><input id="mobile" type="text" placeholder="' + language["mobile"] +'" autocorrect="off" autocapitalize="none" value="'+ data.mobile+'"/></p>';
					html=html+'<p><button class="red button" data-tab="#delete_people" id="delete_people">' + language['delete'] + '</button> <button class="button" data-tab="#save_people_edit" id="save_people_edit" >' +language["save"] +'</button> </p>';
					html=html+'</div>';
           			$("#page #rendered").html(html);

           		}
           	}
           	}//end online
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
        },
        renderSearchView: function(search){

			$('#firstRun').hide();
        	$('#page').show();


            var storage = window.localStorage;
            var token = storage.getItem('token');
             if(navigator.onLine)
        	{
            $("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
            var churchURL = 'https://www.churchadminplugin.com';
            if(search==''){self.renderAddressView(); }//don't search if no value entered
            else
            {
            	var language=JSON.parse(storage.getItem('language'));
            	var args={ action: "ca_search", 'token': token,'search': search};

            	$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,searchResult);
        		$('#page #rendered').html('<h2>' + language["search-yield"] +'...</h2><ul class="address  ui-listview"></ul>');
           		function searchResult(data)
           		{

           			if(data.error==='login required')self.renderLoginView('address');
           			else if(data['error']==='No results')
           			{
           				var html='<h2>' + language["address-list"]+'</h2>';
           				html = html + '<h3>'+language["no-results"]+'</h3>';
           				html = html+'<input id="s" type="text" placeholder="'+language["who"]+'" autocorrect="off" autocapitalize="none"/><br/>';
           				html = html + '<button id="search" data-tab="#search" class="button">' + language["search"] +'</button';
           				html = html + '</div>';
           				$("#page #rendered").html(html);
           			}
           			else
           			{

           				var html='';
           				for(var count = 0; count < data.length; count++)
        				{
        					var item=data[count];
        					html = html +	'<li >';
        					html = html +	'<div  class="ui-btn ui-btn-icon-right ui-icon-plus addItem" id="'+ count + '">';
        					html = html +'<h3>'+ item.name+'</h3>';
        					html = html +	 item.address + '<br/>';
        					if(item.phone){html = html + '<a href="tel:'+item.phone+'">'+item.phone +'</a><br/>';}
        					if(item.mobile)html = html + '<a href="tel:'+item.mobile+'">'+item.mobile + '</a><br/>';
        					if(item.email)html = html + '<a href="mailto:'+item.email+'">'+item.email + '</a><br/>';
        					html = html + '</div></li>';
        				}
        				$("#page #rendered  ul").append(html);
        				//add to contacts section
        				$("#page #rendered").on('click', '.addItem', function()
        				{

        						//grab contact details
        						var count=$(this).attr('id');
        						var contactItem = data[count];
                    console.log(contactItem);
        						//function for found item on contacts db on device
        						function contactsSearchSuccess(contacts) {


        								if(contacts!='')
  										{

  											//contact is in device contacts
  											navigator.notification.alert(contactItem.name+ ' is already in contacts', null, null, "Close");
  										}
  										else
  										{
  											//add contacts to device contacts
  											function contactsSaveSuccess(contact) {  navigator.notification.alert(contactItem.name+ ' saved in contacts', null, null, "Close");};
											function contactsSaveError(contactError) {alert("Error = " + contactError.code);};
											// create a new contact object
											var newContact = navigator.contacts.create();
											newContact.displayName = contactItem.name;
											newContact.nickname = item.name;// specify both to support all devices
											// populate name fields
											var name = new ContactName();
											name.givenName = contactItem.first_name;
											name.familyName = contactItem.last_name;
											newContact.name = name;
											// store contact phone numbers in ContactField[]
    										var phoneNumbers = [];
    										phoneNumbers[0] = new ContactField('home', contactItem.phone, false);
    										phoneNumbers[1] = new ContactField('mobile', contactItem.mobile, true); // preferred number
    										newContact.phoneNumbers = phoneNumbers;
											//address
											var address= new ContactAddress();
											address.type='home';
											address.streetAddress=contactItem.streetAddress;
											address.locality=contactItem.locality;
											address.region=contactItem.region;
											address.postalCode=contactItem.postalCode;
											newContact.address=address;
																					// save to device
											newContact.save(contactsSaveSuccess,contactsSaveError);
											newContact=null;

  										}

								};//end success in finding contact
								function contactsSearchError(contactError) {alert('onError!');};
								// find all contacts with chosen name in any name field
								var options      = new ContactFindOptions();
								options.filter   = contactItem.name;
								options.multiple = false;
								options.desiredFields = [navigator.contacts.fieldType.id];
								options.hasPhoneNumber = true;
								var fields       = [navigator.contacts.fieldType.displayName, navigator.contacts.fieldType.name];
								navigator.contacts.find(fields, contactsSearchSuccess, contactsSearchError, options);
        				});//end of add to contacts section
           			}
           		}
        	}
        	}//end online
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
        },
        renderSermonView:function(ID){
       		$('#firstRun').hide();
        	$('#page').show();

        	var storage=window.localStorage;
        	var storage = window.localStorage;
            var token = storage.getItem('token');
            var churchURL = 'https://www.churchadminplugin.com';
            var args={ action: "ca_sermon", ID: ID,'token':token };
            console.log(args);
        	if(navigator.onLine)
        	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args,processPost);
        		function processPost(data)
        		{
        		console.log(data);
				function pad2(number) {return (number < 10 ? '0' : '') + number};

				myaudio = new Audio(data.file_url);

				var isPlaying = false;
				var readyStateInterval = null;
				var html5audio = {

				play: function()
				{

					isPlaying = true;
					myaudio.play();

					readyStateInterval = setInterval(function(){
						if (myaudio.readyState <= 2) {
							$('#page #rendered #playbutton').hide();
							//activityIndicator.style.display = 'block';
							$('#page #rendered tesxtposition').html('Loading...');
						}
					},1000);
					myaudio.addEventListener("timeupdate", function() {
						if(isPlaying)
						{
							var s = parseInt(myaudio.currentTime % 60);
							var m = parseInt((myaudio.currentTime / 60) % 60);
							var h = parseInt(((myaudio.currentTime / 60) / 60) % 60);
							if (isPlaying && myaudio.currentTime > 0) {
							$('#page #rendered #textposition').html(pad2(h) + ':' + pad2(m) + ':' + pad2(s));
							}
						}
					}, false);
					myaudio.addEventListener("error", function() {
						console.log('myaudio ERROR');
					}, false);
					myaudio.addEventListener("canplay", function() {
						console.log('myaudio CAN PLAY');
					}, false);
					myaudio.addEventListener("waiting", function() {

						isPlaying = false;
						$('#page #rendered #playbutton').hide();

						$('#page #rendered #stopbutton').hide();
						//activityIndicator.style.display = 'block';
					}, false);
					myaudio.addEventListener("playing", function() {
						isPlaying = true;
						$('#page #rendered #playbutton').hide();
						//activityIndicator.style.display = 'none';
						$('#page #rendered #pausebutton').show();
						$('#page #rendered #stopbutton').show();
					}, false);
					myaudio.addEventListener("ended", function() {
						console.log('myaudio ENDED');
						html5audio.stop();
						// navigator.notification.alert('Streaming failed. Possibly due to a network error.', null, 'Stream error', 'OK');
						// navigator.notification.confirm(
						//	'Streaming failed. Possibly due to a network error.', // message
						//	onConfirmRetry,	// callback to invoke with index of button pressed
						//	'Stream error',	// title
						//	'Retry,OK'		// buttonLabels
						// );
						if (window.confirm('Streaming failed. Possibly due to a network error. Retry?')) {
							onConfirmRetry();
						}
					}, false);
				},
				pause: function() {
					isPlaying = false;
					clearInterval(readyStateInterval);
					myaudio.pause();
					$('#page #rendered #pausebutton').hide();
					$('#page #rendered #stopbutton').show();
					//activityIndicator.style.display = 'none';
					$('#page #rendered #playbutton').show();
				},
				stop: function() {
					isPlaying = false;
					clearInterval(readyStateInterval);
					if(myaudio!=null)myaudio.pause();
					$('#page #rendered #stopbutton').hide();
					//activityIndicator.style.display = 'none';
					$('#page #rendered #playbutton').show();
					$('#page #rendered #textposition').html('');

					myaudio = null;
					myaudio = new Audio(data.file_url);

				}
			}	;




			var html = '<h2 class="languagespecificHTML" data-text="listen">Listen to a sermon</h2><h3>' + data.title + '</h3><p>' + data.description + '<br/>'+ data.speaker+' - '+ data.pub_date + '</p>';
        		html= html +   '<p><i class="fa fa-play fa-2x" aria-hidden="true" id="playbutton" data-tab="playbutton" data-target="#player-play"></i> <i class="fa fa-pause fa-2x" aria-hidden="true" id="pausebutton" data-tab="player-pause" data-target="#pausebutton" style="display:none"></i>  <i class="fa fa-stop fa-2x" aria-hidden="true" id="stopbutton" data-tab="player-stop" data-target="#stopbutton" style="display:none"></i></p> <div id="textpositiondiv">Time played: <span id="textposition">Stopped</span></div>';
				$("#page #rendered").html(html);
				$('#page #rendered').on('click','#playbutton',function() {console.log('Play');html5audio.play(); });
				$('#page #rendered').on('click','#pausebutton',function() {console.log('Pause');html5audio.pause(); });
        		$('#page #rendered').on('click','#stopbutton',function() {console.log('Stop');html5audio.stop(); });
				$('#page').on('click','#header-menu',function() {console.log('Navigate Away');if( myaudio!=null)html5audio.stop(); });
				$('#page').on('click','#footer-menu',function() {console.log('Navigate Away');if( myaudio!=null)html5audio.stop(); });
			}
			}//end online
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
		},
      renderRotaView: function(rota_id) {
			$('#firstRun').hide();
        	$('#page').show();

            var storage=window.localStorage;
            var token = storage.getItem('token');
            var churchURL = 'https://www.churchadminplugin.com';

           	var args={ action: "ca_rota", rota_id: rota_id,token:token,version:2.6  };
						console.log(args);

           	if(navigator.onLine)
           	{
        		$("#page #rendered").html('<img class="waiting" src="./img/spinner.gif"/>');
        		$.getJSON(churchURL+'/wp-admin/admin-ajax.php',args, function(data) {
              if(data.error==='login required')self.renderLoginView('rota');
              else{
							         var language=JSON.parse(storage.getItem('language'));
           		          if(data.error== "No one is doing anything yet") {console.log('Error');$('#page #rendered').html('<p>'+language["rota-setup"]+'</p>');}
           		          else
           		           {
           			             if(data.services!=undefined)
           			               {
           					                 //o/p structure
           					                $('#page #rendered').html('<h2>'+language['rota']+'</h2><p><button id="myrota" data-tab="#myrota" class="button">'+language["my-rota"]+'</button></p><div id="servicePicker" class="ui-field-contain"></div>');
           					                    //servicepicker
           					                var servicepicker=data.services;
           					                $('#page #rendered #servicePicker').append('<select class="tab-button" data-tab="#rota" id="serviceSelect">');
           					                $.each(servicepicker, function(arrayIndex, userObject){
  											                $('#page #rendered #servicePicker #serviceSelect ').append('<option value="' + userObject.rota_id+'" >'+ userObject.detail + '</option>');
										                });
										                $('#rendered #servicePicker').append('</select>');
								               }
								               if(data.tasks!=undefined)
								               {
           					                 var tasks=data.tasks;
										                 $('#page #rendered').append('<table>');
           					                 $.each(tasks, function(arrayIndex, userObject){
												                  $('#page #rendered').append('<tr><td>' + userObject.job+'</td><td>'+ userObject.people + '</td></tr>');
											               });
										                 $('#page #rendered').append('</table>');
								               }
			                    }
                  }//NO NEED TO LOGIN

        	});

        	}//end online
        	else
            {//not online
            	var language=JSON.parse(window.localStorage.getItem('language'));
            	var html='<p>'+language["disconnected"]+'</p>';
            	$("#page #rendered").html(html);
            }
        },


    }
    controller.initialize();

    return controller;
}
