<?php
  /**
   * Plugin Name: Automatic SPAM Eraser
   * Plugin URI: http://wordpress.org/plugins/automatic-spam-eraser/
   * Description: The plugin is adding a new WP-Cron event which automatically removes all <a href="edit-comments.php?comment_status=spam">spam</a> comments older than 7 days.
   * Version: 1.0
   * Author: Piotr Prądzyński
   * Author URI: http://prondzyn.com
   * License: GPL2
   */

  register_activation_hook(__FILE__, 'on_activation');

  function on_activation() {
    wp_schedule_event( time(), 'daily', 'automatic_spam_eraser_event' );
  }
  
  add_action( 'automatic_spam_eraser_event',  'delete_spam_from_db' );
  
  function delete_spam_from_db() {
    global $wpdb;
    $wpdb->query( 
      $wpdb->prepare(
        "DELETE FROM $wpdb->comments WHERE comment_approved = %s AND DATEDIFF(DATE(%s), comment_date) >= %d", 
        'spam',
        date('Y-m-d'), 7
      )
    );
  }

  register_deactivation_hook(__FILE__, 'on_deactivation');
  
  function on_deactivation() {
    wp_clear_scheduled_hook( 'automatic_spam_eraser_event' );
  }
?>