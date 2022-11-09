<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

?>
<!-- <p>content-questions.php</p> -->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

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
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'kvizopija' ),
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


