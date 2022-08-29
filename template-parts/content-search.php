<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<section class="container">

    <div class="content-container">

        <div class="container-questions">
                    <?php $title = get_the_title(); $keys= explode(" ",$s); $title = preg_replace('/('.implode('|', $keys) .')/iu', '<strong class="search-results-highlited">\0</strong>', $title); ?>
                    <p><?= $title; ?></p>

                        <?php $excerpt = get_the_excerpt(); $keys= explode(" ",$s); $excerpt = preg_replace('/('.implode('|', $keys) .')/iu', '<strong class="search-results-highlited">\0</strong>', $excerpt); ?>
                        <div class="answer"><?= $excerpt; ?></div>
                </div>
        </div>
		
    </div>
</section>
</article><!-- #post-<?php the_ID(); ?> -->