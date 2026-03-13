<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_categories';
$current_page       = get_post();
$is_locked_page     = class_exists( 'MeprRule' ) && $current_page instanceof WP_Post && MeprRule::is_locked( $current_page );
$has_submit         = isset( $_POST['submit'] );
$random_posts       = null;

if ( ! $is_locked_page && $has_submit ) {
	$number_of_posts = isset( $_POST['number_of_posts'] ) ? absint( $_POST['number_of_posts'] ) : 0;
	$categories      = isset( $_POST['category'] ) && is_array( $_POST['category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['category'] ) ) : array();
	$date_range_raw  = isset( $_POST['date_range'] ) ? sanitize_text_field( wp_unslash( $_POST['date_range'] ) ) : '';
	$date_range      = trim( $date_range_raw );
	$player_name     = isset( $_POST['player_name'] ) ? sanitize_text_field( wp_unslash( $_POST['player_name'] ) ) : '';

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

	if ( $random_posts->have_posts() ) {
		$questions_and_answers = array();

		while ( $random_posts->have_posts() ) {
			$random_posts->the_post();
			$filtered_answer = apply_filters( 'the_content', get_the_content() );
			$questions_and_answers[] = array(
				'question' => get_the_title(),
				'answer'   => trim( wp_strip_all_tags( $filtered_answer ) ),
			);
		}

		set_transient( 'questions_and_answers', $questions_and_answers, 12 * HOUR_IN_SECONDS );
		set_transient( 'player_name', $player_name, 12 * HOUR_IN_SECONDS );
		wp_reset_postdata();

		$redirect_url = get_permalink( get_page_by_path( 'kviz-aplikacija-igraj' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	wp_reset_postdata();
}
?>

<section class="container quizapp-page-layout" style="background-color: #e7f3f1;">
	<div class="content-container">
		<div class="page-title">
			<h1><?php the_title(); ?></h1>
		</div>

		<div class="page-description">
			<p class="page-description-paragraph-text"><?php the_content(); ?></p>
		</div>

		<?php if ( ! $is_locked_page ) : ?>
			<?php if ( $has_submit && $random_posts instanceof WP_Query && ! $random_posts->have_posts() ) : ?>
				<p class="onlinequiz-message">Nema rezultata za odabrane parametre. Pokusaj s drugim odabirom.</p>
			<?php endif; ?>

			<div class="onlinequiz-generator">
				<form action="" method="post" class="onlinequiz-generator-form">
					<label class="onlinequiz-field-label" for="player_name">Ime igrača:</label>
					<input
						type="text"
						id="player_name"
						name="player_name"
						value="<?= isset( $_POST['player_name'] ) ? esc_attr( wp_unslash( $_POST['player_name'] ) ) : ''; ?>"
					>

					<label class="onlinequiz-field-label" for="number_of_posts">Broj pitanja:</label>
					<input
						type="number"
						id="number_of_posts"
						name="number_of_posts"
						min="1"
						value="<?= isset( $_POST['number_of_posts'] ) ? esc_attr( wp_unslash( $_POST['number_of_posts'] ) ) : ''; ?>"
					>

					<label class="onlinequiz-field-label" for="category">Kategorije:</label>
					<div class="onlinequiz-category-grid">
						<?php
						$categories          = get_terms( $questions_taxonomy );
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
					<input
						type="text"
						id="date_range"
						name="date_range"
						placeholder="npr. 23.11.2022."
						value="<?= isset( $_POST['date_range'] ) ? esc_attr( wp_unslash( $_POST['date_range'] ) ) : ''; ?>"
					>

					<input type="submit" name="submit" value="Generiraj" class="homepage-button onlinequiz-generate-button">
				</form>
			</div>
		<?php endif; ?>
	</div>
</section>
