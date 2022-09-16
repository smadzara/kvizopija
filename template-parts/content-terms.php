<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_terms';
$questions_terms = get_terms($questions_taxonomy); // Get all terms of a questions taxonomy

$args = [
    'post_type' => 'questions',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => '20',
/*  'tax_query' => [
        [
            'taxonomy' => 'questions_categories',
            'field' => 'slug',
            'terms' => ['film']
        ],
    ] */

];

$query = new WP_Query($args);
$posts=$query->posts;

//dump($query->posts);

?>

<!-- Kvizopija Template -->

<section class="container">

    <!-- <img src="img/Logo-70px.png" alt="Pub kviz pitanja by kvizopija.com - Logo"> -->

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


        <div class="terms-container">
        <h2 class="categories-title">Pojmovi</h2>
            <?php foreach ( $questions_terms as $questions_term ) : //dump($questions_term)?>
            <a href="<?= get_term_link($questions_term->slug, $questions_taxonomy); ?>">
                <div class="terms-box">
                    <p><?= $questions_term->name; ?> (<?= $questions_term->count; ?>)</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        
    </div>
</section>





<!-- END Kvizopija Template -->