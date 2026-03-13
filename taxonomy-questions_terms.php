<?php
/**
 * The template for term archives in questions_terms taxonomy.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
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
					data-page="1"
					data-max-pages="<?= esc_attr( $max_pages ); ?>"
					data-base-url="<?= esc_url( is_wp_error( $term_link ) ? home_url( '/' ) : $term_link ); ?>"
				>
					U&#269;itaj vi&#353;e
				</button>
			<?php endif; ?>

			<button id="revealTermAnswers" type="button" class="homepage-button terms-load-more">Otkrij odgovore</button>
		</div>
	</div>
</section>

<?php
get_sidebar( 'questions' );
get_footer();
?>

<?php // Otkrij odgovore i load more - START ?>
<script>
(function() {
	const revealButton = document.getElementById('revealTermAnswers');

	if (revealButton) {
		revealButton.addEventListener('click', function() {
			document.body.classList.add('answers-revealed-term');
			document.querySelectorAll('.answer-category').forEach(function(answer) {
				answer.classList.add('show');
			});
		});
	}

	const loadMoreButton = document.getElementById('loadMoreTermQuestions');
	const postContainer = document.querySelector('#primary.site-main');

	if (!loadMoreButton || !postContainer) {
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
				throw new Error('Term questions request failed');
			}

			const html = await response.text();
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const newArticles = doc.querySelectorAll('#primary.site-main article');

			if (!newArticles.length) {
				loadMoreButton.style.display = 'none';
				return;
			}

			const answersAlreadyRevealed = document.body.classList.contains('answers-revealed-term');
			const fragment = document.createDocumentFragment();

			newArticles.forEach(function(article) {
				if (answersAlreadyRevealed) {
					article.querySelectorAll('.answer-category').forEach(function(answer) {
						answer.classList.add('show');
					});
				}
				fragment.appendChild(article);
			});
			postContainer.appendChild(fragment);

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
<?php // Otkrij odgovore i load more - END ?>
