<?php
add_action('init', function(){
	if(!$_GET['ajax'])
		return;
	global $wpdb;
	extract($_REQUEST);
	
	$q = "
		select 
			users.ID, 
			users.display_name,
			meta.meta_value nim,
			concat('http://staff.stiba.ac.id/wp-content/themes/staff-2016/img/profile-pics/1.jpg') img 
		
		from $wpdb->users users
		left join $wpdb->usermeta meta
		on meta.user_id = users.ID
		
		where 
			(display_name like '%$search%' or  meta.meta_value like '%$search%')
			and meta.meta_key = '_nim_baru'
			
		limit 0,10
	";
	$r = $wpdb->get_results($q);
	
	header('Content-Type: application/json');
	echo json_encode($r);
	exit;
		
});