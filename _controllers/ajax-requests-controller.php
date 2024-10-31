<?php

namespace PxReviews\Controller;

require_once PXPR_PLUGIN_PATH . '_models/main-model.php';

use PxReviews\Model\mainModel as PxPreviewsMainModel;

/**
 * Class AjaxRequestController used for jquery ajax requests
 *
 * @category Controller
 * @package  AjaxRequest
 * @author  LP|PIXOLETTE
 * @license  http://www.pixollete.com
 * @link     http://www.pixollete.com
 **/
class AjaxRequestsController {

	/**
	 * The main function that is initialized via add_action hook
	 *
	 * @access public
	 * @var function|method
	 */
	public function init() {

		if ( ! isset( $_POST['section'] ) ) {
			die();
		}

		$data = array();

		if ( isset( $_POST['data'] ) ) {


			foreach ( $_POST['data'] as $key => $row_data ) {

				if ( is_array( $row_data ) ) {
					$data[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $row_data ) );

				} else {
					$data[ $key ] = sanitize_text_field( wp_unslash( $row_data ) );
				}
			}
		}

		$model = new PxPreviewsMainModel();

		switch ( $_POST['section'] ) {

			case 'save_options':

				$options = array();
				$options['display-voting-system'] = 0;
				$options['display-review-system'] = 0;
				$options['display-classic-stars'] = 0;

				foreach ( $data as $key => $values ) {

					if ( 'px_reviews_post_types' === $key ) {

						foreach ( $values as $value ) {
							$options['active_posts'][] = $value;
						}
					} else {
						$options[ $key ] = $values;
					}
				}

				$model->save_options( $options );

				echo wp_json_encode(
					array( 'results' => $options, 'status' => 1 )
				);

				die();

				break;

			case 'mark_reviews_as_read':

				$checked_ids = $data;

				if ( $model->mark_reviews_as_read( $checked_ids ) ) {
					echo wp_json_encode( array(
						'status' => 1,
						)
					);
				} else {
					echo wp_json_encode( array(
						'status' => 0,
						)
					);
				}

				die();
				break;

			case 'remove_reviews':

				$checked_ids = $data;

				if ( $model->remove_reviews( $checked_ids ) ) {
					echo wp_json_encode( array(
						'status' => 1,
						)
					);
				} else {
					echo wp_json_encode( array(
						'status' => 0,
						)
					);
				}

				die();
				break;

			case 'vote':

				$rating = (int) $data['rating'];
				$post_id = (int) $data['postID'];


				$user_ip = px_get_user_ip();
				$active_ips = $model->get_active_ips( $post_id );

				if ( '' == $active_ips ) { $active_ips = array(); }

				if ( false === $active_ips || false !== $active_ips && ! in_array( $user_ip, $active_ips, true ) ) {

					$review = array();

					if ( isset( $data['rating'] ) ) {
						$review['vote'] = (int) $data['rating'];
					}

					if ( isset( $data['comment'] ) ) {
						$review['comment'] = $data['comment'];
					}
					if ( isset( $data['user'] ) ) {
						$review['user'] = $data['user'];
					}

					$model->update_ips_list( $post_id, $user_ip );

					$results = $model->update_post_votes( $post_id, $user_ip, $review );

					echo $results;


				}


			die();
			break;


			case 'submit_review':

				if ( ! isset( $_POST['postID'] ) || 0 === (int) $_POST['postID'] ) {
					die();
				}

				if ( ! isset( $_POST['type'] ) ) {
					$comment_type = 'review';
				} else {
					$comment_type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
				}

				$user_ip = px_get_user_ip();

				$review = array();
				$review['date'] = date( 'Y-m-d G:i:s', time() );

				$fields = $data;
				$post_id = (int) $_POST['postID'];

				if ( ! is_array( $fields ) ) {
					die();
				}

				foreach ( $fields as $field ) {

					switch ( $field['name'] ) {

						case 'email-address':

							$review['email'] = sanitize_email( $field['value'] );

							break;

						case 'username':

							$review['name'] = $field['value'];

							break;

						case 'review-message':

							$review['comment'] = $field['value'];

							break;

					}
				}

				switch ( $comment_type ) {

					case 'review':

						global $wpdb;

						$sql = $wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}pxreviews 
								( `post_id`, `comment`, `user_ip`, `email`, `username`, `review_date`, `comment_parent`, `comment_type`, `status` ) 
								VALUES ( %d, %s, %s, %s, %s, %s, %d, %s, %s)",
							array(
								$post_id,
								$review['comment'],
								$user_ip,
								$review['email'],
								$review['name'],
								$review['date'],
								0,
								'review',
								'active',
							)
						);

						$wpdb->query( $sql );

						$sql_last_inserted = $wpdb->prepare(
							"SELECT reviews.reviewID, postmeta.meta_value FROM {$wpdb->prefix}pxreviews as reviews
							LEFT JOIN {$wpdb->postmeta} as postmeta
							ON reviews.post_id = postmeta.post_id
							WHERE reviews.post_id = %d AND reviews.user_ip = %s AND reviews.username = %s AND reviews.email = %s AND review_date = %s AND postmeta.meta_key = %s ",
							array(
								$post_id,
								$user_ip,
								$review['name'],
								$review['email'],
								$review['date'],
								'px_reviews',
							)
						);

						$sql_reviews_count = $wpdb->prepare(
							"SELECT count(post_id)  FROM {$wpdb->prefix}pxreviews 
								WHERE post_id = %d and status = %s and comment_type = %s",
							array(
								$post_id,
								'unread',
								'review',
							)
						);

						$count_reviews = $wpdb->get_var( $sql_reviews_count );

						// update reviews count to votes json object -> postmeta.
						$model->update_post_votes( $post_id, $user_ip, array( 'reviews_count' => $count_reviews ) );

						// get last Inserted ID from reviews.
						$last_insert_id = $wpdb->get_results( $sql_last_inserted );
						$meta_value = $last_insert_id[0]->meta_value;

						if (  '' !== $meta_value  ) {

							$u_votes = stripslashes_deep( json_decode( $meta_value, true ) );

							$key = str_replace( '.', '', px_get_user_ip() );
							$u_votes = $u_votes['votes'][ $key ];

							$review['user_vote'] = $u_votes['vote'];
						}

						$review['last_inserted_id'] = $last_insert_id[0]->reviewID;
						echo wp_json_encode( $review );

						break;

					case 'reply':

						if ( ! isset( $_POST['parent'] ) ) { die(); }

						global $wpdb;

						$sql = $wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}pxreviews 
								( `post_id`, `comment`, `user_ip`, `email`, `username`, `review_date`, `comment_parent`, `comment_type`, `status` ) 
								VALUES ( %d, %s, %s, %s, %s, %s, %d, %s, %s)",
							array(
								$post_id,
								$review['comment'],
								$user_ip,
								$review['email'],
								$review['name'],
								$review['date'],
								(int) $_POST['parent'],
								'reply',
								'unread',
							)
						);

						$wpdb->query( $sql );

						echo wp_json_encode( $review );

						break;

				}

			die();
			break;

		}

		die();

	}

}
