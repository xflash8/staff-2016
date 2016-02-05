<?php
class Log_List_Table extends List_Table{
	var $columns = array(
		'datetime' => 'tgl/waktu',
		'description'=>'Deskripsi'
	);
	
	var $hide_nav = true;
	
	function query(){
		$log = new Log;
		$log->pagename = get_query_var('pagename');
		$data =  $log->get();
		$this->total = count($data);
		return $data;
	}
	
	function column_description($item){
		$user = '<a href="#" target="_blank">'.$item->display_name.'</a>';
		
		switch($item->action){
			case 'member_add':
				$u = get_userdata($item->object_id);
				$action = 'menambahkan <a href="#" target="_blank">'.ucwords($u->display_name).'</a>';
			 break;
			case 'promote_member':
				$u = get_userdata($item->object_id);
				$action = 'menjadikan <a href="#" target="_blank">'.ucwords($u->display_name).'</a> sebagai admin';
			 break;
			case 'member_remove':
				$u = get_userdata($item->object_id);
				$action = 'mengeluarkan <a href="#" target="_blank">'.ucwords($u->display_name).'</a>';
			 break;
			case 'member_downgrade':
				$u = get_userdata($item->object_id);
				$action = 'merubah <a href="#" target="_blank">'.ucwords($u->display_name).'</a> dari admin menjadi anggota biasa';
			 break;
		}
		$text = $user.' '.$action;
		echo $text;
	}
	
}