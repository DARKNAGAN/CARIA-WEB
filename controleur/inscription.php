<?php
require_once './modele/profil.php';

// Fonction pour inscrire l'utilisateur
function inscriptionUser($userData) {
    global $bdd;
    // Enregistrement de l'utilisateur
    post_Registre(
        $userData['pseudo'], 
        $userData['password'], 
        $userData['email'], 
        $userData['localisation'], 
        $userData['prenom'], 
        $userData['nom'], 
        $userData['phone'], 
        $userData['avatar']
    );
    // Définition des variables de session
    $_SESSION['pseudo_session'] = $userData['pseudo'];
    $_SESSION['id_session'] = $bdd->lastInsertId(); // Assurez-vous d'avoir accès à $bdd ici
    $_SESSION['privilege_session'] = 2;
}

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
        // Validation de la taille et de la présence des champs
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $localisation = trim($_POST['localisation']);
    $phone = trim($_POST['phone']);

    if (empty($nom) || strlen($nom) < 3 || strlen($nom) > 25) {
        $errors[] = "Le nom est requis et doit contenir entre 3 et 25 caractères.";
    }

    if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ0-9 -]+$/', $nom)) {
    $errors[] = "Le nom peut contenir uniquement des lettres, des chiffres, des tirets et des espaces.";
    }
        
    if (preg_match('/^\s+$/', $nom)) {
        $errors[] = "Le nom ne peut pas être composé uniquement d'espaces.";
    }

    if (empty($prenom) || strlen($prenom) < 3 || strlen($prenom) > 25) {
        $errors[] = "Le prénom est requis et doit contenir entre 3 et 25 caractères.";
    }

    if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ0-9 -]+$/', $prenom)) {
    $errors[] = "Le prénom peut contenir uniquement des lettres, des chiffres, des tirets et des espaces.";
}
        
    if (preg_match('/^\s+$/', $prenom)) {
        $errors[] = "Le prénom ne peut pas être composé uniquement d'espaces.";
    }

    if (empty($pseudo) || strlen($pseudo) < 3 || strlen($pseudo) > 25) {
        $errors[] = "Le pseudo est requis et doit contenir entre 3 et 25 caractères.";
    }

    if (!ctype_alnum($pseudo)) {
        $errors[] = "Le pseudo ne doit contenir que des lettres alphabétiques ou des chiffres.";
    }

    if (!get_checkPseudo($pseudo)) {
        $errors[] = "Votre pseudo est déjà utilisé par un membre.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse e-mail est requise et doit être valide.";
    }

    if (!get_checkMail($email)) {
        $errors[] = "Votre adresse email est déjà utilisée par un membre.";
    }

    if (empty($password) || strlen($password) < 6 || strlen($password) > 32) {
        $errors[] = "Le mot de passe est requis et doit contenir entre 6 et 32 caractères.";
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).+$/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une lettre, un chiffre et un caractère spécial (@$!%*?&)";
    }

    if ($password !== $confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    
    // Gestion des espaces vides pour le numéro de téléphone
    if (empty($phone) || strlen($phone) < 10 || strlen($phone) > 15) {
        $errors[] = "Le numéro de téléphone est requis et doit contenir entre 10 et 15 caractères.";
    }

    if (preg_match('/^\s+$/', $phone)) {
        $errors[] = "Le numéro de téléphone ne peut pas être composé uniquement d'espaces.";
    }

    if (!preg_match('/^\d+$/', $phone)) {
        $errors[] = "Le numéro de téléphone ne doit contenir que des chiffres.";
    }
    
    if (empty($localisation) || strlen($localisation) < 6 || strlen($localisation) > 50) {
        $errors[] = "L'adresse postale est requise et doit contenir entre 6 et 50 caractères.";
    }

    if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ0-9 ]+$/', $localisation)) {
        $errors[] = "L'adresse postale peut contenir uniquement des lettres, des chiffres et des espaces.";
    }

    if (preg_match('/^\s+$/', $localisation)) {
        $errors[] = "L'adresse ne peut pas être composé uniquement d'espaces.";
    }

    if (!empty($_FILES['avatar']['size'])) {
        // Vérification de la taille maximale du fichier (par exemple, 5 Mo)
        $maxFileSize = 5 * 1024 * 1024; // 5 Mo en octets
        if ($_FILES['avatar']['size'] > $maxFileSize) {
            $errors[] = "La taille de l'avatar dépasse la limite autorisée de 5 Mo.";
        }

        // Vérification de l'extension du fichier
        $extensions_valides = array('jpg', 'jpeg', 'gif', 'png');
        $extension_upload = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension_upload, $extensions_valides)) {
            $errors[] = "Extension de l'avatar incorrecte. Seules les extensions JPG, JPEG, GIF et PNG sont autorisées.";
        }

        // Vérification du type MIME du fichier
        $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');
        $fileMimeType = mime_content_type($_FILES['avatar']['tmp_name']);
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            $errors[] = "Type de fichier non pris en charge. Veuillez télécharger une image au format JPG, JPEG, GIF ou PNG.";
        }
    }
    
require_once './vue/inscription.html';
    if (empty($errors)) {
        $userData = [
            'pseudo' => $pseudo,
            'password' => $password,
            'email' => $email,
            'localisation' => $localisation,
            'prenom' => $prenom,
            'nom' => $nom,
            'phone' => $_POST['phone'],
            'avatar' => $_FILES['avatar']
        ];
        inscriptionUser($userData);
        displayInscriptionSuccessMessage();
        exit;
    }
}

// Affichage du formulaire d'inscription avec les éventuelles erreurs
require_once './vue/inscription.html';
if (!empty($errors)) {
    displayInscriptionErrorMessage($errors);
}

// Fonction pour afficher un message d'erreur
function displayInscriptionErrorMessage($errors) {
    echo '<script>document.addEventListener("DOMContentLoaded", function() {
    var messageDiv = document.getElementById("message");
    var errorMessage = `<div class="alert alert-danger">
        <section id="content" class="page-content">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Echec de l\'inscription</h3>
                </div>
                <div class="card-body">
                    <p>Erreur(s) dans le formulaire d\'inscription :</p>
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
function displayInscriptionSuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
                <div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Inscription terminée</h3>
                            </div>
                            <div class="card-body">
                                <h5>Bienvenue ' . htmlspecialchars($_POST['pseudo'], ENT_QUOTES, 'UTF-8') . '<br><br> vous êtes maintenant inscrit sur la plateforme!</h5><br>
                                <p>Cliquez <a href="./index.php">ici</a> pour revenir à l\'acceuil</p>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = successMessage;
            // Redirection après un délai
            setTimeout(function() {
                window.location.href = "index.php";
            }, 5000); // 5 secondes
        });
    </script>';
}
?>
