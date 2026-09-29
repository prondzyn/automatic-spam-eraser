<?php
  /**
   * Plugin Name: Automatic SPAM Eraser
   * Plugin URI: http://wordpress.org/plugins/automatic-spam-eraser/
   * Description: The plugin is adding a new WP-Cron event which automatically removes all <a href="edit-comments.php?comment_status=spam">spam</a> comments older than 7 days (configurable in <a href="options-discussion.php#automatic_spam_eraser_days">Settings &rarr; Discussion</a>).
   * Version: 1.1
   * Author: Piotr Prądzyński
   * Author URI: http://prondzyn.com
   * License: GPL2
   * Text Domain: automatic-spam-eraser
   */

  define( 'AUTOMATIC_SPAM_ERASER_DEFAULT_DAYS', 7 );

  register_activation_hook(__FILE__, 'on_activation');

  function on_activation() {
    wp_schedule_event( time(), 'daily', 'automatic_spam_eraser_event' );
  }

  add_action( 'automatic_spam_eraser_event',  'delete_spam_from_db' );

  function automatic_spam_eraser_get_days() {
    return automatic_spam_eraser_sanitize_days( get_option( 'automatic_spam_eraser_days', AUTOMATIC_SPAM_ERASER_DEFAULT_DAYS ) );
  }

  function automatic_spam_eraser_sanitize_days( $days ) {
    $days = intval( $days );
    return $days >= 1 ? $days : AUTOMATIC_SPAM_ERASER_DEFAULT_DAYS;
  }

  function delete_spam_from_db() {
    global $wpdb;
    // spam from N or more calendar days ago, i.e. before midnight N-1 days ago;
    // computed in PHP instead of DATEDIFF() to work on non-MySQL databases (e.g. SQLite)
    $cutoff = gmdate( 'Y-m-d', strtotime( '-' . ( automatic_spam_eraser_get_days() - 1 ) . ' days' ) );
    $wpdb->query(
      $wpdb->prepare(
        "DELETE FROM $wpdb->comments WHERE comment_approved = %s AND comment_date < %s",
        'spam',
        $cutoff
      )
    );
  }

  add_action( 'admin_init', 'automatic_spam_eraser_register_settings' );

  function automatic_spam_eraser_register_settings() {
    register_setting( 'discussion', 'automatic_spam_eraser_days', 'automatic_spam_eraser_sanitize_days' );
    add_settings_field(
      'automatic_spam_eraser_days',
      __( 'Automatic SPAM Eraser', 'automatic-spam-eraser' ),
      'automatic_spam_eraser_render_days_field',
      'discussion'
    );
  }

  function automatic_spam_eraser_render_days_field() {
    printf(
      '<label for="automatic_spam_eraser_days">%s</label>',
      sprintf(
        /* translators: %s: number input */
        __( 'Delete spam comments older than %s days', 'automatic-spam-eraser' ),
        sprintf(
          '<input name="automatic_spam_eraser_days" type="number" step="1" min="1" id="automatic_spam_eraser_days" value="%d" class="small-text" />',
          automatic_spam_eraser_get_days()
        )
      )
    );
  }

  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'automatic_spam_eraser_action_links' );

  function automatic_spam_eraser_action_links( $links ) {
    array_unshift( $links, sprintf(
      '<a href="%s">%s</a>',
      esc_url( admin_url( 'options-discussion.php#automatic_spam_eraser_days' ) ),
      __( 'Settings', 'automatic-spam-eraser' )
    ) );
    return $links;
  }

  register_deactivation_hook(__FILE__, 'on_deactivation');

  function on_deactivation() {
    wp_clear_scheduled_hook( 'automatic_spam_eraser_event' );
  }

  register_uninstall_hook( __FILE__, 'automatic_spam_eraser_uninstall' );

  function automatic_spam_eraser_uninstall() {
    delete_option( 'automatic_spam_eraser_days' );
  }
?>