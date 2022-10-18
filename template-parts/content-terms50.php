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
    'orderby' => 'count', 
    'order' => 'DESC', /*sortira po broju postova u tagovima*/

) );
$page = get_query_var('paged', '1' );
$total = count( $terms); // ovo je broj termova
$limit = 75; // koliko mi treba postova po stranici
$totalPages = ceil( $total/ $limit );
$page = min($page, $totalPages);
$page = max($page, 1);
$offset = ($page - 1) * $limit;

if( $offset < 0 ) $offset = 0;
$terms_10= array_slice( $terms, $offset, $limit ); ?>

                <div class="terms-container">
                        <h2 class="categories-title">Pojmovi po broju pitanja</h2>
<!-- Novi pojmovi start -->

<section class="page-contain">
                <?php foreach ( $terms_10 as $questions_term ) : //dump($questions_term)?>
                    <a href="<?= get_term_link($questions_term->slug, $questions_taxonomy); ?>" class="data-card">
                        <h2><?= $questions_term->name; ?></h2>
                        <h5>Broj pitanja: <?= $questions_term->count; ?></h5>
                        <span class="link-text">
                        Sva pitanja
                        <svg width="25" height="16" viewBox="0 0 25 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.8631 0.929124L24.2271 7.29308C24.6176 7.68361 24.6176 8.31677 24.2271 8.7073L17.8631 15.0713C17.4726 15.4618 16.8394 15.4618 16.4489 15.0713C16.0584 14.6807 16.0584 14.0476 16.4489 13.657L21.1058 9.00019H0.47998V7.00019H21.1058L16.4489 2.34334C16.0584 1.95281 16.0584 1.31965 16.4489 0.929124C16.8394 0.538599 17.4726 0.538599 17.8631 0.929124Z" fill="#753BBD"/>
                    </svg>
                        </span>
                    </a>
                <?php endforeach; ?>
            </section>

<!-- Novi pojmovi end -->
                </div>



                    <div class="navigation pagination">
                    <?php 
                    $link = 'https://pubkvizpitanja.com/top-50/page/';
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