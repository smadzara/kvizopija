<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package kvizopija
 */

get_header();
?>

	<main id="primary" class="site-main-search">

		<?php if ( have_posts() ) : ?>

			<div class="search-title">
                    <h2 class="search-title-body">
                        <?php
                        /* translators: %s: search query. */
                        printf( esc_html__( 'Rezultati pretraživanja za pojam: %s', 'kvizopija' ), '<span class="search-results-highlited">' . get_search_query() . '</span>' );
                        ?>
                    </h2>
            </div><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'search' );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar('questions');
get_footer();
