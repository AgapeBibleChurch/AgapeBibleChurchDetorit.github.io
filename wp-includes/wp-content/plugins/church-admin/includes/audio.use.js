jQuery(document).ready(function( $ ) {
				bindEvents();
				$("body").on('click',".ca-media-file-list .more-sermons",function()
				{
					var page=$(this).attr('data-page');
					var data = {
						"action":  "church_admin",
						"method": "more-sermons",
						"page":page
					};
					console.log(data);
					$.ajax({
						url: ChurchAdminAjax1.ajaxurl,
						type: 'post',
						data:data,
						success: function( response ) {
							console.log(response);
							$(".ca-media-file-list").html(response);
							bindEvents();
						},
					});

				});
				$(".sermons-tab").click(function()
					{
						$(".sermons-tab").removeClass("ca-podcast-tab");
						$(".sermons-tab").addClass("ca-podcast-tab-active");
						$(".series-tab").removeClass("ca-podcast-tab-active");
						$(".series-tab").addClass("ca-podcast-tab");
						$(".ca-media-file-list").show();
						$(".ca-media-series-list").hide();
					});
				$(".series-tab").click(function()
					{
						$(".series-tab").removeClass("ca-podcast-tab");
						$(".series-tab").addClass("ca-podcast-tab-active");
						$(".sermons-tab").removeClass("ca-podcast-tab-active");
						$(".sermons-tab").addClass("ca-podcast-tab");
						$(".ca-media-file-list").hide();
						$(".ca-media-series-list").show();
					});
				$("body").on("click",".ca-tabs",function()
					{
						var active =$(this).attr("id");

						$(".ca-tabs").removeClass("ca-podcast-tab-active");
						$(".ca-tabs").addClass("ca-podcast-tab");
						$(this).removeClass("ca-podcast-tab");
						$(this).addClass("ca-podcast-tab-active");
						$(".ca-tab-content").hide();
						$("."+active).show();
					});
				$("body").on("click",".ca-series-list-item",function()
				{

					var series =$(this).attr("id");
					$("#sermons"+series).toggle();
				});
				$(".ca-media-list-item").click(function()
				{
					var id=$(this).attr("id");

					var data = {
								"action":  "church_admin",
								"method": "podcast-file",
								"id":id
					};
					console.log(data);
					$.ajax({
        				url: ChurchAdminAjax1.ajaxurl,
        				type: 'post',
        				data:data,
        				success: function( response ) {

            					$(".ca-podcast-left-column").html(response);
            					bindEvents();
        				},
    				});

				});
				$(".ca-series-list-item").click(function()
				{
					var id=$(this).attr("id");

					var data = {
								"action":  "church_admin",
								"method": "series-detail",
								"id":id
					};

					$.ajax({
        				url: ChurchAdminAjax1.ajaxurl,
        				type: 'post',
        				data:data,
        				success: function( response ) {

            					$(".ca-series-current").html(response);
            					bindEvents();
        				},
    				});


				});
				$('.ca-podcast-list').on('scroll', function() {
						if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
							var lastDate= $('.ca-media-file-list').children().last().data('date');

							var data = {
								"action":  "church_admin",
								"method": "more-sermons",
								"date":lastDate
							};
							console.log(data);
							$.ajax({
								url: ChurchAdminAjax1.ajaxurl,
								type: 'post',
								data:data,
								success: function( response ) {

									$(".ca-media-file-list").append(response);
									bindEvents();
								},
							});
						}

				});
		function bindEvents(){

				$('body .sermonmp3').on('playing',function(){

					var fileID=$(this).attr('id');

					var data = {file_id: fileID	,security:ChurchAdminAjax.security};
					console.log(data);
					jQuery.post(ChurchAdminAjax.ajaxurl, { 'action': 'church_admin','method':'mp3_plays','data':   data },
						function(response){

							$('body .plays').html(response);

						}
					);

				});
			}
});
