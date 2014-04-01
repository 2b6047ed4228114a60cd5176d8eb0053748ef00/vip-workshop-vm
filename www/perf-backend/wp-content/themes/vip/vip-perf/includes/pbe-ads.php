<?php // Ads

add_action( 'wp_ajax_pbe_load_ad', 'pbe_load_ad' );
add_action( 'wp_ajax_nopriv_pbe_load_ad', 'pbe_load_ad' );

function pbe_load_ad() {
	$ad_url = add_query_arg( array(
		'id' => urlencode( $_GET['id'] ),
		'pos' => urlencode( $_GET['pos'] ),
		'size' => urlencode( $_GET['size'] ),
	), plugins_url( '', __FILE__ ) . '/ad.jpg' );

	$rand = (int) ( isset( $_GET['rand'] ) ) ? $_GET['rand'] : rand( 0, 1000000000 );
	?>
	<div style="width: 300px; height: 250px; background: pink;">Advertising</div>
	<script>var ad_rand = '<?php echo esc_js( $rand ) ?>';</script>
	<?php
	exit;
}

function pbe_show_ad( $id, $pos, $size ) {
	?>
	<iframe src="<?php echo esc_url( pbe_get_ajax_ad_url( $id, $pos, $size ) ); ?>" width="300" height="250" scrolling="no" style="width: 300px; height: 250px; display: block; margin: 0 auto;"></iframe>
	<?php
}

function pbe_get_ajax_ad_url( $id, $pos, $size ) {
	$ajax_ad_url = add_query_arg( array(
		'action' => 'pbe_load_ad',
		'id' => urlencode( $id ),
		'pos' => urlencode( $pos ),
		'size' => urlencode( $size ),
		'rand' => rand( 0, 1000000000 ),
	), site_url( '/wp-admin/admin-ajax.php' ) );

	return $ajax_ad_url;
}
