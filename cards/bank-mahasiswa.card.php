<?php
class Bank_Mahasiswa_Card extends Card{
	static $description = '<p>Halaman ini berisi mahasiswa yang terdaftar secara resmi sebagai mahasiswa stiba.<br />Yaitu yang telah memiliki nim dan telah menyetorkan berkas NIMKO</p>';
	var $editable = true;
	var $pagetitle = "Bank Mahasiswa";
	var $items = array(
		'display_name'=>'Nama Lengkap',
		'nim'=>'NIM',
		'tempat_lahir'=>'Tempat Lahir',
		'tanggal_lahir'=>'Tanggal Lahir',
		'gender'=>'Jenis Kelamin',
	);
	var $js = array(
	);
	
	var $table = 'bank-mahasiswa';
	
	function display_list(){
		?>
		
		<?php $this->card_header();?>
		<style>
				.edit{display: none;}
				input.money{ width:70px;}
				.alumni, .maba{color:green}
				.do{color:red}
		</style>
				
		<?php if(current_user_can('edit_bank-mahasiswa')):?>
		<!-- Add button -->
		<a target="_blank" class="btn btn-float btn-danger m-btn" href="?sub=new"><i class="zmdi zmdi-plus"></i></a>
		<?php endif;?>
		
		<div class="card"><?php $this->table_obj->display();?></div>
		<?php 
	}
	public function display_new(){
		?>
		<?php $this->card_header();?>
		
		<!-- start nota --->
			<div class="card">
				<div class="card-body m-t-0">
					<div class="table-responsive">
						<form method="post" id="create-mahasiswa">
						<table class="table table-vmiddle">
								<tbody>
									<?php foreach($this->items as $k => $v):?>										
										<tr>
												<td class="f-500 text-right  b-0"><?php echo $v;?></td>												
												<td class="text-left text-money b-0">
													<div class="fg-line">
														<?php if($k == 'tanggal_lahir'):?>
															<input type="text" placeholder="contoh: 23/05/2014" data-mask="00/00/0000" class="input-text form-control input-mask" maxlength="10" autocomplete="off" name="tanggal_lahir">
														<?php elseif($k == 'gender'):?>
															<label class="radio radio-inline m-r-20">
                                <input type="radio" value="1" name="gender" class="input-radio">
                                <i class="input-helper"></i>  
                                Laki-Laki
															</label>
															<label class="radio radio-inline m-r-20">
                                <input type="radio" value="2" name="gender" class="input-radio">
                                <i class="input-helper"></i>  
                                Perempuan
															</label>
														<?php else:?>
															<input type="text" placeholder="<?php echo $v;?>" class="input-text form-control" name="<?php echo $k;?>"/>
														<?php endif;?>
													</div>
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
	function display_ubah_nim(){
		$user = new WP_User($_GET['user_id']);
		
		?>
		<?php $this->card_header();?>
		
		<!-- start nota --->
			<div class="card">
				<div class="card-body m-t-0">
					<div class="table-responsive">
						<form method="post" id="update-nim">
						<table class="table table-vmiddle">
								<tbody>
									<tr>
											<td class="f-500 text-right  b-0">Nama Lengkap</td>												
											<td class="text-left b-0"> 
													<?php echo ucwords($user->display_name);?>
											</td>
									</tr>
									<tr>
											<td class="f-500 text-right  b-0">TTL</td>												
											<td class="text-left b-0"> 
													<?php echo ucwords($user->tempat_lahir).', '.$user->tanggal_lahir;?>
											</td>
									</tr>
									<tr>
											<td class="f-500 text-right  b-0">NIM Lama</td>												
											<td class="text-left b-0"> 
													<?php echo $user->_nim_baru;?>
											</td>
									</tr>

									<tr>
											<td class="f-500 text-right  b-0">NIM Baru</td>												
											<td class="text-left b-0">
												<div class="fg-line">
													<input type="text" class="input-text form-control" name="nim"/>
												</div>
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
						<input type="hidden" name="user_id" value="<?php echo $_GET['user_id'];?>"/>
						</form>
					</div>
				</div>
			</div>
			<!-- end nota --->
		<?php
	}
	public function footer(){
		?>
			<script>
				$(function() {	
					$('form#update-nim').submit(function(e){
						e.preventDefault();
						$.ajax({
							url: '?ajax=update_nim',
							data: $(this).serialize(),
							type: 'POST',
							beforeSend: function(){
								$('#submit').attr('disabled', 'disabled');
							},
							success: function(){
								$('#submit').removeAttr('disabled');
								alert('sukses Bro');
							},
							error: function(){
								alert('Ups, sepertinya ada masalah dengan internet anda');
							}
						});
					});
					
					$('form#create-mahasiswa').submit(function(e){
						e.preventDefault();
						$.ajax({
							url: '?ajax=create_mahasiswa',
							data: $(this).serialize(),
							type: 'POST',
							beforeSend: function(){
								$('#submit').attr('disabled', 'disabled');
							},
							success: function(){
								$('.input-text').val('');
								$('.input-radio').attr('checked', false);
								$('#submit').removeAttr('disabled');
								alert('success bro');
							},
							error: function(){
								alert('Ups, sepertinya ada masalah dengan internet anda');
							}
						});
					});
					
					$('.set-alumni, .set-maba, .remove-user, .remove-nim').click(function(e){
						e.preventDefault();
						userId = $(this).attr('data-id');
						name = $(this).attr('data-name');
						action = $(this).attr('data-action');
						console.log('start');
						t = $(this);
						$.ajax({
							url:'?ajax='+ action +'&user_id=' + userId,
							type: 'POST',
							success: function(){
								console.log(name + 'success');
							if(action == 'remove_user' || action == 'remove_nim'){
									t.parent().parent().remove();
								}
							},
							error: function(){
								alert(name + 'gagal di set');
							}
						});
					});
				
				});
			</script>
		<?php
	}
}