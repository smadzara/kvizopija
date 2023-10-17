<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kvizopija
 */

?>

<style>

    #timer {
        background-color: white;
        padding: 5px 10px; /* Dodaje prostor oko teksta unutar timera */
        border-radius: 10px; /* Zaobljeni kutovi */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Dodaje malu sjenu za dodatni vizualni efekt */
    }

    /* Postavlja checkbox i label inline */
    .question-block input[type="checkbox"],
    .question-block label[for^="check_answer_"] {
        display: inline-block;
        vertical-align: middle;
    }

    /* Smanjuje razmak između polja za unos i checkbox-a */
    input[name^="user_answer_"] {
        margin-bottom: 10px;
    }

    input[type="checkbox"][name^="check_answer_"] {
        margin-top: -10px;
        transform: scale(1.15);
    }

    /* Postavlja veličinu fonta za tekst uz checkbox na 14px */
        .question-block label[for^="check_answer_"] {
        font-size: 14px;
    }

    /* Stilovi za desktop */
    #resultContent {
        font-size: 18px;
        margin-top: 20px;
    }

    /* Stilovi za mobilni prikaz */
    @media only screen and (max-width: 768px) {
        #resultContent {
            font-size: 14px; /* Manja veličina fonta za mobilne uređaje */
        }

        #resultModal {
            padding-top: 15%; /* Centriranje modala na sredinu ekrana */
        }

        #resultModal > div {
            width: 90%; /* Širina modala za mobilne uređaje */
            margin: 0 auto; /* Centriranje modala */
        }
    }
</style>

<section class="container" style="background-color: #e7f3f1;">
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
    </div>
</section>

<div id="timer" style="position: fixed; top: 70px; right: 30px; font-size: 30px; color: black; z-index: 1000;"></div>

<?php

$player_name = get_transient('player_name');
if ($player_name) {
    echo "<h2>Dobrodošao, " . esc_html($player_name) . "!</h2>";
    delete_transient('player_name');
}

$questions_and_answers = get_transient('questions_and_answers');
if ($questions_and_answers) {
    echo '<form id="quizForm">';
    foreach ($questions_and_answers as $index => $qa) {
        echo '<div class="question-block">';
        echo "<label>Pitanje " . ($index + 1) . ": " . $qa['question'] . "</label><br>";
        
        // Polje za upis odgovora
        echo '<input type="text" name="user_answer_' . $index . '" placeholder="Upišite odgovor"><br>';
        
        // Checkbox za provjeru točnosti odgovora
        echo '<input type="checkbox" id="check_answer_' . $index . '" name="check_answer_' . $index . '">';
        echo '<label for="check_answer_' . $index . '"> Provjeri točnost odgovora</label>';
        
        // Skriveni div s pravim odgovorom
        echo '<div class="correct-answer" style="display:none; color: #bb00ff; font-size: 16px; font-weight: 600;">Točan odgovor: ' . esc_html($qa['answer']) . '</div>';
        
        echo '</div><br>';
    }
    echo '<input type="button" value="Prikaži odgovore" onclick="showCorrectAnswers()">';
    echo '<input type="button" value="Pošalji odgovore" onclick="checkAnswers()">';
    echo '</form>';
    delete_transient('questions_and_answers');
}

?>

<!-- Modal za prikazivanje rezultata-->
<div id="resultModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1001;">
    <div style="background-color: #fff; padding: 20px; width: 60%; margin: 10% auto; box-shadow: 0 0 10px rgba(0,0,0,0.3);">
        <h2>Rezultati</h2>
        <div id="resultContent"></div>
        <button onclick="closeModal()" style="margin-top: 20px;">Zatvori</button>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
function showCorrectAnswers() {
    var correctAnswers = document.querySelectorAll('.correct-answer');
    correctAnswers.forEach(function(answerDiv) {
        answerDiv.style.display = 'block';
    });
}

var totalQuestions = document.querySelectorAll('.question-block').length;

function checkAnswers() {
    var playerName = "<?php echo esc_js($player_name); ?>";
    var questions = document.querySelectorAll('.question-block');
    var correctCount = 0;

    questions.forEach(function(question, index) {
        var checkAnswer = question.querySelector('input[name="check_answer_' + index + '"]').checked;

        if (checkAnswer) {
            correctCount++;
        }
    });

    var successPercentage = (correctCount / totalQuestions) * 100;
    successPercentage = Math.round(successPercentage * 10) / 10;

    var elapsedTime = totalQuestions * 25 - totalTime;
    var elapsedMinutes = Math.floor(elapsedTime / 60);
    var elapsedSeconds = elapsedTime % 60;

    // Prikazivanje rezultata
    var resultContent = document.getElementById('resultContent');
    resultContent.innerHTML = "Igrač: " + playerName + "<br>" +
                              "Broj postavljenih pitanja: " + totalQuestions + "<br>" +
                              "Broj točnih odgovora: " + correctCount + "<br>" +
                              "Vrijeme za odgovor: " + elapsedMinutes + ":" + (elapsedSeconds < 10 ? "0" : "") + elapsedSeconds +
                              "<br>Postotak uspješnosti: " + Number(successPercentage) + "%";
    document.getElementById('resultModal').style.display = 'block';

    // Sprema rezultate u bazu
    jQuery.ajax({
        url: '/kvizopija/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
            action: 'save_quiz_results',
            player_name: playerName,
            total_questions: totalQuestions,
            correct_answers: correctCount,
            percentage: successPercentage,
            time_taken: elapsedMinutes + ":" + elapsedSeconds
        },
        success: function(response) {
            // Ovdje možete dodati kod koji će se izvršiti nakon što se rezultati uspješno spreme
        }
    });
}

    function closeModal() {
        document.getElementById('resultModal').style.display = 'none';
    }

//Timer

var totalQuestions = document.querySelectorAll('.question-block').length;
var totalTime = totalQuestions * 25; // 25 sekundi za svako pitanje
var timerInterval;

function startTimer() {
    timerInterval = setInterval(function() {
        var minutes = Math.floor(totalTime / 60);
        var seconds = totalTime % 60;
        document.getElementById('timer').textContent = minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
        totalTime--;

        if (totalTime < 0) {
            clearInterval(timerInterval);
            alert('Vrijeme je isteklo!');
        }
    }, 1000);
}

function stopTimer() {
    clearInterval(timerInterval);
}

// Pokreni timer kada se stranica učita
window.onload = startTimer;

// Zaustavitimer kada korisnik klikne na gumb "Prikaži odgovore"
document.querySelector('input[value="Prikaži odgovore"]').addEventListener('click', stopTimer);

</script>