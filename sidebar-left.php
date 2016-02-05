<aside id="sidebar">
	<div class="sidebar-inner c-overflow">
		<div class="profile-menu">
			<a href="">
				<div class="profile-pic">
					<img src="<?php bloginfo('template_url');?>/img/profile-pics/2.jpg" alt="">
				</div>

				<div class="profile-info">
					<?php global $current_user;echo $current_user->display_name;?>
					<i class="zmdi zmdi-arrow-drop-down"></i>
				</div>
			</a>

			<ul class="main-menu">
				<li>
						<a href="/my-profile/"><i class="zmdi zmdi-account"></i> View Profile</a>
				</li>
				<li>
						<a href=""><i class="zmdi zmdi-input-antenna"></i> Privacy Settings</a>
				</li>
				<li>
						<a href=""><i class="zmdi zmdi-settings"></i> Settings</a>
				</li>
				<li>
						<a data-action="clear-localstorage" href=""><i class="zmdi zmdi-delete"></i> Clear Local Storage</a>
				</li>
				<li>
						<a href="<?php echo wp_logout_url('/');?>"><i class="zmdi zmdi-time-restore"></i> Logout</a>
				</li>
			</ul>
		</div>
			
		<?php
		$menu = array(
			array(
				'slug'=>'index.php',
				'title'=>'Home',
				'icon'=>'home'
			),
			array(
				'slug'=>'#',
				'title'=>'Akademik',
				'icon'=>'accounts-list',
				'child'=>array(
					array(
						'slug'=>'registrasi-mahasiswa',
						'title'=>'Registrasi Mahasiswa',
					),
					array(
						'slug'=>'bank-mahasiswa',
						'title'=>'Bank Mahasiswa',
					),
					array(
						'slug'=>'mahasiswa-tanpa-nim',
						'title'=>'Perubahan NIM',
					),
				),
			),
			array(
				'slug'=>'#',
				'title'=>'Pegawai & Dosen',
				'icon'=>'accounts-list',
				'child'=>array(
					array(
						'slug'=>'bank-pegawai-dosen',
						'title'=>'Bank Pegawai & Dosen',
					),
				),
			),
			array(
				'slug'=>'#',
				'title'=>'Bagian Keuangan',
				'icon'=>'balance-wallet',
				'child'=>array(
					array(
						'slug'=>'mutasi-rekening-utama',
						'title'=>'Mutasi Rekening Utama'
					),
					array(
						'slug'=>'pembayaran-uang-masuk',
						'title'=>'Pembayaran Uang Masuk'
					),
					array(
						'slug'=>'pembayaran-spp',
						'title'=>'Pembayaran SPP'
					),
					array(
						'slug'=>'keringanan-uang-masuk',
						'title'=>'Nota Keringanan Uang Masuk'
					),
					array(
						'slug'=>'rekap-uang-masuk',
						'title'=>'Rekap Uang Masuk'
					),
					array(
						'slug'=>'rekap-spp',
						'title'=>'Rekap SPP'
					),
				)
			),
		);
		
		/* remove restricted menu */
		foreach($menu as $i => $l1){
			if(!$l1['child'])
				continue;
			
			foreach($l1['child'] as $j => $l2){
				if(!current_user_can('view_'.$l2['slug']))
					unset($menu[$i]['child'][$j]);
			}
		}
		
		/* remove main menu without child */
		foreach($menu as $i => $l1){
			if(!$l1['child'] && $l1['slug'] != 'index.php')
				unset($menu[$i]);
		}
		
		?>
		<ul class="main-menu">
			<?php foreach($menu as $m):
				$name = get_query_var('pagename');
				if(!$name)
					$name = 'index.php';
				
				$active = ($name == $m['slug'])?'active':'';
				if($m['child']):?>
					<li class="sub-menu <?php echo $active;?>">
						<a href=""><i class="zmdi zmdi-<?php echo $m['icon'];?>"></i> <?php echo $m['title'];?></a>

						<ul>
							<?php foreach($m['child'] as $c): ?>
								<li><a href="/<?php echo $c['slug'];?>/"><?php echo $c['title'];?></a></li>
							<?php endforeach;?>
						</ul>
					</li>				
				<?php else:?>
					<li class="<?php echo $active;?>"><a href="/<?php echo $m['slug'];?>/"><i class="zmdi zmdi-<?php echo $m['icon'];?>"></i> <?php echo $m['title'];?></a></li>
				<?php endif;?>
			<?php endforeach;?>
		</ul>
	</div>
</aside>