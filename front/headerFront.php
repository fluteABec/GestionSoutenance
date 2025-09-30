<?php
// headerFront.php
// À inclure en haut de chaque page front
if (isset($enseignantFullName)) {
    $nomProfesseur = $enseignantFullName;
} else {
    session_start();
    $nomProfesseur = '';
    if (isset($_SESSION['professeur_nom'])) {
        $nomProfesseur = $_SESSION['professeur_nom'];
    } elseif (isset($_SESSION['nom'])) {
        $nomProfesseur = $_SESSION['nom'];
    }
}
?>
<div class="navbar" style="display: flex; align-items: center; justify-content: space-between; padding: 0 32px; background: var(--navy); height: 64px; border-bottom: 1px solid var(--border); position: fixed; top: 0; left: 0; right: 0; width: 100vw; z-index: 20;">
    <div class="brand" style="display: flex; align-items: center; gap: 10px;">
        <span class="logo"></span>
    </div>
    <form class="nav-search" method="get" action="/projet_sql/front/front_office.php" style="flex: 1; display: flex; justify-content: center; margin: 0;">
        <input type="text" name="search" placeholder="Rechercher..."/>
        <button type="submit" style="display:none"></button>
    </form>
    <div style="color:#fff; font-weight:600; font-size:1.1rem; margin-left:32px; white-space:nowrap;">
        <?php echo htmlspecialchars($nomProfesseur); ?>
    </div>
</div>
