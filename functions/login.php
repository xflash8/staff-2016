<?php
/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function my_login_redirect( $redirect_to, $request, $user ) {
	//is there a user to check?
	
	if(!is_user_logged_in())
		$redirect_to;
	
	$hp = get_user_meta($user->ID, 'phone', true);
	$sms = new Sms;
	$r = $sms->send($hp, "Notifikasi Login\nYth $user->display_name, Anda baru saja login. Kalau itu bukan anda, harap laporkan pada admin.\nstaff.stiba.ac.id");
	return $redirect_to;
}

add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );