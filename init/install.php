<?php
class BookingUltra_Install
{

   public function __construct()
   {
      $this->add_bup_categories();
      $this->default_settings();
      $this->create_page('Appointment','[bupro_appointment]');
      
   }
   public function add_bup_categories()
   {

      global $wpdb, $bookingultrapro;
      $table  = $wpdb->prefix . "bup_categories";
    $query = $wpdb->prepare("SELECT cate_name FROM %s", $table);
      $result = $wpdb->get_results($query, ARRAY_A);      
      if ( $wpdb->num_rows == 0 ) {

         $wpdb->insert( $table, array(
            'cate_template_id'  => 1,
            'cate_name' => 'uncategorized',
            'cate_order' =>  1// ... and so on
         ));
      }

   }
    public function create_page($title_of_the_page, $content, $parent_id = null)
   {
     // $objPage = get_page_by_title($title_of_the_page, 'OBJECT', 'page');
         $pages = get_posts( [
             'title'     => $title_of_the_page,
             'post_type' => 'page',
         ] );
      $objPage= get_post( $pages[0]->ID, 'OBJECT');

      if (!empty($objPage)) {
         return;
      }
      wp_insert_post(
         array(
            'comment_status' => 'close',
            'ping_status'    => 'close',
            'post_author'    => 1,
            'post_title'     => ucwords($title_of_the_page),
            'post_name'      => strtolower(str_replace(' ', '-', trim($title_of_the_page))),
            'post_status'    => 'publish',
            'post_content'   => $content,
            'post_type'      => 'page',
            'post_parent'    => $parent_id, //'id_of_the_parent_page_if_it_available'
         )
      );
   }

   public function default_settings() {
     
      $settings = get_option('bup_options');    
      if(!isset($settings['gateway_test_payment_active'])) {
         $settings['gateway_test_payment_active'] = 1;
         update_option('bup_options', $settings);   
      }
      
   }
}

new BookingUltra_Install;