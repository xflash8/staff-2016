<?php
class Laporan_SPP_Card extends Card{
	public $columns = array(
		'sequence'=>'#',
		'display_name'=>'Nama',
		'nim'=>'NIM',
		'kewajiban_spp'=>'Kewajiban',
		'total_pembayaran_spp'=>'Total Pembayaran'
	);
	public $column_align = array(
		'total_pembayaran_spp'=>'right',
		'kewajiban_spp'=>'right',
	);
	public function query(){
		global $wpdb;
		$q = "
			select 
				users.display_name,
				meta2.meta_value nim,
				meta.meta_value kewajiban_spp,
				sum(credit) total_pembayaran_spp,
				count(*) count_spp
				
			from mutasi 
			
			left join $wpdb->usermeta meta
			on meta.user_id = mutasi.object_id and meta.meta_key = '_spp'
			
			left join $wpdb->usermeta meta2
			on meta2.user_id = mutasi.object_id and meta2.meta_key = '_nim_baru'
			
			left join $wpdb->users users
			on users.ID = mutasi.object_id
			
			
			where 
				category = 'spp'
				and object_id > 0
			group by object_id
			order by count_spp
		";
		$r = $wpdb->get_results($q);
		return $r;
	}
	public function column_total_pembayaran_spp($item, $column_name){
		echo number_format($item->$column_name, 0, ',', '.').' <a href="?sub=detail">('. $item->count_spp .')</a>';
	}
	public function column_kewajiban_spp($item, $column_name){
		echo number_format($item->$column_name, 0, ',', '.');
	}
	public function display(){
		$this->data = $this->query();
		?>
		
		<div class="card">
				<div class="card-header">
						<h2>
							<?php echo $this->rekening['description'];?> 
							<small><?php echo $this->rekening['bank'];?> - <?php echo $this->rekening['number'];?></small>
						</h2>
						
						<?php if(!$this->widget):?>
						<div class="row">
							<div class="col-md-4">
								<div class="input-group">
									<span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
									<div class="fg-line">
										<input type="text" class="form-control month-picker"  value="<?php echo $this->monthName.' '.$this->year;?>">
									</div>
								</div>
							</div>
							<div class="col-md-3">
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
						<?php else:?>							
							<ul class="actions"> 
								<li>
									<a href="/<?php echo $this->pagename;?>/?sub=mutasi&id=<?php echo $this->rekening['number'];?>">
										<i class="zmdi zmdi-forward"></i>
									</a>
								</li>
							</ul>
						<?php endif;?>
						
						
				</div>				
				<?php $this->display_table();?>
		</div>	
		<?php
	}
} 