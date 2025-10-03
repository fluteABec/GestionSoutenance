<?php

function afficherEtudiantAvecLiens($etudiant, $idUser, $role) {
	$etudient_id = $_SESSION['idEtudiant'];
	echo '<div class="admin-block" style="max-width:900px;width:96%;margin:60px auto 0 auto;box-sizing:border-box;">';
	echo "<h2 class='section-title'>Informations sur l'étudiant :</h2>";
	foreach ($etudiant as $etu) {
		if($etu['date_h'] > date("Y-m-d H:i:s")) {
			echo "<h2 class='section-title' style='color:#2a7;'>À venir</h2>";
		} else {
			echo "<h2 class='section-title' style='color:#888;'>Passé</h2>";
		}
		echo '<div class="table-container" style="max-width:100%;overflow-x:auto;">';
		echo "<table class='styled-table' style='min-width:400px;'>";
		echo "<tr><th>Nom</th><td>" . htmlspecialchars($etu['nom']) . "</td></tr>";
		echo "<tr><th>Prénom</th><td>" . htmlspecialchars($etu['prenom']) . "</td></tr>";
		echo "<tr><th>Entreprise</th><td>" . htmlspecialchars($etu['entreprise']) . "</td></tr>";
		echo "<tr><th>Maitre Stage</th><td>" . htmlspecialchars($etu['maitreStage']) . "</td></tr>";
		echo "<tr><th>Sujet</th><td>" . htmlspecialchars($etu['sujet']) . "</td></tr>";
		echo "<tr><th>Date soutenance</th><td>" . htmlspecialchars($etu['date_h']) . "</td></tr>";
		echo "<tr><th>Statut</th><td>" . htmlspecialchars($etu['Statut']) . "</td></tr>";
		echo "<tr><th>Salle</th><td>" . htmlspecialchars($etu['salle']) . "</td></tr>";
		echo "</table>";
		echo '</div>';

		echo "<h3 style='margin-top:32px;'>Actions disponibles pour " . htmlspecialchars($role) . "</h3>";
		echo "<ul style='margin-bottom:32px;'>";
		echo "<li><a href='../Page C/index.php?nature=portfolio' class='btn'>Saisir/Consulter les grilles de Portfolio</a></li>";
		echo "<li><a href='../Page C/index.php?nature=anglais' class='btn'>Saisir/Consulter les grilles d'Anglais</a></li>";
		echo "<li><a href='../Page C/index.php?nature=soutenance' class='btn'>Saisir/Consulter les grilles de Soutenance</a></li>";
		echo "<li><a href='../Page C/index.php?nature=rapport' class='btn'>Saisir/Consulter les grilles de Rapport</a></li>";
		echo "<li><a href='../Page C/index.php?nature=stage' class='btn'>Saisir/Consulter les grilles de Stage</a></li>";
		echo "</ul>";
	}
	echo '</div>';
}



