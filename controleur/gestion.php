<?php
require_once './modele/profil.php';
require_once './modele/vehicule.php';
// Récupérer les informations sur les véhicules et les utilisateurs
// Initialisation des variables
$TotalDesMembres = get_MemberCount();
$TotalDesVehicules = get_VehiculesCount();
$data = get_LastMember();
$derniermembre = htmlspecialchars(stripslashes($data['pseudo']), ENT_QUOTES, 'UTF-8');
$vehicules = get_CarsInfo();
$cars = get_CarsInfo();
$users = get_ProfilsInfo();
$errors = [];
// Gestion des requêtes POST pour la suppression des véhicules
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicule'])) {
    try {
        // Assurez-vous que vehicule_id est défini et est un nombre valide
        if (!isset($_POST['vehicule_id']) || !is_numeric($_POST['vehicule_id'])) {
            throw new Exception("ID de véhicule invalide.");
        }
        // Convertir l'ID en entier de manière sécurisée
        $id = (int)$_POST['vehicule_id'];
        // Suppression du véhicule
        post_RemoveVehicule($id);
        // Inclure le fichier de vue et afficher le message de succès
        require_once './vue/gestion.html';
        displayDeleteVehiculeSuccessMessage();
        exit();
    } catch (Exception $e) {
        // En cas d'erreur, inclure le fichier de vue et afficher le message d'erreur
        require_once './vue/gestion.html';
        displayAddVehiculeErrorMessage(["Une erreur est survenue lors de la suppression du véhicule : " . $e->getMessage()]);
    }
}
// Formulaire pour les nouveaux véhicules
if (!empty($_POST['plaque'])) {
    try {
        $plaque = $_POST['plaque'];
        $marque = $_POST['marque'];
        $modele = $_POST['modele'];
        $annee = $_POST['annee'];

         // Validation des champs du formulaire
        if (empty($plaque) || strlen($plaque) < 7 || strlen($plaque) > 9) {
            $errors[] = "L'immatriculation doit comporter entre 7 et 9 caractères.";
        }

        if (empty($marque) || strlen($marque) < 2 || strlen($marque) > 50) {
            $errors[] = "La marque doit comporter entre 2 et 50 caractères.";
        }

        if (empty($modele) || strlen($modele) < 2 || strlen($modele) > 50) {
            $errors[] = "Le modèle doit comporter entre 2 et 50 caractères.";
        }

        if (empty($annee) || !preg_match('/^\d{4}$/', $annee)) {
            $errors[] = "L'année doit être un nombre de 4 chiffres.";
        }

        if (!get_checkPlaque()) {
            $errors[] = "Votre immatriculation est déjà utilisée par un véhicule.";
        }

    if (!empty($_FILES['image']['size'])) {
        // Vérification de la taille maximale du fichier (par exemple, 5 Mo)
        $maxFileSize = 5 * 1024 * 1024; // 5 Mo en octets
        if ($_FILES['image']['size'] > $maxFileSize) {
            $errors[] = "La taille de l'image dépasse la limite autorisée de 5 Mo.";
        }
        // Vérification de l'extension du fichier
        $extensions_valides = array('jpg', 'jpeg', 'gif', 'png');
        $extension_upload = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension_upload, $extensions_valides)) {
            $errors[] = "Extension de l'image incorrecte. Seules les extensions JPG, JPEG, GIF et PNG sont autorisées.";
        }
        // Vérification du type MIME du fichier
        $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');
        $fileMimeType = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errors[] = "Type de fichier non pris en charge. Veuillez télécharger une image au format JPG, JPEG, GIF ou PNG.";
        }
    }
        if (empty($errors)) {
            // Enregistrement du véhicule
            post_RegistreCar();
            require_once './vue/gestion.html';
            displayAddVehiculeSuccessMessage();
            exit();
        } else {
        require_once './vue/gestion.html';
        displayAddVehiculeErrorMessage($errors);  
        }
    } catch (Exception $e) {
        // Afficher les messages d'erreur
        require_once './vue/gestion.html';
        displayAddVehiculeErrorMessage(["Une erreur est survenue lors de l'enregistrement du véhicule."]);
    }
}
// Fonction pour afficher un message de succès
function displayDeleteVehiculeSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
                <div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Suppression du véhicule terminé</h3>
                            </div>
                            <div class="card-body">
                                <h5>Le véhicule ' . addslashes(htmlspecialchars($_POST['plaque'])) . ' a été supprimé de la plateforme.</h5><br>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = successMessage;
            // Redirection après un délai
            setTimeout(function() {
                window.location.href = "gestion.php";
            }, 2000); // 2 secondes
        });
    </script>';
}
// Fonction pour afficher un message d'erreur
function displayAddVehiculeErrorMessage($errors) {
    echo '<script>document.addEventListener("DOMContentLoaded", function() {
    var messageDiv = document.getElementById("message");
    var errorMessage = `<div class="alert alert-danger">
        <section id="content" class="page-content">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Ajout du véhicule interrompu</h3>
                </div>
                <div class="card-body">
                    <p>Erreur(s) dans le formulaire d\'ajout de véhicule :</p>
                    <ul style="list-style-type:none;">'; // Début de la liste d'erreurs
                        foreach ($errors as $error) {
                            echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>"; // Ajout de chaque erreur à la liste
                        }
        echo '       </ul>
                    </div>
                </div>
            </section>
        </div>
    `;
    messageDiv.innerHTML = errorMessage;
    });
    </script>';
}
// Fonction pour afficher un message de succès
function displayAddVehiculeSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
                <div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Ajout du véhicule terminé</h3>
                            </div>
                            <div class="card-body">
                                <h5>Le véhicule ' . addslashes(htmlspecialchars($_POST['plaque'])) . ' a été ajouté au véhicule de la plateforme.</h5><br>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = successMessage;
            // Redirection après un délai
            setTimeout(function() {
                window.location.href = "gestion.php";
            }, 2000); // 3 secondes
        });
    </script>';
}
require_once  './vue/gestion.html';
?>