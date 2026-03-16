<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_categories';
$has_submit         = isset( $_POST['submit'] );
$random_posts       = null;

if ( $has_submit ) {
	$number_of_posts = isset( $_POST['number_of_posts'] ) ? absint( $_POST['number_of_posts'] ) : 0;
	$categories      = isset( $_POST['category'] ) && is_array( $_POST['category'] ) ? array_map( 'sanitize_text_field', $_POST['category'] ) : array();
	$date_range_raw  = isset( $_POST['date_range'] ) ? sanitize_text_field( wp_unslash( $_POST['date_range'] ) ) : '';
	$date_range      = trim( $date_range_raw );

	$args = array(
		'post_type'      => 'questions',
		'post_status'    => 'publish',
		'orderby'        => 'rand',
		'posts_per_page' => $number_of_posts > 0 ? $number_of_posts : 20,
	);

	if ( ! empty( $categories ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'questions_categories',
				'field'    => 'slug',
				'terms'    => $categories,
			),
		);
	}

	if ( '' !== $date_range ) {
		$args['date_query'] = array(
			array(
				'after'     => $date_range,
				'inclusive' => true,
			),
		);
	}

	$random_posts = new WP_Query( $args );
}
?>

<section class="container" style="background-color: #e7f3f1;">
	<div class="content-container">
		<div class="page-title">
			<h1><?php the_title(); ?></h1>
		</div>

		<div class="page-description">
			<p class="page-description-paragraph-text"><?php the_content(); ?></p>
		</div>

		<?php if ( $has_submit && $random_posts instanceof WP_Query ) : ?>
			<?php if ( $random_posts->have_posts() ) : ?>
				<div class="container-questions questions-list--readable questions-list--onlinequiz">
					<?php
					$question_index = 0;
					while ( $random_posts->have_posts() ) :
						$random_posts->the_post();
						$question_index++;
						?>
						<div class="questions-homepage">
							<p class="questions">
								<span class="question-accent"><?= esc_html( $question_index ); ?>.</span>
								<?php the_title(); ?>
							</p>
							<div class="answer-homepage"><?php the_content(); ?></div>
						</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>

				<div class="more-questions more-questions--random40 more-questions--onlinequiz">
					<button id="showOnlineQuizAnswers" type="button" class="homepage-button random40-action-button">Otkrij odgovore</button>
				</div>
			<?php else : ?>
				<p class="onlinequiz-message">Nema rezultata za odabrane parametre. Pokusaj s drugim odabirom.</p>
			<?php endif; ?>
		<?php endif; ?>

		<div class="onlinequiz-generator">
			<form action="" method="post" class="onlinequiz-generator-form">
				<label class="onlinequiz-field-label" for="number_of_posts">Broj pitanja:</label>
				<input type="number" id="number_of_posts" name="number_of_posts" min="1" value="<?= isset( $_POST['number_of_posts'] ) ? esc_attr( wp_unslash( $_POST['number_of_posts'] ) ) : ''; ?>">

				<label class="onlinequiz-field-label" for="category">Kategorije:</label>
				<div class="onlinequiz-category-grid">
					<?php
					$categories = get_terms( $questions_taxonomy );
					$selected_categories = isset( $_POST['category'] ) && is_array( $_POST['category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['category'] ) ) : array();

					foreach ( $categories as $category ) :
						$is_checked = in_array( $category->slug, $selected_categories, true );
						?>
						<label class="onlinequiz-category-item">
							<input type="checkbox" name="category[]" value="<?= esc_attr( $category->slug ); ?>" <?= $is_checked ? 'checked' : ''; ?>>
							<span><?= esc_html( $category->name ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>

				<label class="onlinequiz-field-label" for="date_range">Pitanja novija od datuma:</label>
				<input type="text" id="date_range" name="date_range" placeholder="npr. 23.11.2022." value="<?= isset( $_POST['date_range'] ) ? esc_attr( wp_unslash( $_POST['date_range'] ) ) : ''; ?>">

				<input type="submit" name="submit" value="Generiraj" class="homepage-button onlinequiz-generate-button">
			</form>
		</div>
	</div>
</section>

<script>
(function() {
	const showAnswersButton = document.getElementById('showOnlineQuizAnswers');
	if (!showAnswersButton) {
		return;
	}

	showAnswersButton.addEventListener('click', function() {
		document.querySelectorAll('.questions-list--onlinequiz .answer-homepage').forEach(function(answer) {
			answer.classList.add('show');
		});
	});
})();
</script>
