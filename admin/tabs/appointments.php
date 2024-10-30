<?php
global $bookingultrapro , $bup_filter, $bupultimate, $bupcomplement;
$currency_symbol =  $bookingultrapro->get_option('paid_membership_symbol');
$date_format =  $bookingultrapro->get_int_date_format();
$time_format =  $bookingultrapro->service->get_time_format();
//$appointments = $bookingultrapro->appointment->get_all();
//$total_filtered_appo = $bookingultrapro->appointment->get_all(1);//deepak

$approved = $bookingultrapro->appointment->get_appointments_total_by_status(1);
$pending = $bookingultrapro->appointment->get_appointments_total_by_status(0,'');
$cancelled = $bookingultrapro->appointment->get_appointments_total_by_status(2);
$noshow = $bookingultrapro->appointment->get_appointments_total_by_status(3);
$unpaid = $bookingultrapro->order->get_orders_by_status('pending');
$total_appo = $approved+$pending+$cancelled+$noshow;

//$allappo = $bookingultrapro->appointment->get_appointments_planing_total("all");

$howmany = "";
$year = "";
$month = "";
$day = "";
$special_filter = "";
$bup_staff_calendar = "";
$nonce_check=false;
if(isset($_GET["_wpnonce"]))
{
    $nonce = $_REQUEST['_wpnonce'];

if ( ! wp_verify_nonce( $nonce, 'bup_appointment_filter' ) ) {
  exit; // Get out of here, the nonce is rotten!
}
else
$nonce_check=true;
}

if(isset($_GET["howmany"]))
{
    if($nonce_check==false)
    exit;
	$howmany = esc_attr($_GET["howmany"]);		
}else { //deepak
    $howmany=20;
}

if(isset($_GET["month"]))
{
    if($nonce_check==false)
    exit;
	$month = esc_attr($_GET["month"]);		
}

if(isset($_GET["day"]))
{
    if($nonce_check==false)
    exit;
	$day = esc_attr($_GET["day"]);		
}

if(isset($_GET["year"]))
{
    if($nonce_check==false)
    exit;
	$year = esc_attr($_GET["year"]);		
}

if(isset($_GET["special_filter"]))
{
    if($nonce_check==false)
    exit;
	$special_filter = esc_attr($_GET["special_filter"]);		
}
if(isset($_GET["bup-staff-calendar"]))
{
    if($nonce_check==false)
    exit;
	$bup_staff_calendar = esc_attr($_GET["bup-staff-calendar"]);		
}

$paged = (!empty($_GET['paged'])) ? esc_attr($_GET['paged']) : 1;	 // added by deepak



?>

         <h1 class="appointment"><?php esc_html_e('Appointments','booking-ultra-pro'); ?><?php //echo $bookingultrapro->appointment->total_result;?></h1>
       <div class="bup-sect welcome-panel">
        
        
        
        <span class="bup-add-appo"><a href="#" id="bup-create-new-app" title="<?php esc_html_e('Add New Appointment ','booking-ultra-pro'); ?>" class="page-title-action"><i class="fa fa-plus" aria-hidden="true"></i> <?php esc_html_e('Add New','booking-ultra-pro')?></a></span>
        
       
        <form action="" method="get">
         <input type="hidden" name="page" value="bookingultra-appointments" /> 
        
        <div class="bup-ultra-success bup-notification"><?php esc_html_e('Success ','booking-ultra-pro'); ?></div>
        
        
         <div class="bup-appointments-module-stats">
         
         	<ul>
            <li class="approved"><h3><?php esc_html_e('Approved','booking-ultra-pro')?></h3><p class="totalstats"><?php echo esc_attr($approved) ?></p></li>
             <li class="pending"><h3><?php esc_html_e('Pending','booking-ultra-pro')?></h3><p class="totalstats"><?php echo esc_attr($pending) ?></p></li>
                <li class="cancelled"><h3><?php esc_html_e('Cancelled','booking-ultra-pro')?></h3><p class="totalstats"><?php echo esc_attr($cancelled) ?></p></li>
                
                <li class="noshow"><h3><?php esc_html_e('No-Show','booking-ultra-pro')?></h3><p class="totalstats"><?php echo esc_attr($noshow) ?></p> </li>
                
                <li class="total"><h3><?php esc_html_e('Total','booking-ultra-pro')?></h3><p class="totalstats"><?php echo esc_attr($total_appo) ?></p></li>
            
            </ul>
         
         
         </div>
         
         <div class="bup-appointments-module-filters">
         
              <select name="month" id="month">
               <option value="" selected="selected"><?php esc_html_e('All Months','booking-ultra-pro'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=12){
			  ?>
               <option value="<?php echo esc_attr($i)?>"  <?php if($i==$month) echo 'selected="selected"';?>><?php echo esc_attr($i)?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="day" id="day">
               <option value="" selected="selected"><?php esc_html_e('All Days','booking-ultra-pro'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=31){
			  ?>
               <option value="<?php echo esc_attr($i)?>"  <?php if($i==$day) echo 'selected="selected"';?>><?php echo esc_attr($i)?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="year" id="year">
               <option value="" selected="selected"><?php esc_html_e('All Years','booking-ultra-pro'); ?></option>
               <?php
			  
			  $i = 2014;
              
			  while($i <= date('Y')){
			  ?>
               <option value="<?php echo esc_attr($i)?>" <?php if($i==$year) echo 'selected="selected"';?> ><?php echo esc_attr($i)?></option>
               <?php 
			    $i++;
			   }?>
             </select>
                
                        <?php if(isset($bupcomplement) && isset($bupultimate)){?>
            <select name="special_filter" id="special_filter">
               <option value="" selected="selected"><?php esc_html_e('All Locations','booking-ultra-pro'); ?></option>
               <?php
			  
			  $filters = $bup_filter->get_all();
              
			 foreach ( $filters as $filter )
				{
			  ?>
               <option value="<?php echo esc_attr($filter->filter_id)?>" <?php if($special_filter==$filter->filter_id) echo 'selected="selected"';?> ><?php echo esc_attr($filter->filter_name)?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
            <?php  }?>        
                       <?php echo $bookingultrapro->userpanel->get_staff_list_calendar_filter();?> 
                       
                       <select name="howmany" id="howmany">
               <option value="20" <?php if(20==$howmany ||$howmany =="" ) echo 'selected="selected"';?>>20 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                <option value="40" <?php if(40==$howmany ) echo 'selected="selected"';?>>40 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                 <option value="50" <?php if(50==$howmany ) echo 'selected="selected"';?>>50 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                  <option value="80" <?php if(80==$howmany ) echo 'selected="selected"';?>>80 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                   <option value="100" <?php if(100==$howmany ) echo 'selected="selected"';?>>100 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                   
                   <option value="150" <?php if(150==$howmany ) echo 'selected="selected"';?>>150 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                   
                    <option value="200" <?php if(200==$howmany ) echo 'selected="selected"';?>>200 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                    <option value="250" <?php if(250==$howmany ) echo 'selected="selected"';?>>250 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
                    
                    <option value="300" <?php if(300==$howmany ) echo 'selected="selected"';?>>300 <?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
               
          </select>
          
                       <button name="bup-btn-calendar-filter-appo" id="bup-btn-calendar-filter-appo" class="bup-button-submit-changes button"><?php esc_html_e('Filter','booking-ultra-pro')?>	</button>
                </div>  
                
                
            
        
                <?php wp_nonce_field( 'bup_appointment_filter' ); ?> 

         </form>
         
                 
         
         </div>
         
         
         <div class="bup-sect welcome-panel">
        
         <?php
$appointments = $bookingultrapro->appointment->get_all();//deepak
$total_filtered_appo=$bookingultrapro->appointment->get_total_of_filtered_appo();
							
				if (!empty($appointments)){
                    $total_pages = ceil($total_filtered_appo/$howmany); //deepak

                    echo escape_with_custom_html(paginate_links( array(
                        'base'         => @add_query_arg('paged','%#%'),
                        'total'        => $total_pages,
                        'current'      => $paged,
                        'show_all'     => false,
                        'end_size'     => 1,
                        'mid_size'     => 2,
                        'prev_next'    => true,
                        'prev_text'    => __('« Previous','booking-ultra-pro'),
                        'next_text'    => __('Next »','booking-ultra-pro'),
                        'type'         => 'plain',
                    ))); //deepak

				?>
       
           <table width="100%" class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <th  width="4%"><?php esc_html_e('#', 'booking-ultra-pro'); ?></th>
                    <th width="4%">&nbsp;</th>
                    
                     <th><?php esc_html_e('Date', 'booking-ultra-pro'); ?></th>
                     
                     <?php if(isset($bup_filter) && isset($bupultimate)){?>
                     
                      <th width="11%"><?php esc_html_e('Location', 'booking-ultra-pro'); ?></th>
                     
                     <?php	} ?>
                    
                    <th><?php esc_html_e('Client', 'booking-ultra-pro'); ?></th>
                    <th><?php esc_html_e('Phone Number', 'booking-ultra-pro'); ?></th>
                    <th><?php esc_html_e('Provider', 'booking-ultra-pro'); ?></th>
                     <th><?php esc_html_e('Service', 'booking-ultra-pro'); ?></th>
                    <th><?php esc_html_e('At', 'booking-ultra-pro'); ?></th>
                    
                     
                     <th><?php esc_html_e('Status', 'booking-ultra-pro'); ?></th>
                    <th><?php esc_html_e('Actions', 'booking-ultra-pro'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$staff_phone= '';
			$staff_display_name= '';
			foreach($appointments as $appointment) {
				$date_from=  date("Y-m-d", strtotime($appointment->booking_time_from));
				$booking_time = date($time_format, strtotime($appointment->booking_time_from ))	.' - '.date($time_format, strtotime($appointment->booking_time_to ));
				 
				$staff = $bookingultrapro->userpanel->get_staff_member($appointment->booking_staff_id);
				if($staff) {
					$staff_phone = !empty(get_user_meta( $staff->ID,'reg_telephone',true)) ? get_user_meta( $staff->ID,'reg_telephone',true) : '';
					$staff_display_name = !empty($staff->display_name) ? $staff->display_name : '';					
				}
				
				
				
				$client_id = $appointment->booking_user_id;				
				$client = get_user_by( 'id', $client_id );
				
				if(isset($appointment->filter_name))
				{
					$filter_name=$appointment->filter_name;
					
				}else{					
					
					if(isset($bupultimate)){ 
						$filter_id = $bookingultrapro->appointment->get_booking_meta($appointment->booking_id, 'filter_id');					
						$filter_n = $bookingultrapro->appointment->get_booking_location($filter_id);
						if($filter_n) {
							$filter_name=$filter_n->filter_name;	
						}					
					}
					
					
				}
				
				//get phone
			
				$phone = $bookingultrapro->appointment->get_booking_meta($appointment->booking_id, 'full_number');
				
				$comments = $bookingultrapro->appointment->get_booking_meta($appointment->booking_id, 'special_notes');
				$client_full_name = $bookingultrapro->appointment->get_booking_meta($appointment->booking_id, 'display_name'). " ". $bookingultrapro->appointment->get_booking_meta($appointment->booking_id, 'last_name');
				
					
			?>
              

                <tr>
                    <td><?php echo esc_attr($appointment->booking_id); ?></td>
                     <td>  <?php if($comments!=''){?><a href="#" class="bup-appointment-edit-module" appointment-id="<?php echo esc_attr($appointment->booking_id)?>" title="<?php esc_html_e('See Details','booking-ultra-pro'); ?>"><i class="fa fa-envelope-o"></i></a> <?php }?></td>
                   
                     <td><?php echo  wp_kses_post(date($date_format, strtotime($date_from))); ?>      </td> 
                     
                      <?php if(isset($bup_filter) && isset($bupultimate)){?>
                      
                      <td><?php echo esc_attr($filter_name); ?> </td>
                       <?php	} ?>
                      
                    <td><?php echo esc_attr($client_full_name); ?> (<?php echo esc_attr($client->user_email); ?>)</td>
                    <td><?php echo esc_attr($phone); ?></td>
                    <td><?php echo esc_attr($staff_display_name); ?></td>
                    <td><?php echo esc_attr($appointment->service_title); ?> </td>
                    <td><?php echo $booking_time; ?></td>                  
                     
                      <td><?php echo $bookingultrapro->appointment->get_status_legend($appointment->booking_status); ?></td>
                   <td class="bup-action-holder"> <a href="#" class="bup-appointment-edit-module page-title-action" appointment-id="<?php echo esc_attr($appointment->booking_id)?>" title="<?php esc_html_e('Edit','booking-ultra-pro'); ?>"><i class="fa fa-edit"></i></a><a href="#" class="bup-appointment-delete-module page-title-action" appointment-id="<?php echo esc_attr($appointment->booking_id)?>" title="<?php esc_html_e('Delete','booking-ultra-pro'); ?>"><i class="fa fa-trash-o"></i></a></td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php esc_html_e('There are no appointments yet.','booking-ultra-pro'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
        </div>
        
           
    <div id="bup-spinner" class="bup-spinner" style="display:">
            <span> <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo esc_html__('Please wait ...','booking-ultra-pro')?>
	</div>
        
         <div id="bup-appointment-new-box" title="<?php esc_html_e('Create New Appointment','booking-ultra-pro')?>"></div>
     <div id="bup-appointment-edit-box" title="<?php esc_html_e('Edit Appointment','booking-ultra-pro')?>"></div>     
     <div id="bup-new-app-conf-message" title="<?php esc_html_e('Appointment Created','booking-ultra-pro')?>"></div> 
     <div id="bup-new-payment-cont" title="<?php esc_html_e('Add Payment','booking-ultra-pro')?>"></div>
     <div id="bup-confirmation-cont" title="<?php esc_html_e('Confirmation','booking-ultra-pro')?>"></div>
     <div id="bup-new-note-cont" title="<?php esc_html_e('Add Note','booking-ultra-pro')?>"></div>     
     <div id="bup-appointment-list" title="<?php esc_html_e('Pending Appointments','booking-ultra-pro')?>"></div>
      <div id="bup-appointment-change-status" title="<?php esc_html_e('Appointment Status','booking-ultra-pro')?>"></div>
      
      <div id="bup-client-new-box" title="<?php esc_html_e('Create New Client','booking-ultra-pro')?>"></div>
               
     <script type="text/javascript">
	
			var err_message_payment_date ="<?php esc_html_e('Please select a payment date.','booking-ultra-pro'); ?>";
			var err_message_payment_amount="<?php esc_html_e('Please input an amount','booking-ultra-pro'); ?>"; 
			var err_message_payment_delete="<?php esc_html_e('Are you totally sure that you want to delete this payment?','booking-ultra-pro'); ?>"; 
			
			var err_message_note_title ="<?php esc_html_e('Please input a title.','booking-ultra-pro'); ?>";
			var err_message_note_text="<?php esc_html_e('Please input some text','booking-ultra-pro'); ?>";
			var err_message_note_delete="<?php esc_html_e('Are you totally sure that you want to delete this note?','booking-ultra-pro'); ?>"; 
			
			
			var gen_message_rescheduled_conf="<?php esc_html_e('The appointment has been rescheduled.','booking-ultra-pro'); ?>"; 
			var gen_message_infoupdate_conf="<?php esc_html_e('The information has been updated.','booking-ultra-pro'); ?>"; 
	
		     var err_message_start_date ="<?php esc_html_e('Please select a date.','booking-ultra-pro'); ?>";
			 var err_message_service ="<?php  esc_html_e('Please select a service.','booking-ultra-pro'); ?>"; 
		     var err_message_time_slot ="<?php esc_html_e('Please select a time.','booking-ultra-pro'); ?>";
			 var err_message_client ="<?php esc_html_e('Please select a client.','booking-ultra-pro'); ?>";
			 var message_wait_availability ='<img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo esc_html__("Please wait ...","bookingup")?>'; 
			 
			 jQuery("#bup-spinner").hide();	
			  
		
	</script>
 
        
