<?php
/**
 * Template for News category archive with load-more behavior.
 *
 * @package kvizopija
 */

get_header();

$queried_object = get_queried_object();
$current_page   = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$term_id = ( $queried_object instanceof WP_Term ) ? (int) $queried_object->term_id : 0;
$term_name = ( $queried_object instanceof WP_Term ) ? $queried_object->name : 'News';
$term_description = '';
if ( $term_id > 0 ) {
	$term_description = term_description( $term_id, 'category' );
}

$news_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 5,
		'paged'               => $current_page,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'cat'                 => $term_id,
	)
);

$max_pages = (int) $news_query->max_num_pages;
$base_url  = ( $term_id > 0 ) ? get_category_link( $term_id ) : home_url( '/' );
if ( is_wp_error( $base_url ) ) {
	$base_url = home_url( '/' );
}
?>

<main id="primary" class="site-main news-archive-modern-page">
	<section class="container">
		<div class="content-container news-archive-modern__container">
			<header class="news-archive-modern__header">
				<h1 class="news-archive-modern__title"><?= esc_html( $term_name ); ?></h1>
				<?php if ( '' !== trim( wp_strip_all_tags( (string) $term_description ) ) ) : ?>
					<div class="news-archive-modern__description"><?= wp_kses_post( $term_description ); ?></div>
				<?php endif; ?>
			</header>

			<?php if ( $news_query->have_posts() ) : ?>
				<div class="news-archive-modern__list" id="newsArchiveList">
					<?php
					while ( $news_query->have_posts() ) :
						$news_query->the_post();
						?>
						<article class="news-archive-modern__card">
							<p class="news-archive-modern__date"><?= esc_html( get_the_date( 'j. n. Y.' ) ); ?></p>
							<h2 class="news-archive-modern__card-title">
								<a href="<?= esc_url( get_permalink() ); ?>"><?= esc_html( get_the_title() ); ?></a>
							</h2>
							<p class="news-archive-modern__excerpt"><?= esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 26 ) ); ?></p>
							<a class="news-archive-modern__read-more" href="<?= esc_url( get_permalink() ); ?>">Pro&#269;itaj vi&#353;e</a>
						</article>
					<?php endwhile; ?>
				</div>

				<?php if ( $max_pages > $current_page ) : ?>
					<div class="news-archive-modern__actions">
						<button
							id="loadMoreNewsPosts"
							type="button"
							class="homepage-button terms-load-more news-archive-modern__button"
							data-page="<?= esc_attr( $current_page ); ?>"
							data-max-pages="<?= esc_attr( $max_pages ); ?>"
							data-base-url="<?= esc_url( $base_url ); ?>"
						>
							Load more
						</button>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<p class="home-modern-empty">Trenutno nema objavljenih novosti.</p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>
</main>

<script>
(function() {
	const loadMoreButton = document.getElementById('loadMoreNewsPosts');
	const list = document.getElementById('newsArchiveList');

	if (!loadMoreButton || !list) {
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
		loadMoreButton.textContent = 'Loading...';

		try {
			const nextPage = currentPage + 1;
			const response = await fetch(buildPageUrl(nextPage), { credentials: 'same-origin' });

			if (!response.ok) {
				throw new Error('News archive request failed');
			}

			const html = await response.text();
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const newCards = doc.querySelectorAll('.news-archive-modern__list .news-archive-modern__card');

			if (!newCards.length) {
				loadMoreButton.style.display = 'none';
				return;
			}

			const fragment = document.createDocumentFragment();
			newCards.forEach(function(card) {
				fragment.appendChild(card);
			});
			list.appendChild(fragment);

			currentPage = nextPage;
			loadMoreButton.dataset.page = String(currentPage);

			if (currentPage >= maxPages) {
				loadMoreButton.style.display = 'none';
				return;
			}

			loadMoreButton.textContent = 'Load more';
			loadMoreButton.disabled = false;
		} catch (error) {
			console.error(error);
			loadMoreButton.textContent = 'Try again';
			loadMoreButton.disabled = false;
		} finally {
			isLoading = false;
		}
	});
})();
</script>

<?php
get_sidebar( 'questions' );
get_footer();
?>
