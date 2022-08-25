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
                    <p><?php the_title(); ?></p>
                    <div class="answer"><?php the_excerpt(); ?></div>
                </div>
        </div>
		
    </div>
</section>
</article><!-- #post-<?php the_ID(); ?> -->