<?php
class Group_Info_Card extends Card{
	
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
					<li class="active waves-effect"><a href="?sub=group-info">Info</a></li>
					<li class="waves-effect"><a href="?sub=group-member">Member</a></li>
					<li class="waves-effect"><a href="?sub=group-log">Logs</a></li>
			</ul>
			<div class="card-body card-padding">
				<?php
				$class = str_replace('-', '_', $pagename) . '_card';
				if(class_exists($class)){
					if(property_exists($class, 'description')){
						echo $class::$description;
					}else{
						echo '<div role="alert" class="alert alert-info">Maaf, saat ini deskripsi belum tersedia. Silahkan hubungi administrator sistem. Trims</div>';
					}
				}
				?>
			</div>
		</div>
		
		
	<?php
	}
}