<?php
/* page as group
user can be added or remove
user can have view acces or full access */

add_action('parse_query', function(){
	if(!is_page())
		return;
	
	$pagename = get_query_var('pagename');
	
	
	$white_list = array('sms-inbound');
	/* View Level Access Checking */
	if(!current_user_can('view_'.$pagename) && !in_array($pagename, $white_list))
		wp_die('Maaf, anda tidak berhak melihat halaman ini.');
	
	$p = array('group-member', 'member-add', 'group-info', 'group-log');
	if(in_array($_GET['sub'], $p)){
		/* Editing Level Access Checking */
		if('member-add' == $_GET['sub']){
			if(!current_user_can('edit_'.$pagename))
				wp_die('Maaf, anda tidak punya akses di halaman ini');
		}
		
		
		/* display interface */
		if('group-member' == $_GET['sub'])
			$class = 'Group_Member_Card';
		else if('member-add' == $_GET['sub'])
			$class = 'Member_Add_Card';
		else if('group-log' == $_GET['sub'])
			$class = 'Group_Log_Card';
		else if('group-info' == $_GET['sub'])
			$class = 'Group_Info_Card';
		
		get_header();
		$member = new $class;
		$member->display();
		get_footer();
		exit;
	}
	
});

add_filter('card_header_menu', function($menu){
	$pagename = get_query_var('pagename');
	
		$menu[] = array(
			'icon'=>'accounts',
			'url'=>'?sub=group-member',
			'text'=>'Anggota Group',
			'cap'=>'view_'.$pagename
		);
	
		$menu[] = array(
			'icon'=>'account-add',
			'url'=>'?sub=member-add',
			'text'=>'Tambah Anggota',
			'cap'=>'edit_'.$pagename
		);
		
	return $menu;
});