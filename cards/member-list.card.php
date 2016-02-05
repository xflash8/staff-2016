<?php
class Member_List_Card extends Card{
	
	public function display(){
		global $current_user;
		$pagename = get_query_var('pagename');
	
	?>
		<?php if(current_user_can('edit_'.$pagename)):?>
		<!-- Add button -->
		<a class="btn btn-float btn-danger m-btn" href="?sub=member-add"><i class="zmdi zmdi-plus"></i></a>
		<?php endif;?>
		
		<div class="card">
		
			<ul class="tab-nav tn-justified" style="overflow: hidden;" tabindex="1">
					<li class="waves-effect"><a href="?sub=group-info">Info</a></li>
					<li class="active waves-effect"><a href="?sub=member-list">Member</a></li>
					<li class="waves-effect"><a href="?sub=group-log">Logs</a></li>
			</ul>
			<div class="listview lv-bordered lv-lg">
				
				<div class="lv-body">
				
				
					<?php	
						global $current_blog;
						$users = new WP_User_Query( 
							array( 
								'blog_id' => $current_blog->blog_id,
								'orderby' => 'display_name', 
								'order' => 'ASC'  
							) 
						);
					?>
					<?php foreach($users->get_results() as $user):?>
						<?php if($user->has_cap('view_'.$pagename)):
							$collapse= ($user->has_cap('edit_'.$pagename))?'collapse':'';
							$show= ($user->has_cap('edit_'.$pagename))?'':'collapse';
						?>
							<div class="lv-item media item-<?php echo $user->ID;?>">
								<div class="pull-left">
									<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/2.jpg" alt="">
								</div>
								<div class="media-body">
									<div class="lv-title">
										<?php echo ($current_user->ID == $user->ID)?"Anda Sendiri":ucwords($user->display_name);?>
									</div>
									<small class="lv-small">jabatan</small>
									
									<ul class="lv-attrs">
										<li>Date Added: 09/06/1988</li>
										<li class="status-<?php echo $user->ID;?> <?php echo $show;?>">Admin</li>
									</ul>
									
									
										<?php if(current_user_can('edit_'.$pagename) && $current_user->ID != $user->ID):?>
											<div class="lv-actions actions dropdown" id="user-menu-<?php echo $user->ID;?>">
												<a href="" data-toggle="dropdown" aria-expanded="true"><i class="zmdi zmdi-more-vert"></i></a>
						
												<ul class="dropdown-menu dropdown-menu-right" id="<?php echo $user->ID;?>">
													<li class="<?php echo $collapse;?>">
														<a href="#" class="promote ">Beri Akses Penuh</a>
													</li>
													
													<?php if(!$user->has_cap('edit_'.$pagename) || current_user_can('manage_sites')):?>
														<li class="<?php echo $show;?>">
															<a href="#" class="downgrade ">Batasi Akses</a>
														</li>
														<li>
															<a href="#" class="remove">Keluarkan</a>
														</li>
													<?php endif;?>
													
													<li class="">
														<a href="#" class="send-message">Kirim Pesan</a>
													</li>
													
												</ul>
											</div>
										<?php endif;?>
								</div>
							</div>
						<?php endif;?>
					<?php endforeach;?>
				</div>
			</div>
				
		</div>
		
		
	<?php
	}
	public function footer(){
		?>
		<script>
		 /*
		 * Notifications
		 */
		function notify(message, from, align, icon, type, animIn, animOut){
				$.growl({
						icon: icon,
						title: message,
						message: '',
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
			$(document).ready(function(){
				$('.collapse').hide();
				
				$('a.remove').click(function(e){
					e.preventDefault();
					
					data = {
						action: 'remove',
						user_id: $(this).parent().parent().attr('id')
					};
					$(this).parent().parent().parent().removeClass('open');
					$('.item-'+data.user_id).remove();
					console.log(data);
					$.post( "", data, function( r) {
						console.log(r);
					}).fail(function(){
						console.log('gagal');
					});
					notify('Berhasil', 'top', 'center', '', 'success', 'animated bounceIn', 'animated bounceOut');
				});
				
				$('a.promote').click(function(e){
					e.preventDefault();
					
					data = {
						action: 'promote',
						user_id: $(this).parent().parent().attr('id')
					};
					
					t = $(this);
					
					/* close the menu */
					t.parent().parent().parent().removeClass('open');
					
					/* hide the button */
					t.parent().hide();
					
					$.post( "", data, function( r) {
						t.parent().next().show();
						$('.status-'+data.user_id).show();
						notify('Berhasil', 'top', 'center', '', 'success', 'animated bounceIn', 'animated bounceOut');
					}).fail(function(){
						console.log('gagal');
					});
				});
				
				$('a.downgrade').click(function(e){
					e.preventDefault();
					
					data = {
						action: 'downgrade',
						user_id: $(this).parent().parent().attr('id')
					};
					t = $(this);
					
					/* close the menu */
					t.parent().parent().parent().removeClass('open');
					
					/* hide the button */
					t.parent().hide();
					
					$.post( "", data, function( r) {
						t.parent().prev().show();
						$('.status-'+data.user_id).hide();
						notify('Berhasil', 'top', 'center', '', 'success', 'animated bounceIn', 'animated bounceOut');
					}).fail(function(){
						console.log('gagal');
					});
					
					
				});
				
			});
		</script>
		<?php
	}
}