<?php
// Fichier permettant de se connecter à la base
mysqli_report(MYSQLI_REPORT_OFF);

$mysqli = @new mysqli("localhost", "root", "", "EvaluationStages");

if ( $mysqli->connect_errno ) {
    echo "Impossible de se connecter à MySQL: errNum=" . $mysqli->connect_errno .
    " errDesc=". $mysqli -> connect_error;
    exit();
}




?>