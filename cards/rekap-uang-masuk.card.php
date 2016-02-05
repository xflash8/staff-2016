<?php
class Rekap_Uang_Masuk_Card extends Card{
	public $pagetitle = "Rekap Uang Masuk";
	public $columns = array(
		'nim'=>'NIM',
		'display_name'=>'Nama Lengkap',
		'spp'=>'SPP',
		'pembayaran'=>'Pembayaran',
		'kali'=>'x',
		'sisa'=>'Sisa'
	);
	public $menus = array(
		array(
			'url'=>'?action=xls',
			'icon'=>'download',
			'text'=>'Download Excel',
			'cap'=>'view_rekap-uang-masuk',
		),
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
	
	public $js = array(
		array('bootstrap-datetimepicker', 1),
		array('file-upload'),
		array('jquery.autoNumeric'),
		array('jquery.fakeLoader', 1),
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
				coalesce(meta2.meta_value,2850000) uang_masuk,
				mutasi.ID mutasi_id, 
				count(mutasi.credit) kali,
				coalesce(sum(mutasi.credit),0) pembayaran,
				(coalesce(meta2.meta_value,2850000) - coalesce(sum(mutasi.credit),0)) sisa 
			from $wpdb->usermeta meta
			
			left join $wpdb->users users
				on users.ID = meta.user_id			
				
			left join $wpdb->usermeta meta2
				on meta2.user_id = meta.user_id and meta2.meta_key = 'uang_masuk'
				
			left join $wpdb->usermeta meta3
				on meta3.user_id = meta.user_id and meta3.meta_key = '_2015_1_maba'
			
			left join mutasi
				on mutasi.object_id = meta.user_id and mutasi.category = 'uang_masuk'
			
			where meta.meta_key = '_nim_baru' and meta3.meta_value = 1
			
			group by nim
			order by sisa DESC
		";
		$r = $wpdb->get_results($q);
		return $r;
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
		</style>
		
		<div class="card">
						
			<!-- start table -->
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
					<tr>
						<th>#</th>
						<th>NIM</th>
						<th>Nama</th>
						<th>Uang Masuk</th>
						<th>Pembayaran</th>
						<th>sisa</th>
					</tr>
					</thead>
					<tbody>
					<?php $r = $this->query();if($r):?>
						<?php foreach($r as $k => $v): $index = $k + 1;?>
							<tr>
								<td class="select-cell">								
									<?php echo $index;?>
								</td>
								<td>
									<?php echo ucwords($v->nim);?>
								</td>
								<td>
									<?php echo ucwords($v->display_name);?>
								</td>
								<td>
									<?php echo number_format($v->uang_masuk, 0, ',', '.');?>
								</td>
								<td>
									<?php if($v->pembayaran > 0):?>
										<a href="/pembayaran-uang-masuk/?user_id=<?php echo $v->user_id;?>" target="_blank">
										<?php echo number_format($v->pembayaran, 0, ',', '.');?>
										</a>
										<?php if($v->kali > 1):?>
											(<?php echo $v->kali;?>x)
										<?php endif;?>
									<?php else:?>
										<?php echo $v->pembayaran;?>
									<?php endif;?>
								</td>
								<td>
									<?php echo number_format($v->sisa, 0, ',', '.');?>
								</td>
							</tr>
						<?php endforeach;?>
					<?php else:?>
					
					<?php endif;?>
					</tbody>
				</table>
			</div>
			<!-- end table -->
			
			<!-- pagination -->
			<div class="row">
				<div class="col-sm-6">
					<ul class="pagination">
						<li class="first" aria-disabled="false"><a href="#first" class="button"><i class="zmdi zmdi-more-horiz"></i></a></li>
						<li class="prev" aria-disabled="false"><a href="#prev" class="button"><i class="zmdi zmdi-chevron-left"></i></a></li>
						<li class="page-1" aria-disabled="false" aria-selected="false"><a href="#1" class="button">1</a></li><li class="page-2 active" aria-disabled="false" aria-selected="true"><a href="#2" class="button">2</a></li>
						<li class="next disabled" aria-disabled="true"><a href="#next" class="button"><i class="zmdi zmdi-chevron-right"></i></a></li>
						<li class="last disabled" aria-disabled="true"><a href="#last" class="button"><i class="zmdi zmdi-more-horiz"><i></i></i></a></li>
					</ul>
				</div>
				<div class="col-sm-6 infoBar">
					<div class="infos">Showing 11 to 20 of 20 entries</div>
				</div>
			</div>
			<!-- pagination -->
		</div>
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