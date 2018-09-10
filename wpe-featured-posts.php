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
	}

	public function output_styles() {
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
	public function post_submitbox_misc_actions() {
		global $post;
		$post_meta = get_post_meta( $post->ID, '_wpe_featured_post', true );
		echo '<div class="misc-pub-section wpe-featured-post">';
		echo '<span id="wpe-featured-post">';
		printf( '<input id="wpe-featured-post-checkbox" name="wpe-featured-ppst" type="checkbox" value="%s" %s />', esc_attr( $post->ID ), checked( true, $post_meta, false ) );
		printf( '<label for="wpe-featured-post-checkbox" class="selectit">%s</label>', esc_html__( 'Feature on WPEngine', 'wpe-featured-posts' ) );
		echo '</span>';
		echo '</div>';
	}
}
add_action( 'plugins_loaded', function() {
	new WPE_Featured_Posts();
} );