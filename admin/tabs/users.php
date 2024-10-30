<?php
global $bookingultrapro, $bupcomplement;

$howmany = "20";
$year = "";
$month = "";
$day = "";
$status = "";
$avatar = "";
$edit = "";

if(isset($_GET["avatar"]) && $_GET["avatar"]!=''){
	
	$avatar = esc_attr($_GET["avatar"]);
}

if(isset($_GET["edit"]) && $_GET["edit"]!=''){
	
	$edit = esc_attr($_GET["edit"]);
}

$load_staff_id = $bookingultrapro->userpanel->get_first_staff_on_list();

if(isset($_GET["ui"]) && $_GET["ui"]!=''){
	
	$load_staff_id=esc_attr($_GET["ui"]);
}
if(isset($_GET["code"]) && $_GET["code"] !='' && isset($bupcomplement->googlecalendar))
{
	if(session_id() == '') { 
		session_start();	
	}
	
	$current_staff_id =$_SESSION["current_staff_id"] ;
	//echo "Google Calendar Linked Staff ID :" . $current_staff_id;
		
	if($current_staff_id!='')
	{				
		//google calendar.	
		$client = $bupcomplement->googlecalendar->auth_client_with_code(esc_attr($_GET["code"]), $current_staff_id);	
		$load_staff_id=$current_staff_id;
	
	}

}else{
	
	if(session_id() == '') { 
		session_start();	
	}
	
	$_SESSION["current_staff_id"] = $load_staff_id;
	//$_SESSION["current_staff_id"] = NULL;
}

?>



     
        <div class="bup-sect ">
        
        <div class="bup-staff ">
        
        	
            
            
             <?php if($avatar==''){?>	
             
             
                 <div class="bup-staff-left " id="bup-staff-list">
            	
                          
            	
            	 </div>
                 
                 <div class="bup-staff-right " id="bup-staff-details">
                 </div>
            
            <?php }else{ //upload avatar?>
            
           <?php  
		   if(isset( $_POST['crop_image'])){
		   $crop_image = $_POST['crop_image'];
		   }else{
			$crop_image =null;
		   }

		   if( $crop_image=='crop_image') //displays image cropper
			{
			
			 $image_to_crop = $_POST['image_to_crop'];
			 
			
			 ?>
             
             <div class="bup-staff-right-avatar " >
           		  <div class="pr_tipb_be">
                              
                            <?php echo $bookingultrapro->userpanel->display_avatar_image_to_crop($image_to_crop, $avatar);?>                          
                              
                   </div>
                   
             </div>
            
           
		    <?php }else{  
			
			$user = get_user_by( 'id', $avatar );
			?> 
            
            <div class="bup-staff-right-avatar " >
            
           
                   <div class="bup-avatar-drag-drop-sector"  id="bup-drag-avatar-section">
                   
                   <h3> <?php echo esc_html($user->display_name)?><?php esc_html_e("'s Picture",'booking-ultra-pro')?></h3>
                        
                             <?php echo $bookingultrapro->userpanel->get_user_pic( $avatar, 80, 'avatar', 'rounded', 'dynamic')?>

                                                    
                             <div class="uu-upload-avatar-sect">
                              
                                     <?php echo $bookingultrapro->userpanel->avatar_uploader($avatar)?>  
                              
                             </div>
                             
                        </div>  
                    
             </div>
             
             
              <?php }  ?>
            
             <?php }?>
        
        	
        </div>        
        </div>
        
        <div id="bup-breaks-new-box" title="<?php esc_html_e('Add Breaks','booking-ultra-pro')?>"></div>
        
        <div id="bup-spinner" class="bup-spinner" style="display:">
            <span> <img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo esc_html__('Please wait ...','booking-ultra-pro')?>
	</div>
        
         <div id="bup-staff-editor-box"></div>
        
  

 <script type="text/javascript">
	
			
			 var message_wait_availability ='<img src="<?php echo esc_url(BOOKINGUP_URL)?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo esc_html__("Please wait ...","bookingup")?>'; 
			 
			 jQuery("#bup-spinner").hide();		 
			  
			  
			  
			  <?php if($avatar==''){?>	
		
			   bup_load_staff_list_adm();
			   
				   <?php if($load_staff_id!=''){?>		  
				  
					setTimeout("bup_load_staff_details(<?php echo esc_attr($load_staff_id)?>)", 1000);
				  
				  <?php }?>
			  
			   <?php }?>	
				  
			  
		
	</script>
