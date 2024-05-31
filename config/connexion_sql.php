<?php
// Connexion à la base de données
try
{
    $bdd = new PDO('mysql:host=IP:3307;dbname=CARIA', 'USER', 'MDP',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}
catch(Exception $e)
{
    die('Erreur : '.$e->getMessage());
}
# Constantes
define('VISITEUR',1);
define('INSCRIT',2);
define('MODO',3);
define('ADMIN',4);
define('ERR_IS_CO','Vous ne pouvez pas accéder à cette page si vous n\'êtes pas connecté');
session_start();
# Connexion et initialisation des variables
$lvl_session = isset($_SESSION['privilege_session']) ? (int)$_SESSION['privilege_session'] : 1;
$id_session = isset($_SESSION['id_session']) ? (int)$_SESSION['id_session'] : 0;
$pseudo_session = isset($_SESSION['pseudo_session']) ? $_SESSION['pseudo_session'] : '';

function verifierAcces($min_privilege_requis) {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['id_session'])) {
        // Afficher la page d'erreur en incluant le fichier de vue
        $message = "Vous devez être connecté pour accéder à cette page.";
        include 'erreur.php';
        exit(); // Arrêter l'exécution du script après affichage de l'erreur
    }

    // Vérifier le privilège de l'utilisateur
    $privilege_utilisateur = isset($_SESSION['privilege_session']) ? (int)$_SESSION['privilege_session'] : VISITEUR;
    
    // Vérifier si le privilège de l'utilisateur est suffisant pour accéder à la page
    if ($privilege_utilisateur < $min_privilege_requis) {
        // Afficher la page d'erreur en incluant le fichier de vue
        $message = "Vous n'avez pas les droits d'accès nécessaires pour accéder à cette page.";
        include 'erreur.php';
        exit(); // Arrêter l'exécution du script après affichage de l'erreur
    }
}

?>