<?php
class Keringanan_Uang_Masuk_Card extends Card{
	public $pagetitle = "Nota Keringanan Uang Masuk";
	public $menus = array(
		array(
			'url'=>'?sub=new',
			'icon'=>'file-plus',
			'text'=>'Buat Nota Baru',
			'cap'=>'edit_nota-keringanan-uang-masuk'
		),
		array(
			'url'=>'?action=xls',
			'icon'=>'download',
			'text'=>'Download Excel',
			'cap'=>'view_rekap-spp',
		)
	);
	public $items = array(
		'uang_pangkal'=>'Uang Pangkal',
		'spp'=>'SPP',
		'nimko'=>'NIMKO',
		'daurah_orientasi'=>'Daurah Orientasi',
		'jas_almamater'=>'Jas Almamater',
		'ktm'=>'KTM',
		'kartu_perpustakaan'=>'Kartu Perpustakaan',
	);
	
	public $columns = array(
		'display_name'=>'Nama Lengkap',
		'uang_masuk'=>'Uang Masuk',
		'pembayaran_uang_masuk'=>'Pembayaran',
		'kali'=>'x',
		'sisa'=>'Sisa'
	);
	
	public $js = array(
		array('bootstrap-datetimepicker', 1),
		array('file-upload'),
		array('jquery.autoNumeric'),
		array('jquery.fakeLoader', 1),
	);
	
	function get_data(){
		return $this->query_list();
	}
	
	function query_list(){
		global $wpdb;
		$q = "
			select 
				users.ID, 
				users.display_name, 
				meta2.meta_value uang_masuk, 
				coalesce(sum(mutasi.credit),0) pembayaran_uang_masuk, 
				meta3.meta_value nim, 
				count(mutasi.credit) kali,
				(meta2.meta_value - coalesce(sum(mutasi.credit),0)) sisa 				
			from $wpdb->usermeta meta
			left join $wpdb->users users
				on users.ID = meta.user_id
				
			left join $wpdb->usermeta meta2
				on meta.user_id = meta2.user_id and meta2.meta_key = 'uang_masuk'
			
			left join $wpdb->usermeta meta3
				on meta3.user_id = meta.user_id and meta3.meta_key = '_nim_baru'
			
			left join mutasi
				on meta.user_id = mutasi.object_id and mutasi.category = 'uang_masuk'
				
			where meta.meta_key like 'nota_keringanan_uang_masuk'
			group by users.ID
			order by sisa DESC
		";
		$r = $wpdb->get_results($q);//print_r($r);
		
		return $r;
	}
	
	function ajax_profile(){
		extract($_REQUEST);
		global $wpdb;
		$user = new WP_USER($uid);
		
		$fid = $user->foto_baru;
		switch_to_blog(14);
		$img = wp_get_attachment_image_src( $user->foto_baru, 'medium');
		if(!$img){
			$img = wp_get_attachment_image_src( $user->foto, 'medium');
		}
		
		restore_current_blog();
		
		$data = array( 
			'foto_baru'=>$img[0],
			'display_name'=>$user->display_name,
			'kabupaten_asal'=>$user->kabupaten_asal,
			'provinsi_asal'=>$user->provinsi_asal,
			'phone'=>$user->phone,
			'email'=>$user->email,
			'gender'=>$user->gender,
			
		);
		return $data;
	}
	
	function ajax_add_nota_uang_masuk(){
		extract($_REQUEST);
		if(!$user_id)
			return array('status'=>false, 'detail'=>'no user id');
		
		update_user_meta($user_id, 'nota_keringanan_uang_masuk', $_POST);
		update_user_meta($user_id, 'uang_masuk', $uang_masuk);
		update_user_meta($user_id, 'spp', $spp);
		
		return array('post'=>$_POST, 'status'=>1, 'detail'=>'complete');
	}
	
	function ajax_img(){
		$upload_dir = wp_upload_dir();		
		$this->image_folder = $upload_dir['path'];
		$this->path = $upload_dir['path'].'/';
		$this->base_url = $upload_dir['url'].'/';
		
		$name = 'nota_keringanan_spp_'.date('Y-m-d_his');
		$ext = '.jpg';
		$imageName = $this->path .$name. $ext;
		$imageUrl = $this->base_url .$name. $ext;
		if($_FILES){
			if(move_uploaded_file($_FILES['upload_file']['tmp_name'], $imageName)){ 
					
					/* echo $_FILES['upload_file']['name']. " OK"; */
					$data = array(
						'files'=>$_FILES,
						'imagePath'=>$imageName,
						'imageUrl'=>$imageUrl
					); 
					
					return $data;
					
			} else {
					/* echo $_FILES['upload_file']['name']. " KO"; */
			}
		}else{
			echo 'gak ada files';
			exit;
		}
	}
	function display(){
		extract($_GET);
		switch($_GET['sub']){
			default:
				$this->display_list();
			 break;
			case 'new':
				$this->display_single();
			 break;
			case 'single':
				$this->single = true;
				$this->data = get_user_meta($id, 'nota_keringanan_uang_masuk', true);
				$this->display_single();
			 break;
		}
	}
	function display_list(){
		?>
		
		<?php $this->card_header();?>
		
		<div class="card">
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th>#</th>
						<th>NIM</th>
						<th>Nama</th>
						<th>Uang Pangkal</th>
						<th>Pembayaran</th>
						<th>sisa</th>
						<th>Nota</th>
					</tr>
					<?php $r = $this->query_list();if($r):?>
						<?php foreach($r as $k => $v):$index = $k +1;?>
							<tr>
								<td>
									<?php echo $index;?> 
								</td>
								<td>
									<?php echo $v->nim;?>
								</td>
								<td>
									<?php echo ucwords($v->display_name);?>
								</td>
								<td>
									<?php echo number_format($v->uang_masuk, 0, ',', '.');?>
								</td>
								<td>
									<?php if($v->pembayaran_uang_masuk > 0):?>
										<a href="/pembayaran-uang-masuk/?user_id=<?php echo $v->ID;?>&cat=uang_masuk" target="_blank">
										<?php echo number_format($v->pembayaran_uang_masuk, 0, ',', '.');?>
										</a>
										<?php if($v->kali > 1):?>
											(<?php echo $v->kali;?>x)
										<?php endif;?>

									<?php endif;?>
								</td>
								<td>
									<?php echo number_format($v->sisa, 0, ',', '.');?>
								</td>
								<td>										
									<a href="?sub=single&id=<?php echo $v->ID;?>" target="_blank">
											<i class="zmdi zmdi-receipt"></i>
									</a>&nbsp;&nbsp;&nbsp;&nbsp;									
									<a href="?sub=single&mode=edit&id=<?php echo $v->ID;?>" target="_blank">
											<i class="zmdi zmdi-edit"></i>
									</a>
								</td>
							</tr>
						<?php endforeach;?>
					<?php else:?>
					
					<?php endif;?>
				</table>
			</div>
		</div>
		<?php 
	}
	function display_single(){
		if($this->single){
			extract($this->data);
		}
		?>	
		<style>
			.btn{
				position: fixed;
				bottom:100px;
				right:30px;
				z-index:99;
			}
			
			.money-row, .money, .total{
				text-align:right;
			}
			input.money{
				width:100px;
			}
			
			<?php if($_GET['sub'] == 'new' || $_GET['mode'] == 'edit'):?>
				.view{display:none;}
			<?php else:?>
				.edit{display:none;}
			<?php endif;?>
			
			
		</style>
		<div class="fakeLoader"></div>
		<?php $this->card_header();?>
		
		<form method="post" id="form">
		
		<?php if($_GET['mode'] == 'edit' || $_GET['sub'] == 'new'):?>
			<button type="submit" id="submit" class="btn bgm-orange btn-icon waves-effect waves-circle waves-float"><i class="zmdi zmdi-check"></i></button>
		<?php else:?>
			<a href="?sub=single&mode=edit&id=<?php echo $_GET['id'];?>" class="btn bgm-red btn-icon waves-effect waves-circle waves-float"><i class="zmdi zmdi-edit"></i></a>
		<?php endif;?>
		
		
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
												<th>&nbsp;</th>
												<?php for($i = 1; $i < 5; $i++): $name = 'tgl_t'.$i;?>
													<?php if($_GET['sub'] == 'new' || $_GET['mode'] == 'edit'):?>													
														<th>		
															<div class="input-group edit">
																<span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
																<div class="fg-line">
																	<input name="<?php echo $name;?>" type="text" class="form-control date-picker"  value="<?php echo $$name;?>" id="date<?php echo $i;?>" autocomplete="off">
																</div>
															</div>
															<div class="view text-right">
																<?php echo $$name;?>
															</div>
														</th>
													<?php else:?>														
														<?php if($$name):?>
														<th>		
															<div class="input-group edit">
																<span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
																<div class="fg-line">
																	<input name="<?php echo $name;?>" type="text" class="form-control date-picker"  value="<?php echo $$name;?>" id="date<?php echo $i;?>" autocomplete="off">
																</div>
															</div>
															<div class="view text-right">
																<?php echo $$name;?>
															</div>
														</th>
														<?php endif;?>
													
													<?php endif;?>
												
												<?php endfor;?>
												<th class="total">
													TOTAL
												</th>
										</tr>
								</thead>
								<tbody>
									<?php foreach($this->items as $k => $v):?>										
										<tr class="money-row">
												<td class="f-500 info"><?php echo $v;?>
													<input type="hidden" name="<?php echo $k;?>" value="<?php echo $$k;?>" id="<?php echo $k;?>" autocomplete="off"/>
												</td>
												<?php for($i= 1; $i< 5; $i++):$name = $k.'_'.$i;$t = 't'.$i;?>
													<?php if($_GET['sub'] == 'new' || $_GET['mode'] == 'edit'):?>
														<td>
														
															<div class="edit">
																<input type="text" value="<?php echo $$name;?>" class="<?php echo $k;?> money <?php echo $t;?> <?php echo $name;?>"  autocomplete="off"/>
															</div>
															
															<div class="view text-right total"><?php echo $$name;?></div>
															
															<input type="hidden" name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php echo $$name;?>" autocomplete="off"/>
															
														</td>
													<?php else:$tgl = 'tgl_t'.$i;?>
														<?php if($$tgl):?>
															<td>
															
																<div class="edit">
																	<input type="text" value="<?php echo $$name;?>" class="<?php echo $k;?> money <?php echo $t;?> <?php echo $name;?>"  autocomplete="off"/>
																</div>
																
																<div class="view text-right total"><?php echo $$name;?></div>
																
																<input type="hidden" name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php echo $$name;?>" autocomplete="off"/>
																
															</td>
														<?php endif;?>
													<?php endif;?>
												<?php endfor;?>
												
												<td class="total" id="total_<?php echo $k;?>"><?php echo $$k;?></td>
										</tr>
									<?php endforeach;?>
										
										<tr class="success">
											<td class="text-right">TOTAL:</td>
											<?php for($i = 1; $i< 5; $i++): $t = 't'.$i;$tgl = 'tgl_t'.$i;?>
												<?php if($_GET['mode'] == 'edit' || $_GET['sub'] == 'new'):?>
													<td class="total" id="total_t<?php echo $i;?>"><?php echo $$t;?></td>
												<?php else:?>
													<?php if($$tgl):?>
														<td class="total" id="total_t<?php echo $i;?>"><?php echo $$t;?></td>
													<?php endif;?>
												<?php endif;?>
											<?php endfor;?>
											<td class="total" id="total_general"><?php echo $uang_masuk;?></td>
										</tr>
								</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end nota --->
			
			<!-- foto nota -->
			<div class="row">
				<div class="col-sm-6 col-md-6">
					<?php if($_GET['sub'] == 'edit' || $_GET['sub'] == 'new'):?>
						<div id="image">
							<div class="progress collapse">
								<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
								</div>
							</div>
							
							<label for="<?php echo $i;?>" <?php echo $style;?> id="img<?php echo $i;?>">
								<?php echo $bg;?>
								<input style="opacity: 0;" type="file" id="<?php echo $i;?>">
								<div class="uploader"></div>
							</label>
							
						</div>
					<?php else:?>
						<img class="img-responsive" src="<?php echo $image_url;?>" />
					<?php endif;?>
				</div>
			</div>
			<!-- foto nota -->
			</div>
		</div>
			<input type="hidden" name="image_url" id="image_url" value="<?php echo $image_url;?>" autocomplete="off"/>
			<input type="hidden" name="image_path" id="image_path" value="<?php echo $image_path;?>" autocomplete="off"/>
			<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>" autocomplete="off"/>
			<input type="hidden" name="uang_masuk" id="uang_masuk" value="<?php echo $uang_masuk;?>" autocomplete="off"/>
			<input type="hidden" name="t1" id="t1" value="<?php echo $t1;?>" autocomplete="off"/>
			<input type="hidden" name="t2" id="t2" value="<?php echo $t2;?>" autocomplete="off"/>
			<input type="hidden" name="t3" id="t3" value="<?php echo $t3;?>" autocomplete="off"/>
			<input type="hidden" name="t4" id="t4" value="<?php echo $t4;?>" autocomplete="off"/>
			
		</form>
		<?php
	}
	public function footer(){
			if($this->single){
				extract($this->data);
			}
		?>
			<script>
				$(function() {	
					var userId;
					
					/* input uang */
					$(".money, .total, .view-money").autoNumeric('init', {aSep: '.', aDec:',', mDec:0}); 
					
					if(get.sub == 'single'){
						console.log('single');
						/* uid */
						get_user_profile(get.id);
						
						$('#image label').css({
							'background-image': 'url( <?php echo $image_url;?> )', 
							'background-size':"100% 100%"
						});
						
						/* copy each input value to hidden input */
						$('.money').each(function(){						
							/* copy value to hidden input */
							curClass = $(this).attr('class').split(' ')[3];
							val = $(this).autoNumeric('get');
							$('#'+curClass).val(val);
						});
					}
					
					/* upload gambar */					
					$('input[type=file]').gaptekmediaUpload({ 
						uploadUrl : '?action=img',
						removeImgUrl: '?action=remove_img',
						success: function(r){
							$('#image_url').val(r.imageUrl);
							$('#image_path').val(r.imagePath);
						}
					});
					
					/* hitung total */
					$('.money').keyup(function(){
						
						/* copy value to hidden input */
						curClass = $(this).attr('class').split(' ')[3];
						
						val = $(this).autoNumeric('get');
						$('#'+curClass).val(val);
						
						/* count total general */
						var total_general = 0;
						$('.money').each(function(){
							val = $(this).autoNumeric('get');
							if(val > 0){
								val = parseInt(val);
								total_general += val;
							}
						});
						$('#total_general').autoNumeric('set',total_general);
						$('#uang_masuk').val(total_general);
						
						/* count total row */
						var hRow = 0;
						rowClass = $(this).attr('class').split(' ')[0];
						$('.'+rowClass).each(function(){
							val = $(this).autoNumeric('get');
							if(val > 0){
								val = parseInt(val);
								hRow += val;
							}
						});						
						$('#total_'+rowClass).autoNumeric('set',hRow);
						$('#'+rowClass).val(hRow);
						
						/* count total column */
						var col = 0;
						colClass = $(this).attr('class').split(' ')[2];
						$('.'+colClass).each(function(){
							val = $(this).autoNumeric('get');
							if(val > 0){
								val = parseInt(val);
								col += val;
							}
						});						
						$('#total_'+colClass).autoNumeric('set',col);
						$('#'+colClass).val(col);
						
					});
					
					
					/* submit */
					$('form').submit(function(e){
						e.preventDefault();
						
						if(!userId){
							alert('Harap isi dulu uid nya');
							return;
						}
						
						$.ajax({
							url: '?action=add_nota_uang_masuk',
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
					
					/* month picker */
					if ($('.date-picker')[0]) {
						
						$('.date-picker').datetimepicker({
								format: 'DD-MMMM YYYY'
						}).on("dp.change", function(e) {
							id = $(this).attr('id');
							if(userId){
								// ajax maybe
							}else{
								$(this).val('');
							}
						});
					}
					
					$('#uid').blur(function(){
						uid = $(this).val();				
						get_user_profile(uid);
					});
					
					function get_user_profile(uid){						
						$.ajax({
							url: '?action=profile&uid='+uid,
							beforeSend: function(){
								$('#info').hide();
								userId = false;
							},
							success: function(r){console.log(r);
								if(!r.foto_baru){
									alert('tidak dapat menemukan mahasiswa dengan uid tersebut');
									return;
								}
								
								userId = uid;
								$('#user_id').val(uid);
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
		<?php
	}
}