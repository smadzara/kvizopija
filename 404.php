<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package kvizopija
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="container">
			<header class="content-container-fof">
				<h1 class="page-title"><?php esc_html_e( 'Nešto se krivo skešalo ili stvarno ne postoji ono što ste tražili ili mislili da postoji.', 'kvizopija' ); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content">
				<p class="fof"><?php esc_html_e( 'Upiši novi pojam ili odleti na krilima prepunim bratske ljubavi za čovjeka koji je zalutao, tako što ćeš odabrati jednu od kategorija pitanja.', 'kvizopija' ); ?></p>

			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_sidebar('questions');
get_footer();
