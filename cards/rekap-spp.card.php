<?php
class Rekap_SPP_Card extends Card{
	var $table = 'rekap-spp';
	public $pagetitle = "Rekap SPP";
	public $columns = array(
		'nim'=>'NIM',
		'display_name'=>'Nama Lengkap',
		'spp'=>'SPP',
		'pembayaran'=>'Pembayaran',
		'kali'=>'x',
		'sisa'=>'Sisa'
	);
	
	function query(){
		global $wpdb;
		$q = "
			select 
				meta.user_id, 
				users.display_name,
				meta.meta_value nim, 
				left(meta.meta_value, 2) angkatan, 
				right(meta.meta_value, 3) seq,
				meta2.meta_value spp,
				mutasi.ID mutasi_id, 
				count(mutasi.credit) kali,
				coalesce(sum(mutasi.credit),0) pembayaran,
				(meta2.meta_value - coalesce(sum(mutasi.credit),0)) sisa,
				meta3.meta_value status,
				meta4.meta_value maba
			from $wpdb->usermeta meta
			
			left join $wpdb->users users
				on users.ID = meta.user_id			
				
			left join $wpdb->usermeta meta2
				on meta2.user_id = meta.user_id and meta2.meta_key = '_spp'
				
			left join $wpdb->usermeta meta3
				on meta3.user_id = meta.user_id and meta3.meta_key = '_2015_1_status'
				
			left join $wpdb->usermeta meta4
				on meta4.user_id = meta.user_id and meta4.meta_key = '_2015_1_maba'
			
			left join mutasi
				on mutasi.object_id = meta.user_id and mutasi.category like '%spp%'
			
			where meta.meta_key = '_nim_baru' 
			and ((meta3.meta_value not like 'alumni' and meta3.meta_value not like 'dropout') or meta3.meta_value is null)
			and (meta4.meta_value <> 1 or meta4.meta_value is null)
			
			group by nim
			order by angkatan ASC, seq ASC
			limit 0, 10
		";
		$r = $wpdb->get_results($q);//echo $q;print_r($r);
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
		<style>
				.edit{display: none;}
				input.money{ width:70px;}
				.alumni{color:red}
				.maba{color:green}
		</style>
		<div class="card"><?php $this->table_obj->display();?></div>
		<?php 
	}
	public function footer(){
		?>
			<script>
				$(function() {	
					if(get.mode == 'edit'){
						/* input uang */
						$(".money").autoNumeric('init', {aSep: '.', aDec:',', mDec:0}); 
						
						/* mode edit */
						$('.view, .edit').toggle();
						
						/* simpan perubahan */
						$('.money').change(function(e){
							e.preventDefault();
							data = {uid: $(this).attr('data-uid'), spp: $(this).autoNumeric('get'), nim: $(this).attr('data-nim'), default: $(this).attr('data-default')};
							console.log(data);
							var $this = $(this);
							
							$.ajax({
								url: '?ajax=update_spp',
								data: data,
								type: 'POST',
								success: function(r){
									console.log('nim '+data.nim+' berhasil disimpan');
									e.preventDefault();
									var nFrom = 'top';
									var nAlign = 'right';
									var nType = 'inverse';
									var nAnimIn = 'animated bounceIn';
									var nAnimOut = $(this).attr('animated bounceOut');
									var text = 'nim '+data.nim+' berhasil disimpan';
									notify(text, nFrom, nAlign, nType, nAnimIn, nAnimOut);

								},
								error: function(er){ 
									$this.autoNumeric('set',data.default);
									alert('data user dengan nim '+data.nim+' tidak dapat disimpan. Harap cek koneksi internet anda');
								}
							});
						});
						
						/* notify */
						
            function notify(text, from, align, type, animIn, animOut){
							$.growl({
								title: '',
								message: text,
								url: ''
							},{
								element: 'body',
								type: type,
								allow_dismiss: true,
								placement: {
												from: from,
												align: align
								},
								offset: {
										x: 20,
										y: 85
								},
								spacing: 10,
								z_index: 1031,
								delay: 2500,
								timer: 1000,
								url_target: '_blank',
								mouse_over: false,
								animate: {
												enter: animIn,
												exit: animOut
								},
								icon_type: 'class',
								template: '<div data-growl="container" class="alert" role="alert">' +
																'<button type="button" class="close" data-growl="dismiss">' +
																		'<span aria-hidden="true">&times;</span>' +
																		'<span class="sr-only">Close</span>' +
																'</button>' +
																'<span data-growl="icon"></span>' +
																'<span data-growl="title"></span>' +
																'<span data-growl="message"></span>' +
																'<a href="#" data-growl="url"></a>' +
														'</div>'
							});
            };

						
					}
				});
			</script>
		<?php
	}
}