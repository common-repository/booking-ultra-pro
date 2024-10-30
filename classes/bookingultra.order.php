<?php
class BookingUltraOrder 
{
	var $pages;
	var $total_result;

	function __construct() 
	{
		$this->ini_db();		

	}
	
	public function ini_db()
	{
		global $wpdb;			

		// Create table
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'bup_orders (
				`order_id` bigint(20) NOT NULL auto_increment,				
				`order_booking_id` int(11) NOT NULL,
				`order_method_name`  varchar(60) NOT NULL,				
				`order_key` varchar(250) NOT NULL,
				`order_txt_id` varchar(60) NOT NULL,
				`order_status` varchar(60) NOT NULL,
				`order_amount` decimal(11,2) NOT NULL,
				`order_qty` int(11) NOT NULL DEFAULT "1",
				`order_date` date NOT NULL,									 			
				PRIMARY KEY (`order_id`)
			) COLLATE utf8_general_ci;';
	
	
		$wpdb->query( $query );	
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'bup_bookings (
				`booking_id` bigint(20) NOT NULL auto_increment,
				`booking_user_id` int(11) NOT NULL,
				`booking_service_id` int(11) NOT NULL,
				`booking_staff_id` int(11) NOT NULL,
				`booking_cart_id` int(11) NOT NULL DEFAULT "0",
				`booking_template_id` int(1) NOT NULL DEFAULT "0",
				`booking_date` date NOT NULL,					
				`booking_time_from` datetime NOT NULL,	
				`booking_time_to` datetime NOT NULL,	
				`booking_time_offset` int(11) NOT NULL DEFAULT "0",	
				`booking_status` int(1) NOT NULL DEFAULT "0",
				`booking_qty` int(11) NOT NULL DEFAULT "1",					
				`booking_qty_2` int(11) NOT NULL DEFAULT "0",	
				`booking_amount` decimal(11,2) NOT NULL,
				`booking_key` varchar(250) NOT NULL,					 			
				PRIMARY KEY (`booking_id`)
			) COLLATE utf8_general_ci;';
	
	
		$wpdb->query( $query );	
		
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'bup_bookings_meta (
				`meta_id` bigint(20) NOT NULL auto_increment,
				`meta_booking_id` int(11) NOT NULL,				
				`meta_booking_name` varchar(300) NOT NULL,
				`meta_booking_value` longtext,					 			
				PRIMARY KEY (`meta_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'bup_carts (
				`cart_id` bigint(20) NOT NULL auto_increment,
				`cart_key` varchar(250) NOT NULL,
				`cart_date` date NOT NULL,
				`cart_amount` decimal(11,2) NOT NULL,	
				`cart_status` int(1) NOT NULL DEFAULT "0",			 			
				PRIMARY KEY (`cart_id`),
				UNIQUE KEY `cart_key` (`cart_key`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );		
		
		$this->update_table();
		
	}
	
	
	function update_table()
	{
		global $wpdb;
		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'bup_bookings where field="booking_qty" ';		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'bup_bookings add column booking_qty int (11) default 1 ; ';
			$wpdb->query($sql);
		}
		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'bup_bookings where field="booking_qty_2" ';		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'bup_bookings add column booking_qty_2 int (11) default 0 ; ';
			$wpdb->query($sql);
		}
		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'bup_bookings where field="booking_time_offset" ';		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'bup_bookings add column booking_time_offset int (11) default 0 ; ';
			$wpdb->query($sql);
		}	
		
		
		
		
		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'bup_bookings where field="booking_template_id" ';		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'bup_bookings add column booking_template_id int (11) default 0 ; ';
			$wpdb->query($sql);
		}
		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'bup_bookings where field="booking_cart_id" ';		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'bup_bookings add column booking_cart_id int (11) default 0 ; ';
			$wpdb->query($sql);
		}
		
		
		
		
		$sql ='SHOW columns from ' . $wpdb->prefix . 'bup_orders where field="order_qty" ';		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'bup_orders add column order_qty int (11) default 1 ; ';
			$wpdb->query($sql);
		}
		
		
								
		
		
	}
	
	public function update_cart_amount ($cart_id,$amount)
	{
		global $wpdb,  $bookingultrapro;
		$amount = esc_sql($amount);
		$cart_id = esc_sql($cart_id);
		$query = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}bup_carts SET cart_amount = %s WHERE cart_id = %d",
			$amount,
			$cart_id
		);		
		$wpdb->query( $query );
	
	}
	
	/*Create Order*/
	public function create_order ($orderdata)
	{
		global $wpdb,  $bookingultrapro;
		
		extract($orderdata);
		$booking_id = esc_sql($booking_id);
		$transaction_key = esc_sql($transaction_key);
		$method = esc_sql($method);
		$status = esc_sql($status);
		$amount = esc_sql($amount);
		$quantity = esc_sql($quantity);
		//update database
		// Define the data to be inserted
		$data = array(
			'order_booking_id' => $booking_id,
			'order_key' => $transaction_key,
			'order_method_name' => $method,
			'order_status' => $status,
			'order_amount' => $amount,
			'order_qty' => $quantity,
			'order_date' => date('Y-m-d')
		);

		// Define the format for the data
		$data_format = array('%d', '%s', '%s', '%s', '%s', '%d', '%s');

		// Generate the prepared SQL statement
		$query = $wpdb->prepare(
			"INSERT INTO {$wpdb->prefix}bup_orders (`order_booking_id`, `order_key`, `order_method_name`, `order_status`, `order_amount`, `order_qty`, `order_date`) VALUES (%d, %s, %s, %s, %s, %d, %s)",
			$data['order_booking_id'],
			$data['order_key'],
			$data['order_method_name'],
			$data['order_status'],
			$data['order_amount'],
			$data['order_qty'],
			$data['order_date']
		);


		
		//echo $query;						
		$wpdb->query( $query );	
		return $wpdb->insert_id;					
						
	}
	
	/*Create Order*/
	public function create_cart ($transaction_key)
	{
		global $wpdb,  $bookingultrapro;
		$transaction_key = esc_sql($transaction_key);
		//update database
// Define the data to be inserted
$data = array(
    'cart_key' => $transaction_key,
    'cart_date' => date('Y-m-d')
);

// Define the format for the data
$data_format = array('%s', '%s');

// Generate the prepared SQL statement
$query = $wpdb->prepare(
    "INSERT INTO {$wpdb->prefix}bup_carts (`cart_key`, `cart_date`) VALUES (%s, %s)",
    $data['cart_key'],
    $data['cart_date']
);

// Insert the data into the table using prepared statement
		$wpdb->query( $query );	
		return $wpdb->insert_id;					
						
	}
	
	/*Create Appointment*/
	public function create_reservation ($orderdata)
	{
		global $wpdb,  $bookingultrapro;
		
		extract($orderdata);
		
		$start = $day.' '.$time_from.':00';
		$ends = $day.' '.$time_to.':00';
		$user_id = esc_sql($user_id);
		$service_id = esc_sql($service_id);
		$staff_id = esc_sql($staff_id);
		//update database
		// Define the data to be inserted
		$data = array(
			'booking_user_id' => $user_id,
			'booking_service_id' => $service_id,
			'booking_staff_id' => $staff_id,
			'booking_date' => date('Y-m-d'),
			'booking_time_from' => $start,
			'booking_time_to' => $ends,
			'booking_amount' => $amount,
			'booking_key' => $transaction_key,
			'booking_qty' => $quantity,
			'booking_template_id' => $template_id,
			'booking_cart_id' => $cart_id
		);

		// Define the format for the data
		$data_format = array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d');

		// Generate the prepared SQL statement
		$query = $wpdb->prepare(
			"INSERT INTO {$wpdb->prefix}bup_bookings (`booking_user_id`, `booking_service_id`, `booking_staff_id`, `booking_date`, `booking_time_from`, `booking_time_to`, `booking_amount`, `booking_key`, `booking_qty`, `booking_template_id`, `booking_cart_id`) VALUES (%d, %d, %d, %s, %s, %s, %s, %s, %d, %d, %d)",
			$data['booking_user_id'],
			$data['booking_service_id'],
			$data['booking_staff_id'],
			$data['booking_date'],
			$data['booking_time_from'],
			$data['booking_time_to'],
			$data['booking_amount'],
			$data['booking_key'],
			$data['booking_qty'],
			$data['booking_template_id'],
			$data['booking_cart_id']
		);

		// Insert the data into the table using a prepared statement
		
		//echo $query;						
		$wpdb->query( $query );		
		
		return $wpdb->insert_id;				
						
	}
	
	public function update_appointment ($orderdata)
	{
		global $wpdb,  $bookingultrapro;
		
		extract($orderdata);
		
		$start = $day.' '.$time_from.':00';
		$ends = $day.' '.$time_to.':00';
		$service_id = esc_sql($service_id);
		$staff_id = esc_sql($staff_id);
		$booking_id = esc_sql($booking_id);
		$day = esc_sql($day);
		//update database
		// Define the data to be updated
		$data = array(
			'booking_service_id' => $service_id,
			'booking_staff_id' => $staff_id,
			'booking_time_from' => $start,
			'booking_time_to' => $ends,
			'booking_amount' => $amount,
			'booking_id' => $booking_id
		);

		// Define the format for the data
		$data_format = array('%d', '%d', '%s', '%s', '%s', '%d');

		// Generate the prepared SQL statement
		$query = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}bup_bookings SET `booking_service_id` = %d, `booking_staff_id` = %d, `booking_time_from` = %s, `booking_time_to` = %s, `booking_amount` = %s WHERE `booking_id` = %d",
			$data['booking_service_id'],
			$data['booking_staff_id'],
			$data['booking_time_from'],
			$data['booking_time_to'],
			$data['booking_amount'],
			$data['booking_id']
		);

		// Execute the prepared statement to update the data
		
		//echo $query;						
		$wpdb->query( $query );		
		
		return $wpdb->insert_id;				
						
	}
	
	public function update_order_status ($id,$status)
	{
		global $wpdb,  $bookingultrapro;
		$id = esc_sql($id);
		$status = esc_sql($status);
		//update database
		// Define the data to be updated
		$data = array(
			'order_status' => $status,
			'order_id' => $id
		);

		// Define the format for the data
		$data_format = array('%s', '%d');

		// Generate the prepared SQL statement
		$query = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}bup_orders SET order_status = %s WHERE order_id = %d",
			$data['order_status'],
			$data['order_id']
		);

		// Execute the prepared statement to update the data
		$wpdb->query($query);
	
	}
	
	public function update_cart_status ($id,$status)
	{
		global $wpdb,  $bookingultrapro;
		$id = esc_sql($id);
		$status = esc_sql($status);

		//update database
		// Define the data to be updated
		$data = array(
			'cart_status' => $status,
			'cart_id' => $id
		);

		// Define the format for the data
		$data_format = array('%s', '%d');

		// Generate the prepared SQL statement
		$query = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}bup_carts SET cart_status = %s WHERE cart_id = %d",
			$data['cart_status'],
			$data['cart_id']
		);

		// Execute the prepared statement to update the data
		$wpdb->query($query);
	
	}
	
	public function update_expiration_date ($id,$expiration_date)
	{
		global $wpdb,  $bookingultrapro;
		$id = esc_sql($id);
		//$expiration_date = esc_sql($expiration_date);


		//update database
		// Define the data to be updated
		$data = array(
			'order_expiration' => $expiration_date,
			'order_id' => $id
		);

		// Define the format for the data
		$data_format = array('%s', '%d');

		// Generate the prepared SQL statement
		$query = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}bup_orders SET order_expiration = %s WHERE order_id = %d",
			$data['order_expiration'],
			$data['order_id']
		);

		// Execute the prepared statement to update the data
		$wpdb->query($query);
	
	}
	
	public function update_order_payment_response ($id,$order_txt_id)
	{
		global $wpdb,  $bookingultrapro;
		$id = esc_sql($id);
		$order_txt_id = esc_sql($order_txt_id);

		//update database
		// Define the data to be updated
		$data = array(
			'order_txt_id' => $order_txt_id,
			'order_id' => $id
		);

		// Define the format for the data
		$data_format = array('%s', '%d');

		// Generate the prepared SQL statement
		$query = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}bup_orders SET order_txt_id = %s WHERE order_id = %d",
			$data['order_txt_id'],
			$data['order_id']
		);

		// Execute the prepared statement to update the data
		$wpdb->query($query);
	
	}
	
	
	/*Get Order With Booking*/
	public function get_order_with_booking_id ($booking_id)
	{
		global $wpdb,  $bookingultrapro;
		$booking_id = esc_sql($booking_id);

		$orders = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $wpdb->prefix . 'bup_orders WHERE order_booking_id = %d',
				$booking_id
			)
		);
				
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
		
	
	}
	
	/*Get Cart*/
	public function get_cart_with_key_status ($key, $status)
	{
		global $wpdb,  $bookingultrapro;
		$key = esc_sql($key);
		$status = esc_sql($status);

		$orders = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $wpdb->prefix . 'bup_carts WHERE cart_key = %s AND cart_status = %s',
				$key,
				$status
			)
		);
				
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
		
	
	}
	
	
	/*Get Order*/
	public function get_order ($id)
	{
		global $wpdb,  $bookingultrapro;
		$id = esc_sql($id);

		$orders = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}bup_orders WHERE order_key = %s",
				$id
			)
		);
				
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
		
	
	}
	
	/*Get Order*/
	public function get_order_edit ($order_id , $booking_id)
	{
		global $wpdb,  $bookingultrapro;
		$order_id = esc_sql($order_id);
		$booking_id = esc_sql($booking_id);


		$orders = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}bup_orders WHERE order_id = %d AND order_booking_id = %d",
				$order_id,
				$booking_id
			)
		);
				
		if ( empty( $orders ) )
		{		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
		
	
	}
	
	/*Get Latest*/
	public function get_latest ($howmany)
	{
		global $wpdb,  $bookingultrapro;
		$howmany = absint($howmany); // Assuming you want a positive integer limit.

		$sql = "SELECT ord.*, usu.* FROM {$wpdb->prefix}bup_orders AS ord";
		$sql .= " RIGHT JOIN {$wpdb->users} AS usu ON (usu.ID = ord.order_user_id)";
		$sql .= " WHERE ord.order_id <> 0 AND usu.ID = ord.order_user_id";
		$sql .= " ORDER BY ord.order_id DESC LIMIT %d";
		
		$prepared_sql = $wpdb->prepare($sql, $howmany);
		
		$orders = $wpdb->get_results($prepared_sql);
		
		return $orders ;		
	
	}
	
	/*Get Orders*/
	public function get_booking_payments ($appointment_id)
	{
		global $wpdb,  $bookingultrapro;
		$appointment_id = esc_sql($appointment_id); 

		$sql = "SELECT ord.* FROM {$wpdb->prefix}bup_orders AS ord";
		$sql .= " WHERE ord.order_id <> 0 AND ord.order_booking_id = %d";
		$sql .= " ORDER BY ord.order_date DESC";
		
		$prepared_sql = $wpdb->prepare($sql, $appointment_id);
		
		$orders = $wpdb->get_results($prepared_sql);
				
		return $orders ;		
	
	}
	
	public function get_booking_payments_balance ($appointment_id)
	{
		global $wpdb,  $bookingultrapro;
		$appointment_id = esc_sql($appointment_id);
		$totals = array();
		
		$total_confirmed = 0;
		$total_pending = 0;
		$balance = 0;		
		$booking_cost = 0;
		

		$sql = "SELECT SUM(order_amount) as total FROM {$wpdb->prefix}bup_orders";
		$sql .= " WHERE order_booking_id = %d AND order_status = 'confirmed'";
		
		$prepared_sql = $wpdb->prepare($sql, $appointment_id);
		
		$orders = $wpdb->get_results($prepared_sql);
		
		
		foreach ( $orders as $order )
		{
			$total_confirmed =$order->total;					
			
		}
		
		$appointment_id = esc_sql($appointment_id); 

		$sql = "SELECT SUM(order_amount) as total FROM {$wpdb->prefix}bup_orders";
		$sql .= " WHERE order_booking_id = %d AND order_status = 'pending'";
		
		$prepared_sql = $wpdb->prepare($sql, $appointment_id);
		
		$orders = $wpdb->get_results($prepared_sql);
		
		
		foreach ( $orders as $order )
		{
			$total_pending =$order->total;					
			
		}
		
		$appointment_id = esc_sql($appointment_id); // Assuming $appointment_id is a string.

		$sql = "SELECT booking_amount as total FROM {$wpdb->prefix}bup_bookings";
		$sql .= " WHERE booking_id = %d";

		$prepared_sql = $wpdb->prepare($sql, $appointment_id);

		$orders = $wpdb->get_results($prepared_sql);

		
		foreach ( $orders as $order )
		{
			$booking_cost =$total_confirmed+$total_pending;					
			
		}
		
		if($total_confirmed==''){$total_confirmed=0;}
		if($total_pending==''){$total_pending=0;}
		
		$balance = $booking_cost - $total_confirmed ;			
		$totals = array('cost' => $booking_cost ,'confirmed' => $total_confirmed , 'pending' => $total_pending , 'balance' => $balance);
				
		return $totals ;		
	
	}
	
	
	/*Get all*/
	public function get_all ()
	{
		global $wpdb,  $bookingultrapro;
		
		$keyword = "";
		$month = "";
		$day = "";
		$year = "";
		$howmany = "";
		$ini = 1;
		
		if(isset($_GET["keyword"]))
		{
			$keyword = esc_sql($_GET["keyword"]);		
		}
		
		if(isset($_GET["month"]) && $_GET["month"] != 0 )
		{
			$month = esc_sql($_GET["month"]);		
		}
		
		if(isset($_GET["day"]) && $_GET["day"] != 0)
		{
			$day =esc_sql( $_GET["day"]);		
		}
		
		if(isset($_GET["year"]) && $_GET["year"] != 0 )
		{
			$year = esc_sql($_GET["year"]);		
		}
		
		if(isset($_GET["howmany"]) && $_GET["howmany"] != 0)
		{
			$howmany = esc_sql($_GET["howmany"]);		
		}
		if(isset($_GET["paged"])) // deepak
		{
			$bup_paged = esc_sql($_GET["paged"]);		
		}
		else
		{
			$bup_paged = 1;
		}
		
				
		$uri= esc_attr($_SERVER['REQUEST_URI']) ;
		$url = explode("&ini=",$uri);
		
		if(is_array($url ))
		{
			//print_r($url);
			if(isset($url["1"]))
			{
				$ini = $url["1"];
			    if($ini == ""){$ini=1;}
			
			}
		
		}		
		
		if($howmany == ""){$howmany=10;}

		$limit = 10;
		$current_page = $ini;
		$target_page =  site_url()."/wp-admin/admin.php?page=bookingultra-orders";
		
		$how_many_per_page =  $howmany;
		$from=($bup_paged-1)*$howmany;	//deepak
		$to = $how_many_per_page + $from;

		//get all			
		
		$sql = 'SELECT ord.*, usu.*, serv.*, appo.* FROM ' . $wpdb->prefix . 'bup_orders ord';
		$sql .= " RIGHT JOIN " . $wpdb->prefix . 'bup_bookings appo ON (ord.order_booking_id = appo.booking_id)';
		$sql .= " RIGHT JOIN " . $wpdb->users . ' usu ON (usu.ID = appo.booking_staff_id)';
		$sql .= " RIGHT JOIN " . $wpdb->prefix . 'bup_services serv ON (serv.service_id = appo.booking_service_id)';
		$sql .= " WHERE serv.service_id = appo.booking_service_id AND ord.order_booking_id = appo.booking_id";
		
		if (!empty($keyword)) {
			$keyword = '%' . esc_sql($keyword) . '%';
			$sql .= $wpdb->prepare(" AND (ord.order_txt_id LIKE %s OR usu.display_name LIKE %s OR usu.user_email LIKE %s OR usu.user_login LIKE %s)", $keyword, $keyword, $keyword, $keyword);
		}
		
		if (!empty($day)) {
			$sql .= $wpdb->prepare(" AND DAY(ord.order_date) = %d", $day);
		}
		
		if (!empty($month)) {
			$sql .= $wpdb->prepare(" AND MONTH(ord.order_date) = %d", $month);
		}
		
		if (!empty($year)) {
			$sql .= $wpdb->prepare(" AND YEAR(ord.order_date) = %d", $year);
		}
		
		$sql .= " ORDER BY ord.order_id DESC";
		
		if (!empty($from) && !empty($to)) {
			$sql .= $wpdb->prepare(" LIMIT %d, %d", $from, $to);
		}
		
					
		$orders = $wpdb->get_results($sql);
		
		return $orders ;
		
	
	}

		/*Get total of filtered order all*/
		public function get_total_of_filtered_order ()
		{
			global $wpdb,  $bookingultrapro;
			
			$keyword = "";
			$month = "";
			$day = "";
			$year = "";

			
			if(isset($_GET["keyword"]))
			{
				$keyword = esc_sql($_GET["keyword"]);		
			}
			
			if(isset($_GET["month"]) && $_GET["month"] != 0 )
			{
				$month = esc_sql($_GET["month"]);		
			}
			
			if(isset($_GET["day"]) && $_GET["day"] != 0)
			{
				$day =esc_sql( $_GET["day"]);		
			}
			
			if(isset($_GET["year"]) && $_GET["year"] != 0 )
			{
				$year =esc_sql( $_GET["year"]);		
			}
			
			//get total				
					
			$sql = 'SELECT count(*) as total, ord.*, usu.*, serv.*, appo.* FROM ' . $wpdb->prefix . 'bup_orders ord';
			$sql .= " RIGHT JOIN " . $wpdb->prefix . 'bup_bookings appo ON (ord.order_booking_id = appo.booking_id)';
			$sql .= " RIGHT JOIN " . $wpdb->users . ' usu ON (usu.ID = appo.booking_staff_id)';
			$sql .= " RIGHT JOIN " . $wpdb->prefix . 'bup_services serv ON (serv.service_id = appo.booking_service_id)';
			$sql .= " WHERE serv.service_id = appo.booking_service_id AND ord.order_booking_id = appo.booking_id";

			if (!empty($keyword)) {
				$keyword = '%' . esc_sql($keyword) . '%';
				$sql .= " AND (ord.order_txt_id LIKE %s OR usu.display_name LIKE %s OR usu.user_email LIKE %s OR usu.user_login LIKE %s)";
			}

			if (!empty($day)) {
				$sql .= $wpdb->prepare(" AND DAY(ord.order_date) = %d", $day);
			}

			if (!empty($month)) {
				$sql .= $wpdb->prepare(" AND MONTH(ord.order_date) = %d", $month);
			}

			if (!empty($year)) {
				$sql .= $wpdb->prepare(" AND YEAR(ord.order_date) = %d", $year);
			}

			$sql .= " ORDER BY appo.booking_time_from";

			$orders = $wpdb->get_results($sql);

			$orders_total = $this->fetch_result($orders);
			$order_total = $orders_total->total;
			return $order_total ;
		
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
	
	public function get_order_pending ($id)
	{
		global $wpdb,  $bookingultrapro;
		$id = esc_sql($id);
		$id = esc_sql($id); // Assuming $id is a string.

		$orders = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}bup_orders WHERE order_key = %s AND order_status = 'pending'",
				$id
			)
		);
				
		if ( empty( $orders ) )
		{
		
		
		}else{			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	public function get_orders_by_status ($status)
	{
		global $wpdb,  $bookingultrapro;
		$status = esc_sql($status);
		
		//$sql = 'SELECT count(*) as total FROM ' . $wpdb->prefix . 'bup_orders WHERE order_status="'.$status.'" ';
		
		

		$sql = 'SELECT count(*) as total, ord.*, usu.*, serv.*, appo.* FROM ' . $wpdb->prefix . 'bup_orders ord';
		$sql .= " RIGHT JOIN " . $wpdb->prefix . 'bup_bookings appo ON (ord.order_booking_id = appo.booking_id)';
		$sql .= " RIGHT JOIN " . $wpdb->users . ' usu ON (usu.ID = appo.booking_staff_id)';
		$sql .= " RIGHT JOIN " . $wpdb->prefix . 'bup_services serv ON (serv.service_id = appo.booking_service_id)';
		$sql .= " WHERE serv.service_id = appo.booking_service_id AND ord.order_booking_id = appo.booking_id";
		$sql .= $wpdb->prepare(" AND ord.order_status = %s", $status);

		$rows = $wpdb->get_results($sql);

		
		
		if ( empty( $rows ) )
		{
		
		}else{
			
			foreach ( $rows as $order )
			{
				return $order->total;			
			
			}
		}
				
	}
	
	public function get_order_confirmed ($id)
	{
		global $wpdb,  $bookingultrapro;
		$id = esc_sql($id);
		
		$orders = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}bup_orders WHERE order_key = %s AND order_status = 'confirmed'",
				$id
			)
		);
				
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	/*Get Latest*/
	public function get_latest_user ($user_id, $howmany)
	{

		global $wpdb,  $bookingultrapro;
		$user_id = esc_sql($user_id);
		$howmany = esc_sql($howmany);
		

		$sql = "SELECT ord.*, usu.* FROM {$wpdb->prefix}bup_orders ord";
		$sql .= " RIGHT JOIN $wpdb->users usu ON (usu.ID = ord.order_user_id)";
		$sql .= " WHERE ord.order_id <> 0 AND usu.ID = %s";
		$sql .= " ORDER BY ord.order_id DESC LIMIT %d";

		$prepared_sql = $wpdb->prepare($sql, $user_id, $howmany);

		$orders = $wpdb->get_results($prepared_sql);

		
		return $orders ;		
	
	}
	
	/**
	 * My Orders 
	 */
	function show_my_latest_orders($howmany, $status=null)
	{
		global $wpdb, $current_user, $bookingultrapro; 
		
			
		
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		
		$user_id = get_current_user_id();
		 
		
        $drOr = $this->get_latest_user($user_id,30);
		
		//print_r($loop );
				
		
		if (  empty( $drOr) )
		{
			echo esc_attr('<p>', __( 'You have no orders.', 'bookingup' ), '</p>');
		}
		else
		{
			$n = count( $drOr );
			
			
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						
                       
						<th class="manage-column" ><?php echo esc_html_e( 'Order #', 'bookingup' ); ?></th>
                        <th class="manage-column"><?php echo esc_html_e( 'Total', 'bookingup' ); ?></th>
						<th class="manage-column"><?php echo esc_html_e( 'Date', 'bookingup' ); ?></th>
						<th class="manage-column" ><?php echo esc_html_e( 'Package', 'bookingup' ); ?></th>
                        <th class="manage-column" ><?php echo esc_html_e( 'Status', 'bookingup' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
							
							foreach ( $drOr as $order){
							$order_id = $order->order_id;
							
							//get package
							
							$package = $xoouserultra->paypal->get_package($order->order_package_id);
							
							
							//print_r($order );
							
							?>
						<tr>
							                         
                            
							<td>#<?php echo esc_attr($order_id); ?></td>
                            <td><?php echo  esc_attr($currency_symbol.$order->order_amount);?></td>
							<td> <?php echo esc_attr($order->order_date); ?></td>
							<td><?php echo esc_attr($package->package_name); ?></td>
                            <td><?php echo esc_attr($order->order_status); ?></td>
                            
                            
							<?php
	
							}
						?>
					</tbody>
					
				</table>
			</form>
			<?php
	
		}
		?>

	<?php
	}
	
	
	

}
$key = "order";
$this->{$key} = new BookingUltraOrder();