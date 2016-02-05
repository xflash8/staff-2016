<?php
class Registrasi_Mahasiswa_List_Table extends List_Table{
	var $columns = array(
		'checkbox'=>'cb',
		'index'=>'#',
		'nim' => 'NIM',
		'display_name' => 'Nama Lengkap',
		'gender' => 'L/P',
		'prodi' => 'Prodi',
		'semester' => 'Semester',
		'room' => 'Kelas',
		'pembayaran'=>'Pembayaran',
		'sisa'=>'Piutang',
		#'status'=>'Status',
	);
	
	var $actions = array(
		'cancel'=>array(
			'text'=>'Cancel',
			'cap'=>'edit-pembayaran-spp',
		),
	);
		
	
	var $max = 10;
	
	function get_columns(){
		$cols = parent::get_columns();
		
		if($_GET['action'] == 'xls')
			unset($cols['checkbox']);
		
		return $cols;
	}
	
	function query()
	{
		extract($_REQUEST);
		global $wpdb;
		
		$q['select'] = array(
				"meta.user_id",
				"meta.meta_value status"				
		);
		
		$j['display_name'] = "left join $wpdb->users users on users.ID = meta.user_id";
		$q['select'][] = "users.display_name";
				
		$j['nim'] = "left join $wpdb->usermeta meta2 on meta2.user_id = meta.user_id and meta2.meta_key = '_nim_baru'";
		$q['select'][] = "meta2.meta_value nim";
		$q['select'][] = "left(meta2.meta_value, 2) angkatan";
		$q['select'][] = "right(meta2.meta_value, 3) seq";
				
		$j['spp'] = "left join $wpdb->usermeta meta3 on meta3.user_id = meta.user_id and meta3.meta_key = '_spp'";
		$q['select'][] = "meta2.meta_value spp";
		
		
		
		
		$j['prodi'] = "left join $wpdb->usermeta meta4 on meta4.user_id = meta.user_id and meta4.meta_key = '_2015_2_prodi'";
		$q['select'][] = "meta4.meta_value prodi";
		
		$j['semester'] = "left join $wpdb->usermeta meta5 on meta5.user_id = meta.user_id and meta5.meta_key = '_2015_2_semester'";
		$q['select'][] = "meta5.meta_value semester";
		
		$j['room'] = "left join $wpdb->usermeta meta6 on meta6.user_id = meta.user_id and meta6.meta_key = '_2015_2_room'";
		$q['select'][] = "meta6.meta_value room";
		
		$j['gender'] = "left join $wpdb->usermeta meta7 on meta7.user_id = meta.user_id and meta7.meta_key = 'gender'";
		$q['select'][] = "meta7.meta_value gender";
		
		
		
		$j['mutasi'] = "left join mutasi on mutasi.object_id = meta.user_id and mutasi.category like 'spp_2015_2'";
		$q['select'][] = "mutasi.ID mutasi_id";
		$q['select'][] = "count(mutasi.credit) kali";
		$q['select'][] = "coalesce(sum(mutasi.credit),0) pembayaran";
		$q['select'][] = "(meta3.meta_value - coalesce(sum(mutasi.credit),0)) sisa";		
		
		$q['join'] = implode(' ', $j);
		$q['select'] = "select " . implode(', ', $q['select']) . " from $wpdb->usermeta meta";
		
		
		
			
		$w['main'] = "meta.meta_key = '_2015_2_status'";
		
		
		if($this->search_query){
			if(is_numeric($this->search_query))
				$w['search'] = "and meta2.meta_value like '%$this->search_query%'";
			else
				$w['search'] = "and users.display_name like '%$this->search_query%'";
		}
		
		if($_GET['gender']){
			$w['gender'] = " and meta7.meta_value = $gender";
		}
		
		
		if($_GET['prodi'])
			$w['prodi'] = " and meta4.meta_value = '$prodi'";
		
		if($_GET['semester'])
			$w['semester'] = " and meta5.meta_value = '$semester'";
		
		if($_GET['room'])
			$w['room'] = " and meta6.meta_value = '$room'";
		
		$q['where'] = "where ".implode(' ', $w);
		
		$q['groupby'] = "group by user_id";
		$q['order'] = "order by angkatan ASC, seq ASC";
		$q['limit'] = $this->limit_query();
		$q1 = implode(' ', $q);#echo $q1;//exit;  
		
		$r = $wpdb->get_results($q1);//print_r($r);
		
		unset($q['groupby']);
		unset($q['order']);
		unset($q['limit']);
		unset($w['gender']);
		$q['where'] = "where ".implode(' ', $w);
		$q['select'] = "select COUNT(DISTINCT meta.user_id) total from $wpdb->usermeta meta";
		$q2 = implode(' ', $q);
		$this->total = $wpdb->get_var($q2);
		
		
		unset($j['mutasi']);
		$q['join'] = implode(' ', $j);
		$q['select'] = "select meta.user_id, meta7.meta_value gender, count(meta7.meta_value) total from $wpdb->usermeta meta";
		$q['groupby'] = "group by gender";
		
		$q3 = implode(' ', $q);
		$this->subs = $wpdb->get_results($q3);//print_r($this->subs); 
		
		return $r;
	}
	
	function column_code($v){
		return '<a href="?sub=single&ref='.$v->ID.'" target="_blank">'.$v->code.'</a>';
	}
	
	function column_display_name($v){
		return ucwords($v->display_name);
	}
	
	
	function column_spp($v){
		$txt = '<div class="view">'.number_format($v->spp, 0, ',', '.').'</div>
						<div class="edit">
							<input type="text" class="set_spp money" value="'.number_format($v->spp, 0, ',', '').'" data-uid="'.$v->user_id.'" data-nim="'.$v->nim.'" data-default="'. $v->spp.'"/>
						</div>';
		return $txt;
	} 
	
	function column_pembayaran($item){
		if($item->pembayaran > 0){
			$txt = '<a href="/pembayaran-spp/?user_id='.$item->user_id.'" target="_blank">'.number_format($item->pembayaran, 0, ',', '.').'</a>';
			if($item->kali > 1)
					$txt .= '( '.$item->kali.')';
				
			return $_GET['action'] == 'xls'?$item->pembayaran: $txt;
		}else{
			 return $item->pembayaran;
		}
	}
	
	function column_sisa($v){
		return number_format($v->sisa, 0, ',', '.');
	}
	function column_checkbox($item, $column_name)
	{
		return '<input type="checkbox" name="cb" value="'.$item->user_id.'" class="cb"/>';
	}
	function column_prodi($item){
		return strtoupper($item->prodi);
	}
	function row_subs(){
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
		echo '<select>'.implode('', $x).'</select> ';
		
		$this->row_sub_gender();
		
	}
	
	function row_actions()
	{
		$this->set_kelas_btn();
	}
	
	function set_kelas_btn(){		
		$kelas = array(
			'',
			'PMH 2A',
			'PMH 2B',
			'PMH 2C',
			'PMH 2D',
			'PMH 2E',
			'PMH 2F',
			'PMH 2G',
			'PMH 2H',
			'PMH 2I',
			'PMH 2J',
			'PMH 2K',
			'PMH 2L',
			'PMH 4A',
			'PMH 4B',
			'PMH 4C',
			'PMH 4D',
			'PMH 4E',
			'PMH 4F',
			'PMH 4G',
			'PMH 6A',
			'PMH 6B',
			'PMH 8A',
			'PMH 8B',
			'PKU 2A',
			'PKU 2B',
			'PKU 4A',
			'PKU 4B',		
		); 
		foreach($kelas as $k => $v){
			if($k > 0)
				$data['kelas'] .= '<option value="'.$k.'">'.$v.'</option>';
		} 
		
		if(current_user_can('edit_registrasi-mahasiswa')){
			$setbtn = '<button class="btn btn-primary waves-effect" type="button" id="set-kelas-btn">Set</button>';
		}
		
		$k = '
			<select class="selectpicker" id="kelas">
					<option>Kelas</option>
					'.$data['kelas'].'
			</select>
			'.$setbtn.'
			<button class="btn btn-primary waves-effect" type="button" id="filter-kelas-btn">Filter</button>
		';
		echo $k;		
		
	}
	
	
	
}