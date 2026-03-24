<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pkp
 */

get_header();


$questions_custom_taxonomy_term = get_queried_object();
$max_pages = isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->max_num_pages : 1;
$category_term_link = get_term_link( $questions_custom_taxonomy_term );
if ( is_wp_error( $category_term_link ) ) {
	$category_term_link = home_url( '/' );
}
//dump($questions_custom_taxonomy_term);

?>

<section class="container questions-category-page">

	<div class="content-container">

		<h1><?= esc_html( $questions_custom_taxonomy_term->name ); ?></h1>

			<p class="cat-taxonomy"><?= wp_kses_post( $questions_custom_taxonomy_term->description ); ?></p>

				<main id="primary" class="site-main questions-list--readable">

					<?php
					if ( have_posts() ) :

						if ( is_home() && ! is_front_page() ) :
							?>
							<header>
								<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
							</header>
							<?php
						endif;

						/* Start the Loop */
						while ( have_posts() ) :
							the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/
							get_template_part( 'template-parts/content', get_post_type() );

						endwhile;

					else :

						get_template_part( 'template-parts/content', 'none' );

					endif;
                    
					?>

				</main><!-- #main -->

		<div class="more-questions more-questions--questions-archive">
			<?php if ( $max_pages > 1 ) : ?>
				<button
					id="loadMoreCategoryQuestions"
					type="button"
					class="homepage-button terms-load-more"
					data-questions-load-more="1"
					data-url-mode="path"
					data-page="1"
					data-max-pages="<?= esc_attr( $max_pages ); ?>"
					data-base-url="<?= esc_url( $category_term_link ); ?>"
					data-container-selector="#primary.site-main"
					data-items-selector="#primary.site-main article"
					data-answer-selector=".answer-category"
					data-reveal-body-class="answers-revealed-category"
					data-default-text="U&#269;itaj vi&#353;e"
					data-loading-text="U&#269;itavam..."
					data-retry-text="Poku&#353;aj ponovno"
				>
					U&#269;itaj vi&#353;e
				</button>
			<?php endif; ?>

			<button
				id="btn"
				type="button"
				class="homepage-button terms-load-more"
				data-answers-reveal="1"
				data-reveal-target=".answer-category"
				data-reveal-body-class="answers-revealed-category"
			>
				Otkrij odgovore
			</button>
		</div>
	</div>
</section>

<?php
//get_sidebar();
get_sidebar('questions');
get_footer();
?>
