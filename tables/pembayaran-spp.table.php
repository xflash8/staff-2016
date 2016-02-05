<?php
class Pembayaran_Spp_List_Table extends List_Table{
	var $columns = array(
		'index'=>'#',
		'date'=>'Tanggal',
		'code' => 'Referensi',
		'nim' => 'NIM',
		'display_name' => 'Nama Lengkap',
		'credit'=>'Jumlah',
		'action'=>'Action',
	);
	
	var $actions = array(
		'cancel'=>array(
			'text'=>'Cancel',
			'cap'=>'edit-pembayaran-spp',
		),
	);
		
	
	var $max = 10;
	
	function query()
	{
		global $wpdb;
		
		$q['select'] = array(
				"mutasi.*", 
		);
		
		$q['join'][] = "left join $wpdb->users users on users.ID = mutasi.object_id";
		$q['select'][] = "users.display_name";
				
		$q['join'][] = "left join $wpdb->usermeta meta on meta.user_id = mutasi.object_id and meta.meta_key = '_nim_baru'";
		$q['select'][] = "meta.meta_value nim";
		
		
		$q['join'] = implode(' ', $q['join']);
		$q['select'] = "select " . implode(', ', $q['select']) . " from mutasi";
		
			
		$w['main'] = "category like 'spp_2015_2'";
		
		
		if($this->search_query){
			$w['search'] = "and users.display_name like '%$this->search_query%'";
		}
		
		$q['where'] = "where ".implode(' ', $w);
		
		$q['order'] = "order by date DESC";
		$q['limit'] = $this->limit_query();
		$q1 = implode(' ', $q);//echo $q1;//exit; 
		
		$r = $wpdb->get_results($q1);//print_r($r);
		
		unset($q['groupby']);
		unset($q['order']);
		unset($q['limit']);
		unset($w['gender']);
		$q['where'] = "where ".implode(' ', $w);
		$q['select'] = "select count(*) from mutasi";
		$q2 = implode(' ', $q);
		$this->total = $wpdb->get_var($q2);
		
		$q['select'] = "select substr(meta.meta_value, 6, 1) gender, count(substr(meta.meta_value, 6, 1)) total from $wpdb->usermeta meta";
		$q['group'] = "group by substr(meta.meta_value, 6, 1)";
		$q3 = implode(' ', $q);
		$this->subs = $wpdb->get_results($q3);#print_r($this->subs);
		
		return $r;
	}
	
	function column_code($v){
		return '<a href="?sub=single&ref='.$v->ID.'" target="_blank">'.$v->code.'</a>';
	}
	
	function display_name($v){
		return ucwords($v->display_name);
	}
	
	function column_credit($v){
		return number_format($v->credit, 0, ',', '.');
	}
	
	function column_action($v){
		return '<a href="#" class="cancel_transaction" data-id="'.$v->ID.'">Cancel</a>';
	}
	
	function row_actions()
	{
		$l= array(
			array(
				'text'=>'2015/2016 Genap',
				'year'=>2015,
				'semester'=>'genap'
				),
			array(
				'text'=>'2015/2016 Ganjil',
				'year'=>2015,
				'semester'=>'ganjil'
				),
		);
		foreach($l as $k=> $v){
			$x[] = '<option value="'.$v['year'].'-'.$v['semester'].'">'.$v['text'].'</option>';
		}
		echo '<select>'.implode('', $x).'</select>';
	}
	
	
	
}