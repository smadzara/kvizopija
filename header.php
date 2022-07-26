<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kvizopija
 */

?>
<!doctype html>

<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

            <!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-WHSMNMQ');</script>
            <!-- End Google Tag Manager -->

        	<!-- Google tag (gtag.js) -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=G-8ZG00G03MQ"></script>
				<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());

				gtag('config', 'G-8ZG00G03MQ');
				</script>
			<!-- END Google tag (gtag.js) -->

			<!-- Global site tag (gtag.js) - Google Analytics -OLD- -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=UA-239932210-1"></script>
				<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());

				gtag('config', 'UA-239932210-1');
				</script>
			<!--END Global site tag (gtag.js) - Google Analytics -OLD- -->

            <!-- AdSense -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4983269975347159" crossorigin="anonymous"></script>
            <!-- AdSense - END -->

		<?php wp_head(); ?>

	</head>

<?php 

$questions_taxonomy_cat = 'questions_categories';
$questions_terms_cat = get_terms($questions_taxonomy_cat); // Get all terms of a questions taxonomy

?>
        

<?php /*
<nav class="navbar">
	<!-- LOGO -->
	<div class="logo"><a href="<?= get_home_url(); ?>" style="color: #fff;">pubkvizpitanja.com</a></div>
	<!-- NAVIGATION MENU -->
		<ul class="navigation-links">
		<!-- USING CHECKBOX HACK -->
		<input type="checkbox" id="checkbox_toggle" />
		<label for="checkbox_toggle" class="hamburger">&#9776;</label>
		<!-- NAVIGATION MENUS -->
			<div class="menu">
				<li><a href="<?= get_home_url(); ?>">Početna</a></li>
				<li class="services">
				<a href="<?=get_post_type_archive_link( 'questions' ); ?>">Kviz pitanja</a>
				<!-- DROPDOWN MENU -->
				<ul class="dropdown">
					<?php foreach ( $questions_terms_cat as $questions_term_cat ) : //dump($questions_term_cat)?>
						<li><a href="<?= get_term_link($questions_term_cat->slug, $questions_taxonomy_cat); ?>"><?= $questions_term_cat->name; ?></a></li>
					<?php endforeach; ?>
				</ul>
				</li>
				<li><a href="<?=get_page_link( 294 )?>">Slučajnih 40</a></li>
				<li><a href="<?=get_page_link( 340 )?>">Kontakt</a></li>
			</div>
		</ul>
</nav>
*/ ?>

<div class="wrapper">
    <nav>
      <input type="checkbox" id="show-search">
      <input type="checkbox" id="show-menu">
      <label for="show-menu" class="menu-icon"><i class="fas fa-bars"></i></label>
      <div class="content">
      <div class="logo"><a href="<?= get_home_url(); ?>" style="color: #fff;">pubkvizpitanja.com</a></div>
        <ul class="links">
        <li><a href="<?= get_home_url(); ?>">Početna</a></li>
			<li>
				<a href="<?=get_post_type_archive_link( 'questions' ); ?>" class="desktop-link">Kviz pitanja</a>
				<input type="checkbox" id="show-features">
				<label for="show-features">Kviz pitanja</label>
				<ul>
						<?php foreach ( $questions_terms_cat as $questions_term_cat ) : //dump($questions_term_cat)?>
							<li><a href="<?= get_term_link($questions_term_cat->slug, $questions_taxonomy_cat); ?>"><?= $questions_term_cat->name; ?></a></li>
						<?php endforeach; ?>
				</ul>
			</li>
        <li><a href="<?=get_page_link( 712 )?>">Pojmovnik</a></li>
		<li><a href="<?=get_page_link( 294 )?>">Slučajnih 40</a></li>
		<li><a href="<?=get_page_link( 340 )?>">Kontakt</a></li>
        </ul>
      </div>
    </nav>
  </div>

	</header><!-- #masthead -->
