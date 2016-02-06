<?php
$incs = array(
	/* classes */
	'excel.class' => 'classes',
	'excel-reader.class' => 'classes',
	'card.class' => 'classes',
	'list-table.class' => 'classes',
	'log.class' => 'classes',
	'sms.class' => 'classes',
	'botnet-bsm.class' => 'classes',
	'query-builder.class' => 'classes',
	
	/* functions */
	'register-style-script' => 'functions',
	'no-frontpage' => 'functions',
	'page-as-group' => 'functions',
	'ajax-handler' => 'functions',
	'login' => 'functions',
	
	/* cards */
	//'calendar.card' => 'cards',
	//'weather.card' => 'cards',
	'member-add.card' => 'cards',
	'group-member.card' => 'cards',
	'group-info.card' => 'cards',
	'group-log.card' => 'cards',
	'rekap-spp.card' => 'cards',
	'keringanan-uang-masuk.card' => 'cards',
	'pembayaran-uang-masuk.card' => 'cards',
	'pembayaran-spp.card' => 'cards',
	'rekap-spp.card' => 'cards',
	'rekap-uang-masuk.card' => 'cards',
	'mahasiswa-tanpa-nim.card' => 'cards',
	'bank-pegawai-dosen.card' => 'cards',
);

foreach($incs as $file => $type){
	include_once get_template_directory().'/'.$type.'/'.$file.'.php';
}

/* disable admin bar on frontpage */
show_admin_bar( false ); 

add_filter('template_include', function($template){
	
	$slug = get_query_var('pagename');
	$filename = get_template_directory().'/cards/'.$slug.'.card.php';
	
	if(file_exists($filename)){
		include_once $filename;
		$template = get_template_directory().'/template.php';
		header("HTTP/1.1 200 OK");
	}
	return $template;
});