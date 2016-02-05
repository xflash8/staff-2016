<?php
class Bank_Mahasiswa_List_Table extends List_Table{
	var $columns = array(
		'index'=>'#',
		'nim_baru'=>'NIM',
		'display_name'=>'Nama Lengkap', 
		'tempat_lahir'=>'Tempat Lahir', 
		'tanggal_lahir'=>'Tanggal Lahir', 
		'gender'=>'L/P',
		'user_id'=>'UID',
	);
	
	var $actions = array(
		'set_alumni'=>array(
			'text'=>'Set Alumni',
			'cap'=>'edit-alumni',
		),
	);		
	
	var $max = 10;
	
	function query()
	{
		global $wpdb;
    extract($_REQUEST);
		
		$b = new Query_Builder();
		$b->set_main_table("$wpdb->usermeta meta")
        -> select(array(
				'meta.user_id',
				'meta.meta_value nim_baru',
				'substr(meta.meta_value, 6, 1) gender',
        ))
        ->join('user', "left join $wpdb->users users on users.ID = meta.user_id")
        ->select_add('user', "users.display_name")
    
        ->join("maba", "left join $wpdb->usermeta meta3 on meta3.user_id = meta.user_id and meta3.meta_key = '_2015_1_maba'")
        ->select_add('maba', "meta3.meta_value maba")
    
        ->where('main', "meta.meta_key = '_nim_baru'")
        ->search('gender', " and substr(meta.meta_value, 6, 1) = $gender")
        ->search('src', " and (users.display_name like '%$src%' or meta.meta_value  like '%$src%')")
        ->order("nim_baru ASC")
        ->limit($this->limit_query());
		$r = $wpdb->get_results($b->build());
		    
    $b->remove('order')
        ->remove('limit')
        ->remove_where('gender')
        ->select(array("count(*)"));
		$this->total = $wpdb->get_var($b->build());
        
    $b->select(array("substr(meta.meta_value, 6, 1) gender", "count(substr(meta.meta_value, 6, 1)) total"))
        ->groupby("substr(meta.meta_value, 6, 1)");
		$this->subs = $wpdb->get_results($b->build());
		
		return $r;
	}
	
	function column_display_name($item){
		echo $item->display_name.' '.$item->maba;
	}
	
}