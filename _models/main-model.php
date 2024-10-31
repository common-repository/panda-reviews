<?php

namespace PxReviews\Model;

/**
 * Class the main Model used for plugin's main DB queries;
 *
 * @category Controller
 * @package  AjaxRequest
 * @author  LP|PIXOLETTE
 * @license  http://www.pixollete.com
 * @link     http://www.pixollete.com
 **/
class MainModel {

	/**
	 * The option_name of plugin's data options
	 *
	 * @access public
	 * @var string
	 */
	public $plugin_options = 'px_reviews_options';

	/**
	 * Is true if option name is already into db.
	 *
	 * @access public
	 * @var boolean
	 */
	private $option_exists = false;

	/**
	 * The post meta_name for reviews data
	 *
	 * @access public
	 * @var string
	 */
	public $post_meta = 'px_reviews';

	/**
	 * The post meta_name of user's ips who voted
	 *
	 * @access public
	 * @var string
	 */
	public $votes_ips = 'px_votes_ips';

	/**
	 * Will store in an array all plugin's options
	 *
	 * @access public
	 * @var array
	 */
	public $options_data = array(
		'display-voting-system' => 1,
		'display-review-system' => 1,
		'display-classic-stars' => 1,
		'main-color'			=> '#0FBBFF',
	);


	/**
	 * The class constructor. Init plugin' options data;
	 *
	 * @access public
	 * @var function
	 */
	function __construct() {

		$data = get_option( $this->plugin_options );

		if ( false !== $data ) {

			$this->option_exists = true;
			$this->options_data = stripslashes_deep( json_decode( $data, true ) );

		}

	}

	/**
	 * Update the plugin's options.
	 *
	 * @access public
	 * @param array $data An array of plugin's option.
	 * @var function
	 */
	public function save_options( $data = array() ) {

		$data_to_save = wp_json_encode( $data );

		if ( false !== $this->option_exists ) {

			update_option( $this->plugin_options, $data_to_save );

		} else {

			add_option( $this->plugin_options, $data_to_save );

		}

		$this->options_data = $data;

	}

	/**
	 * Get the IPs of the users who already voted
	 *
	 * @access public
	 * @param int $post_id The post ID.
	 * @var function
	 */
	public function get_active_ips( $post_id ) {

		$ips = get_post_meta( $post_id, $this->votes_ips, true );

		if ( false !== $ips ) {
			return json_decode( $ips, true );
		}

		return false;

	}

	/**
	 * Get the IPs of the users who already voted
	 *
	 * @access public
	 * @param int    $post_id The post ID.
	 * @param string $ip      The user IP.
	 * @var function
	 */
	public function update_ips_list( $post_id, $ip ) {

		$data_ips = $this->get_active_ips( $post_id );

		if ( false !== $data_ips ) {

			$data_ips[] = $ip;
			$data = wp_json_encode( $data_ips );

			update_post_meta( $post_id, $this->votes_ips, $data );

		} else {

			$data = wp_json_encode( array( $ip ) );
			add_post_meta( $post_id, $this->votes_ips, $data );

		}

	}

	/**
	 * Get the votes list by post_id
	 *
	 * @access public
	 * @param int $post_id The post ID.
	 * @var function
	 */
	public function get_votes( $post_id ) {

		$data = get_post_meta( $post_id, $this->post_meta, true );

		if ( false !== $data ) {
			return stripslashes_deep( json_decode( $data, true ) );
		}

		return false;

	}

	/**
	 * Get the votes list by post_id
	 *
	 * @access public
	 * @param int   $post_id The post ID.
	 * @param int   $user_ip The user's IP.
	 * @param array $review  The user's review data.
	 * @var function
	 */
	public function update_post_votes( $post_id, $user_ip, $review ) {

		$key = str_replace( '.', '', $user_ip );

		$data_post_meta = $this->get_votes( $post_id );

		if ( false !== $data_post_meta ) {
			$data = $data_post_meta;
		}

		$votes_count = ( isset( $data['votes_count'] ) ? (int) $data['votes_count'] : 0 );

		if ( isset( $review['reviews_count'] ) ) {
			@$data['comments_count'] = $review['reviews_count'];
		}

		if ( isset( $review['user'] ) ) {
			@$data['votes'][ $key ]['user'] = $review['user'];
		}

		if ( isset( $review['vote'] ) ) {
			@$data['votes'][ $key ]['vote'] = (int) $review['vote'];
		}

		if ( isset( $review['comment'] ) ) {
			@$data['votes'][ $key ]['comment'] = $review['comment'];
		}

		if ( isset( $review['vote'] ) ) {
			@$data['votes_count'] = $votes_count + 1;
		}

		if ( isset( $review['vote'] ) ) {
			@$data['votes_amount'] = (int) $data['votes_amount'] + $review['vote'];
		}

		$data = wp_json_encode( $data );

		if ( false !== $data_post_meta ) {

			update_post_meta( $post_id, $this->post_meta, $data );

		} else {

			add_post_meta( $post_id, $this->post_meta, $data );

		}

		return $data;
	}

	/**
	 * Get the votes list by post_id
	 *
	 * @access public
	 * @param int   $post_id The post ID.
	 * @param array $args The args of review query. Posibble values:
	 *		  array( 'status' => { 'active' | 'draft' }, 'meta_key' => 'query by meta key value' ).
	 * @var function
	 */
	public function get_reviews_comments( $post_id, $args = array() ) {

		global $wpdb;

		$post_id = (int) $post_id;

		if ( ! isset( $args['meta_key'] ) ) {
			$args['meta_key'] = $this->post_meta;
		}

		if ( ! isset( $args['status'] ) ) {
			$args['status'] = 'draft';
		}

		$request = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}pxreviews as reviews
				LEFT JOIN $wpdb->postmeta postmeta
				ON postmeta.post_id = reviews.post_id
				WHERE reviews.post_id = %d AND ( reviews.status = %s  OR reviews.status = 'unread' ) AND postmeta.meta_key = %s 
				ORDER BY reviews.review_date DESC",
			array(
				$post_id,
				$args['status'],
				$args['meta_key'],
			)
		);

		$results = wp_cache_get( md5( $request ), 'px_get_reviews' );

		if ( false === $results ) {

			$results = $wpdb->get_results( $request );
			wp_cache_add( md5( $request ), 'px_get_reviews' );
		}

		$reviews = array();

		$reviews_count = 0;

		foreach ( $results as $row ) {

			if ( '0' === $row->comment_parent && 'review' === $row->comment_type ) {

				$reviews_count++;
				$reviews['data'][ $row->reviewID ]['review'] = $row;

			}

			$reviews['count'] = $reviews_count;

		}

		foreach ( $results as $row ) {

			if ( 'reply' === $row->comment_type && '0' !== $row->comment_type ) {

				if ( ! isset( $reviews['data'][ $row->comment_parent ] ) ) { continue; }
				$reviews['data'][ $row->comment_parent ]['replies'][] = $row;
			}
		}

		return $reviews;

	}

	/**
	 * Get all reviews and replies list
	 *
	 * @access public
	 * @param int $limit The query limit.
	 * @param int $offset The query offset.
	 *
	 * @var function
	 */
	public function get_all_reviews_and_replies( $limit, $offset ) {

		global $wpdb;

		$rows_count_sql = "SELECT COUNT(reviewID) FROM {$wpdb->prefix}pxreviews";
		$rows_count = $wpdb->get_var( $rows_count_sql );

		if ( $limit > $rows_count ) {
			return false;
		}

		$prepared_statement = $wpdb->prepare(
			"SELECT reviews.reviewID, reviews.post_id, reviews.comment, reviews.user_ip, reviews.username, reviews.email, reviews.review_date, reviews.comment_type, reviews.status, posts.post_title
			 FROM {$wpdb->prefix}pxreviews as reviews
			 LEFT JOIN $wpdb->posts as posts
			 ON posts.ID = reviews.post_id
			 GROUP BY reviews.reviewID
			 ORDER BY case when reviews.status = 'unread' then 'active' else 'draft' end,
			 			reviews.review_date DESC
			 LIMIT %d, %d 
			",
			array(
				$limit,
				$offset,
			)
		);

		$results = wp_cache_get( md5( $prepared_statement ), 'px_get_all_reviews' );

		if ( false === $results ) {

			$results = $wpdb->get_results( $prepared_statement );
			wp_cache_set( md5( $prepared_statement ), 'px_get_all_reviews' );
		}

		if ( empty( $results ) || 0 === count( $results ) ) {
			return false;
		} else {
			return array( 'count' => $rows_count, 'data' => $results );
		}


	}

	/**
	 * Mark checked reviews as seen/read.
	 *
	 * @access public
	 * @param array $ids An array of affected ids.
	 *
	 * @var function
	 */
	public function mark_reviews_as_read( $ids = array() ) {

		global $wpdb;

		$count_ids = count( $ids );

		if ( 0 === $count_ids ) {
			return false;
		}

		$placeholders = array_fill( 0, $count_ids, '%d' );
		$format = implode( ',', $placeholders );

		$query = "UPDATE {$wpdb->prefix}pxreviews 
				  SET status='active' 
			      WHERE reviewID IN ($format)";

		$sql_update = $wpdb->prepare( $query, $ids );

		if ( $wpdb->query( $sql_update ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove checked reviews
	 *
	 * @access public
	 * @param array $ids An array of affected ids.
	 *
	 * @var function
	 */
	public function remove_reviews( $ids = array() ) {

		global $wpdb;

		$count_ids = count( $ids );

		if ( 0 === $count_ids ) {
			return false;
		}

		$placeholders = array_fill( 0, $count_ids, '%d' );
		$format = implode( ',', $placeholders );

		$query = "DELETE FROM {$wpdb->prefix}pxreviews  
			      WHERE reviewID IN ($format)";

		$sql = $wpdb->prepare( $query, $ids );

		if ( $wpdb->query( $sql ) ) {
			return true;
		}

		return false;
	}

}
