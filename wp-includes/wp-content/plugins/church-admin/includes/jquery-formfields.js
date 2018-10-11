jQuery(document).ready(function($) {

			$('#btnAdd').click(function() {

				var num		= $('.clonedInput').length;	// how many "duplicatable" input fields we currently have

				var newNum	= new Number(num + 1);		// the numeric ID of the new input field being added

				// create the new element via clone(), and manipulate it's ID using newNum value
				var newElem = $('#input' + num).clone().attr('id', 'input' + newNum);

				// manipulate the name/id values of the input inside the new element
				newElem.find('.person').html(newNum);
				newElem.find('.hndle').attr('id',  newNum);
				newElem.find('.inside').attr('id',  'person'+newNum);
				newElem.find('.first_name').attr('id', 'first_name' + newNum).val('');
				newElem.find('.first_name').attr('name', 'first_name' + newNum).val('');
				newElem.find('.middle_name').attr('id', 'middle_name' + newNum).val('');
				newElem.find('.middle_name').attr('name', 'middle_name' + newNum).val('');
				newElem.find('.nickname').attr('id', 'nickname' + newNum).val('');
				newElem.find('.nickname').attr('name', 'nickname' + newNum).val('');
				newElem.find('.prefix').attr('id', 'prefix' + newNum).val('');
				newElem.find('.prefix').attr('name', 'prefix' + newNum).val('');
				newElem.find('.last_name').attr('id', 'last_name' + newNum).val('');
				newElem.find('.last_name').attr('name', 'last_name' + newNum).val('');
				newElem.find('.prefix').attr('id', 'prefix' + newNum).val('');
				newElem.find('.prefix').attr('name', 'prefix' + newNum).val('');
				newElem.find('.mobile').attr('id', 'mobile' + newNum).val('');
				newElem.find('.mobile').attr('name', 'mobile' + newNum).val('');
				newElem.find('.email_send').attr('id', 'email_send' + newNum).val('');
				newElem.find('.email_send').attr('name', 'email_send' + newNum).val('');
				newElem.find('.sms_send').attr('id', 'sms_send' + newNum).val('');
				newElem.find('.sms_send').attr('name', 'sms_send' + newNum).val('');
				newElem.find('.mail_send').attr('id', 'mail_send' + newNum).val('');
				newElem.find('.mail_send').attr('name', 'mail_send' + newNum).val('');
				newElem.find('.gdpr').attr('id', 'gdpr' + newNum);
				newElem.find('.gdpr').attr('name', 'gdpr' + newNum);
				newElem.find('.private').attr('id', 'private' + newNum).val('');
				newElem.find('.private').attr('name', 'private' + newNum).val('');
				newElem.find('.prayer_chain').attr('id', 'prayer_chain' + newNum).val('');
				newElem.find('.prayer_chain').attr('name', 'prayer_chain' + newNum).val('');
				newElem.find('.marital_status').attr('id', 'marital_status' + newNum).val('');
				newElem.find('.marital_status').attr('name', 'marital_status' + newNum).val('');
				newElem.find('.prayer_chain').attr('id', 'prayer_chain' + newNum).val('');
				newElem.find('.prayer_chain').attr('name', 'prayer_chain' + newNum).val('');
				newElem.find('.date_of_birth').attr('id', 'date_of_birth' + newNum).val('');
				newElem.find('.date_of_birth').attr('name', 'date_of_birth' + newNum).val('');
				newElem.find('.date_of_birth').removeClass("hasDatepicker");
				newElem.find('.date_of_birthx').attr('id', 'date_of_birth' + newNum+'x').val('');
				newElem.find('.date_of_birthx').attr('name', 'date_of_birth' + newNum+'x').val('');
				newElem.find('.date_of_birthx').removeClass("hasDatepicker");
				newElem.find('.custom-0').attr('name','custom-0-'+ newNum).val('');
				newElem.find('.custom-0').attr('id','custom-0-'+ newNum);
				newElem.find('.custom-0x').attr('id', 'custom-0-'+ newNum+'x').val('');
				newElem.find('.custom-0x').attr('name', 'custom-0-' + newNum+'x').val('');
				newElem.find('.custom-0x').removeClass("hasDatepicker");
				newElem.find('.custom-1').attr('name','custom-1-'+ newNum).val('');
				newElem.find('.custom-1').attr('id','custom-1-'+ newNum);
				newElem.find('.custom-1x').attr('id', 'custom-1-'+ newNum+'x').val('');
				newElem.find('.custom-1x').attr('name', 'custom-1-' + newNum+'x').val('');
				newElem.find('.custom-1x').removeClass("hasDatepicker");
				newElem.find('.custom-2').attr('name','custom-2-'+ newNum).val('');
				newElem.find('.custom-2').attr('id','custom-2-'+ newNum);
				newElem.find('.custom-2x').attr('id', 'custom-2-'+ newNum+'x').val('');
				newElem.find('.custom-2x').attr('name', 'custom-2-' + newNum+'x').val('');
				newElem.find('.custom-2x').removeClass("hasDatepicker");
				newElem.find('.custom-3').attr('name','custom-3-'+ newNum).val('');
				newElem.find('.custom-3').attr('id','custom-3-'+ newNum);
				newElem.find('.custom-3').attr('name','custom-3-'+ newNum).val('');
				newElem.find('.custom-3x').attr('id', 'custom-3-'+ newNum+'x').val('');
				newElem.find('.custom-3x').attr('name', 'custom-3-' + newNum+'x').val('');
				newElem.find('.custom-3x').removeClass("hasDatepicker");

				newElem.find('.custom-4').attr('id','custom-4-'+ newNum);
				newElem.find('.custom-4').attr('name','custom-4-'+ newNum).val('');
				newElem.find('.custom-4x').attr('id', 'custom-4-'+ newNum+'x').val('');
				newElem.find('.custom-4x').attr('name', 'custom-4-' + newNum+'x').val('');
				newElem.find('.custom-4x').removeClass("hasDatepicker");
				newElem.find('.custom-5').attr('id','custom-5-'+ newNum);
				newElem.find('.custom-5').attr('name','custom-5-'+ newNum).val('');
				newElem.find('.custom-5x').attr('id', 'custom-5-'+ newNum+'x').val('');
				newElem.find('.custom-5x').attr('name', 'custom-5-' + newNum+'x').val('');
				newElem.find('.custom-5x').removeClass("hasDatepicker");
				newElem.find('.custom-6').attr('id','custom-6-'+ newNum);
				newElem.find('.custom-6').attr('name','custom-6-'+ newNum).val('');
				newElem.find('.custom-6x').attr('id', 'custom-6-'+ newNum+'x').val('');
				newElem.find('.custom-6x').attr('name', 'custom-6-' + newNum+'x').val('');
				newElem.find('.custom-6x').removeClass("hasDatepicker");
				newElem.find('.custom-7').attr('id','custom-7-'+ newNum);
				newElem.find('.custom-7').attr('name','custom-7-'+ newNum).val('');
				newElem.find('.custom-7x').attr('id', 'custom-7-'+ newNum+'x').val('');
				newElem.find('.custom-7x').attr('name', 'custom-7-' + newNum+'x').val('');
				newElem.find('.custom-7x').removeClass("hasDatepicker");
				newElem.find('.custom-8').attr('id','custom-8-'+ newNum);
				newElem.find('.custom-8').attr('name','custom-8-'+ newNum).val('');
				newElem.find('.custom-8x').attr('id', 'custom-8-'+ newNum+'x').val('');
				newElem.find('.custom-8x').attr('name', 'custom-8-' + newNum+'x').val('');
				newElem.find('.custom-8x').removeClass("hasDatepicker");
				newElem.find('.custom-9').attr('id','custom-9-'+ newNum);
				newElem.find('.custom-9').attr('name','custom-9-'+ newNum).val('');
				newElem.find('.custom-9x').attr('id', 'custom-9-'+ newNum+'x').val('');
				newElem.find('.custom-9x').attr('name', 'custom-9-' + newNum+'x').val('');
				newElem.find('.custom-9x').removeClass("hasDatepicker");
				newElem.find('.custom-9').attr('id','custom-9-'+ newNum);
				newElem.find('.custom-10').attr('name','custom-10-'+ newNum).val('');
				newElem.find('.custom-10').attr('id','custom-10-'+ newNum);
				newElem.find('.custom-10x').attr('id', 'custom-10-'+ newNum+'x').val('');
				newElem.find('.custom-10x').attr('name', 'custom-10-' + newNum+'x').val('');
				newElem.find('.custom-10x').removeClass("hasDatepicker");



				newElem.find('.mt-1').attr('name','mt-1-'+ newNum).val('');
				newElem.find('.mt-1').attr('id','mt-1-'+ newNum).val('');
				newElem.find('.mt-1').removeClass("hasDatepicker");
				newElem.find('.mt-1x').attr('id', 'mt-1-'+ newNum+'x').val('');
				newElem.find('.mt-1x').attr('name', 'mt-1-' + newNum+'x').val('');
				newElem.find('.mt-1x').removeClass("hasDatepicker");

				newElem.find('.mt-2').attr('name','mt-2-'+ newNum).val('');
				newElem.find('.mt-2').attr('id','mt-2-'+ newNum).val('');
				newElem.find('.mt-2').removeClass("hasDatepicker");
				newElem.find('.mt-2x').attr('id', 'mt-2-'+ newNum+'x').val('');
				newElem.find('.mt-2x').attr('name', 'mt-2-' + newNum+'x').val('');
				newElem.find('.mt-2x').removeClass("hasDatepicker");

				newElem.find('.mt-3').attr('name','mt-3-'+ newNum).val('');
				newElem.find('.mt-3').attr('id','mt-3-'+ newNum).val('');
				newElem.find('.mt-3').removeClass("hasDatepicker");
				newElem.find('.mt-3x').attr('id', 'mt-3-'+ newNum+'x').val('');
				newElem.find('.mt-3x').attr('name', 'mt-3-' + newNum+'x').val('');
				newElem.find('.mt-3x').removeClass("hasDatepicker");

				newElem.find('.mt-4').attr('name','mt-4-'+ newNum).val('');
				newElem.find('.mt-4').attr('id','mt-4-'+ newNum).val('');
				newElem.find('.mt-4').removeClass("hasDatepicker");
				newElem.find('.mt-4x').attr('id', 'mt-4-'+ newNum+'x').val('');
				newElem.find('.mt-4x').attr('name', 'mt-4-' + newNum+'x').val('');
				newElem.find('.mt-4x').removeClass("hasDatepicker");

				newElem.find('.mt-5').attr('name','mt-5-'+ newNum).val('');
				newElem.find('.mt-5').attr('id','mt-5-'+ newNum).val('');
				newElem.find('.mt-5').removeClass("hasDatepicker");
				newElem.find('.mt-5x').attr('id', 'mt-5-'+ newNum+'x').val('');
				newElem.find('.mt-5x').attr('name', 'mt-5-' + newNum+'x').val('');
				newElem.find('.mt-5x').removeClass("hasDatepicker");

				newElem.find('.mt-6').attr('name','mt-6-'+ newNum).val('');
				newElem.find('.mt-6').attr('id','mt-6-'+ newNum).val('');
				newElem.find('.mt-6').removeClass("hasDatepicker");
				newElem.find('.mt-6x').attr('id', 'mt-6-'+ newNum+'x').val('');
				newElem.find('.mt-6x').attr('name', 'mt-6-' + newNum+'x').val('');
				newElem.find('.mt-6x').removeClass("hasDatepicker");

				newElem.find('.mt-7').attr('name','mt-7-'+ newNum).val('');
				newElem.find('.mt-7').attr('id','mt-7-'+ newNum).val('');
				newElem.find('.mt-7').removeClass("hasDatepicker");
				newElem.find('.mt-7x').attr('id', 'mt-7-'+ newNum+'x').val('');
				newElem.find('.mt-7x').attr('name', 'mt-7-' + newNum+'x').val('');
				newElem.find('.mt-7x').removeClass("hasDatepicker");

				newElem.find('.mt-8').attr('name','mt-8-'+ newNum).val('');
				newElem.find('.mt-8').attr('id','mt-8-'+ newNum).val('');
				newElem.find('.mt-8').removeClass("hasDatepicker");
				newElem.find('.mt-8x').attr('id', 'mt-8-'+ newNum+'x').val('');
				newElem.find('.mt-8x').attr('name', 'mt-8-' + newNum+'x').val('');
				newElem.find('.mt-8x').removeClass("hasDatepicker");

				newElem.find('.mt-9').attr('name','mt-9-'+ newNum).val('');
				newElem.find('.mt-9').attr('id','mt-9-'+ newNum).val('');
				newElem.find('.mt-9').removeClass("hasDatepicker");
				newElem.find('.mt-9x').attr('id', 'mt-9-'+ newNum+'x').val('');
				newElem.find('.mt-9x').attr('name', 'mt-9-' + newNum+'x').val('');
				newElem.find('.mt-9x').removeClass("hasDatepicker");

				newElem.find('.mt-10').attr('name','mt-10-'+ newNum).val('');
				newElem.find('.mt-10').attr('id','mt-10-'+ newNum).val('');
				newElem.find('.mt-10').removeClass("hasDatepicker");
				newElem.find('.mt-10x').attr('id', 'mt-10-'+ newNum+'x').val('');
				newElem.find('.mt-10x').attr('name', 'mt-10-' + newNum+'x').val('');
				newElem.find('.mt-10x').removeClass("hasDatepicker");



				newElem.find('.email').attr('id', 'email' + newNum).val('');
				newElem.find('.email').attr('name', 'email' + newNum).val('');
				newElem.find('.sex').attr('id', 'sex' + newNum).val();
				newElem.find('.sex').attr('name', 'sex' + newNum).val();
				newElem.find('.twitter').attr('id', 'twitter' + newNum).val();
				newElem.find('.twitter').attr('name', 'twitter' + newNum).val();
				newElem.find('.facebook').attr('id', 'facebook' + newNum).val();
				newElem.find('.facebook').attr('name', 'facebook' + newNum).val();
				newElem.find('.twitter').attr('id', 'twitter' + newNum).val();
				newElem.find('.twitter').attr('name', 'twitter' + newNum).val();

                newElem.find('.member_type_id').attr('id', 'member_type_id' + newNum).val('');
                newElem.find('.member_type_id').attr('name', 'member_type_id' + newNum).val('');
               	newElem.find('.attachment_id').attr('id', 'attachment_id' + newNum).val('');
               	newElem.find('.attachment_id').attr('name', 'attachment_id' + newNum).val('');
               	newElem.find('.file-chooser').attr('id', 'file-chooser' + newNum).val('');
               	newElem.find('.file-chooser').attr('name', 'logo' + newNum).val('');
                newElem.find('.people_type_id').attr('id', 'people_type_id' + newNum);
                newElem.find('.people_type_id').attr('name', 'people_type_id' + newNum).val();
                newElem.find('.smallgroup_id').attr('name', 'smallgroup_id' + newNum+'[]');
                newElem.find('.smallgroup_id').attr('id', 'smallgroup_id' + newNum).val();
                newElem.find('.smallgroup').attr('name', 'smallgroup' + newNum);
                newElem.find('.smallgroup').attr('id', 'smallgroup' + newNum).val();
                newElem.find('.site_id').attr('id', 'site_id' + newNum).val();
                newElem.find('.site_id').attr('name', 'site_id' + newNum).val();
                newElem.find('.frontend-button').attr('id', newNum).val();
                //give the media upload button the newNum value for image processing
                newElem.find('.frontend-button').attr('id', newNum);

                newElem.find('.frontend-image').attr('id', 'frontend-image'+newNum);

				// insert the new element after the last "duplicatable" input field
				console.log('Cloned');
				$('#input' + num).after(newElem);

				$('#fields').val(newNum);

				// enable the "remove" button
				$('#btnDel').prop( "disabled", false );

				// business rule: you can only add 50 names
				if (newNum == 50)
					$('#btnAdd').prop( "disabled", true );

				$('#fields').val(newNum);
			});

			$('#btnDel').click(function() {
				var num	= $('.clonedInput').length;	// how many "duplicatable" input fields we currently have
				$('#input' + num).remove();		// remove the last element
				$('#fields').val(num-1);
				// enable the "add" button
				$('#btnAdd').prop( "disabled", false );

				// if only one element remains, disable the "remove" button
				if (num-1 == 1)
					$('#btnDel').prop( "disabled", true);
			});

			$('#btnDel').prop( "disabled", "disabled" );


});
