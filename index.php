<?php
/*
Plugin Name: Booking Ultra Pro CORE
Plugin URI: http://bookingultrapro.com
Description: Booking Plugin for every service provider: dentists, medical services, hair & beauty salons, repair services, event planners, rental agencies, educational services, government agencies, school counsellors and more. This plugin allows you to manage your appointments easily.
Tested up to: 6.6.1
Version: 1.1.16
Author: Booking Ultra Pro
Domain Path: /languages
Text Domain: booking-ultra-pro
Author URI: https://bookingultrapro.com/

*/
define('BOOKINGUP_URL',plugin_dir_url(__FILE__ ));
define('BOOKINGUP_PATH',plugin_dir_path(__FILE__ ));
define('MY_PLUGIN_SETTINGS_URL',admin_url( 'admin.php?page=bookingultra'));

$plugin = plugin_basename(__FILE__);

/* Loading Function */
require_once (BOOKINGUP_PATH . 'functions/functions.php');

/* Init */
define('BUP_PRO_URL','https://bookingultrapro.com/');

function bup_load_textdomain() 
{     	   
	   $locale = apply_filters( 'plugin_locale', get_locale(), 'booking-ultra-pro' );	   
       $mofile = BOOKINGUP_PATH . "languages/booking-ultra-pro-$locale.mo";
			
		// Global + Frontend Locale
		load_textdomain( 'booking-ultra-pro', $mofile );
		load_plugin_textdomain( 'booking-ultra-pro', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}

/* Load plugin text domain (localization) */
add_action('init', 'bup_load_textdomain');	
		
add_action('init', 'bup_output_buffer');
function bup_output_buffer() {
		ob_start();
}



/* Master Class  */
require_once (BOOKINGUP_PATH . 'classes/bookingultra.class.php');

// Helper to activate a plugin on another site without causing a fatal error by
register_activation_hook( __FILE__, 'bupro_activation');
 
function  bupro_activation( $network_wide ) 
{
	$plugin_path = '';
	$plugin = "booking-ultra-pro/index.php";	
	
	if ( is_multisite() && $network_wide ) // See if being activated on the entire network or one blog
	{ 
		activate_plugin($plugin_path,NULL,true);
			
		
	} else { // Running on a single blog		   	
			
		activate_plugin($plugin_path,NULL,false);		
		
	}
}

$bookingultrapro = new BookingUltraPro();
$bookingultrapro->plugin_init();

register_activation_hook(__FILE__, 'bup_my_plugin_activate');
add_action('admin_init', 'bup_my_plugin_redirect');

function bup_my_plugin_activate() 
{

    require_once (BOOKINGUP_PATH . 'init/install.php');
    add_option('bup_plugin_do_activation_redirect', true);
}

function bup_my_plugin_redirect() 
{	
	if(!get_option('bup_dismiss_notice') && isset($_REQUEST['page']) && $_REQUEST['page'] == 'bookingultra') {
		add_action( 'admin_notices', 'bup_admin_notice_for_appointment_page' );	
	}
	
    if (get_option('bup_plugin_do_activation_redirect', false)) {
        delete_option('bup_plugin_do_activation_redirect');
        wp_redirect(MY_PLUGIN_SETTINGS_URL);
        exit;
    }
    else if(isset($_REQUEST['bup-dismiss-notice']) && !get_option('bup_dismiss_notice')) {
		update_option('bup_dismiss_notice',true);
		wp_redirect( bup_get_current_admin_url() );
        exit;
	}
}

function bup_admin_notice_for_appointment_page() {
	
	//$page_link = get_permalink(get_page_by_title( 'Appointment', 'OBJECT', 'page'));
	$pages = get_posts( [
		'title'     => 'Appointment',
		'post_type' => 'page',
	] );
 $objPage= get_post( $pages[0]->ID, 'OBJECT');
 $page_link= get_permalink($objPage);
    ?>
    
        <div class="notice notice-success">
        	<div class="bup-notice-holder">
        		<p><i class="fa fa-info-circle" aria-hidden="true"></i> <?php esc_html_e( 'We have added a new page "Appointment" to your site to show the appointment form', 'booking-ultra-pro'); ?>  <a href="<?php echo esc_url($page_link); ?>" target="_blank"> <?php esc_html_e( 'View Page', 'booking-ultra-pro') ?></a></p><a href="<?php echo esc_attr_e(bup_get_current_admin_url( array("bup-dismiss-notice" => '1'))); ?>"><span class="dashicons dashicons-dismiss"></span></a>	
        	</div>
    	</div>
    
    <?php
}



require_once BOOKINGUP_PATH . 'addons/maintenance/index.php';