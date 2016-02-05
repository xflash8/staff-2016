<?php
class Registrasi_Mahasiswa_Card extends Card{
	var $table = 'registrasi-mahasiswa';
	public $pagetitle = "Registrasi Mahasiswa";
	public $columns = array(
		'index'=>'#',
		'date'=>'Tanggal',
		'code' => 'Kode',
		'nim' => 'NIM',
		'display_name' => 'Nama Lengkap',
		'credit'=>'Kredit',
	);
	public $items = array(
		'date'=>'Tanggal/Waktu',
		'credit'=>'Jumlah',
		'description'=>'Deskripsi',
	);
	
	public $menus = array(
		array(
			'url'=>'?sub=new',
			'icon'=>'file-plus',
			'text'=>'Tambah mahasiswa penundaan',
			'cap'=>'edit_registrasi-mahasiswa'
		),
		array(
			'url'=>'?sub=kelas',
			'icon'=>'folder-person',
			'text'=>'Manajemen Kelas',
			'cap'=>'edit_registrasi-mahasiswa'
		),
	);
	
	var $periode = array(
		array(
			'slug'=>'2015_2',
			'text'=>'2015/2016 Genap',
			'cap'=>'edit_registrasi-mahasiswa',
		),
		array(
			'slug'=>'2015_1',
			'text'=>'2015/2016 Ganjil',
			'cap'=>'edit_registrasi-mahasiswa',
		),
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
	
	function query_mutasi($id){
		global $wpdb;
		$q = "select * from mutasi where ID = $id";
		$r = $wpdb->get_row($q);
		return $r;
	}
	function query_list(){
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
			where category like 'spp%'
			$w_user
			order by date DESC
			limit 0, 10
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
				$this->display_new();
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
		<div class="card"><?php $this->table_obj->display();?></div>
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
				// main page
				$(function() {						
					kelas = [
						{},
						{prodi: "pmh", semester: 2, room: "A"},
						{prodi: "pmh", semester: 2, room: "B"},
						{prodi: "pmh", semester: 2, room: "C"},
						{prodi: "pmh", semester: 2, room: "D"},
						{prodi: "pmh", semester: 2, room: "E"},
						{prodi: "pmh", semester: 2, room: "F"},
						{prodi: "pmh", semester: 2, room: "G"},
						{prodi: "pmh", semester: 2, room: "H"},
						{prodi: "pmh", semester: 2, room: "I"},
						{prodi: "pmh", semester: 2, room: "J"},
						{prodi: "pmh", semester: 2, room: "K"},
						{prodi: "pmh", semester: 2, room: "L"},
						{prodi: "pmh", semester: 4, room: "A"},
						{prodi: "pmh", semester: 4, room: "B"},
						{prodi: "pmh", semester: 4, room: "C"},
						{prodi: "pmh", semester: 4, room: "D"},
						{prodi: "pmh", semester: 4, room: "E"},
						{prodi: "pmh", semester: 4, room: "F"},
						{prodi: "pmh", semester: 4, room: "G"},
						{prodi: "pmh", semester: 6, room: "A"},
						{prodi: "pmh", semester: 6, room: "B"},
						{prodi: "pmh", semester: 8, room: "A"},
						{prodi: "pmh", semester: 8, room: "B"},
						{prodi: "pku", semester: 2, room: "A"},
						{prodi: "pku", semester: 2, room: "B"},
						{prodi: "pku", semester: 4, room: "A"},
						{prodi: "pku", semester: 4, room: "B"},
					]; 
				
					$('#set-kelas-btn').click(function(ev){
						ev.preventDefault();
						
						$(this).text('Loading...');
							
						var cb = [];
						$('.cb:checkbox:checked').each(function() {
							cb.push($(this).val());
						});
						
						var r = $('#kelas').val();
						
						data = {periode: '_2015_2', cb: cb, prodi: kelas[r].prodi, semester: kelas[r].semester, room: kelas[r].room, };
						
						console.log(data);
						$.ajax({
							url: '?ajax=set_kelas',
							type: 'POST',
							data: data,
							beforeSend: function(){},
							success: function(data){
								console.log(data);
								window.location.reload();
							},
							error: function(e){
								alert('error connection');
							}
						});
					});
					
					$('#filter-kelas-btn').click(function(ev){
						ev.preventDefault();
						var r = $('#kelas').val();
						url = '?prodi='+kelas[r].prodi+'&semester='+kelas[r].semester+'&room='+kelas[r].room;
						window.location.href = url;
					});
					
				});
			</script>
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
						t = $(this);
						tid = $(this).attr('data-id');
						$.ajax({
							url: '?ajax=cancel_transaction&id='+tid,
							type : 'POST',
							beforeSend: function(){
								console.log('doing ajax, please wait...');
							},
							success:function(r){
								t.parent().parent().remove();
							},
							error: function(){
								alert('something is wrong. maybe your internet connection');
							}
						});
					});
					
					/* submit */
					$('#form').submit(function(e){
						e.preventDefault();
						
						
						if(!userId){
							alert('Harap isi dulu uid nya');
							return;
						}
						
						$.ajax({
							url: '?ajax=add_mahasiswa_penundaan',
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
							url: '?ajax=get_profile&uid='+uid,
							type : 'POST',
							beforeSend: function(){
								$('#info').hide();
								userId = false;
							},
														success: function(r){console.log(r);
								if(!r.ID){
									alert('tidak dapat menemukan mahasiswa dengan uid tersebut');
									return;
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
		<?php
	}
	
	function display_new(){
		?>
		<div class="fakeLoader"></div>
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
												<th class="text-right">Form Penundaan</th>
												<th class="text-left">&nbsp</th>
										</tr>
								</thead>
								<tbody>
									<tr>
											<td class="f-500 info text-right">Periode</td>												
											<td class="text-left text-money">
												<select name="periode" autocomplete="off" id="category">
													<?php foreach($this->periode as $c):?>
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
}