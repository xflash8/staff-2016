<?php 
$pagename = get_query_var('pagename');
$class = str_replace('-', '_', $pagename).'_Card';
$c = new $class;
get_header();
$c->display();
get_footer();