<?php
// index.php (version de test avec faux id par défaut)

// charge la configuration / modèle / contrôleur
require_once 'config.php';
require_once 'grilleController.php';

// Si l'application gère une session d'authentification, on pourrait préférer $_SESSION.
// Ici on supporte GET pour tester facilement depuis l'URL.
$idUser     = isset($_GET['idUser']) ? (int)$_GET['idUser'] : null;
$idEtudiant = isset($_GET['idEtudiant']) ? (int)$_GET['idEtudiant'] : null;

// Valeurs de test par défaut (à adapter si besoin)
$DEFAULT_USER_ID     = 1; // faux enseignant / secrétaire
$DEFAULT_ETUDIANT_ID = 1; // faux étudiant

// Utilise les valeurs par défaut si les paramètres manquent
if (!$idUser) {
    $idUser = $DEFAULT_USER_ID;
}
if (!$idEtudiant) {
    $idEtudiant = $DEFAULT_ETUDIANT_ID;
}

// Affichage informatif pour le debug / test
echo "<p style='background:#f0f0f0;padding:8px;border:1px solid #ddd'>
        Mode TEST : idUser = <strong>{$idUser}</strong>, idEtudiant = <strong>{$idEtudiant}</strong>.
      </p>";

// Appelle la fonction du contrôleur qui affiche la page pour cet étudiant
// (selon ta structure, cette fonction doit exister dans grilleController.php)
afficherPageEtudiant($idUser, $idEtudiant);

// Pour tester d'autres cas, ouvre : index.php?idUser=2&idEtudiant=3
