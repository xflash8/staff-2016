<?php
class Mutasi_Card extends Card{
	var $rekening;
	var $number;
	var $monthName;
	var $max;
	var $start;
	var $limit = false;
	var $pagetitle = "Rekening";
	var $table = 'mutasi';
	
	var $categories = array(
		array(
			'slug'=>'uang_masuk',
			'text'=>'Pembayaran Uang Masuk',
			'cap'=>'edit_pembayaran-uang-masuk',
		),
		array(
			'slug'=>'spp',
			'text'=>'Pembayaran SPP',
			'cap'=>'edit_pembayaran-spp',
		),
	);
	
	var $columns = array(
		'index'=>'#',
		'date'=>'Tanggal',
		'code' => 'Kode',
		'description' => 'Deskripsi',
		'category' => 'Kategori',
		'credit'=>'Kredit',
	);
	var $column_align = array(
		'credit'=>'right'
	);
	
	var $items = array(
		'date'=>'Tanggal/Waktu',
		'credit'=>'Jumlah',
		'description'=>'Deskripsi',
	);
	
	var $js = array(
		array('bootstrap-datetimepicker', 1),
		array('jquery.autoNumeric'),
		array('jquery.fakeLoader', 1),
	);
	
	
	var $rek_list = array(
		array(
			'bank'=>'Bank Syariah Mandiri',
			'number'=>'7772227714',
			'description'=>'Rekening Utama'
		),
		array(
			'bank'=>'Bank Syariah Mandiri',
			'number'=>'xxxx',
			'description'=>'Penerimaan Mahasiswa Baru'
		),
		array(
			'bank'=>'Bank Syariah Mandiri',
			'number'=>'yyy',
			'description'=>'Masjid Anas bin Malik'
		)
	);
	
	function get_pagetitle(){
		if($this->id){
			$this->pagetitle .= ' '.$this->id;
		}
		return $this->pagetitle;
	}
	
	function query_mutasi($id){
		global $wpdb;
		$q = "select * from mutasi where ID = $id";
		$r = $wpdb->get_row($q);
		return $r;
	}
	
	function query_category($user_id, $catname){
		global $wpdb;
		$q = "select * from mutasi where object_id = $user_id and category = '$catname'";
		$r = $wpdb->get_results($q);
		return $r;
	}
	
	function query(){
		global $wpdb;
		
		$number = $this->id;
				
		/* get rekening info */
		$i = array_search($number, array_column($this->rek_list, 'number'));
		$this->rekening = $this->rek_list[$i];
		
		$rek = $_GET['id'];
		$month = $_GET['mth'];
		$year = $_GET['yr'];
		$q = "
			select mutasi.*, meta.user_id, users.display_name
			from mutasi 
			left join $wpdb->usermeta meta
			on mutasi.ID = meta.meta_value and meta.meta_key = '_2015_1_spp_ref'
			
			left join $wpdb->users users
			on users.ID = meta.user_id
			
			left join $wpdb->usermeta meta2
			on meta.user_id = meta2.user_id and meta2.meta_key = '_nim_baru'
			
			where rekening = '$number' and credit > 0
		";
		
		if($month){
			$q.=" and month(date) = $month";
		}else{
			$month = (int) date('m');
			$q.=" and month(date) = $month";
		}
		
		if($year){ 
			$q.=" and year(date) = $year";
		}else{
			$year = date('Y');
			$q.=" and year(date) = $year";
		}
		
		
		/* order */
		$q .= " order by date DESC, ID DESC"; 
		
		if($this->limit)
			$q .= " limit $this->start, $this->max";
		
		

		/* the data */
		$r = $wpdb->get_results($q); 
		
		$this->query = $q;
		$this->monthName = date('F', mktime(0, 0, 0, $month, 10)); 
		$this->year = $year;
		
		return $r;
	}
	
	public function set_limit($start, $max){
		$this->start = $start;
		$this->max = $max;
		$this->limit = true;
	}
	public function column_credit($item, $column_name){
		echo number_format($item->credit, 0, ',', '.');
	}
	
	public function column_category($item, $column_name){
		if($item->category){
			echo $item->category;
		}else{
			echo '<a href="/rekening/?sub=transaction&ref='.$item->ID.'" target="_blank">not set</a>';
		}
	}
	
	public function display(){
		$this->table_obj->display();
		$this->data = $this->query();
		?>
		<style>
			.glyphicon-ok{color:green;}
			.glyphicon-flag{color:red;}
			.ui-autocomplete{z-index:1051}
		</style>
		<?php if($_GET['id']) $this->card_header();?>
		<div class="card">
				<div class="card-header">
						<h2>
							<?php if($_GET['sub']):?>
								<?php echo $this->rekening['description'];?>
							<?php else:?>
								<a href="/<?php echo $this->pagename;?>/?sub=mutasi&id=<?php echo $this->rekening['number'];?>"><?php echo $this->rekening['description'];?></a>
							<?php endif;?>
							
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
	
	function display_category(){
		$this->data = $this->query_category($_GET['id'], $_GET['cat']);
		?>	
		<style>	
			
			<?php if($_GET['sub'] == 'new' || $_GET['mode'] == 'edit'):?>
				.view{display:none;}
			<?php else:?>
				.edit{display:none;}
			<?php endif;?>
			
			
		</style>
		<div class="fakeLoader"></div>
		<?php $this->card_header();?>
		
		<form method="post" id="form-transaction">
		
		<div class="row">
			<div class="col-md-12 col-lg-3">
				<!-- start profile -->
					<div class="card">
							<div class="card-body card-padding">
								<div class="row collapse" id="info">
									<div class="col-sm-4 col-md-4 col-lg-12">
										<div id="foto" class="img-responsive">foto</div>
									</div>
									<div class="col-sm-8 col-md-8 col-lg-12">
										<div class="pmo-contact">        
												<ul>
														<li class="ng-binding">
															<i class="zmdi zmdi-account"></i> <span id="name"></span>
														</li>
														<li class="ng-binding">
															<i class="zmdi zmdi-phone"></i> <span id="phone"></span>
														</li>
														<li>
																<i class="zmdi zmdi-pin"></i><span id="location"></span>
														</li>
												</ul>
										</div>
									
									</div>
								</div>
							</div>
					</div>
				<!-- end profile -->
			</div>
			<div class="col-md-12 col-lg-9">
			
			<?php foreach($this->data as $item):?>
			
			<!-- start nota --->
			<div class="card">
				
				<div class="card-body m-t-0">
					<div class="table-responsive">
						<table class="table table-inner table-vmiddle">
								<thead>
										<tr>
												<th class="text-right">Kode Transaksi</th>
												<th class="text-left"><?php echo $item->code;?></th>
										</tr>
								</thead>
								<tbody>
									<?php foreach($this->items as $k => $v):?>										
										<?php if($k == 'credit') $item->credit = number_format($item->credit, 0, ', ', '.');?>
										<tr>
												<td class="f-500 info text-right"><?php echo $v;?></td>												
												<td class="text-left text-money"><?php echo $item->$k;?></td>
										</tr>
									<?php endforeach;?>
								</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end nota --->
			<?php endforeach;?>
			
			</div>
		</div>
			<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>" autocomplete="off"/>
			<input type="hidden" name="ref_id" id="ref_id" value="<?php echo $_GET['ref'];?>" autocomplete="off"/>
			
		</form>
		<?php
	}
	function display_transaction(){
		$this->data = $this->query_mutasi($_GET['ref']);
		?>	
		<style>	
			
			<?php if($_GET['sub'] == 'new' || $_GET['mode'] == 'edit'):?>
				.view{display:none;}
			<?php else:?>
				.edit{display:none;}
			<?php endif;?>
			
			
		</style>
		<div class="fakeLoader"></div>
		<?php $this->card_header();?>
		
		<form method="post" id="form-transaction">
		
		<div class="row">
			<div class="col-md-12 col-lg-3">
				<!-- start profile -->
					<div class="card">
							<div class="card-body card-padding">
								<?php $collapse = $this->single?'collapse':'';?>
								<div class="form-group <?php echo $collapse;?>"> 
									<label for="exampleInputEmail1">UID</label> 
									<input class="form-control" id="uid" placeholder="UID" autocomplete="off"/> 
								</div>
								<div class="row collapse" id="info">
									<div class="col-sm-4 col-md-4 col-lg-12">
										<div id="foto" class="img-responsive">foto</div>
									</div>
									<div class="col-sm-8 col-md-8 col-lg-12">
										<div class="pmo-contact">        
												<ul>
														<li class="ng-binding">
															<i class="zmdi zmdi-account"></i> <span id="name"></span>
														</li>
														<li class="ng-binding">
															<i class="zmdi zmdi-phone"></i> <span id="phone"></span>
														</li>
														<li>
																<i class="zmdi zmdi-pin"></i><span id="location"></span>
														</li>
												</ul>
										</div>
									
									</div>
								</div>
							</div>
					</div>
				<!-- end profile -->
			</div>
			<div class="col-md-12 col-lg-9">
			
			<!-- start nota --->
			<div class="card">
				
				<div class="card-body m-t-0">
					<div class="table-responsive">
						<table class="table table-inner table-vmiddle">
								<thead>
										<tr>
												<th class="text-right">Kode Transaksi</th>
												<th class="text-left"><?php echo $this->data->code;?></th>
										</tr>
								</thead>
								<tbody>
									<?php foreach($this->items as $k => $v):?>										
										<tr>
												<td class="f-500 info text-right"><?php echo $v;?></td>												
												<td class="text-left text-money"><?php echo $this->data->$k;?></td>
										</tr>
									<?php endforeach;?>
									<tr>
											<td class="f-500 info text-right">Kategori Transaksi</td>												
											<td class="text-left text-money">
												<select name="category" autocomplete="off" id="category">
													<?php foreach($this->categories as $c):?>
														<?php if(current_user_can($c['cap'])):?>
															<option value="<?php echo $c['slug'];?>"><?php echo $c['text'];?></option>
														<?php endif;?>
													<?php endforeach;?>
												</select>
											</td>
									</tr>
									<tr>
											<td class="f-500 info text-right">&nbsp;</td>												
											<td class="text-left text-money">
												<button class="btn btn-primary waves-effect"type="submit" id="submit">Simpan</button>
											</td>
									</tr>
								</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end nota --->
			
			</div>
		</div>
			<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>" autocomplete="off"/>
			<input type="hidden" name="ref_id" id="ref_id" value="<?php echo $_GET['ref'];?>" autocomplete="off"/>
			
		</form>
		<?php
	}
	public function footer(){
		?>
		<?php if(!$this->widget):?>
			<script>
				$(function() {		
					if(get.sub == 'category'){
						get_user_profile(get.id);
					}
				
					/* month picker */
					if ($('.month-picker')[0]) {
						console.log(get);
						$('.month-picker').datetimepicker({
								format: 'MMMM YYYY'
						}).on("dp.change", function(e) {
							if(get.sub == 'mutasi'){
								m = e.date.format('M');
								y = e.date.format('YYYY');
								link = "?sub=mutasi&id="+get.id+"&mth="+m+"&yr="+y;
								window.location.href = link;
							}
						});
					}
					
					/* categories handler */
					$('select#categories')
					.change(function(){
						par.push('catname='+$(this).val());
						link = '?'+par.join('&');
						window.location.href = link;
					});
					
					if(get.catname){
						$('select#categories option[value="'+get.catname+'"]').attr("selected","selected")
						console.log('yea');
					}
					
					/* submit transaction detail */
					$('#form-transaction').submit(function(e){
						e.preventDefault();
						
						if(!userId){
							alert('Harap isi dulu uid nya');
							return;
						}
						
						$.ajax({
							url: '?ajax=set_transaction_category',
							data: $(this).serialize(),
							type : 'POST',
							beforeSend: function(){
								$(".fakeLoader").show().fakeLoader({
									timeToHide:1200000,
									zIndex:9999,
									spinner:"spinner5",
									bgColor:"#2ecc71"
								});
							},
							success:function(){
								$('.fakeLoader').fadeOut('slow', function(){
									alert('Sukses');
								});
								
							},
							error: function(){
								alert('something is wrong. maybe your internet connection');
							}
						});
					});
					
					/* get user profile */
					$('#uid').blur(function(){
						uid = $(this).val();				
						get_user_profile(uid);
					});
					function get_user_profile(uid){						
						$.ajax({
							url: '?ajax=get_profile&uid='+uid,
							beforeSend: function(){
								$('#info').hide();
								userId = false;
							},
							success: function(r){console.log(r);
								if(!r.ID){
									alert('tidak dapat menemukan mahasiswa dengan uid tersebut');
									return;
								}
								
								if(r.type){
									if(r.type == 'maba'){
										$('#category').val('uang_masuk');
									}else if(r.type == 'mala'){
										$('#category').val('spp');
									}
								}
								
								userId = r.ID;
								$('#user_id').val(r.ID);
								/* foto */
								if(r.gender == 1){
									$('#foto').html('<img class="img-responsive center-block" src="'+r.foto_baru+'"/>');
								}
								
								$('#name').text(r.display_name);
								$('#phone').text(r.phone);
								$('#email').text(r.email);
								$('#location').text(r.kabupaten_asal+', '+r.provinsi_asal);
								$('#info').show();
							},
							error: function(e){
								alert('ups, something wrong!');
								console.log(e);
								userId = false;
							}
						});		
					}
					
				});

				</script> 
			<?php endif;?>
		<?php 
	}
}