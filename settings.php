<?php

use PxReviews\Controller\PxprMainController as ReviewsController;
use PxReviews\Controller\AjaxRequestsController;

$review_main_controller = new ReviewsController();
$ajax_requests_controller = new AjaxRequestsController();

add_action( 'admin_menu', 'pr_init_plugin_page' );

/**
 * Init the plugin menu into admin sidebar menu
 *
 * @var function
 */
function pr_init_plugin_page() {

	global $review_main_controller;

	$unread_reviews = ReviewsController::count_unread_reviews();
	$count_label = '';

	if ( $unread_reviews > 0 ) {
		$count_label = '<span class="update-plugins count-' . (int) $unread_reviews . '"><span class="plugin-count">' . (int) $unread_reviews . '</span></span>';
	}

	add_menu_page( 'Panda Reviews', 'Panda Reviews' . $count_label , 'manage_options', 'px_posts_reviews', array( $review_main_controller, 'init_wp_backend' ), '' );

}

add_action( 'init', array( $review_main_controller, 'init_frontend' ) );


add_action( 'wp_ajax_nopriv_px-reviews-ajax',  array( $ajax_requests_controller, 'init' ) );
add_action( 'wp_ajax_px-reviews-ajax', array( $ajax_requests_controller, 'init' ) );

add_action( 'admin_enqueue_scripts', 'px_reviews_admin_enqueue_scripts' );

/**
 * Wp backend scripts register
 *
 * @var function
 */
function px_reviews_admin_enqueue_scripts() {

	wp_enqueue_script( 'jquery' );

	wp_register_script( 'px_custom_select_box', PXPR_PLUGIN_URL . 'assets/js/custom-select-box.js', false, 1.0 );
	wp_enqueue_script( 'px_custom_select_box', array( 'jquery' ), '1.0', true );

	wp_enqueue_script( 'px_reviews_main', PXPR_PLUGIN_URL . 'assets/wpbackend.min.js', array( 'jquery' ), '', true );

	wp_localize_script( 'px_reviews_main', 'pxreviews', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'action' => 'px-reviews-ajax' ) );

}

add_action( 'admin_enqueue_scripts', 'px_admin_styles' );

/**
 * Wp backend styles register
 *
 * @var function
 */
function px_admin_styles() {
	wp_enqueue_style( 'px-reviews-wpadmin-style', PXPR_PLUGIN_URL . 'assets/css/wpbackend.css', array(), '1.0' );
}


add_action( 'wp_enqueue_scripts', array( $review_main_controller, 'px_reviews_style' ) );

add_action( 'wp_enqueue_scripts', 'px_reviews_scripts' );

/**
 * Plugin scripts register
 *
 * @var function
 */
function px_reviews_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'px_reviews_main', PXPR_PLUGIN_URL . 'assets/main.min.js', array( 'jquery' ), '1.0', true );

	wp_localize_script( 'px_reviews_main', 'pxreviews', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'action' => 'px-reviews-ajax' ) );

}

add_action( 'wp_head', 'pxreviews_header' );

/**
 * Include overlay review form into header section
 *
 * @var function
 */
function pxreviews_header() {

	include_once PXPR_PLUGIN_PATH . '_views/post-review.php';

}

/**
 * Get the user IP
 *
 * @var function
 */
function px_get_user_ip() {

	$client = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];

	return filter_var( $client, FILTER_VALIDATE_IP ) ? $client : filter_var( $forward, FILTER_VALIDATE_IP ) ? $forward : $_SERVER['REMOTE_ADDR'];
}

if ( ! function_exists( 'px_set_as_active' ) ) {

	/**
	 * Set Menu Nav as Active
	 *
	 * @param string $active_nav The current nav that should be active.
	 * @param string $nav_name The nav name.
	 * @param string $active_class_name The name of active class.
	 * @var function
	 */
	function px_set_as_active( $active_nav, $nav_name, $active_class_name ) {
		if ( $active_nav === $nav_name ) {
			echo esc_attr( $active_class_name );
		}
	}
}
