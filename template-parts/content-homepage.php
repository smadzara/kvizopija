<?php
/**
 * Template part for displaying homepage content.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
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

if ( is_wp_error( $questions_terms ) ) {
	$questions_terms = array();
}

$questions_count_obj = wp_count_posts( 'questions' );
$questions_count     = ( $questions_count_obj && isset( $questions_count_obj->publish ) ) ? (int) $questions_count_obj->publish : 0;

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
	)
);

$latest_question_date = '';
if ( ! empty( $latest_questions ) && $latest_questions[0] instanceof WP_Post ) {
	$latest_question_date = get_the_date( 'j. n. Y.', $latest_questions[0] );
}

$news_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

$novosti_category = get_category_by_slug( 'novosti' );
$news_page_url    = '';

if ( $novosti_category instanceof WP_Term ) {
	$news_page_url = get_category_link( $novosti_category->term_id );
}

if ( empty( $news_page_url ) || is_wp_error( $news_page_url ) ) {
	$news_page_url = home_url( '/category/novosti/' );
}

$question_of_the_day = function_exists( 'kvizopija_get_question_of_the_day' ) ? kvizopija_get_question_of_the_day() : array();

$memberpress_login_html = '';
if ( shortcode_exists( 'mepr-login-form' ) ) {
	$memberpress_login_html = do_shortcode( '[mepr-login-form use_redirect="true"]' );
} elseif ( shortcode_exists( 'mepr-login' ) ) {
	$memberpress_login_html = do_shortcode( '[mepr-login]' );
}

if ( '' === trim( wp_strip_all_tags( (string) $memberpress_login_html ) ) ) {
	ob_start();
	wp_login_form(
		array(
			'echo'           => true,
			'remember'       => true,
			'label_username' => __( 'Korisničko ime', 'kvizopija' ),
			'label_password' => __( 'Lozinka', 'kvizopija' ),
			'label_log_in'   => __( 'Prijava', 'kvizopija' ),
		)
	);
	$memberpress_login_html = ob_get_clean();
}
?>

<section class="container home-modern">
	<div class="content-container home-modern__container">
		<div class="home-modern__hero">
			<div class="home-modern__text">
				<div class="page-description">
					<div class="page-description-paragraph-text">
						<?php the_content(); ?>
					</div>
				</div>
			</div>

			<div class="home-modern__quick-buttons" aria-label="Brze informacije">
				<a class="home-modern__quick-button home-modern__quick-button--donate" href="https://buymeacoffee.com/pekape" target="_blank" rel="noopener noreferrer">
					<span class="home-modern__quick-button-label">Doniraj</span>
					<span class="home-modern__quick-button-value">Podrži rad ovih stranica</span>
				</a>

				<div class="home-modern__quick-button home-modern__quick-button--count" role="status" aria-live="polite">
					<span class="home-modern__quick-button-label">Broj pitanja</span>
					<span class="home-modern__quick-button-value"><?= esc_html( number_format_i18n( $questions_count ) ); ?></span>
				</div>

				<div class="home-modern__quick-button home-modern__quick-button--updated" role="status" aria-live="polite">
					<span class="home-modern__quick-button-label">Ažurirano</span>
					<span class="home-modern__quick-button-value">
						<?= '' !== $latest_question_date ? esc_html( $latest_question_date ) : esc_html__( 'Nema pitanja', 'kvizopija' ); ?>
					</span>
				</div>
			</div>
		</div>

		<?php if ( ! empty( $question_of_the_day ) ) : ?>
			<section class="home-modern__panel home-modern__panel--question-of-day" aria-labelledby="home-question-of-day-title">
				<div class="home-modern__question-of-day-head">
					<h2 id="home-question-of-day-title">Pub kviz pitanje dana</h2>
					<?php if ( ! empty( $question_of_the_day['category_name'] ) ) : ?>
						<?php if ( ! empty( $question_of_the_day['category_link'] ) ) : ?>
							<a class="home-modern__question-category" href="<?= esc_url( $question_of_the_day['category_link'] ); ?>">
								<?= esc_html( $question_of_the_day['category_name'] ); ?>
							</a>
						<?php else : ?>
							<span class="home-modern__question-category"><?= esc_html( $question_of_the_day['category_name'] ); ?></span>
						<?php endif; ?>
					<?php endif; ?>
				</div>

				<p><?= esc_html( $question_of_the_day['question_title'] ); ?></p>
			</section>
		<?php endif; ?>

		<div class="home-modern__grid">
			<section class="home-modern__panel home-modern__panel--news" aria-labelledby="home-news-title">
				<div class="home-modern__panel-head">
					<h2 id="home-news-title">Novosti</h2>
					<a class="home-modern__panel-link" href="<?= esc_url( $news_page_url ); ?>">Sve novosti</a>
				</div>

				<?php if ( $news_query->have_posts() ) : ?>
					<div class="home-modern-news-grid">
						<?php
						while ( $news_query->have_posts() ) :
							$news_query->the_post();
							?>
							<a class="home-modern-news-card-link" href="<?= esc_url( get_permalink() ); ?>">
								<article class="home-modern-news-card">
									<p class="home-modern-news-date"><?= esc_html( get_the_date( 'j. n. Y.' ) ); ?></p>
									<h3 class="home-modern-news-title"><?= esc_html( get_the_title() ); ?></h3>
									<p class="home-modern-news-excerpt"><?= esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 20 ) ); ?></p>
								</article>
							</a>
						<?php endwhile; ?>
					</div>
				<?php else : ?>
					<p class="home-modern-empty">Trenutno nema objavljenih novosti.</p>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</section>

			<section class="home-modern__panel home-modern__panel--login" aria-labelledby="home-login-title">
				<div class="home-modern__panel-head">
					<h2 id="home-login-title">Prijava</h2>
				</div>
				<p class="home-modern-login-note">Za pristup premium sadržaju prijavite se svojim korisničkim računom.</p>
				<div class="home-modern-login-form">
					<?php echo $memberpress_login_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</section>
		</div>

		<section class="home-modern__panel home-modern__panel--categories" aria-labelledby="home-categories-title">
			<div class="home-modern__panel-head">
				<h2 id="home-categories-title">Kategorije pitanja</h2>
			</div>

			<?php if ( ! empty( $questions_terms ) ) : ?>
				<div class="home-modern-categories">
					<?php foreach ( $questions_terms as $questions_term ) : ?>
						<?php
						$term_link = get_term_link( $questions_term );
						if ( is_wp_error( $term_link ) ) {
							continue;
						}

						$latest_term_posts = get_posts(
							array(
								'post_type'              => 'questions',
								'post_status'            => 'publish',
								'posts_per_page'         => 1,
								'orderby'                => 'modified',
								'order'                  => 'DESC',
								'no_found_rows'          => true,
								'update_post_meta_cache' => false,
								'update_post_term_cache' => false,
								'tax_query'              => array(
									array(
										'taxonomy' => $questions_taxonomy,
										'field'    => 'term_id',
										'terms'    => array( (int) $questions_term->term_id ),
									),
								),
							)
						);

						$latest_term_date = '';
						if ( ! empty( $latest_term_posts ) && $latest_term_posts[0] instanceof WP_Post ) {
							$latest_term_date = get_the_modified_date( 'j. n. Y.', $latest_term_posts[0] );
						}

						$term_count       = (int) $questions_term->count;
						$term_count_label = $term_count . ' ' . ( 1 === $term_count ? 'pitanje' : 'pitanja' );
						?>
						<a class="home-modern-category-card" href="<?= esc_url( $term_link ); ?>">
							<h3 class="home-modern-category-title"><?= esc_html( $questions_term->name ); ?></h3>
							<?php if ( '' !== $latest_term_date ) : ?>
								<p class="home-modern-category-meta">Osvježeno: <?= esc_html( $latest_term_date ); ?></p>
							<?php endif; ?>
							<span class="home-modern-category-count"><?= esc_html( $term_count_label ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="home-modern-empty">Trenutno nema dostupnih kategorija pitanja.</p>
			<?php endif; ?>
		</section>
	</div>
</section>
