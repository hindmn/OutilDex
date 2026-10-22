<?php
require_once 'includes/fonctionsEssentiels.php';
require_once 'includes/connexion.php';

$idTravail = trim($_GET['travail'] ?? '');

// ============= RENVOIE L'UTILISATEUR À L'ACCUEIL =============
if ($idTravail === '') {
    rediriger('index.php');
}

// ============= CHERCHER ET RENVOIE LE TRAVAIL SI EXISTE =============
$requete = $pdo->prepare(
    'SELECT tr.*, cs.titre AS cours_titre
     FROM travaux tr 
     JOIN cours cs ON cs.id = tr.cours_id
     WHERE tr.id = :id'
);
$requete->execute(['id' => $idTravail]);
$travail = $requete->fetch();

if ($travail === false) {
    rediriger('index.php');
}
$requete = $pdo->prepare(
    'SELECT * FROM creneaux WHERE travail_id = :id ORDER BY debut'
);
$requete->execute(['id' => $idTravail]);
$creneaux = $requete->fetchAll();
$idCreneaux = array_column($creneaux, 'id');

$erreurs = [];
$matricule = $_SESSION['matricule'] ?? '';
$nom = $_SESSION['nom'] ?? '';
$caseCoche = [];

$titrePage = $travail['titre'];

// ============= TRAITEMENT DE LA SOUMISSION =============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = trim($_POST['matricule'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $creneauxCoche = $_POST['creneaux'] ?? [];
    if (!is_array($creneauxCoche)) {
        $creneauxCoche = [];
    }
    $creneauxCoche = array_unique($creneauxCoche);

    if ($matricule === '') {
        $erreurs[] = 'Le matricule est requis.';
    } elseif (mb_strlen($matricule) > 20) {
        $erreurs[] = 'Le matricule ne peut pas dépasser 20 caractères.';
    }

    if ($nom === '') {
        $erreurs[] = 'Le nom est requis.';
    } elseif (mb_strlen($nom) > 100) {
        $erreurs[] = 'Le nom ne peut pas dépasser 100 caractères.';
    }

    if (count($creneauxCoche) === 0) {
        $erreurs[] = 'Vous devez sélectionner au moins un créneau.';
    }

    foreach ($creneauxCoche as $c) {
        if (!in_array($c, $idCreneaux, true)) {
            $erreurs[] = 'Un créneau sélectionné est invalide.';
            break;
        }
    }

    $creneauxCoche = array_values(array_intersect($creneauxCoche, $idCreneaux));
    $caseCoche = $creneauxCoche;

    // ============= VERIFIE ET ENREGISTRE SI TOUT EST VALIDE =============
    if (empty($erreurs)) {
        try {
            $pdo->beginTransaction();
            $supprimer = $pdo->prepare(
                'DELETE FROM disponibilites WHERE travail_id = :travail AND matricule = :matricule'
            );
            $supprimer->execute(['travail' => $idTravail, 'matricule' => $matricule]);

            $inserer = $pdo->prepare(
                'INSERT INTO disponibilites (travail_id, creneau_id, matricule, nom)
                 VALUES (:travail, :creneau, :matricule, :nom)'
            );
            foreach ($creneauxCoche as $c) {
                $inserer->execute([
                    'travail' => $idTravail,
                    'creneau' => $c,
                    'matricule' => $matricule,
                    'nom' => $nom
                ]);
            }
            $pdo->commit();
            $_SESSION['matricule'] = $matricule;
            $_SESSION['nom'] = $nom;
            rediriger('travail.php?travail=' . urlencode($idTravail) . '&enregistre=1');
        } catch (PDOException $e) {
            $pdo->rollBack();
            $erreurs[] = 'Erreur lors de l\'enregistrement des disponibilités.';
        }
    }
}

// ============= ENREGISTREMENT ET AFFICHARGE DES DISPONIBILITÉS =============
$requete = $pdo->prepare(
    'SELECT creneau_id, matricule, nom 
    FROM disponibilites 
    WHERE travail_id = :id
    ORDER BY nom'
);
$requete->execute(['id' => $idTravail]);
$disponibilites = $requete->fetchAll();

$nomCreneaux = [];
foreach ($idCreneaux as $c) {
    $nomCreneaux[$c] = [];
}
foreach ($disponibilites as $d) {
    $nomCreneaux[$d['creneau_id']][] = $d['nom'];
}

// ============= MEILLEUR CRÉNEAU =============
$compte = array_map('count', $nomCreneaux);
$max = empty($compte) ? 0 : max($compte);
$meilleurCreneaux = [];
if ($max > 0) {
    $meilleurCreneaux = array_keys($compte, $max);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $matricule !== '') {
    foreach ($disponibilites as $d) {
        if ($d['matricule'] === $matricule) {
            $caseCoche[] = $d['creneau_id'];
        }
    }
}

// ============= MESSAGE DE CONFIRMATION APRÈS VALIDATION =============
$succes = isset($_GET['enregistre']) && empty($erreurs);
require 'includes/header.php';

// ============= AFFICHAGE =============
?>
<p><a href="index.php">Retour à l'accueil</a></p>

<p><?= affichage($travail['cours_id']) . ' - ' . affichage($travail['cours_titre']) ?></p>
<h1><?= affichage($travail['titre']) ?></h1>
<p><?= affichage($travail['description']) ?></p>
<p>Type : <?= affichage($travail['type']) ?></p>
<p>Remise : <?= affichage($travail['remise']) ?></p>

<?php if (!empty($erreurs)): ?>
    <div class="message-erreur">
        <p>Veuillez corriger les erreurs suivantes :</p>
        <ul>
            <?php foreach ($erreurs as $e): ?>
                <li><?= affichage($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($succes): ?>
    <p class="message-succes">Vos disponibilités ont été enregistrées avec succès.</p>
<?php endif; ?>

<h2>Vos disponibilités</h2>

<form method="post" action="travail.php?travail=<?= urlencode($idTravail) ?>">
    <p>
        <label for="matricule">Matricule</label>
        <input type="text" name="matricule" id="matricule" maxlength="20" value="<?= affichage($matricule) ?>">
    </p>
    <p>
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" maxlength="100" value="<?= affichage($nom) ?>">
    </p>

    <table>
        <thead>
            <tr>
                <th>Libre</th>
                <th>Créneau</th>
                <th>Disponibles</th>
                <th>Noms</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($creneaux as $c):
                $noms = $nomCreneaux[$c['id']] ?? [];
                $estMeilleur = in_array($c['id'], $meilleurCreneaux, true);
                ?>
                <tr>
                    <td>
                        <input type="checkbox" name="creneaux[]" id="cr-<?= affichage($c['id']) ?>"
                            value="<?= affichage($c['id']) ?>" <?= in_array($c['id'], $caseCoche, true) ? 'checked' : '' ?>>
                    </td>
                    <td>
                        <label for="cr-<?= affichage($c['id']) ?>">
                            <?= affichage($c['debut']) ?> à <?= affichage($c['fin']) ?>
                        </label>
                        <?php if ($estMeilleur): ?>
                            <strong>Meilleur créneau</strong>
                        <?php endif; ?>
                    </td>
                    <td><?= count($noms) ?></td>
                    <td>
                        <?= empty($noms) ? 'Aucun' : affichage(implode(', ', $noms)) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit">Soumettre mes disponibilités</button>
</form>

<?php require 'includes/footer.php'; ?>