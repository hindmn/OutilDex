<?php

// ========== BONUS (démarrage de la session) ==========
//permet de retenir les informations de l'utilisateur courant
//PHP_SESSION_NONE : aucune session n'est encore démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========== AFFICHAGE ==========
//ENT_QUOTES : convertit les guillemets simples et doubles
function affichage($val)
{
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

// ========== REDIRECTION ==========
// envoie l'utilisateur vers une autre page
function rediriger($url)
{
    header('Location: ' . $url);
    exit;
}