<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_categories';
$terms              = get_the_terms( get_the_ID(), $questions_taxonomy );
$question_author    = get_field( 'question_author', get_the_ID() );
$question_author_url = get_field( 'question_author_url', get_the_ID() );
$term_list          = wp_get_post_terms( get_the_ID(), 'questions_terms', array( 'fields' => 'all' ) );
$search_query       = trim( get_search_query() );

if ( ! function_exists( 'kvizopija_highlight_search_text' ) ) {
	/**
	 * Highlight searched keywords in plain text for search listings.
	 */
	function kvizopija_highlight_search_text( $text, $search_phrase ) {
		$highlighted = esc_html( (string) $text );
		$tokens      = preg_split( '/\s+/', trim( (string) $search_phrase ) );

		if ( ! is_array( $tokens ) ) {
			return $highlighted;
		}

		$tokens = array_values(
			array_filter(
				$tokens,
				function( $token ) {
					return '' !== trim( $token );
				}
			)
		);

		if ( empty( $tokens ) ) {
			return $highlighted;
		}

		$pattern_parts = array_map(
			function( $token ) {
				return preg_quote( $token, '/' );
			},
			$tokens
		);

		$highlighted = preg_replace(
			'/(' . implode( '|', $pattern_parts ) . ')/iu',
			'<strong class="search-results-highlited">$1</strong>',
			$highlighted
		);

		return wp_kses(
			(string) $highlighted,
			array(
				'strong' => array(
					'class' => array(),
				),
			)
		);
	}
}

$title_highlighted = kvizopija_highlight_search_text( get_the_title(), $search_query );
$excerpt_raw       = wp_strip_all_tags( (string) get_the_excerpt() );

if ( '' === trim( $excerpt_raw ) ) {
	$excerpt_raw = wp_trim_words( wp_strip_all_tags( get_the_content( null, false, get_the_ID() ) ), 38 );
}

$excerpt_highlighted = kvizopija_highlight_search_text( $excerpt_raw, $search_query );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'questions-homepage search-result-item' ); ?>>
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
		<p class="question-author">Autor: <a href="https://kvizopija.com" target="_blank" rel="noopener noreferrer">kvizopija.com</a></p>
	<?php else : ?>
		<p class="question-author">Autor: <a href="<?= esc_url( $question_author_url ); ?>" target="_blank" rel="noopener noreferrer"><?= esc_html( $question_author ); ?></a></p>
	<?php endif; ?>

	<?php if ( ! is_wp_error( $term_list ) && ! empty( $term_list ) ) : ?>
		<p class="question-category">Pojmovi:
			<?php foreach ( $term_list as $single_term ) : ?>
				<?php
				$single_term_link = get_term_link( $single_term->slug, 'questions_terms' );
				if ( ! is_wp_error( $single_term_link ) ) :
					?>
					<a href="<?= esc_url( $single_term_link ); ?>">|<?= esc_html( $single_term->name ); ?>| </a>
				<?php endif; ?>
			<?php endforeach; ?>
		</p>
	<?php endif; ?>

	<p class="questions"><?= $title_highlighted; ?></p>
	<div class="answer-category"><p><?= $excerpt_highlighted; ?></p></div>
</article>
