<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

$questions_taxonomy = 'questions_categories';
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

        <div class="category-container">
        <h2>Zadnje objavljena pitanja</h2>
            <?php foreach ( $questions_terms as $questions_term ) : //dump($questions_term)?>
            <a href="<?= get_term_link($questions_term->slug, $questions_taxonomy); ?>">
                <div class="category-box">
                    <h2><?= $questions_term->name; ?></h2>
                    <p class="number-of-questions">
                        Broj pitanja: <span class="accent"><?= $questions_term->count; ?></span>
                    </p>
                    <p class="refresh-date">
                        <?php // Vadi datum iz zadnjeg objavljenog posta u kategoriji CPT-a
                            $args = array(
                                'post_type' => array('questions'),
                                'post_status' => 'publish',
                                'posts_per_page' => 1,
                                'tax_query' => array(
                                    array (
                                        'taxonomy' => 'questions_categories',
                                        'field' => 'slug',
                                        'terms' => array($questions_term->slug),
                                    )
                                ),
                            );
                            $q = new WP_Query($args);
                        ?>
                        Zadnje osvježavanje: <span class="accent"><?php echo (date( 'd.m.Y.', strtotime($q->post->post_date) )) ?></span>
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="container-questions">
            <h2>Zadnje objavljena pitanja</h2>
            <?php foreach($posts as $key => $item): 
                $terms = get_the_terms( $item, 'questions_categories'); // povezujem CPT taksonomiju sa postom
                $question_author = get_field('question_author', $item->ID); // čupam ACF iz CPT
                $question_author_url = get_field('question_author_url', $item->ID); // čupam ACF iz CPT
                //dump($question_author);
            ?>
                <div class="questions-homepage">
                    <p class="question-category">Kategorija:
                        <a href="<?= get_term_link($terms[0]->slug, $questions_taxonomy); ?>">
                            <?=$terms[0]-> name; ?>
                        </a>
                    </p>
                    <p class="question-date">Objavljeno:
                        <span class="question-accent"><?=get_the_date( 'j. n. Y.', $item->ID ) ?></span>
                    </p>
                    <?php if(empty($question_author) || (empty($question_author_url))): ?>
                        <p class="question-author">Autor: <a href="https://kvizopija.com" target="_blank">kvizopija.com</a></p>
                        <?php else: ?>
                        <p class="question-author">Autor: <a href="<?=$question_author_url;?>" target="_blank"><?=$question_author;?></a></p>
                    <?php endif; ?>
                    <p><?=get_the_title($item->ID) ?></p>
                    <div class="answer"><?= apply_filters('the_content', get_the_content(null,false,$item)); ?></div>
                </div>
            <?php endforeach; ?>
        </div>


    </div>




</section>


<!-- END Kvizopija Template -->