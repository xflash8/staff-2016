<?php
/* frontpage can be access by member only */
add_action('parse_query', function(){
	/* don't tocuh wp-admin */
	if(is_admin())
		return;
	
	/* do not touch wp-login.php page */
	if('/wp-login.php' == $_SERVER['SCRIPT_NAME'])
		return;
	
	global $current_blog, $current_user;
	
	if(is_user_logged_in()){
		/* only accessible to member of blog */
		if(!is_user_member_of_blog($current_user->ID, $current_blog->blog_id) && !current_user_can('manage_sites'))
			wp_die('<p>Maaf, anda tidak memiliki akses di portal ini.<br />Jika anda merasa ini salah, harap hubungi puskom@stiba.ac.id</p>');
	}else{
		$pagename = get_query_var('pagename');
		$white_list = array('sms-inbound');
		
		if(in_array($pagename, $white_list)){
			return;
		}else{
			get_template_part('login');
		}
		exit;
	}
});
