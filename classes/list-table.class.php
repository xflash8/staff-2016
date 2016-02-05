<?php
class List_Table{
	var $par; // use this for general purpose
	var $data;
	var $columns = array();
	var $actions = array();
	var $start = 0;
	var $max;
	var $default_max = 10;
	var $page;
	var $total_page;
	var $current_count;
	var $total;
	var $max_option = array(10, 20, 25, 50, 100);
	var $search_query;
	var $excel = false;
	
	
	function __construct($par = null)
	{		
		$this->par = $par;
		
		/* excel  */
		if($_GET['action'] == 'xls')
			$this->excel = true;
		
		/* search query */
		if($_GET['src'])
			$this->search_query = $_GET['src'];
		
		/* filter by month */
		if($_GET['mth'] && $_GET['yr']){
			$this->mth = $_GET['mth'];
			$this->yr = $_GET['yr'];			
		}
		
		/* determine max row per page */
		if($_GET['max'] > 0){
			$this->set_max($_GET['max']);
			update_user_meta(get_current_user_id(), get_query_var('pagename').'_max', $_GET['max']);
		}else{
			$val = (int) get_user_meta(get_current_user_id(), get_query_var('pagename').'_max', true); 
			if($val > 0)
				$this->set_max($val);
		}		
		
		/* mysql limit start, max */
		$paged = $_GET['paged']?($_GET['paged'] - 1):0;
		$this->start = $paged * $this->get_max();
		
		if(method_exists($this, 'query')){
			$this->data = $this->query();			
			
			/* pagination */
			$this->page = $paged +1;
			$this->total_page = ceil($this->total / $this->get_max());
			$this->next_page = $this->page + 1;
			$this->prev_page = $this->page -1;
			
			foreach($_GET as $k => $v){
				if($k == 'paged' || $k == 'max') continue;
				
				$current_parameter .= '&' . $k . '=' . $v;
			}
			$this->first_url = '?paged=1' . $current_parameter;
			$this->next_url = '?paged='.$this->next_page . $current_parameter;
			$this->prev_url = '?paged='.$this->prev_page . $current_parameter;
			$this->last_url = '?paged='.$this->total_page . $current_parameter;
			
			wp_enqueue_script('list-table');
		}
	}
	
	function set_max($val)
	{
		$this->max = (int) $val;
	}
	
	function get_max(){
		return ($this->max > 0) ? $this->max : $this->default_max;
	}
	
	
	function get_columns(){
		$cols = $this->columns;
		if($_GET['mode'] == 'edit' && count($this->actions) > 0){
			$col_prepend = array('checkbox'=>'<input type="checkbox"/>');
			$col_append = array('action'=>'Action');
			$cols = array_merge($col_prepend, $cols, $col_append);
			
		}
		return $cols;
	}	
	
	function get_columns_xl(){
		return $this->get_columns();
	}
	function get_actions()
	{
		return $this->actions;
	}
	function limit_query()
	{
			return ($_GET['action'] == 'xls') ? '' : "limit $this->start, ".$this->get_max();
	}
	
	function display()
	{
		$this->css();
		?>
		<?php if($this->search_query):?>
		<div class="alert alert-info alert-dismissible">
			Menampilkan hasil penelusuran untuk <u><?php echo $this->search_query;?></u>. <a href="?" class="alert-link">Reset</a>
		</div>
		<?php elseif($this->mth):?>
		<div class="alert alert-info alert-dismissible">
			Menampilkan data untuk bulan <?php echo $this->mth;?>-<?php echo $this->yr;?>. <a class="alert-link" href="?">Reset</a>
		</div>
		<?php endif;?>
		
		<form method="get">
			<?php $this->table_nav('top');?>
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-condensed">
						<thead><?php $this->display_table_header();?></thead>
						<tbody><?php $this->display_table_rows();?></tbody>
						<tfoot><?php $this->display_table_header();?></tfoot>
					</table>
				</div>
			<?php $this->table_nav('bottom');?>
		</form>
		<?php
	}
	
	function css(){
		?>		
		<style>
			input.current-page{
				width:40px;
				padding:3px 5px;
				text-align:center;
			}
			
			.form-group{
				margin-bottom: 0;
			}
			
			.bootgrid-header{
				padding: 5px;
			}
			
			.input-search{
				width:200px;
				display:inline;
			}
			
			.dropdown{
				display:inline;
			}
			
			.dropdown-menu > .active > a{
				background-color: #f1f1f1;
			}
			
			.search-wrapper{
				padding:5px;
			}
			
			.table > tbody > tr:last-child > td{
				padding-bottom:5px;
				border-bottom:1px solid #f0f0f0 !important;
			}
			
			.table > tfoot > tr > th{
				border:1px solid #000;
			}
			
			.table-bordered{
				border-bottom:1px solid #f0f0f0;
			}
			
			.table > thead > tr > th:first-child, .table > tbody > tr > td:first-child,  .table > tfoot > tr > th:first-child{
				padding-left:14px;
			}
			
			.table > thead > tr > th, .table > tfoot > tr > th{
				background-color: #fff;
				vertical-align: middle;
				font-weight: 500;
				color: #333;
				border-width: 1px;
				text-transform: uppercase;
			}
			
		</style>
		<?php
	}
	
	function table_nav($position)
	{
		if($this->hide_nav)
			return;
		?>
		
		<?php if($position == 'top'):?>
			<div id="data-table-basic-header" class="bootgrid-header container-fluid">
				<div class="row">
				
					<!-- subs -->
					<div class="col-md-8">
						<?php $this->row_subs();?>
					</div>
					<!-- end subs -->
					
					<!-- Search and max row per page setting -->
					<div class="col-md-4 text-right">
							<input type="text" name="src" class="input-search form-control input-sm" placeholder="Search">
							<button class="btn bgm-bluegray waves-effect"><i class="zmdi zmdi-search"></i></button>							
							<?php $this->max_option();?>
					</div>
					<!-- end search and max row per page setting -->
					
				</div>
			</div>
		<?php endif;?>
		<div id="data-table-basic-header" class="bootgrid-header container-fluid">
		<div class="row table-nav">
			<?php if($position == 'top'):?>
				<div class="col-md-8">
					<?php $this->row_actions();?>
				</div>	
			<?php endif;?>
			
			<!-- Pagination -->
			<?php $this->pagination($position);?>
			<!-- End Pagination -->
			
		</div>
		</div>
		<?php
	}
	function pagination($position){		
		if($this->total == 0){
			$text_items = 'no item';
		}else if($this->total == 1){
			$text_items = '1 item';
		}else{
			$text_items = $this->total.' items';
		}
		$col = ($position == 'top')?'col-md-4':'col-md-12';?>
		<div class="<?php echo $col;?> text-right">
			<span class="total"><?php echo $text_items;?> </span>
			<?php if($this->total_page > 1):?>					
				<?php if($this->page == 1):?>
					<button class="btn bgm-bluegray" disabled="disabled">«</button>
					<button class="btn bgm-bluegray" disabled="disabled">‹</button>
				<?php else:?>
					<a href="<?php echo $this->first_url;?>" class="first-page btn bgm-bluegray">«</a>
					<a href="<?php echo $this->prev_url;?>" class="prev-page btn bgm-bluegray">‹</a>
				<?php endif;?>
				
				<?php if($position == 'top'):?>
					<input type="text" name="paged" value="<?php echo $this->page;?>" class="current-page"/>
				<?php else:?>
					<?php echo $this->page;?>
				<?php endif;?>
				of <?php echo $this->total_page;?>
				
				<?php if($this->page == $this->total_page):?>
					<button class="btn bgm-bluegray" disabled="disabled">›</button>
					<button class="btn bgm-bluegray" disabled="disabled">»</button>
				<?php else:?>
					<a href="<?php echo $this->next_url;?>" class="next-page btn bgm-bluegray">›</a>
					<a href="<?php echo $this->last_url;?>" class="last-page btn bgm-bluegray">»</a>
				<?php endif;?>
			<?php endif;?>
		</div>
		<?php
	}
	function max_option()
	{
		?>
		<div class="dropdown">
			<a href="#" class="dropdown-toggle btn bgm-bluegray waves-effect" data-toggle="dropdown" aria-expanded="false"><i class="zmdi zmdi-settings"></i></a>
			<ul class="dropdown-menu pull-right" id="max-option">
				<?php foreach($this->max_option as $v):$active = ($this->max == $v)?'active':'';?>
					<li class="<?php echo $active;?>">
						<a tabindex="-1" href="#" data-max="<?php echo $v;?>"><?php echo $v;?> baris</a>
					</li>
				<?php endforeach;?>
			</ul>
		</div>
		<?php
	}
	
	function get_total_column(){
		return count($this->get_columns());
	}
	
	function display_table_header(){
		?>
			<tr>
				<?php foreach($this->get_columns() as $column_name => $v):
						$class = array('column_'.$column_name);
						if($this->column_align[$column_name])
							$class[] = 'text-'.$this->column_align[$column_name];
						
						$class = implode(' ', $class);
					?>
					<th class="<?php echo $class;?>"><?php echo $v;?></th>
				<?php endforeach;?>
			</tr>
		<?php
	}
	
	function display_table_rows()
	{
		if($this->data):
			$this->index = $this->start;
			foreach($this->data as $item):
				$this->index++;
				?>		
				<tr>
					<?php foreach($this->get_columns() as $column_name => $v):
						$class = array('column_'.$column_name);
						if($this->column_align[$column_name])
							$class[] = 'text-'.$this->column_align[$column_name];
						
						$class = implode(' ', $class);
						?>
						<td class="<?php echo $class;?>">
							<?php 
								$method = 'column_'.$column_name;
								if(method_exists($this, $method)){
									echo $this->{$method}($item, $column_name);
								}else{
									echo $this->column_default($item, $column_name);
								}
							?>
						</td>
					<?php endforeach;?>
				</tr>
				<?php
			endforeach;
		else:
			?>
			<tr>
				<?php if($this->page > $this->total_page && $this->page > 1):?>
					<td colspan="<?php echo $this->get_total_column();?>" class="text-center">ERROR: Anda minta halaman <?php echo $this->page;?> padahal halaman yg ada cuma sampai <?php echo $this->total_page;?>. <br /> Hadeuh, Anda ini gimana sih!</td>
				<?php else:?>
					<td colspan="<?php echo $this->get_total_column();?>" class="text-center">No Entry</td>
				<?php endif;?>
			</tr>
			<?php
		endif;
	}
	function column_default($item, $column_name)
	{
		return $item->$column_name;
	}
	function column_checkbox($item, $column_name)
	{
		return '<input type="checkbox" name="cb" value="" class="cb"/>';
	}
	function column_index($item, $column_name)
	{
		if($this->excel){
			$this->index = $this->index + 1;
			return $this->index;
		}else{
			return $this->index;
		}
	}
	function column_action($item, $column_name)
	{
		foreach($this->get_actions() as $k => $v){
			if(current_user_can($c['cap']))
				$l[] = '<a class="action" href="?sub='.$k.'&id='.$item->user_id.'" target="_blank">'.$v['text'].'</a>';
		}
		
		if($l){
			return implode(' | ', $l);
		}
	}
	
	function column_gender($item){
		$g = array();
		$g[1] = 'L';
		$g[2] = 'P';
		return $g[$item->gender];
	}
	function row_sub_gender(){		
		$data[0] = 'Unknown';
		$data[1] = 'Laki-laki';
		$data[2] = 'Perempuan';
		
		if($_GET['gender']){
			$link[] = '<a href="?">Semua </a> (' . $this->total . ')';
		}else{
			$link[] = '<strong>Semua</strong> (' . $this->total . ')';
		}
		
		if($_GET){
        foreach($_GET as $k => $v){
            if($k == 'gender')
                continue;
            
            $p[$k] = $k.'='.$v;
        }
    }
    
    if($p)
        $p = implode('&', $p);
			
		foreach($this->subs as $d)
		{
			if($_GET['gender'] == $d->gender){
				$this->total = $d->total;
				$link []= '<strong>'.$data[$d->gender].' </strong> (' . $d->total . ')';
			}else{
				$link []= '<a href="?gender='.$d->gender.'&'.$p.'">'.$data[$d->gender].' </a> (' . $d->total . ')';
			}
		}
		
		$link = implode(' | ', $link);
		echo $link;
	}
	function row_actions(){}
	function row_subs(){
		$cols = $this->get_columns();
		if($cols['gender']){
			$this->row_sub_gender();
		}
	}
}