<?php
class Member_Add_Card extends Card{
	
	public function display(){
	?>
		<style>
			button.cancel{display:none;}
		</style>
		
		<!-- Add button -->
		<a class="btn btn-float btn-danger m-btn" href="?sub=group-member"><i class="zmdi zmdi-arrow-left"></i></a>
		
		<div class="card">
				<div class="lv-header-alt clearfix m-b-5">
					<h2 class="lvh-label">Tambah Peserta</h2>
				</div>
				
				
				<div class="card-body card-padding">
						<?php 
						
							global $current_blog;
							$users = new WP_User_Query( 
								array( 
									'blog_id' => $current_blog->blog_id,
									'orderby' => 'display_name', 
									'order' => 'ASC'  
								) 
							);
							$pagename = get_query_var('pagename');
						?>
						<div class="contacts clearfix row">
							<?php foreach($users->get_results() as $user):?>
								<?php if(!$user->has_cap('view_'.$pagename)):?>
									<div class="col-md-2 col-sm-4 col-xs-6 item-<?php echo $user->ID;?>">
											<div class="c-item">
													<a href="" class="ci-avatar">
															<img src="<?php bloginfo('template_url');?>/img/contacts/7.jpg" alt="">
													</a>

													<div class="c-info">
															<strong><?php echo $user->display_name;?></strong>
															<small><?php echo $user->user_email;?></small>
													</div>

													<div class="c-footer" id="<?php echo $user->ID;?>">
															<button class="waves-effect add bgm-blue">Tambah</button>
													</div>
											</div>
									</div>
								<?php endif;?>
							<?php endforeach;?>
						</div>
						<div class="load-more">
								<a href=""><i class="zmdi zmdi-refresh-alt"></i> Load More...</a>
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
				console.log(get);
				$('button.add').click(function(){
					
					data = {
						user_id: $(this).parent().attr('id')
					};
					
					$('.item-'+data.user_id).remove();
					
					$(this).hide().next().show();
					
					$.ajax({
						url: '?ajax=member_add&pagename='+get.pagename,
						type: 'POST',
						data: data,
						success: function(r){
							console.log(r);
							notify('Berhasil', 'top', 'center', '', 'success', 'animated bounceIn', 'animated bounceOut');
						},
						error: function(){
							alert('Error: tidak dapat menambahkan user');
						}
					});
					
					/*
					$.post( "", data, function( r) {
						notify('Berhasil', 'top', 'center', '', 'success', 'animated bounceIn', 'animated bounceOut');
					}).fail(function(){
						console.log('gagal');
					});
					*/
				});
				
			});
		</script>
		<?php
	}
}