<?php
require_once './modele/vehicule.php';
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
        $plaque_erreur1 = "Votre plaque est déjà utilisée par un vehicule";
        $i++;
    }
    if (strlen($plaque) < 7 || strlen($plaque) > 9) {
        $plaque_erreur2 = "Votre plaque est soit trop grande, soit trop petite";
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
        // Affichage du message de réussite
        echo '
            <div class="container">
                <section id="content" class="page-content">
                    <div class="container text-center">
                        <hr><h2 style="color: green;">Ajout du véhicule terminé</h2><br>
                        <h5 style="color: green;">Le véhicule ' . stripslashes(htmlspecialchars($_POST['plaque'])) . ' a été ajouté au véhicule de la compagnie</h5><hr><br>
                    </div>
                </section>
            </div>';
        // Inclure à nouveau le formulaire pour permettre à l'utilisateur de ressaisir des valeurs
        post_RegistreCars();
    } else {
        // Affichage des erreurs et lien pour retourner au formulaire
        echo '
            <div class="container"><section id="content" class="page-content"><div class="container text-center">
                <hr><h2>Ajout du véhicule interrompu</h2><br>
                    <h5>' . $i . ' erreurs se sont produites lors de l\'ajout du véhicule</h5><br>
                    <ul>';
                        if ($plaque_erreur1) echo "<p>$plaque_erreur1</p>";
                        if ($plaque_erreur2) echo "<p>$plaque_erreur2</p>";
                        if ($image_erreur3) echo "<p>$image_erreur3</p>";
    echo '
                    </ul><hr><br>
                </div></section></div>';
    }
}
require './vue/vehicule.html';
?>