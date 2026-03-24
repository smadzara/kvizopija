<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pkp
 */

$current_page   = get_post();
$is_locked_page = class_exists( 'MeprRule' ) && $current_page instanceof WP_Post && MeprRule::is_locked( $current_page );

if ( ! $is_locked_page ) {
	$args = array(
		'post_type'      => 'questions',
		'orderby'        => 'rand',
		'order'          => 'DESC',
		'posts_per_page' => '40',
	);

	$query = new WP_Query( $args );
}
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

		<?php if ( ! $is_locked_page ) : ?>
			<div class="container-questions container-questions--random40">
				<?php if ( $query->have_posts() ) : ?>
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						$categories = get_the_terms( get_the_ID(), 'questions_categories' );
						$term_list  = wp_get_post_terms( get_the_ID(), 'questions_terms', array( 'fields' => 'all' ) );
						?>
						<div class="questions-homepage">
							<?php
							$category_link = '';
							if ( ! empty( $categories ) ) {
								$category_link = get_term_link( $categories[0]->slug, 'questions_categories' );
							}
							if ( ! empty( $categories ) && ! is_wp_error( $category_link ) ) :
								?>
								<p class="question-category">Kategorija:
									<a href="<?= esc_url( $category_link ); ?>">
										<?= esc_html( $categories[0]->name ); ?>
									</a>
								</p>
							<?php endif; ?>

							<p class="question-date">Objavljeno:
								<span class="question-accent"><?= esc_html( get_the_date( 'j. n. Y.' ) ); ?></span>
							</p>

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

							<p class="questions"><?php the_title(); ?></p>
							<div class="answer-homepage"><?php the_content(); ?></div>
						</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			</div>
			<div class="more-questions more-questions--random40">
				<form class="random40-action-form" action="<?php echo esc_url( get_permalink() ); ?>">
					<button type="submit" class="homepage-button random40-action-button terms-load-more">Promje&#353;aj pitanja</button>
				</form>
				<button id="btn" type="button" class="homepage-button random40-action-button terms-load-more">Otkrij odgovore</button>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php if ( ! $is_locked_page ) : ?>
	<?php // Otkrij odgovore - START ?>
	<script>

	const btn = document.getElementById('btn');
	const para = document.querySelectorAll('.answer-homepage');

	if (btn) {
	  btn.addEventListener('click', () => {
	    para.forEach(el => {
	      el.classList.toggle('show');
	    });
	  });
	}

	</script>
	<?php // Otkrij odgovore - END ?>
<?php endif; ?>

<!-- END Kvizopija Template -->

