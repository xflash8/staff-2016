<?php
class Bank_Pegawai_Dosen_Card extends Card{
	static $description = '<p>Halaman ini berisi Daftar pegawai baik yang masih aktif maupun yang tidak</p>';
	var $editable = true;
	var $pagetitle = "Bank Pegawai dan Dosen";
	
	var $items = array(
		'display_name'=>'Nama Lengkap',
		'nim'=>'NIM',
		'tempat_lahir'=>'Tempat Lahir',
		'tanggal_lahir'=>'Tanggal Lahir',
		'gender'=>'Jenis Kelamin',
	);
	var $js = array(
	);
	
	var $table = 'bank-pegawai-dosen';
	
	function display(){
		$sub = 'display_'.str_replace('-', '_', $_GET['sub']);
		if(method_exists($this, $sub)){
			$this->$sub();
		}else{
			$this->display_list();
		}
	}
	function display_list(){
		$this->card_header();?>
		
		<style>
				.edit{display: none;}
				input.money{ width:70px;}
				.alumni, .maba{color:green}
				.do{color:red}
		</style>
				
		<?php if(current_user_can('edit_bank-pegawai-dosen')):?>
			<!-- Add button -->
			<a target="_blank" class="btn btn-float btn-danger m-btn" href="?sub=new"><i class="zmdi zmdi-plus"></i></a>
		<?php endif;?>
		
		<div class="card"><?php $this->table_obj->display();?></div>
		<?php 
	}
	public function display_new(){
		$data = array(
			'username'=>'Username',
			'display_name'=>'Nama Lengkap',
			'phone' => 'Hp',
			'user_email' => 'Email',
			'gender'=>'Jenis Kelamin'
		);
		
		$gender[1] = 'Laki-laki';
		$gender[2] = 'Perempuan';
		?>
		<?php $this->card_header();?>
		
		<!-- start nota --->
			<div class="card">
				<div class="card-body m-t-0">
					<div class="table-responsive">
						<form method="post" id="create-mahasiswa">
						<table class="table table-vmiddle">
								<tbody>
									<?php foreach($data as $k => $v):?>
										<tr>
												<td class="f-500 text-right  b-0"><?php echo $v;?></td>												
												<td class="text-left b-0"> 
													<?php if($k == 'gender'):?>
														<?php foreach($gender as $a => $b):?>
														<label class="radio radio-inline m-r-20">
															<input type="radio" value="<?php echo $a;?>" name="gender" class="input-radio">
															<i class="input-helper"></i>  
															<?php echo $b;?>
														</label>
														<?php endforeach;?>
													<?php else:?>
														<input type="text" placeholder="<?php echo $v;?>" class="input-text form-control" name="<?php echo $k;?>" value=""/>
													<?php endif;?>
												</td>
										</tr>								
									<?php endforeach;?>
									<tr>
											<td class="f-500 text-right b-0">&nbsp;</td>												
											<td class="text-left text-money b-0">
												<button class="btn btn-primary waves-effect" type="submit" id="submit">Simpan</button>
											</td>
									</tr>
								</tbody>
						</table>
						</form>
					</div>
				</div>
			</div>
			<!-- end nota --->
		<?php
	}
	function display_edit_data(){
		$user = new WP_User($_GET['id']);
		$data = array(
			'display_name'=>'Nama Lengkap',
			'phone' => 'Hp',
			'user_email' => 'Email',
			'gender'=>'Jenis Kelamin'
		);
		
		$gender[1] = 'Laki-laki';
		$gender[2] = 'Perempuan';
		?>
		<?php $this->card_header();?>
		
		<!-- start nota --->
			<div class="card">
				<div class="card-body m-t-0">
					<div class="table-responsive">
						<form method="post" id="update-data">
						<table class="table table-vmiddle">
							<tbody>
								<tr>
									<td class="f-500 text-right b-0">username</td>												
									<td class="text-left text-money b-0">
										<?php echo $user->user_login;?>
									</td>
								</tr>
								<?php foreach($data as $k => $v):?>
									<tr>
											<td class="f-500 text-right  b-0"><?php echo $v;?></td>												
											<td class="text-left b-0"> 
												<?php if($k == 'gender'):?>
													<?php foreach($gender as $a => $b): $checked = ($a == $user->gender)?'checked="checked"':'';?>
													<label class="radio radio-inline m-r-20">
														<input type="radio" value="<?php echo $a;?>" name="gender" class="input-radio" <?php echo $checked;?>>
														<i class="input-helper"></i>  
														<?php echo $b;?>
													</label>
													<?php endforeach;?>
												<?php else:?>
													<input type="text" placeholder="<?php echo $v;?>" class="input-text form-control" name="<?php echo $k;?>" value="<?php echo $user->$k;?>"/>
												<?php endif;?>
											</td>
									</tr>								
								<?php endforeach;?>
								<tr>
									<td class="f-500 text-right b-0">&nbsp;</td>												
									<td class="text-left text-money b-0">
										<button class="btn btn-primary waves-effect" type="submit" id="submit">Simpan</button>
									</td>
								</tr>
							</tbody>
						</table>
						<input type="hidden" name="user_id" value="<?php echo $user->ID;?>"/>
						</form>
					</div>
				</div>
			</div>
			<!-- end nota --->
		<?php
	}
	
	function display_sms(){
				
		$data = array(
			'display_name'=>'Nama',
			'phone' => 'Hp',
			'gender'=>'Jenis Kelamin'
		);
		if($_GET['id']){
			$user = new WP_User($_GET['id']);
		}else{
			$group = true;
			$user = new stdClass;
			$user->display_name = 'Pegawai dan Pengelola';
			unset($data['phone']);
			unset($data['gender']);
		}
			

		
		$gender[1] = 'Laki-laki';
		$gender[2] = 'Perempuan';
		?>
		<?php $this->card_header();?>
		<!-- start nota --->
			<div class="card">
				<div class="card-body m-t-0">
					<?php if($user->phone || $group):?>
						<div class="table-responsive">
							<form method="post" id="send-sms">
							<table class="table">
								<tbody>
									<?php foreach($data as $k => $v):?>
										<tr>
												<td class="f-500 text-right  b-0"><?php echo $v;?></td>												
												<td class="text-left b-0"> 
													<?php echo $user->$k;?>
												</td>
										</tr>								
									<?php endforeach;?>
									<tr>
										<td class="f-500 text-right b-0">sms</td>												
										<td class="text-left text-money b-0">
											<textarea name="text" placeholder="Ketik pesan anda...." rows="5" class="form-control"></textarea>
										</td>
									</tr>
									<tr>
										<td class="f-500 text-right b-0">&nbsp;</td>												
										<td class="text-left text-money b-0">
											<button class="btn btn-primary waves-effect" type="submit" id="submit">Simpan</button>
										</td>
									</tr>
								</tbody>
							</table>
							<?php if($_GET['id']):?>
								<input type="hidden" name="user_id" value="<?php echo $user->ID;?>"/>
							<?php endif;?>
							
							<?php if($_GET['gender']):?>
								<input type="hidden" name="gender" value="<?php echo $_GET['gender'];?>"/>
							<?php endif;?>
							</form>
						</div>
					<?php else:?>
						<p>Maaf, saat ini user tidak dapat menerima sms karena tidak memiliki nomor hp. Isi dolo dong nomor hpnya. :)</p>
					<?php endif;?>
				</div>
			</div>
			<!-- end nota --->
		<?php
	}
	public function footer(){
		?>
			<script>
				$(function() {	
					$('form#update-data').submit(function(e){
						e.preventDefault();
						$.ajax({
							url: '?ajax=update_data_pegawai',
							data: $(this).serialize(),
							type: 'POST',
							beforeSend: function(){
								$('#submit').attr('disabled', 'disabled');
							},
							success: function(r){
								$('#submit').removeAttr('disabled');
								console.log(r);
								alert('sukses Bro');
								
							},
							error: function(){
								alert('Ups, sepertinya ada masalah dengan internet anda');
							}
						});
					});
					
					$('form#send-sms').submit(function(e){
						if(get.id){
							action = 'send_sms';
						}else{
							action = 'send_sms_pegawai';
						}
						
						e.preventDefault();
						$.ajax({
							url: '?ajax='+action,
							data: $(this).serialize(),
							type: 'POST',
							beforeSend: function(){
								$('#submit').attr('disabled', 'disabled');
							},
							success: function(r){
								$('#submit').removeAttr('disabled');
								console.log(r);
								alert('sukses Bro');
								
							},
							error: function(r){
								console.log(r);
								alert('Ups, sepertinya ada masalah dengan internet anda');
							}
						});
					});
				
				});
			</script>
		<?php
	}
}