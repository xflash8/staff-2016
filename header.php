<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Portal Staff </title>

        
        
        
				<?php wp_head();?>
        <!-- CSS -->
        <link href="<?php bloginfo('template_url');?>/css/app.min.1.css" rel="stylesheet">
        <link href="<?php bloginfo('template_url');?>/css/app.min.2.css" rel="stylesheet">
				<!-- Custom CSS -->
        <link href="<?php bloginfo('template_url');?>/style.css" rel="stylesheet">
        
    </head>
    <body>
        <?php get_template_part('header-main');?>
        <section id="main">
						<?php get_template_part('sidebar-left');?>
						<?php get_template_part('sidebar-right');?>
						
						<?php if(!is_home()):?>
						<ol class="breadcrumb">
							<li><a href="<?php bloginfo('home');?>"><i class="zmdi zmdi-home"></i></a></li>
							<?php if($_GET['sub']):?>
								<li><a href="/<?php echo get_query_var('pagename');?>/"><?php echo ucwords(get_query_var('pagename'));?></a></li>
								<li class="active"><?php echo $_GET['sub'];?></li>
							<?php else:?>
								<li><a href="/<?php echo get_query_var('pagename');?>/"><?php echo ucwords(get_query_var('pagename'));?></a></li>
							<?php endif;?>
						</ol>
						<?php endif;?>
						
            <section id="content">
                <div class="container">