<?php
class Rekap_Spp_List_Table extends List_Table{
	var $columns = array(
		'index'=>'#',		
		'nim'=>'NIM',
		'display_name'=>'Nama Lengkap',
		'spp'=>'SPP',
		'pembayaran'=>'Pembayaran',
		'kali'=>'x',
		'sisa'=>'Sisa'
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
		
		$q['select'] = array(
				'meta.user_id',
				'meta.meta_value nim_baru',
				'substr(meta.meta_value, 6, 1) gender',
		);
		
		$q['join'][] = "left join $wpdb->users users on users.ID = meta.user_id";
		$q['select'][] = "users.display_name";
				
		$q['join'][] = "left join $wpdb->usermeta meta3 on meta3.user_id = meta.user_id and meta3.meta_key = '_2015_1_maba'";
		$q['select'][] = "meta3.meta_value maba";
		
		
		$q['join'] = implode(' ', $q['join']);
		$q['select'] = "select " . implode(', ', $q['select']) . " from $wpdb->usermeta meta";
		
			
		$w['main'] = "meta.meta_key = '_nim_baru'";
		
		if($_GET['gender']){
			$gender = $_GET['gender'];
			$w['gender'] = " and substr(meta.meta_value, 6, 1) = $gender";
		}
		
		if($this->search_query){
			$w['search'] = " and (users.display_name like '%$this->search_query%' or meta.meta_value  like '%$this->search_query%')";
		}
		
		$q['where'] = "where ".implode(' ', $w);
		
		$q['order'] = "order by nim_baru ASC";
		$q['limit'] = $this->limit_query();
		$q1 = implode(' ', $q);#echo $q1;exit;
		
		$r = $wpdb->get_results($q1);
		
		unset($q['order']);
		unset($q['limit']);
		unset($w['gender']);
		$q['where'] = "where ".implode(' ', $w);
		$q['select'] = "select count(*) from $wpdb->usermeta meta";
		$q2 = implode(' ', $q);
		$this->total = $wpdb->get_var($q2);
		
		$q['select'] = "select substr(meta.meta_value, 6, 1) gender, count(substr(meta.meta_value, 6, 1)) total from $wpdb->usermeta meta";
		$q['group'] = "group by substr(meta.meta_value, 6, 1)";
		$q3 = implode(' ', $q);
		$this->subs = $wpdb->get_results($q3);#print_r($this->subs);
		
		return $r;
	}
	
	function column_display_name($item){
		echo $item->display_name.' '.$item->maba;
	}
	
}