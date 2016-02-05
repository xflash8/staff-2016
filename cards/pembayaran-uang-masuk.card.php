<?php
class Pembayaran_Uang_Masuk_Card extends Card{
	const SLUG = 'pembayaran-uang-masuk';
	public $pagetitle = "Pembayaran Uang Masuk";
	public $menus = array(
		array(
			'url'=>'?sub=new',
			'icon'=>'file-plus',
			'text'=>'Buat Nota Baru',
			'cap'=>'edit_pembayaran-uang-masuk'
		),
		array(
			'url'=>'?action=xls',
			'icon'=>'download',
			'text'=>'Download Excel',
			'cap'=>'view_pembayaran-uang-masuk',
		),
	);
	var $columns = array(
		'index'=>'#',
		'date'=>'Tanggal/Waktu',
		'code'=>'Ref',
		'nim'=>'NIM',
		'display_name'=>'Nama Lengkap',
		'credit'=>'Jumlah'
	);
	public $items = array(
		'date'=>'Tanggal/Waktu',
		'credit'=>'Jumlah',
		'description'=>'Deskripsi',
	);
	
	public $js = array(
		array('bootstrap-datetimepicker', 1),
		array('file-upload'),
		array('jquery.autoNumeric'),
		array('jquery.fakeLoader', 1),
	);
	
	function get_data(){
		return $this->query();
	}
	
	function query_mutasi($id){
		global $wpdb;
		$q = "select * from mutasi where ID = $id";
		$r = $wpdb->get_row($q);
		return $r;
	}
	
	function query(){
		global $wpdb;
		if($_GET['user_id']){
			extract($_REQUEST);
			$w_user = "and mutasi.object_id = $user_id";
		}
		$q = "
			select mutasi.*, users.display_name, meta.meta_value nim from mutasi 
			left join $wpdb->users users 
				on users.ID = mutasi.object_id
			left join $wpdb->usermeta meta
				on meta.user_id = mutasi.object_id and meta.meta_key = '_nim_baru'
			where category = 'uang_masuk'
			$w_user
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
			'display_name'=>ucwords($user->display_name),
			'kabupaten_asal'=>$user->kabupaten_asal,
			'provinsi_asal'=>$user->provinsi_asal,
			'phone'=>$user->phone,
			'email'=>$user->email,
			'gender'=>$user->gender,
			
		);
		return $data;
	}
	
	function ajax_add_pembayaran_uang_masuk(){
		extract($_REQUEST);
		if(!$user_id)
			return array('status'=>false, 'detail'=>'no user id');
		
		global $wpdb;
		$data = array(
			'object_id'=>$user_id,
			'category'=>'uang_masuk'
		);
		
		$where = array(
			'ID'=>$ref_id
		);
		$wpdb->update('mutasi', $data, $where);
		
		return array('post'=>$_POST);
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
				if(!$_GET['ref']){
					$this->display_die();
				}else{
					$this->data = $this->query_mutasi($_GET['ref']);
					$this->display_single();
				}
			 break;
			case 'single':
				$this->single = true;
				$this->data = $this->query_mutasi($_GET['ref']);
				$this->display_single();
			 break;
		}
	}
	function display_die(){
		?>
		<h2>Error</h2>
		<p>Maaf, halaman yang anda tuju tidak valid</p>
		<?php
	}
	function display_list(){
		?>
		
		<?php $this->card_header();?>
		
		<div class="card">
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th>Date</th>
						<th>Ref</th>
						<th>NIM</th>
						<th>Nama</th>
						<th>Jumlah</th>
						<th>action</th>
					</tr>
					<?php $r = $this->query();if($r):?>
						<?php foreach($r as $k => $v):?>
							<tr>
								<td>
									<?php echo ucwords($v->date);?>
								</td>
								<td>
									<a href="?sub=single&ref=<?php echo $v->ID;?>" target="_blank"><?php echo $v->code;?></a>
								</td>
								<td>
									<?php echo $v->nim;?>
								</td>
								<td>
									<?php echo ucwords($v->display_name);?>
								</td>
								<td>
									<?php echo number_format($v->credit, 0, ',', '.');?>
								</td>
								<td>
									<a href="#" class="cancel_transaction" data-id="<?php echo $v->ID;?>">Cancel</a>
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
		?>	
		<style>
			.btn{
				position: fixed;
				bottom:100px;
				right:30px;
				z-index:99;
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
			<script>
				$(function() {	
					var userId;
					
					/* input uang */
					$(".text-money").autoNumeric('init', {aSep: '.', aDec:',', mDec:0}); 
					
					if(get.sub == 'single'){
						/* uid */
						get_user_profile(<?php echo $this->data->object_id;?>);
					}
					
					
					
					/* cancel transaction				 */
					$('.cancel_transaction').click(function(e){
						e.preventDefault();
						
						tid = $(this).attr('data-id');
						$.ajax({
							url: '?ajax=cancel_transaction&id='+tid,
							type : 'POST',
							beforeSend: function(){
								console.log('doing ajax, please wait...');
							},
							success:function(r){
								console.log(r);
							},
							error: function(){
								alert('something is wrong. maybe your internet connection');
							}
						});
					});					
					
					/* submit */
					$('form').submit(function(e){
						e.preventDefault();
						
						if(!userId){
							alert('Harap isi dulu uid nya');
							return;
						}
						
						$.ajax({
							url: '?action=add_pembayaran_uang_masuk',
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