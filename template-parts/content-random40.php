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
    'orderby' => 'rand',
    'order' => 'DESC',
    'posts_per_page' => '40',
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



        <div class="container-questions">
            <?php foreach($posts as $key => $item): 
                $terms = get_the_terms( $item, 'questions_categories'); // povezujem CPT taksonomiju sa postom
                $question_author = get_field('question_author', $item->ID); // čupam ACF iz CPT
                $question_author_url = get_field('question_author_url', $item->ID); // čupam ACF iz CPT
                //dump($question_author);
            ?>
                <div class="questions-homepage">
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
        
            <form action="<?php echo get_permalink(); ?>">
                <input type="submit" value="PROMJEŠAJ PITANJA" />
            </form>

        </div>

        <div class="more-questions">

<button id='btn' type="button" class="random40-button">Otkrij odgovore</button>

</div>
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

<!-- END Kvizopija Template -->