<?php
class Group_Log_Card extends Card{
	var $table = 'log';
	function display(){
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
					<li class="waves-effect"><a href="?sub=group-member">Member</a></li>
					<li class="active waves-effect"><a href="?sub=group-log">Logs</a></li>
			</ul>
			<div class="card-body card-padding">
				<?php
					$this->table_obj->display();
				?>
				
			</div>
		</div>
		
		
	<?php
	}
}