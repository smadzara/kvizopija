<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package pkp
 */

$player_name = get_transient( 'player_name' );
if ( $player_name ) {
	delete_transient( 'player_name' );
}

$questions_and_answers = get_transient( 'questions_and_answers' );
$has_quiz_data         = is_array( $questions_and_answers ) && ! empty( $questions_and_answers );
?>

<style>
	#timer {
		background-color: #ffffff;
		padding: 5px 10px;
		border-radius: 10px;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		position: fixed;
		top: 70px;
		right: 30px;
		font-size: 30px;
		color: #000000;
		z-index: 1000;
	}

	.questions-list--quizapp-play {
		margin-top: 12px;
	}

	.questions-list--quizapp-play .question-block {
		margin: 0;
	}

	.questions-list--quizapp-play .question-play-label {
		display: block;
		margin: 0;
		font-size: 1.9rem;
		line-height: 1.45;
	}

	.questions-list--quizapp-play input[name^="user_answer_"] {
		margin: 10px 0 12px;
	}

	.questions-list--quizapp-play .question-play-check {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		font-size: 1.4rem;
	}

	.questions-list--quizapp-play input[type="checkbox"][name^="check_answer_"] {
		transform: scale(1.15);
		margin: 0;
	}

	.questions-list--quizapp-play .correct-answer {
		display: none;
		color: #753bbd;
		font-size: 1.6rem;
		font-weight: 600;
		margin-top: 10px;
	}

	.more-questions--quizapp-play {
		width: 100%;
		padding: 0;
		margin-top: 14px;
	}

	#resultContent {
		font-size: 18px;
		margin-top: 20px;
	}

	@media only screen and (max-width: 768px) {
		#resultContent {
			font-size: 14px;
		}

		#resultModal {
			padding-top: 15%;
		}

		#resultModal > div {
			width: 90%;
			margin: 0 auto;
		}
	}
</style>

<section class="container" style="background-color: #e7f3f1;">
	<div class="content-container">
		<div class="page-title">
			<h1><?php the_title(); ?></h1>
		</div>

		<div class="page-description">
			<p class="page-description-paragraph-text"><?php the_content(); ?></p>
		</div>

		<?php if ( $player_name ) : ?>
			<h2>Dobrodošli, <?= esc_html( $player_name ); ?>!</h2>
		<?php endif; ?>

		<?php if ( $has_quiz_data ) : ?>
			<div id="timer"></div>

			<form id="quizForm">
				<div class="container-questions questions-list--readable questions-list--quizapp-play">
					<?php foreach ( $questions_and_answers as $index => $qa ) : ?>
						<div class="question-block questions-homepage">
							<label class="question-play-label">Pitanje <?= esc_html( $index + 1 ); ?>: <?= esc_html( $qa['question'] ); ?></label>

							<input type="text" name="user_answer_<?= esc_attr( $index ); ?>" placeholder="Upišite odgovor">

							<label class="question-play-check" for="check_answer_<?= esc_attr( $index ); ?>">
								<input type="checkbox" id="check_answer_<?= esc_attr( $index ); ?>" name="check_answer_<?= esc_attr( $index ); ?>">
								<span>Označi točan odgovor</span>
							</label>

							<div class="correct-answer">Tocan odgovor: <?= esc_html( $qa['answer'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="more-questions more-questions--random40 more-questions--quizapp-play">
					<input id="showCorrectAnswersButton" class="homepage-button random40-action-button" type="button" value="Prikaži odgovore" onclick="showCorrectAnswers()">
					<input class="homepage-button random40-action-button" type="button" value="Pošalji odgovore" onclick="checkAnswers()">
				</div>
			</form>

			<?php delete_transient( 'questions_and_answers' ); ?>
		<?php endif; ?>
	</div>
</section>

<!-- Modal za prikazivanje rezultata -->
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
var totalTime = totalQuestions * 25;
var timerInterval;

function checkAnswers() {
	var playerName = <?php echo wp_json_encode( $player_name ? $player_name : '' ); ?>;
	var questions = document.querySelectorAll('.question-block');
	var correctCount = 0;

	questions.forEach(function(question, index) {
		var checkAnswer = question.querySelector('input[name=\"check_answer_' + index + '\"]').checked;
		if (checkAnswer) {
			correctCount++;
		}
	});

	var successPercentage = totalQuestions > 0 ? (correctCount / totalQuestions) * 100 : 0;
	successPercentage = Math.round(successPercentage * 10) / 10;

	var elapsedTime = totalQuestions * 25 - totalTime;
	var elapsedMinutes = Math.floor(elapsedTime / 60);
	var elapsedSeconds = elapsedTime % 60;

	var resultContent = document.getElementById('resultContent');
	resultContent.innerHTML = 'Igrac: ' + playerName + '<br>' +
		'Broj postavljenih pitanja: ' + totalQuestions + '<br>' +
		'Broj tocnih odgovora: ' + correctCount + '<br>' +
		'Vrijeme za odgovor: ' + elapsedMinutes + ':' + (elapsedSeconds < 10 ? '0' : '') + elapsedSeconds +
		'<br>Postotak uspjesnosti: ' + Number(successPercentage) + '%';
	document.getElementById('resultModal').style.display = 'block';

	jQuery.ajax({
		url: <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
		type: 'POST',
		data: {
			action: 'save_quiz_results',
			player_name: playerName,
			total_questions: totalQuestions,
			correct_answers: correctCount,
			percentage: successPercentage,
			time_taken: elapsedMinutes + ':' + elapsedSeconds
		}
	});
}

function closeModal() {
	document.getElementById('resultModal').style.display = 'none';
}

function startTimer() {
	timerInterval = setInterval(function() {
		var minutes = Math.floor(totalTime / 60);
		var seconds = totalTime % 60;
		document.getElementById('timer').textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
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

if (totalQuestions > 0) {
	window.addEventListener('load', startTimer);
	var showAnswersButton = document.getElementById('showCorrectAnswersButton');
	if (showAnswersButton) {
		showAnswersButton.addEventListener('click', stopTimer);
	}
}
</script>
