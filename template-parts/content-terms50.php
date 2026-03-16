<?php

/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_terms';
$terms_per_page     = 50;

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
			return strcasecmp( remove_accents( (string) $a->name ), remove_accents( (string) $b->name ) );
		}

		return (int) $b->count - (int) $a->count;
	}
);

$terms_page_1 = array_slice( $all_terms, 0, $terms_per_page );
$total_terms  = count( $all_terms );
$has_more     = $total_terms > $terms_per_page;
?>

<section class="container terms-page">
	<div
		class="content-container"
		id="termsCountExplorer"
		data-endpoint="<?= esc_attr( wp_make_link_relative( rest_url( 'custom/v1/questions-terms' ) ) ); ?>"
		data-per-page="<?= esc_attr( $terms_per_page ); ?>"
		data-next-page="<?= esc_attr( $has_more ? 2 : 1 ); ?>"
		data-sort="count_desc"
	>
		<div class="terms-hero">
			<div class="page-title">
				<h1><?php the_title(); ?></h1>
			</div>

			<div class="page-description">
				<?php the_content(); ?>
			</div>

			<div class="terms-hero-meta">
				<span class="terms-stat">
					<strong><?= esc_html( number_format_i18n( $total_terms ) ); ?></strong>
					pojmova
				</span>
				<span class="terms-stat">
					<strong id="termsCountVisible"><?= esc_html( number_format_i18n( count( $terms_page_1 ) ) ); ?></strong>
					prikazano
				</span>
			</div>
		</div>

		<div class="terms-results-header">
			<h2 class="categories-title">Pojmovi po broju pitanja</h2>
			<p id="termsCountState" class="terms-results-note">Sortirano silazno po broju pitanja</p>
		</div>

		<div class="terms-container terms-grid" id="termsCountContainer">
			<?php if ( ! empty( $terms_page_1 ) ) : ?>
				<?php foreach ( $terms_page_1 as $questions_term ) : ?>
					<?php
					$term_link = get_term_link( $questions_term->slug, $questions_taxonomy );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}

					$term_count       = (int) $questions_term->count;
					$term_count_label = $term_count . ' ' . ( 1 === $term_count ? 'pitanje' : 'pitanja' );
					?>
					<a class="term-card-link" href="<?= esc_url( $term_link ); ?>">
						<article class="terms-box term-card">
							<p class="term-title"><?= esc_html( $questions_term->name ); ?></p>
							<span class="term-count-badge"><?= esc_html( $term_count_label ); ?></span>
						</article>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<p id="termsCountEmptyState" class="terms-empty" <?= ! empty( $terms_page_1 ) ? 'hidden' : ''; ?>>
			Nema dostupnih pojmova.
		</p>

		<div class="navigation pagination pagination--terms">
			<button
				id="loadMoreTermsCount"
				type="button"
				class="homepage-button terms-load-more"
				<?= $has_more ? '' : 'style="display:none;"'; ?>
			>
				U&#269;itaj vi&#353;e
			</button>
		</div>
	</div>
</section>

<script>
(function() {
	const explorer = document.getElementById('termsCountExplorer');
	if (!explorer) {
		return;
	}

	const container = document.getElementById('termsCountContainer');
	const button = document.getElementById('loadMoreTermsCount');
	const emptyState = document.getElementById('termsCountEmptyState');
	const visibleCount = document.getElementById('termsCountVisible');
	const stateNote = document.getElementById('termsCountState');

	const endpointFromData = explorer.dataset.endpoint || '/wp-json/custom/v1/questions-terms/';
	const perPage = parseInt(explorer.dataset.perPage || '50', 10);
	const sort = explorer.dataset.sort || 'count_desc';
	let nextPage = parseInt(explorer.dataset.nextPage || '1', 10);
	let activeEndpoint = '';

	if (!button || !container) {
		return;
	}

	let isLoading = false;

	function uniqueValues(items) {
		const filtered = items.filter(function(value) {
			return typeof value === 'string' && value.trim() !== '';
		});

		return filtered.filter(function(value, index) {
			return filtered.indexOf(value) === index;
		});
	}

	function buildEndpointCandidates() {
		const pathnameParts = window.location.pathname.split('/').filter(Boolean);
		const firstSegment = pathnameParts.length ? '/' + pathnameParts[0] : '';
		const prefixes = uniqueValues(['', firstSegment, '/premium', '/premium-pkp']);

		const candidates = [
			endpointFromData,
			endpointFromData.replace(/\/+$/, ''),
			'/wp-json/custom/v1/questions-terms',
			'/index.php?rest_route=/custom/v1/questions-terms'
		];

		prefixes.forEach(function(prefix) {
			candidates.push(prefix + '/wp-json/custom/v1/questions-terms');
			candidates.push(prefix + '/index.php?rest_route=/custom/v1/questions-terms');
		});

		return uniqueValues(candidates);
	}

	function buildProbeUrl(endpointCandidate) {
		const url = new URL(endpointCandidate, window.location.origin);
		url.searchParams.set('page', '1');
		url.searchParams.set('per_page', '1');
		url.searchParams.set('sort', sort);
		return url.toString();
	}

	function setButtonDefault() {
		button.textContent = 'U\u010ditaj vi\u0161e';
		button.disabled = false;
	}

	function setButtonLoading() {
		button.textContent = 'U\u010ditavam...';
		button.disabled = true;
	}

	function setButtonRetry() {
		button.textContent = 'Poku\u0161aj ponovno';
		button.disabled = false;
	}

	function updateVisibleCount() {
		if (!visibleCount) {
			return;
		}

		visibleCount.textContent = String(container.querySelectorAll('.term-card-link').length);
	}

	function setUnavailableState() {
		button.style.display = 'none';

		if (stateNote) {
			stateNote.textContent = 'U\u010ditavanje dodatnih pojmova trenutno nije dostupno.';
		}
	}

	async function detectWorkingEndpoint() {
		if (activeEndpoint) {
			return activeEndpoint;
		}

		const candidates = buildEndpointCandidates();

		for (let i = 0; i < candidates.length; i += 1) {
			const candidate = candidates[i];

			try {
				const response = await fetch(buildProbeUrl(candidate), { credentials: 'same-origin' });
				if (!response.ok) {
					continue;
				}

				const payload = await response.json();
				if (Array.isArray(payload)) {
					activeEndpoint = candidate;
					return activeEndpoint;
				}
			} catch (error) {
				// Try next endpoint candidate.
			}
		}

		return '';
	}

	function buildUrl(pageToLoad, endpointCandidate) {
		const url = new URL(endpointCandidate, window.location.origin);
		url.searchParams.set('page', String(pageToLoad));
		url.searchParams.set('per_page', String(perPage));
		url.searchParams.set('sort', sort);
		return url.toString();
	}

	function createTermCard(term) {
		const termCount = parseInt(term.count || 0, 10);
		const termCountLabel = termCount + ' ' + (termCount === 1 ? 'pitanje' : 'pitanja');

		const link = document.createElement('a');
		link.className = 'term-card-link';
		link.href = term.link || '#';

		const card = document.createElement('article');
		card.className = 'terms-box term-card';

		const title = document.createElement('p');
		title.className = 'term-title';
		title.textContent = term.name || '';

		const badge = document.createElement('span');
		badge.className = 'term-count-badge';
		badge.textContent = termCountLabel;

		card.appendChild(title);
		card.appendChild(badge);
		link.appendChild(card);

		return link;
	}

	async function loadTerms(pageToLoad) {
		if (isLoading) {
			return;
		}

		isLoading = true;
		button.style.display = 'inline-flex';
		setButtonLoading();

		try {
			const workingEndpoint = await detectWorkingEndpoint();
			if (!workingEndpoint) {
				throw new Error('No working terms endpoint detected');
			}

			const response = await fetch(buildUrl(pageToLoad, workingEndpoint), { credentials: 'same-origin' });
			if (!response.ok) {
				throw new Error('Terms request failed');
			}

			const terms = await response.json();
			if (!Array.isArray(terms)) {
				throw new Error('Unexpected terms payload');
			}

			if (terms.length > 0) {
				const fragment = document.createDocumentFragment();
				terms.forEach(function(term) {
					fragment.appendChild(createTermCard(term));
				});
				container.appendChild(fragment);
			}

			if (emptyState) {
				emptyState.hidden = container.children.length > 0;
			}

			updateVisibleCount();

			nextPage = pageToLoad + 1;
			explorer.dataset.nextPage = String(nextPage);

			if (terms.length < perPage) {
				button.style.display = 'none';
			} else {
				button.style.display = 'inline-flex';
				setButtonDefault();
			}
		} catch (error) {
			console.error(error);
			setButtonRetry();
			button.style.display = 'inline-flex';
		} finally {
			isLoading = false;
		}
	}

	button.addEventListener('click', function() {
		loadTerms(nextPage);
	});

	(async function initializeTermsEndpoint() {
		const detectedEndpoint = await detectWorkingEndpoint();
		if (!detectedEndpoint) {
			setUnavailableState();
		}
	})();

	updateVisibleCount();
})();
</script>
