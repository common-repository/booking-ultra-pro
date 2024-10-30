<form method="post" action="">
<input type="hidden" name="update_settings" />

<?php
wp_nonce_field( 'bup_setting_page' );

global $bookingultrapro, $bupcomplement;

 
?>


<div id="tabs-bupro-settings" class="bup-multi-tab-options">

<ul class="nav-tab-wrapper bup-nav-pro-features">
<li class="nav-tab bup-pro-li"><a href="#tabs-1" title="<?php esc_html_e('General','booking-ultra-pro'); ?>"><?php esc_html_e('General','booking-ultra-pro'); ?></a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-bup-business-hours" title="<?php esc_html_e('Business Hours','booking-ultra-pro'); ?>"><?php esc_html_e('Business Hours','booking-ultra-pro'); ?> </a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-bup-newsletter" title="<?php esc_html_e('Newsletter','booking-ultra-pro'); ?>"><?php esc_html_e('Newsletter','booking-ultra-pro'); ?> </a></li>


<li class="nav-tab bup-pro-li"><a href="#tabs-bup-googlecalendar" title="<?php esc_html_e('Google Calendar','booking-ultra-pro'); ?>"><?php esc_html_e('Google Calendar','booking-ultra-pro'); ?> </a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-bup-shopping" title="<?php esc_html_e('Shopping Cart','booking-ultra-pro'); ?>"><?php esc_html_e('Shopping Cart','booking-ultra-pro'); ?> </a></li>





</ul>


<div id="tabs-1">

<div class="bup-sect  bup-welcome-panel">
  <h3><?php esc_html_e('Premium  Settings','booking-ultra-pro'); ?></h3>
  
    <?php if(isset($bupcomplement))
{?>

  <p><?php esc_html_e('This section allows you to set your company name, phone number and many other useful things such as set time slot, date format.','booking-ultra-pro'); ?></p>
  
  <table class="form-table">
<?php

$active_feature = false;


if($active_feature){
$this->create_plugin_setting(
            'select',
            'gateway_payment_request_page',
              esc_html__('Payment Page for Appointments','booking-ultra-pro'),
            $this->get_all_sytem_pages(),
              esc_html__("Select the page that will be used to request payments from your clients. The client will be taken to this page so they can submit their payment, once tha payment is confirmed then the appointment will change it's status to 'Approved'. Make sure this page contains this shortcode: [bup_payment_form]",'booking-ultra-pro'),
              esc_html__('Select the page that will be used to request payments from your clients.','booking-ultra-pro')
    );

}

$this->create_plugin_setting(
	'select',
	'what_display_in_admin_calendar',
	  esc_html__('What To Display in BUP Admin Calendar?','booking-ultra-pro'),
	array(
		1 =>   esc_html__('Staff Name','booking-ultra-pro'), 		
		2 =>   esc_html__('Client Name','booking-ultra-pro')),
		
	  esc_html__('You can set what will be displayed in the BUP Dashboard Calendar. You can set either Staff Name or Client Name','booking-ultra-pro'),
    esc_html__('You can set what will be displayed in the BUP Dashboard Calendar. You can set either Staff Name or Client Name','booking-ultra-pro')
       );

$days_min = array(
						'0' =>   esc_html__('Disabled.','booking-ultra-pro'),
						'1' =>   esc_html__('1 hour.','booking-ultra-pro'),
						'2' =>   esc_html__('2 hours.','booking-ultra-pro'),
						'3' =>   esc_html__('3 hours.','booking-ultra-pro'),
						'4' =>   esc_html__('4 hours.','booking-ultra-pro'),
						'5' =>   esc_html__('5 hours.','booking-ultra-pro'),
						'6' =>   esc_html__('6 hours.','booking-ultra-pro'),		
		 				'7' =>   esc_html__('7 hours.','booking-ultra-pro'),
						'8' =>   esc_html__('8 hours.','booking-ultra-pro'),
						'9' =>   esc_html__('9 hours.','booking-ultra-pro'),
                        '10' =>  esc_html__('10 hours.','booking-ultra-pro'),
						'11' =>  esc_html__('11 hours.','booking-ultra-pro'),
						'12' =>  esc_html__('12 hours.','booking-ultra-pro'),
                        '24' =>   esc_html__('1 day','booking-ultra-pro'),
                        '48' =>   esc_html__('2 days.','booking-ultra-pro'),
                        '72' =>   esc_html__('3 days.','booking-ultra-pro'),
                        '96' =>  esc_html__('4 days.','booking-ultra-pro'),                       
                        '120' =>  esc_html__('5 days','booking-ultra-pro'),
						'144' =>  esc_html__('6 days','booking-ultra-pro'),
						'168' =>  esc_html__('1 week.','booking-ultra-pro'),
						'336' =>  esc_html__('2 weeks.','booking-ultra-pro'),
						'504' =>  esc_html__('3 weeks.','booking-ultra-pro'),
						'672' =>  esc_html__('4 Weeks.','booking-ultra-pro'),
                       
                    );
   
		
		$this->create_plugin_setting(
            'select',
            'bup_min_prior_booking',
              esc_html__('Minimum time requirement prior to booking:','booking-ultra-pro'),
            $days_min,
              esc_html__('Set how late appointments can be booked (for example, require customers to book at least 1 hour before the appointment time).','booking-ultra-pro'),
              esc_html__('Set how late appointments can be booked (for example, require customers to book at least 1 hour before the appointment time).','booking-ultra-pro')
    );
	
	
	$this->create_plugin_setting(
	'select',
	'allow_timezone',
	  esc_html__("Activate timezone detection?",'booking-ultra-pro'),
	array(
		0 =>   esc_html__('NO','booking-ultra-pro'), 		
		1 =>   esc_html__('YES','booking-ultra-pro')),
		
	  esc_html__("This will detect the client's timezone. Which is useful if you offer services on different locations with different hours.",'booking-ultra-pro'),
    esc_html__("This will detect the client's timezone. Which is useful if you offer services on different locations with different hours.",'booking-ultra-pro')
       );
	
       $this->create_plugin_setting(
        'input',
        'limit_daysforbooking',
          esc_html__('Limit Days for Booking :','booking-ultra-pro'),array(),
          esc_html__('Enter No. of Days you would like to limit for Booking.','booking-ultra-pro'),
          esc_html__('Enter No. of Days you would like to limit for Booking.','booking-ultra-pro')
);
	  
   
		
?>
</table>

<?php }else{?>

<p><?php esc_html_e('These settings are included in the premium version of Booking Ultra Pro. If you find the plugin useful for your business please consider buying a licence for the full version.','booking-ultra-pro'); ?>. Click <a href="https://bookingultrapro.com/compare-packages/">here</a> to upgrade </p>

<strong><?php esc_html_e( 'The following settings are included in Premium Version', 'booking-ultra-pro') ?></strong>
<p>- <?php esc_html_e( 'Google Calendar.', 'booking-ultra-pro') ?></p>
<p>- <?php esc_html_e( 'Minimum time requirement prior to booking.', 'booking-ultra-pro') ?> </p>
<p>- <?php esc_html_e( 'Display either Staff Name or Client name on Admin Calendar.', 'booking-ultra-pro') ?></p>
<p>- <?php esc_html_e( 'Limit Days Feature Availablity for Booking.', 'booking-ultra-pro') ?></p>



<?php }?> 

  
</div>


<div class="bup-sect  bup-welcome-panel">
  <h3><?php esc_html_e('Miscellaneous  Settings','booking-ultra-pro'); ?></h3>
  
  <p><?php esc_html_e('This section allows you to set your company name, phone number and many other useful things such as set time slot, date format.','booking-ultra-pro'); ?></p>
  
  
  <table class="form-table">
<?php 


$this->create_plugin_setting(
        'input',
        'company_name',
          esc_html__('Company Name:','booking-ultra-pro'),array(),
          esc_html__('Enter your company name here.','booking-ultra-pro'),
          esc_html__('Enter your company name here.','booking-ultra-pro')
);

$this->create_plugin_setting(
        'input',
        'company_phone',
          esc_html__('Company Phone Number:','booking-ultra-pro'),array(),
          esc_html__('Enter your company phone number here.','booking-ultra-pro'),
          esc_html__('Enter your company phone number here.','booking-ultra-pro')
);

$this->create_plugin_setting(
        'input',
        'company_address',
          esc_html__('Company Address:','booking-ultra-pro'),array(),
          esc_html__('Enter your company address here.','booking-ultra-pro'),
          esc_html__('Enter your company address here.','booking-ultra-pro')
);

$this->create_plugin_setting(
	'select',
	'registration_rules',
	  esc_html__('Booking Type','booking-ultra-pro'),
	array(
		4 =>   esc_html__('Paid Booking','booking-ultra-pro'), 		
		1 =>   esc_html__('Free Booking','booking-ultra-pro')),
		
	  esc_html__('Free Booking allows users to book and appointment for free, the payment methods will not be displayed. ','booking-ultra-pro'),
    esc_html__('Free Booking allows users to book and appointment for free, the payment methods will not be displayed.','booking-ultra-pro')
       );
	   
	   
	    $this->create_plugin_setting(
	'select',
	'wp_head_present',
	  esc_html__("Is wp_head in theme?",'booking-ultra-pro'),
	array(
		1 =>   esc_html__('YES','booking-ultra-pro'), 		
		0=>   esc_html__('NO','booking-ultra-pro')),
		
	  esc_html__("This setting is useful for themes that doesn't include the wp_head functions, which is not the ideal for the best practice to develop WP themes.",'booking-ultra-pro'),
    esc_html__("This setting is useful for themes that doesn't include the wp_head functions, which is not the ideal for the best practice to develop WP themes.",'booking-ultra-pro')
       );
	   
	    $this->create_plugin_setting(
	'select',
	'country_detection',
	  esc_html__("Country Detection Active?",'booking-ultra-pro'),
	array(
		1 =>   esc_html__('YES','booking-ultra-pro'), 		
		0=>   esc_html__('NO','booking-ultra-pro')),
		
	  esc_html__("This settings us a third-party library to auto-fill the phone number field on the front-end booking form.",'booking-ultra-pro'),
    esc_html__("This settings us a third-party library to auto-fill the phone number field on the front-end booking form.",'booking-ultra-pro')
       );
	   
	   
       $this->create_plugin_setting(
	'select',
	'gateway_free_success_active',
	  esc_html__('Custom Success Page Redirect ','booking-ultra-pro'),
	array(
		1 =>   esc_html__('YES','booking-ultra-pro'), 		
		0=>   esc_html__('NO','booking-ultra-pro')),
		
                   esc_html__('If checked, the users will be taken to this page. This option is used only when you have set Free Bookins as Regitration Type ','booking-ultra-pro'),
                  esc_html__('If checked, the users will be taken to this page ','booking-ultra-pro')
       );
	  
/*	   
$this->create_plugin_setting(
                'checkbox',
                'gateway_free_success_active',
                __('Custom Success Page Redirect ','booking-ultra-pro'),
                '1',
                __('If checked, the users will be taken to this page. This option is used only when you have set Free Bookins as Regitration Type ','booking-ultra-pro'),
                __('If checked, the users will be taken to this page ','booking-ultra-pro')
        ); 


*/

$this->create_plugin_setting(
            'select',
            'gateway_free_success',
              esc_html__('Success Page for Free Bookings','booking-ultra-pro'),
            $this->get_all_sytem_pages(),
              esc_html__("Select the sucess page. The user will be taken to this page right after the booking confirmation.",'booking-ultra-pro'),
              esc_html__('Select the sucess page. The user will be taken to this page right after the booking confirmation.','booking-ultra-pro')
    );
	
	
	$data_status = array(
		 				'0' => 'Pending',
                        '1' =>'Approved'
                       
                    );
$this->create_plugin_setting(
            'select',
            'gateway_free_default_status',
              esc_html__('Default Status for Free Appointments','booking-ultra-pro'),
            $data_status,
              esc_html__("Set the default status an appointment will have when NOT using a payment method. You won't have to approve the appointments manually, they will get approved automatically.",'booking-ultra-pro'),
              esc_html__('et the default status an appointment will have when NOT using a payment method.','booking-ultra-pro')
    );	


	
$this->create_plugin_setting(
        'textarea',
        'gateway_free_success_message',
          esc_html__('Custom Message for Free Bookings','booking-ultra-pro'),array(),
          esc_html__('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','booking-ultra-pro'),
          esc_html__('Input here a custom message that will be displayed to the client once the booking has been confirmed at the front page.','booking-ultra-pro')
);


	 $this->create_plugin_setting(
	'select',
	'appointment_cancellation_active',
	  esc_html__('Redirect Cancellation link? ','booking-ultra-pro'),
	array(
		1 =>   esc_html__('YES','booking-ultra-pro'), 		
		0=>   esc_html__('NO','booking-ultra-pro')),
		
                  esc_html__('If selected Yes, the clients will be able to cancel the appointment by using the cancellation link displayed in the appointment details email and they will be redirected to your custom page specified above. ','booking-ultra-pro'),
                  esc_html__('If selected Yes, the clients will be able to cancel the appointment by using the cancellation link displayed in the appointment details email. ','booking-ultra-pro')
       );
	  
/*$this->create_plugin_setting(
                'checkbox',
                'appointment_cancellation_active',
                  esc_html__('Redirect Cancellation link? ','booking-ultra-pro'),
                '1',
                  esc_html__('If checked, the clients will be able to cancel the appointment by using the cancellation link displayed in the appointment details email and they will be redirected to your custom page specified above. ','booking-ultra-pro'),
                  esc_html__('If checked, the clients will be able to cancel the appointment by using the cancellation link displayed in the appointment details email. ','booking-ultra-pro')
        );*/

$this->create_plugin_setting(
            'select',
            'appointment_cancellation_redir_page',
              esc_html__('Cancellation Page','booking-ultra-pro'),
            $this->get_all_sytem_pages(),
              __("Select the cancellation page. The appointment cancellation needs a page. Please create your cancellation page and set it here. IMPORTANT: Setting a page is very important, otherwise this feature will not work.",'booking-ultra-pro'),
              esc_html__('Select the cancellation page. The appointment cancellation needs a page. Please create your cancellation page and set it here.','booking-ultra-pro')
    );	
	
	
$this->create_plugin_setting(
            'select',
            'appointment_admin_approval_page',
              esc_html__('Appointment Approval Page','booking-ultra-pro'),
            $this->get_all_sytem_pages(),
              __("Select the approbation page for your appointments. Please create a page if you wish to let the admin to approve an appointment via email. <br><br><strong>IMPORTANT:</strong> Setting this page is very important, otherwise this feature will not work. <br><br><strong>IMPORTANT:</strong> Only the admin will receive the link to approve and appointment via email.",'booking-ultra-pro'),
              esc_html__('Select the Approbation page for your appointments','booking-ultra-pro')
    );	    


 $data = array(
		 				'm/d/Y' => date('m/d/Y'),                        
                        'Y/m/d' => date('Y/m/d'),
                        'd/m/Y' => date('d/m/Y'),                  
                       
                        'F j, Y' => date('F j, Y'),
                        'j M, y' => date('j M, y'),
                        'j F, y' => date('j F, y'),
                        'l, j F, Y' => date('l, j F, Y')
                    );
		$data_picker = array(
		 				'm/d/Y' => date('m/d/Y'),
						'd/m/Y' => date('d/m/Y')
                    );
					
		$data_admin = array(
		 				'm/d/Y' => date('m/d/Y'),
						'd/m/Y' => date('d/m/Y')
                    );
					
		 $data_time = array(
		 				'5' => 5,
                        '10' =>10,
                        '12' => 12,
                        '15' => 15,
                        '20' => 20,
                        '30' =>30,                       
                        '60' =>60,
						'90' =>90,
						'120' =>120,
                                                '240' =>240
                       
        );
		
		$data_time_format = array(
		 				
                        'H:i' => date('H:i'),
                        'h:i A' => date('h:i A')
                    );
		 $days_availability = array(
						'1' => 1,
						'2' => 2,
						'3' => 3,
						'4' => 4,
						'5' => 5,
						'6' => 6,		
		 				'7' => 7,
                        '10' =>10,
                        '15' => 15,
                        '20' => 20,
                        '25' => 25,
                        '30' =>30,                       
                        '35' =>35,
						'40' =>40,
                       
                    );
   
		
		$this->create_plugin_setting(
            'select',
            'bup_date_format',
              esc_html__('Date Format:','booking-ultra-pro'),
            $data,
              esc_html__('Select the date format to be used','booking-ultra-pro'),
              esc_html__('Select the date format to be used','booking-ultra-pro')
    );
	
	
	$this->create_plugin_setting(
            'select',
            'bup_date_picker_format',
              esc_html__('Date Picker Format:','booking-ultra-pro'),
            $data_picker,
              esc_html__('Select the date format to be used on the Date Picker','booking-ultra-pro'),
              esc_html__('Select the date format to be used on the Date Picker','booking-ultra-pro')
    );
	
	$this->create_plugin_setting(
            'select',
            'bup_date_admin_format',
              esc_html__('Admin Date Format:','booking-ultra-pro'),
            $data_admin,
              esc_html__('Select the date format to be used on the Date Picker','booking-ultra-pro'),
              esc_html__('Select the date format to be used on the Date Picker','booking-ultra-pro')
    );
	
	$this->create_plugin_setting(
            'select',
            'bup_time_format',
              esc_html__('Display Time Format:','booking-ultra-pro'),
            $data_time_format,
              esc_html__('Select the time format to be used','booking-ultra-pro'),
              esc_html__('Select the time format to be used','booking-ultra-pro')
    );
	
	
	
		$this->create_plugin_setting(
	'select',
	'allow_bookings_outsite_business_hours',
	  esc_html__('Allow booking outside business hours?','booking-ultra-pro'),
	array(
		'yes' =>   esc_html__('YES','booking-ultra-pro'), 		
		'no' =>   esc_html__('NO','booking-ultra-pro')),
		
	  esc_html__("Use this option if you don't wish to receive purchases on services that fall outside the business hours. The booking system calculates that the appointments have to end when the business hours stop. ",'booking-ultra-pro'),
    esc_html__("Use this option if you don't wish to receive purchases on services that fall outside the business hours. The booking system calculates that the appointments have to end when the business hours stop.  ",'booking-ultra-pro')
       );
	
	
	$this->create_plugin_setting(
	'select',
	'display_only_from_hour',
	  esc_html__('Display only from hour?','booking-ultra-pro'),
	array(
		'no' =>   esc_html__('NO','booking-ultra-pro'), 		
		'yes' =>   esc_html__('YES','booking-ultra-pro')),
		
	  esc_html__("Use this option if you don't wish to display the the whole time range, example 08:30 – 09:00 ",'booking-ultra-pro'),
    esc_html__("Use this option if you don't wish to display the the whole time range, example 08:30 – 09:00  ",'booking-ultra-pro')
       );
	   
	   
	   $this->create_plugin_setting(
	'select',
	'phone_number_mandatory',
	  esc_html__('Is Phone Number Mandatory?','booking-ultra-pro'),
	array(
		'yes' =>   esc_html__('YES','booking-ultra-pro'), 		
		'no' =>   esc_html__('NO','booking-ultra-pro')),
		
	  esc_html__("Use this option if you don't wish to require a phone number at the step 3 ",'booking-ultra-pro'),
    esc_html__("Use this option if you don't wish to require a phone number at the step 3  ",'booking-ultra-pro')
       );
	   
	    $this->create_plugin_setting(
	'select',
	'last_name_mandatory',
	  esc_html__('Ask for Last Name on Checkout?','booking-ultra-pro'),
	array(
		'yes' =>   esc_html__('YES','booking-ultra-pro'), 		
		'no' =>   esc_html__('NO','booking-ultra-pro')),
		
	  esc_html__("Use this option if you don't wish to require a the last name of your client at the step 3 ",'booking-ultra-pro'),
    esc_html__("Use this option if you don't wish to require a the last name of your client at the step 3 ",'booking-ultra-pro')
       );
	
	
	
	$this->create_plugin_setting(
            'select',
            'bup_calendar_days_to_display',
              esc_html__('Days to display on Step 2:','booking-ultra-pro'),
            $days_availability,
              esc_html__('Set how many days will be displayed on the step 2','booking-ultra-pro'),
              esc_html__('Set how many days will be displayed on the step 2','booking-ultra-pro')
    );
	
	
	
	
	$this->create_plugin_setting(
        'input',
        'currency_symbol',
          esc_html__('Currency Symbol','booking-ultra-pro'),array(),
          esc_html__('Input the currency symbol: Example: $','booking-ultra-pro'),
          esc_html__('Input the currency symbol: Example: $','booking-ultra-pro')
);

$this->create_plugin_setting(
	'select',
	'price_on_staff_list_front',
	  esc_html__('Display service price on staff list?','booking-ultra-pro'),
	array(
		'yes' =>   esc_html__('YES','booking-ultra-pro'), 		
		'no' =>   esc_html__('NO','booking-ultra-pro')),
		
	  esc_html__("Use this option if you don't wish to display the service's price on the staff drop/down list ",'booking-ultra-pro'),
    esc_html__("Use this option if you don't wish to display the service's price on the staff drop/down list ",'booking-ultra-pro')
       );
	   
	   $this->create_plugin_setting(
	'select',
	'display_unavailable_slots_on_front',
	  esc_html__('Display unavailable slots on booking form?','booking-ultra-pro'),
	array(
		'yes' =>   esc_html__('YES','booking-ultra-pro'), 		
		'no' =>   esc_html__('NO','booking-ultra-pro')),
		
	  esc_html__("Use this option if you don't wish to display the unavailable slots in the front-end booking form.",'booking-ultra-pro'),
    esc_html__("Use this option if you don't wish to display the unavailable slots in the front-end booking form. ",'booking-ultra-pro')
       );
	   
	   
	   $working_hours_time = array(
	                    '' => '',
		 				'5' => 5,
                        '10' =>10,
                        '12' => 12,
                        '15' => 15,
                        '20' => 20,
                        '30' =>30,                       
                        '60' =>60,
			'90' =>90,
			'120' =>120,
                        '240' =>240
                       
                    );
					
	
	 $this->create_plugin_setting(
            'select',
            'bup_calendar_working_hours_start',
              esc_html__('Staff Schedule Period:','booking-ultra-pro'),
            $working_hours_time,
              esc_html__('This gives you flexibility to set the start working hour for your staff members','booking-ultra-pro'),
              esc_html__('This gives you flexibility to set the start working hour for your staff members','booking-ultra-pro')
    );
	   
	 $this->create_plugin_setting(
            'select',
            'bup_calendar_time_slot_length',
              esc_html__('Calendar Slot Length:','booking-ultra-pro'),
            $data_time,
              esc_html__('Select the slot length to be used on the Calendar','booking-ultra-pro'),
              esc_html__('Select the slot length to be used on the Calendar','booking-ultra-pro')
    );
	
	
	$this->create_plugin_setting(
            'select',
            'bup_time_slot_length',
              esc_html__('Time slot length:','booking-ultra-pro'),
            $data_time,
              esc_html__('Select the time interval that will be used in frontend and backend, e.g. in calendar, second step of the booking process, while indicating the working hours, etc.','booking-ultra-pro'),
              esc_html__('Select the time interval that will be used in frontend and backend, e.g. in calendar, second step of the booking process, while indicating the working hours, etc.','booking-ultra-pro')
    );
	
	
	$this->create_plugin_setting(
	'select',
	'bup_override_avatar',
	  esc_html__('Use Booking Ultra Avatar','booking-ultra-pro'),
	array(
		'no' =>   esc_html__('No','booking-ultra-pro'), 
		'yes' =>   esc_html__('Yes','booking-ultra-pro'),
		),
		
	  esc_html__('If you select "yes", Booking Ultra will override the default WordPress Avatar','booking-ultra-pro'),
    esc_html__('If you select "yes", Booking Ultra will override the default WordPress Avatar','booking-ultra-pro')
       );
	
	
	   $this->create_plugin_setting(
	'select',
	'avatar_rotation_fixer',
	  esc_html__('Auto Rotation Fixer','booking-ultra-pro'),
	array(
		'no' =>   esc_html__('No','booking-ultra-pro'), 
		'yes' =>   esc_html__('Yes','booking-ultra-pro'),
		),
		
	  esc_html__("If you select 'yes', Booking Ultra will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'booking-ultra-pro'),
    esc_html__("If you select 'yes', Booking Ultra will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'booking-ultra-pro')
       );
	   $this->create_plugin_setting(
        'input',
        'media_avatar_width',
          esc_html__('Avatar Width:','booking-ultra-pro'),array(),
          esc_html__('Width in pixels','booking-ultra-pro'),
          esc_html__('Width in pixels','booking-ultra-pro')
);

$this->create_plugin_setting(
        'input',
        'media_avatar_height',
          esc_html__('Avatar Height','booking-ultra-pro'),array(),
          esc_html__('Height in pixels','booking-ultra-pro'),
          esc_html__('Height in pixels','booking-ultra-pro')
);
	
	
	
	 								
	
	  
		
?>
</table>


</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />
</p>




</div>



<div id="tabs-bup-googlecalendar">
  
<div class="bup-sect bup-welcome-panel ">
<h3><?php esc_html_e('Google Calendar Settings','booking-ultra-pro'); ?></h3>


  <?php if(isset($bupcomplement))
{?>

  
  <p><?php esc_html_e('This module gives you the capability to sync the plugin with Google Calendar. Each Staff member can have a different Google Calendar linked to their accounts.','booking-ultra-pro'); ?></p>
  
  
<table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'google_calendar_client_id',
          esc_html__('Client ID','booking-ultra-pro'),array(),
          esc_html__('Fill out this field with your Client ID obtained from the Developers Console','booking-ultra-pro'),
          esc_html__('Fill out this field with your Client ID obtained from the Developers Console','booking-ultra-pro')
);

$this->create_plugin_setting(
        'input',
        'google_calendar_client_secret',
          esc_html__('Client Secret','booking-ultra-pro'),array(),
          esc_html__('Fill out this field with your Client Secret obtained from the Developers Console.','booking-ultra-pro'),
          esc_html__('Fill out this field with your Client Secret obtained from the Developers Console.','booking-ultra-pro')
);


$this->create_plugin_setting(
	'select',
	'google_calendar_template',
	  esc_html__('What To Display in Google Calendar?','booking-ultra-pro'),
	array(
		'service_name' =>   esc_html__('Service Name','booking-ultra-pro'), 
		'staff_name' =>   esc_html__('Staff Name','booking-ultra-pro'),
		'client_name' =>   esc_html__('Client Name','booking-ultra-pro')
		),
		
	  esc_html__("Set what information should be placed in the title of Google Calendar event",'booking-ultra-pro'),
    esc_html__("Set what information should be placed in the title of Google Calendar event",'booking-ultra-pro')
       );
	   
	   
	   $this->create_plugin_setting(
	'select',
	'google_calendar_debug',
	  esc_html__('Debug Mode?','booking-ultra-pro'),
	array(
		'no' =>   esc_html__('NO','booking-ultra-pro'), 
		'yes' =>   esc_html__('YES','booking-ultra-pro')
		),
		
	  esc_html__("This option will display the detail of the error message if the Google Calendar Insert Method fails.",'booking-ultra-pro'),
    esc_html__("This option will display the detail of the error message if the Google Calendar Insert Method fails.",'booking-ultra-pro')
       );
	
?>
</table>


<p><strong><?php esc_html_e('Redirect URI','booking-ultra-pro'); ?></strong></p>
<p><?php esc_html_e('Enter this URL as a redirect URI in the Developers Console','booking-ultra-pro'); ?></p>

<p><strong><?php echo esc_url(get_admin_url());?>admin.php?page=bookingultra-users </strong></p>



<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />
</p>


<?php }else{?>

<p><?php esc_html_e('This function is disabled in the free version of Booking Ultra Pro. If you find the plugin useful for your business please consider buying a licence for the full version.','booking-ultra-pro'); ?>. Click <a href="https://bookingultrapro.com/compare-packages/">here</a> to upgrade </p>
<?php }?> 


</div>

</div>

<div id="tabs-bup-business-hours">
<div class="bup-sect  bup-welcome-panel">
  <h3><?php esc_html_e('Business Hours','booking-ultra-pro'); ?></h3>  
  <p><?php esc_html_e('.','booking-ultra-pro'); ?></p>
   <?php echo $bookingultrapro->service->get_business_hours_global_settings();?>
  
  <p class="submit">
	<input type="button" name="ubp-save-glogal-business-hours" id="ubp-save-glogal-business-hours" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />&nbsp; <span id="bup-loading-animation-business-hours">  <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; <?php esc_html_e('Please wait ...','booking-ultra-pro'); ?> </span>
</p>

    
  
  
</div>


</div>





<div id="tabs-bup-newsletter">
  
  
  
  <?php if(isset($bupcomplement))
{?>


<div class="bup-sect bup-welcome-panel ">
<h3><?php esc_html_e('Newsletter Settings','booking-ultra-pro'); ?></h3>
  
  <p><?php esc_html_e('Here you can activate your preferred newsletter tool.','booking-ultra-pro'); ?></p>

<table class="form-table">
<?php 
   
$this->create_plugin_setting(
	'select',
	'newsletter_active',
	  esc_html__('Activate Newsletter','booking-ultra-pro'),
	array(
		'no' =>   esc_html__('No','booking-ultra-pro'), 
		'aweber' =>   esc_html__('AWeber','booking-ultra-pro'),
		'mailchimp' =>   esc_html__('MailChimp','booking-ultra-pro'),
		'sendinblue' =>   esc_html__('Sendinblue','booking-ultra-pro'),
		),
		
	  esc_html__('Just set "NO" to deactivate the newsletter tool.','booking-ultra-pro'),
    esc_html__('Just set "NO" to deactivate the newsletter tool.','booking-ultra-pro')
       );

	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />
</p>


</div>


<div class="bup-sect bup-welcome-panel ">
<h3><?php esc_html_e('Aweber Settings','booking-ultra-pro'); ?></h3>
  
  <p><?php esc_html_e('This module gives you the capability to subscribe your clients automatically to any of your Aweber List when they complete the purchase.','booking-ultra-pro'); ?></p>
  
  
<table class="form-table">
<?php 
   
		

$this->create_plugin_setting(
        'input',
        'aweber_consumer_key',
          esc_html__('Consumer Key','booking-ultra-pro'),array(),
          esc_html__('Fill out this field with your aweber consumer key.','booking-ultra-pro'),
          esc_html__('Fill out this field with your aweber consumer key.','booking-ultra-pro')
);

$this->create_plugin_setting(
        'input',
        'aweber_consumer_secret',
          esc_html__('Consumer Secret','booking-ultra-pro'),array(),
          esc_html__('Fill out this field with your aweber consumer secret.','booking-ultra-pro'),
          esc_html__('Fill out this field with your aweber consumer secret.','booking-ultra-pro')
);




$this->create_plugin_setting(
                'checkbox',
                'aweber_auto_checked',
                  esc_html__('Auto Checked Aweber','booking-ultra-pro'),
                '1',
                  esc_html__('If checked, the user will not need to click on the awerber checkbox. It will appear checked already.','booking-ultra-pro'),
                  esc_html__('If checked, the user will not need to click on the aweber checkbox. It will appear checked already.','booking-ultra-pro')
        );
$this->create_plugin_setting(
        'input',
        'aweber_text',
          esc_html__('Aweber Text','booking-ultra-pro'),array(),
          esc_html__('Please input the text that will appear when asking users to get periodical updates.','booking-ultra-pro'),
          esc_html__('Please input the text that will appear when asking users to get periodical updates.','booking-ultra-pro')
);

	$this->create_plugin_setting(
        'input',
        'aweber_header_text',
          esc_html__('Aweber Header Text','booking-ultra-pro'),array(),
          esc_html__('Please input the text that will appear as header when aweber is active.','booking-ultra-pro'),
          esc_html__('Please input the text that will appear as header when aweber is active.','booking-ultra-pro')
);
	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />
</p>


</div>




<div class="bup-sect bup-welcome-panel ">
<h3><?php esc_html_e('MailChimp Settings','booking-ultra-pro'); ?></h3>
  
  <p><?php esc_html_e('.','booking-ultra-pro'); ?></p>
  
  
<table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'mailchimp_api',
          esc_html__('MailChimp API Key','booking-ultra-pro'),array(),
          esc_html__('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','booking-ultra-pro'),
          esc_html__('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','booking-ultra-pro')
);

$this->create_plugin_setting(
        'input',
        'mailchimp_list_id',
          esc_html__('MailChimp List ID','booking-ultra-pro'),array(),
          esc_html__('Fill out this field your list ID.','booking-ultra-pro'),
          esc_html__('Fill out this field your list ID.','booking-ultra-pro')
);



$this->create_plugin_setting(
                'checkbox',
                'mailchimp_auto_checked',
                  esc_html__('Auto Checked MailChimp','booking-ultra-pro'),
                '1',
                  esc_html__('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','booking-ultra-pro'),
                  esc_html__('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','booking-ultra-pro')
        );
$this->create_plugin_setting(
        'input',
        'mailchimp_text',
          esc_html__('MailChimp Text','booking-ultra-pro'),array(),
          esc_html__('Please input the text that will appear when asking users to get periodical updates.','booking-ultra-pro'),
          esc_html__('Please input the text that will appear when asking users to get periodical updates.','booking-ultra-pro')
);

	$this->create_plugin_setting(
        'input',
        'mailchimp_header_text',
          esc_html__('MailChimp Header Text','booking-ultra-pro'),array(),
          esc_html__('Please input the text that will appear as header when mailchip is active.','booking-ultra-pro'),
          esc_html__('Please input the text that will appear as header when mailchip is active.','booking-ultra-pro')
);
	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />
</p>


</div>
<!-- Sendinblue Settings-->
<div class="bup-sect bup-welcome-panel ">
<h3><?php esc_html_e('Sendinblue Settings','booking-ultra-pro'); ?></h3>
  
  <p><?php esc_html_e('.','booking-ultra-pro'); ?></p>
  
  
<table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'sendinblue_api',
          esc_html__('Sendinblue API Key','booking-ultra-pro'),array(),
          esc_html__('Fill out this field with your Sendinblue API key here to allow integration with Sendinblue subscription.','booking-ultra-pro'),
          esc_html__('Fill out this field with your Sendinblue API key here to allow integration with Sendinblue subscription.','booking-ultra-pro')
);

$this->create_plugin_setting(
        'input',
        'sendinblue_list_id',
          esc_html__('Sendinblue List ID','booking-ultra-pro'),array(),
          esc_html__('Fill out this field your list ID.( If you want to add email in multiple list then separate list with comma(,), e,g 3,2 )','booking-ultra-pro'),
          esc_html__('Fill out this field your list ID.','booking-ultra-pro')
);



$this->create_plugin_setting(
                'checkbox',
                'sendinblue_auto_checked',
                  esc_html__('Auto Checked Sendinblue','booking-ultra-pro'),
                '1',
                  esc_html__('If checked, the user will not need to click on the sendinblue checkbox. It will appear checked already.','booking-ultra-pro'),
                  esc_html__('If checked, the user will not need to click on the sendinblue checkbox. It will appear checked already.','booking-ultra-pro')
        );
$this->create_plugin_setting(
        'input',
        'sendinblue_text',
          esc_html__('Sendinblue Text','booking-ultra-pro'),array(),
          esc_html__('Please input the text that will appear when asking users to get periodical updates.','booking-ultra-pro'),
          esc_html__('Please input the text that will appear when asking users to get periodical updates.','booking-ultra-pro')
);

	$this->create_plugin_setting(
        'input',
        'sendinblue_header_text',
          esc_html__('Sendinblue Header Text','booking-ultra-pro'),array(),
          esc_html__('Please input the text that will appear as header when sendinblue is active.','booking-ultra-pro'),
          esc_html__('Please input the text that will appear as header when sendinblue is active.','booking-ultra-pro')
);
	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />
</p>


</div>


<?php }else{?>

<p><?php esc_html_e('This function is disabled in the free version of Booking Ultra Pro. If you find the plugin useful for your business please consider buying a licence for the full version.','booking-ultra-pro'); ?>. Click <a href="https://bookingultrapro.com/compare-packages/">here</a> to upgrade </p>
<?php }?>  

</div>



</div>


<div id="tabs-bup-shopping">
  
<div class="bup-sect bup-welcome-panel ">
<h3><?php esc_html_e('Shopping Cart Settings','booking-ultra-pro'); ?></h3>


  <?php if(isset($bupcomplement))
{?>

  
  <p><?php esc_html_e('This module gives you the capability to allow users to purchase multiple services at once. There are some settings you can tweak on this section','booking-ultra-pro'); ?></p>
  
  
<table class="form-table">
<?php 
   
$this->create_plugin_setting(
        'input',
        'shopping_cart_description',
          esc_html__('Purchase Description','booking-ultra-pro'),array(),
          esc_html__('Here you can set a custom description that will be displayed when the client purchases multiple items by using the shopping cart features.','booking-ultra-pro'),
          esc_html__('Here you can set a custom description that will be displayed when the client purchases multiple items by using the shopping cart features.','booking-ultra-pro')
);


	
?>
</table>



<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','booking-ultra-pro'); ?>"  />
</p>


<?php }else{?>

<p><?php
// Translators: This string is used to notify users that the function is not available in the free version and encourages them to buy a license for the full version of the plugin.
echo esc_html__('This function is disabled in the free version of Booking Ultra Pro. If you find the plugin useful for your business please consider buying a license for the full version.', 'booking-ultra-pro');

// Translators: %1$s is the opening HTML anchor tag for a link, %2$s is the closing HTML anchor tag. This string is used to display a clickable link to upgrade.
$upgrade_text_template =   esc_html__('%1$sClick Here%2$s to upgrade', 'booking-ultra-pro');

// Example dynamic content for the link
$upgrade_link_start = '<a href="https://bookingultrapro.com/compare-packages/" target="_blank">'; // Opening HTML anchor tag
$upgrade_link_end = '</a>'; // Closing HTML anchor tag

// Format the translatable string with the dynamic content
$upgrade_text = sprintf($upgrade_text_template, $upgrade_link_start, $upgrade_link_end);

// Output the formatted text
echo $upgrade_text;
?>
 </p>
<?php }?> 


</div>

</div>


</form>