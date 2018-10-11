=== Church Admin ===
Contributors: andymoyle
Donate link: http://www.churchadminplugin.com/
Tags: church admin, church management, sms, small groups, rota, email, address list, calendar, schedule
Requires at least: 3.0
Tested up to: 4.9
Stable tag: 1.4590

A fully featured administration backend for your church, with associated Android and iOS smartphone app

== Description ==

This plugin is for church wordpress sites and has an smartphone app too - it adds an easy to use address directory and you can email and sms different groups of people.

*   Small Groups - add, edit and delete

*   Members - add, edit and delete

*   Email- send an email to members, parents or small group leaders. Now has a template - make sure you update your settings to include Facebook page and twitter if you use them!

* Directory syncs to Mailchimp (not back yet)

*   SMS - send bulk sms to members using www.bulksms.co.uk account (not just UK!)

*   Sunday Rota - create and show rotas for your volunteers.
* Kidswork - automatically sort children into their age groups, with manual override
*   Attendance tracking

*   Ministries - people can have different ministries they are involved in and be sent SMS or email by role, other functions coming soon.

*   Google map integrations for small groups and directories

*   Calendar - month to view, agenda view and nth day recurring events (eg 3rd Sunday)
*  Facilities - manage facilities like rooms and equipment and their bookings.
*   The calendar now includes that most powerful of planning tools - the year planner!

== Installation ==

1. Upload the `church_admin` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place [church_admin type=address-list member_type_id=# map=1 photo=1] on the page you want the address book displayed, member_type=1 for members, map=1 toshow map for geocoded addresses. The member_type_id can be comma separated  e.g. member_type_id=1,2,3
4. Place [church_admin type=small-groups-list] on the page you want the small group list displayed
5. Place [church_admin type=small-groups ] on the page you want the list of small groups and their members displayed
6. Place [church_admin type=rota] on the page you want the rota displayed
7. Place [church_admin type=calendar category=# weeks=#] on the page you want a page per month calendar displayed
8. Place [church_admin type=calendar-list] on the page you want a agenda view calendar - option category and weeks options pastable from category admin page
9. There is a calendar widget with customisable title, how many events you want to show and an option for it to look like a post-it note
10. Place [church_admin_map member_type_id=#] to show a map of colour coded small groups - need to set a service venue first to centre map and geolocate member's addresses by editing them.
We recommend password protecting the pages - if it is password protected, a link is provided to logout
The # should be replaced with which member types you want displayed as a comma separated list e.g. member_type=1,2

Join our email list to receive a free PDF manual - http://eepurl.com/b7nniP

== Frequently Asked Questions ==

http://www.churchadminplugin.com/support/

== Screenshots ==
1. Address list display
2. Sermon podcasting display
3. Calendar display
4. Rota / Schedule display



== Changelog =
= 1.4590 =
* App menu title edits save
* Custom date filtering fix
* App logo fix
= 1.4581 =
* Minor adjustments for app
* Fixed bulk sms not sending
= 1.4580 =
* Filtering of custom date field now dropdown for more than 20 date
* Cronbackup and cronemail files not overwritten with wrong path
= 1.4570 =
* New user email subject working for filtered list create user click
=1.4560 =
* Most recent 10 sermons not showing bug fixed
= 1.4550 =
* Fix attendance meeting chooser
* Fix graphs chooser
= 1.4540 =
* Fix sermon podcast display where no series set.
* Sermon series image upload & App logo update now uses native Wordpress Image Uploader
* Customise email subject for new user email template.
= 1.4530 =
* Removed delete plugin code that wasn't working
* Fixed non use of new user email template when clicking "Create User"
= 1.4520 =
* Recent Posts and comments widgets change for Prayer requests and Bible readings archives
* Logged in only sermons bug fixed
* Pagination on sermons
* Improved sermons layout on thin sites
= 1.4510 =
* Edit individual attendance by editing date and previous entries are checked
* Removed autocomplete bug for new installs rota/schedule settings
* Ministries and predefined custom fields added to CSV import
= 1.4500 =
* Restore kidswork pdf link on Children tab
* Fix incorrect number of unconfirmed people on GDPR compliance box
* Fix private prayer requests on app
= 1.4400 =
* Directory PDF download from People tab
* Site set when clicking add person on edit/add person form
* Added active/deactivated to people filter
* Custom fields allow default value and option to assign default to everyone who doesn't have a value for that custom field
* [CONFIRM_LINK] available for use in GDPR email message
* Fixed bug for using cron email send with GDPR
* Better handling of member levels dates under the hood
* Edit the new user email in General Settings
* Creating a new user, updates the directory with the user identity automatically
* Checkin functionality added for app users on next app update
= 1.4300 =
* Custom field boolean working
* App allows checkin to class, service and small group on that day for individual attendance tracking
= 1.4200 =
* [church_admin type="restricted" member_type_id="#"]Some Content[/church-admin] restricts content to logged in users of the specified member levels (use the integer for required member levels and comma separate if more than one e.g.1,2)
= 1.4100 =
* Fix for connect username when editing person
* Fix for individual attendance CSV not toggling
= 1.4000 =
* Fix for safeguarding date not saving
= 1.3999 =
* Further filtering fix for small group and ministries in same query
= 1.3998 =
* CSV download fix for ministries
* Site image upload fix
* Rota/Schedule Copy fixed
* Safeguarding data entry dates now save
* App licence checked automatically, so no need to enter anymore
* Mailing label download fix.
= 1.3997 =
* Prayer request submission display error fix
* CSV download fixed on sites where it wasn't working
= 1.3996 =
* Tidy up sermon upload and editing
* Fix GDPR message [CONFIRM_URL] issue
= 1.3995 =
* Bug fixes for graphs.
= 1.3994 =
* Added GDPR to Filtering
* Tidied filtering boxes -click shift refresh to get latest style!
* Button to create user accounts for GDPR confirmed directory entries
= 1.3993 =
* GDPR email template saves
* Filtering shows numbers of people for member type
* Clear debug log deletes debug log.
= 1.3992 =
* GDPR email bug fix
* Missing people tab content restored
= 1.3990 =
* GDPR email message template is now editable in Church Admin>Settings>General Settings
* Filtering includes year and month of birth and custom fields
* If no Google map api stored, the map doesn't show on edit address screens.
* Custom field delete bug fixed
= 1.3980 =
* Fixed non sending of auto rota/schedule email on some servers
= 1.3970 =
* Fix some issues for rota/schedule menu on fresh installs
* Create and delete multiple auto email of rota/schedule on the same/different days
= 1.3960 =
* Fix for "Click to toggle" not working on some people editing
* Fix for front end attendance graphs not showing
* My rota/schedule shown in date order
* Batch geocode button for all households
* Correct wording for address edit of services
= 1.3950 =
* Fix address list for themes that insert tags into Javascript whitespace
= 1.3940 =
* Fix Children's tab to show all items
= 1.3930 =
* Fix gender issues with CSV import
= 1.3920 =
* Remove force login for podcast shortcode
= 1.3910 =
* Fix for Android not picking up date for bible reading
* Saved marital status changes show in filter option screen
* Add custom CSS to app (ready for app version 2.8)
= 1.3900 =
* Fix marital status not updating
* Fix first install db error
= 1.3800 =
* SMS & Email communications only send to people with GDPR consent
* Fix directory issues concerning people order and head of household
= 1.3700 =
* Fixed directory bug where multiply entries of same household appeared
* fixed fatal error in app admin
= 1.3600 =
* Fix directory crash
= 1.3500 =
* Fixed app class check in
* Directory removed "time to produce" message for non-admins
* Make sure every household has a head of household set
* Tidy up multiple person edit on [church_admin_register]
= 1.3400 =
* Remove image buttons on editing people and households
* App upgrade - login only setting for rota/schedule for data protection (app v2.7 required)
* App upgrade - show multiple my groups if in more than one group (app v2.7 required)
* Use updated marital status on edit people forms
= 1.3300 =
* [church_admin_map] can be use loggedin=TRUE flag if you only want it visible to logged in users.
* Add an instruction message to prayer request submission form.
* Front end register edit bug fixed
= 1.3200 =
* Categories bug fixed on calendar widget
* Submit prayer request form on Prayer Requests Archive Page
* Fixed [church_admin_register] duplicating entries if already on directory and other bugs.
= 1.3100 =
* Latest sermon can be sent out
* Fix issues for people with really old PHP install running. Tut, Tut!
= 1.2900 =
* Bug fix for small group list
* Fixes for users using Themify themes
= 1.2700 =
* Bug fix for sermon podcast
= 1.2608 =
* Prayer Requests and Bible reading receiving are now ministries so MailChimp can be used for sending out. Directories are updated, but please remember to sync MailChimp and add those ministries.
* Mailchimp sync errors fixed when sites, smallgroups, member types or ministries not set to be synced
* Mailchimp synchronises parents of children in your various children's groups so they can be sent segmented emails.
* Parents of kids in kids work filter added
* Menu configuration for upcoming App version 2.6
= 1.2607 =
* Individual attendance is editable, by picking date
* Added links to calendar events
* Option to show description on calendar widget
* Fix sermon series image upload
* Exclude description, date, download, or plays from Sermon file using 'exclude="description,date,download,plays"' in the shortcode
= 1.2606 =
* Bug fix for address list PDFs where some entries don't have an image_get_intermediate_size
* Static map showing on address list
* Click on address in back end edits household rather than displaying it
= 1.2605 =
* Back end search missing prefix added, missing rota and podcast address added
* Import CSV gender bug fixed
* Address List display shortcode first_initial=1 adds head of household's initial to the last name on the left
* Static google maps not showing in the back end fixed for users who don't have cURL enabled
= 1.2604 =
* PDF photos fixed
* Map fixed on new directory for those users it wasn't working for.
= 1.2603 =
* Manual Refresh link for directory
* Address list style="old" now works
= 1.2601 =
* Missing PDF with photo function added
= 1.2600 =
* Improved Address list with cacheing. Add style=old if you want the old style still
* Household photo added to new household/edit household and register admin screens
* Diacritics handled for small group and member maps
= 1.2591 =
* Email saves for create new user
= 1.2590 =
* Backend calendar working on older PHP installs
* Lost password link for "loggedin=TRUE" shortcodes
= 1.2580 =
* Create user accounts from "filter results" and "view household" screens (so users can log in on app and login protected pages)
* New directory entries without address are created in a new household (fixing bug where they were appended to current no address household)
= 1.2570 =
* PDF mailing labels returns using filters to choose recipients
= 1.2560 =
* Fix pagination for site where it clashed with another plugin
* Show how many people have not confirmed they are happy for personal data to be stored
* Bulk GDPR confirm - not recommended!
= 1.2550 =
* GDPR PDF prints diacritics correctly
* Fixed CSRF risk for uploading bible reading plan in the app
* Fixed error for nth day classes
* Prettier small groups times
= 1.2530 =
* Fixed missing settings sections (which were hidden under Bulk SMS settings)
* Fixed some missing translation strings
* PDF Manual link at top of screen
* Tidied up iTunes Podcast Settings form
= 1.2520 =
* Fixed issue where some people not shown in pdf or app
* Fixed issue where prefix was missing from last name, when required
* Back end search now shows schedule entries
* Fixed an issue where some users couldn't update bulk SMS settings
* Add new hosehold added to Admin Toolbar
* Use prefix where needed for prayer chain
* Changed download variable to prevent clash with ChurchTheme
= 1.2510 =
* Individual attendance CSV bug fixed
= 1.2500 =
* Fix rota pdf from front end shortcode
* Address list shows everyone if no member_type_id specified
* Rota CSV fixed and shows accents correctly
= 1.2490 =
* More diacritic issues fixed for PDFs
* Year planner pdf fixed
= 1.2480 =
* Use today=TRUE for classes shortcode to only show classes meeting today
= 1.2470 =
* Fix Rota PDF sizing
* weeks=5 in rota shortcode works now
= 1.2460 =
* Fixed bug where not everyone shows up on individual attendance checklist
* Fixed fresh install bug for missing table
= 1.2450 =
* Individual attendance done by checkbox
* Individual attendance CSV download
= 1.2440 =
* Private households no longer shown on app searches
* Simpler app signup screen
* Bible Reading post type sent out to Prayer Request subscribers
= 1.2430 =
* Fix notes not showing one edit people and household screens
= 1.2420 =
* Fix bug where full class not shown to logged in leader
= 1.2410 =
* Fixed bug for front end register when no exclude="" used in shortcode
* Made form fields required on Classes so that editing and adding is smoother
= 1.2400 =
* Fixed classes not saving on editing people
* View older rota/schedules in backend
= 1.2300 =
* Added exclude="" to front_end_register shortcode to not show various fields
* Marital Status options now editable
= 1.2200 =
* CSV download fix
* Small group edit error fixed
= 1.2100 =
* Fix for Mailchimp only syncing one contact
= 1.2000 =
* Fix for site saves on edit people
* Mailchimp synchronisation made two way.
* Sermon podcast upload and add files allows m4a as well as mp3
* Fix for address list pdf with photo
* Email confirmation request for email, mail and sms
* [church_admin_register] allows editing for logged in users of their household
* Switched to Bible Gateway for Bible passages to get multi-language modern versions
* Bible Reading plan post type
* Use media uploader for edit people in the backend.
= 1.1690 =
* PDFs handle diacritics properly (accented characters)
* Single event classes save to calendar issue fixed
* People Type shows saved value on edit people form
* Email unsubscribe added to emails sent by bulk email
* Add new household saving people issue fixed
= 1.1680 =
* Bulk SMS now removes leading zero when texting in France as well as UK
= 1.1670 =
* Fix for attendance graphs
= 1.1660 =
* Bug fixes for classes
= 1.1650 =
* Sermon upload db bug fixed
* Editing a person, disconnects their user account bug fixed (edit again to reconnect them)
* Calendar widget now uses your Wordpress time display setting
* Database table clear up error message fixed
* DB backup now saves all tables
= 1.1620 =
* Revamp of Classes section, to add class leader and allow booking by logged in users and check in by leaders
* Prayer Request email checkbox saves on edit people screen
* Member level dates not saving bug fix
* Upgrade no longer saves people's small groups from v0.9
* Small group - day and time (please update)
* Services may show as wrong day in the back end, so please edit
= 1.1600 =
* Previously saved gender shown in edit people screen
* Sermon notes and bible passages added to sermon podcast
* Calendar list bug fix
= 1.1510 =
* Calendar list shortcode categories now work
= 1.1500 =
* Sermons display updated including series order by date of last sermon
* Fixed occasional non display of rota in backend if only one service
* App now plays external media files as well as site hosted ones.
* Admin email sent when draft prayer request sent from app
= 1.1300 =
* Internationalise Bible Reading Plan for app - French, German, Dutch, Swedish, Norwegian
= 1.1250 =
* Removed slash from rota entries that include apostrophes
* Calendar list updated so that it does the next x days [church_admin type="calendar-list" days="28"]
= 1.1240 =
* Fixed surname not showing if no head of household set
= 1.1230 =
* Fixed gender always male on save
* Email no longer required for people editing
* App tab link fixed
* Fix date picker bug for attendance
= 1.1220 =
* Add people type to import CSV and fix address handling
= 1.1210 =
* First name, last name, email and address made required fields in forms
= 1.1200 =
* Fix week calendar for any day  as week begins
* Fix spacing of names in small group listing on admin screen
= 1.1100 =
* Added missing calendar file
= 1.10 =
* Check and remove auto rota email wp-cron jobs under rota tab, communicate section
* Unsubscribe link from emails no longer requires shortcode
* Add new household and front end register allow multiple small group membership
* Google map issue in address editing fixed
* Calendar shortcode design made responsive
* Fixed csv import issue when date of birth or gender are first column
* Deleting a person, deletes their login
* Deleting a user, releases person to have new user account in the directory
* Option in settings to make prayer requests only viewable by logged in users
* Option in settings to allow social media handles to be saved in the directory
* Simplify calendar widget output as design breaks on most sites.
* Rota date dropdown fixed
= 1.0978 =
* Make Photo PDF option conditionally on photo=TRUE in shortcode
= 1.0977 =
* PDF with photos option
* Unsubscribe email bug fix
* Address Form map geolocation bug fix
= 1.0976 =
* Fix push notifications for app
= 1.0975 =
* Users can set time format for app
* Directory record can be disconnected from a wordpress user on edit people screen
= 1.0974 =
* Directory edit people uses wordpress native uploader
* Show which speaker or series is selected on sermon podcast shortcode
* Shortcode list updated
* Address list formatting
* Import CSV includes marital status
= 1.0973 =
* Calendar multi event spacing reduced
* Calendar list shortcode can have comma separated categories
* All day event
* Fix for no category set on date
* Fix for white background popup
= 1.0972 =
* Address list pagination setting fixed
* Google API Static map fixed for display household( ned to enable Static map api)
= 1.0971 =
* SMS submit button fixed
* Directory pagination limit fixed
= 1.0970 =
* Link to create user accounts for all/some people in directory
= 1.0969 =
* Fix pagination for address list shortcode
= 1.0968 =
* Auto rota email has user message
* Fix wp-cron for auto rota email
* Fix push notification subscribe*
Translation strings fixes
= 1.0967 =
* CSV download of directory fixed
= 1.0966 =
* Fix directory display
= 1.0965 =
* Translation fixes
* App administration updates for editing home, giving and group pages
* App server key entry for push notifications
* Bulk Email send button disabled until recipients chosen
* Directory address if no data saved, will try HTML5 geolocating where user is for map
= 1.0963 =
* Rota auto email fixed and allows setting for different services
= 1.0962 =
* Dropdown sermon filtering
* Front end register - new household every time if no address given
* Front end register - first named person is head of household
* Date of services added to rota email
* Cancelled auto rota email not defaulting to showing Thursday
* Rota shortcode choose month dropdown
* SMS fix for US installs.
Adjustable width for graph - specify height and width as integer pixels
= 1.0961 =
* Choose months on Rota shortcode
= 1.0961 =
* Removed error for older PHP versions on install
= 1.096 =
* Add 3 months to rota fixed
= 1.095 =
* Ministries connected to rota/schedules tasks, so you can have checkbox selecting of people
* Rotas stored differently for better retrieval
* Calendar widget ordered by date and time
* Safeguarding section in Children's tab for UK and Australia
* Autocomplete working again
* Attendance graphs and editing now cover classes, groups and services
* Demo app updated
* Reset version link in case corrupted install/upgrade
= 1.092 =
* Fix non-translation of people type and gender
* Remove app nag message causing errors for some users
* Remove cron nag message causing errors for some users
= 1.090 =
* Fix for install crash in admin.php
= 1.088 =
* Fix error on update and fresh install
= 1.087
* Attendance graph shows in backend under services section
* Fix for front end register email
* Improved error notices
= 1.086 =
* Improved attendance graphs, using new shortcode [church_admin type=graph]
= 1.084 =
* Database efficiency
* Kidswork led by ministry fixed
= 1.083 =
* Modernise calendar style
* Fix edit calendar category
= 1.081 =
* Fix "Add/Edit Classes" where how many times is not specified recurring class
= 1.080 =
* Fix church_admin_map shortcode
= 1.079 =
* Update app screen to show features of app, demo and sign up link
= 1.078 =
* Show already saved oversight ministry on update ministry form
* Shortcode extra updateable=FALSE for address list if you don't want the edit link to show
* App demo page
* Kidswork pdf selectable by member type
= 1.075 =
* Fix audio plugin showing when video file is present but no audio file
= 1.075 =
* Fix Add new follow up funnel
= 1.074 =
* Make small group shortcode able to be restricted loggedin=TRUE restricted=TRUE - only those involved see their group's list
= 1.073 =
* Fix for translated installs that breaks rota/schedule email & sms notifications
* Fix for notes not showing under small groups
* Note deletion
* Fix permissions hidden on settings page
* Fix only show small groups on admin screen to people assigned as leaders to that group and site admins
= 1.072 =
* Fix rota autosend
* Fix  email out rota for immediate method
* Fix notes
= 1.071 =
* Add optional nickname to people
* Updated translations
* Fix front end sessions
* Small group editing now uses autocomplete for leadership section
* Fixed add 3mths rota jobs for non English translations bug
* Fix people missing from small group in sessions if no phone number
= 1.070 =
* Fix for crash on old versions of PHP
* SMS filter fixed
= 1.069 =
* Comments table installed on fresh installs
= 1.068 =
* Loggedin shortcode bug fixed
= 1.067 =
* Test email link for debugging email issues
= 1.066 =
* Rota display not using specified service_id from shortcode fixed
= 1.065 =
* Adding loggedin=TRUE to shortcode makes it available to logged in users only.
= 1.064 =
* Edit people allows username creation (defaults to firstnamelastname)
= 1.063 =
* Ministries filter fix
= 1.062 =
* Email sends with first name
= 1.061 =
* Small groups email fix
= 1.06 =
* Filter ministries bug fix
* Search name bug fix
* Date picker bug fix
= 1.05 =
* Use filter for choosing email recipients
* Fix ministries pdf
* Fix create new ministry while editing person
* Fix create new small group while editing person
* Commented by name fixed
* Frontend Ministries list fixed
* Email from name and address fixed
* Gmail SMTP warning added to settings screen
* People drag and drop when viewing household saves
= 1.04 =
* Email not sending bug on some servers
= 1.03 =
* CSV download now using filters
= 1.01 =
* Clean up datepicker in edit people screen
= 1.0 =
* Filtered address list in admin area
* Bug fixed for podcast admin permissions
= 0.965 =
* Keep track of small group life with session
* Bug fix for multiple emails
* bug fix for deleting a person
= 0.964 =
* Overseers for ministries, especially small groups
= 0.962 =
* CSV upload assumes first named in each address is head of household
* CSV upload allows for multi-column address details
= 0.961 =
* Cron backup added
= 0.960 =
* Save notes for people, households, small groups and classes
* Social media usernames added for people
= 0.958 =
* Make small group map pointers work on ssl
= 0.957 =
* Wordpress account dropdown missing fixed
= 0.956 =
* First named person in household is head of household for address list sorting
= 0.954 =
* Cron email fix
* Ministry list
= 0.953 =
* Small group fix for new installs
* small group saved when adding new household
= 0.952 =
* Edit and resend old emails
* Sites fix on editing
= 0.949 =
* Fix for site and marital status on re-editing a person
* Fix for sending email to small group
= 0.948 =
* People activity edit bug fix
= 0.947 =
* Multi-site install bug fix
* Recent people edit bug fix
= 0.946 =
* Attendance added to services tab
= 0.945 =
* Multi-site capability
* Improved choosing of member types for different functions
= 0.944 =
* Add date of birth and small groups to new household form
* Setting to use prefixes on names (default yes)
= 0.943
* Fix rota being emailed every day bug introduced in 0.940
= 0.942 =
* Fixed can't edit people after searching bug
= 0.941 =
* Fix Permissions & individual permissions
= 0.940 =
* Fix bugs with auto rota send and resend emails
* Add sms rota
= 0.936 =
* Show 4 weeks of rotas for rota shortcode
= 0.935 =
* Adding new household with identical address doesn't overwrite existing one
= 0.934 =
* Add 3 months of dates for any service's rota/roster
= 0.933 =
* Show correct service details when edit rota for a certain date
= 0.932 =
* Urgent rota bug fix
= 0.931 =
* Refactored rota code to remove multiple service bugs
= 0.923 =
* Add names shortcode to just display names
= 0.922 =
* Fix google maps shortcode
* Fix services map
= 0.921 =
* Remove api_key from static maps on address list which doesn't work
* Fixed people saved as male on new household edit in backend
= 0.920 =
* Front end register used for editing an entry if church_admin_register shortcode used
* Ministries bugs fixed
= 0.914 =
* Edit small group map bug fixed
* Smallgroup PDF bug fixed
* Google API key added to back end maps - must be saved in settings!
= 0.913 =
* Fix autocomplete not working
* Add ministries shortcode
* Fix email send not working for "Type in Names"
= 0.912 =
* Quickly add 3 months of dates to service rota
* Fix missing link to permissions
= 0.911 =
* Fix cron bug and move cron path
= 0.910 =
* People Small groups stored more efficiently
* Add/remove people to a small group while creating it
* Recent shortcode bug fixed
* Prefix spacing issues fixed
* Code tidy up for enqueuing scripts
= 0.908 =
* Are you sure? Confirmation popup for deletes
* Dutch translation
= 0.907 =
* Fixed issue with CSV import and internationlised.
= 0.906 =
* Migrate old smtp settings over
* SSL enqueue of google map scripts

= 0.905 =
* Rota issue when services deleted fixed
= 0.904 =
* Error when trying to send to small groups by sms or email fixed
= 0.903 =
* Email type set for new users
* Member type select for SMS added
* Fix SMS Norwegian characters
* member type select for email fixed
= 0.902 =
* Update move household dropdown to include first name
= 0.900 =
* Option to not show all modules
* Major rewrite of Email function
* Fix smtp settings - so you can use gmail to send your emails from your site.
= 0.856 =
* Option to blog sermon posts
* Rota table edits, submitted onblur rather than after pressing enter
* Rota edits bug fix for non-autocomplete
* Bulksms ssl bug fix
= 0.855 =
* Individual attendance bug fix
= 0.854 =
* Translation errors fixed
* Horizontal PDF form hidden until clicked in rota
= 0.853 =
* Install table bugs fixed
* Add Classes bug
= 0.852 =
* Improved styling for Calendar widget
* Daily calendar item bug fix
* Private option for households, so not displayed publicly
* Schedule Emails
* Fix front end register gender save
= 0.841 =
* Bug fix to Media admin page
* Jquery formfields update
= 0.840 =
* Added delete follow up funnel
= 0.839 =
* Fix autocomplete bug where title has whitespace in it
= 0.838 =
* Simplified add new household form and screen
* Bug fixes for follow up funnels
* Kids work groups sorted by youngest age
* Rota/Schedule CSV export bug fix
= 0.837 =
* Fix admin css table borders
* Fix front end register gender dropdown
= 0.836 =
* First install db table install
= 0.835 =
* Various minor bug fixes for email and sms
= 0.834 =
Admin page bug fix
= 0.833 =
* Facilities section bug fixes
= 0.832 =
* Rota emails sent with plain text & HTML (working on rest of plugin)
* Added Classes
* Changed gender to a dropdown
* Some translation fixes
= 0.830 =
* Classes
= 0.822 =
* Update US translation
= 0.821 =
* Archived email list and ability to resend
= 0.820 =
* Search now searches address list, rota and sermon podcasts
= 0.819 =
* Search fix, rota tab for choosing service, podcast service dropdown bug fix
= 0.818 =
* Kidswork bug fix
= 0.816 =
* Added "kids" attribute to address list shortcode; 0 stops them showing
= 0.814 =
* Added facilities to main tabs
= 0.813 =
* Sends pings for page with podcast shortcode when sermon file added/edited
* Fix old style rota edit layout
* Json api working - use ca_app GET variable to access data
* Make BulkSMS api work for non UK accounts - goto settings to setup!
= 0.812 =
* Rota setting bug fix
= 0.811 =
* Fixed bugs in rota and editing small groups
= 0.810 =
* Fixed XSS vulnerability in front end register and sermon podcast
= 0.800 =
* Admin screen tidy up to make it easier to use and more in tune with Wordpress styling
= 0.731 =
* Fixed multiple services rota/schedule bug, edit facilities bug, mailchimp sync bug on fresh mailchimp account
= 0.730 =
* Individual Tracked attendance Added
= 0.727 =
* Added mobile number to directory search
= 0.726 =
* Tie rota/schedule tasks to particular services (default all)
= 0.725 =
* Remove error when no member_type_is is specified in address-list shortcode
= 0.724 =
* Small bug fixes
= 0.723 =
* New install activation errors remove
= 0.722 =
* Sermon podcast file not found error fix
= 0.721 =
* Add kids work age ranges
* Add wp-cron for emailing out service rotas with no extra message
= 0.710 =
* Add externally hosted files to sermons
= 0.702 =
* Horizontal Rota pdf
* Removed annoying pagination on rota list page
= 0.701 =
* Rota CSV bug fix
= 0.700 =
* Prayer Chain messaging added
= 0.613 =
* New rota job bug fix
* Sermon podcast warning bug fix
= 0.612 =
* Edit Rota by clicking elements
= 0.611 =
* Mailchimp sync - hope teams and ministries sync properly now.
* Improved attendance graphs
= 0.610 =
* Old style calendar edit bug fix
= 0.609 =
* Directory Small group Bug fix
* Small group PDF bug fix
= 0.608 =
* Year Planner bug fix
= 0.607 =
* Initial Install bug fix for calendar table
= 0.605 =
* Calendar List bug fix
= 0.604 =
* Fix date table for new installs
= 0.603 =
* Directory - display household ministries fix
* Added hope team to edit people
= 0.601 =
* Sermon mp3 file edit path bug fix
= 0.600 =
* Fixed font size on post it notes
* People can be in more then one small group
* ssl proof the plugin, by using better formed include paths and uris
* Fixed directory edit people bugs
= 0.5970 =
* Added icon and banner
= 0.5969 =
* Added empty index.php to email cache directory
= 0.5968 =
* Obfuscate backup filename
= 0.5965 =
* Simplified calendar events database
* Added image for calendar events which appears as thumbnail on widget and popups
* Added Hope Team - practical ministries

= 0.5962 =
* Attendance Graph Updated
* Added Hope team - practical helps
= 0.5961 =
* Address list pdf line space bug
* Hope team
* Fix pdf nonces
= 0.5957 =
* Latest Sermons widget Control
* Calender remove empty class warning
* Directory remove empty class warning
* Fix Calendar Postit style width
= 0.5955 =
* Bug fixes to directory
= 0.5952 =
* Made audio tag valid html5
* Fixed rota pdf issue
= 0.5951 =
* Fix repeated itunes link on sermon pages
= 0.5950 =
* Fix  permissions bug
= 0.5949 =
* fix small group map and addres list map bugs
= 0.5948 =
* Add small group attendance indicators to directory edit pages and pdf
= 0.5946 =
* Tidy up sermon podcast display page
= 0.5945 =
* Remove console.log() from maps js for old IE versions
= 0.5944 =
* Correct links in sermon widget and itunes links
= 0.5943 =
* Users can edit own entry
* Address list uses microdata and valid HTML5
= 0.5941 =
* US and Aus translations added
= 0.5940 =
* Rota display bugs fixed on screen and pdf
= 0.5937 =
* Fix mobile linking on single mobile households
= 0.5936 =
* Hyperlinking within address list pdf
= 0.5935 =
* Address list display and PDF tweaks
= 0.5934 =
* Display multiple surname for a household, still sorted alphabetically by first surname in directory
= 0.5933 =
* Closing div fix when not using google maps in address list
= 0.5931 =
* Put first name against mobile numbers where more than one in a household
* Made implementation of recaptcha not clash with other plugins
* Put all scripts in footer and removed version number to improve load speed
* Put CSS in one file
= 0.5930 =
* Bug fixes - nonces for xmls for maps
* Ministries pdf, showing who is doing what
* Widget for latest sermons
= 0.5920 =
* Made protective nonces only valid for given member types
* Fixed address-xml link error
= 0.5910 =
* Changed Rota PDF to allow more text and initials instead of full names
= 0.5901 =
* Added nonces to all download links, to protect privacy
= 0.5858 =
* Make post editing screen for people and household clearer
= 0.5857 =
* Get rid of activation error by re-encoding as UTF8 without BOM
= 0.5851 =
* Mailchimp sync with directory
= 0.5841 =
* Correct rota email pdf link
= 0.584 =
* Email out service rota fix
= 0.583 =
* Calendar Series Edit Bug Fix
= 0.582 =
* Fix permissions bug
= 0.581 =
* Admin page css improvements for WP3.8x
* Drop Down date CSS fix
= 0.580 =
* Directly update who is in a ministry on the ministry page
= 0.579 =
* Add attachment page link for directory photos
* Add play counter for sermon mp3 played with <audio> and download, but not flash
= 0.575 =
* Fix for associating wordpress logins with directory people
= 0.574 =
* Fix vcard address missing
* Fix search form in people meta box after edit people
* Change donate message
= 0.573 =
* Move household - allow create new household with same address
* Search from edit people pages fix
= 0.572 =
* Birthdays bug fix
* Fix previous date shown in wrong format for editing people
= 0.571 =
* Fix people csv download bug
* Fix Date Picker CSS
= 0.570 =
* Birthdays Shortcode and widget
= 0.568 =
* Add individual user permissions
* Fix date picker to force ISO to internationalise
* Improve wordpress user functions when editing people
= 0.567 =
* People CSV spreadsheet download added into admin people meta box
* Fix diacritics (language accents) misprinting in pdfs
* Admin meta boxes only show for those with permissions
= 0.566 =
* Small group order made sortable
= 0.565 =
* Fixed front end registration with reCaptcha protection
= 0.564 =
* Updated Internationalisation
= 0.563 =
* Fix Follow up email not sending all information
= 0.562 =
* Fix Address label bug
* Fix creating new small group in edit people form not saving the person as a small group leader
= 0.561 =
* Add Word/PDF file upload to sermon podcasting
= 0.560 =
* Added Google metadata to events in calendar widget (event details should show in search results)
* Tidied up how autocomplete people are shown
* Fixed Itunes Category
* Added Itunes File Subtitle
* Fixed address not showing in follow up activity emails
= 0.559 =
* Add Subtitle to Itunes podcasts
= 0.558 =
* Tidy up the rota - no extra commas
= 0.557 =
* Dutch prefix support
= 0.556 =
* minor bug fixes
= 0.555 =
* Small Group geocoding
* More international friendly address storage and use
= 0.554 =
* Address list still displays if member_type_id=# is used!
= 0.553 =
* Image bug fix on address list display
= 0.552 =
* Double shortcode bug fix
* Google small group max fix [church_admin_map member_type_id=#]
= 0.551 =
* Household Edit - old data displayed in form fix
= 0.550 =
* Option of Photos on address list shortcode
* List of all shortcodes on main admin page
= 0.542 =
* Activation headers error on new installs bug fix
= 0.541 =
* Comms Setting cron instructions bug fix
* community.bulksms.co.uk fix
* Major rota bug fixes for upgrades
= 0.53 =
* Bug Fix
= 0.52 =
* Remove redundant chmod on old email cache directory
= 0.50 =
* Bug fixes for fresh installs
* Sermon Podcasting
* better rota handling (autocomplete) and ability to email weekly service rotas to participants
* Move email cache to uploads/church-admin-cache directory and handle redirect
= 0.4.91 =
* Updates rota table to new format
* Address list pdf and shortcode can have comma separated member_type_ids
= 0.4.8 =
* Podcasting and autocomplete for rota
= 0.4.73 =
* Install Member type table bug fix
= 0.4.72 =
* Tweak CSV support for rotas
= 0.4.71 =
* Added CSV download support for rotas
= 0.4.7 =
* Added Internationalisation
= 0.4.632 =
* Fix calendar link bugs
= 0.4.60 =
* Creating wp user for people fixed
= 0.4.59 =
* Address List pdf bug fixed
= 0.4.57 =
* Bug Fixes
* Departments/Roles renamed to ministries for clarity
= 0.4.56 =
* Bug Fixes
* Admin home screen tidied up
= 0.4.3 =
* Security vulnerability fixed
= 0.4.2 =
* Google map of small groups members [church_admin_map member_type_id=#]
= 0.4.1 =
* Bug fixes for rewrite
= 0.4.0 =
* Major Rewrite, especially how the directory is handled and stored
= 0.33.4.5 =
* Rota Gremlins fixed
= 0.33.4.4 =
* Apologies, your rota would have been duplicated. This fix  stops it happening on further upgrades.
= 0.33.4.3 =
Clear out files
= 0.33.4.0 =
* PDFs created dynamically
= 0.33.3.3 =
* UTF8 DB conversion
= 0.33.3.2 =
* Calendar Year planner added choices to main directory list
= 0.33.3.1 =
* Fixed add calendar event bug where details same as previous event not being saved.
= 0.33.3.0 =
* Email jquery no conflict wrappers
= 0.33.2.9 =
* calendar list format bug fix
= 0.33.2.8 =
* Fixed another calendar bug - next and previous
= 0.33.2.7 =
* Fixed calendar display dropdown menu year sticking.
= 0.33.2.6 =
* Fixed calendar display bug and calendar caching on editing or delete.
= 0.33.2.5 =
* Added more years to year planner caching
= 0.33.2.4 =
* Fix salutation missing from 1st email address for each family when sent instantly
= 0.33.2.3 =
* Attendance Graph Shortcodes
= 0.33.2.2 =
* Email cache directory change
= 0.33.2.1 =
* Missing template file added
* Added ability to send immediately
= 0.33.1 =
* Non queued emails not being sent fixed
* Email template and view before sending
* Small group now shows current group on directory editing
= 0.32.9.6 =
* Minor CSS tweak on address-list display for non white backgrounds
= 0.32.9.5 =
* Error message if calendar event not saved!
= 0.32.9.4 =
* Fixed calendar admin drop down menu bug
= 0.32.9.3 =
* Added category & weeks to calendar-list shortcode - copy and paste from Category subpage of Calendar menu
= 0.32.9.2 =
* Jquery conflict mode fix
= 0.32.9.1 =
* Fixed cron email issue
= 0.32.9 =
* Agenda View fix
= 0.32.8 =
* Calendar times use Wordpress Format Settings
* Calendar list view times and dates use Wordpress Format Settings
* PDF's now available in A4, Letter and Legal sizes
* Label options available
= 0.32.7 =
* Calendar errors showing again in red
= 0.32.5.1 =
* Calendar CSS tweak for WP20:20 theme
= 0.32.5 =
* Adjustable width calendar table from settings page
= 0.32.4 =
* DB fixes where prefix not wp_
* Improved calendar tooltips
* Cronemail.php not auto generated now!
* Calendar Widget compatable with most themes now!
= 0.32.3 =
* Formatting fixes
= 0.32.2 =
* Admin pages now all valid XHTML, and external CSS
= 0.32.1 =
* A4 Calendar added
= 0.31.4 =
* Calendar deletes added
* Improved calendar table
* Fixed jquery conflict bug on admin page
* Rota shows this Sunday rather than next Sunday
= 0.31.3 =
* Widget displays multple events per day and sorted by start date and time
= 0.31.2 =
* Oops install directory on wordpress.org is church-admin not church_admin
= 0.31 =
* Calendar functionality added
= 0.21 =
* Minor visitor fixes
= 0.2 =
* Minor bug fixes for small groups
= 0.1 =
* Initial release


* 0.566 required
== Credits: ==
