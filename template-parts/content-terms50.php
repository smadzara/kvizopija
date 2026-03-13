<?php

/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_terms';
$terms_per_page     = 75;

$all_terms = get_terms(
	array(
		'taxonomy'   => $questions_taxonomy,
		'hide_empty' => false,
	)
);

if ( is_wp_error( $all_terms ) || ! is_array( $all_terms ) ) {
	$all_terms = array();
}

usort(
	$all_terms,
	function( $a, $b ) {
		if ( (int) $a->count === (int) $b->count ) {
			return strcasecmp( $a->name, $b->name );
		}
		return (int) $b->count - (int) $a->count;
	}
);

$terms_page_1 = array_slice( $all_terms, 0, $terms_per_page );
?>

<!-- Kvizopija Template -->

<section class="container">

	<!-- <img src="img/Logo-70px.png" alt="Pub kviz pitanja by kvizopija.com - Logo"> -->

	<div class="content-container">

		<div class="page-title">
			<h1>
				<?php the_title(); ?>
			</h1>
		</div>

		<div class="page-description">
			<p class="page-description-paragraph-text">
				<?php the_content(); ?>
			</p>
		</div>

		<div class="terms-container">
			<h2 class="categories-title">Pojmovi po broju pitanja</h2>

			<section class="page-contain" id="termsCountContainer">
				<?php foreach ( $terms_page_1 as $questions_term ) : ?>
					<?php
					$term_link = get_term_link( $questions_term->slug, $questions_taxonomy );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}
					?>
					<a href="<?= esc_url( $term_link ); ?>" class="data-card">
						<h2><?= esc_html( $questions_term->name ); ?></h2>
						<h5>Broj pitanja: <?= (int) $questions_term->count; ?></h5>
						<span class="link-text">
							Sva pitanja
							<svg width="25" height="16" viewBox="0 0 25 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M17.8631 0.929124L24.2271 7.29308C24.6176 7.68361 24.6176 8.31677 24.2271 8.7073L17.8631 15.0713C17.4726 15.4618 16.8394 15.4618 16.4489 15.0713C16.0584 14.6807 16.0584 14.0476 16.4489 13.657L21.1058 9.00019H0.47998V7.00019H21.1058L16.4489 2.34334C16.0584 1.95281 16.0584 1.31965 16.4489 0.929124C16.8394 0.538599 17.4726 0.538599 17.8631 0.929124Z" fill="#753BBD"/>
							</svg>
						</span>
					</a>
				<?php endforeach; ?>
			</section>
		</div>

		<?php if ( count( $terms_page_1 ) === $terms_per_page ) : ?>
			<div class="navigation pagination">
				<button
					id="loadMoreTermsCount"
					type="button"
					class="homepage-button"
					data-endpoint="<?= esc_url( rest_url( 'custom/v1/questions-terms' ) ); ?>"
					data-sort="count_desc"
					data-per-page="<?= (int) $terms_per_page; ?>"
				>
					Učitaj vise
				</button>
			</div>
		<?php endif; ?>

	</div>
</section>

<script>
(function() {
	const button = document.getElementById('loadMoreTermsCount');
	const container = document.getElementById('termsCountContainer');

	if (!button || !container) {
		return;
	}

	let page = 2;
	let isLoading = false;
	const endpoint = button.dataset.endpoint;
	const sort = button.dataset.sort || 'count_desc';
	const perPage = button.dataset.perPage || '75';
	const arrowSvg = '<svg width="25" height="16" viewBox="0 0 25 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.8631 0.929124L24.2271 7.29308C24.6176 7.68361 24.6176 8.31677 24.2271 8.7073L17.8631 15.0713C17.4726 15.4618 16.8394 15.4618 16.4489 15.0713C16.0584 14.6807 16.0584 14.0476 16.4489 13.657L21.1058 9.00019H0.47998V7.00019H21.1058L16.4489 2.34334C16.0584 1.95281 16.0584 1.31965 16.4489 0.929124C16.8394 0.538599 17.4726 0.538599 17.8631 0.929124Z" fill="#753BBD"/></svg>';

	button.addEventListener('click', async function() {
		if (isLoading) {
			return;
		}

		isLoading = true;
		button.disabled = true;
		button.textContent = 'Učitavam...';

		try {
			const requestUrl = endpoint + '?page=' + page + '&sort=' + encodeURIComponent(sort) + '&per_page=' + encodeURIComponent(perPage);
			const response = await fetch(requestUrl, { credentials: 'same-origin' });

			if (!response.ok) {
				throw new Error('Terms request failed');
			}

			const terms = await response.json();
			if (!Array.isArray(terms) || terms.length === 0) {
				button.style.display = 'none';
				return;
			}

			const fragment = document.createDocumentFragment();

			terms.forEach(function(term) {
				const card = document.createElement('a');
				card.href = term.link || '#';
				card.className = 'data-card';

				const title = document.createElement('h2');
				title.textContent = term.name;

				const count = document.createElement('h5');
				count.textContent = 'Broj pitanja: ' + term.count;

				const linkText = document.createElement('span');
				linkText.className = 'link-text';
				linkText.innerHTML = 'Sva pitanja ' + arrowSvg;

				card.appendChild(title);
				card.appendChild(count);
				card.appendChild(linkText);
				fragment.appendChild(card);
			});

			container.appendChild(fragment);
			page += 1;
		} catch (error) {
			console.error(error);
			button.textContent = 'Pokušaj ponovno';
			button.disabled = false;
			isLoading = false;
			return;
		}

		button.textContent = 'Učitaj vise';
		button.disabled = false;
		isLoading = false;
	});
})();
</script>

<!-- END Kvizopija Template -->
