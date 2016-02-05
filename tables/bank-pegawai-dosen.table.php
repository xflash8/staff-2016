<?php
class Bank_Pegawai_Dosen_List_Table extends List_Table{
	var $columns = array(
		'index'=>'#',
		'display_name'=>'Nama Lengkap', 
		'user_email'=>'Email', 
		'phone'=>'Hp', 
		'gender'=>'L/P',
		'user_id'=>'UID',
	);
	
	var $actions = array(
		'edit-data'=>array(
			'text'=>'Edit Data',
			'cap'=>'edit-bank-pegawai-dosen',
		),
	);
		
	
	var $max = 10;
	
	function query()
	{
		global $wpdb;
		$q['select'] = array(
				'meta.user_id',
		);
		
		$q['join'][] = "left join $wpdb->users users on users.ID = meta.user_id";
		$q['select'][] = "users.display_name, users.user_email";
				
		$q['join'][] = "left join $wpdb->usermeta meta2 on meta2.user_id = meta.user_id and meta2.meta_key = 'gender'";
		$q['select'][] = "meta2.meta_value gender";
		
		$q['join'][] = "left join $wpdb->usermeta meta3 on meta3.user_id = meta.user_id and meta3.meta_key = 'phone'";
		$q['select'][] = "meta3.meta_value phone";
		
		
		$q['join'] = implode(' ', $q['join']);
		$q['select'] = "select " . implode(', ', $q['select']) . " from $wpdb->usermeta meta";
		
			
		$w['main'] = "meta.meta_key like '".$wpdb->prefix."capabilities'";
		
		if($_GET['gender']){
			$gender = $_GET['gender'];
			$w['gender'] = " and meta2.meta_value = $gender";
		}
		
		if($this->search_query){
			$w['search'] = " and (users.display_name like '%$this->search_query%' or meta.meta_value  like '%$this->search_query%')";
		}
		
		$q['where'] = "where ".implode(' ', $w);
		
		$q['order'] = "";
		$q['limit'] = $this->limit_query();
		$q1 = implode(' ', $q);#echo $q1;
		
		$r = $wpdb->get_results($q1);
		
		unset($q['order']);
		unset($q['limit']);
		unset($w['gender']);
		$q['where'] = "where ".implode(' ', $w);
		$q['select'] = "select count(*) from $wpdb->usermeta meta";
		$q2 = implode(' ', $q);
		$this->total = $wpdb->get_var($q2);
		
		$q['select'] = "select meta2.meta_value gender, count(meta2.meta_value) total from $wpdb->usermeta meta";
		$q['group'] = "group by gender";
		$q3 = implode(' ', $q);
		$this->subs = $wpdb->get_results($q3);#print_r($this->subs);exit;
		
		return $r;
	}
	function column_phone($item){
		if($this->excel){
			return $this->phone;
		}else{
			$text = '<a href="?sub=sms&id='.$item->user_id.'" target="_blank">'.$item->phone.'</a>';
			return $text;
		}
	}
	function row_actions(){
		if($_GET['gender'])
			$gender = '&gender='.$_GET['gender'];
		
		echo '<a class="btn btn-success waves-effect" href="?sub=sms'.$gender.'"><i class="zmdi zmdi-comments"></i></a>';
	}
	
}