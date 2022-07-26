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

<!-- query -->
<?php

//kraučeva magija SVI POJMOVI - Paginacija

$terms = get_terms( array(
    'taxonomy' => 'questions_terms',
    'hide_empty' => false,
/*  'orderby' => 'count', 
    'order' => 'DESC', sortira po broju postova u tagovima
*/
) );
$page = get_query_var('paged', '1' );
$total = count( $terms); // ovo je broj termova
$limit = 100; // koliko mi treba postova po stranici
$totalPages = ceil( $total/ $limit );
$page = min($page, $totalPages);
$page = max($page, 1);
$offset = ($page - 1) * $limit;

if( $offset < 0 ) $offset = 0;
$terms_10= array_slice( $terms, $offset, $limit ); ?>

                <div class="terms-container">
                        <h2 class="categories-title">Pojmovi po abecedi</h2>
                            <?php foreach ( $terms_10 as $questions_term ) : //dump($questions_term)?>
                            <a href="<?= get_term_link($questions_term->slug, $questions_taxonomy); ?>">
                                <div class="terms-box">
                                    <p class="term-title"><?= $questions_term->name; ?> (<?= $questions_term->count; ?>)</p>
                                </div>
                            </a>
                            <?php endforeach; ?>
                </div>
                    <div class="navigation pagination">
                    <?php 
                    $link = 'https://pubkvizpitanja.com/pojmovnik/page/';
                    $next_page = $page + 1;
                    $prev_page = $page - 1;
                    $pagerContainer = '<div style="width: 100%;">';   
                    if( $totalPages != 0 ) 
                    {
                    if( $page == 1 ) 
                    { 
                        $pagerContainer .= ''; 
                    } 
                    else 
                    { 
                        $pagerContainer .= sprintf( '<a href="' . $link . $prev_page . '" style="color: #c00"> &#171; prethodna stranica</a>', $page - 1 ); 
                    }
                    $pagerContainer .= ' <span> stranica <strong>' . $page . '</strong> od ' . $totalPages . '</span>'; 
                    if( $page == $totalPages ) 
                    { 
                        $pagerContainer .= ''; 
                    }
                    else 
                    { 
                        $pagerContainer .= sprintf( '<a href="' . $link . $next_page . '" style="color: #c00"> slijedeća stranica &#187; </a>', $page + 1 ); 
                    }           
                    }                   
                    $pagerContainer .= '</div>';

                    echo $pagerContainer;

                    ?>
                    </div>

    </div>
</section>

<!-- END Kvizopija Template -->