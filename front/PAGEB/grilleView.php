<?php
function afficherEtudiantAvecLiens($etudiant, $idUser, $role) {
	session_start();
	echo "<h2>Informations sur l'étudiant</h2>";
	foreach ($etudiant as $etu) {
		//$_SESSION['idEtu'] = $etu['idEtu'];
		if($etu['date_h'] > date("Y-m-d H:i:s")) {
			echo "<h2>A Venir</h2>";
			echo "<table border='1'>";
			echo "<tr><th>Nom</th><td>{$etu['nom']}</td></tr>";
			echo "<tr><th>Prénom</th><td>{$etu['prenom']}</td></tr>";
			echo "<tr><th>Entreprise</th><td>{$etu['entreprise']}</td></tr>";
			echo "<tr><th>Maitre Stage</th><td>{$etu['maitreStage']}</td></tr>";
			echo "<tr><th>Sujet</th><td>{$etu['sujet']}</td></tr>";
			echo "<tr><th>Date soutenance</th><td>{$etu['date_h']}</td></tr>";
			echo "<tr><th>Statut</th><td>{$etu['Statut']}</td></tr>";

			echo "<tr><th>Salle</th><td>{$etu['salle']}</td></tr>";
			echo "</table>";
		}
		else {
			echo "<h2>Passer</h2>";
			echo "<table border='1'>";
			echo "<tr><th>Nom</th><td>{$etu['nom']}</td></tr>";
			echo "<tr><th>Prénom</th><td>{$etu['prenom']}</td></tr>";
			echo "<tr><th>Entreprise</th><td>{$etu['entreprise']}</td></tr>";
			echo "<tr><th>Maitre Stage</th><td>{$etu['maitreStage']}</td></tr>";
			echo "<tr><th>Sujet</th><td>{$etu['sujet']}</td></tr>";
			echo "<tr><th>Date soutenance</th><td>{$etu['date_h']}</td></tr>";
			echo "<tr><th>Statut</th><td>{$etu['Statut']}</td></tr>";

			echo "<tr><th>Salle</th><td>{$etu['salle']}</td></tr>";
			echo "</table>";
		}
 

    echo "<h3>Actions disponibles pour $role</h3>";
    echo "<ul>";
    echo "<li><a href='../PAGEC/index.php?<?php echo htmlspecialchars(SID); ?>action=idEtu=idEtu={$etu['idEtu']}'>Saisir/Consulter les grilles</a></li>"; 
	echo "</ul>";
	}
}