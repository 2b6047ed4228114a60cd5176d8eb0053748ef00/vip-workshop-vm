<?php

// On my machine, this state somehow gets intermittently changed
// Possibly some OPCache bug?
// Anyway - normalize here just in case
libxml_disable_entity_loader( false );

class VIP_Test {
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'loop_end', array( $this, 'form' ) );

		add_action( 'admin_post_test_exploit', array( $this, 'handle' ) );
		add_action( 'admin_post_nopriv_test_exploit', array( $this, 'handle' ) );
	}

	function enqueue() {
		wp_enqueue_style( 'vip-test', plugins_url( '/test.css', __FILE__ ), array(), '20140321' );
		wp_enqueue_script( 'vip-test', plugins_url( '/test.js', __FILE__ ), array( 'jquery' ), '20140321', true );
	}

	function form( $current_query ) {
		$page = $current_query->get( 'page' );

		if ( ! $current_query->is_singular() || $page < 2 ) {
			return;
		}

		$name = $current_query->get( 'name' );

		switch ( $page ) {
		case 2:
			$type = "attack";
			$title = "Verify Exploit";
			break;
		case 3:
			$type = "defend";
			$title = "Verify Fix";
			break;
		}
	?>
		<form id="vip-test" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="submit" value="<?php echo esc_attr( $title ); ?>" />
			<input type="hidden" name="action" value="test_exploit" />
			<input type="hidden" name="test" value="<?php echo esc_attr( $name ); ?>" />
			<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />
			<?php wp_nonce_field( 'test', 'test_nonce' ); ?>
		</form>
	<?php
	}

	function handle() {
		check_admin_referer( 'test', 'test_nonce' );

		$test = preg_replace( '/[^a-z0-9-]/i', '', $_POST['test'] );

		$image = dirname( ABSPATH ) . "/attacks/{$test}.png";

		@unlink( $image );

		$proc = proc_open(
			sprintf( '%s %s %s', dirname( ABSPATH ) . '/.tests/test', escapeshellarg( $test ), escapeshellarg( $_POST['type'] ) ),
			array(
				0 => array( 'pipe', 'r' ),
				1 => array( 'pipe', 'w' ),
				2 => array( 'pipe', 'w' ),
			),
			$pipes,
			dirname( ABSPATH ),
			array(
				'PATH' => '/usr/local/node/node-default/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/opt/vagrant_ruby/bin',
			)
		);

		$out = stream_get_contents( $pipes[1] );
		$err = stream_get_contents( $pipes[2] );

		echo "<pre>";
		echo $this->cli_to_html( $out );
		echo "</pre>";

		echo "<p><a href='" . esc_url( wp_get_referer() ) . "'>Go Back</a>.</p>";

		if ( file_exists( $image ) ) {
			echo '<p>What the verifier saw:</p>';
			echo '<img src="' . esc_url( home_url( "attacks/{$_POST['test']}.png" ) ) . '" width="800" />';
		} else if ( '15-configuration' === $test && 'attack' === $_POST['type'] ) {
			echo '<p>What the verifier saw:</p>';
			echo '<img src="' . esc_url( plugins_url( 'nyan.gif', __FILE__ ) ) . '" />';
			echo '<audio autoplay loop src="' . esc_url( plugins_url( 'nyan.mp3', __FILE__ ) ) . '"></audio>';
		}

		exit;
	}

	// Doesn't properly handle nested
	function cli_to_html( $text ) {
		$text = htmlspecialchars( $text, ENT_QUOTES );
		$text = preg_replace_callback( '/\033\[([^m]*)m(.*?)\033\[0m/s', array( $this, 'cli_to_html_callback' ), $text );
		return make_clickable( $text );
	}

	function cli_to_html_callback( $matches ) {
		$colors = explode( ';', $matches[1] );
		$prefix = '';
		$suffix = '';

		foreach ( $colors as $color ) {
			switch ( $color ) {
			case 1:
				$prefix .= "<span style='font-weight: bold'>";
				break;
			case 31:
				$prefix .= "<span style='color: red'>";
				break;
			case 32:
				$prefix .= "<span style='color: green'>";
				break;
			case 34:
				$prefix .= "<span style='color: blue'>";
				break;
			default:
				continue 2;
			}
			$suffix .= "</span>";
		}

		return $prefix . $matches[2] . $suffix;
	}
}

new VIP_Test;
