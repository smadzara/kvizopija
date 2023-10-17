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
    .main {
        background-color: #e7f3f1 !important;
    }

    .custom-table {
        font-size: 18px; /* Povećajte font tablice */
    }

    .custom-table thead {
        background-color: #753bbd; /* Primjer boje za zaglavlje */
        color: white; /* Boja teksta za zaglavlje */
    }

    .custom-table thead th:hover {
    background-color: #0056b3; /* Primjer tamnije nijanse plave boje */
    cursor: pointer; /* Promijenite kursor u pokazivač */
    }

    /* Medij upit za mobilne uređaje */
    @media (max-width: 768px) {
    .custom-table {
        font-size: calc(18px * 0.7); /* Smanjite font za 30% */
        }
    .custom-table td, .custom-table th {
        vertical-align: middle !important; /* Poravnajte sadržaj ćelija okomito na sredini */
    }
    }


</style>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<section style="background-color: #e7f3f1;">
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

    <?php
global $wpdb;
$results = $wpdb->get_results("SELECT * FROM kv_quiz_results ORDER BY date_recorded DESC");
?>
<div class="table-responsive">
    <table class="table table-bordered table-hover custom-table">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Igrač</th>
                <th onclick="sortTable(1)">Broj postavljenih pitanja</th>
                <th onclick="sortTable(2)">Broj točnih odgovora</th>
                <th onclick="sortTable(3)">Postotak uspješnosti</th>
                <th onclick="sortTable(4)">Vrijeme odgovora m:s</th>
                <th onclick="sortTable(5)">Datum igranja</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?php echo esc_html($result->player_name); ?></td>
                    <td><?php echo intval($result->total_questions); ?></td>
                    <td><?php echo intval($result->correct_answers); ?></td>
                    <td><?php echo floatval($result->percentage) . '%'; ?></td>
                    <td><?php echo esc_html($result->time_taken); ?></td>
                    <td><?php echo date('d.m.Y., H:i:s', strtotime($result->date_recorded)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


</section>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script>

function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.querySelector(".table");
    switching = true;
    dir = "asc";

    while (switching) {
        switching = false;
        rows = table.rows;

        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];

            if (x && y) {
                var xContent = x.innerHTML.trim();
                var yContent = y.innerHTML.trim();

                if (n === 1 || n === 2) { // Broj postavljenih pitanja i Broj točnih odgovora
                    xContent = parseInt(xContent, 10);
                    yContent = parseInt(yContent, 10);
                } else if (n === 3) { // Postotak uspješnosti
                    xContent = parseFloat(xContent.replace('%', ''));
                    yContent = parseFloat(yContent.replace('%', ''));
                } else if (n === 4) { // Vrijeme odgovora m:s
                    var xTimeParts = xContent.split(':');
                    var yTimeParts = yContent.split(':');
                    xContent = parseInt(xTimeParts[0], 10) * 60 + parseInt(xTimeParts[1], 10);
                    yContent = parseInt(yTimeParts[0], 10) * 60 + parseInt(yTimeParts[1], 10);
                }

                if (!isNaN(xContent) && !isNaN(yContent)) {
                    if (dir == "asc" ? xContent > yContent : xContent < yContent) {
                        shouldSwitch = true;
                        break;
                    }
                } else if (Date.parse(xContent) && Date.parse(yContent)) {
                    if (dir == "asc" ? new Date(xContent) > new Date(yContent) : new Date(xContent) < new Date(yContent)) {
                        shouldSwitch = true;
                        break;
                    }
                } else {
                    if (dir == "asc" ? xContent.toLowerCase() > yContent.toLowerCase() : xContent.toLowerCase() < yContent.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }
        }

        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}


</script>

