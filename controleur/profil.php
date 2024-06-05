<?php
require_once './modele/profil.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client'])) {
    $id = (int)$_SESSION['id_session'];
    post_RemoveClient($id);
    header("Location: index.php"); // Rediriger vers la page appropriée après la suppression
    exit();
}

$vehicules = get_ProfilsInfo();

// Récupérer l'action et l'ID du membre depuis les paramètres URL
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : 'consulter';
$membreId = isset($_GET['m']) ? (int)$_GET['m'] : null;

switch($action){
    case "consulter":
        // Afficher les informations du membre
        $userData = get_MemberInfo($membreId);
        if ($userData === false) {
            $message = "Impossible de récupérer les informations de l'utilisateur où il n'existe pas.";
           require_once './vue/erreur.html';
        } else {
            require_once './vue/profile_view.html';
        }
        break;
    case "modifier":
        handleProfileModification();
        break;
    default:
        echo '<p>Cette action est impossible</p>';
}

function handleProfileModification() {
    if (empty($_POST['sent'])) {
        $userData = get_MemberInfoId();
        require_once './vue/edit_profile_view.html';
    } else {
        // Collecter les données du formulaire
        $id = (int)$_SESSION['id_session'];
        $pseudo = $_SESSION['pseudo_session'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];
        $email = $_POST['email'];
        $localisation = $_POST['localisation'];
        $phone = $_POST['phone'];
        $avatar = $_FILES['avatar'];
        $errors = [];

        // Validation de l'email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail est requise et doit être valide.";
        }

        if (!get_checkMyMail($email, $id)) {
            $errors[] = "Votre adresse email est déjà utilisée par un membre.";
        }

        // Validation du mot de passe
        if (empty($password) || strlen($password) < 6 || strlen($password) > 32) {
            $errors[] = "Le mot de passe est requis et doit contenir entre 6 et 32 caractères.";
        }

        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).+$/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre, un chiffre et un caractère spécial (@$!%*?&)";
        }

        if ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        // Validation du numéro de téléphone
        if (empty($phone) || strlen($phone) < 10 || strlen($phone) > 15) {
            $errors[] = "Le numéro de téléphone est requis et doit contenir entre 10 et 15 caractères.";
        }

        if (preg_match('/^\s+$/', $phone)) {
            $errors[] = "Le numéro de téléphone ne peut pas être composé uniquement d'espaces.";
        }

        if (!preg_match('/^\d+$/', $phone)) {
            $errors[] = "Le numéro de téléphone ne doit contenir que des chiffres.";
        }

        // Validation de l'adresse postale
        if (empty($localisation) || strlen($localisation) < 6 || strlen($localisation) > 70) {
            $errors[] = "L'adresse postale est requise et doit contenir entre 6 et 70 caractères.";
        }

        if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ0-9 -]+$/', $localisation)) {
        $errors[] = "L'adresse postale peut contenir uniquement des lettres, des chiffres, des tirets et des espaces.";
        }

        if (preg_match('/^\s+$/', $localisation)) {
            $errors[] = "L'adresse ne peut pas être composée uniquement d'espaces.";
        }

        // Validation de l'avatar
        if (!empty($_FILES['avatar']['size'])) {
            $maxFileSize = 5 * 1024 * 1024; // 5 Mo en octets
            if ($_FILES['avatar']['size'] > $maxFileSize) {
                $errors[] = "La taille de l'avatar dépasse la limite autorisée de 5 Mo.";
            }

            $extensions_valides = ['jpg', 'jpeg', 'gif', 'png'];
            $extension_upload = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (!in_array($extension_upload, $extensions_valides)) {
                $errors[] = "Extension de l'avatar incorrecte. Seules les extensions JPG, JPEG, GIF et PNG sont autorisées.";
            }

            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileMimeType = mime_content_type($_FILES['avatar']['tmp_name']);
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                $errors[] = "Type de fichier non pris en charge. Veuillez télécharger une image au format JPG, JPEG, GIF ou PNG.";
            }
        }

        if (empty($errors)) {
            if (isset($_POST['delete'])) {
                post_RemoveAvatar($pseudo);
            }
            post_UpdateProfile($_SESSION['id_session'], $pseudo, $password, $email, $localisation, $phone, $avatar);
            $userData = get_MemberInfoId(); // Récupérer les informations mises à jour
            require_once './vue/edit_profile_view.html';
            displayModificationProfilSuccessMessage();
            exit();
        } else {
            $userData = get_MemberInfoId(); // Récupérer les informations pour afficher les erreurs
            require_once './vue/edit_profile_view.html';
            displayModificationProfilErrorMessage($errors);
        }
    }
}

// Fonction pour afficher un message de succès lors de la modification du profil
function displayModificationProfilSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
                <div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h2>Modification de profil terminée</h2>
                            </div>
                            <div class="card-body">
                                <h5>Votre profil a été modifié avec succès !</h5>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = successMessage;
        });
    </script>';
}

// Fonction pour afficher un message d'erreur lors de la modification du profil
function displayModificationProfilErrorMessage($errors) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var errorMessage = `
                <div class="alert alert-danger">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Échec de la modification du profil</h3>
                            </div>
                            <div class="card-body">
                                <p>Erreur(s) dans le formulaire de modification :</p>
                                <ul style="list-style-type:none;">';
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>"; // Ajouter chaque erreur à la liste
            }
            echo '           </ul>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = errorMessage;
        });
    </script>';
}

// Fonction pour afficher un message de succès lors de la suppression du profil
function displayDeleteProfilSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
                <div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h2>Suppression de compte confirmée</h2>
                            </div>
                            <div class="card-body">
                                <p>Votre compte a été supprimé avec succès. Nous vous remercions pour votre utilisation de notre service.</p>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = successMessage;
        });
    </script>';
}
?>
