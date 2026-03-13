<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kvizopija
 */

get_header();
?>

	<main id="primary" class="site-main single-post-page">
		<section class="container">
			<div class="content-container single-post-page__container">

		<?php
		while ( have_posts() ) :
			the_post();

			if ( 'post' === get_post_type() ) {
				get_template_part( 'template-parts/content', 'single-post' );
			} else {
				get_template_part( 'template-parts/content', get_post_type() );
			}

/* 			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'kvizopija' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'kvizopija' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif; */

		endwhile; // End of the loop.
		?>

			</div>
		</section>
	</main><!-- #main -->

<?php
get_sidebar('questions');
get_footer();
