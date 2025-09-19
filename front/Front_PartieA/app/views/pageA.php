<?php
// Fonction d'échappement HTML
function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Vérifier que les variables nécessaires sont définies
if (!isset($enseignantFullName) || !isset($enseignant)) {
    die('Erreur: Données manquantes pour afficher la page.');
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Page A — Soutenances</title>
  <link rel="stylesheet" href="../public/stylee.css">
  <style>
    .filters {
      background: #f8f9fa;
      padding: 1rem;
      border-radius: 4px;
      margin-bottom: 1.5rem;
      border: 1px solid #dee2e6;
    }
    
    .filters h3 {
      margin: 0 0 0.75rem 0;
      font-size: 1rem;
      color: #495057;
    }
    
    .filter-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    
    .filter-btn {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      background: white;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .filter-btn:hover {
      background: #e9ecef;
    }
    
    .filter-btn.active {
      background: #4a6baf;
      color: white;
      border-color: #3a5a9a;
    }
    
    .filter-btn.active[data-filter="tuteur"] {
      background: #e1f5fe;
      color: #01579b;
    }
    
    .filter-btn.active[data-filter="second"] {
      background: #e8f5e9;
      color: #1b5e20;
    }
    
    .filter-btn.active[data-filter="confidentiel"] {
      background: #fff3e0;
      color: #e65100;
    }
    
    .filter-btn.active[data-filter="none"] {
      background: #f5f5f5;
      color: #424242;
    }
    
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    
    .sort-buttons {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    
    .sort-btn {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      background: white;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .sort-btn:hover {
      background: #f5f5f5;
    }
    
    .sort-btn.active {
      background: #4a6baf;
      color: white;
      border-color: #3a5a9a;
    }
    
    .sort-icon {
      font-size: 1.1em;
    }
    
    /* Styles pour les rôles */
    tr.tuteur {
      --role-color: #e1f5fe;
    }
    
    tr.second {
      --role-color: #e8f5e9;
    }
    
    tr.confidentiel {
      --role-color: #fff3e0;
    }
    
    tr.tuteur td {
      background-color: var(--role-color);
    }
    
    tr.second td {
      background-color: var(--role-color);
    }
    
    tr.confidentiel td {
      background-color: var(--role-color);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <h1>Tableau des soutenances</h1>
      <div>
        <h2>Bienvenue, <?= e($enseignantFullName) ?></h2>
        <div class="muted">Connecté en tant qu'enseignant</div>
        <div class="muted">Email: <?= e($enseignant['mail'] ?? 'Non défini') ?></div>
      </div>
    </div>

    <div class="filters">
      <h3>Filtrer par rôle :</h3>
      <div class="filter-buttons">
        <button class="filter-btn active" data-filter="tuteur">
          <span class="filter-icon">👨‍🏫</span> Tuteur
        </button>
        <button class="filter-btn active" data-filter="second">
          <span class="filter-icon">👨‍💼</span> Second enseignant
        </button>
        <button class="filter-btn active" data-filter="confidentiel">
          <span class="filter-icon">🔒</span> Confidentiel
        </button>
        <button class="filter-btn active" data-filter="none">
          <span class="filter-icon">👤</span> Aucun rôle
        </button>
      </div>
    </div>

    <!-- Soutenances à venir -->
    <section>
      <div class="section-header">
        <h2>Soutenances à venir</h2>
        <div class="sort-buttons">
          <button class="sort-btn active" data-sort="date" data-order="asc">
            <span class="sort-icon">📅</span> Trier par date
          </button>
          <button class="sort-btn" data-sort="etudiant" data-order="asc">
            <span class="sort-icon">👤</span> Trier par étudiant
          </button>
        </div>
      </div>

      <?php if (empty($aVenir)) : ?>
        <p class="muted">Aucune soutenance à venir pour le moment.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Étudiant</th>
              <th>Entreprise</th>
              <th>Maître de stage (présence)</th>
              <th>Date</th>
              <th>Heure</th>
              <th>Salle</th>
              <th>Rôle</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
<?php foreach ($aVenir as $s) : ?>
  <?php
    $conf = !empty($s['confidentiel']);
    $role = isset($s['role']) ? $s['role'] : '';
    $rowClass = $conf ? 'confidentiel' : ($role === 'tuteur' ? 'tuteur' : ($role === 'second' ? 'second' : ''));
  ?>
  <tr class="<?= e($rowClass) ?>">
    <td><?= e(trim(($s['etudiant_nom'] ?? '') . ' ' . ($s['etudiant_prenom'] ?? '')) ?: '—') ?></td>
    <td><?= e($s['entreprise'] ?? '—') ?></td>
    <td><?= e(($s['maitre'] ?? '—') . ' (' . ((isset($s['maitre_present']) && $s['maitre_present']) ? 'Oui' : 'Non') . ')') ?></td>
    <td><?= e(!empty($s['date_heure']) ? date('d/m/Y', strtotime($s['date_heure'])) : '—') ?></td>
    <td><?= e(!empty($s['date_heure']) ? date('H:i', strtotime($s['date_heure'])) : '—') ?></td>
    <td><?= e($s['salle'] ?? '—') ?></td>
    <td><?= e($role ? ucfirst($role) : '—') ?></td>
    <td><?= e($s['type_stage'] ?? '—') ?></td>
  </tr>
<?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <!-- Soutenances passées (pour tuteur) -->
    <section style="margin-top:28px;">
      <h2>Soutenances passées (suivi)</h2>

      <?php if (empty($passees)) : ?>
        <p class="muted">Aucune soutenance passée enregistrée.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Étudiant</th>
              <th>Entreprise</th>
              <th>Statut évaluation</th>
              <th>Date</th>
              <th>Heure</th>
            </tr>
          </thead>
          <tbody>
<?php foreach ($passees as $s) : ?>
    <tr>
        <td><?= e($s['etudiant_nom'] . ' ' . $s['etudiant_prenom']) ?></td>
        <td><?= e($s['entreprise']) ?></td>
        <td><?= e($s['Statut']) ?></td>
        <td><?= e(date('d/m/Y', strtotime($s['date_heure']))) ?></td>
        <td><?= e(date('H:i', strtotime($s['date_heure']))) ?></td>
    </tr>
<?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Gestion des filtres
      const filterButtons = document.querySelectorAll('.filter-btn');
      
      filterButtons.forEach(button => {
        button.addEventListener('click', function() {
          this.classList.toggle('active');
          applyFilters();
        });
      });
      
      function applyFilters() {
        const activeFilters = Array.from(filterButtons)
          .filter(btn => btn.classList.contains('active'))
          .map(btn => btn.dataset.filter);
        
        // Afficher/masquer les lignes du tableau
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
          const rowClass = row.className || 'none';
          const shouldShow = activeFilters.some(filter => rowClass.includes(filter));
          row.style.display = shouldShow ? '' : 'none';
        });
      }
      
      // Initialisation des filtres
      applyFilters();
      
      // Gestion du tri
      const sortButtons = document.querySelectorAll('.sort-btn');
      const table = document.querySelector('table');
      
      if (!table) return;
      
      sortButtons.forEach(button => {
        button.addEventListener('click', function() {
          const sortBy = this.dataset.sort;
          const sortOrder = this.dataset.order || 'asc';
          
          // Mettre à jour l'état des boutons
          sortButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          
          // Inverser l'ordre pour le prochain clic
          this.dataset.order = sortOrder === 'asc' ? 'desc' : 'asc';
          
          // Trier le tableau
          sortTable(table, sortBy, sortOrder);
        });
      });
      
      function sortTable(table, sortBy, order) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
          let aValue, bValue;
          
          switch(sortBy) {
            case 'date':
              aValue = a.cells[3].textContent;
              bValue = b.cells[3].textContent;
              // Convertir les dates en objets Date pour la comparaison
              return compareDates(aValue, bValue, order);
              
            case 'etudiant':
              aValue = a.cells[0].textContent.toLowerCase();
              bValue = b.cells[0].textContent.toLowerCase();
              return compareStrings(aValue, bValue, order);
              
            case 'role':
              aValue = a.className || '';
              bValue = b.className || '';
              // Définir la priorité : tuteur (0) > second (1) > confidentiel (2) > (aucun = 3)
              const priority = { 
                'tuteur': 0, 
                'second': 1, 
                'confidentiel': 2, 
                '': 3 
              };
              
              // Si les deux éléments ont la même classe, on trie par nom d'étudiant
              if (aValue === bValue) {
                const aName = a.cells[0].textContent.toLowerCase();
                const bName = b.cells[0].textContent.toLowerCase();
                return compareStrings(aName, bName, 'asc');
              }
              
              aValue = priority[aValue] ?? 3;
              bValue = priority[bValue] ?? 3;
              return order === 'asc' ? aValue - bValue : bValue - aValue;
              
            default:
              return 0;
          }
        });
        
        // Réorganiser les lignes dans le DOM
        rows.forEach(row => tbody.appendChild(row));
      }
      
      function compareStrings(a, b, order) {
        if (a < b) return order === 'asc' ? -1 : 1;
        if (a > b) return order === 'asc' ? 1 : -1;
        return 0;
      }
      
      function compareDates(a, b, order) {
        // Convertir les dates du format jj/mm/aaaa en timestamp
        const parseDate = (dateStr) => {
          if (!dateStr || dateStr === '—') return order === 'asc' ? Infinity : -Infinity;
          const [day, month, year] = dateStr.split('/').map(Number);
          return new Date(year, month - 1, day).getTime();
        };
        
        const aTime = parseDate(a);
        const bTime = parseDate(b);
        
        if (aTime < bTime) return order === 'asc' ? -1 : 1;
        if (aTime > bTime) return order === 'asc' ? 1 : -1;
        return 0;
      }
    });
  </script>
  </body>
</html>
