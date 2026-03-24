<?php
/**
 * Template part for displaying homepage content.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
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

$latest_quiz_questions = get_posts(
	array(
		'post_type'              => 'questions',
		'post_status'            => 'publish',
		'posts_per_page'         => 30,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);
$initial_quiz_questions_visible = 5;

$questions_archive_url = get_post_type_archive_link( 'questions' );
if ( ! is_string( $questions_archive_url ) || '' === $questions_archive_url ) {
	$questions_archive_url = home_url( '/questions/' );
}

$novosti_category = get_category_by_slug( 'novosti' );
$news_page_url    = '';

if ( $novosti_category instanceof WP_Term ) {
	$news_page_url = get_category_link( $novosti_category->term_id );
}

if ( empty( $news_page_url ) || is_wp_error( $news_page_url ) ) {
	$news_page_url = home_url( '/category/novosti/' );
}

$question_of_the_day = function_exists( 'kvizopija_get_question_of_the_day' ) ? kvizopija_get_question_of_the_day() : array();
$latest_term_dates   = function_exists( 'kvizopija_get_question_category_latest_modified_dates' ) ? kvizopija_get_question_category_latest_modified_dates() : array();

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
						<?= '' !== $latest_question_date ? esc_html( $latest_question_date ) : esc_html__( 'Nema pitanja', 'pkp' ); ?>
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

		<section class="home-modern__panel home-modern__panel--news" aria-labelledby="home-news-title" style="margin-top:14px;">
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

						$term_id          = (int) $questions_term->term_id;
						$latest_term_date = isset( $latest_term_dates[ $term_id ] ) ? (string) $latest_term_dates[ $term_id ] : '';
						$term_count       = (int) $questions_term->count;
						$term_count_label = sprintf(
							/* translators: %s: number of questions in category. */
							_n( '%s pitanje', '%s pitanja', $term_count, 'pkp' ),
							number_format_i18n( $term_count )
						);
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

		<section
			id="home-latest-questions"
			class="home-modern__panel home-modern__panel--latest-questions"
			aria-labelledby="home-latest-questions-title"
			data-load-batch="<?= esc_attr( $initial_quiz_questions_visible ); ?>"
			style="margin-top:14px;"
		>
			<div class="home-modern__panel-head">
				<h2 id="home-latest-questions-title">Zadnjih 5 kviz pitanja</h2>
				<a class="home-modern__panel-link" href="<?= esc_url( $questions_archive_url ); ?>">Sva pitanja</a>
			</div>

			<?php if ( ! empty( $latest_quiz_questions ) ) : ?>
				<div class="questions-list--readable">
					<?php foreach ( $latest_quiz_questions as $question_index => $latest_quiz_question ) : ?>
						<?php
						if ( ! ( $latest_quiz_question instanceof WP_Post ) ) {
							continue;
						}

						$question_categories = get_the_terms( $latest_quiz_question->ID, $questions_taxonomy );
						if ( ! is_array( $question_categories ) ) {
							$question_categories = array();
						}

						$question_answer = apply_filters( 'the_content', get_post_field( 'post_content', $latest_quiz_question->ID ) );
						?>
						<article data-home-question-item <?= $question_index >= $initial_quiz_questions_visible ? 'hidden' : ''; ?>>
							<div class="home-modern__question-meta-row">
								<p class="home-modern-news-date"><?= esc_html( get_the_date( 'j. n. Y.', $latest_quiz_question ) ); ?></p>
								<?php if ( ! empty( $question_categories ) ) : ?>
									<p class="home-modern__question-categories-row">
										<?php foreach ( $question_categories as $question_category ) : ?>
											<?php
											$question_category_link = get_term_link( $question_category );
											if ( is_wp_error( $question_category_link ) ) {
												continue;
											}
											?>
											<a class="home-modern__question-category" href="<?= esc_url( $question_category_link ); ?>">
												<?= esc_html( $question_category->name ); ?>
											</a>
										<?php endforeach; ?>
									</p>
								<?php endif; ?>
							</div>
							<p><?= esc_html( get_the_title( $latest_quiz_question ) ); ?></p>
							<div class="answer-category">
								<?= wp_kses_post( $question_answer ); ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="more-questions more-questions--questions-archive">
					<button
						type="button"
						class="homepage-button terms-load-more"
						data-home-questions-load-more="1"
						<?= count( $latest_quiz_questions ) > $initial_quiz_questions_visible ? '' : 'style="display:none;"'; ?>
					>
						Učitaj više
					</button>
					<button
						type="button"
						class="homepage-button terms-load-more"
						data-home-answers-reveal="1"
					>
						Otkrij odgovore
					</button>
				</div>
			<?php else : ?>
				<p class="home-modern-empty">Trenutno nema objavljenih pitanja.</p>
			<?php endif; ?>
		</section>
	</div>
</section>

<script>
(function() {
	const section = document.getElementById('home-latest-questions');
	if (!section) {
		return;
	}

	const loadMoreButton = section.querySelector('[data-home-questions-load-more]');
	const revealAnswersButton = section.querySelector('[data-home-answers-reveal]');
	const questionItems = Array.from(section.querySelectorAll('[data-home-question-item]'));
	const batchSize = parseInt(section.dataset.loadBatch || '5', 10);
	const isCoarsePointer = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;

	function clearTapFocus(element) {
		if (!isCoarsePointer || !element || typeof element.blur !== 'function') {
			return;
		}

		window.setTimeout(function() {
			element.blur();
		}, 0);
	}

	function getHiddenItems() {
		return questionItems.filter(function(item) {
			return item.hidden;
		});
	}

	function updateLoadMoreVisibility() {
		if (!loadMoreButton) {
			return;
		}

		if (getHiddenItems().length > 0) {
			loadMoreButton.style.display = 'block';
			return;
		}

		loadMoreButton.style.display = 'none';
	}

	if (loadMoreButton) {
		loadMoreButton.addEventListener('click', function() {
			getHiddenItems()
				.slice(0, batchSize)
				.forEach(function(item) {
					item.hidden = false;
				});

			updateLoadMoreVisibility();
			clearTapFocus(loadMoreButton);
		});

		updateLoadMoreVisibility();
	}

	if (revealAnswersButton) {
		revealAnswersButton.addEventListener('click', function() {
			section.querySelectorAll('.answer-category').forEach(function(answer) {
				answer.classList.add('show');
			});

			revealAnswersButton.disabled = true;
			clearTapFocus(revealAnswersButton);
		});
	}
})();
</script>
