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

        <div class="category-container">
            <?php foreach ( $questions_terms as $questions_term ) : ?>
            <a href="<?= get_term_link($questions_term->slug, $questions_taxonomy); ?>">
                <div class="category-box">
                    <h2><?= $questions_term->name; ?></h2>
                    <p class="number-of-questions">
                        Broj pitanja: <span class="accent"><?= $questions_term->count; ?></span>
                    </p>
                    <p class="refresh-date">
                        Zadnje osvježavanje: <span
                            class="accent"><?php echo (get_the_date( 'j. n. Y.', $questions_term->ID, $questions_taxonomy )) ?></span>
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="container-questions">
            <h2>Zadnje objavljena pitanja</h2>
            <div class="questions-homepage">
                <p class="question-category"><a href="#">Naziv kategorije</a></p>
                <p class="question-date">Objavljeno: <span class="question-accent">23.12.2022.</span></p>
                <p class="question-author">Autor: <a href="https://kvizopija.com" target="_blank">kvizopija.com</a></p>
                <p>Ovdje ide neko pitanje, pa sad pišem tekst koji će glumiti neko pitanje iz kviza, a kao i u gornjem
                    slučaju, trebat će mi gomila teksta da to testiram jer nikad ne znaš kakva će mudra pitanja smisliti
                    naš drugar Sale Glijaković, koji je inače, najbolji smišljavač pitanja kako u RH tako i u vaseloj
                    vaseljeni.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category"><a href="#">Naziv kategorije</a></p>
                <p class="question-date">Objavljeno: <span class="question-accent">23.12.2022.</span></p>
                <p class="question-author">Autor: <a href="https://kvizopija.com" target="_blank">kvizopija.com</a></p>
                <p>Ovdje ide neko pitanje, pa sad pišem tekst koji će glumiti neko pitanje iz kviza, a kao i u gornjem
                    slučaju, trebat će mi gomila teksta da to testiram jer nikad ne znaš kakva će mudra pitanja smisliti
                    naš drugar Sale Glijaković, koji je inače, najbolji smišljavač pitanja kako u RH tako i u vaseloj
                    vaseljeni.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category"><a href="#">Naziv kategorije</a></p>
                <p class="question-date">Objavljeno: <span class="question-accent">23.12.2022.</span></p>
                <p class="question-author">Autor: <a href="https://kvizopija.com" target="_blank">kvizopija.com</a></p>
                <p>Ovdje ide neko pitanje, pa sad pišem tekst koji će glumiti neko pitanje iz kviza, a kao i u gornjem
                    slučaju, trebat će mi gomila teksta da to testiram jer nikad ne znaš kakva će mudra pitanja smisliti
                    naš drugar Sale Glijaković, koji je inače, najbolji smišljavač pitanja kako u RH tako i u vaseloj
                    vaseljeni.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: <span class="question-accent">23.12.2022.</span></p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>Ovdje ide neko pitanje, pa sad pišem tekst koji će glumiti neko pitanje iz kviza, a kao i u gornjem
                    slučaju, trebat će mi gomila teksta da to testiram jer nikad ne znaš kakva će mudra pitanja smisliti
                    naš drugar Sale Glijaković, koji je inače, najbolji smišljavač pitanja kako u RH tako i u vaseloj
                    vaseljeni.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: <span class="question-accent">23.12.2022.</span>.</p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus id odio convallis, tempus arcu
                    vel, laoreet mi. Sed dignissim auctor est eu tristique. Duis cursus velit vel arcu commodo iaculis.
                    Morbi ac malesuada magna. Vivamus sed eros vel risus malesuada posuere nec vel neque. Duis
                    pellentesque a quam et fringilla. Vivamus tempor eros ullamcorper ex hendrerit imperdiet. Nam ac
                    varius quam. In dignissim viverra ligula, sed sagittis massa egestas id. In eget fermentum risus.
                    Phasellus finibus molestie diam eget rhoncus. Fusce tempus mattis sapien ut.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: <span class="question-accent">23.12.2022.</span></p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>Ovdje ide neko pitanje, pa sad pišem tekst koji će glumiti neko pitanje iz kviza, a kao i u gornjem
                    slučaju, trebat će mi gomila teksta da to testiram jer nikad ne znaš kakva će mudra pitanja smisliti
                    naš drugar Sale Glijaković, koji je inače, najbolji smišljavač pitanja kako u RH tako i u vaseloj
                    vaseljeni.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: 23.12.2022.</p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>Ovdje ide neko pitanje, pa sad pišem tekst koji će glumiti neko pitanje iz kviza, a kao i u gornjem
                    slučaju, trebat će mi gomila teksta da to testiram jer nikad ne znaš kakva će mudra pitanja smisliti
                    naš drugar Sale Glijaković, koji je inače, najbolji smišljavač pitanja kako u RH tako i u vaseloj
                    vaseljeni.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: 23.12.2022.</p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc blandit placerat laoreet. Nam ornare
                    velit eget metus facilisis vestibulum. Ut ac ipsum nec ligula laoreet consectetur mattis ut ipsum.
                    Sed purus nulla, tincidunt vel tincidunt sed, volutpat vel augue.
                    Nulla vulputate rhoncus ipsum sit amet mattis. Nulla facilisi. Phasellus ut tincidunt elit. Cras
                    vitae nisi eu augue egestas lobortis a egestas orci. Fusce dolor justo, facilisis vitae augue sed,
                    viverra porttitor libero. Sed sodales eros in faucibus bibendum.
                    Ut suscipit arcu scelerisque justo rhoncus, maximus rutrum est varius. Nullam scelerisque eros nec
                    tellus varius, vel consectetur magna gravida. Aliquam eu ligula mi.
                    Suspendisse congue felis at dapibus egestas. Phasellus fringilla eu elit non maximus. Morbi laoreet
                    egestas tortor porttitor lobortis. Integer quis rhoncus nunc. Integer.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: 23.12.2022.</p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc blandit placerat laoreet. Nam ornare
                    velit eget metus facilisis vestibulum. Ut ac ipsum nec ligula laoreet consectetur mattis ut ipsum.
                    Sed purus nulla, tincidunt vel tincidunt sed, volutpat vel augue.
                    Nulla vulputate rhoncus ipsum sit amet mattis. Nulla facilisi. Phasellus ut tincidunt elit. Cras
                    vitae nisi eu augue egestas lobortis a egestas orci. Fusce dolor justo, facilisis vitae augue sed,
                    viverra porttitor libero. Sed sodales eros in faucibus bibendum.
                    Ut suscipit arcu scelerisque justo rhoncus, maximus rutrum est varius. Nullam scelerisque eros nec
                    tellus varius, vel consectetur magna gravida. Aliquam eu ligula mi.
                    Suspendisse congue felis at dapibus egestas. Phasellus fringilla eu elit non maximus. Morbi laoreet
                    egestas tortor porttitor lobortis. Integer quis rhoncus nunc. Integer.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: 23.12.2022.</p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>Ovdje ide neko pitanje, pa sad pišem tekst koji će glumiti neko pitanje iz kviza, a kao i u gornjem
                    slučaju, trebat će mi gomila teksta da to testiram jer nikad ne znaš kakva će mudra pitanja smisliti
                    naš drugar Sale Glijaković, koji je inače, najbolji smišljavač pitanja kako u RH tako i u vaseloj
                    vaseljeni.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
            <div class="questions-homepage">
                <p class="question-category">Naziv kategorije</p>
                <p class="question-date">Objavljeno: 23.12.2022.</p>
                <p class="question-author">Autor: kvizopija.com</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus id odio convallis, tempus arcu
                    vel, laoreet mi. Sed dignissim auctor est eu tristique. Duis cursus velit vel arcu commodo iaculis.
                    Morbi ac malesuada magna. Vivamus sed eros vel risus malesuada posuere nec vel neque. Duis
                    pellentesque a quam et fringilla. Vivamus tempor eros ullamcorper ex hendrerit imperdiet. Nam ac
                    varius quam. In dignissim viverra ligula, sed sagittis massa egestas id. In eget fermentum risus.
                    Phasellus finibus molestie diam eget rhoncus. Fusce tempus mattis sapien ut.</p>
                <p class="answer">Odgovor na pitanje</p>
            </div>
        </div>

    </div>




</section>

<!-- END Kvizopija Template -->