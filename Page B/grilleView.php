<?php
function afficherEtudiantAvecLiens($etudiant, $idUser, $role) {
    echo "<h2>Informations sur l'étudiant</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nom</th><td>{$etudiant['nom']}</td></tr>";
    echo "<tr><th>Prénom</th><td>{$etudiant['prenom']}</td></tr>";
    echo "<tr><th>Entreprise</th><td>{$etudiant['entreprise']}</td></tr>";
    echo "<tr><th>Sujet</th><td>{$etudiant['sujet']}</td></tr>";
    echo "<tr><th>Date soutenance</th><td>{$etudiant['date_h']}</td></tr>";
    echo "<tr><th>Salle</th><td>{$etudiant['salle']}</td></tr>";
    echo "</table>";

    echo "<h3>Actions disponibles pour $role</h3>";
    echo "<ul>";
    echo "<li><a href='pageSuivante.php?action=portfolio&idUser=$idUser&idEtudiant={$etudiant['IdEtudiant']}'>Saisir/Consulter la grille Portfolio</a></li>";
    echo "<li><a href='pageSuivante.php?action=soutenance&idUser=$idUser&idEtudiant={$etudiant['IdEtudiant']}'>Saisir/Consulter la grille Soutenance</a></li>";
    echo "<li><a href='pageSuivante.php?action=rapport&idUser=$idUser&idEtudiant={$etudiant['IdEtudiant']}'>Saisir/Consulter la grille Rapport</a></li>";
    echo "<li><a href='pageSuivante.php?action=stage&idUser=$idUser&idEtudiant={$etudiant['IdEtudiant']}'>Saisir/Consulter la grille Stage</a></li>";
    echo "</ul>";
}