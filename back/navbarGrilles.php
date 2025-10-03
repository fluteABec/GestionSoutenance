<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
<nav class="navbar" style="width:100%;">
    <div class="brand">
        <span class="logo"></span>
    </div>
    <div class="nav-menu" style="flex:1;display:flex;flex-wrap:wrap;">
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], '3_1_natan.php')!==false) echo ' selected'; ?>" href="../../Partie3.1/3_1_natan.php">Tâches enseignants</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'Partie3.3')!==false) echo ' selected'; ?>" href="../../Partie3.3/index.php">Évaluations IUT</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'Partie3.4')!==false) echo ' selected'; ?>" href="../../Partie3.4/index.php">Diffusion résultats</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'Partie3.5')!==false) echo ' selected'; ?>" href="../../Partie3.5/Partie3.5.2/Grille.php">Gestion Grille</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'mainAdministration.php')!==false) echo ' selected'; ?>" href="../../mainAdministration.php">Administration</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'Partie3.6')!==false) echo ' selected'; ?>" href="../../Partie3.5.3/gestion.php">Gestion des ressources</a>
        <a class="nav-item logout-button" href="../../deconnexion.php">Déconnexion</a>
    </div>
</nav>
