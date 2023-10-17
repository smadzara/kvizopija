<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */
?>
<section class="container" style="background-color: #e7f3f1;">
    <div class="content-container">

        <div class="page-title">
            <h1>
                <?php the_title() ?>
            </h1>
        </div>

        <div class="page-description">
            <p class="page-description-paragraph-text">
                <?php the_content() ?>
            </p>
        </div>
    </div>
</section>

<?php
$questions_taxonomy = 'questions_categories';

if (isset($_POST['submit'])) {
    $number_of_posts = $_POST['number_of_posts'];
    $categories = isset($_POST['category']) ? $_POST['category'] : array();
    $date_range = $_POST['date_range'];

    $args = array(
        'post_type' => 'questions',
        'post_status' => 'publish',
        'orderby' => 'rand',
        'posts_per_page' => $number_of_posts,
        'tax_query' => array(
            array(
                'taxonomy' => 'questions_categories',
                'field' => 'slug',
                'terms' => $categories
            )
        ),
        'date_query' => array(
            array(
                'after' => $date_range,
                'inclusive' => true,
            ),
        ),
    );
    $random_posts = new WP_Query($args);

    if ($random_posts->have_posts()) {
        $questions_and_answers = array();
        $player_name = sanitize_text_field($_POST['player_name']);
        while ($random_posts->have_posts()) {
            $random_posts->the_post();
            $questions_and_answers[] = array(
                'question' => get_the_title(),
                'answer' => get_the_content()
            );
        }
        set_transient('questions_and_answers', $questions_and_answers, 12 * HOUR_IN_SECONDS);
        set_transient('player_name', $player_name, 12 * HOUR_IN_SECONDS);
        wp_reset_postdata();

        // Ovdje postavite kod za preusmjeravanje
        $redirect_url = get_permalink(get_page_by_path('kviz-aplikacija-igraj'));
        wp_redirect($redirect_url);
        exit;
    }
}

?>

<div style="padding-top:30px;">
    <form action="" method="post">

    <label for="player_name" style="padding-top:20px;">Ime igrača:</label>
        <input type="text" id="player_name" name="player_name">

        <label for="number_of_posts" style="padding-top:20px;">Broj pitanja:</label>
        <input type="number" id="number_of_posts" name="number_of_posts">

        <label for="category" style="padding-top:20px;">Kategorije:</label>
        <div style="display: flex; flex-wrap: wrap;">
            <?php
            $categories = get_terms($questions_taxonomy);
            foreach($categories as $category) {
                echo '<div style="width: 50%; padding: 5px;"><input type="checkbox" name="category[]" value="'.$category->slug.'">'.'<span style="font-size:16px;">'.$category->name.'</span></div>';
            }
            ?>
        </div>

        <label for="date_range" style="padding-top:30px;">Pitanja novija od datuma:</label>
        <input type="text" id="date_range" name="date_range" placeholder="npr. 23.11.2022.">

        <input type="submit" name="submit" value="Generiraj">

    </form>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
// Add an event listener to the button
jQuery('.show-content-button').click(function() {
    // Show the content when the button is clicked
    jQuery(this).siblings('.post-answer-generator').show();
});
</script>
