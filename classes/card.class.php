<?php
class Card{
	var $classes = array();
	var $table;
	var $js = array();
	var $css = array();
	var $data = array();
	var $columns = array();
	var $column_align = array();
	var $pagename;
	var $pagetitle;
	var $menus = array();
	var $alt_menus = array();
	var $editable;
	
	function __construct($id = false){
		$this->pagename = get_query_var('pagename');
		
		$this->log();
		
		$this->id = $id;		
		$this->get_classes();
		
		if($this->table){
			$this->get_table();
		}
		
		$this->ajax_handler();
		add_action('wp_enqueue_scripts', array($this, 'js'));
		add_action('after_footer', array($this, 'footer'));
		
		if($_GET['action'] == 'xls'){
			$this->get_excel();
		}
	}
	function log(){
		if($_GET['sub']){
			$pagename = $this->pagename.' sub '.$_GET['sub'];
			$action = 'view';
		}elseif($_GET['mode']){
			$pagename = $this->pagename.' sub '.$_GET['mode'];
			$action = 'view';
		}elseif($_GET['action']){
			$pagename = $this->pagename.' sub '.$_GET['action'];
			$action = 'view';
		}else{
			$pagename = $this->pagename;
			$action = 'view';
		}
		
		log::insert($pagename, $action, get_current_user_id(), $action);
	}
	public function get_menus(){
		$menu_prepend = array();
		
		/* edit */
		if($_GET['mode'] == 'edit'){		
			$edit = array(
				'url'=>'?',
				'icon'=>'eye',
				'text'=>'Mode View',
				'cap'=>'edit-'.$this->pagename,
			);
		}else{			
			$edit = array(
				'url'=>'?mode=edit',
				'icon'=>'edit',
				'text'=>'Mode Edit',
				'cap'=>'edit-'.$this->pagename,
			);
		}
		
		if($this->editable)
			$menu_prepend[] = $edit;
		
		/* excel */
		$u = '?action=xls';
		if($_GET){
			foreach($_GET as $k => $v){
				$p[]= $k.'='.$v;
			}
			
			$p = implode('&', $p);
			
			$u = $u .'&'. $p;
		}
		
		
		$xl = array(
			'url'=>$u,
			'icon'=>'download',
			'text'=>'Download Excel',
			'cap'=>'view_'.$this->pagename,
		);		
		if($this->table)
			$menu_prepend[] = $xl;
				
		$menu = array_merge($menu_prepend, $this->menus);
		
		$menu = apply_filters('card_header_menu', $menu);
		return $menu;
	}
	public function get_pagetitle(){
		return $this->pagetitle;
	}
	public function get_columns($array){
		$column = apply_filters('card_columns', $this->columns);
		return $columns;
	}
	function set_columns($array){
		$this->columns = $array;
	}
	function get_classes()
	{
		foreach($this->classes as $filename => $type){
			include_once get_template_directory().'/'.$type.'/'.$filename.'.php';
		}
	}
	function get_table()
	{
		include_once get_template_directory().'/tables/'.$this->table.'.table.php';
		$c = str_replace('-', '_', $this->table) . '_List_Table';
		$this->table_obj = new $c;
		
	}
	function display(){
		if($_GET['sub']){
			$method = 'display_'.$_GET['sub'];
			if(method_exists($this, $method)){
				$this->$method();
			}
		}else{
			$this->display_list();
		}
	}
	function display_table(){
		?>
		<div class="table-responsive">
			<table class="table table-striped">
				<?php $this->display_table_header();?>
				<?php $this->display_table_rows();?>
			</table>
		</div>
		<?php
	}
	public function display_table_header(){
		?>		
			<tr>
				<?php foreach($this->columns as $column_name => $v):
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
	public function display_table_rows(){
		if($this->data):
			$this->index = 0;
			foreach($this->data as $item):
				$this->index++;
				?>		
				<tr>
					<?php foreach($this->columns as $column_name => $v):
						$class = array('column_'.$column_name);
						if($this->column_align[$column_name])
							$class[] = 'text-'.$this->column_align[$column_name];
						
						$class = implode(' ', $class);
						?>
						<td class="<?php echo $class;?>">
							<?php 
								$method = 'column_'.$column_name;
								if(method_exists($this, $method)){
									$this->{$method}($item, $column_name);
								}else{
									$this->column_default($item, $column_name);
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
				<td colspan="6" class="text-center">No Entry</td>
			</tr>
			<?php
		endif;
	}
	public function column_default($item, $column_name){
		echo $item->$column_name;
	}
	public function column_index(){
		echo $this->index;
	}
	public function card_header(){
		?>		
		<div class="block-header">
			<h2><?php echo $this->get_pagetitle();?></h2>
			<ul class="actions">
			
				<?php foreach($this->get_menus() as $k => $v):?>
					<?php if(current_user_can($v['cap'])):$id=$v['id']?'id="'.$v['id'].'"':'';?>
					<li class="hidden-xs" <?php echo $id;?>>
							<a href="<?php echo $v['url'];?>">
									<i class="zmdi zmdi-<?php echo $v['icon'];?>"></i>
							</a>
					</li>
					<?php endif;?>
				<?php endforeach;?>
				
				<li class="dropdown visible-xs">
						<a data-toggle="dropdown" href="" aria-expanded="false">
								<i class="zmdi zmdi-more-vert"></i>
						</a>
						
						<ul class="dropdown-menu dropdown-menu-right">
						
							<?php foreach($this->get_menus() as $k => $v):?>								
								<?php if(current_user_can($v['cap'])): $id=$v['id']?'id="'.$v['id'].'"':'';?>
									<li <?php echo $id;?>>
											<a href="<?php echo $v['url'];?>"><?php echo $v['text'];?> </a>
									</li>
								<?php endif;?>
							<?php endforeach;?>
							
						</ul>
				</li>
			</ul>
		</div>
		<?php
	}
	public function js(){
		if($this->css){
			foreach($this->css as $css){
				wp_enqueue_style($css);
			}
		}
		
		if($this->js){
			foreach($this->js as $js){
				if(is_array($js)){
					wp_enqueue_script($js[0]);
					if($js[1])
						wp_enqueue_style($js[0]);
				}else{
					wp_enqueue_script($js);
				}
			}
		}
	}
	
	public function ajax_handler(){
		extract($_REQUEST);
		$method = 'ajax_'.$action;
		if(method_exists($this, $method)){
			$r = $this->$method();
			header('Content-Type: application/json');
			echo json_encode($r);
			exit;
		}
	}
	public function get_excel(){		
		$x = new Excel($this->table_obj);
		$x->setFilename($this->pagetitle);
		$x->display();
		exit;
	}
	
	public function get_data(){
		return $this->query();
	}
	public function query(){}
	public function footer(){}
}