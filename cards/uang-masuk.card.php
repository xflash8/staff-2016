<?php
class Uang_Masuk_Card extends Card{
	public $js = array(
		array('bootstrap-datetimepicker', 1),
		array('bootgrid', 1),
	);
	public function query(){
		global $wpdb;
		extract($_GET);
		
		$rek = $_GET['id'];
		$month = $_GET['mth'];
		$year = $_GET['yr'];
		$q = "
			select *, meta.meta_value tipe_pembayaran, meta2.meta_value nim from $wpdb->users users
			left join $wpdb->usermeta meta
			on meta.user_id = users.ID
			left join $wpdb->usermeta meta2
			on meta2.user_id = users.ID and meta2.meta_key = '_nim_baru'
			where meta.meta_key = 'tipe_pembayaran'
		";
		
		/* where */
		if($sub){
			if($sub != 'summary')
				$q .= "and meta.meta_value = '$sub' ";
		}
		
		/* order */
		$q .= " order by users.ID";
		//$q .= " limit 0,10";

		/* the data */
		$r = $wpdb->get_results($q); 
		
		$this->data = $r;
		$this->monthName = date('F', mktime(0, 0, 0, $month, 10)); 
		$this->year = $year;
	}
	
	public function display(){
	?>
		<style>
			.glyphicon-ok{color:green;}
			.glyphicon-flag{color:red;}
			.ui-autocomplete{z-index:1051}
		</style>                   
		
		<div class="card">
				<div class="card-header">
						<h2>Uang Masuk</h2>
						<dl class="dl-horizontal">
								<dt class="p-t-10">Tahun</dt>
								<dd>
										<div class="dtp-container dropdown fg-line">
												<input type='text' class="form-control year-picker" data-toggle="dropdown" value="<?php echo ($_GET['yr'])?$_GET['yr']:2015;?>">
										</div>
								</dd>
						</dl>
						<ul class="actions"> 
							<li><a href="?sub=mutasi&id=<?php echo $rek['rekening'];?>"><i class="zmdi zmdi-link"></i></a></li>
							<li class="dropdown action-show">
									<a href="" data-toggle="dropdown"><i class="zmdi zmdi-more-vert"></i></a>
	
									<ul class="dropdown-menu dropdown-menu-right">
											<li><a href="">Refresh</a></li>
											<li><a href="">Manage Widgets</a></li>
											<li><a href="">Widgets Settings</a></li>
									</ul>
							</li>
						</ul>
				</div>
				<ul class="tab-nav tn-justified">
					<?php 
					$t = array(
						'summary'=>'Summary',
						'transaction'=>'Transaction',
					);
					foreach($t as $k => $v):
						if(!$_GET['sub'])
							$_GET['sub'] = 'transaction';
						
						$active = ($k == $_GET['sub'])?'active':'';
					?>
						<li class="<?php echo $active;?> waves-effect"><a href="?sub=<?php echo $k;?>"><?php echo $v;?></a></li>
					<?php endforeach;?>
				</ul>

				<div class="table-responsive">
					<table id="data-table-basic" class="table table-striped">
					 <thead>
						<tr>
							<th data-column-id="no">No</th>
							<th data-column-id="id" data-type="numeric" data-identifier="true">NIM</th>
							<th data-column-id="nama">Nama</th>					
							<th data-column-id="tipe">Tipe Pembayaran</th>			
							<th data-column-id="kewajiban">Kewajiban</th>
							<th data-column-id="pembayaran">Pembayaran</th>		
							<th data-column-id="piutang">Piutang</th>			
						</tr>
					 </thead>
					 <tbody>
						<?php if($this->data):$i = 0;?>
							<?php foreach($this->data as $item):$i++;?>
								<tr class="item-mutasi">
									<td><?php echo $i;?><?php //print_r($item);?></td> 
									<td><?php echo $item->nim;?></td>
									<td><?php echo $item->display_name;?></td>				
									<td><?php echo $item->tipe_pembayaran;?></td>				
									<td><?php echo $item->total_uang_masuk;?></td>				
									<td><?php echo $item->total_pembayaran;?></td> 
									<td><?php echo $item->total_piutang;?></td>
								</tr>
							<?php endforeach;?>
						<?php else:?>
							<tr>
								<td colspan="6" class="text-center">No Entry</td>
							</tr>
						<?php endif;?>
					 </tbody>
					</table>
				</div>
		</div>	
	<?php
	}
	
	public function footer(){
	?>
		<script>		
			$(document).ready(function(){
				//Basic Example
				$("data-table-basic").bootgrid({
					css: {
							icon: 'zmdi icon',
							iconColumns: 'zmdi-view-module',
							iconDown: 'zmdi-expand-more',
							iconRefresh: 'zmdi-refresh',
							iconUp: 'zmdi-expand-less'
					},
				});
			});
		</script>
	<?php
	}
}