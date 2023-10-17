<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

get_header();

$questions_taxonomy = 'questions_categories';
$questions_terms = get_terms($questions_taxonomy); // Get all terms of a questions taxonomy

$args = [
    'post_type' => 'questions',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => '50',
];

$query = new WP_Query($args);
$posts=$query->posts;

//dump($query->posts);

?>

	<main id="primary" class="container">

            <div class="content-container">
                <div class="container-questions">
                        <h2>Sva pub kviz pitanja</h2>
                        <?php foreach($posts as $key => $item): 
                            $terms = get_the_terms( $item, 'questions_categories'); // povezujem CPT taksonomiju sa postom
                            $question_author = get_field('question_author', $item->ID); // čupam ACF iz CPT
                            $question_author_url = get_field('question_author_url', $item->ID); // čupam ACF iz CPT
                            $term_list = wp_get_post_terms( $item->ID, 'questions_terms', array( 'fields' => 'all' ) ); // čupam termove iz CPT
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
                                <?php if($term_list):?>
                        <p class="question-category">Pojmovi:
                            <?php foreach ($term_list as $single_term_key):
                                echo '<a href="'.get_term_link($single_term_key->slug, 'questions_terms').'">|' .$single_term_key->name.'| </a>';
                            endforeach;
                    endif;
                     ?>
                        </p>
                                <p><?=get_the_title($item->ID) ?></p>
                                <div class="answer-category"><?= apply_filters('the_content', get_the_content(null,false,$item)); ?></div>
                            </div>
                        <?php endforeach; 
                        
                        the_posts_pagination( array(
                            'prev_text'          => __( 'Novija pitanja', 'pkp' ),
                            'next_text'          => __( 'starija pitanja', 'pkp' ),
                            'before_page_number' => '<span class="meta-nav screen-reader-text pagination">' . __( 'Stranica', 'pkp' ) . ' </span>',
                        ) );
                        
                        ?>
                </div>
                    <div class="more-questions">
                        <button id='btn' type="button" class="homepage-button">Otkrij odgovore</button>
                    </div>
            </div>

	</main><!-- #main -->

<?php
get_sidebar('questions');
get_footer();
?>

<?php // Otkrij odgovore - START ?>
<script>

const btn = document.getElementById('btn');
const para = document.querySelectorAll('.answer-category');

btn.addEventListener('click',()=>{
  para.forEach(el => {
    el.classList.toggle('show');
  })
})

</script>
    <?php // Otkrij odgovore - END ?>
