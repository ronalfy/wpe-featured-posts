<?php
/**
 * Plugin Name:       WPE Featured Posts
 * Plugin URI:        https://github.com/ronalfy/wpe-featured-posts
 * Description:       Select posts to be featured on WPEngine.
 * Version:           1.0.0
 * Author:            Ronald Huereca
 * Author URI:        https://mediaron.com
 * Text Domain:       wpe-featured-posts
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

class WPE_Featured_Posts {

	public function __construct() {
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'admin_head', array( $this, 'output_styles' ) );
		add_action( 'save_post', array( $this, 'maybe_save_wpe_featured' ) );
		add_action( 'rest_api_init', array( $this, 'rest_api_register' ) );		
	}

	/**
	 * Register our route.
	 *
	 * Register route for getting posts that are featured.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function rest_api_register() {
		register_rest_route( 
			'wpe-featured/v1', 
			'/get',
			array(
				'methods' => 'GET',
				'callback' =>  array( $this, 'rest_return_featured_posts' )
			)
		);
	}

	/**
	 * Fetch posts with the WPE featured meta key.
	 *
	 * Fetch posts with the WPE featured meta key.
	 *
	 * @since 1.0.0
	 *
	 * @return JSON Found posts in JSON format
	 */
	public function rest_return_featured_posts() {
		$posts_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_key'       => '_wpe_featured_post',
			'meta_value'     => 'true'
		);
		$query_results = get_posts( $posts_args );
		foreach( $query_results as &$post ) {
			$post->permalink = get_permalink( $post->ID );
			$post->featured_image = get_the_post_thumbnail_url( $post );
		}
		wp_send_json( $query_results );
	}

	/**
	 * Saves the post meta for featured WP Engine featured.
	 *
	 * Saves the post meta for featured WP Engine featured.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_save_wpe_featured( $post_id ) {
		if ( 'post' !== get_post_type() ) return;
		if ( wp_is_post_revision( $post_id ) ) return;

		if( isset( $_POST['wpe-featured-post'] ) ) {
			$value = $_POST['wpe-featured-post'];
			if ( '0' === $value ) {
				delete_post_meta( $post_id, '_wpe_featured_post' );
			} else {
				update_post_meta( $post_id, '_wpe_featured_post', 'true' );
			}
		}

	}

	/**
	 * Outputs styles for the WPEngine featured icon.
	 */
	public function output_styles() {
		if ( 'post' !== get_post_type() ) return;
		?>
		<style>
		#wpe-featured-post:before {
			content: "\f237";
			color: #82878c;
			position: relative;
			top: -1px;
			font: 400 20px/1 dashicons;
			speak: none;
			display: inline-block;
			margin-left: -1px;
			padding-right: 3px;
			vertical-align: top;
			-webkit-font-smoothing: antialiased;
		}
		</style>
		<?php
	}

	/**
	 * Outputs WPEngine sharing interface.
	 */
	public function post_submitbox_misc_actions() {
		global $post;
		if( 'post' !== get_post_type() ) return;
		$post_meta = get_post_meta( $post->ID, '_wpe_featured_post', true );
		echo '<div class="misc-pub-section wpe-featured-post">';
		echo '<span id="wpe-featured-post">';
		echo '<input name="wpe-featured-post" type="hidden" value="0" />';
		printf( '<input id="wpe-featured-post-checkbox" name="wpe-featured-post" type="checkbox" value="%s" %s />', esc_attr( $post->ID ), checked( 'true', $post_meta, false ) );
		printf( '<label for="wpe-featured-post-checkbox" class="selectit">%s</label>', esc_html__( 'Feature on WPEngine', 'wpe-featured-posts' ) );
		echo '</span>';
		echo '</div>';
	}
}
add_action( 'plugins_loaded', function() {
	new WPE_Featured_Posts();
} );