<?php
require_once 'includes/connexion.php';

// ========================= CRÉATION ET INSERTION DES TABLES =========================

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS cours (
            id  VARCHAR(30) NOT NULL,
            titre   VARCHAR(100)    NOT NULL,
            PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS travaux (
            id  VARCHAR(30) NOT NULL,
            cours_id   VARCHAR(30)    NOT NULL,
            titre   VARCHAR(150)    NOT NULL,
            description TEXT    NOT NULL,
            type    VARCHAR(20) NOT NULL,
            disponible  DATE    NOT NULL,
            remise  DATETIME    NOT NULL,
            ouvert  BOOLEAN NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (cours_id) REFERENCES cours(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS creneaux (
            id  VARCHAR(30) NOT NULL,
            travail_id   VARCHAR(30)    NOT NULL,
            debut  DATETIME    NOT NULL,
            fin  DATETIME    NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (travail_id) REFERENCES travaux(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS disponibilites (
            id  INT NOT NULL    AUTO_INCREMENT,
            travail_id   VARCHAR(30)    NOT NULL,
            creneau_id   VARCHAR(30)    NOT NULL,
            matricule  VARCHAR(20)    NOT NULL,
            nom  VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY (travail_id, creneau_id, matricule),
            FOREIGN KEY (travail_id) REFERENCES travaux(id),
            FOREIGN KEY (creneau_id) REFERENCES creneaux(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

$cours = [
    ['420-244-AH', 'Bases de données applicatives'],
    ['420-245-AH', 'Programmation Web 1'],
    ['420-248-AH', 'Programmation Web 2'],
    ['420-253-AH', 'Projet de fin d’études']
];

$requete = $pdo->prepare('INSERT IGNORE INTO cours(id, titre) VALUES (?, ?)');
foreach ($cours as $c) {
    $requete->execute($c);
}

$travaux = [
    [
        'web1-tp1',
        '420-245-AH',
        'TP1 — Catalogue de produits',
        'Construire une première application PHP permettant d’afficher et de filtrer un catalogue.',
        'individuel',
        '2026-09-01',
        '2026-09-20 23:59',
        0
    ],

    [
        'web2-tp1',
        '420-248-AH',
        'TP1 — Gestion de réservations',
        'Développer une application Web transactionnelle permettant d’enregistrer et de consulter des réservations.',
        'equipe',
        '2026-09-21',
        '2026-10-18 23:59',
        1
    ],

    [
        'bdd-lab2',
        '420-244-AH',
        'Laboratoire 2 — Requêtes SQL',
        'Écrire et tester des requêtes SQL sur une base de données relationnelle fournie.',
        'individuel',
        '2026-09-28',
        '2026-10-12 23:59',
        1
    ],

    [
        'bdd-tp1',
        '420-244-AH',
        'TP1 — Modélisation d’une bibliothèque',
        'Concevoir un modèle relationnel et produire le script SQL permettant de créer la base de données.',
        'equipe',
        '2026-10-05',
        '2026-10-25 23:59',
        1
    ],

    [
        'pfe-prototype',
        '420-253-AH',
        'Prototype fonctionnel',
        'Produire une première version fonctionnelle du projet et préparer une démonstration à l’équipe enseignante.',
        'equipe',
        '2026-09-15',
        '2026-10-09 23:59',
        1
    ]
];

$requete = $pdo->prepare('INSERT IGNORE INTO travaux( id, cours_id, titre, description, type, disponible, remise, ouvert) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
foreach ($travaux as $t) {
    $requete->execute($t);
}

$creneaux = [
    ['web1-tp1-01', 'web1-tp1', '2026-09-15 18:00', '2026-09-15 19:30'],
    ['web1-tp1-02', 'web1-tp1', '2026-09-16 12:00', '2026-09-16 13:30'],
    ['web1-tp1-03', 'web1-tp1', '2026-09-17 17:00', '2026-09-17 18:30'],
    ['web2-tp1-01', 'web2-tp1', '2026-10-05 18:00', '2026-10-05 19:30'],
    ['web2-tp1-02', 'web2-tp1', '2026-10-06 12:00', '2026-10-06 13:30'],
    ['web2-tp1-03', 'web2-tp1', '2026-10-07 17:00', '2026-10-07 18:30'],
    ['bdd-lab2-01', 'bdd-lab2', '2026-10-01 16:00', '2026-10-01 17:30'],
    ['bdd-lab2-02', 'bdd-lab2', '2026-10-02 12:00', '2026-10-02 13:30'],
    ['bdd-lab2-03', 'bdd-lab2', '2026-10-04 10:00', '2026-10-04 11:30'],
    ['bdd-tp1-01', 'bdd-tp1', '2026-10-13 18:00', '2026-10-13 19:30'],
    ['bdd-tp1-02', 'bdd-tp1', '2026-10-14 12:00', '2026-10-14 13:30'],
    ['bdd-tp1-03', 'bdd-tp1', '2026-10-15 17:00', '2026-10-15 18:30'],
    ['int-s1-01', 'pfe-prototype', '2026-09-29 18:00', '2026-09-29 20:00'],
    ['int-s1-02', 'pfe-prototype', '2026-09-30 17:00', '2026-09-30 19:00'],
    ['int-s1-03', 'pfe-prototype', '2026-10-01 18:00', '2026-10-01 20:00']
];

$requete = $pdo->prepare('INSERT IGNORE INTO creneaux(id, travail_id, debut, fin) VALUES (?, ?, ?, ?)');
foreach ($creneaux as $cr) {
    $requete->execute($cr);
}

echo 'Initilisation terminée!';