<?php
class Mutasi_List_Table extends List_Table{
	var $columns = array(
		'index'=>'#',
		'date'=>'Tanggal',
		'code' => 'Kode',
		'description' => 'Deskripsi',
		//'nim'=>'NIM',
		//'prediksi'=>'Prediksi',
		//'action'=>'Action',  
		'category' => 'Kategori',
		'credit'=>'Kredit',
	);
	
	var $actions = array(
		'set_alumni'=>array(
			'text'=>'Set Alumni',
			'cap'=>'edit-alumni',
		),
	);
	function query()
	{
		global $wpdb;
		extract($_REQUEST);
		$main_table = 'mutasi';
		
		$q['select'] = array(
				'mutasi.*',
		);
		
		$q['select'] = "select " . implode(', ', $q['select']) . " from $main_table";
		
			
		$w['main'] = "rekening = '$this->par' and credit > 0";
		if($this->search_query){
			$w['search'] = " ";
		}
		
		
		if($dt){ 
			if(strlen($dt) == 1)
				$dt = '0'.$dt;
			
			if(strlen($mth) == 1)
				$mth = '0'.$mth;
						
			$dt = $yr.'-'.$mth.'-'.$dt;
		
			$w['dt'] = " and date(date) = '$dt'";
		}else{
			if($mth)
				$w['mth'] = " and month(date) = $mth";
			
			if($yr) 
				$w['yr'] = " and year(date) = $yr";			
		}
		
		if($this->search_query){
			$w['search_query'] = " and (description like '%$this->search_query%' or code like '%$this->search_query%')";
		}
		
		
		$q['where'] = "where ".implode(' ', $w);
		
		$q['order'] = "order by date DESC";
		$q['limit'] = $this->limit_query();
		$q1 = implode(' ', $q);#echo $q1;
		
		$r = $wpdb->get_results($q1);
		
		unset($q['order']);
		unset($q['limit']);
		unset($w['gender']);
		$q['where'] = "where ".implode(' ', $w);
		$q['select'] = "select count(*) from $main_table";
		$q2 = implode(' ', $q);
		$this->total = $wpdb->get_var($q2);
		
		$q['select'] = "select substr(meta.meta_value, 6, 1) gender, count(substr(meta.meta_value, 6, 1)) total from $main_table";
		$q['group'] = "group by substr(meta.meta_value, 6, 1)";
		$q3 = implode(' ', $q);
		$this->subs = $wpdb->get_results($q3);#print_r($this->subs);
		
		return $r;
	}
	
	function column_category($item, $column_name){
		$d['spp'] = 'SPP';
		$d['uang_masuk'] = 'Uang Masuk';
		
		if($d[$item->category]){
			$text = $d[$item->category];
		}else{
			$text = $item->category;
		}
		
		if(current_user_can('edit_'.get_query_var('pagename')) && !$this->excel){
			if($item->category){
				return '<a href="/'.get_query_var('pagename').'/?sub=transaction&ref='.$item->ID.'" target="_blank">'.$text.'</a>'; 
			}else{
				return '<a href="/'.get_query_var('pagename').'/?sub=transaction&ref='.$item->ID.'" target="_blank">not set</a>';
			}
		}else{
			return $text;
		}
	}
	
	function column_nim($item){
		
		
		$na = explode('/', $item->description);
		foreach($na as $n){
			if(is_numeric($n) && strlen($n) ==9 ){
				$nim = $n;
				continue;
			}
		}
		//$nim = preg_replace("/[^0-9]/","",$item->description);
		$item->nim = $nim;
		return $nim;
	}
	function column_prediksi($item){
		global $wpdb;
		$q = "
			select user_id, display_name from $wpdb->usermeta meta 
			left join $wpdb->users user 
				on user.ID = meta.user_id
			where meta.meta_key = '_nim_lama' 
				and meta.meta_value = $item->nim
			
		";
		$r = $wpdb->get_row($q);
		if($r){
			$item->user_id = $r->user_id;
			$item->display_name = $r->display_name;
		}
		
		return $r->display_name;
	}
	function column_action($item){
		if(!$item->category && $item->display_name)
			$a = '<a href="" data-id="'.$item->user_id.'" data-name="'.$item->display_name.'" data-action="validasi_spp" data-mutasi_id="'.$item->ID.'" class="ajax-action">Validasi</a>';
		return $a;
	}
	
	
	function row_actions(){
		extract($_REQUEST);
		$m = array(
			'',
			'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
		);
		if($mth && $yr){
			$val = $m[$mth].' '.$yr;
		}
		?>
		
		<div class="row">
			<div class="col-md-5">
				<div class="input-group">
					<span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
					<div class="fg-line">
						
						<input type="text" placeholder="Filter Bulan" class="form-control month-picker"  value="<?php echo $val;?>">
					</div>
				</div>
			</div>
			<?php if($_GET['mth'] && $_GET['yr']):?>
			<div class="col-md-2">
				<button id="resync" class="btn btn-primary">reSync</button>
			</div>
			<?php endif;?>
			<div class="col-md-5">
				<div class="input-group">
					<span class="input-group-addon"><i class="zmdi zmdi-label"></i></span>
						<div class="fg-line select">    
							<select class="form-control" id="categories"> 
								<option value="">Kategori</option>
								<?php foreach($this->categories as $k => $v):?>
									<option value="<?php echo $k;?>"><?php echo $v;?></option>
								<?php endforeach;?>
							</select>
						</div>
				</div>
			</div>
		</div>
		<?php
	}
	
}