<?php
function afficherEtudiantAvecLiens($etudiant, $idUser, $role) {
	
	$etudient_id = $_SESSION['idEtudiant'];

	echo "<h2>Informations sur l'étudiant : </h2>";
	foreach ($etudiant as $etu) {
		if($etu['date_h'] > date("Y-m-d H:i:s")) {
			?>
			<h2>A Venir</h2>
			<table border='1'> 
			<tr><th>Nom</th><td><?=$etu['nom']?></td></tr>
			<tr><th>Prénom</th><td><?=$etu['prenom']?></td></tr>
			<tr><th>Entreprise</th><td><?=$etu['entreprise']?></td></tr>
			<tr><th>Maitre Stage</th><td><?=$etu['maitreStage']?></td></tr>
			<tr><th>Sujet</th><td><?=$etu['sujet']?></td></tr>
			<tr><th>Date soutenance</th><td><?=$etu['date_h']?></td></tr>
			<tr><th>Statut</th><td><?=$etu['Statut']?></td></tr>

			<tr><th>Salle</th><td><?=$etu['salle']?></td></tr>
			</table>"

			<?php
		}
		else {
			?>
			<h2>Passer</h2>
			<table border='1'>
			<tr><th>Nom</th><td><?=$etu['nom']?></td></tr>
			<tr><th>Prénom</th><td><?=$etu['prenom']?></td></tr>
			<tr><th>Entreprise</th><td><?=$etu['entreprise']?></td></tr>
			<tr><th>Maitre Stage</th><td><?=$etu['maitreStage']?></td></tr>
			<tr><th>Sujet</th><td><?=$etu['sujet']?></td></tr>
			<tr><th>Date soutenance</th><td><?=$etu['date_h']?></td></tr>
			<tr><th>Statut</th><td><?=$etu['Statut']?></td></tr>

			<tr><th>Salle</th><td><?=$etu['salle']?></td></tr>
			</table>

			<?php
		}
		


?>
    <h3>Actions disponibles pour <?=$role?></h3>
	<ul>
	<li><a href='../Page C/index.php?nature=portfolio'>Saisir/Consulter les grilles de Portfolio</a></li>
	<?php if (!empty($etu['but3sinon2']) && $etu['but3sinon2']): ?>
		<li><a href='../Page C/index.php?nature=anglais'>Saisir/Consulter les grilles d'Anglais</a></li>
	<?php endif; ?>
	<li><a href='../Page C/index.php?nature=soutenance'>Saisir/Consulter les grilles de Soutenance</a></li>
	<li><a href='../Page C/index.php?nature=rapport'>Saisir/Consulter les grilles de Rapport</a></li>
	<li><a href='../Page C/index.php?nature=stage'>Saisir/Consulter les grilles de Stage</a></li>
    
	</ul>

	<?php

	}
}



