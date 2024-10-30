<?php
global $bookingultrapro, $bupcomplement;

//get appointment			
$appointment = $bookingultrapro->appointment->get_one($appointment_id);
$staff_id = $appointment->booking_staff_id;	
$client_id = $appointment->booking_user_id;	
$service_id = $appointment->booking_service_id;
$booking_time_from = $appointment->booking_time_from;

$client = $bookingultrapro->userpanel->get_one($client_id);

$currency = $bookingultrapro->get_option('currency_symbol');		
$time_format = $bookingultrapro->service->get_time_format();		
$booking_time = date($time_format, strtotime($booking_time_from ))	;		
$booking_day = date('D, j F, Y', strtotime($booking_time_from));
?>



<div class="bup-adm-new-appointment bup-adm-schedule-info-bar">

	 <strong><?php esc_html_e('Created on : ','booking-ultra-pro')?></strong> <?php echo date('m/d/Y', strtotime($appointment->booking_date));?> | <strong><?php esc_html_e('Appointment Date: ','booking-ultra-pro');?></strong> <?php echo esc_attr($booking_day);?> <?php esc_html_e('at ','booking-ultra-pro');?> <?php echo $booking_time;?> | <strong><?php esc_html_e('Client: ','booking-ultra-pro');?></strong>	<?php echo esc_attr($client->ID);?>, <?php echo esc_attr($client->display_name);?> (<?php echo esc_attr($client->user_email);?>)	(<?php echo esc_attr(get_user_meta($client->id,"reg_telephone", true));?>)           
             
</div>
<div class="bup-adm-new-appointment">	

    <div class="bup-adm-frm-blocks" >               
                   
        <div class="field-header"><?php esc_html_e('Select Service','booking-ultra-pro')?></div>                   
        <?php echo $bookingultrapro->service->get_categories_drop_down_admin($service_id);?>                            
               
   </div>
   
    <div class="bup-adm-frm-blocks" >
            
        <div class="field-header"><?php esc_html_e('On or After','booking-ultra-pro')?> </div> 
        <input type="text" class="bupro-datepicker" id="bup-start-date" value="<?php echo date($bookingultrapro->get_date_picker_date(), strtotime($appointment->booking_time_from))?>" />         
           
    </div>
        
     <div class="bup-adm-frm-blocks" id="bup-staff-booking-list" >
            
              <div class="field-header"><?php esc_html_e('With','booking-ultra-pro')?>  </div> 
           
              <?php echo $bookingultrapro->userpanel->get_staff_list_front();?>          
     </div>  
     
      
           


</div>

<div class="bup-adm-bar-opt-edit">

<?php $app_status = $bookingultrapro->appointment->get_status_legend($appointment->booking_status);?>

<p><strong><?php esc_html_e('Status','booking-ultra-pro')?></strong>: <span id="bup-app-status"> <?php echo $app_status?> </span> <span> <a href="#" id="bup-adm-update-appoint-status-btn" appointment-id="<?php echo esc_attr($appointment_id)?>" title="<?php esc_html_e('Change Status','booking-ultra-pro')?>"><i class="fa fa-edit"></i></a></span>  <p>

</div>
<div class="bup-adm-check-av-button"  > 
         
      <input type="checkbox" id="bup_re_schedule" name="bup_re_schedule" value="1"> <?php esc_html_e('Reschedule Appointment','booking-ultra-pro')?>
         
</div>

 <div class="bup-adm-check-av-button"  id="bup-availability-box-btn" style="display:none"> 
         
       <button id="bup-adm-check-avail-btn-edit" class="bup-button-submit"><?php esc_html_e('Check Availability','booking-ultra-pro')?></button>
         
</div> 

<div class="bup-adm-new-appointment" id="bup-availability-box" style="display:none">
<input type="hidden" id="bup_time_slot" value="">
<input type="hidden" id="bup_booking_date" value="">
<input type="hidden" id="bup_client_id" value="">
<input type="hidden" id="bup_service_staff" value="">
<input type="hidden" id="bup_custom_form" value="">
<input type="hidden" id="bup_appointment_id" value="<?php echo esc_attr($appointment_id);?>">

<h3><?php esc_html_e('Availability','booking-ultra-pro')?> </h3>
    
    <div class="bup-adm-availa-box" id="bup-steps-cont-res-edit" >  
    
    <p> <?php esc_html_e('Please click on the Check Availability to display the available time slots.','booking-ultra-pro')?> </p>      
                
               
        
    </div> 
    
     <div class="bup-adm-check-av-button-d"  > 
         
      <input type="checkbox" id="bup_notify_client_reschedule" name="bup_notify_client_reschedule" value="1" checked="checked"> <?php esc_html_e('Send Notification To Client','booking-ultra-pro')?>
         
</div>
    
    <div class="bup-adm-check-av-button-d"  id="bup-availability-box-btn"> 
         
       <button id="bup-adm-confirm-reschedule-btn" class="bup-button-submit-changes"><?php esc_html_e('Confirm Reschedule ','booking-ultra-pro')?></button>
         
</div> 
    
    


</div>

<div class="bup-adm-new-appointment">

	<div class="bup-adm-extrainfo-box" id="bup-additioninfo-cont-res" >         
                
               <?php echo  $bookingultrapro->appointment->get_appointment_edition_form_fields($appointment_id);?>
               
    </div>

    <div class="bup-adm-check-av-button"  id="bup-addpayment-box-btn" > 
         
       	<button id="bup-adm-update-info" class="bup-button-submit-changes"><?php esc_html_e('Update Info','booking-ultra-pro')?></button>
         
		</div>
</div>
<?php if(isset($bupcomplement)){
	
	echo $client->payment->get_payments_module();
	echo $client->note->get_admin_module();
	?>


<?php }?> 



 <div class="bup-adm-check-av-button"  > 
         
      <input type="checkbox" id="bup_notify_client" name="bup_notify_client" value="1"> <?php esc_html_e('Send Notification To Client','booking-ultra-pro')?>
         
</div>