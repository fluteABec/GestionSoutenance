<?php
// headerFront.php
// À inclure en haut de chaque page front
if (isset($enseignantFullName)) {
    $nomProfesseur = $enseignantFullName;
} else {
    // Start session only if not already active to avoid PHP notice
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $nomProfesseur = '';
    if (isset($_SESSION['professeur_nom'])) {
        $nomProfesseur = $_SESSION['professeur_nom'];
    } elseif (isset($_SESSION['nom'])) {
        $nomProfesseur = $_SESSION['nom'];
    }
}
?>

<div class="navbar" style="display: flex; align-items: center; justify-content: space-between; padding: 0 32px; background: var(--navy); height: 64px; border-bottom: 1px solid var(--border); position: fixed; top: 0; left: 0; right: 0; width: 100vw; z-index: 20;">
    <div class="brand" style="flex:0 0 auto;">
        <span class="logo"></span>
    </div>
    <div style="flex:1;display:flex;justify-content:center;align-items:center;">
        <span style="color:#fff; font-weight:700; font-size:1.15rem; white-space:nowrap;"><?php echo htmlspecialchars($nomProfesseur); ?></span>
    </div>
    <div style="flex:0 0 auto;">
        <a class="nav-item logout-button deconnexion-btn" href="../../back/deconnexion.php">Déconnexion</a>
    </div>
</div>

