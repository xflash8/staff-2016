<?php
	if(!$_GET['sub']){
		$c = new mutasi_card();
		foreach($c->rek_list as $rek_item){
			$x = new mutasi_card($rek_item['number']);
			$x->set_limit(0, 5);
			$x->widget = true;
			$reks[] = $x;
		}
	}else{
		$c = new mutasi_card($_GET['id']);
	}

get_header();
	
if($_GET['sub'] == 'mutasi'):
	$c->display();
elseif($_GET['sub'] == 'transaction'):
	$c->display_transaction();
elseif($_GET['sub'] == 'category'):
	$c->display_category();
else:
	$c->card_header();
	foreach($reks as $rek):
		$rek->display();
	endforeach;
endif;
	
get_footer();