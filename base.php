<?php 

/**
	Plugin Name: Panda Reviews
	Plugin URI: http://pixollete.com
	Description: A review and rating plugin for WordPress. Let your users to post reviews and vote any posts or custom post from your WordPress site.
	Author: Pixolette
	Version: 1.0.0
	Author URI: http://pixolette.com
 */

if ( ! defined( 'PXPR_PLUGIN_PATH' ) ) {
	define( 'PXPR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'PXPR_PLUGIN_URL' ) ) {
	define( 'PXPR_PLUGIN_URL', plugins_url() . '/panda-reviews/' );
}

require_once PXPR_PLUGIN_PATH . '_controllers/main-controller.php';
require_once PXPR_PLUGIN_PATH . '_controllers/ajax-requests-controller.php';
require_once PXPR_PLUGIN_PATH . 'settings.php';

register_activation_hook( __FILE__, 'install_px_reviews' );
/**
 * Creates custom table pxreviews. Used to store the user's revies.
 * Runs only when plugin is activated
 *
 * @var function
 */
function install_px_reviews() {

	global $wpdb;

	$table_name = esc_sql( $wpdb->prefix . 'pxreviews' );
	$table_exist = $wpdb->get_var( "SHOW TABLES LIKE '$table_name' ");

	if ( $table_exist != $table_name ) {

		$sql = "CREATE TABLE {$wpdb->prefix}pxreviews (
					reviewID INT(11) NOT NULL AUTO_INCREMENT, 
					post_id INT(11) NOT NULL, 
					comment VARCHAR(1000) NOT NULL, 
					user_ip VARCHAR(100), 
					email VARCHAR(255), 
					username VARCHAR(255), 
					review_date DATETIME, 
					comment_parent INT(11) DEFAULT 0, 
					comment_type VARCHAR(255),
					status VARCHAR(100) DEFAULT 'draft',
					PRIMARY KEY (reviewID)
				)";

		$default_review_sql = "INSERT INTO {$wpdb->prefix}pxreviews
							(`post_id`, `username`, `comment`, `user_ip`, `email`, `username`, `comment_parent`, `comment_type`) 
							VALUES (1, 'Panda Reviews' 'A simple draft review. It's just a draft!', '', '', '', 0, 'review')";
		$wpdb->query( $sql );
		$wpdb->query( $default_review_sql );

	}
}
