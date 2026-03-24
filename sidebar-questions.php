<?php
/**
 * Questions sidebar.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package pkp
 */

$questions_taxonomy = 'questions_categories';
$questions_terms    = get_terms(
	array(
		'taxonomy'   => $questions_taxonomy,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

if ( is_wp_error( $questions_terms ) || ! is_array( $questions_terms ) ) {
	$questions_terms = array();
}
?>

<aside class="questions-sidebar" aria-label="Questions sidebar">
	<div class="questions-sidebar__search-panel">
		<?= get_search_form(); ?>
	</div>

	<div class="questions-sidebar__panel">
		<div class="questions-sidebar__panel-head">
			<h2 class="questions-sidebar__title">Kategorije pitanja</h2>
			<p class="questions-sidebar__subtitle">Brzi pregled svih kategorija</p>
		</div>

		<div class="questions-sidebar__grid">
			<?php foreach ( $questions_terms as $questions_term ) : ?>
				<?php
				$term_link = get_term_link( $questions_term->slug, $questions_taxonomy );
				if ( is_wp_error( $term_link ) ) {
					continue;
				}

				$latest_questions = get_posts(
					array(
						'post_type'              => 'questions',
						'post_status'            => 'publish',
						'posts_per_page'         => 1,
						'orderby'                => 'date',
						'order'                  => 'DESC',
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'tax_query'              => array(
							array(
								'taxonomy' => 'questions_categories',
								'field'    => 'slug',
								'terms'    => array( $questions_term->slug ),
							),
						),
					)
				);

				$latest_date = '';
				if ( ! empty( $latest_questions ) && $latest_questions[0] instanceof WP_Post ) {
					$latest_date = get_the_date( 'j. n. Y.', $latest_questions[0] );
				}

				$term_count       = (int) $questions_term->count;
				$term_count_label = $term_count . ' ' . ( 1 === $term_count ? 'pitanje' : 'pitanja' );
				?>

				<a class="questions-sidebar__card" href="<?= esc_url( $term_link ); ?>">
					<p class="questions-sidebar__card-title"><?= esc_html( $questions_term->name ); ?></p>
					<?php if ( '' !== $latest_date ) : ?>
						<p class="questions-sidebar__meta">Osvježeno: <?= esc_html( $latest_date ); ?></p>
					<?php endif; ?>
					<span class="questions-sidebar__badge"><?= esc_html( $term_count_label ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</aside>
