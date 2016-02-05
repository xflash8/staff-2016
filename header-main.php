<header id="header">
		<ul class="header-inner">
				<li id="menu-trigger" data-trigger="#sidebar">
						<div class="line-wrap">
								<div class="line top"></div>
								<div class="line center"></div>
								<div class="line bottom"></div>
						</div>
				</li>
		
				<li class="logo hidden-xs">
						<a href="index.html">STIBA Makassar</a>
				</li>
				
				<li class="pull-right">
					<ul class="top-menu">
						<li id="toggle-width">
								<div class="toggle-switch">
										<input id="tw-switch" type="checkbox" hidden="hidden">
										<label for="tw-switch" class="ts-helper"></label>
								</div>
						</li>
						
						<?php if(current_user_can('tes')):?>						
						<li class="dropdown">
								<a data-toggle="dropdown" class="tm-message" href=""><i class="tmn-counts">6</i></a>
								<div class="dropdown-menu dropdown-menu-lg pull-right">
										<div class="listview">
												<div class="lv-header">
														Messages
												</div>
												<div class="lv-body">
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/1.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">David Belle</div>
																				<small class="lv-small">Cum sociis natoque penatibus et magnis dis parturient montes</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/2.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Jonathan Morris</div>
																				<small class="lv-small">Nunc quis diam diamurabitur at dolor elementum, dictum turpis vel</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/3.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Fredric Mitchell Jr.</div>
																				<small class="lv-small">Phasellus a ante et est ornare accumsan at vel magnauis blandit turpis at augue ultricies</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/4.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Glenn Jecobs</div>
																				<small class="lv-small">Ut vitae lacus sem ellentesque maximus, nunc sit amet varius dignissim, dui est consectetur neque</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/4.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Bill Phillips</div>
																				<small class="lv-small">Proin laoreet commodo eros id faucibus. Donec ligula quam, imperdiet vel ante placerat</small>
																		</div>
																</div>
														</a>
												</div>
												<a class="lv-footer" href="">View All</a>
										</div>
								</div>
						</li>
						<?php endif;?>
						
						<?php if(current_user_can('tes')):?>
						<li class="dropdown">
								<a data-toggle="dropdown" class="tm-notification" href=""><i class="tmn-counts">9</i></a>
								<div class="dropdown-menu dropdown-menu-lg pull-right">
										<div class="listview" id="notifications">
												<div class="lv-header">
														Notification
						
														<ul class="actions">
																<li class="dropdown">
																		<a href="" data-clear="notification">
																				<i class="zmdi zmdi-check-all"></i>
																		</a>
																</li>
														</ul>
												</div>
												<div class="lv-body">
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/1.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">David Belle</div>
																				<small class="lv-small">Cum sociis natoque penatibus et magnis dis parturient montes</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/2.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Jonathan Morris</div>
																				<small class="lv-small">Nunc quis diam diamurabitur at dolor elementum, dictum turpis vel</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/3.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Fredric Mitchell Jr.</div>
																				<small class="lv-small">Phasellus a ante et est ornare accumsan at vel magnauis blandit turpis at augue ultricies</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/4.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Glenn Jecobs</div>
																				<small class="lv-small">Ut vitae lacus sem ellentesque maximus, nunc sit amet varius dignissim, dui est consectetur neque</small>
																		</div>
																</div>
														</a>
														<a class="lv-item" href="">
																<div class="media">
																		<div class="pull-left">
																				<img class="lv-img-sm" src="<?php bloginfo('template_url');?>/img/profile-pics/4.jpg" alt="">
																		</div>
																		<div class="media-body">
																				<div class="lv-title">Bill Phillips</div>
																				<small class="lv-small">Proin laoreet commodo eros id faucibus. Donec ligula quam, imperdiet vel ante placerat</small>
																		</div>
																</div>
														</a>
												</div>
						
												<a class="lv-footer" href="">View Previous</a>
										</div>
						
								</div>
						</li>
						<?php endif;?>
						
						<?php
							$tasks = array(
								'Pusat Komputer' => 10,
								'Humas'=>80,
								'PMB'=>60,
								'P2B'=>40
							);	
						?>
						<?php if(current_user_can('tes')):?>
						<li class="dropdown">
							<a data-toggle="dropdown" class="tm-task" href=""><i class="tmn-counts">2</i></a>
							<div class="dropdown-menu pull-right dropdown-menu-lg">
								<div class="listview">
									<div class="lv-header">Program Kerja</div>
									<div class="lv-body">
										<?php foreach($tasks as $k => $v):
											if($v < 25){
												$class = 'danger';
											}else if($v >= 25 && $v < 50){
												$class = 'warning';
											}else if($v >= 50 && $v < 75){
												$class = 'default';
											}else if($v >= 75){
												$class = 'success';
											}
											$class = 'progress-bar-'.$class;
										?>
											<div class="lv-item">
												<div class="lv-title m-b-5"><?php echo $k;?></div>
												<div class="progress"><div class="progress-bar <?php echo $class;?>" role="progressbar" aria-valuenow="<?php echo $v;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $v;?>%"></div></div>
											</div>
										<?php endforeach;?>
									</div>
									<a class="lv-footer" href="">Lihat Semua</a>
								</div>
							</div>
						</li>
						<?php endif;?>
						
						
						<li class="" id="chat-trigger" data-trigger="#chat">
								<a class="tm-chat" href=""></a>
						</li>
						
					</ul>
				</li>
		</ul>
</header>