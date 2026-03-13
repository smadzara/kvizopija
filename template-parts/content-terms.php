<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_terms';
$terms_per_page     = 100;

$terms_page_1 = get_terms(
	array(
		'taxonomy'   => $questions_taxonomy,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'number'     => $terms_per_page,
		'offset'     => 0,
	)
);

if ( is_wp_error( $terms_page_1 ) || ! is_array( $terms_page_1 ) ) {
	$terms_page_1 = array();
}

$total_terms = wp_count_terms(
	$questions_taxonomy,
	array(
		'hide_empty' => false,
	)
);

if ( is_wp_error( $total_terms ) ) {
	$total_terms = 0;
}

$total_terms     = (int) $total_terms;
$has_more_initial = $total_terms > $terms_per_page;
$alphabet        = array( 'A', 'B', 'C', 'Č', 'Ć', 'D', 'Đ', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'Š', 'T', 'U', 'V', 'Z', 'Ž' );
?>

<section class="container terms-page">
	<div
		class="content-container"
		id="termsExplorer"
		data-endpoint="<?= esc_attr( wp_make_link_relative( rest_url( 'custom/v1/questions-terms' ) ) ); ?>"
		data-per-page="<?= esc_attr( $terms_per_page ); ?>"
		data-next-page="<?= esc_attr( $has_more_initial ? 2 : 1 ); ?>"
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
					<strong id="termsVisibleCount"><?= esc_html( number_format_i18n( count( $terms_page_1 ) ) ); ?></strong>
					prikazano
				</span>
			</div>
		</div>

		<div class="terms-controls" id="termsControls">
			<div class="terms-search-wrap">
				<label class="screen-reader-text" for="termsSearchInput">Pretraži pojmove</label>
				<input
					id="termsSearchInput"
					type="search"
					class="terms-search-input"
					placeholder="Pretraži pojmove..."
					autocomplete="off"
				>
			</div>

			<div class="terms-letter-filter" id="termsLetterFilter" role="group" aria-label="Filtriraj po početnom slovu">
				<button type="button" class="terms-letter-btn is-active" data-letter="">Sve</button>
				<?php foreach ( $alphabet as $letter ) : ?>
					<button type="button" class="terms-letter-btn" data-letter="<?= esc_attr( $letter ); ?>"><?= esc_html( $letter ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="terms-results-header">
			<h2 class="categories-title">Pojmovi po abecedi</h2>
			<p id="termsFilterState" class="terms-results-note">Prikaz svih pojmova</p>
		</div>

		<div class="terms-container terms-grid" id="termsContainer">
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

		<p id="termsEmptyState" class="terms-empty" <?= ! empty( $terms_page_1 ) ? 'hidden' : ''; ?>>
			Nema pojmova za odabrani filtar.
		</p>

		<div class="navigation pagination pagination--terms">
			<button
				id="loadMoreTerms"
				type="button"
				class="homepage-button terms-load-more"
				<?= $has_more_initial ? '' : 'style="display:none;"'; ?>
			>
				U&#269;itaj vi&#353;e
			</button>
		</div>
	</div>
</section>

<script>
(function() {
	const explorer = document.getElementById('termsExplorer');
	if (!explorer) {
		return;
	}

	const endpointFromData = explorer.dataset.endpoint || '/wp-json/custom/v1/questions-terms/';
	const perPage = parseInt(explorer.dataset.perPage || '100', 10);
	let nextPage = parseInt(explorer.dataset.nextPage || '1', 10);
	let activeEndpoint = '';

	const container = document.getElementById('termsContainer');
	const button = document.getElementById('loadMoreTerms');
	const searchInput = document.getElementById('termsSearchInput');
	const lettersWrapper = document.getElementById('termsLetterFilter');
	const filterState = document.getElementById('termsFilterState');
	const emptyState = document.getElementById('termsEmptyState');
	const visibleCount = document.getElementById('termsVisibleCount');
	const letterButtons = lettersWrapper ? lettersWrapper.querySelectorAll('.terms-letter-btn') : [];

	if (!container || !button) {
		return;
	}

	let isLoading = false;
	let currentQuery = '';
	let currentLetter = '';
	let searchDebounceTimer = null;

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
		return url.toString();
	}

	function setControlsDisabled() {
		if (searchInput) {
			searchInput.disabled = true;
			searchInput.placeholder = 'Pretraživanje trenutno nije dostupno';
		}

		letterButtons.forEach(function(btn) {
			btn.disabled = true;
		});

		button.style.display = 'none';

		if (filterState) {
			filterState.textContent = 'Filtriranje privremeno nije dostupno (REST endpoint nije pronađen).';
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

	function updateFilterState() {
		if (!filterState) {
			return;
		}

		const parts = [];
		if (currentLetter) {
			parts.push('slovo "' + currentLetter + '"');
		}
		if (currentQuery) {
			parts.push('pojam "' + currentQuery + '"');
		}

		filterState.textContent = parts.length ? 'Filtrirano: ' + parts.join(' + ') : 'Prikaz svih pojmova';
	}

	function buildUrl(pageToLoad, endpointCandidate) {
		const url = new URL(endpointCandidate, window.location.origin);
		url.searchParams.set('page', String(pageToLoad));
		url.searchParams.set('per_page', String(perPage));

		if (currentQuery) {
			url.searchParams.set('q', currentQuery);
		}

		if (currentLetter) {
			url.searchParams.set('letter', currentLetter);
		}

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

	async function loadTerms(pageToLoad, replaceExisting) {
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

			if (replaceExisting) {
				container.innerHTML = '';
			}

			if (terms.length > 0) {
				const fragment = document.createDocumentFragment();
				terms.forEach(function(term) {
					fragment.appendChild(createTermCard(term));
				});
				container.appendChild(fragment);
			}

			emptyState.hidden = container.children.length > 0;
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
		loadTerms(nextPage, false);
	});

	if (searchInput) {
		searchInput.addEventListener('input', function() {
			clearTimeout(searchDebounceTimer);
			searchDebounceTimer = setTimeout(function() {
				currentQuery = searchInput.value.trim();
				nextPage = 1;
				updateFilterState();
				loadTerms(1, true);
			}, 280);
		});
	}

	if (letterButtons.length) {
		letterButtons.forEach(function(buttonElement) {
			buttonElement.addEventListener('click', function() {
				letterButtons.forEach(function(item) {
					item.classList.remove('is-active');
				});

				buttonElement.classList.add('is-active');
				currentLetter = buttonElement.dataset.letter || '';
				nextPage = 1;
				updateFilterState();
				loadTerms(1, true);
			});
		});
	}

	(async function initializeTermsEndpoint() {
		const detectedEndpoint = await detectWorkingEndpoint();
		if (!detectedEndpoint) {
			setControlsDisabled();
		}
	})();

	updateFilterState();
	updateVisibleCount();
})();
</script>
