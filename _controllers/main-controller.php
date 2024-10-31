<?php 

namespace PxReviews\Controller;

require_once PXPR_PLUGIN_PATH . '_models/main-model.php';

use PxReviews\Model\mainModel as PxReviewsMainModel;

/**
 * Class The main controller
 *
 * @category Controller
 * @package  PxprMain
 * @author  LP|PIXOLETTE
 * @license  http://www.pixollete.com
 * @link     http://www.pixollete.com
 **/
class PxprMainController {

	/**
	 * The post meta name of post reviews
	 *
	 * @access public
	 * @var string
	 */
	public $post_meta = 'px_reviews';

	/**
	 * The option name of plugin's options
	 *
	 * @access public
	 * @var string
	 */
	public $plugin_options = 'px_reviews_options';

	/**
	 * The main model Class
	 *
	 * @access public
	 * @var Class
	 */
	public $model;

	/**
	 * Will store the plugin's options
	 * Default settings, if user has not defined any settings...
	 *
	 * @access public
	 * @var array
	 */
	public $settings;

	/**
	 * Avoid reapeating the same global JS Object
	 *
	 * @access public
	 * @var boolean
	 */
	private $is_cdata_registered = false;

	/**
	 * The class constructor.
	 * Init the main Model.
	 * Init the plugin's settings.
	 *
	 * @var Function
	 */
	function __construct() {

		$model = new pxReviewsMainModel();
		$this->model = $model;

		$this->settings = $model->options_data;
	}

	/**
	 * Init the Wp backend functionalities
	 *
	 * @access public
	 * @var Function
	 */
	public function init_wp_backend() {

		$model = $this->model;
		$pages_count = 1;
		$page_link = admin_url() . 'admin.php?page=px_posts_reviews&px-reviews-tab=manage-reviews&rpage=';
		$settings = $this->settings;



		if ( isset( $_GET['px-reviews-tab'] ) ) {
			$active_tab = sanitize_text_field( wp_unslash( $_GET['px-reviews-tab'] ) );
		} else {
			$active_tab  = 'general-settings';
		}

		$post_types = get_post_types(

			array( 'public' => true ),
			'objects'

		);

		unset( $post_types['page'] );
		unset( $post_types['attachment'] );
		unset( $post_types['revision'] );
		unset( $post_types['nav_menu_item'] );

		switch ( $active_tab ) {

			default:
			case 'general-settings':

				$like_schemes_template = array(
					'stars' => array(
						array(
							'icon'  => 'stars-1.png',
							'value' => 'like-scheme-1',
						),
						array(
							'icon'  => 'stars-2.png',
							'value' => 'like-scheme-2',
						),
						array(
							'icon'  => 'stars-3.png',
							'value' => 'like-scheme-3',
						),
						array(
							'icon'  => 'stars-4.png',
							'value' => 'like-scheme-4',
						),
					),
					'hearts' => array(
						array(
							'icon'  => 'hearts-1.png',
							'value' => 'like-scheme-5',
						),
						array(
							'icon'  => 'hearts-2.png',
							'value' => 'like-scheme-6',
						),
						array(
							'icon'  => 'hearts-3.png',
							'value' => 'like-scheme-7',
						),
						array(
							'icon'  => 'hearts-4.png',
							'value' => 'like-scheme-8',
						),
					),
					'stars-2' => array(
						array(
							'icon'  => 'stars-2-1.png',
							'value' => 'like-scheme-9',
						),
						array(
							'icon'  => 'stars-2-2.png',
							'value' => 'like-scheme-10',
						),
						array(
							'icon'  => 'stars-2-3.png',
							'value' => 'like-scheme-11',
						),
						array(
							'icon'  => 'stars-2-4.png',
							'value' => 'like-scheme-12',
						),
					),
					'likes' => array(
						array(
							'icon'  => 'like-1.png',
							'value' => 'like-scheme-13',
						),
						array(
							'icon'  => 'like-2.png',
							'value' => 'like-scheme-14',
						),
					),
				);

				$voting_system = ( ( isset( $settings['display-voting-system'] ) ? $settings['display-voting-system'] : 0 ) );
				$reviews_system = ( ( isset( $settings['display-review-system'] ) ? $settings['display-review-system'] : 0 ) );
				$classic_stars = ( ( isset( $settings['display-classic-stars'] ) ? $settings['display-classic-stars'] : 0 ) );
				$active_scheme = ( isset( $settings['scheme-type'] ) ? $settings['scheme-type'] : 0 );
				$main_color = ( isset( $settings['main-color'] ) ? $settings['main-color'] : 0 );
				$like_scheme = ( isset( $settings['scheme-type'] ) ? $settings['scheme-type'] : 0 );

				break;

			case 'manage-reviews':

				$rows_per_page = 30;

				if ( isset( $_GET['rpage'] ) ) {
					$active_page = (int) $_GET['rpage'];
				} else {
					$active_page = 1;
				}

				$limit = ( ( $active_page - 1 ) * $rows_per_page );
				$offset = $rows_per_page;

				$data = $model->get_all_reviews_and_replies( $limit, $offset );

				if ( false !== $data ) {
					$pages_count = ceil( $data['count'] / $rows_per_page );
				} else {
					$pages_count = 1;
				}

				$reviews_data = $data['data'];
				break;
		}

		include PXPR_PLUGIN_PATH . '_views/plugin-backend.php';

	}

	/**
	 * Count post's reviews
	 *
	 * @param int $post_id The post ID.
	 * @access public
	 * @var static function
	 */
	public static function count_post_reviews( $post_id ) {

		global $wpdb;

		$count_sql = $wpdb->prepare(
			"SELECT COUNT(reviewID) 
				FROM {$wpdb->prefix}pxreviews
				WHERE post_id = %d AND comment_type = 'review' ",
			array( (int) $post_id )
		);

		$count = wp_cache_get( md5( $count_sql ), 'px_count_reviews' );

		if ( false === $count ) {
			$count = $wpdb->get_var( $count_sql );
			wp_cache_add( md5( $count_sql ), 'px_count_reviews' );
		}

		return $count;
	}

	/**
	 * Count all unread reviews
	 *
	 * @access public
	 * @var static function
	 */
	public static function count_unread_reviews() {

		global $wpdb;

		$count_sql = "SELECT COUNT(reviewID) 
					  FROM {$wpdb->prefix}pxreviews
					  WHERE status = 'unread' ";
		$count = $wpdb->get_var( $count_sql );

		return $count;
	}

	/**
	 * Init frontend functions
	 *
	 * @access public
	 * @var function
	 */
	public function init_frontend() {

		add_action( 'wp_head', array( $this, 'generate_dynamic_css' ) );

		if ( function_exists( 'add_filter' ) ) {

			if ( isset( $this->settings['display-voting-system'] ) && 1 == $this->settings['display-voting-system'] ) {
				add_filter( 'the_title', array( $this, 'load_reviews_view' ), 10, 2 );
			}
			if ( isset( $this->settings['display-review-system'] ) && 1 == $this->settings['display-review-system'] ) {
				add_filter( 'the_content', array( $this, 'load_reviews_after_content' ), 7 );
			}
		}

	}

	/**
	 * Init the reviews section.
	 * Display the review section under post title
	 *
	 * @param string $title The post title passed by add_filter hook.
	 * @param int    $id    The post ID.
	 * @access public
	 * @var function
	 */
	public function load_reviews_view( $title, $id ) {

		$model = $this->model;

		global $wp_query;

		if ( isset( $wp_query->comments ) || ! isset( $id ) ) {
			return $title;
		}

		$user_ip = px_get_user_ip();
		$key = str_replace( '.', '', $user_ip );

		$post = get_post( $id );


		if ( ! in_the_loop() ) { return $title; }

		if ( ! is_single() && ! is_singular() ) { return $title; }

		if ( isset( $_GET['post_id'] ) && $_GET['post_id'] != $id ) { return $title; }

		if ( ! isset( $_GET['post_id'] ) && ! preg_match( "/{$post->post_name}/i", $_SERVER['REQUEST_URI'] ) ) { return $title; }

		if ( ! isset( $this->settings['active_posts'] ) || ! in_array( $post->post_type, $this->settings['active_posts'] ) ) { return $title; }

		$show_reviews = ( isset( $this->settings['display-review-system'] ) && 1 == $this->settings['display-review-system'] ? 1 : 0 );
		$active_ips = $model->get_active_ips( $id );
		$votes_data = $model->get_votes( $id );
		$user_votes_data = @$votes_data['votes'][ $key ];

		$reviews_count = self::count_post_reviews( $id );

		if ( isset( $this->settings['scheme-type'] ) && ( ! isset( $this->settings['display-classic-stars'] ) ) ) {
			$scheme_class_name = $this->settings['scheme-type'];
		} elseif ( isset( $this->settings['scheme-type'] ) && ( isset( $this->settings['display-classic-stars'] ) && 1 != $this->settings['display-classic-stars'] ) ) {
			$scheme_class_name = $this->settings['scheme-type'];
		} else {
			$scheme_class_name = '';
		}


		$rating = ( ( (int) @$votes_data['votes_count'] ) > 0 ? (int) @$votes_data['votes_amount'] / $votes_data['votes_count'] : 0 );

		$user_has_voted = ( (false == $active_ips || false != $active_ips && ! in_array( $user_ip, $active_ips ) ) ? 0 : 1 );

		if ( ! $this->is_cdata_registered ) {

			$this->is_cdata_registered = true;

			wp_localize_script(
				'px_reviews_main',
				'pxReviewsData',
				array(
					'postID' => (int) $id,
					'rating' => esc_attr( $rating ),
					'userHasVoted' => esc_attr( $user_has_voted ),
					'voted' => (int) @$user_votes_data['vote'],
					'votes_count' => (int) @$votes_data['votes_count'],
					'comments_count' => (int) @$votes_data['comments_count'],
					'user_ip' => esc_attr( $key ),
					'show_reviews' => (int) $show_reviews,
					'scheme_class' => esc_attr( $scheme_class_name ),
				)
			);
		}

		ob_start();

		include_once PXPR_PLUGIN_PATH . '_views/reviews.php';

		$template = ob_get_clean();

		return $title . $template;

	}

	/**
	 * Init the reviews section.
	 * Display the review section under post title
	 *
	 * @param string $content The post content passed by add_filter hook.
	 * @access public
	 * @var function
	 */
	public function load_reviews_after_content( $content ) {

		$model = $this->model;

		$post = get_post( get_the_ID() );

		if ( ! is_single() && ! is_singular() ) { return $content; }

		if ( ! isset( $this->settings['active_posts'] ) || ! in_array( $post->post_type, $this->settings['active_posts'] ) ) { return $content; }

		$post_id = $post->ID;

		$reviews = $model->get_reviews_comments( $post_id, array( 'status' => 'active' ) );

		if ( isset( $this->settings['scheme-type'] ) && ( ! isset( $this->settings['display-classic-stars'] ) ) ) {
			$scheme_class_name = $this->settings['scheme-type'];
		} elseif ( isset( $this->settings['scheme-type'] ) && ( isset( $this->settings['display-classic-stars'] ) && 1 != $this->settings['display-classic-stars'] ) ) {
			$scheme_class_name = $this->settings['scheme-type'];
		} else {
			$scheme_class_name = '';
		}

		ob_start();

		include_once PXPR_PLUGIN_PATH . '_views/reviews-replies-list.php';

		$reviews_template = ob_get_clean();


		return $content . $reviews_template;

	}

	/**
	 * Plugin styles register
	 *
	 * @var function
	 */
	function px_reviews_style() {

		wp_enqueue_style( 'px-reviews-style', PXPR_PLUGIN_URL . 'assets/css/style.css', array(), '1.0' );

		$dynamic_style = $this->generate_dynamic_css();

		wp_add_inline_style( 'px-reviews-style', $dynamic_style );

	}

	/**
	 * Generate dynamic CSS. Get's the main color from backend and generates the css style code.
	 *
	 * @access public
	 * @var function
	 */
	public function generate_dynamic_css() {

		$dynamic_style = '';

		if ( isset( $this->settings['main-color'] ) ) :

			$dynamic_style = '
			.px-reviews-replies-list .review-headline strong{
				color:' . esc_attr( $this->settings['main-color'] ) . '
			}
			.px-reviews-replies-list .px-reply .username span{
				color:' . esc_attr( $this->settings['main-color'] ) . '
			}
			.px-reviews-replies-list .px-submit-reply{
				background:' . esc_attr( $this->settings['main-color'] ) . '
			}
			.px-post-review-wrapper .px-review-box button{
				background:' . esc_attr( $this->settings['main-color'] ) . '
			}
			.px-post-review-wrapper .px-review-headline .review-headline-title label{
				color:' . esc_attr( $this->settings['main-color'] ) . '
			}';

		endif;

		return $dynamic_style;
	}

}
