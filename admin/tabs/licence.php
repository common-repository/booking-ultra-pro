<?php
global $bookingultrapro, $bupultimate;

//

$va = get_option('bup_c_key');

///echo "licence ".$va;
$domain = $_SERVER['SERVER_NAME'];

?>

<h1><?php esc_html_e('Recommendation!','booking-ultra-pro'); ?></h1>
 <div class="bup-sect bup-welcome-panel ">
 
 <?php if($va=='' && isset($bupultimate)){ //user is running either professional or utlimate?>
 
  
   <p><?php esc_html_e("You're running either Professional or Ultimate version which doesn't require a serial number to each one of your websites. However, if you don't create a serial number for this domain :",'booking-ultra-pro'); ?><strong> <?php echo esc_url($domain) ; ?></strong>, <?php esc_html_e(" you won't be able to update the plugin automatically through the WP Update Section. So.. we highly recommend you creating a serial number for your domain.",'booking-ultra-pro'); ?></p>

  <?php }?>
  
  
  <?php if($va!='' && ( isset($bupultimate) || isset($bupcomplement) )  ){ //user is running a validated copy?>
  
  <h3><?php esc_html_e('Congratulations!','booking-ultra-pro'); ?></h3>
   <p><?php esc_html_e("Your copy has been validated. You should be able to update the plugin through your WP Update sections. Also, you should start receiving an notice every time the plugin is updated.",'booking-ultra-pro'); ?></p>
<p class="submit">
    <input style="background: #d63638; color: #fff; border-color: #d63638;" type="submit" name="submit" id="bupadmin-btn-deactivate-copy" class="button button-danger"  value="<?php esc_html_e('CLICK HERE TO DEACTIVATE YOUR COPY','booking-ultra-pro'); ?>"  /> &nbsp; <span id="loading-animation">  <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; <?php esc_html_e('Please wait ...','booking-ultra-pro'); ?></span>
</p>

   <?php }else{?>  
   
        
        <?php if($va=='' && isset($bupcomplement)){ //user is running either professional or utlimate?>    
   
       
            <h3><?php esc_html_e('Validate your copy','booking-ultra-pro'); ?></h3>
            <p><?php esc_html_e("Please fill out the form below with the serial number generated when you registered your domain through your account at BookingUltraPro.com",'booking-ultra-pro'); ?>. <a href="http://doc.bookingultrapro.com/installing-booking-ultra-pro/" target="_blank"><?php esc_html_e('Click here to create your serial number','booking-ultra-pro'); ?></a></p> 
            
            <p> <?php esc_html_e('INPUT YOUR SERIAL KEY','booking-ultra-pro'); ?></p>
             <p><input type="text" name="p_serial" id="p_serial" style="width:200px" /></p>
            
            
            <p class="submit">
        <input type="submit" name="submit" id="bupadmin-btn-validate-copy" class="button button-primary " value="<?php esc_html_e('CLICK HERE TO VALIDATE YOUR COPY','booking-ultra-pro'); ?>"  /> &nbsp; <span id="loading-animation">  <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; <?php esc_html_e('Please wait ...','booking-ultra-pro'); ?> </span>
        
           </p>
       
       <?php }else{?>
     
       <h3><?php esc_html_e('Validating your Plugin','booking-ultra-pro'); ?></h3>
     <p><?php esc_html_e("In order to validate the plugin you will need to purchase a licence on BookingUltraPro.com",'booking-ultra-pro'); ?>. <a href="https://bookingultrapro.com/#pricing-section" target="_blank"><?php esc_html_e('Click here to purchase a serial number','booking-ultra-pro'); ?></a></p>

     <p> <?php esc_html_e('INPUT YOUR SERIAL KEY','booking-ultra-pro'); ?></p>
             <p><input type="text" name="p_serial" id="p_serial" style="width:200px" /></p>
            
            
            <p class="submit">
        <input type="submit" name="submit" id="bupadmin-btn-validate-copy" class="button button-primary " value="<?php esc_html_e('CLICK HERE TO VALIDATE YOUR COPY','booking-ultra-pro'); ?>"  /> &nbsp; <span id="loading-animation">  <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; <?php esc_html_e('Please wait ...','booking-ultra-pro'); ?> </span>
        
           </p>
     
        <?php }?>
       
   <?php }?> 
       
       <p id='bup-vaalidation-results'>
       
       </p>
                     
       
    
</div>  

