<?php
	require_once './modele/profil.php';
	require_once './modele/vehicule.php';
    // Gestion des requêtes POST pour la suppression des véhicules
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicule'])) {
    $id = (int)$_POST['vehicule_id'];
    post_RemoveVehicule($id);
    header("Location: gestion.php"); // Redirigez vers la page appropriée après la suppression
    exit();
}
####################
# Liste des véhicules de la compagnie #
$cars = get_CarsInfo(); // Récupère les informations sur les vehicules depuis le modèle pour afficher toutes les voitures
$users = get_ProfilsInfo(); // Récupère les informations sur les vehicules depuis le modèle pour afficher toutes les voitures
####################
# Formulaire pour les nouveaux véhicules #
if (empty($_POST['plaque'])) { // Si la variable est vide, on peut considérer qu'on est sur la page de formulaire
} else { // On est dans le cas traitement
    $plaque_erreur1 = NULL;
    $plaque_erreur2 = NULL;
    $image_erreur3 = NULL;
    // On récupère les variables
    $i = 0;
    $extensions_valides = array('jpg', 'jpeg', 'gif', 'png');
    $plaque = $_POST['plaque'];
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    // Vérification des champs du formulaire
    $plaque_free = get_checkPlaque();
    if (!$plaque_free) {
        $plaque_erreur1 = "Votre immatriculation est déjà utilisée par un vehicule";
        $i++;
    }
    if (!empty($_FILES['image']['size'])) {
        $extension_upload = strtolower(substr(strrchr($_FILES['image']['name'], '.'), 1));
        if (!in_array($extension_upload, $extensions_valides)) {
            $i++;
            $image_erreur3 = "Extension de l'image incorrecte";
        }
    }
    if ($i == 0) {
        // Message de réussite
        echo '
            <script>
                alert("Ajout du véhicule terminé.\n\n Le véhicule ' . addslashes(htmlspecialchars($_POST['plaque'])) . ' a été ajouté au véhicule de la plateforme.");
            </script>';
        // Inclure à nouveau le formulaire pour permettre à l'utilisateur de ressaisir des valeurs
        post_RegistreCar();
        header("Location: gestion.php"); // Redirigez vers la page appropriée après la suppression
        exit();
    } else {
        // Affichage des erreurs
        $errorMessages = '';
        if ($plaque_erreur1) $errorMessages .= "$plaque_erreur1";
        if ($image_erreur3) $errorMessages .= "$image_erreur3";
        echo '
            <script>
                alert("' . $i . ' erreurs s\'est produites lors de l\'ajout du véhicule.\n\n' . addslashes($errorMessages) . '");
            </script>';
    }
}
require  './vue/gestion.html';
?>