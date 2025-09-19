<?php
require_once 'grilleModel.php';
require_once 'grilleView.php';


function afficherPageEtudiant($idUser, $idEtudiant) {
    $etudiant = getInfosEtudiant($idEtudiant);
    $role     = getRoleUtilisateur($idUser, $idEtudiant);

    afficherEtudiantAvecLiens($etudiant, $idUser, $role);
}