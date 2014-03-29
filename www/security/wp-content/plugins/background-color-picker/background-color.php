<?php

/*
 * Plugin Name: Background Color Picker
 * Description: Allows visitors to pick a background color when viewing your site.
 * Author: Vulnerabilities Inside Plugins
 * Version: 0.0.1
 */

defined( 'VIP_BACKGROUND_COLOR_PICKER__KEY' ) or define( 'VIP_BACKGROUND_COLOR_PICKER__KEY', 'secret-color-key' );

require dirname( __FILE__ ) . '/widget.php';
require dirname( __FILE__ ) . '/color-functions.php';

class VIP_Background_Color_Picker {
	var $color;

	static function instance() {
		static $instance;

		if ( ! $instance ) {
			$instance = new VIP_Background_Color_Picker;
		}

		return $instance;
	}

	private function __construct() {
		if ( isset( $_COOKIE['color'] ) ) {
			$this->color = trim( $_COOKIE['color'] );
			$this->reason = $this->get_color_reason( $this->color );
		} else {
			$this->color = '#ffffff';
			$this->reason = '';
		}

		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		// For clients with no JS
		add_action( 'admin_post_vip_background_color_pick', array( $this, 'handle_submit' ) );
		add_action( 'admin_post_nopriv_vip_background_color_pick', array( $this, 'handle_submit' ) );

		// For clients with JS
		add_action( 'wp_ajax_save_color', array( $this, 'save_color_ajax' ) );
		add_action( 'wp_ajax_nopriv_save_color', array( $this, 'save_color_ajax' ) );

		// AJAX polling for clients with JS
		add_action( 'wp_ajax_recent_colors', array( $this, 'get_recent_colors_ajax' ) );
		add_action( 'wp_ajax_nopriv_recent_colors', array( $this, 'get_recent_colors_ajax' ) );

		add_action( 'admin_post_vip_background_color_image', array( $this, 'handle_image' ) );
		add_action( 'admin_post_nopriv_vip_background_color_image', array( $this, 'handle_image' ) );

		// Set our widget's title to the current visitor's color preference
		add_filter( 'widget_display_callback', array( $this, 'widget_filter' ), 10, 2 );

		add_action( 'wp_head', array( $this, 'css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'js' ) );
	}

	/**
	 * The widget class is defined in widget.php, though most of the functionality is handled by the
	 * methods in this file.
	 */
	function register_widget() {
		register_widget( 'VIP_Background_Color_Picker_Widget' );
	}

/* Recent Colors DB */
	function get_recent_colors( $limit = 6 ) {
		global $wpdb;

		return $wpdb->get_results( "SELECT `value`, `reason` FROM `{$wpdb->prefix}recent_colors` ORDER BY `time` DESC LIMIT $limit" );
	}

	function get_colors_like_mine( $limit = 6 ) {
		$colors = $this->get_recent_colors( 100 );
		$colors = array_filter( $colors, create_function( '$a', 'return color_similarity( "$a->value", "' . $this->color . '" ) < 0.2;' ) );
		return array_slice( $colors, 0, $limit );
	}

	function get_color_reason( $color ) {
		global $wpdb;

		return $wpdb->get_var( "SELECT `reason` FROM `{$wpdb->prefix}recent_colors` WHERE `value` = '$color'" );
	}

	function add_recent_color( $value, $reason = '' ) {
		global $wpdb;

		if ( $reason ) {
			$on_duplicate = "ON DUPLICATE KEY UPDATE `reason` = VALUES( `reason` )";
			$ignore = '';
		} else {
			$on_duplicate = '';
			$ignore = 'IGNORE';
		}

		return $wpdb->query( $wpdb->prepare( "INSERT $ignore INTO `{$wpdb->prefix}recent_colors` ( `value`, `reason` ) VALUES ( '$value', '$reason' ) $on_duplicate" ) );
	}

	function delete_recent_colors() {
		global $wpdb;

		return $wpdb->query( "DELETE FROM `{$wpdb->prefix}recent_colors`" );
	}

/* HTTP Endpoints */
	function handle_submit() {
		$request = wp_unslash( $_REQUEST );

		if ( isset( $request['clear'] ) ) {
			$this->delete_recent_colors();
			setcookie( 'color', ' ', strtotime( '-1 year' ), COOKIEPATH );
		} else if ( isset( $request['value'] ) ) {
			setcookie( 'color', $request['value'], strtotime( '+1 year' ), COOKIEPATH );
			$this->add_recent_color( $request['value'], isset( $request['reason'] ) ? $request['reason'] : '' );
		}

		wp_redirect( $request['referer'] );
		exit;
	}

	// All the cool kids use XML for ajaX
	function get_recent_colors_ajax() {
		if ( isset( $_GET['like_my_color'] ) ) {
			$recent_colors = $this->get_colors_like_mine();
		} else {
			$recent_colors = $this->get_recent_colors();
		}

		header( 'Content-Type: text/xml' );
		die( $this->colors_xml( $recent_colors ) );
	}

	// All the cool kids still use XML for ajaX
	function save_color_ajax() {
		$dom = new DOMDocument();

		// No network access, no entities
		$dom->loadXML( file_get_contents( "php://input" ), LIBXML_NONET | LIBXML_NOENT );

		$colors = $dom->getElementsByTagName( 'color' );
		if ( ! $colors->length ) {
			status_header( 400 );
			exit;
		}

		$color = $colors->item( 0 );

		$value = $color->getElementsByTagName( 'value' );
		if ( ! $value->length ) {
			status_header( 400 );
			exit;
		} else {
			$value = $value->item( 0 )->textContent;
		}

		$reason = $color->getElementsByTagName( 'reason' );
		if ( ! $reason->length ) {
			$reason = '';
		} else {
			$reason = $reason->item( 0 )->textContent;
		}

		setcookie( 'color', $value, strtotime( '+1 year' ), COOKIEPATH );
		$this->add_recent_color( $value, $reason );

		header( 'Content-Type: text/xml' );
		die( $this->colors_xml( array( (object) compact( 'value', 'reason' ) ) ) );
	}

	private function colors_xml( $colors ) {
		$xml = '<colors>';
		foreach ( $colors as $color ) {
			$hash = $this->color_hash( $color );
			$xml .= "<color><value><![CDATA[$color->value]]></value><reason><![CDATA[$color->reason]]></reason><hash>$hash</hash></color>";
		}
		$xml .= '</colors>';
		return $xml;
	}

	function handle_image() {
		$request = wp_unslash( $_REQUEST );

		header_remove( 'Pragma' );

		header( 'Content-Type: image/png' );
		header( 'Cache-Control: public; max-age=31536000' );
		header( 'Expires: ' . gmdate( 'r', strtotime( '+1 year' ) ) );

		if ( $error = ! array_key_exists( 'value', $request ) )
			goto fail;
		if ( $error = ! strlen( $request['value'] ) )
			goto fail;
		if ( $error = ! array_key_exists( 'hash', $request ) )
			goto fail;
			goto fail;
		if ( $error = ! strlen( $request['hash'] ) )
			goto fail;

		$error = ! $this->verify_color_hash( $_SERVER['QUERY_STRING'] );

		fail:

		if ( $error ) {
			status_header( 400 );
			exit;
		}

		chdir( dirname( ABSPATH ) );
		passthru( sprintf( '%s %s %s', './bin/image', escapeshellarg( $request['value'] ), escapeshellarg( $request['reason'] ) ) );
		exit;
	}

/* Output */
	/**
	 * Sets our widget's title to be the color preference of the current visitor
	 */
	function widget_filter( $instance, $widget ) {
		if ( ! ( $widget instanceof VIP_Background_Color_Picker_Widget ) ) {
			return $instance;
		}

		if ( $this->reason ) {
			$instance['title'] = $this->capitalize_color_names( $this->reason );
		}

		return $instance;
	}

	/**
	 * CAPITALIZES the color names the given text
	 */
	function capitalize_color_names( $text ) {
		$colors = include dirname( __FILE__ ) . '/colors.php';
		$color_regex = join( '|', array_keys( $colors ) );
		return preg_replace( "/\S*(?:$color_regex)\S*/ie", 'strtoupper( "\\0" )', $text );
	}

	/**
	 * The widget contents
	 */
	function form() {
		if ( isset( $_GET['like_my_color'] ) ) {
			$recent_colors = $this->get_colors_like_mine();
		} else {
			$recent_colors = $this->get_recent_colors();
		}
?>
		<form id="vip-background-color-picker" method="POST" action="<?php echo admin_url( 'admin-post.php?action=vip_background_color_pick' ); ?>">
			<p>
				<input type="color" id="vip-background-color-picker-input" name="value" placeholder="#123456" value="<?php echo $this->color; ?>" />
				<input type="text" id="vip-background-color-picker-reason" name="reason" placeholder="Why?" value="" />
			</p>
			<p class="submit">
				<input type="hidden" name="referer" value="<?php
					printf( '%s://%s%s', is_ssl() ? 'https' : 'http', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] );
				?>" />
				<input type="submit" value="Save!" />
				<input type="submit" name="clear" value="Clear" />
			</p>

			<h2>Recent Colors<?php if ( $this->color ) echo ' <a href="?like_my_color">(like mine)</a>'; ?></h2>
			<ol class="recent-colors"><?php foreach ( $recent_colors as $recent_color ) : ?>
				<li><a href="#"><img src="<?php echo $this->color_url( $recent_color ); ?>" data-value="<?php echo $recent_color->value; ?>" /></a>
			<?php endforeach; ?></ol>
		</form>
<?php
	}

	private function sign( $string ) {
		$string = urldecode( $string );
		return md5( VIP_BACKGROUND_COLOR_PICKER__KEY . $string );
	}

	private function color_hash( $color = null ) {
		if ( ! $color ) {
			return false;
		}

		// Sign the whole query string
		return $this->sign( http_build_query( array(
			'action' => 'vip_background_color_image',
			'value' => $color->value,
			'reason' => $color->reason
		) ) );
	}


	private function verify_color_hash( $query_string ) {
		list ( $signed, $hash ) = explode( '&hash=', $query_string );

		$expected_hash = $this->sign( $signed );

		return $expected_hash == $hash;
	}

	private function color_url( $color = null ) {
		$url = admin_url( 'admin-post.php?action=vip_background_color_image' );
		if ( ! $color ) {
			return $url;
		}

		return $url
			. '&value='  . ( $color ? urlencode( $color->value )  : '' )
			. '&reason=' . ( $color ? urlencode( $color->reason ) : '' )
			. '&hash='   . $this->color_hash( $color );
	}

	// Our CEO loves color names. Don't ask.
	private function get_color_name( $color ) {
		$colors = include dirname( __FILE__ ) . '/colors.php';

		$name = array_search( trim( $color, '#' ), $colors );
		if ( $name ) {
			return $name;
		}

		return $color;
	}

	function css() {
		// Layout, etc.
		wp_enqueue_style( 'vip-background-color', plugins_url( '/style.css', __FILE__ ) );

		// Dynamic CSS rules:
?>
<style>
div.site,
.site-content .entry-header,
.site-content .entry-content,
.site-content .entry-summary,
.page-content,
.page-links > span {
	background-color: <?php echo $this->get_color_name( $this->color ); ?>;
	border-color: <?php echo $this->get_color_name( $this->color ); ?>;
	color: <?php echo $this->get_color_name( contrasting_color_YIQ( $this->color ) ); ?>;
}
<?php if ( '#ffffff' !== $this->color ) : ?>
.site-content .entry-content a:link {
	color: <?php echo $this->get_color_name( complementary_color( $this->color ) ); ?>;
}
.site-content .entry-content a:hover,
.site-content .entry-content a:active {
	color: <?php echo $this->get_color_name( contrasting_color_YIQ( $this->color ) ); ?>;
}
<?php endif; ?>
</style>
<?php
	}

	function js() {
		wp_enqueue_script( 'vip-background-color', plugins_url( '/script.js', __FILE__ ), array( 'jquery' ), '20140323', true );
		wp_localize_script( 'vip-background-color', 'VIP_Color_Picker', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'color_url' => $this->color_url(),
			'cookie_path' => COOKIEPATH,
		) );
	}
}

VIP_Background_Color_Picker::instance();

function vip_background_color_picker_activate() {
	global $wpdb;

	$wpdb->query( "
		CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}recent_colors` (
		 `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		 `value` VARCHAR(200) NOT NULL DEFAULT '',
		 `reason` VARCHAR(200) NOT NULL DEFAULT '',
		 `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		 PRIMARY KEY (`id`),
		 UNIQUE KEY value (`value`)
		)
	" );
}

register_activation_hook( __FILE__, 'vip_background_color_picker_activate' );
