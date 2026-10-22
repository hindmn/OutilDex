<?php
$dsn = 'mysql:host=localhost;dbname=DexBD;charset=utf8mb4';
$utilisateur = 'root';
$motDePasse = '';

//configurer le comportement de PDO
$options = [
    //si erreur, PDO lance une exception
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //desactive la simulation des requetes préparées par PHP
    PDO::ATTR_EMULATE_PREPARES => false,
    //accéder aux valeurs par le nom de la colonne, évite les valeurs en double
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $utilisateur, $motDePasse, $options);
} catch (PDOException $e) {
    exit('Connexion impossible : Vérifiez que votre MySQL est démarré!');
}