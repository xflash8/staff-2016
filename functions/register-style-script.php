<?php
/* js & css */
add_action( 'wp_enqueue_scripts', function(){
	/* css first */
	wp_enqueue_style( 'animate');
	wp_enqueue_style( 'sweet-alert');
	wp_enqueue_style( 'material-design-iconic-font');
	
	/* crucial js */
	wp_enqueue_script( 'jquery.nicescroll');
	wp_enqueue_script( 'waves');
	wp_enqueue_script( 'bootstrap-growl');
	wp_enqueue_script( 'sweet-alert');
	
	if(is_home()){
		wp_enqueue_script( 'curved-line-chart');
		wp_enqueue_script( 'line-chart');
		wp_enqueue_script( 'charts');
	}
	
	if(is_page('my-profile')){
		wp_enqueue_style( 'bootstrap-datetimepicker');
		wp_enqueue_script( 'bootstrap-datetimepicker');
	}
	global $current_user;
	?>
	<script>	
		get = {};
		
		<?php foreach($_GET as $k => $v):?>
			get.<?php echo $k;?>='<?php echo $v;?>';
		<?php endforeach;?>
		
		// pagename
		get.pagename = '<?php echo get_query_var('pagename');?>';
		
		par = [];
		for (var key in get) {
			if (get.hasOwnProperty(key) && get[key].length > 0) {
				par.push(key+"="+get[key]);
			}
		}
		
		data = {};
		<?php if(is_home()):?>
			data.is_home = true;
		<?php elseif(is_page()):?>
			data.is_page = true;
			data.page = '<?php echo get_query_var('pagename');?>';
		<?php endif;?>
		
		user = {};
		<?php foreach($current_user->data as $k => $v): if($k == 'user_pass') continue;?>
			user.<?php echo $k;?>='<?php echo $v;?>';
		<?php endforeach;?>
		
		
		
	</script>
	<?php
	wp_enqueue_script( 'functions');
	wp_enqueue_script( 'demo');
});

/* The style and script */
/* Vendor CSS */
wp_register_style('fullcalendar', get_stylesheet_directory_uri().'/vendors/bower_components/fullcalendar/dist/fullcalendar.min.css');
wp_register_style('animate', get_stylesheet_directory_uri().'/vendors/bower_components/animate.css/animate.min.css');
wp_register_style('sweet-alert', get_stylesheet_directory_uri().'/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css');
wp_register_style('material-design-iconic-font', get_stylesheet_directory_uri().'/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css');
wp_register_style('bootstrap-datetimepicker', get_stylesheet_directory_uri().'/vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');
wp_register_style('bootgrid', get_stylesheet_directory_uri().'/vendors/bootgrid/jquery.bootgrid.min.css');
wp_register_style( 'jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
wp_register_style('jquery.fakeLoader', get_stylesheet_directory_uri().'/css/fakeLoader.css');

/* jquery */
wp_deregister_script('jquery');
wp_register_script( 'jquery', get_stylesheet_directory_uri() . '/vendors/bower_components/jquery/dist/jquery.min.js', array(), '2.1.4', true );

/* twitter bootsrap */
wp_register_script( 'bootstrap', get_stylesheet_directory_uri() . '/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js', array('jquery'), '3.3.4', true );

/* date time manipulation */
wp_register_script( 'moment', get_stylesheet_directory_uri() . '/vendors/bower_components/moment/min/moment.min.js', array(), '', true );

/* click effect inspired by material design */
wp_register_script( 'waves', get_stylesheet_directory_uri() . '/vendors/bower_components/Waves/dist/waves.min.js', array(), '', true );

/* advance alert http://t4t5.github.io/sweetalert/ */
wp_register_script( 'sweet-alert', get_stylesheet_directory_uri() . '/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.min.js', array(), '', true );


/* jquery plugin  */
/* jquery number formatting */
wp_register_script( 'jquery.maskMoney', get_stylesheet_directory_uri() . '/js/jquery.maskMoney.js', array('jquery'), '', true );
wp_register_script( 'jquery.number', get_stylesheet_directory_uri() . '/js/jquery.number.min.js', array('jquery'), '', true );
wp_register_script( 'jquery.autoNumeric', get_stylesheet_directory_uri() . '/js/jquery.autoNumeric.js', array('jquery'), '', true );

/* jquery fake loader */

wp_register_script( 'jquery.fakeLoader', get_stylesheet_directory_uri() . '/js/jquery.fakeLoader.min.js', array('jquery'), '', true );

/* other jquery plugin */
wp_register_script( 'jquery.flot', get_stylesheet_directory_uri() . '/vendors/bower_components/flot/jquery.flot.js', array('jquery'), '', true );
wp_register_script( 'jquery.sparkline', get_stylesheet_directory_uri() . '/vendors/sparklines/jquery.sparkline.min.js', array('jquery')  , '', true);
wp_register_script( 'jquery.easy-pie-chart', get_stylesheet_directory_uri() . '/vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js', array('jquery'), '', true );
wp_register_script( 'fullcalendar', get_stylesheet_directory_uri() . '/vendors/bower_components/fullcalendar/dist/fullcalendar.min.js', array('jquery', 'moment') , '', true);
wp_register_script( 'jquery.simpleWeather', get_stylesheet_directory_uri() . '/vendors/bower_components/simpleWeather/jquery.simpleWeather.min.js', array('jquery') , '', true);
wp_register_script( 'jquery.nicescroll', get_stylesheet_directory_uri() . '/vendors/bower_components/jquery.nicescroll/jquery.nicescroll.min.js', array('jquery', 'bootstrap'), '', true );
wp_register_script( 'jquery-placeholder', get_stylesheet_directory_uri() . '/vendors/bower_components/jquery-placeholder/jquery.placeholder.min.js' , array('jquery'), '', true);
wp_register_script( 'bootgrid', get_stylesheet_directory_uri() . '/vendors/bootgrid/jquery.bootgrid.min.js' , array('jquery', 'bootstrap'), '', true);
wp_register_script( 'jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js' , array('jquery', 'bootstrap'), '', true);
wp_register_script( 'typeahead', get_stylesheet_directory_uri() . '/vendors/bower_components/typeahead.js/dist/typeahead.bundle.min.js' , array('jquery', 'bootstrap'), '', true);
/* date time picker */
wp_register_script( 'bootstrap-datetimepicker', get_stylesheet_directory_uri() . '/vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js' , array('jquery', 'bootstrap', 'moment'));

/* multiple notification, its not alert */
wp_register_script( 'bootstrap-growl', get_stylesheet_directory_uri() . '/vendors/bootstrap-growl/bootstrap-growl.min.js', array('jquery','bootstrap') , '', true);

/* flot plugin */
wp_register_script( 'flot.resize', get_stylesheet_directory_uri() . '/vendors/bower_components/flot/jquery.flot.resize.js', array('jquery.flot') , '', true);
wp_register_script( 'flot.curvedlines', get_stylesheet_directory_uri() . '/vendors/bower_components/flot.curvedlines/curvedLines.js' , array('jquery.flot') , '', true);


/* custom script */
wp_register_script( 'curved-line-chart', get_stylesheet_directory_uri() . '/js/flot-charts/curved-line-chart.js', array('flot.resize', 'flot.curvedlines'), '', true  );
wp_register_script( 'line-chart', get_stylesheet_directory_uri() . '/js/flot-charts/line-chart.js', array('flot.resize', 'flot.curvedlines'), '', true  );

wp_register_script( 'charts', get_stylesheet_directory_uri() . '/js/charts.js', array('flot.resize', 'flot.curvedlines', 'jquery.sparkline', 'jquery.easy-pie-chart'), '', true ); 
wp_register_script( 'functions', get_stylesheet_directory_uri() . '/js/functions.js' , array('jquery', 'bootstrap'), '', true);
wp_register_script( 'demo', get_stylesheet_directory_uri() . '/js/demo.js' , array('jquery', 'bootstrap'), '', true);
wp_register_script( 'file-upload', get_stylesheet_directory_uri() . '/js/file-upload.js' , array('jquery'), '', true);
wp_register_script( 'list-table', get_stylesheet_directory_uri() . '/js/list-table.js' , array('jquery'), '', true);

