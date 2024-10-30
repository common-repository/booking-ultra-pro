<?php
global $bookingultrapro;
		
?>

        <h1><?php esc_html_e('Services','booking-ultra-pro'); ?></h1>
        <div class="bup-sect welcome-panel"> 
        
                
           <div class="bup-services">
           
           		<div class="bup-categories" id="bup-categories-list">
                
                 
                 
                                 
                </div>
                
                <div class="bup-services" id="bup-services-list">
                
                 
                
                
                </div>
           
               
           
           </div>
       
       
         
        
        </div>
        
        <div id="bup-service-editor-box"></div>
        <div id="bup-service-add-box"></div>
        <div id="bup-service-variable-pricing-box"  title="<?php echo esc_html__('Set Flexible Pricing','booking-ultra-pro')?>"></div>
        <div id="bup-service-add-category-box" title="<?php echo esc_html__('Add Category','booking-ultra-pro')?>"></div>
        
        
         <script type="text/javascript">
		 
			 var err_message_category_name ="<?php esc_html_e('Please input a name.','booking-ultra-pro'); ?>";  
		   		 
			bup_load_categories();
			bup_load_services();
		 </script>
<div id="bup-spinner" class="bup-spinner" style="display:">
            <span> <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo esc_html__('Please wait ...','booking-ultra-pro')?>
	</div>