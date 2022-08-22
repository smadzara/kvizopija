<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kvizopija
 */

get_search_form();

$questions_taxonomy = 'questions_categories';
$questions_terms = get_terms($questions_taxonomy); // Get all terms of a questions taxonomy

?>

        <div class="sidebar-content-container sidebar-categories">
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
