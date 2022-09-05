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

<nav class="navbar">
    <div class="logo">
      LOGO/HEADER
    </div>
    <div class="sitenavigation">
      <span class="menu-icon">
  <a href="#" class="menu example5"><span></span></a>
      <div id="hamburger">
        <span></span>
        <span></span>
        <span></span>
      </div>
      </span>
      <ul>
        <li><a href="#">Home</a></li>
        <li class="nav-dropdown"><a href="#">Categories</a>
          <ul>
            <li><a href="#">Hot Sellers</a></li>
            <li><a href="#">On Sale</a></li>
            <li class="nav-dropdown"><a href="#">Men's</a>
              <ul>
                <li><a href="#">T-Shirts</a></li>
                <li><a href="#">Pants</a></li>
              </ul>
            </li>
            <li><a href="#">Clearance</a></li>
          </ul>
        </li>
        <li class="nav-dropdown"><a href="#">Others</a>
          <ul>
            <li><a href="#">Hot Sellers</a></li>
            <li><a href="#">On Sale</a></li>
            <li class="nav-dropdown"><a href="#">Women's</a>
              <ul>
                <li><a href="#">Dresses</a></li>
                <li class="nav-dropdown"><a href="#">Shoes</a>
                  <ul>
                    <li>
                      <li><a href="#">High Heels</a></li>
                      <li><a href="#">Tennis Shoes</a></li>
                      <li><a href="#">Flip Flops</a></li>
                  </ul>
                  </li>
              </ul>
              </li>
              <li><a href="#">Forth Sub-nav item</a></li>
          </ul>
          </li>
          <li class="nav-dropdown"><a href="#">Third Item</a>
            <ul>
              <li><a href="#">Nav item</a></li>
              <li class="nav-dropdown"><a href="#">Nav item</a>
                <ul>
                  <li><a href="#">Nav item</a></li>

                </ul>
              </li>
              <li class="nav-dropdown"><a href="#">Third Sub-nav item</a>
                <ul>
                  <li><a href="#">Third level nav item</a></li>
                </ul>
              </li>

              <li><a href="#">Forth Sub-nav item</a></li>
            </ul>
          </li>
          <li><a href="#">Forth Item</a></li>
      </ul>
    </div>
  </nav>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

  

	</header><!-- #masthead -->
