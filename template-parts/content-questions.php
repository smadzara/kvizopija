<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pkp
 */

?>

<?php
$questions_taxonomy = 'questions_categories';
$questions_terms = get_terms($questions_taxonomy); // Get all terms of a questions taxonomy
$term_list = wp_get_post_terms( $questions_taxonomy, 'questions_terms', array( 'fields' => 'all' ) ); // čupam termove iz CPT
$terms = get_the_terms( get_the_ID(), 'questions_terms' );

//dump($terms);
?>

<!-- <p>content-questions.php</p> -->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php if($terms):?>
                        <p class="question-category">Pojmovi:
                            <?php foreach ($terms as $term):
                                echo '<a href="'.get_term_link($term->slug, 'questions_terms').'">|' .$term->name.'| </a>';
                            endforeach;
                    endif;
                     ?>

	<p class="question-date">Objavljeno:
		<span class="question-accent"><?= esc_html( get_the_date( 'j. n. Y.' ) ); ?></span>
	</p>

	<header class="question-category-single">
		<?php
		if ( is_singular() ) :
			the_title( '<p>', '</p>' );
		else :
			the_title( '<p>', '</p>'  );
		endif;
?>
	</header><!-- .entry-header -->

	<?php kvizopija_post_thumbnail(); ?>

	<div class="answer-category">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'pkp' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			)
		);
        ?>
        
    </div>

	<footer class="entry-footer">
		<?php kvizopija_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->


