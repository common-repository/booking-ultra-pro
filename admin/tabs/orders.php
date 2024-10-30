<?php
global $bookingultrapro;
$currency_symbol =  $bookingultrapro->get_option('paid_membership_symbol');
$orders = $bookingultrapro->order->get_all();
$total_filtered_orders=$bookingultrapro->order->get_total_of_filtered_order();
$howmany = 10;
$year = "";
$month = "";
$day = "";
$nonce_check=false;
if(isset($_GET["_wpnonce"]))
{
    $nonce = $_REQUEST['_wpnonce'];

if ( ! wp_verify_nonce( $nonce, 'bup_order_filter' ) ) {
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
}else
$howmany=10;

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
		
$paged = (!empty($_GET['paged'])) ? esc_attr($_GET['paged']) : 1;	 // added by deepak

?>
   <h1><?php esc_html_e('Payments','booking-ultra-pro'); ?></h1>
         
         <div class="bup-sect welcome-panel">
          <form action="" method="get">

         <input type="hidden" name="page" value="bookingultra-orders" />
         <ul class="subsubsub">
            <li class="all"><a href="#" class="current" aria-current="page">Total <span class="count">(<?php echo escape_with_custom_html($total_filtered_orders);?>)</span></a></li>  
        </ul>
          <div class="tablenav top" style="margin-bottom: 12px;">
              <input type="text" name="keyword" id="keyword" placeholder="<?php esc_html_e('write some text here ...','booking-ultra-pro'); ?>" />

              <select name="month" id="month">
               <option value="0" selected="selected"><?php esc_html_e('Select Month','booking-ultra-pro'); ?></option>
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
               <option value="0" selected="selected"><?php esc_html_e('Select Day','booking-ultra-pro'); ?></option>
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
               <option value="0" selected="selected"><?php esc_html_e('Select Year','booking-ultra-pro'); ?></option>
               <?php
        
        $i = 2014;
              
        while($i <= date('Y')){
        ?>
               <option value="<?php echo esc_attr($i)?>" <?php if($i==$year) echo 'selected="selected"';?> ><?php echo esc_attr($i)?></option>
               <?php 
          $i++;
         }?>
             </select>
             <select name="howmany" id="howmany">
                <option value="0"><?php esc_html_e('Per Page','booking-ultra-pro'); ?></option>
               <option value="20" <?php if(20==$howmany ) echo 'selected="selected"';?>>20</option>
                <option value="40" <?php if(40==$howmany ) echo 'selected="selected"';?>>40</option>
                 <option value="50" <?php if(50==$howmany ) echo 'selected="selected"';?>>50</option>
                  <option value="80" <?php if(80==$howmany ) echo 'selected="selected"';?>>80</option>
                   <option value="100" <?php if(100==$howmany ) echo 'selected="selected"';?>>100</option>
               
          </select>
          <button class="button"><?php esc_html_e('Filter','booking-ultra-pro'); ?></button>
          </div>
          <?php wp_nonce_field( 'bup_order_filter' ); ?> 
        </form>
         <table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>

                <tr>
                    <th width="4%"><?php esc_html_e('#', 'booking-ultra-pro'); ?></th>
                    <th width="6%"><?php esc_html_e('A. #', 'booking-ultra-pro'); ?></th>
                     <th width="11%"><?php esc_html_e('Date', 'booking-ultra-pro'); ?></th>
                    
                    <th width="23%"><?php esc_html_e('Client', 'booking-ultra-pro'); ?></th>
                     <th width="18%"><?php esc_html_e('Service', 'booking-ultra-pro'); ?></th>
                    <th width="16%"><?php esc_html_e('Transaction ID', 'booking-ultra-pro'); ?></th>
                    
                     <th width="9%"><?php esc_html_e('Method', 'booking-ultra-pro'); ?></th>
                     <th width="9%"><?php esc_html_e('Status', 'booking-ultra-pro'); ?></th>
                    <th width="9%"><?php esc_html_e('Amount', 'booking-ultra-pro'); ?></th>
                </tr>

            </thead>
        
         <?php
        if(empty($howmany))
              $howmany=10;
				if (!empty($orders)){
          $total_pages = ceil($total_filtered_orders/$howmany); //deepak
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
        
          
            
            <tbody>
            
            <?php 
			foreach($orders as $order) {
				
				$client_id = $order->booking_user_id;				
				$client = get_user_by( 'id', $client_id );
					
			?>
              

                <tr>
                    <td><?php echo esc_attr($order->order_id); ?></td>
                    <td><?php echo  esc_attr($order->booking_id); ?></td>
                     <td><?php echo  date("m/d/Y", strtotime($order->order_date)); ?></td>
                    <td><?php echo esc_attr($client->display_name); ?> (<?php echo esc_attr($client->user_login); ?>)</td>
                    <td><?php echo esc_attr($order->service_title); ?> </td>
                    <td><?php echo esc_attr($order->order_txt_id); ?></td>
                     
                      <td><?php echo esc_attr($order->order_method_name); ?></td>
                      <td><?php echo esc_attr($order->order_status); ?></td>
                   <td> <?php echo $currency_symbol.$order->order_amount; ?></td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
      <tr>
        <td colspan="12"><?php esc_html_e('There are no transactions yet.','booking-ultra-pro'); ?></td>
      </tr>
			
			<?php	} ?>

            </tbody>
        </table>
        
        
        </div>
        
