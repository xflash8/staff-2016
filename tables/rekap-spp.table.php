<?php
class Rekap_Spp_List_Table extends List_Table{
	var $columns = array(
		'index'=>'#',		
		'nim'=>'NIM',
		'display_name'=>'Nama Lengkap',
		'spp'=>'SPP',
		'pembayaran'=>'Pembayaran',
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
				"meta.user_id", 
				"meta.meta_value nim", 
				"left(meta.meta_value, 2) angkatan", 
				"right(meta.meta_value, 3) seq",				
		);
		
		$q['join'][] = "left join $wpdb->users users on users.ID = meta.user_id";
		$q['select'][] = "users.display_name";
				
		$q['join'][] = "left join $wpdb->usermeta meta2 on meta2.user_id = meta.user_id and meta2.meta_key = '_spp'";
		$q['select'][] = "meta2.meta_value spp";
		
		
		$q['join'][] = "left join $wpdb->usermeta meta3 on meta3.user_id = meta.user_id and meta3.meta_key = '_2015_1_status'";
		$q['select'][] = "meta3.meta_value status";
		
		
		$q['join'][] = "left join $wpdb->usermeta meta4 on meta4.user_id = meta.user_id and meta4.meta_key = '_2015_1_maba'";
		$q['select'][] = "meta4.meta_value maba";
		
		$q['join'][] = "left join mutasi
				on mutasi.object_id = meta.user_id and mutasi.category like '%spp%'";
		$q['select'][] = "mutasi.ID mutasi_id";
		$q['select'][] = "count(mutasi.credit) kali";
		$q['select'][] = "coalesce(sum(mutasi.credit),0) pembayaran";
		$q['select'][] = "(meta2.meta_value - coalesce(sum(mutasi.credit),0)) sisa";
		
		
		$q['join'] = implode(' ', $q['join']);
		$q['select'] = "select " . implode(', ', $q['select']) . " from $wpdb->usermeta meta";
		
			
		$w['main'] = "
			meta.meta_key = '_nim_baru'
			and ((meta3.meta_value not like 'alumni' and meta3.meta_value not like 'dropout') or meta3.meta_value is null)
			and (meta4.meta_value <> 1 or meta4.meta_value is null)
		";
		
		$q['where'] = "where ".implode(' ', $w);
		
		$q['groupby'] = "group by nim";
		$q['order'] = "order by angkatan ASC, seq ASC";
		$q['limit'] = $this->limit_query();
		$q1 = implode(' ', $q);//echo $q1;//exit;
		
		$r = $wpdb->get_results($q1);//print_r($r);
		
		unset($q['groupby']);
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
			if($v->kali > 1)
					$txt .= '( '.$v->kali.')';
				
			return $txt;
		}else{
			 return $item->pembayaran;
		}
	}
	
	function column_sisa($v){
		return number_format($v->sisa, 0, ',', '.');
	}
	
	
}