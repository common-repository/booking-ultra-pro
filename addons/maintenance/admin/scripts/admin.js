jQuery(document).ready(function($) {
	
		
	jQuery(document).on("click", "#bup_clean_app_without_service", function(e) {
		
		e.preventDefault();	
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "bup_clean_appo_without_service", _ajax_nonce: bup_admin_v98.nonce, },
						
						success: function(data){
							
							window.location.reload();
								
							
							}
					});
			
	});
	
	jQuery(document).on("click", "#bup_clean_app_without_staff", function(e) {
		
		e.preventDefault();	
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "bup_clean_appo_without_staff",_ajax_nonce: bup_admin_v98.nonce, },
						
						success: function(data){
							
							window.location.reload();
								
							
							}
					});
			
	});
	
	
	
});