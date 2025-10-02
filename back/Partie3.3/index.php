<?php
// Inclusion du contrôleur
require_once 'controllers/EvaluationController.php';

// Initialisation du contrôleur et traitement des requêtes
$controller = new EvaluationController();
$donnees = $controller->traiterRequetes();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Évaluations - IUT</title>
    <link rel="stylesheet" href="../../stylee.css">
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="admin-block">
        <h1 class="page-title">Gestion des Évaluations - IUT</h1>

        <!-- Affichage des messages -->
        <?php if (!empty($donnees['messages'])): ?>
            <div class="messages-container">
                <?php foreach ($donnees['messages'] as $message): ?>
                    <div class="message message-<?php echo htmlspecialchars($message['type']); ?>">
                        <?php echo htmlspecialchars($message['text']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Bouton pour remonter tout -->
        <div class="action-buttons">
            <form method="post" class="inline-form">
                <button type="submit" name="remonter_tout" class="btn btn-primary">
                    Remonter tous les élèves
                </button>
            </form>
        </div>

        <!-- Section BUT2 prêts à la remontée -->
        <section class="students-section">
            <h2 class="section-title">Étudiants BUT2 prêts à la remontée</h2>
            <?php if (!empty($donnees['etudiantsBUT2'])): ?>
                <div class="table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>ID</th>
                                <th>Stage</th>
                                <th>Portfolio</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donnees['etudiantsBUT2'] as $etudiant): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['IdEtudiant']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_stage']); ?>"><?php echo htmlspecialchars($etudiant['statut_stage']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_portfolio']); ?>"><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></span></td>
                                    <td>
                                        <a href="?action=remonter&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=0" class="btn btn-action">
                                            Remonter
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">Aucun étudiant BUT2 prêt.</p>
            <?php endif; ?>
        </section>

        <!-- Section BUT3 prêts à la remontée -->
        <section class="students-section">
            <h2 class="section-title">Étudiants BUT3 prêts à la remontée</h2>
            <?php if (!empty($donnees['etudiantsBUT3'])): ?>
                <div class="table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>ID</th>
                                <th>Stage</th>
                                <th>Portfolio</th>
                                <th>Anglais</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donnees['etudiantsBUT3'] as $etudiant): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['IdEtudiant']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_stage']); ?>"><?php echo htmlspecialchars($etudiant['statut_stage']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_portfolio']); ?>"><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_anglais']); ?>"><?php echo htmlspecialchars($etudiant['statut_anglais']); ?></span></td>
                                    <td>
                                        <a href="?action=remonter&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=1" class="btn btn-action">
                                            Remonter
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">Aucun étudiant BUT3 prêt.</p>
            <?php endif; ?>
        </section>

        <!-- Section étudiants en retard -->
        <section class="students-section">
            <h2 class="section-title">Étudiants en retard (soutenance passée, statut SAISIE)</h2>
            <?php if (!empty($donnees['etudiantsNonBloques'])): ?>
                <div class="table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>ID</th>
                                <th>Stage</th>
                                <th>Portfolio</th>
                                <th>Anglais</th>
                                <th>Date Soutenance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donnees['etudiantsNonBloques'] as $etudiant): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['IdEtudiant']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_stage']); ?>"><?php echo htmlspecialchars($etudiant['statut_stage']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_portfolio']); ?>"><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_anglais']); ?>"><?php echo htmlspecialchars($etudiant['statut_anglais']); ?></span></td>
                                    <td><?php echo htmlspecialchars($etudiant['date_soutenance']); ?></td>
                                    <td>
                                        <a href="?action=rappel&id=<?php echo $etudiant['IdEtudiant']; ?>" class="btn btn-warning">
                                            Envoyer mail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">Aucun étudiant en retard.</p>
            <?php endif; ?>
        </section>

        <!-- Section BUT2 déjà remontés -->
        <section class="students-section">
            <h2 class="section-title">Étudiants BUT2 déjà remontés</h2>
            <?php if (!empty($donnees['etudiantsRemonteeBUT2'])): ?>
                <div class="table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>ID</th>
                                <th>Stage</th>
                                <th>Portfolio</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donnees['etudiantsRemonteeBUT2'] as $etudiant): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['IdEtudiant']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_stage']); ?>"><?php echo htmlspecialchars($etudiant['statut_stage']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_portfolio']); ?>"><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></span></td>
                                    <td class="action-buttons">
                                        <a href="?action=bloquer&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=0" class="btn btn-danger">
                                            Bloquer
                                        </a>
                                        <a href="?action=autoriser&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=0" class="btn btn-success">
                                            Autoriser saisie
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">Aucun étudiant BUT2 remonté.</p>
            <?php endif; ?>
        </section>

        <!-- Section BUT3 déjà remontés -->
        <section class="students-section">
            <h2 class="section-title">Étudiants BUT3 déjà remontés</h2>
            <?php if (!empty($donnees['etudiantsRemonteeBUT3'])): ?>
                <div class="table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>ID</th>
                                <th>Stage</th>
                                <th>Portfolio</th>
                                <th>Anglais</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donnees['etudiantsRemonteeBUT3'] as $etudiant): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['IdEtudiant']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_stage']); ?>"><?php echo htmlspecialchars($etudiant['statut_stage']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_portfolio']); ?>"><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo strtolower($etudiant['statut_anglais']); ?>"><?php echo htmlspecialchars($etudiant['statut_anglais']); ?></span></td>
                                    <td class="action-buttons">
                                        <a href="?action=bloquer&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=1" class="btn btn-danger">
                                            Bloquer
                                        </a>
                                        <a href="?action=autoriser&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=1" class="btn btn-success">
                                            Autoriser saisie
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">Aucun étudiant BUT3 remonté.</p>
            <?php endif; ?>
        </section>

        <!-- Section export -->
        <section class="export-section">
            <h2 class="section-title">Export des données</h2>
            
            <div class="export-buttons">
                <div class="export-group">
                    <h3>Téléchargement direct</h3>
                    <form method="post" class="inline-form">
                        <button type="submit" name="export_csv" value="but2" class="btn btn-secondary">
                            Exporter BUT2 en CSV
                        </button>
                        <button type="submit" name="export_csv" value="but3" class="btn btn-secondary">
                            Exporter BUT3 en CSV
                        </button>
                    </form>
                </div>

                <div class="export-group">
                    <h3>Envoi par mail</h3>
                    <form method="post" class="inline-form">
                        <button type="submit" name="export_csv_mail" value="but2" class="btn btn-info">
                            Exporter BUT2 et envoyer par mail
                        </button>
                        <button type="submit" name="export_csv_mail" value="but3" class="btn btn-info">
                            Exporter BUT3 et envoyer par mail
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>
</html>