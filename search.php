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
						data-page="<?= esc_attr( $current_page ); ?>"
						data-max-pages="<?= esc_attr( $max_pages ); ?>"
						data-search-query="<?= esc_attr( get_search_query() ); ?>"
						data-home-url="<?= esc_url( home_url( '/' ) ); ?>"
					>
						Učitaj više
					</button>
				<?php endif; ?>

				<button id="revealSearchAnswers" type="button" class="homepage-button terms-load-more search-results-action-button">Otkrij odgovore</button>
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

<script>
(function() {
	const revealButton = document.getElementById('revealSearchAnswers');
	const allAnswersSelector = '.search-result-item .answer-category';

	if (revealButton) {
		revealButton.addEventListener('click', function() {
			document.body.classList.add('answers-revealed-search');
			document.querySelectorAll(allAnswersSelector).forEach(function(answer) {
				answer.classList.add('show');
			});
		});
	}

	const loadMoreButton = document.getElementById('loadMoreSearchResults');
	const container = document.querySelector('#primary.site-main');

	if (!loadMoreButton || !container) {
		return;
	}

	let currentPage = parseInt(loadMoreButton.dataset.page || '1', 10);
	const maxPages = parseInt(loadMoreButton.dataset.maxPages || '1', 10);
	const searchQuery = loadMoreButton.dataset.searchQuery || '';
	const homeUrl = loadMoreButton.dataset.homeUrl || window.location.origin;
	let isLoading = false;

	function buildPageUrl(pageNumber) {
		const url = new URL(homeUrl, window.location.origin);
		url.searchParams.set('s', searchQuery);
		url.searchParams.set('post_type', 'questions');
		url.searchParams.set('paged', String(pageNumber));
		return url.toString();
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
				throw new Error('Search results request failed');
			}

			const html = await response.text();
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const newItems = doc.querySelectorAll('#primary.site-main article.search-result-item');

			if (!newItems.length) {
				loadMoreButton.style.display = 'none';
				return;
			}

			const answersAlreadyRevealed = document.body.classList.contains('answers-revealed-search');
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
