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

    <div class="content-container">
        <?php /*
        <div class="page-title">
            <h1>
                <?php the_title() ?>
            </h1>
        </div> */
        ?>

        <div class="page-description">
            <p class="page-description-paragraph-text">
                <?php the_content() ?>
            </p>
        </div>

        <?php $total_questions = new WP_Query(array( 'post_type' => 'questions' ));?>
            <?php if ($total_questions->have_posts()) { 
                $count_posts = wp_count_posts('questions')->publish; 
                if ( $count_posts == "1" ) { 
                    echo "<h3>Trenutno imamo samo jedno kviz pitanje...</h3>"; }
                else { echo "<h3>Trenutno brojimo $count_posts kviz pitanja.</h3>"; }
                    } else { ?>
                <h2>Trenutno nema kviz pitanja :(</h2>
            <?php } 
        ?>

        <?php /* Last question date */
            $latest_question = new WP_Query(
                array(
                    'post_type' => 'questions',
                    'post_status' => 'publish',
                    'posts_per_page' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC'
                )
            );

            if($latest_question->have_posts()){
                $latest_question_date = date( 'j. n. Y.', strtotime($latest_question->posts[0]->post_modified) );
                
            }

        ?>

        <h3>Zadnje ažuriranje baze pitanja: <?= $latest_question_date; ?></h3>
        <br>

        <?php // Nove kategorije - Blok ?>

            <section class="page-contain">
            <?php foreach ( $questions_terms as $questions_term ) : //dump($questions_term)?>
                <a href="<?= get_term_link($questions_term->slug, $questions_taxonomy); ?>" class="data-card">
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

        <div class="forma">
        <h2>Pronađi pitanja i odgovore</h2>    
            <?=get_search_form();?>
        </div>

        <div class="container-questions">
            <h2>Zadnje objavljena pitanja</h2>
            <?php foreach($posts as $key => $item): 
                $terms = get_the_terms( $item, 'questions_categories'); // povezujem CPT taksonomiju sa postom
                $question_author = get_field('question_author', $item->ID); // čupam ACF iz CPT
                $question_author_url = get_field('question_author_url', $item->ID); // čupam ACF iz CPT
                $term_list = wp_get_post_terms( $item->ID, 'questions_terms', array( 'fields' => 'all' ) ); // čupam termove iz CPT
            ?>
                <div class="questions-homepage">
                    <p class="question-category">Kategorija:
                        <a href="<?= get_term_link($terms[0]->slug, $questions_taxonomy); ?>">
                            <?=$terms[0]-> name; ?>
                        </a>
                    </p>
                    
                    <?php if($term_list):?>
                        <p class="question-category">Pojmovi:
                            <?php foreach ($term_list as $single_term_key):
                                echo '<a href="'.get_term_link($single_term_key->slug, 'questions_terms').'">|' .$single_term_key->name.'| </a>';
                            endforeach;
                    endif;
                     ?>
                        </p>
                    <p class="question-date">Objavljeno:
                        <span class="question-accent"><?=get_the_date( 'j. n. Y.', $item->ID ) ?></span>
                    </p>
                    <?php if(empty($question_author) || (empty($question_author_url))): ?>
                        <p class="question-author">Autor: <a href="https://kvizopija.com" target="_blank">kvizopija.com</a></p>
                        <?php else: ?>
                        <p class="question-author">Autor: <a href="<?=$question_author_url;?>" target="_blank"><?=$question_author;?></a></p>
                    <?php endif; ?>
                    <p class="questions"><?=get_the_title($item->ID) ?></p>
                    <div class="answer-homepage"><?= apply_filters('the_content', get_the_content(null,false,$item)); ?></div>
                    
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="more-questions">

            <form action="<?php echo get_post_type_archive_link( 'questions' ); ?>" style="padding: 0px;">
                <input type="submit" value="SVA KVIZ PITANJA" style="background-color: red; margin-top: 0px;" />
            </form>
        </div>
		<div class="more-questions">

		<button id='btn' type="button" class="homepage-button">Otkrij odgovore</button>

		</div>

        <h2>Pomozi zajednici, pošalji nam svoj set</h2>

        <p>Ukoliko želite pomoći našem malom projektu, slobodno nam pošaljite vaš set pitanja koji ćemo s ponosom objaviti. Pa ako ste zainteresirani, slobodno se javite.</p>
        <br>

        <?php 

            echo do_shortcode('[gravityform id="2" title="false" description="false"]');

        ?>

    </div>


</section>



<?php // Otkrij odgovore - START ?>
<script>

const btn = document.getElementById('btn');
const para = document.querySelectorAll('.answer-homepage');

btn.addEventListener('click',()=>{
  para.forEach(el => {
    el.classList.toggle('show');
  })
})

</script>
<?php // Otkrij odgovore - END ?>