<?php
global $bookingultrapro, $bupcomplement, $bupultimate, $bup_filter, $wp_locale;

$how_many_upcoming_app = 5;


$currency_symbol =  $bookingultrapro->get_option('paid_membership_symbol');
$date_format =  $bookingultrapro->get_int_date_format();
$time_format =  $bookingultrapro->service->get_time_format();

//today
$today = $bookingultrapro->appointment->get_appointments_planing_total('today');
$tomorrow = $bookingultrapro->appointment->get_appointments_planing_total('tomorrow');
$week = $bookingultrapro->appointment->get_appointments_planing_total('week');

$pending = $bookingultrapro->appointment->get_appointments_total_by_status(0);
$cancelled = $bookingultrapro->appointment->get_appointments_total_by_status(2);
$noshow = $bookingultrapro->appointment->get_appointments_total_by_status(3);
$unpaid = $bookingultrapro->order->get_orders_by_status('pending');


$va = get_option('bup_c_key');
                
if($va==''  && isset($bupultimate)){                    
    $this->display_ultimate_validate_copy();
}

$upcoming_appointments = $bookingultrapro->appointment->get_upcoming_appointments($how_many_upcoming_app);

?>
<div class="wrap bup-wrap">
    <h1><?php esc_html_e('Booking Ultra Dashboard','booking-ultra-pro'); ?></h1>
<div class="welcome-panel">
    

    <div class="welcome-panel-content">

        <div><h1><strong><?php esc_html_e( 'Dashboard', 'booking-ultra-pro' ); ?></strong></h1></div>
        <div class="buprodash-main-sales-summary " id="bup-main-cont-home-111" >
        
       
        
        <!--Col1-->
       <div class="bupro-main-dashcol-2" > 
           
           <div class="bupro-main-tool-bar" >
               
               <ul>           
                   <li class="newappo page-title-action"><a id="bup-doc" href="?page=bookingultra-help"><span><i class="fa fa-book"></i></span><?php esc_html_e('Documentation','booking-ultra-pro')?></a> </li>
                   
                    <li class="newappo page-title-action"><a id="bup-create-new-app" href="#"><span><i class="fa fa-plus"></i></span><?php esc_html_e('New Appointment','booking-ultra-pro')?></a> </li>
                   
               </ul>
               
           </div>
            
             <div class="bupro-main-quick-summary" >
             
            
          
               <ul>
                   <li>                    
                     
                      <p style="color: #3C0"> <?php echo esc_attr($today)?></p>  
                       <small><?php esc_html_e('Today','booking-ultra-pro')?> </small>                  
                    </li>
                    
                    <li>                   
                     
                      <p style="color:"> <?php echo esc_attr($tomorrow)?></p> 
                       <small><?php esc_html_e('Tomorrow','booking-ultra-pro')?> </small>                   
                    </li>
                
                    <li>                   
                     
                      <p style="color:"> <?php echo esc_attr($week)?></p> 
                       <small><?php esc_html_e('This Week','booking-ultra-pro')?> </small>                   
                    </li>
                   
                    <li><a href="#"  class="bup-adm-see-appoint-list-quick" bup-status='0' bup-type='bystatus'>                    
                          <small><?php esc_html_e('Pending','booking-ultra-pro')?> </small>
                          <p style="color: #333"> <?php echo esc_attr($pending)?></p>   </a>                
                    </li>
                   
                     <li>     
                        
                         <a href="#" class="bup-adm-see-appoint-list-quick" bup-status='3' bup-type='byunpaid'>              
                          <small><?php esc_html_e('Unpaid','booking-ultra-pro')?> </small>
                          <p style="color: #F90000"> <?php echo esc_attr($unpaid)?></p> 
                          
                           </a>                     
                   </li>
                   
                   
              </ul>
              
            </div>
            
          
            
          
          </div>
        <!-- End Col1-->
            
            
        
         <div class="bupro-main-dashcol-1" >
             <div id='easywpm-gcharthome' style="width: 100%; height: 180px;">
             </div>
        </div>
        
        
        
        </div>
        
    </div>

</div>    

<div id="dashboard-widgets-wrap">
   <div class="metabox-holder" id="dashboard-widgets">
      <div class="postbox-container">
         <div class="meta-box-sortables ui-sortable" >
            <div class="postbox " id="">
               <div class="postbox-header">
                  <h2 class="hndle ui-sortable-handle">
                     <?php esc_html_e('Upcoming Appointments','booking-ultra-pro') ?>
                  </h2>
                  <div class="handle-actions hide-if-no-js"> 
                    
                     <button aria-expanded="true" class="handlediv" type="button">
                        <span class="screen-reader-text">
                           Toggle panel: At a Glance
                        </span>
                        <span aria-hidden="true" class="toggle-indicator bup-widget-home-colapsable" widget-id="1">
                        </span>
                     </button>
                  </div>
               </div>
               <div class="inside">
                  <div class="main" id="bup-main-cont-home-1">
                    <?php
                        if (!empty($upcoming_appointments)){
                
                
                ?>
       
           <table width="100%" class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                  
                    
                     <th width="13%"><?php esc_html_e('Date', 'booking-ultra-pro'); ?></th>
                     
                     <?php if(isset($bup_filter) && isset($bupultimate)){?>
                     
                      <th width="11%"><?php esc_html_e('Location', 'booking-ultra-pro'); ?></th>
                     
                     <?php  } ?>
                    
                    <th width="23%"><?php esc_html_e('Client', 'booking-ultra-pro'); ?></th>
                    <th width="23%"><?php esc_html_e('Phone Number', 'booking-ultra-pro'); ?></th>
                    <th width="23%"><?php esc_html_e('Provider', 'booking-ultra-pro'); ?></th>
                     <th width="18%"><?php esc_html_e('Service', 'booking-ultra-pro'); ?></th>
                    <th width="16%"><?php esc_html_e('At', 'booking-ultra-pro'); ?></th>
                    
                     
                     <th width="9%"><?php esc_html_e('Status', 'booking-ultra-pro'); ?></th>
                    <th width="9%"><?php esc_html_e('Actions', 'booking-ultra-pro'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
            $filter_name= '';
            $staff_display_name= '';
            $phone= '';
            foreach($upcoming_appointments as $appointment) {
                
                
                $date_from=  date("Y-m-d", strtotime($appointment->booking_time_from));
                $booking_time = date($time_format, strtotime($appointment->booking_time_from )) .' - '.date($time_format, strtotime($appointment->booking_time_to ));
                 
                $staff = $bookingultrapro->userpanel->get_staff_member($appointment->booking_staff_id);
                if(!empty($staff)) {
                  $staff_display_name= $staff->display_name;  
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
        
                
                    
            ?>
              

                <tr>
                   
                   
                     <td><?php echo  date($date_format, strtotime($date_from)); ?>      </td> 
                     
                      <?php if(isset($bup_filter) && isset($bupultimate)){?>
                      
                      <td><?php echo esc_attr($filter_name); ?> </td>
                       <?php    } ?>
                      
                    <td><?php echo esc_attr($client->display_name); ?> (<?php echo esc_attr($client->user_email); ?>)</td>
                    <td><?php echo esc_attr($phone); ?></td>
                    <td><?php echo esc_attr($staff_display_name); ?></td>
                    <td><?php echo esc_attr($appointment->service_title); ?> </td>
                    <td><?php echo  $booking_time; ?></td>                  
                     
                      <td id="bup-td-status"><?php echo $bookingultrapro->appointment->get_status_legend($appointment->booking_status); ?></td>
                   <td> <a href="#" class="bup-appointment-edit-module page-title-action" appointment-id="<?php echo esc_attr($appointment->booking_id)?>" title="<?php esc_html_e('Edit','booking-ultra-pro'); ?>"><i class="fa fa-edit"></i></a></td>
                </tr>
                
                
                <?php
                    }
                    
                    } else {
            ?>
            <p><?php esc_html_e('There are no appointments yet.','booking-ultra-pro'); ?></p>
            <?php   } ?>

            </tbody>
        </table>
                  </div>
               </div>
            </div>           
         </div>
      </div>

<div class="postbox-container">
         <div class="meta-box-sortables ui-sortable message-box">
            <?php if(!isset($bupcomplement)){?>

<div id="message" class="updated below-h2"><p> ⭐ <?php esc_html_e('Do you need more features or manage multiple locations, google calendar integration, SMS reminders, change legends & colors? Check out ','booking-ultra-pro')?> <a style="text-decoration: none" href="https://bookingultrapro.com/compare-packages/" target="_blank">Booking Ultra Paid Version</a></p></div>
<?php }?>
            </div>
        </div>
      <div class="postbox-container calender-holder">
         <div class="meta-box-sortables ui-sortable" >
            <div class="postbox" id="">
               <div class="postbox-header">
                  <h2 class="hndle ui-sortable-handle">
                     <?php esc_html_e('Calendar','booking-ultra-pro') ?>
                  </h2>
                  <div class="handle-actions hide-if-no-js"> 
                    
                     <button aria-expanded="true" class="handlediv" type="button">
                       
                        <span aria-hidden="true" class="toggle-indicator bup-widget-home-colapsable" widget-id="2">
                        </span>
                     </button>
                  </div>
               </div>
               <div class="inside">
                  <div class="main" id="bup-main-cont-home-2">
                               
            <div id="full_calendar_wrapper">     
            
            <?php if(isset($bupcomplement) && isset($bupultimate)){?>
            
                <div class="bup-calendar-filters">
                
                       <?php echo $bup_filter->get_all_calendar_filter();?>          
                       <?php echo $bookingultrapro->userpanel->get_staff_list_calendar_filter();?> 
                       <button name="bup-btn-calendar-filter" id="bup-btn-calendar-filter" class="bup-button-submit-changes"><?php esc_html_e('Filter','booking-ultra-pro')?>   </button>
                </div>  
            
            <?php }?>    
                
             <?php 
                ///staff filters
                if(isset($bupcomplement) && isset($bupultimate)){?>
            
                <div class="bup-calendar-staff-bar-filter">
                
                                
                       <?php //echo $bookingultrapro->userpanel->get_staff_list_calendar_bar();?> 
                </div>  
            
            <?php }?>    
                
                
                <div class="table-responsive">
                    

                        <div class="ab-loading-inner" style="display: none">
                            <span class="ab-loader"></span>
                        </div>
                        <div class="bup-calendar-element"></div>
                </div>  
                
            </div>  
      
                  </div>
               </div>
            </div>           
         </div>
      </div>

   </div>   
</div>




       
        
     <div id="bup-appointment-new-box" title="<?php esc_html_e('Create New Appointment','booking-ultra-pro')?>"></div>
     <div id="bup-appointment-edit-box" title="<?php esc_html_e('Edit Appointment','booking-ultra-pro')?>"></div>     
     <div id="bup-new-app-conf-message" title="<?php esc_html_e('Appointment Created','booking-ultra-pro')?>"></div> 
     <div id="bup-new-payment-cont" title="<?php esc_html_e('Add Payment','booking-ultra-pro')?>"></div>
     <div id="bup-confirmation-cont" title="<?php esc_html_e('Confirmation','booking-ultra-pro')?>"></div>
     <div id="bup-new-note-cont" title="<?php esc_html_e('Add Note','booking-ultra-pro')?>"></div>     
     <div id="bup-appointment-list" title="<?php esc_html_e('Pending Appointments','booking-ultra-pro')?>"></div>
     
     <div id="bup-client-new-box" title="<?php esc_html_e('Create New Client','booking-ultra-pro')?>"></div>
           <div id="bup-appointment-change-status" title="<?php esc_html_e('Appointment Status','booking-ultra-pro')?>"></div>

     
     
       
    
    <div id="bup-spinner" class="bup-spinner" style="display:">
            <span> <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo esc_html('Please wait ...','booking-ultra-pro')?>
    </div>
    
</div>   
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
             var err_message_service ="<?php esc_html_e('Please select a service.','booking-ultra-pro'); ?>"; 
             var err_message_time_slot ="<?php esc_html_e('Please select a time.','booking-ultra-pro'); ?>";
             var err_message_client ="<?php esc_html_e('Please select a client.','booking-ultra-pro'); ?>";
             var message_wait_availability ='<img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo esc_html("Please wait ...","bookingup")?>'; 
              
        
    </script>
    
    <?php

$sales_val= $bookingultrapro->appointment->get_graph_total_monthly();
$months_array = array_values( $wp_locale->month );
$current_month = date("m");
$current_month_legend = $months_array[$current_month -1];

?>

<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
          
        var data = google.visualization.arrayToDataTable([
          ["<?php esc_html_e('Day','booking-ultra-pro')?>", "<?php esc_html_e('Bookings','booking-ultra-pro')?>"],
         <?php echo $sales_val?>
        ]);

        var options = {
        
          hAxis: {title: '<?php 
	// Translators: %s will be replaced with the name of the current month or month legend. This string is used to display the current month.

	printf(esc_html( 'Month: %s', 'booking-ultra-pro' ),
    $current_month_legend);?> ',  titleTextStyle: {color: '#333'},  textStyle: {fontSize: '9'}},
          vAxis: {minValue: 0},      
          legend: { position: "none" }
        };

        var chart_1 = new google.visualization.AreaChart(document.getElementById('easywpm-gcharthome'));
        chart_1.draw(data, options);
        
                
        
        
        
      }
    </script>


     
