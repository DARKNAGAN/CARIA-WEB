<?php
require_once './modele/vehicule.php';
require_once './modele/profil.php';
$vehicules = get_CarsInfo();

// Vérification de la session utilisateur
if (isset($id_session) && $id_session != 0) {
    require './vue/home.html';
} else {
    // Afficher la page d'accueil ou le formulaire de connexion
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require 'connexion.php';
    } else {
        require './vue/connexion.html';
    }
}
?>