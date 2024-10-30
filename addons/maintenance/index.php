<?php
global $bookingultrapro;

define('BUP_MAINTENANCE_URL',plugin_dir_url(__FILE__ ));
define('BUP_MAINTENANCE_PATH',plugin_dir_path(__FILE__ ));



	/* functions */
	foreach (glob(BUP_MAINTENANCE_PATH . 'functions/*.php') as $filename) { require_once $filename; }
	
	/* administration */
	if (is_admin()){
		foreach (glob(BUP_MAINTENANCE_PATH . 'admin/*.php') as $filename) { include $filename; }
	}
	
