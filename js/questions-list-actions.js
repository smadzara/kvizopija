/**
 * Questions list interactions (reveal answers + load more).
 *
 * Works via data attributes so templates can opt in without inline scripts.
 */
( function() {
	'use strict';

	const answerShownClass = 'show';

	function toInt( value, fallback ) {
		const parsed = parseInt( value, 10 );
		return Number.isNaN( parsed ) ? fallback : parsed;
	}

	function revealAnswers( button ) {
		const targetSelector = button.getAttribute( 'data-reveal-target' ) || '.answer-category';
		const bodyClass = button.getAttribute( 'data-reveal-body-class' ) || '';

		if ( bodyClass ) {
			document.body.classList.add( bodyClass );
		}

		document.querySelectorAll( targetSelector ).forEach( function( answer ) {
			answer.classList.add( answerShownClass );
		} );
	}

	function buildLoadMoreUrl( button, pageNumber ) {
		const mode = button.getAttribute( 'data-url-mode' ) || 'path';

		if ( 'search' === mode ) {
			const homeUrl = button.getAttribute( 'data-home-url' ) || window.location.origin;
			const searchQuery = button.getAttribute( 'data-search-query' ) || '';
			const url = new URL( homeUrl, window.location.origin );

			url.searchParams.set( 's', searchQuery );
			url.searchParams.set( 'post_type', 'questions' );
			url.searchParams.set( 'paged', String( pageNumber ) );

			return url.toString();
		}

		const baseUrl = ( button.getAttribute( 'data-base-url' ) || window.location.pathname ).replace( /\/+$/, '' );
		return baseUrl + '/page/' + pageNumber + '/';
	}

	async function loadMoreQuestions( button ) {
		if ( '1' === button.dataset.loading ) {
			return;
		}

		const currentPage = toInt( button.getAttribute( 'data-page' ), 1 );
		const maxPages = toInt( button.getAttribute( 'data-max-pages' ), 1 );

		if ( currentPage >= maxPages ) {
			return;
		}

		const containerSelector = button.getAttribute( 'data-container-selector' ) || '#primary.site-main';
		const itemsSelector = button.getAttribute( 'data-items-selector' ) || '#primary.site-main article';
		const answerSelector = button.getAttribute( 'data-answer-selector' ) || '.answer-category';
		const revealBodyClass = button.getAttribute( 'data-reveal-body-class' ) || '';
		const defaultText = button.getAttribute( 'data-default-text' ) || 'U\u010ditaj vi\u0161e';
		const loadingText = button.getAttribute( 'data-loading-text' ) || 'U\u010ditavam...';
		const retryText = button.getAttribute( 'data-retry-text' ) || 'Poku\u0161aj ponovno';

		const container = document.querySelector( containerSelector );
		if ( ! container ) {
			return;
		}

		button.dataset.loading = '1';
		button.disabled = true;
		button.textContent = loadingText;

		try {
			const nextPage = currentPage + 1;
			const response = await fetch( buildLoadMoreUrl( button, nextPage ), { credentials: 'same-origin' } );

			if ( ! response.ok ) {
				throw new Error( 'Questions page request failed' );
			}

			const html = await response.text();
			const parser = new DOMParser();
			const doc = parser.parseFromString( html, 'text/html' );
			const newItems = doc.querySelectorAll( itemsSelector );

			if ( ! newItems.length ) {
				button.style.display = 'none';
				return;
			}

			const shouldRevealAnswers = revealBodyClass && document.body.classList.contains( revealBodyClass );
			const fragment = document.createDocumentFragment();

			newItems.forEach( function( item ) {
				if ( shouldRevealAnswers ) {
					item.querySelectorAll( answerSelector ).forEach( function( answer ) {
						answer.classList.add( answerShownClass );
					} );
				}

				fragment.appendChild( item );
			} );

			container.appendChild( fragment );

			button.setAttribute( 'data-page', String( nextPage ) );

			if ( nextPage >= maxPages ) {
				button.style.display = 'none';
				return;
			}

			button.textContent = defaultText;
			button.disabled = false;
		} catch ( error ) {
			console.error( error );
			button.textContent = retryText;
			button.disabled = false;
		} finally {
			button.dataset.loading = '0';
		}
	}

	document.querySelectorAll( '[data-answers-reveal]' ).forEach( function( revealButton ) {
		revealButton.addEventListener( 'click', function() {
			revealAnswers( revealButton );
		} );
	} );

	document.querySelectorAll( '[data-questions-load-more]' ).forEach( function( loadMoreButton ) {
		loadMoreButton.addEventListener( 'click', function() {
			loadMoreQuestions( loadMoreButton );
		} );
	} );
}() );
