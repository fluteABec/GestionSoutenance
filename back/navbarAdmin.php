<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&display=swap" rel="stylesheet">
<nav class="navbar">
    <div class="brand">
        <span class="logo"></span>
        <span>IUT</span>
    </div>
    <div class="nav-menu">
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], '3_1_natan.php')!==false) echo ' selected'; ?>" href="Partie3.1/3_1_natan.php">Tâches enseignants</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'Partie3.3')!==false) echo ' selected'; ?>" href="Partie3.3/index.php">Évaluations IUT</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'Partie3.4')!==false) echo ' selected'; ?>" href="Partie3.4/index.php">Diffusion résultats</a>
        <a class="nav-item<?php if(strpos($_SERVER['PHP_SELF'], 'mainAdministration.php')!==false) echo ' selected'; ?>" href="../mainAdministration.php">Administration</a>
    </div>
    <div class="nav-actions">
        <input type="text" id="searchInput" class="nav-search" placeholder="🔍 Rechercher un étudiant...">
    </div>
</nav>