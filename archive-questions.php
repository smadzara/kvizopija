<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

get_header();

$questions_taxonomy = 'questions_categories';
$questions_per_page = 25;
$current_page       = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$archive_base_url   = trailingslashit( get_post_type_archive_link( 'questions' ) );

$args = array(
	'post_type'           => 'questions',
	'orderby'             => 'date',
	'order'               => 'DESC',
	'posts_per_page'      => $questions_per_page,
	'paged'               => $current_page,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
);

$query     = new WP_Query( $args );
$max_pages = (int) $query->max_num_pages;
?>

<main id="primary" class="container questions-archive-page">
	<div class="content-container">
		<div class="container-questions questions-list--readable">
			<h2>Sva pub kviz pitanja</h2>

			<?php if ( $query->have_posts() ) : ?>
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();

					$terms               = get_the_terms( get_the_ID(), $questions_taxonomy );
					$question_author     = get_field( 'question_author', get_the_ID() );
					$question_author_url = get_field( 'question_author_url', get_the_ID() );
					$term_list           = wp_get_post_terms( get_the_ID(), 'questions_terms', array( 'fields' => 'all' ) );
					?>
					<div class="questions-homepage">
						<?php
						$term_link = '';
						if ( ! empty( $terms ) ) {
							$term_link = get_term_link( $terms[0]->slug, $questions_taxonomy );
						}
						if ( ! empty( $terms ) && ! is_wp_error( $term_link ) ) :
							?>
							<p class="question-category">Kategorija:
								<a href="<?= esc_url( $term_link ); ?>">
									<?= esc_html( $terms[0]->name ); ?>
								</a>
							</p>
						<?php endif; ?>

						<p class="question-date">Objavljeno:
							<span class="question-accent"><?= esc_html( get_the_date( 'j. n. Y.' ) ); ?></span>
						</p>

						<?php if ( empty( $question_author ) || empty( $question_author_url ) ) : ?>
							<p class="question-author">Autor: <a href="https://kvizopija.com" target="_blank">kvizopija.com</a></p>
						<?php else : ?>
							<p class="question-author">Autor: <a href="<?= esc_url( $question_author_url ); ?>" target="_blank"><?= esc_html( $question_author ); ?></a></p>
						<?php endif; ?>

						<?php if ( $term_list ) : ?>
							<p class="question-category">Pojmovi:
								<?php foreach ( $term_list as $single_term_key ) : ?>
									<?php
									$single_term_link = get_term_link( $single_term_key->slug, 'questions_terms' );
									if ( ! is_wp_error( $single_term_link ) ) {
										echo '<a href="' . esc_url( $single_term_link ) . '">|' . esc_html( $single_term_key->name ) . '| </a>';
									}
									?>
								<?php endforeach; ?>
							</p>
						<?php endif; ?>

						<p class="questions"><?php the_title(); ?></p>
						<div class="answer-category"><?php the_content(); ?></div>
					</div>
				<?php endwhile; ?>

				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>

		<div class="more-questions more-questions--questions-archive">
			<?php if ( $max_pages > $current_page ) : ?>
				<button
					id="loadMore"
					type="button"
					class="homepage-button terms-load-more"
					data-page="<?= esc_attr( $current_page ); ?>"
					data-max-pages="<?= esc_attr( $max_pages ); ?>"
					data-base-url="<?= esc_url( $archive_base_url ); ?>"
				>
					U&#269;itaj vi&#353;e
				</button>
			<?php endif; ?>

			<button id="btn" type="button" class="homepage-button terms-load-more">Otkrij odgovore</button>
		</div>
	</div>
</main><!-- #main -->

<?php
get_sidebar( 'questions' );
get_footer();
?>

<?php // Otkrij odgovore - START ?>
<script>
(function() {
	const btn = document.getElementById('btn');

	if (!btn) {
		return;
	}

	btn.addEventListener('click', function() {
		document.body.classList.add('answers-revealed-questions');

		const answers = document.querySelectorAll('.answer-category');
		answers.forEach(function(answer) {
			answer.classList.add('show');
		});
	});
})();
</script>
<?php // Otkrij odgovore - END ?>

<?php // LOAD MORE - START ?>
<script>
(function() {
	const loadMoreButton = document.getElementById('loadMore');
	const container = document.querySelector('.container-questions');

	if (!loadMoreButton || !container) {
		return;
	}

	let currentPage = parseInt(loadMoreButton.dataset.page || '1', 10);
	const maxPages = parseInt(loadMoreButton.dataset.maxPages || '1', 10);
	const baseUrl = (loadMoreButton.dataset.baseUrl || window.location.pathname).replace(/\/+$/, '');
	let isLoading = false;

	function buildPageUrl(pageNumber) {
		return baseUrl + '/page/' + pageNumber + '/';
	}

	loadMoreButton.addEventListener('click', async function() {
		if (isLoading || currentPage >= maxPages) {
			return;
		}

		isLoading = true;
		loadMoreButton.disabled = true;
		loadMoreButton.textContent = 'U\u010ditavam...';

		try {
			const nextPage = currentPage + 1;
			const response = await fetch(buildPageUrl(nextPage), { credentials: 'same-origin' });

			if (!response.ok) {
				throw new Error('Questions page request failed');
			}

			const html = await response.text();
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const newItems = doc.querySelectorAll('.container-questions .questions-homepage');

			if (!newItems.length) {
				loadMoreButton.style.display = 'none';
				return;
			}

			const answersAlreadyRevealed = document.body.classList.contains('answers-revealed-questions');
			const fragment = document.createDocumentFragment();

			newItems.forEach(function(item) {
				if (answersAlreadyRevealed) {
					item.querySelectorAll('.answer-category').forEach(function(answer) {
						answer.classList.add('show');
					});
				}

				fragment.appendChild(item);
			});

			container.appendChild(fragment);

			currentPage = nextPage;
			loadMoreButton.dataset.page = String(currentPage);

			if (currentPage >= maxPages) {
				loadMoreButton.style.display = 'none';
				return;
			}

			loadMoreButton.textContent = 'U\u010ditaj vi\u0161e';
			loadMoreButton.disabled = false;
		} catch (error) {
			console.error(error);
			loadMoreButton.textContent = 'Poku\u0161aj ponovno';
			loadMoreButton.disabled = false;
		} finally {
			isLoading = false;
		}
	});
})();
</script>
<?php // LOAD MORE - END ?>
