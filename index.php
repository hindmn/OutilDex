<?php
require_once 'includes/fonctionsEssentiels.php';
require_once 'includes/connexion.php';
$titrePage = 'Accueil';

// ============== RÉCUPÉRER TOUS LES COURS ============== 
$listCours = $pdo->query('SELECT id, titre FROM cours ORDER BY id')->fetchAll();

// ============== VALIDER LE COURS CHOISI ==============
$cours = trim($_GET['cours'] ?? '');
$listeId = array_column($listCours, 'id');

// ============== VALIDATION COTÉ SERVEUR ==============
//Si le cours qui a été envoyé ne fait pas parti des réels cours, on l'ignore
if ($cours !== '' && !in_array($cours, $listeId, true)) {
    $cours = '';
}

// ============== RÉCUPÉRER TOUS LES COURS *OUVERTS* ==============
// .= : sert à coller le texte à la fin du $sql, obligatoire de laisser un espace au début
$sql = 'SELECT tr.id, tr.cours_id, tr.titre, tr.type, tr.remise, cs.titre AS cours_titre
        FROM travaux tr JOIN cours cs ON cs.id = tr.cours_id WHERE tr.ouvert = 1';
$param = [];
if ($cours !== '') {
    $sql .= ' AND tr.cours_id = :cours';
    $param['cours'] = $cours;
}
// tri par cours puis par date de remise
$sql .= ' ORDER BY cs.id, tr.remise';

$requete = $pdo->prepare($sql);
$requete->execute($param);
$travaux = $requete->fetchAll();

// ============== RECUPERER LES TRAVAUX PAR COURS ==============
// isset : si valeur est déclarée et non null
$travauxParCours = [];
foreach ($travaux as $tr) {
    $coursId = $tr['cours_id'];
    if (!isset($travauxParCours[$coursId])) {
        $travauxParCours[$coursId] = [
            'titre' => $tr['cours_titre'],
            'travaux' => [],
        ];
    }
    $travauxParCours[$coursId]['travaux'][] = $tr;
}

require 'includes/header.php';
?>

<?php // ============== AFFICHAGE ============== ?>

<h1>Accueil</h1>

<form method="get" action="index.php">
    <label for="cours">Cours</label>
    <select name="cours" id="cours">
        <option value="">Tous les cours</option>
        <?php foreach ($listCours as $c): ?>
            <option value="<?= affichage($c['id']) ?>" <?= $c['id'] === $cours ? 'selected' : '' ?>>
                <?= affichage($c['id'] . ' ' . $c['titre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrer</button>
</form>

<?php if (empty($travauxParCours)): ?>
    <p>Aucun travail ouvert n'a été trouvé pour ce cours.</p>
<?php endif; ?>

<?php // ============== AFFICHAGE DES TRAVAUX DU COURS SELECTIONNÉ ============== ?>

<?php foreach ($travauxParCours as $coursId => $groupe): ?>
    <section>
        <h2><?= affichage($coursId) ?> <?= affichage($groupe['titre']) ?></h2>
        <ul>
            <?php foreach ($groupe['travaux'] as $t): ?>
                <li>
                    <h3><?= affichage($t['titre']) ?></h3>
                    <p>Type : <?= affichage($t['type']) ?></p>
                    <p>Remise : <?= affichage($t['remise']) ?></p>
                    <a href="travail.php?travail=<?= urlencode($t['id']) ?>">Voir le travail</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endforeach; ?>

<?php require 'includes/footer.php'; ?>