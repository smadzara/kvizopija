<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package kvizopija
 */

get_header();

$current_page   = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$max_pages      = isset( $wp_query ) ? (int) $wp_query->max_num_pages : 1;
$total_results  = isset( $wp_query ) ? (int) $wp_query->found_posts : 0;
$show_load_more = $max_pages > $current_page && $total_results > 50;
?>

<section class="container questions-category-page search-results-page">
	<div class="content-container">

		<?php if ( have_posts() ) : ?>

			<h1><?= esc_html__( 'Rezultati pretraživanja', 'kvizopija' ); ?></h1>
			<p class="cat-taxonomy search-results-summary">
				<?= esc_html__( 'Pojam:', 'kvizopija' ); ?>
				<span class="search-results-highlited"><?= esc_html( get_search_query() ); ?></span>
				|
				<?= esc_html__( 'Rezultata:', 'kvizopija' ); ?>
				<?= esc_html( number_format_i18n( $total_results ) ); ?>
			</p>

			<main id="primary" class="site-main questions-list--readable">
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
			?>
			</main><!-- #main -->

			<div class="more-questions more-questions--questions-archive">
				<?php if ( $show_load_more ) : ?>
					<button
						id="loadMoreSearchResults"
						type="button"
						class="homepage-button terms-load-more search-results-action-button"
						data-questions-load-more="1"
						data-url-mode="search"
						data-page="<?= esc_attr( $current_page ); ?>"
						data-max-pages="<?= esc_attr( $max_pages ); ?>"
						data-search-query="<?= esc_attr( get_search_query() ); ?>"
						data-home-url="<?= esc_url( home_url( '/' ) ); ?>"
						data-container-selector="#primary.site-main"
						data-items-selector="#primary.site-main article.search-result-item"
						data-answer-selector=".answer-category"
						data-reveal-body-class="answers-revealed-search"
						data-default-text="U&#269;itaj vi&#353;e"
						data-loading-text="U&#269;itavam..."
						data-retry-text="Poku&#353;aj ponovno"
					>
						U&#269;itaj vi&#353;e
					</button>
				<?php endif; ?>

				<button
					id="revealSearchAnswers"
					type="button"
					class="homepage-button terms-load-more search-results-action-button"
					data-answers-reveal="1"
					data-reveal-target=".search-result-item .answer-category"
					data-reveal-body-class="answers-revealed-search"
				>
					Otkrij odgovore
				</button>
			</div>

			<?php if ( ! $show_load_more && $max_pages > $current_page ) : ?>
				<div class="navigation pagination">
					<?php the_posts_navigation(); ?>
				</div>
			<?php endif; ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>
	</div>

</section>

<?php
get_sidebar( 'questions' );
get_footer();
?>
