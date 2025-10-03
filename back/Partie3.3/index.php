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
        <h1>Gestion des Évaluations - IUT</h1>

        <?php if (!empty($donnees['messages'])): ?>
            <div>
                <?php foreach ($donnees['messages'] as $message): ?>
                    <div class="<?php echo htmlspecialchars($message['type']); ?>">
                        <?php echo htmlspecialchars($message['text']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <button type="submit" name="remonter_tout">
                Remonter tous les élèves
            </button>
        </form>

        <h2>Étudiants BUT2 prêts à la remontée</h2>
        <?php if (!empty($donnees['etudiantsBUT2'])): ?>
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
                                    <td><?php echo htmlspecialchars($etudiant['statut_stage']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></td>
                                    <td>
                                        <a href="?action=remonter&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=0">
                                            Remonter
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
        <?php else: ?>
            <p>Aucun étudiant BUT2 prêt.</p>
        <?php endif; ?>

        <h2>Étudiants BUT3 prêts à la remontée</h2>
        <?php if (!empty($donnees['etudiantsBUT3'])): ?>
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
                                    <td><?php echo htmlspecialchars($etudiant['statut_stage']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_anglais']); ?></td>
                                    <td>
                                        <a href="?action=remonter&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=1">Remonter</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
        <?php else: ?>
            <p>Aucun étudiant BUT3 prêt.</p>
        <?php endif; ?>

        <h2>Étudiants en retard (soutenance passée, statut SAISIE)</h2>
        <?php if (!empty($donnees['etudiantsNonBloques'])): ?>
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
                                    <td><?php echo htmlspecialchars($etudiant['statut_stage']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_anglais']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['date_soutenance']); ?></td>
                                    <td>
                                        <a href="?action=rappel&id=<?php echo $etudiant['IdEtudiant']; ?>">
                                            Envoyer mail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
        <?php else: ?>
            <p>Aucun étudiant en retard.</p>
        <?php endif; ?>

        <h2>Étudiants BUT2 déjà remontés</h2>
        <?php if (!empty($donnees['etudiantsRemonteeBUT2'])): ?>
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
                                    <td><?php echo htmlspecialchars($etudiant['statut_stage']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></td>
                                    <td>
                                        <a href="?action=bloquer&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=0">
                                            Bloquer
                                        </a>
                                        <a href="?action=autoriser&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=0">
                                            Autoriser saisie
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
        <?php else: ?>
            <p>Aucun étudiant BUT2 remonté.</p>
        <?php endif; ?>

        <h2>Étudiants BUT3 déjà remontés</h2>
        <?php if (!empty($donnees['etudiantsRemonteeBUT3'])): ?>
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
                                    <td><?php echo htmlspecialchars($etudiant['statut_stage']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_portfolio']); ?></td>
                                    <td><?php echo htmlspecialchars($etudiant['statut_anglais']); ?></td>
                                    <td>
                                        <a href="?action=bloquer&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=1">
                                            Bloquer
                                        </a>
                                        <a href="?action=autoriser&id=<?php echo $etudiant['IdEtudiant']; ?>&but3=1">
                                            Autoriser saisie
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
        <?php else: ?>
            <p>Aucun étudiant BUT3 remonté.</p>
        <?php endif; ?>

        <h2>Export des données</h2>
        
        <h3>Téléchargement direct</h3>
        <form method="post">
            <button type="submit" name="export_csv" value="but2">
                Exporter BUT2 en CSV
            </button>
            <button type="submit" name="export_csv" value="but3">
                Exporter BUT3 en CSV
            </button>
        </form>

        <h3>Envoi par mail</h3>
        <form method="post">
            <button type="submit" name="export_csv_mail" value="but2">Exporter BUT2 et envoyer par mail</button>
            <button type="submit" name="export_csv_mail" value="but3">Exporter BUT3 et envoyer par mail</button>
        </form>
    </div>
</body>
</html>