<?php
class BookingUltraAppointment
{
	
	function __construct() 
	{
				
	
		add_action( 'wp_ajax_get_all_staff_appointments', array( &$this, 'get_all_staff_appointments' ));
		add_action( 'init', array($this, 'bup_handle_post') );		
		add_action( 'wp_ajax_bup_admin_new_appointment', array( &$this, 'bup_admin_new_appointment' ));
		add_action( 'wp_ajax_bup_admin_new_appointment_confirm', array( &$this, 'create_new_appointment' ));
		add_action( 'wp_ajax_appointment_get_selected_time', array( &$this, 'appointment_get_selected_time' ));
		add_action( 'wp_ajax_bup_admin_edit_appointment', array( &$this, 'edit_appointment' ));
		add_action( 'wp_ajax_bup_get_payments_list', array( &$this, 'appointment_get_payments_list' ));
		add_action( 'wp_ajax_bup_get_payment_form', array( &$this, 'bup_get_payment_form' ));
		add_action( 'wp_ajax_bup_admin_payment_confirm', array( &$this, 'bup_admin_payment_confirm' ));
		add_action( 'wp_ajax_bup_appointment_confirm_reschedule', array( &$this, 're_schedule_confirm' ));
		add_action( 'wp_ajax_bup_update_booking_info', array( &$this, 'bup_update_booking_info' ));
		add_action( 'wp_ajax_bup_delete_payment', array( &$this, 'bup_delete_payment' ));
		add_action( 'wp_ajax_bup_get_appointments_quick', array( &$this, 'get_appointments_quick' ));
		add_action( 'wp_ajax_bup_update_appointment_status', array( &$this, 'update_appointment_status_inline' ));
		add_action( 'wp_ajax_bup_update_payment_status_inline', array( &$this, 'update_payment_status_inline' ));
		
		add_action( 'wp_ajax_bup_appointment_status_options', array( &$this, 'get_appointment_status_options' ));
		add_action( 'wp_ajax_bup_update_appo_status_ed', array( &$this, 'update_appointment_status_ed' ));
		add_action( 'wp_ajax_bup_delete_appointment', array( &$this, 'delete_appointment_ajax' ));
		
		add_action( 'wp_head', array(&$this, 'bup_add_template_css_style'),114,1);	
		
		
		
		
	
	}
	
	public function bup_add_template_css_style ($arg) 
	{
		global $bookingultrapro;
		$html = "";
		$custom_css = '';
		$template_id = NULL;
		if(!empty($arg))
		$template_id=$arg[0];
		$bg_color = 'background-color:'.$bookingultrapro->get_template_label("bup_cus_bg_color",$template_id).' !important';
		
		$custom_css .= '.bup-front-cont .bup-book-steps-cont .bup-cart-step-active{'.$bg_color.'}  ';
		$custom_css .= 'ul.bup-time-slots-available-list .bup-button{'.$bg_color.'}  ';
		$custom_css .= '.bup-button-submit{'.$bg_color.'}  ';	
		
		
		if($custom_css!="" && $template_id!='' )
		{
			$html .= ' <style type="text/css">';
			$html .= $custom_css;
			$html .= ' </style>';			
		}
		
		echo $html;		
		
	}

	
	
	function bup_handle_post () 
	{		
		
		/*Form is fired*/	    
		if (isset($_GET['bupcancelappointment'])) {
			
			/* cancel appointment */
			$this->cancel_appointment_by_staff_client();
				
		}
		
		/*Form is fired*/	    
		if (isset($_GET['bupapprovalappointment'])) {
			
			/* cancel appointment */
			$this->approval_appointment_by_admin();
				
		}
		
		
		
	}
	
	
	
	
	
	public function is_my_appointment($booking_id, $staff_id)
	{
		global  $bookingultrapro , $wpdb;
		$booking_id =esc_sql($booking_id);
		$staff_id = esc_sql($staff_id);
		$value = esc_sql($value);
		
		// Prepare the SQL query
		$sql = $wpdb->prepare(
			"
			SELECT appo.*, usu.*
			FROM {$wpdb->prefix}bup_bookings AS appo
			RIGHT JOIN {$wpdb->users} AS usu ON (usu.ID = appo.booking_staff_id)
			WHERE appo.booking_id = %d AND appo.booking_staff_id = %d
			",
			$booking_id,
			$staff_id
		);
		$appointments = $wpdb->get_results($sql );	
		
		if ( !empty( $appointments ) )
		{
			
			foreach ( $appointments as  $appointment ) 
			{
				return true;
			
			}
		
		}else{
			
			return false;			
			
		}
		
	}
	
	function approval_appointment_by_admin()
	{
		
		global $wpdb, $bookingultrapro;	
		$appointment_key= esc_attr($_GET['bupapprovalappointment']);
		$appointment_id= esc_attr($_GET['bupid']);
		
		//change appointment status		
		$appointment = $this->get_appointment_with_key_approval($appointment_key);
		
		if($appointment->booking_id=='' || $appointment_id!= $appointment->booking_id)		
		{
			echo "error!";
			die();
			
		}
		
		//print_r($appointment);
		//die(0);
		
		$staff_id = $appointment->booking_staff_id;	
		$client_id = $appointment->booking_user_id;	
		$service_id = $appointment->booking_service_id;
		
		/*Update Appointment*/						
        $bookingultrapro->appointment->update_appointment_status($appointment->booking_id, 1, 0);
		
		/*Get Service*/			
		$service = $bookingultrapro->service->get_one_service($service_id);		
		$new_status = $this->get_status_legend(1);							
											 
		//get user				
		$staff_member = get_user_by( 'id', $staff_id );
		$client = get_user_by( 'id', $client_id );					
											
		$bookingultrapro->messaging->send_appointment_status_changed($staff_member, $client, $service, $appointment, $new_status);	
					
		
		//send notifications						
		$this->handle_redir_for_approval($appointment_key);		
	
	}
	
	//the cancellation needs a redirection page
	public function handle_redir_for_approval($key)
	{
		global $bookingultrapro, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$url = '';
		$my_success_url = '';		
					
		$sucess_page_id = $bookingultrapro->get_option('appointment_admin_approval_page');
		$my_success_url = get_permalink($sucess_page_id);		
		
		
		if($my_success_url=="")
		{
			$url = $_SERVER['REQUEST_URI'].'?order_status=cancelled&bup_order_key='.$key;
				
		}else{
					
			$url = $my_success_url;				
				
		}
		
		 		  
		wp_redirect( $url );
		exit;
		  
		 
	}	
	
	function cancel_appointment_by_staff_client()
	{
		
		global $wpdb, $bookingultrapro;	
		$appointment_key= esc_attr($_GET['bupcancelappointment']);
		
		if(isset($_GET['bupid'])){
			$appointment_id= esc_attr($_GET['bupid']);
		}		
		
		//change appointment status		
		$appointment = $this->get_appointment_with_key_cancellation($appointment_key);
		if($appointment->booking_id=='')		
		{
			echo "error!";
			die();
			
		}
		
		$staff_id = $appointment->booking_staff_id;	
		$client_id = $appointment->booking_user_id;	
		$service_id = $appointment->booking_service_id;
		
		/*Update Appointment*/						
		$bookingultrapro->appointment->update_appointment_status($appointment->booking_id,2,0);		
		
		/*Get Service*/			
		$service = $bookingultrapro->service->get_one_service($service_id);		
		$new_status = $this->get_status_legend(2);						
											 
		//get user				
		$staff_member = get_user_by( 'id', $staff_id );
		$client = get_user_by( 'id', $client_id );					
											
		$bookingultrapro->messaging->send_appointment_status_changed($staff_member, $client, $service, $appointment, $new_status);	
					
		
		//send notifications						
		$this->handle_redir_for_cancelation($appointment_key);		
	
	}
	
	
	//the cancellation needs a redirection page
	public function handle_redir_for_cancelation($key)
	{
		global $bookingultrapro, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$url = '';
		$my_success_url = '';		
		
		if($bookingultrapro->get_option('appointment_cancellation_active')=='1')		
		{			
			$sucess_page_id = $bookingultrapro->get_option('appointment_cancellation_redir_page');
			$my_success_url = get_permalink($sucess_page_id);		
		}
		
		if($my_success_url=="")
		{
			$url = $_SERVER['REQUEST_URI'].'?order_status=cancelled&bup_order_key='.$key;
				
		}else{
					
			$url = $my_success_url;				
				
		}
		
		 		  
		wp_redirect( $url );
		exit;
		  
		 
	}	
	
	function bup_admin_new_appointment () 
	{	
		// Check for nonce security      
		check_ajax_referer('ajax-new_appointment' );
		//turn on output buffering to capture script output
        ob_start();		
		include(BOOKINGUP_PATH."admin/templates/new_appointment.php");
        $content = ob_get_clean();		
		echo $content ;			
		die();
	}
	
	function edit_appointment () 
	{
		check_ajax_referer('ajax-new_appointment' );
		$appointment_id = esc_attr($_POST['appointment_id']);
		//turn on output buffering to capture script output
        ob_start();		
		include(BOOKINGUP_PATH."admin/templates/edit_appointment.php");
        $content = ob_get_clean();		
		echo $content ;			
		die();
	}
	
	function delete_appointment_ajax () 
	{
		check_ajax_referer("ajax-bup_calendar");
		global $wpdb, $bookingultrapro, $bupcomplement;	
		$appointment_id = esc_attr($_POST['appointment_id']);
		
		
		//delete appointment on Google Calendar		
		if(isset($bupcomplement))
		{
			$event_id = $this->get_booking_meta($appointment_id, 'google_event_id');
			$calendar_id = $this->get_booking_meta($appointment_id, 'google_calendar_id');
			$appointment = 		$this->get_one($appointment_id);
			$staff_id = $appointment->booking_staff_id;
			
			//get appointment meta gcal event id and google calendar id			
			$bupcomplement->googlecalendar->delete_event($event_id, $calendar_id, $staff_id);
		
		}
		
		//delete meta data
		$sql = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bup_bookings_meta  WHERE meta_booking_id=%d  ;',array($appointment_id));		
		$wpdb->query($sql);
		
		//delete payments
		$sql = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bup_orders  WHERE order_booking_id=%d  ;',array($appointment_id));		
		$wpdb->query($sql);
		
		//delete notes
		$sql = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bup_appointment_notes  WHERE note_appointment_id=%d  ;',array($appointment_id));		
		$wpdb->query($sql);
		
		//delete appointment
		$sql = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bup_bookings  WHERE booking_id=%d  ;',array($appointment_id));		
		$wpdb->query($sql);
		
		
		
				
		die();
	}
	
	
	
	function get_appointment_edition_form_fields ($booking_id) 
	{
		global $wpdb, $bookingultrapro;
		$custom_form = $this->get_booking_meta($booking_id, 'custom_form');		
		$form_id = '';
		$display ='';
		if(isset($_POST["form_id"])){$form_id = esc_attr($_POST["form_id"]);}
		/* Get end of array */			
		if($custom_form!="" || $form_id !="" )
		{
			//do we have a pre-set value in the get?			
			if($form_id !="")
			{
				$custom_form =$form_id;			
			}
			$custom_form = 'bup_profile_fields_'.$custom_form;		
			$array = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$array = get_option('bup_profile_fields');
			$fields_set_to_update ='bup_profile_fields';
		
		}
	

		foreach($array as $key=>$field) 
		{		     
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}

		$i_array_end = end($array);
	
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];

			if (isset($array[$array_end]['type']) && $array[$array_end]['type'] == 'seperator') 
			{
				if(isset($array[$array_end]))
				{
					unset($array[$array_end]);
				}
			}
		}

		/*Display custom profile fields added by the user*/		
		foreach($array as $key => $field) 
		{

			extract($field);
			
			// WP 3.6 Fix
			if(!isset($deleted))
			    $deleted = 0;
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($required))
			    $required = 0;
			
			$required_class = '';
			$required_text = '';
			
			if($required == 1 )
			{				
			    $required_class = 'validate[required] ';
				$required_text = '(*)';				
			}

			
			
			/* This is a Fieldset seperator */
						
			/* separator */
            if ($type == 'separator' && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
                   $display .= '<div class="bup-profile-separator">'.$name.'</div>';
				   
            }
					
			//check if display emtpy				
				
			if ($type == 'usermeta' &&  isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
				$display .= '<div class="bup-profile-field">';
				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				 {
					$display .= '<label class="bup-field-type" for="'.$meta.'">';	
					
					if (isset($array[$key]['icon']) && $icon) 
					{
						
                            $display .= '<i class="fa fa-' . $icon . '"></i>';
							
                    } else {
						
                            $display .= '<i class="fa fa-icon-none"></i>';
                    }
					
					
											
					$tooltipip_class = '';					
					if (isset($array[$key]['tooltip']) && $tooltip)
					{
						$qtip_classes = 'qtip-light ';	
						$qtip_style = '';					
					
						 //$tooltipip_class = '<a class="'.$qtip_classes.' uultra-tooltip" title="' . $tooltip . '" '.$qtip_style.'><i class="fa fa-info-circle reg_tooltip"></i></a>';
					} 
					
											
					$display .= '<span>'.$name. ' '.$required_text.' '.$tooltipip_class.'</span></label>';
					
					
				} else {
					
					$display .= '<label class="">&nbsp;</label>';
				}
				
				$display .= '<div class="bup-field-value">';
					
					switch($field) {
					
						case 'textarea':
							$display .= '<textarea class="'.$required_class.' bup-custom-field bup-input bup-input-text-area" rows="10" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','booking-ultra-pro').'">'.$bookingultrapro->appointment->get_booking_meta($booking_id, $meta).'</textarea>';
							break;
							
						case 'text':
							$display .= '<input type="text" class="'.$required_class.' bup-custom-field bup-input"  name="'.$meta.'" id="'.$meta.'" value="'.$bookingultrapro->appointment->get_booking_meta($booking_id, $meta).'"  title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','booking-ultra-pro').'"/>';
							break;							
							
						case 'datetime':						
						    $display .= '<input type="text" class="'.$required_class.' bup-custom-field bup-input bup-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$bookingultrapro->appointment->get_booking_meta($booking_id, $meta).'"  title="'.$name.'" />';
						    break;
							
						case 'select':
												
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' )
							
							{
								$loop = $bookingultrapro->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
								
							}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
															
								$loop = $bookingultrapro->uultra_one_line_checkbox_on_window_fix($choices);
								 	
								
							}
							
							if (isset($loop)) 
							{
								$display .= '<select class="'.$required_class.' bup-custom-field bup-input" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','booking-ultra-pro').'">';
								
								foreach($loop as $option)
								{
									
									$option = trim(stripslashes($option));							
								    
									$display .= '<option value="'.$option.'" '.selected( $bookingultrapro->appointment->get_booking_meta($booking_id, $meta), $option, 0 ).'>'.$option.'</option>';
									
								}
								$display .= '</select>';
							}
							
							break;
							
						case 'radio':						
						
							if($required == 1 && in_array($field, $this->include_for_validation))
							{
								$required_class = "validate[required] radio ";
							}
						
							if (isset($array[$key]['choices']))
							{				
													
								
								 $loop = $bookingultrapro->uultra_one_line_checkbox_on_window_fix($choices);
								
							}
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim(stripslashes($option));
									$display .= '<input type="radio" class="'.$required_class.' bup-custom-field" title="'.$name.'" name="'.$meta.'" id="uultra_multi_radio_'.$meta.'_'.$counter.'" value="'.$option.'" '.checked( $bookingultrapro->appointment->get_booking_meta($booking_id, $meta), $option, 0 );
									$display .= '/> <label for="uultra_multi_radio_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label>';
									
									$counter++;
									
								}
							}
							
							break;
							
						case 'checkbox':
						
						
							if($required == 1 && in_array($field, $this->include_for_validation))
							{
								$required_class = "validate[required] checkbox ";
							}						
						
							if (isset($array[$key]['choices'])) 
							{
																
								 $loop = $bookingultrapro->uultra_one_line_checkbox_on_window_fix($choices);
								
								
							}
							
							$saved_choices = $bookingultrapro->appointment->get_booking_meta($booking_id, $meta);
							$saved_choices = explode(',',$saved_choices);
							$saved_choices=array_map('trim',$saved_choices);
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim(stripslashes($option));
								  
								  $display .= '<div class="bup-checkbox bup-custom-field"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="bup_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" ';
									if (in_array($option, $saved_choices ))
									{
										$display .= 'checked="checked"';
									}
									$display .= '/> <label for="bup_multi_box_'.$meta.'_'.$counter.'"> '.$option.'</label> </div>';
									
									
									$counter++;
								}
							}
							
							break;	
						
													
						
							
					}
					
					
					if (isset($array[$key]['help_text']) && $help_text != '') 
					{
						$display .= '<div class="bup-help">'.$help_text.'</div>';
					}
							
					
									
									
					
				$display .= '</div>';
				$display .= '</div>';

			}
		} //end for each
		echo $display;

		die();
	}
	
	public function ini_module()
	{
		global $wpdb;	
		
		
	}
	
	public function update_booking_meta($booking_id, $key, $value)
	{
		
		global $wpdb, $bookingultrapro;
		$booking_id =esc_sql($booking_id);
		$key = esc_sql($key);
		$value = esc_sql($value);
		$sql = $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}bup_bookings_meta
			WHERE meta_booking_id = %d AND meta_booking_name = %s",
			$booking_id,
			$key
		);
		
		$rows = $wpdb->get_results($sql);	
		
		
		if ( !empty( $rows ))
		{
			// Prepare the SQL query using $wpdb->prepare
			$query = $wpdb->prepare(
				"UPDATE {$wpdb->prefix}bup_bookings_meta
				SET meta_booking_value = %s
				WHERE meta_booking_name = %s AND meta_booking_id = %d",
				$value,
				$key,
				$booking_id
			);

			// Execute the prepared query
			$wpdb->query($query);	
		
		}else{
			
			// Prepare the SQL query using $wpdb->prepare
			$query = $wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}bup_bookings_meta (meta_booking_value, meta_booking_name, meta_booking_id)
				VALUES (%s, %s, %d)",
				$value,
				$key,
				$booking_id
			);

			// Execute the prepared query
			$wpdb->query($query);
		
		}
		
	
	}
	
	public function bup_delete_payment()
	{
		check_ajax_referer('ajax-new_appointment' );
		if ( !current_user_can( 'manage_options' ) ) 
		die();
		global $wpdb, $bookingultrapro;
		
		$payment_id = esc_sql($_POST['payment_id']);	
		$appointment_id = esc_sql($_POST['appointment_id']);			
		
		// Prepare the SQL query using $wpdb->prepare
		$query = $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}bup_orders
			WHERE order_booking_id = %d AND order_id = %d",
			$appointment_id,
			$payment_id
		);

		// Execute the prepared query
		$wpdb->query($query);	
		
		die();
	
	}
	
	
	
	public function get_booking_meta($booking_id, $key)
	{
		
		global $wpdb, $bookingultrapro;
		$booking_id =esc_attr($booking_id);
		$key = esc_sql($key);
		
		
		$html='';
		
		$sql = $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}bup_bookings_meta
			WHERE meta_booking_id = %d AND meta_booking_name = %s",
			$booking_id,
			$key
		);		
		$rows = $wpdb->get_results($sql);
		
		
	//	echo $sql ;
		
		//print_r($rows);
			
		if ( !empty( $rows ))
		{
			foreach ( $rows as $row )
			{				
				$html =stripslashes($row->meta_booking_value);
			
			}	
		
		}
		
		return $html;		
	
	}
	
	public function bup_update_booking_info()
	{
		
		check_ajax_referer('ajax-new_appointment' );
		if ( !current_user_can( 'manage_options' ) ) 
		die();
		global $wpdb, $bookingultrapro;	
		
		$html='';	
			
		$bup_custom_fields = $_POST['custom_fields'];
		$booking_id = esc_attr($_POST['booking_id']);	
		
		$exploded = array();
		parse_str($bup_custom_fields, $exploded);
		
		//print_r($exploded);
		
		foreach($exploded as $field => $value)
		{
			if (is_array($value))   // checkboxes
			{
				$value = implode(',', $value);
			}	
						
			$this->update_booking_meta($booking_id, $field, $value);
		
		}	
		
		
		echo $html;
		die();
		
				
	
	}
	
	public function bup_admin_payment_confirm()
	{
		check_ajax_referer('ajax-new_appointment' );
		if ( !current_user_can( 'manage_options' ) ) 
		die();
		global $wpdb, $bookingultrapro;	
		
		$html='';	
		
		$bup_payment_amount = esc_sql($_POST['bup_payment_amount']);
		$bup_payment_transaction = esc_sql($_POST['bup_payment_transaction']);		
		$bup_payment_date = esc_sql($_POST['bup_payment_date']);
		$bup_booking_id = esc_sql($_POST['bup_booking_id']);	
		$bup_payment_id = esc_sql($_POST['bup_payment_id']);
		$bup_payment_status = esc_sql($_POST['bup_payment_status']);	
		
		if($bup_booking_id!='' && $bup_payment_amount!='' && $bup_payment_date!='' && $bup_payment_id=='')		
		{
					
			$query = $wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}bup_orders (order_booking_id, order_txt_id, order_method_name, order_status, order_amount, order_date)
				VALUES (%d, %s, 'local', %s, %s, %s)",
				$bup_booking_id,
				$bup_payment_transaction,
				$bup_payment_status,
				$bup_payment_amount,
				date('Y-m-d', strtotime($bup_payment_date))
			);
			
			$wpdb->query( $query );
			$html ='OK';
		
	    }else{
			
			$query = $wpdb->prepare(
				"UPDATE {$wpdb->prefix}bup_orders
				SET order_txt_id = %s, order_status = %s, order_amount = %s, order_date = %s
				WHERE order_booking_id = %d AND order_ID = %d",
				$bup_payment_transaction,
				$bup_payment_status,
				$bup_payment_amount,
				date('Y-m-d', strtotime($bup_payment_date)),
				$bup_booking_id,
				$bup_payment_id
			);			
			$wpdb->query( $query );
			$html ='OK';
			
			
		}
		
		echo $html;
		die();
		
				
	
	}
	
	
	public function bup_get_payment_form () 
	{
		check_ajax_referer('ajax-new_appointment' );
		global $wpdb, $bookingultrapro;
		
		$html='';	
		
		$order_amount='';
		$order_txt_id='';
		
		$payment_id = '';		
		if(isset($_POST['payment_id'])){$payment_id = esc_attr($_POST['payment_id']);}
		
		$appointment_id = '';		
		if(isset($_POST['appointment_id'])){$appointment_id = esc_attr($_POST['appointment_id']);}
		
	
		$order_date =	date('m/d/Y');
		
		$status_pending ='';
		$status_confirmed ='';
		
		if($payment_id!='' && $appointment_id!='')		
		{
			//get payments			
			$order = $bookingultrapro->order->get_order_edit( $payment_id , $appointment_id);
			$order_date =	date('m/d/Y', strtotime($order->order_date));
			
			$order_amount =	$order->order_amount;
			$order_txt_id =	$order->order_txt_id;			
			
		}			
			
		$html .= '<p>'.__('Amount:','booking-ultra-pro').'</p>' ;	
		$html .= '<p><input type="text" id="bup_payment_amount" value="'.$order_amount.'"></p>' ;
		$html .= '<p>'.__('Transaction ID:','booking-ultra-pro').'</p>' ;	
		$html .= '<p><input type="text" id="bup_payment_transaction" value="'.$order_txt_id.'"></p>' ;		
		$html .= '<p>'.__('Date:','booking-ultra-pro').'</p>' ;	
		
		$html .= '<p>'.'<input type="text" class="bupro-datepicker" id="bup_payment_date" value="'.$order_date .'" /></p>' ;
		
		
		$html .= '<input type="hidden" id="bup_payment_id" value="'.$payment_id .'" />' ;
		
		$html .= '<p>'.__('Status:','booking-ultra-pro').'</p>' ;
		$html .= '<p><select name="bup_payment_status" id="bup_payment_status">
				  <option value="pending" >'.__('Pending','booking-ultra-pro').'</option>
				  <option value="confirmed" selected>'.__('Confirmed','booking-ultra-pro').'</option>
				</select>' ;
		
				
		echo $html;		
		die();
	
	}
	
	
	public function re_schedule_confirm () 
	{
		check_ajax_referer("ajax-bup_calendar");
		global $wpdb, $bookingultrapro;
		
		$html='';		
				
		//create reservation in reservation table	
		$booking_id = esc_attr($_POST['booking_id']);			
		$day_id = esc_attr($_POST['bup_booking_date']);
		$service_and_staff_id = esc_attr($_POST['bup_service_staff']);
		$time_slot = esc_attr($_POST['bup_time_slot']);		
		$bup_notify_client_reschedule = esc_attr($_POST['notify_client']);			
		
		$arr_ser = explode("-", $service_and_staff_id);			
		$service_id = $arr_ser[0]; 
		$staff_id = $arr_ser[1];
		
		$arr_time_slot = explode("-", $time_slot);			
		$book_from = $arr_time_slot[0]; 
		$book_to = $arr_time_slot[1];	
		
		$service_details = $bookingultrapro->userpanel->get_staff_service_rate( $staff_id, $service_id ); 
		$amount= $service_details['price'];
		
		$booking_time_from = $day_id .' '.$book_from.':00';
		
		//appointment		
		$appointment = $bookingultrapro->appointment->get_one($booking_id);
		
		$client_id = $appointment->booking_user_id;
		
				
		//service			
		$service = $bookingultrapro->service->get_one_service($service_id);		
		
		$currency = $bookingultrapro->get_option('currency_symbol');		
		$time_format = $bookingultrapro->service->get_time_format();		
		$booking_time = date($time_format, strtotime($booking_time_from ))	;		
		$booking_day = date('l, j F, Y', strtotime($booking_time_from));		
				
						
		$staff_member = get_user_by( 'id', $staff_id );	
		$client = get_user_by( 'id', $client_id );					
										
		
		
		
		$order_data = array(
				
						'booking_id' => $booking_id,					 			 
						 'amount' => $amount,
						 'service_id' => $service_id ,
						 'staff_id' => $staff_id ,						 					 
						 'day' => $day_id,
						 'time_from' => $book_from,
						 'time_to' => $book_to
						 
						 ); 
						 
		$bookingultrapro->order->update_appointment($order_data);
		
		//appointment		
		$appointment = $bookingultrapro->appointment->get_one($booking_id);
		
		$bookingultrapro->messaging->send_reschedule_notification_on_admin($staff_member, $client, $service, $appointment,  $bup_notify_client_reschedule );
		
											
		$html .= '<p><strong>'.__('Done!. The appointment has been rescheduled. Below are the new details.','booking-ultra-pro').'</strong></p>';		
		$html .= '<p>'.__('Appointment Details.','booking-ultra-pro').'</p>';
		
// Translators: %s will be replaced with the service title. This string displays the name of the service being booked.
$service_text = __('Service: %s', 'booking-ultra-pro');

// Translators: %s will be replaced with the booking date. This string displays the date of the booking.
$date_text = __('Date: %s', 'booking-ultra-pro');

// Translators: %s will be replaced with the booking time. This string displays the time of the booking.
$time_text = __('Time: %s', 'booking-ultra-pro');

// Translators: %s will be replaced with the staff member's display name. This string shows the name of the staff member with whom the booking is made.
$with_text = __('With: %s', 'booking-ultra-pro');

// Translators: %1$s will be replaced with the currency symbol and %2$s with the amount. This string shows the cost of the booking.
$cost_text = __('Cost: %1$s%2$s', 'booking-ultra-pro');

// Dynamic content
$service_title = $service->service_title; // Example dynamic content
$booking_day = '2024-07-26'; // Example dynamic content
$booking_time = '10:00 AM'; // Example dynamic content
$staff_member_name = $staff_member->display_name; // Example dynamic content
$currency = '$'; // Example dynamic content
$amount = '100'; // Example dynamic content

// Format the translatable strings with the dynamic content
$formatted_service = sprintf($service_text, $service_title);
$formatted_date = sprintf($date_text, $booking_day);
$formatted_time = sprintf($time_text, $booking_time);
$formatted_with = sprintf($with_text, $staff_member_name);
$formatted_cost = sprintf($cost_text, $currency, $amount);

// Append the formatted strings to $html with HTML tags
$html .= '<p>' . $formatted_service . '</p>';
$html .= '<p>' . $formatted_date . '</p>';
$html .= '<p>' . $formatted_time . '</p>';
$html .= '<p>' . $formatted_with . '</p>';
$html .= '<p>' . $formatted_cost . '</p>';


				
		echo $html;		
		die();
	
	}
	
	public function appointment_get_selected_time () 
	{
		check_ajax_referer('ajax-new_appointment' );
		global $wpdb, $bookingultrapro;
		
		$html='';		
				
		//create reservation in reservation table				
		$day_id = esc_attr($_POST['bup_booking_date']);
		$service_and_staff_id = esc_attr($_POST['bup_service_staff']);
		$time_slot = esc_attr($_POST['bup_time_slot']);		
		$bup_notify_client = esc_attr($_POST['bup_notify_client']);			
		
		$arr_ser = explode("-", $service_and_staff_id);			
		$service_id = $arr_ser[0]; 
		$staff_id = $arr_ser[1];
		
		$arr_time_slot = explode("-", $time_slot);			
		$book_from = $arr_time_slot[0]; 
		$book_to = $arr_time_slot[1];	
		
		$service_details = $bookingultrapro->userpanel->get_staff_service_rate( $staff_id, $service_id ); 
		$amount= $service_details['price'];
		
		$booking_time_from = $day_id .' '.$book_from.':00';
		
				
		//service			
		$service = $bookingultrapro->service->get_one_service($service_id);		
		
		$currency = $bookingultrapro->get_option('currency_symbol');		
		$time_format = $bookingultrapro->service->get_time_format();		
		$booking_time = date($time_format, strtotime($booking_time_from ))	;		
		$booking_day = date('l, j F, Y', strtotime($booking_time_from));		
				
						
		$staff_member = get_user_by( 'id', $staff_id );						
				
		$html .= '<p><strong>'.__('Appointment Details.','booking-ultra-pro').'</strong></p>';
		
		$html .= '<p>'.__('Service: ','booking-ultra-pro').$service->service_title.'</p>' ;	
		$html .= '<p>'.__('Date: ','booking-ultra-pro').$booking_day.'</p>' ;
		$html .= '<p>'.__('Time: ','booking-ultra-pro').$booking_time.'</p>' ;
		$html .= '<p>'.__('With: ','booking-ultra-pro').$staff_member->display_name.'</p>' ;
		$html .= '<p>'.__('Cost: ','booking-ultra-pro').$currency.$amount.'</p>';
				
		echo $html;		
		die();
	
	}
	
	public function appointment_get_payments_list () 
	{
		check_ajax_referer('ajax-new_appointment' );
		global $wpdb, $bookingultrapro;
		
		$html='';		
				
		//create reservation in reservation table				
		$appointment_id = esc_attr($_POST['appointment_id']);	
		
		$totals = array();	
		
		$orders = $bookingultrapro->order->get_booking_payments($appointment_id ); 		
		$currency = $bookingultrapro->get_option('currency_symbol');		
		$time_format = $bookingultrapro->service->get_time_format();
		
		$totals = $bookingultrapro->order->get_booking_payments_balance($appointment_id );
		$paid = $currency.$totals['confirmed']; 
		$pending = $currency.$totals['pending'];	
		$balance = $currency.$totals['balance'];
		$cost = $currency.$totals['cost'];
		
		if($totals['pending']==0){$class_pending = 'bupendingok'; }else{$class_pending = 'bupending';} 
		
				
		if (!empty($orders)){
			
			
			$html .= '<div class="bup-financial-list"> ';
           
		    $html .= ' <ul>';
            $html .= '<li class="bupaid"><h3>'.__('Service Cost','booking-ultra-pro').'</h3><p class="bupaid">'.$cost.'</p></li>
			<li class="bupaid"><h3>'.__('Paid','booking-ultra-pro').'</h3><p class="bupaid">'.$paid.'</p></li>
                      <li class="bupending"><h3>'.__('Pending','booking-ultra-pro').'</h3><p class="'.$class_pending.'">'.$pending.'</p></li>
                     ';
                
          $html .= '  </ul> ';
        
          $html .= ' </div>';		
				
				       
          $html .= '<table width="100%" class="wp-list-table widefat fixed posts table-generic"> ';
           $html .= ' <thead>
                <tr>
                    <th width="3%">'.__('#', 'booking-ultra-pro').'</th>
                    <th width="11%">'.__('Date', 'booking-ultra-pro').'</th>                     
                    <th width="16%">'.__('Transaction ID', 'booking-ultra-pro').'</th>
                    <th width="9%">'.__('Method', 'booking-ultra-pro').'</th>
                     <th width="9%">'.__('Status', 'booking-ultra-pro').'</th>
                    <th width="9%">'.__('Amount', 'booking-ultra-pro').'</th>
					<th width="9%">'.__('Actions', 'booking-ultra-pro').'</th>
                </tr>
            </thead>';
            
           $html .= ' <tbody>';
            
           
				foreach($orders as $order) {
					
					$order_tr = $order->order_txt_id;
					if($order->order_txt_id==''){$order_tr = 'N/A';}
					
					if($order->order_status=='pending'){$class_pending = 'bupending'; }else{$class_pending = 'buconfirmed';}
						
							  
	
				  $html .= '   <tr>
						<td>'.$order->order_id.'</td>
						<td>'. date("m/d/Y", strtotime($order->order_date)).'</td>
						 
						<td>'. $order_tr.'</td>
						 <td>'. $order->order_method_name.'</td>
						  <td class="'.$class_pending.'">'. $order->order_status.'</td>
					   <td> '. $currency.$order->order_amount.'</td>
					   <td> <a href="#" title="'.__('Delete', 'booking-ultra-pro').'" class="bup-payment-deletion" bup-payment-id="'.$order->order_id.'" bup-appointment-id="'.$appointment_id.'"> <i class="fa fa-remove"> </i> </a>
					<a href="#" title="'.__('Edit', 'booking-ultra-pro').'" class="bup-payment-edit" bup-payment-id="'.$order->order_id.'" bup-appointment-id="'.$appointment_id.'"> <i class="fa fa-pencil"> </i> </a>   
					   </td>
					</tr>';
									
					
				   
				}
					
			} else {
			
			$html .='<p>'.__('There are no transactions yet.','booking-ultra-pro').'</p>';
			} 

       $html .='     </tbody>
        </table>';
        
				
		echo $html;		
		die();
	
	}
	
	public function create_new_appointment () 
	{
		check_ajax_referer("ajax-bup_calendar");
		global $wpdb, $bookingultrapro, $bupcomplement;
		session_start();
		
		//create transaction
		$transaction_key = session_id()."_".time();	
		
		$html='';		
				
		//create reservation in reservation table				
		$day_id = esc_attr($_POST['bup_booking_date']);
		$service_and_staff_id = esc_attr($_POST['bup_service_staff']);
		$time_slot = esc_attr($_POST['bup_time_slot']);
		$client_id = esc_attr($_POST['bup_client_id']);
		$bup_notify_client = esc_attr($_POST['bup_notify_client']);


		
		$arr_ser = explode("-", $service_and_staff_id);			
		$service_id = $arr_ser[0]; 
		$staff_id = $arr_ser[1];
		
		$arr_time_slot = explode("-", $time_slot);			
		$book_from = $arr_time_slot[0]; 
		$book_to = $arr_time_slot[1];	
		
		$service_details = $bookingultrapro->userpanel->get_staff_service_rate( $staff_id, $service_id ); 
		$amount= $service_details['price'];	
		$quantity=1; //When appointment is created from admin dashboard same is not reflected in frontend for other user, resolved by adding this
		$order_data = array(
		
				'user_id' => $client_id,	
				 'transaction_key' => $transaction_key,					 
				 'amount' => $amount,
				 'service_id' => $service_id ,
				 'staff_id' => $staff_id ,
				 'product_name' => $p_name ,						 
				 'day' => $day_id,
				 'time_from' => $book_from,
				 'time_to' => $book_to,
				 'quantity'=>$quantity //When appointment is created from admin dashboard same is not reflected in frontend for other user, resolved by adding this
				 
				 ); 
		
		$booking_id =  $bookingultrapro->order->create_reservation($order_data);	
		
		//service			
		$service = $bookingultrapro->service->get_one_service($service_id);
		
		//create order					  
		$order_data_tran = array('user_id' => $user_id,
						 'transaction_key' => $transaction_key,
						 'amount' => $amount,
						 'booking_id' => $booking_id ,
						 'product_name' => $p_name ,
						 'status' => 'pending',		
						 'service_id' => $service_id ,
						 'staff_id' => $staff_id ,				
						 'method' => $payment_method,
						 ); 						 
						 
						
		$order_id = $bookingultrapro->order->create_order($order_data_tran);	
		
		// Get Order
		$rowOrder = $bookingultrapro->order->get_order_pending($transaction_key);								
	
		/*Update Appointment*/						
		$bookingultrapro->appointment->update_appointment_status($booking_id,1,0);
		
		//get appointment			
		$appointment = $bookingultrapro->appointment->get_one($booking_id);
		
		$currency = $bookingultrapro->get_option('currency_symbol');		
		$time_format = $bookingultrapro->service->get_time_format();		
		$booking_time = date($time_format, strtotime($appointment->booking_time_from ))	;		
		$booking_day = date('l, j F, Y', strtotime($appointment->booking_time_from ));		
				
		/*Notify Admin Only of Appointment*/		
					
		$staff_member = get_user_by( 'id', $staff_id );
		$client = get_user_by( 'id', $client_id );					
										
		$bookingultrapro->messaging->send_booking_notification_on_admin($staff_member, $client, $service, $appointment,$rowOrder, $bup_notify_client );
		$full_number = get_user_meta($client_id,'reg_telephone',true); // added by deepak
		$bookingultrapro->appointment->update_booking_meta($booking_id, 'full_number', $full_number);//added by deepak
		
		$google_client_id = $bookingultrapro->get_option('google_calendar_client_id');
		$google_client_secret = $bookingultrapro->get_option('google_calendar_client_secret');
				
		//google calendar				
		if(isset($bupcomplement) && $google_client_id!='' && $google_client_secret!='' )
		{				
					
			$bupcomplement->googlecalendar->create_event($booking_id,$order_data);						
				
		}
				
		
		$html .= '<p>'.__('The appointment has been created.','booking-ultra-pro').'</p>';
		$html .= '<p><strong>'.__('Appointment Details.','booking-ultra-pro').'</strong></p>';
		
// Translators: %s will be replaced with the service title. This string displays the name of the service being booked.
$service_text = __('Service: %s', 'booking-ultra-pro');

// Translators: %s will be replaced with the booking date. This string displays the date of the booking.
$date_text = __('Date: %s', 'booking-ultra-pro');

// Translators: %s will be replaced with the booking time. This string displays the time of the booking.
$time_text = __('Time: %s', 'booking-ultra-pro');

// Translators: %s will be replaced with the staff member's display name. This string shows the name of the staff member with whom the booking is made.
$with_text = __('With: %s', 'booking-ultra-pro');

// Translators: %1$s will be replaced with the currency symbol and %2$s with the order amount. This string shows the cost of the booking.
$cost_text = __('Cost: %1$s%2$s', 'booking-ultra-pro');

// Dynamic content
$service_title = $service->service_title; // Example dynamic content
$booking_day = '2024-07-26'; // Example dynamic content
$booking_time = '10:00 AM'; // Example dynamic content
$staff_member_name = $staff_member->display_name; // Example dynamic content
$currency = '$'; // Example dynamic content
$order_amount = $rowOrder->order_amount; // Example dynamic content

// Format the translatable strings with the dynamic content
$formatted_service = sprintf($service_text, $service_title);
$formatted_date = sprintf($date_text, $booking_day);
$formatted_time = sprintf($time_text, $booking_time);
$formatted_with = sprintf($with_text, $staff_member_name);
$formatted_cost = sprintf($cost_text, $currency, $order_amount);

// Append the formatted strings to $html with HTML tags
$html .= '<p>' . $formatted_service . '</p>';
$html .= '<p>' . $formatted_date . '</p>';
$html .= '<p>' . $formatted_time . '</p>';
$html .= '<p>' . $formatted_with . '</p>';
$html .= '<p>' . $formatted_cost . '</p>';


		
		$response = array('booking_id' => $booking_id, 'content' => $html);
		echo json_encode($response) ;			
		die();
	
	}
	
	
	public function get_public_booking_form ($atts) 
	{
		global $wpdb, $bookingultrapro, $bupcomplement, $bookingultrapro;
		
		extract( shortcode_atts( array(
		
			'staff_id' => NULL,
			'service_id' => NULL,
			'form_id' => NULL, 
			'location_id' => NULL,
			'redirect_url' => NULL,
			'field_legends' => 'yes',
			'placeholders' => 'yes',
			'template_id' => NULL,
			'category_ids' => NULL,
			'auto_display_slots' => 'no'			
			
			
		), $atts ) );
		
		//turn on output buffering to capture script output
        ob_start();		
        
		if(isset($bupcomplement))
		{
			$wp_head = $bookingultrapro->get_option('wp_head_present');
			$arg=array($template_id);
			
			if($wp_head!='0' && $template_id != NULL )	
			{		
				if(!is_admin()) {
					//do_action('wp_head', $arg);	
					$html = "";
					$custom_css = '';
					$bg_color = 'background-color:'.$bookingultrapro->get_template_label("bup_cus_bg_color",$template_id).' !important';
					$custom_css .= '.bup-front-cont .bup-book-steps-cont .bup-cart-step-active{'.$bg_color.'}  ';
					$custom_css .= 'ul.bup-time-slots-available-list .bup-button{'.$bg_color.'}  ';
					$custom_css .= '.bup-button-submit{'.$bg_color.'}  ';	
					
					
					if($custom_css!="" && $template_id!='' )
					{
						$html .= ' <style type="text/css">';
						$html .= $custom_css;
						$html .= ' </style>';			
					}
					
					echo $html;	
				}
				
			
			}
		}
		
		
		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/buppro/appointment.php"))
		{			
			include($theme_path."/buppro/appointment.php");
		
		}else{
			
			include(BOOKINGUP_PATH."/templates/appointment.php");
		
		}
		
		
        $content = ob_get_clean();
		
		return $content ;
		
	
	}
	
	
	public function update_appointment_status ($id,$status, $remote=1)
	{
		$id = esc_sql($id);
		$status = esc_sql($status);
		$remote = esc_sql($remote);
		if($remote)
        check_ajax_referer("ajax-bup_calendar");
		global $wpdb,  $bookingultrapro;
		
		$query = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}bup_bookings
			SET booking_status = %s
			WHERE booking_id = %d",
			$status,
			$id
		);
				$wpdb->query( $query );
	
	}
	
	public function get_cancel_link_of_appointment ($appointment_key, $appointment_id)
	{
		global   $bookingultrapro;		
		
		$site_url =site_url("/");		
		$link = $site_url.'?bupcancelappointment='.$appointment_key.'&bupid='.$appointment_id;
		
		$link = '<a href="'.$link.'">'.$link.'</a>';
		
		return $link;
	
	}
	
	public function get_approval_link_of_appointment ($appointment_key, $appointment_id)
	{
		global   $bookingultrapro;		
		
		$site_url =site_url("/");		
		$link = $site_url.'?bupapprovalappointment='.$appointment_key.'&bupid='.$appointment_id;
		
		$link = '<a href="'.$link.'">'.$link.'</a>';
		
		return $link;
	
	}
	
	
	
	

	
	public function get_one_service ($service_id) 
	{
		global $wpdb, $bookingultrapro;
		
		$sql = $wpdb->prepare(
			"
			SELECT serv.*, cate.*
			FROM {$wpdb->prefix}bup_services serv
			RIGHT JOIN {$wpdb->prefix}bup_categories cate ON (cate.cate_id = serv.service_category_id)
			WHERE serv.service_id = %d
			",
			$service_id
		);			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	public function get_one ($booking_id) 
	{
		global $wpdb, $bookingultrapro;
		$booking_id=esc_sql($booking_id);
		
		$sql = $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}bup_bookings
			WHERE booking_id = %d",
			$booking_id
		);			
					
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	public function get_one_with_key ($key) 
	{
		global $wpdb, $bookingultrapro;
		$key = esc_sql($key);			

		
		$sql = $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}bup_bookings
			WHERE order_key = %s",
			$key
		);		
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	public function get_all_with_cart ($cart_id) 
	{
		global $wpdb, $bookingultrapro;
		$cart_id=esc_sql($cart_id);
		$sql = $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}bup_bookings
			WHERE booking_cart_id = %s",
			$cart_id
		);		
					
				
		$res = $wpdb->get_results($sql);
		
		return $res;
	
	}
	
	/**
     * Get data for FullCalendar.
     *
     * @return json
     */
    public function get_all_staff_appointments()
    {
		global $wpdb, $bookingultrapro;
		
        $result        = array();
        $staff_members = array();
		
        $one_day       = new DateInterval( 'P1D' );		
        $start_date    = new DateTime( $_REQUEST['start'] );
        $end_date      = new DateTime( $_REQUEST['end'] );
		
		$location_id =$_REQUEST['location_id'];
		$staff_id_selected =$_REQUEST['staff_id'];
		
		if($staff_id_selected=='undefined' || $staff_id_selected=='null'){$staff_id_selected='';}        
        if($location_id=='undefined' || $location_id=='null'){$location_id='';}
		
        // FullCalendar sends end date as 1 day further.
        $end_date->sub( $one_day );
     		
		//get all staff members		
		$staff_members = $bookingultrapro->userpanel->get_staff_list_fc($location_id);

        foreach ( $staff_members as $staff ) 
		{
			$staff_id = $staff->ID;
			$item_start_time = '';
			
			if(isset($staff_id_selected) && $staff_id_selected!='' && $staff_id!=$staff_id_selected){
                
				continue;				
			}
			
            /** Get All appointments for this user/staff member */
            $result = array_merge( $result, $bookingultrapro->userpanel->getAppointmentsForFC( $start_date, $end_date, $staff_id ) );

            // Schedule.
            $items = $bookingultrapro->userpanel->get_working_hours($staff_id);
			
            $day   = clone $start_date;
            // Find previous day end time.
            $last_end = clone $day;
            $last_end->sub( $one_day );
            $w = $day->format( 'N' );            
			
			//we need to get the end time for this week day
			$end_time = $items[$w]['end_time'];					
			
            if ( $end_time !== null ) 
			{
                $end_time = explode( ':', $end_time );
                $last_end->setTime( $end_time[0], $end_time[1] );
				
            } else {
				
                $last_end->setTime( 24, 0 );
            }
			
            // Do the loop.
            while ( $day <= $end_date ) 
			{
                do {
					
                    /**  */
					
					if(isset($items[ $day->format( 'N' )]))
					{
						$item = $items[ $day->format( 'N' )];
						$item_start_time = $item['start_time'];
					}
					
                   // if ( $item->get( 'start_time' ) && ! $staff->isOnHoliday( $day ) ) 
				   if ( $item_start_time && $item_start_time!=''  ) 
					{
                        $start = $last_end->format( 'Y-m-d H:i:s' );
                        $end   = $day->format( 'Y-m-d '.$item_start_time );
                        
						if ( $start < $end ) 
						{
                          /* $result[] = array(
                                'start'     => $start,
                                'end'       => $end,
                                'display' => 'background',
                                'resourceId'   => $staff_id,
                            ); */
                        }
						
                        $last_end = clone $day;
                        $end_time = explode( ':', $item[ 'end_time'] );
                        $last_end->setTime( $end_time[0], $end_time[1] );

                        // Breaks.
                       /* foreach ( $item->getBreaksList() as $break ) 
						{
                            $result[] = array(
                                'start'     => $day->format( 'Y-m-d '.$break['start_time'] ),
                                'end'       => $day->format( 'Y-m-d '.$break['end_time'] ),
                                'display' => 'background',
                                'resourceId'   => $staff_id,
                            );
                        }*/

                        break;
                    }

                   $result[] = array(
                        'start'     => $last_end->format( 'Y-m-d H:i:s' ),
                        'end'       => $day->format( 'Y-m-d 24:00:00' ),
                        'display' => 'background',
                        'resourceId'   => $staff_id,
                    ); 
					
                    $last_end = clone $day;
                    $last_end->setTime( 24, 0 );

                } while ( 0 );

                $day->add( $one_day );
            }

            if ( $last_end->format( 'H' ) != 24 )
			{
                $result[] = array(
                    'start'     => $last_end->format( 'Y-m-d H:i:s' ),
                    'end'       => $last_end->format( 'Y-m-d 24:00:00' ),
                    'display' => 'background',
                    'resourceId'   =>$staff_id,
                );  
            }
			
			
			
        } //end foreach
		

        wp_send_json( $result );
    }
	
	function get_week_date_range ($current_day)
	{
		$range = array();		
		
		$range = array('from' => date("Y-m-d",strtotime('monday this week', $current_day)), 'to' => date("Y-m-d",strtotime("sunday this week", $current_day)));
		
		return $range;

	
	
	}
	
	public function get_appointments_total_by_status($status=null, $staff_id =null)
	{
		
		global $wpdb, $bookingultrapro;
		$status = esc_sql($status);
		$staff_id = esc_sql($staff_id);
		// 0 pending, 1 approved, 2 cancelled
		
		$total = 0;
		
		$sql =  'SELECT count(*) as total FROM ' . $wpdb->prefix . 'bup_bookings   ' ;	
		//if($status!='') //deepak						
		$sql .= $wpdb->prepare(" WHERE  booking_status = %s ",$status);
		
		if($staff_id!='')	
		{
			$sql .= $wpdb->prepare(" AND  booking_staff_id = s ",$staff_id);
			
		}
			
		$appointments = $wpdb->get_results($sql );
		
		foreach ( $appointments as $appointment )
		{
				$total= $appointment->total;			
			
		}
	
		
		return $total;
	
	
	}
	
	
	
		
	public function get_appointments_planing_total($when)
	{
		
		global $wpdb, $bookingultrapro;
		
		$total = 0;
		
		if($when=='today')
		{
			 $date = date( 'Y-m-d ', current_time( 'timestamp', 0 ) );
			 $date=esc_sql($date);	 		
		}elseif($when=='tomorrow'){
			
			$ini_date = date( 'Y-m-d ', current_time( 'timestamp', 0 ) );
			$date=  date("Y-m-d", strtotime("$ini_date + 1 day"));
			$ini_date=esc_sql($ini_date);
			$date=esc_sql($date);	
		}
					
       		
		if($when=='week')
		{
			$dt_min = new DateTime("last sunday");
			$dt_max = clone($dt_min);
			$dt_max->modify('+6 days');
			
			$date_from =$dt_min->format('Y-m-d');
			$date_to =$dt_max->format('Y-m-d');
			
			$date_from=esc_sql($date_from);
			$date_to=esc_sql($date_to);	
			
			$sql =  'SELECT count(*) as total, appo.*, usu.* FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
			$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";					
			$sql .= $wpdb->prepare(" WHERE DATE(appo.booking_time_from) >= %s AND DATE(appo.booking_time_to) <= %s AND usu.ID = appo.booking_staff_id AND appo.booking_status = '1' ",$date_from,$date_to);
		}elseif($when=='all'){
			
			
			$sql =  'SELECT count(*) as total, appo.*, usu.* FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
			$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";					
			$sql .= " WHERE  usu.ID = appo.booking_staff_id   ";			
			
		}else{
			
			$sql =  'SELECT count(*) as total, appo.*, usu.* FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
			$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";					
			$sql .= $wpdb->prepare(" WHERE DATE(appo.booking_time_from) = %s AND usu.ID = appo.booking_staff_id AND  appo.booking_status = '1' ",$date);
			
		
		}	
			
		$appointments = $wpdb->get_results($sql );
		//print_r("$appointments");
		
		foreach ( $appointments as $appointment )
		{
				$total= $appointment->total;	
			
		}
		
		return $total;
	
	
	}
	
	public function get_sales_total_by_day($date)
	{
		
		global $wpdb, $bookingultrapro;
		$date=esc_sql($date);
		
		$total = 0;
		
		$sql =  'SELECT count(*) as total, appo.*, usu.* FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";					
		$sql .= $wpdb->prepare(" WHERE DATE(appo.booking_time_from) = %s AND usu.ID = appo.booking_staff_id AND  appo.booking_status = '1' ",$date);
			
		$appointments = $wpdb->get_results($sql );
		
		foreach ( $appointments as $appointment )
		{
				$total= $appointment->total;			
			
		}
					
		
		return $total;
	}
	
	public function get_graph_total_monthly () 
	{
		global $wpdb, $bookingultrapro;
		
		$date_format =  $bookingultrapro->get_int_date_format();		
		$days_of_month = date("t");		
		$day = 1; 
		
		$vals='';
		while($day <= $days_of_month) {
			
			//get sales
			$date = date("Y").'-'.date("m").'-'.$day;
			
			//$date = date("Y").'-2-'.$day;
			
			$total = $this->get_sales_total_by_day($date);
			$day_format =$day;			
			$vals .= "['".$day_format."', $total]";			
			$day++;
			
			if($day <= $days_of_month){
				
				$vals .= ',';		
			}
		} 
		
		return $vals;		
		
	}
	
	public function get_appointment_status_options()
	{
		check_ajax_referer("ajax-bup_calendar");
		global $wpdb, $bookingultrapro;
		
		$html = '';
		
		$appointment_id = esc_attr($_POST['appointment_id']);
		
		$html .='<div class="bup-appointment-status-update">';
		$html .='<ul>';
		$html .='<li><a href="#" class="bup-adm-change-appoint-status-opt" appointment-id="'.$appointment_id.'" appointment-status="0" title="'.__('Change Status','booking-ultra-pro').'"><i class="fa fa-edit"></i><span> '.__('Pending','booking-ultra-pro').'</span></a></li>';
		
		$html .='<li><a href="#" class="bup-adm-change-appoint-status-opt" appointment-id="'.$appointment_id.'" appointment-status="1" title="'.__('Change Status','booking-ultra-pro').'"><i class="fa fa-check"></i><span> '.__('Approved','booking-ultra-pro').'</span></a></li>';
		
		$html .='<li><a href="#" class="bup-adm-change-appoint-status-opt" appointment-id="'.$appointment_id.'" appointment-status="2" title="'.__('Change Status','booking-ultra-pro').'"><i class="fa fa-remove"></i><span> '.__('Cancelled','booking-ultra-pro').'</span></a></li>';
		
		$html .='<li><a href="#" class="bup-adm-change-appoint-status-opt" appointment-id="'.$appointment_id.'" appointment-status="3" title="'.__('Change Status','booking-ultra-pro').'"><i class="fa fa fa-eye-slash"></i><span> '.__('No-Show','booking-ultra-pro').'</span></a></li>';
		
		$html .='</ul>';
		$html .='</div>';
				
				
		echo $html;
		die();
	
	}
	
	public function get_appointments_quick()
	{
		check_ajax_referer("ajax-bup_calendar");
		
		global $wpdb, $bookingultrapro;
		
		$html = '';
		
		$status = esc_attr($_POST['status']);
		$type = esc_attr($_POST['type']);
		
		if($type=='bystatus')		
		{
			
			$html = $this->get_appointments_by_status($status, $type);
			
			
		}elseif($type=='byunpaid'){
			
			
			$html = $this->get_unpaid_orders($status, $type);
		
		
		
		}
		
		echo $html;
		die();
	
	}
	
	public function get_status_legend($status)
	{
		global  $bookingultrapro;
		
		//0 Pending , 1 Approved, 2 Cancelled, 3 No-Show
		
		$legend ='';
		
		if($status==0)
		{			
			$legend ="<span class='bup-app-legend-pending'>".__("Pending",'booking-ultra-pro')."</span>";
			
		}elseif($status==1){
			
			$legend ="<span class='bup-app-legend-approved'>".__("Approved",'booking-ultra-pro')."</span>";
			
		}elseif($status==2){
			
			$legend ="<span class='bup-app-legend-cancelled'>".__("Cancelled",'booking-ultra-pro')."</span>";
		
		}elseif($status==3){
			
			$legend ="<span class='bup-app-legend-noshow'>".__("No-Show",'booking-ultra-pro')."</span>";
			
		}
		
		return $legend;
		
	}
	
	
	public function update_appointment_status_ed()
	{
		check_ajax_referer("ajax-bup_calendar");
		global $wpdb, $bookingultrapro;
		
		$status = esc_attr($_POST['appointment_status']);
		$appointment_id = esc_attr($_POST['appointment_id']);
		
		$sql = $wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bup_bookings SET booking_status =%s WHERE booking_id=%d ;',array($status,$appointment_id));		
		$results = $wpdb->query($sql);
		
		
		//change appointment status		
		$appointment = $this->get_one($appointment_id);		
		$staff_id = $appointment->booking_staff_id;	
		$client_id = $appointment->booking_user_id;	
		$service_id = $appointment->booking_service_id;
		
				
		/*Get Service*/			
		$service = $bookingultrapro->service->get_one_service($service_id);		
		$new_status = $this->get_status_legend($status);			
						
											 
		//get user				
		$staff_member = get_user_by( 'id', $staff_id );
		$client = get_user_by( 'id', $client_id );					
											
		$bookingultrapro->messaging->send_appointment_status_changed($staff_member, $client, $service, $appointment, $new_status);	
		
		
		$appointment = $this->get_one($appointment_id);		
		$html = $this->get_status_legend($appointment->booking_status);
		echo $html;
		die();
		
	}
	
	public function update_appointment_status_inline()
	{
		check_ajax_referer("ajax-bup_calendar");
		global $wpdb, $bookingultrapro;
		
		$status = esc_attr($_POST['appointment_status']);
		$appointment_id = esc_attr($_POST['appointment_id']);
		
		$sql = $wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bup_bookings SET booking_status =%d WHERE booking_id=%d ;',array($status,$appointment_id));
		
		$results = $wpdb->query($sql);
		die();
		
	}
	
	public function update_payment_status_inline()
	{
		check_ajax_referer("ajax-bup_calendar");
		global $wpdb, $bookingultrapro;
		
		$status = esc_attr($_POST['payment_status']);
		$payment_id = esc_attr($_POST['payment_id']);
		
		$sql = $wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bup_orders SET order_status =%s WHERE order_id=%d ;',array($status,$payment_id));
		
		$results = $wpdb->query($sql);
		die();
		
	}
	
	
	
	public function get_appointments_by_status($status, $type)
	{
		
		global $wpdb, $bookingultrapro;
				
		$appointments_re = array();
		$staff_service_details = array();
		
		$time_format =  $bookingultrapro->service->get_time_format();
		$status=esc_sql($status);
		$sql =  'SELECT appo.*, usu.*, serv.*	 FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";		
		$sql .= $wpdb->prepare(" WHERE serv.service_id = appo.booking_service_id  AND  appo.booking_status = %s  ORDER BY appo.booking_time_from   asc ",$status);	
			
		$appointments = $wpdb->get_results($sql );		
		
		$html = '';
		
		$html .= '<div class="bup-quick-list-appointments">';
		
		
		if ( !empty( $appointments ) )
		{
			$html .= '<ul>';
			
			 foreach ( $appointments as  $appointment ) 
			 {
				 $date_from=  date("Y-m-d", strtotime($appointment->booking_time_from));
				 
				 $staff = $bookingultrapro->userpanel->get_staff_member($appointment->booking_staff_id);
				 
				 $html .= '<li>';
				 
				  $html .= '<span class="bup-quick-appointment-action" id="bup-break-add-1">';
				  
				  $html .= '<a href="#" class="ubp-appo-change-status" bup-type="'.$type.'" bup-status="'.$status.'" title="'.__("Cancel Appointment",'booking-ultra-pro').'" appointment-id="'.$appointment->booking_id.'" appointment-status="2"><i class="fa fa-remove"></i></a>';
				  
				   $html .= '<a href="#" class="ubp-appo-change-status" bup-type="'.$type.'" bup-status="'.$status.'" title="'.__("No-Show Appointment",'booking-ultra-pro').'" appointment-id="'.$appointment->booking_id.'" appointment-status="3"><i class="fa fa-eye-slash"></i></a>';
				  
				 $html .= '<a href="#" class="ubp-appo-change-status" bup-type="'.$type.'" bup-status="'.$status.'" title="'.__("Confirm Appointment",'booking-ultra-pro').'" appointment-id="'.$appointment->booking_id.'" appointment-status="1" ><i class="fa fa-check"></i></a>';
				  
				 $html .= '</span>';
				 $html .= '<h3>'.date('l, j F, Y', strtotime($date_from)).'</h3>';
				 $html .= '<div class="bup-app-info"><strong>'.$appointment->service_title.'</strong> '. __("with",'booking-ultra-pro').' <strong>'.$staff->display_name.'</strong> </div>';
				  
				 $html .= '</li>';
			
			
			 }
			 
			 $html .= '</ul>';
		
		 }else{
			 
			 $html = __("There are no appointments",'booking-ultra-pro');
			 
			 
			 
		 }
		 
		$html .= '</div>';
		 
		 return $html;
		 	
	
	}
	
	public function get_unpaid_orders($status, $type)
	{
		
		global $wpdb, $bookingultrapro;
				
		$appointments_re = array();
		$staff_service_details = array();
		
		$time_format =  $bookingultrapro->service->get_time_format();
       		
		$sql =  'SELECT ord.*,  usu.*, serv.* , appo.*	 FROM ' . $wpdb->prefix . 'bup_orders ord  ' ;
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_bookings appo ON (ord.order_booking_id = appo.booking_id)";				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";	
			
		$sql .= " WHERE serv.service_id = appo.booking_service_id  AND  ord.order_booking_id = appo.booking_id  AND ord.order_status = 'pending' ORDER BY appo.booking_time_from   asc ";
		
		
			
		$appointments = $wpdb->get_results($sql );		
		
		$html = '';
		
		$html .= '<div class="bup-quick-list-appointments">';
		
		
		if ( !empty( $appointments ) )
		{
			$html .= '<ul>';
			
			 foreach ( $appointments as  $appointment ) 
			 {
				 $booking_date=  date("Y-m-d", strtotime($appointment->booking_time_from));
				 $order_date=  date("Y-m-d", strtotime($appointment->order_date));
				 
				 $staff = $bookingultrapro->userpanel->get_staff_member($appointment->booking_staff_id);
				 
				 $html .= '<li>';
				 
				  $html .= '<span class="bup-quick-appointment-action" id="bup-break-add-1">';
				  
				 				  
				 $html .= '<a href="#" class="ubp-payment-change-status" bup-type="'.$type.'" bup-status="'.$status.'" title="'.__("Confirm Payment",'booking-ultra-pro').'" payment-id="'.$appointment->order_id.'" order-status="confirmed" ><i class="fa fa-check"></i></a>';
				  
				 $html .= '</span>';
				 $html .= '<h3>'.date('l, j F, Y', strtotime($order_date)).'</h3>';
				 $html .= '<div class="bup-app-info"><strong>'.$appointment->service_title.'</strong> '. __("with",'booking-ultra-pro').' <strong>'.$staff->display_name.'</strong> </div>';
				 $html .= '<div class="bup-app-info">'.__("Cost: ",'booking-ultra-pro').'<strong>'.$appointment->order_amount.'</strong> </div>';
				 $html .= '<div class="bup-app-info">'.__("Appointment Date: ",'booking-ultra-pro').'<strong>'.date('l, j F, Y', strtotime($booking_date)).'</strong> </div>';
				  
				 $html .= '</li>';
			
			
			 }
			 
			 $html .= '</ul>';
		
		 }else{
			 
			 $html = __("There are no unpaid orders",'booking-ultra-pro');
			 
			 
			 
		 }
		 
		$html .= '</div>';
		 
		 return $html;
		 	
	
	}
	
	public function get_upcoming_appointments($how_many = 5)
	{
		
		global $wpdb, $bookingultrapro;
		$how_many=(int)$how_many;
		$appointments_re = array();
		$staff_service_details = array();
		
		$time_format =  $bookingultrapro->service->get_time_format();
       		
		$sql =  'SELECT appo.*, usu.*, serv.*	 FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";		
		$sql .= " WHERE DATE(appo.booking_time_from) >= '".date('Y-m-d')."' AND serv.service_id = appo.booking_service_id  ORDER BY appo.booking_time_from asc ";
        
        $sql .= $wpdb->prepare( " LIMIT %d ",$how_many);
			
		$appointments = $wpdb->get_results($sql );		
		
		return $appointments;
	
	
	}
	
	public function get_appointment_with_key_approval($key)
	{
		
		global $wpdb, $bookingultrapro;
			
		$key=esc_sql($key);
		$sql =  'SELECT appo.*, usu.*, serv.*	 FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";		
		$sql .= $wpdb->prepare(" WHERE  appo.booking_key = %s  AND appo.booking_status = '0' AND usu.ID = appo.booking_staff_id AND serv.service_id = appo.booking_service_id ",$key);	
			
		$appointments = $wpdb->get_results($sql );	
		
		if ( !empty( $appointments ) )
		{
			
			foreach ( $appointments as  $appointment ) 
			{
				return $appointment;
			
			}
		
		}else{
			
			return false;
			
		}
	
	}
	
	public function get_appointment_with_key_cancellation($key)
	{
		
		global $wpdb, $bookingultrapro;
		$key=esc_sql($key);
       	
		$sql =  'SELECT appo.*, usu.*, serv.*	 FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";		
		$sql .= $wpdb->prepare(" WHERE  appo.booking_key = %s  AND appo.booking_status <> '2' AND usu.ID = appo.booking_staff_id AND serv.service_id = appo.booking_service_id ",$key);	
		
		//echo $sql;
			
		$appointments = $wpdb->get_results($sql );	
		
		if ( !empty( $appointments ) )
		{
			
			foreach ( $appointments as  $appointment ) 
			{
				return $appointment;
			
			}
		
		}else{
			
			return false;
			
		}
	
	}
	
	public function get_appointment_with_key($key)
	{
		
		global $wpdb, $bookingultrapro;
			
		$key=esc_sql($key);
		$sql =  'SELECT appo.*, usu.*, serv.*	 FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";		
		$sql .= $wpdb->prepare(" WHERE  appo.booking_key = %s  ",$key);	
			
		$appointments = $wpdb->get_results($sql );	
		
		if ( !empty( $appointments ) )
		{
			
			foreach ( $appointments as  $appointment ) 
			{
				return $appointment;
			
			}
		
		}else{
			
			return false;
			
			
		}
	
	
	}
	
	public function get_upcoming_app_list($how_many = 20)
	{
		global $wpdb, $bookingultrapro;
		$how_many=(int)$how_many;
		
		$appointments = $this->get_upcoming_appointments($how_many);
		
		$html = '';
		
		
		if ( !empty( $appointments ) )
		{
			$html .= '<ul>';
			
			 foreach ( $appointments as  $appointment ) 
			 {
				 $date_from=  date("Y-m-d", strtotime($appointment->booking_time_from));
				 
				 $staff = $bookingultrapro->userpanel->get_staff_member($appointment->booking_staff_id);
				 
				 $html .= '<li>';
				 $html .= '<h3>'.date('l, j F, Y', strtotime($date_from)).'</h3>';
				 $html .= '<div class="bup-app-info"><strong>'.$appointment->service_title.'</strong> '. __("with",'booking-ultra-pro').' <strong>'.$staff->display_name.'</strong> </div>';
				  
				 $html .= '</li>';
			
			
			 }
			 
			 $html .= '</ul>';
		
		 }else{
			 
			 $html = __("There are no upcoming appointments",'booking-ultra-pro');
			 
			 
			 
		 }
		 
		
		 
		 return $html;
	
	}
	
	public function get_booking_location ($filter_id) 
	{
		global $wpdb, $bookingultrapro;
		$filter_id=esc_sql($filter_id);
		$sql = ' SELECT * FROM ' . $wpdb->prefix . 'bup_filters  ' ;
		$sql .= $wpdb->prepare(' WHERE filter_id = %d',$filter_id) ;			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	
	/*Get all*/
	public function get_all ($return_total=null)//changed by deepak
	{
		global $wpdb,  $bup_filter, $bookingultrapro;
		
		$keyword = "";
		$month = "";
		$day = "";
		$year = "";
		$howmany = "";
		$ini = 1;
		
		$bup_staff_calendar = "";
		
		$special_filter='';
		
		if(isset($_GET["keyword"]))
		{
			$keyword = esc_attr($_GET["keyword"]);		
		}
		
		if(isset($_GET["month"]))
		{
			$month = esc_sql($_GET["month"]);		
		}
		
		if(isset($_GET["day"]))
		{
			$day = esc_sql($_GET["day"]);		
		}
		
		if(isset($_GET["year"]))
		{
			$year = esc_sql($_GET["year"]);		
		}
		
		if(isset($_GET["howmany"]))
		{
			$howmany = (int)$_GET["howmany"];		
		}
		
		if(isset($_GET["special_filter"]))
		{
			$special_filter = esc_attr($_GET["special_filter"]);		
		}
		
		if(isset($_GET["bup-staff-calendar"]))
		{
			$bup_staff_calendar = esc_attr($_GET["bup-staff-calendar"]);		
		}

		if(isset($_GET["paged"])) // deepak
		{
			$bup_paged = (int)$_GET["paged"];		
		}
		else
		{
			$bup_paged = 1;
		}
		
		$uri= esc_attr($_SERVER['REQUEST_URI']) ;
		$url = explode("&ini=",$uri);
		
		if(is_array($url ))
		{
			if(isset($url["1"]))
			{
				$ini = $url["1"];
			    if($ini == ""){$ini=1;}
			
			}
		
		}		
		
		if($howmany == ""){$howmany=10;}
		$limit = 10;
		$current_page = $ini;
		$target_page =  site_url()."/wp-admin/admin.php?page=bookingultra&tab=appointments";		
		$how_many_per_page =  $howmany;
		//get all	
		
		$sql =  "SELECT appo.*, usu.*, serv.* 	  " ;
		
		if($special_filter!="" && isset($bup_filter))
		{
			$sql .= ", bookmeta.*, bookfilter.* ";			
		}
		
		$sql .= " FROM " . $wpdb->prefix . "bup_bookings appo ";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";	
		
		if($special_filter!="" && isset($bup_filter))
		{
			$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_bookings_meta bookmeta ON (bookmeta.meta_booking_id = appo.booking_id)";
			$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_filters bookfilter ON (bookfilter.filter_id = bookmeta.meta_booking_value)";
		}
			
		$sql .= " WHERE serv.service_id = appo.booking_service_id    ";	
		
		if($special_filter!=""){$sql .= $wpdb->prepare(" AND bookmeta.meta_booking_id = appo.booking_id AND bookfilter.filter_id = bookmeta.meta_booking_value AND bookmeta.meta_booking_value= %s AND bookmeta.meta_booking_name='filter_id' ",$special_filter);	}

		if($keyword!="")
		{
			$sql .= $wpdb->prepare("AND (ord.order_txt_id LIKE %s OR usu.display_name LIKE %s OR usu.user_email LIKE %s OR usu.user_login LIKE %s)", '%' . $wpdb->esc_like($keyword) . '%', '%' . $wpdb->esc_like($keyword) . '%', '%' . $wpdb->esc_like($keyword) . '%', '%' . $wpdb->esc_like($keyword) . '%');
		}
		
		if($bup_staff_calendar!="")
		{
			$sql .= $wpdb->prepare(" AND  appo.booking_staff_id = %s ",$bup_staff_calendar);
			
		}
		
		if($day!=""){$sql .= $wpdb->prepare(" AND DAY(appo.booking_time_from) = %d  ",$day);	}
		if($month!=""){	$sql .= $wpdb->prepare(" AND MONTH(appo.booking_time_from) = %d  ",$month);	}		
		if($year!=""){$sql .= $wpdb->prepare(" AND YEAR(appo.booking_time_from) = %d ",$year);}	
		
		$sql .= " ORDER BY appo.booking_id DESC";	
		$from=($bup_paged-1)*$howmany;	//deepak
		$to = $how_many_per_page + $from;
		$sql .= $wpdb->prepare(" LIMIT %d,%d",$from,$to);					
		$appointments = $wpdb->get_results($sql );		
		return $appointments ;			
	}
		/*Get total number filtered appointments*/
		public function get_total_of_filtered_appo ($return_total=null)//changed by deepak
		{
			global $wpdb,  $bup_filter, $bookingultrapro;
			
			$keyword = "";
			$month = "";
			$day = "";
			$year = "";
			$howmany = "";			
			$bup_staff_calendar = "";
			
			$special_filter='';
			
			if(isset($_GET["keyword"]))
			{
				$keyword = esc_attr($_GET["keyword"]);		
			}
			
			if(isset($_GET["month"]))
			{
				$month = esc_sql($_GET["month"]);		
			}
			
			if(isset($_GET["day"]))
			{
				$day = esc_sql($_GET["day"]);		
			}
			
			if(isset($_GET["year"]))
			{
				$year = esc_sql($_GET["year"]);		
			}
			if(isset($_GET["special_filter"]))
			{
				$special_filter = esc_attr($_GET["special_filter"]);		
			}
			
			if(isset($_GET["bup-staff-calendar"]))
			{
				$bup_staff_calendar = esc_attr($_GET["bup-staff-calendar"]);		
			}
	
					
			//get total				
			$sql =  "SELECT count(*) as total, usu.*, serv.* , appo.* 	  " ;
			
			if($special_filter!="" && isset($bup_filter))
			{
				$sql .= ", bookmeta.*, bookfilter.* ";			
			}
			
			$sql .= " FROM " . $wpdb->prefix . "bup_bookings appo ";
							
			$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
			$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";	
			
			
			if($special_filter!="" && isset($bup_filter))
			{
				$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_bookings_meta bookmeta ON (bookmeta.meta_booking_id = appo.booking_id)";
				$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_filters bookfilter ON (bookfilter.filter_id = bookmeta.meta_booking_value)";
			}
				
			$sql .= " WHERE serv.service_id = appo.booking_service_id  ";
			
			
			if($special_filter!=""){$sql .= $wpdb->prepare(" AND bookmeta.meta_booking_id = appo.booking_id AND bookfilter.filter_id = bookmeta.meta_booking_value AND bookmeta.meta_booking_value=%s AND bookmeta.meta_booking_name='filter_id' ",$special_filter);	}
				
			if($keyword!="")
			{
				$keyword = '%' . $keyword . '%';
				$sql.=$wpdb->prepare(" AND (ord.order_txt_id LIKE %s OR usu.display_name LIKE %s OR usu.user_email LIKE %s OR usu.user_login LIKE %s )", $keyword, $keyword, $keyword, $keyword);

			
			}
			
			if($bup_staff_calendar!="")
			{
				$sql .= $wpdb->prepare(" AND  appo.booking_staff_id = %s ",$bup_staff_calendar);
				
			}	
			
			
			if($day!=""){$sql .= $wpdb->prepare(" AND DAY(appo.booking_time_from) = %d  ",$day);	}
			if($month!=""){	$sql .= $wpdb->prepare(" AND MONTH(appo.booking_time_from) = %d  ",$month);	}		
			if($year!=""){$sql .= $wpdb->prepare(" AND YEAR(appo.booking_time_from) = %d",$year);}	
			
			$appointments = $wpdb->get_results($sql );
			$appointments_total = $this->fetch_result($appointments);
			$appointment_total = $appointments_total->total;

				
			
			return $appointment_total ;
			
		
		}

	public function fetch_result($results)
	{
		if ( empty( $results ) )
		{
		
		
		}else{
			
			
			foreach ( $results as $result )
			{
				return $result;			
			
			}
			
		}
		
	}
	
	public function calculate_from($ini, $howManyPagesPerSearch, $total_items)	
	{
		if($ini == ""){$initRow = 0;}else{$initRow = $ini;}
		
		if($initRow<= 1) 
		{
			$initRow =0;
		}else{
			
			if(($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch>= $total_items) {
				$initRow = $totalPages-$howManyPagesPerSearch;
			}else{
				$initRow = ($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch;
			}
		}
		
		
		return $initRow;
		
		
	}

	
}
$key = "appointment";
$this->{$key} = new BookingUltraAppointment();
?>