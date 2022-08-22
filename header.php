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

		<?php wp_head(); ?>
	</head>

<?php 

$questions_taxonomy_cat = 'questions_categories';
$questions_terms_cat = get_terms($questions_taxonomy_cat); // Get all terms of a questions taxonomy

?>
        

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
				<li><a href="/">Pricing</a></li>
				<li><a href="/">Contact</a></li>
			</div>
		</ul>
</nav>

<script>

</script>

	</header><!-- #masthead -->
