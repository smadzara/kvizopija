<?php
/**
 * Template part for displaying a single blog post.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pkp
 */

$published_date = get_the_date( 'j. n. Y.' );

$categories_list = get_the_category_list( ', ' );
$tags_list       = get_the_tag_list( '', ', ' );

$previous_post = get_previous_post();
$next_post     = get_next_post();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post-modern' ); ?>>
	<header class="single-post-modern__header">
		<p class="single-post-modern__meta">
			<span>Objavljeno: <strong><?= esc_html( $published_date ); ?></strong></span>
		</p>

		<?php the_title( '<h1 class="single-post-modern__title">', '</h1>' ); ?>

		<?php if ( has_excerpt() ) : ?>
			<p class="single-post-modern__excerpt"><?= esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="single-post-modern__thumbnail">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
	<?php endif; ?>

	<div class="single-post-modern__body entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
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

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pkp' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<?php if ( $categories_list || $tags_list ) : ?>
		<div class="single-post-modern__tax">
			<?php if ( $categories_list ) : ?>
				<p class="single-post-modern__tax-row">
					<span>Kategorije:</span>
					<?= wp_kses_post( $categories_list ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $tags_list ) : ?>
				<p class="single-post-modern__tax-row">
					<span>Oznake:</span>
					<?= wp_kses_post( $tags_list ); ?>
				</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>

<?php if ( $previous_post || $next_post ) : ?>
	<nav class="single-post-modern__nav" aria-label="Objava navigacija">
		<?php if ( $previous_post ) : ?>
			<a class="single-post-modern__nav-link" href="<?= esc_url( get_permalink( $previous_post ) ); ?>">
				&larr; <?= esc_html( get_the_title( $previous_post ) ); ?>
			</a>
		<?php endif; ?>

		<?php if ( $next_post ) : ?>
			<a class="single-post-modern__nav-link single-post-modern__nav-link--next" href="<?= esc_url( get_permalink( $next_post ) ); ?>">
				<?= esc_html( get_the_title( $next_post ) ); ?> &rarr;
			</a>
		<?php endif; ?>
	</nav>
<?php endif; ?>
