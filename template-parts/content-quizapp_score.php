<?php
/**
 * Template part for Quiz App results page content.
 *
 * @package kvizopija
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="container quizapp-results-page">
	<div class="content-container quizapp-results-page__container">
		<div class="page-title">
			<h1><?php the_title(); ?></h1>
		</div>

		<div class="page-description quizapp-results-page__description">
			<div class="page-description-paragraph-text">
				<?php the_content(); ?>
			</div>
		</div>

		<?php
		global $wpdb;

		$date_30_days_ago = wp_date( 'Y-m-d', strtotime( '-30 days', current_time( 'timestamp' ) ) );
		$table_name       = $wpdb->prefix . 'quiz_results';
		$sql              = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE date_recorded >= %s ORDER BY date_recorded DESC",
			$date_30_days_ago
		);
		$results          = $wpdb->get_results( $sql );
		?>

		<div class="quizapp-results-mobile-sort">
			<label for="quizResultsMobileSort">Sortiranje:</label>
			<select id="quizResultsMobileSort" aria-label="Sortiranje rezultata">
				<option value="5_desc" selected>Datum igranja (najnovije)</option>
				<option value="5_asc">Datum igranja (najstarije)</option>
				<option value="3_desc">Postotak (najve&#263;i)</option>
				<option value="3_asc">Postotak (najmanji)</option>
				<option value="4_asc">Vrijeme (najbr&#382;e)</option>
				<option value="4_desc">Vrijeme (najsporije)</option>
			</select>
		</div>

		<div class="quizapp-results-table-wrap">
			<table id="quizResultsTable" class="quizapp-results-table custom-table">
				<thead>
					<tr>
						<th data-sort-col="0" onclick="sortTable(0)" aria-sort="none">Igra&#269; <span class="sort-indicator" aria-hidden="true">&#8597;</span></th>
						<th data-sort-col="1" onclick="sortTable(1)" aria-sort="none">Broj postavljenih pitanja <span class="sort-indicator" aria-hidden="true">&#8597;</span></th>
						<th data-sort-col="2" onclick="sortTable(2)" aria-sort="none">Broj to&#269;nih odgovora <span class="sort-indicator" aria-hidden="true">&#8597;</span></th>
						<th data-sort-col="3" onclick="sortTable(3)" aria-sort="none">Postotak uspje&#353;nosti <span class="sort-indicator" aria-hidden="true">&#8597;</span></th>
						<th data-sort-col="4" onclick="sortTable(4)" aria-sort="none">Vrijeme odgovora m:ss <span class="sort-indicator" aria-hidden="true">&#8597;</span></th>
						<th data-sort-col="5" onclick="sortTable(5)" aria-sort="descending">Datum igranja <span class="sort-indicator" aria-hidden="true">&#9660;</span></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $results ) ) : ?>
						<?php foreach ( $results as $result ) : ?>
							<tr>
								<td data-label="Igra&#269;"><?php echo esc_html( $result->player_name ); ?></td>
								<td data-label="Pitanja"><?php echo (int) $result->total_questions; ?></td>
								<td data-label="To&#269;ni"><?php echo (int) $result->correct_answers; ?></td>
								<td data-label="Postotak"><?php echo esc_html( (float) $result->percentage . '%' ); ?></td>
								<td data-label="Vrijeme">
									<?php
									$time_parts = explode( ':', (string) $result->time_taken );
									$minutes    = isset( $time_parts[0] ) ? (int) $time_parts[0] : 0;
									$seconds    = isset( $time_parts[1] ) ? (int) $time_parts[1] : 0;
									echo esc_html( $minutes . ':' . str_pad( (string) $seconds, 2, '0', STR_PAD_LEFT ) );
									?>
								</td>
								<?php
								$date_timestamp_raw = strtotime( (string) $result->date_recorded );
								$date_timestamp     = $date_timestamp_raw ? (int) $date_timestamp_raw : 0;
								?>
								<td data-label="Datum igranja" data-sort-ts="<?php echo esc_attr( $date_timestamp ); ?>">
									<?php echo $date_timestamp ? esc_html( wp_date( 'd.m.Y., H:i:s', $date_timestamp ) ) : ''; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr class="quizapp-results-table__empty-row">
							<td colspan="6" class="quizapp-results-table__empty">Nema rezultata u zadnjih 30 dana.</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>

<script>
(function () {
	var table = document.getElementById('quizResultsTable');
	var mobileSort = document.getElementById('quizResultsMobileSort');
	if (!table) {
		return;
	}

	var currentSortColumn = 5;
	var currentSortDirection = 'desc';

	function getCellSortValue(cell, columnIndex) {
		if (!cell) {
			return 0;
		}

		var content = (cell.textContent || '').trim();

		if (columnIndex === 1 || columnIndex === 2) {
			return parseInt(content, 10) || 0;
		}

		if (columnIndex === 3) {
			return parseFloat(content.replace('%', '')) || 0;
		}

		if (columnIndex === 4) {
			var timeParts = content.split(':');
			var minutes = parseInt(timeParts[0], 10) || 0;
			var seconds = parseInt(timeParts[1], 10) || 0;
			return (minutes * 60) + seconds;
		}

		if (columnIndex === 5) {
			return parseInt(cell.getAttribute('data-sort-ts') || '0', 10);
		}

		return content.toLowerCase();
	}

	function updateSortIndicators(activeColumn, direction) {
		var headers = table.querySelectorAll('thead th[data-sort-col]');

		headers.forEach(function (header) {
			var indicator = header.querySelector('.sort-indicator');
			var column = parseInt(header.getAttribute('data-sort-col'), 10);
			var isActive = column === activeColumn;

			if (indicator) {
				indicator.textContent = isActive ? (direction === 'asc' ? '▲' : '▼') : '↕';
			}

			header.setAttribute('aria-sort', isActive ? (direction === 'asc' ? 'ascending' : 'descending') : 'none');
		});
	}

	function sortTableBy(columnIndex, direction) {
		var tbody = table.tBodies[0];
		if (!tbody) {
			return;
		}

		var rows = Array.from(tbody.rows).filter(function (row) {
			return !row.classList.contains('quizapp-results-table__empty-row');
		});

		rows.sort(function (rowA, rowB) {
			var xCell = rowA.cells[columnIndex] ? rowA.cells[columnIndex] : null;
			var yCell = rowB.cells[columnIndex] ? rowB.cells[columnIndex] : null;
			var xContent = getCellSortValue(xCell, columnIndex);
			var yContent = getCellSortValue(yCell, columnIndex);

			if (xContent < yContent) {
				return direction === 'asc' ? -1 : 1;
			}
			if (xContent > yContent) {
				return direction === 'asc' ? 1 : -1;
			}
			return 0;
		});

		rows.forEach(function (row) {
			tbody.appendChild(row);
		});

		currentSortColumn = columnIndex;
		currentSortDirection = direction;
		updateSortIndicators(columnIndex, direction);

		if (mobileSort) {
			var selectedValue = String(columnIndex) + '_' + direction;
			var hasOption = Array.from(mobileSort.options).some(function (option) {
				return option.value === selectedValue;
			});

			if (hasOption) {
				mobileSort.value = selectedValue;
			}
		}
	}

	window.sortTable = function (columnIndex) {
		var direction = 'asc';

		if (currentSortColumn === columnIndex) {
			direction = currentSortDirection === 'asc' ? 'desc' : 'asc';
		}

		sortTableBy(columnIndex, direction);
	};

	if (mobileSort) {
		mobileSort.addEventListener('change', function () {
			var raw = (mobileSort.value || '5_desc').split('_');
			var columnIndex = parseInt(raw[0], 10);
			var direction = raw[1] === 'asc' ? 'asc' : 'desc';

			if (isNaN(columnIndex)) {
				return;
			}

			sortTableBy(columnIndex, direction);
		});
	}

	updateSortIndicators(currentSortColumn, currentSortDirection);
})();
</script>
