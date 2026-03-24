<?php
/**
 * The template for term archives in questions_terms taxonomy.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pkp
 */

get_header();

$questions_custom_taxonomy_term = get_queried_object();
$max_pages                      = isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->max_num_pages : 1;
$term_name                      = $questions_custom_taxonomy_term instanceof WP_Term ? $questions_custom_taxonomy_term->name : '';
$term_description               = $questions_custom_taxonomy_term instanceof WP_Term ? $questions_custom_taxonomy_term->description : '';
$term_count                     = $questions_custom_taxonomy_term instanceof WP_Term ? (int) $questions_custom_taxonomy_term->count : 0;
$term_count_label               = 1 === $term_count ? 'pitanje' : 'pitanja';
$term_link                      = $questions_custom_taxonomy_term instanceof WP_Term ? get_term_link( $questions_custom_taxonomy_term ) : home_url( '/' );
?>

<section class="container terms-page questions-term-page">
	<div class="content-container">
		<div class="terms-hero">
			<div class="page-title">
				<h1><?= esc_html( $term_name ); ?></h1>
			</div>

			<div class="page-description">
				<?php if ( '' !== trim( (string) $term_description ) ) : ?>
					<p class="page-description-paragraph-text"><?= wp_kses_post( $term_description ); ?></p>
				<?php else : ?>
					<p class="page-description-paragraph-text">
						Pub kviz pitanja za pojam <span class="accent"><?= esc_html( $term_name ); ?></span>.
					</p>
				<?php endif; ?>
			</div>

			<div class="terms-hero-meta">
				<span class="terms-stat">
					<strong><?= esc_html( number_format_i18n( $term_count ) ); ?></strong>
					<?= esc_html( $term_count_label ); ?>
				</span>
				<span class="terms-stat">
					<strong>Novo</strong>
					&rarr; staro
				</span>
			</div>
		</div>

		<div class="terms-results-header">
			<h2 class="categories-title">Pitanja za pojam</h2>
			<p class="terms-results-note">Filtrirani pojam &quot;<?= esc_html( $term_name ); ?>&quot;</p>
		</div>

		<main id="primary" class="site-main questions-list--readable questions-list--term-page">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
				endwhile;
			else :
				get_template_part( 'template-parts/content', 'none' );
			endif;
			?>
		</main><!-- #main -->

		<div class="more-questions more-questions--questions-archive questions-term-actions">
			<?php if ( $max_pages > 1 ) : ?>
				<button
					id="loadMoreTermQuestions"
					type="button"
					class="homepage-button terms-load-more"
					data-questions-load-more="1"
					data-url-mode="path"
					data-page="1"
					data-max-pages="<?= esc_attr( $max_pages ); ?>"
					data-base-url="<?= esc_url( is_wp_error( $term_link ) ? home_url( '/' ) : $term_link ); ?>"
					data-container-selector="#primary.site-main"
					data-items-selector="#primary.site-main article"
					data-answer-selector=".answer-category"
					data-reveal-body-class="answers-revealed-term"
					data-default-text="U&#269;itaj vi&#353;e"
					data-loading-text="U&#269;itavam..."
					data-retry-text="Poku&#353;aj ponovno"
				>
					U&#269;itaj vi&#353;e
				</button>
			<?php endif; ?>

			<button
				id="revealTermAnswers"
				type="button"
				class="homepage-button terms-load-more"
				data-answers-reveal="1"
				data-reveal-target=".answer-category"
				data-reveal-body-class="answers-revealed-term"
			>
				Otkrij odgovore
			</button>
		</div>
	</div>
</section>

<?php
get_sidebar( 'questions' );
get_footer();
?>
