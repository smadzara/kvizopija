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

        <div class="terms-container">
        <h2 class="categories-title">Pojmovi 2</h2>


<!-- query -->
<?php

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

// Get all term ID's in a given taxonomy
$taxonomy = 'questions_terms';
$taxonomy_terms = get_terms( $taxonomy, array(
    'hide_empty' => 0,
    'fields' => 'ids'
) );

// Use the new tax_query WP_Query argument (as of 3.1)
$query = new WP_Query( array(
    'posts_per_page' => 10,
    'paged' => $paged,
    'tax_query' => array(
        array(
            'taxonomy' => $taxonomy,
            'field' => 'id',
            'terms' => $taxonomy_terms,
        ),
    ),
) 
);

//dump($query);

?>

<?php if ( $query->have_posts() ) : ?>

<!-- begin loop -->
<?php while ( $query->have_posts() ) : $query->the_post(); ?>

<?php echo(get_term($query->the_post())->name);  ?>

<?php endwhile; ?>
<!-- end loop -->


<!-- WHAT GOES HERE?????? -->
<div class="pagination">
    <?php 
        echo paginate_links( array(
            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'total'        => $query->max_num_pages,
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'format'       => '?paged=%#%',
            'show_all'     => false,
            'type'         => 'plain',
            'end_size'     => 2,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
            'next_text'    => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
            'add_args'     => false,
            'add_fragment' => '',
        ) );
    ?>
</div>
<!-- WHAT GOES HERE?????? -->


<?php wp_reset_postdata(); ?>

<?php else : ?>
    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>

        </div>

    </div>
</section>





<!-- END Kvizopija Template -->