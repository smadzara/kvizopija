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
 * @package kvizopija
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
					data-page="1"
					data-max-pages="<?= esc_attr( $max_pages ); ?>"
					data-base-url="<?= esc_url( $category_term_link ); ?>"
				>
					U&#269;itaj vi&#353;e
				</button>
			<?php endif; ?>

			<button id="btn" type="button" class="homepage-button terms-load-more">Otkrij odgovore</button>
		</div>
	</div>
</section>

<?php
//get_sidebar();
get_sidebar('questions');
get_footer();
?>
<?php // Otkrij odgovore i load more - START ?>
<script>
(function() {
	const revealButton = document.getElementById('btn');

	if (revealButton) {
		revealButton.addEventListener('click', function() {
			document.body.classList.add('answers-revealed-category');
			document.querySelectorAll('.answer-category').forEach(function(answer) {
				answer.classList.add('show');
			});
		});
	}

	const loadMoreButton = document.getElementById('loadMoreCategoryQuestions');
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
				throw new Error('Category questions request failed');
			}

			const html = await response.text();
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const newArticles = doc.querySelectorAll('#primary.site-main article');

			if (!newArticles.length) {
				loadMoreButton.style.display = 'none';
				return;
			}

			const answersAlreadyRevealed = document.body.classList.contains('answers-revealed-category');
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
