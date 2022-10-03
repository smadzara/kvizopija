<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kvizopija
 */


$questions_taxonomy = 'questions_categories';
$questions_terms = get_terms($questions_taxonomy); // Get all terms of a questions taxonomy

?>

<div class="forma">    
<?=get_search_form();?>
</div>
        <div class="sidebar-content-container sidebar-categories">
        <?php // Nove kategorije - Blok ?>

            <section class="page-contain sidebar">
            <?php foreach ( $questions_terms as $questions_term ) : //dump($questions_term)?>
                <a href="<?= get_term_link($questions_term->slug, $questions_taxonomy); ?>" class="data-card sidebar">
                    <h3><?= $questions_term->name; ?></h3>
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
                            $q2 = new WP_Query($args);
                        ?>
                    <h4>Osvježeno: <?php echo (date( 'j. n. Y.', strtotime($q2->post->post_date) )) ?></h4>
                    <p>Broj pitanja: <?= $questions_term->count; ?></p>
                    <span class="link-text">
                    Sva pitanja
                    <svg width="25" height="16" viewBox="0 0 25 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.8631 0.929124L24.2271 7.29308C24.6176 7.68361 24.6176 8.31677 24.2271 8.7073L17.8631 15.0713C17.4726 15.4618 16.8394 15.4618 16.4489 15.0713C16.0584 14.6807 16.0584 14.0476 16.4489 13.657L21.1058 9.00019H0.47998V7.00019H21.1058L16.4489 2.34334C16.0584 1.95281 16.0584 1.31965 16.4489 0.929124C16.8394 0.538599 17.4726 0.538599 17.8631 0.929124Z" fill="#753BBD"/>
                </svg>
                    </span>
                </a>
                <?php endforeach; ?>
            </section>

        <?php // Nove kategorije - Blok - END ?>
        </div>
