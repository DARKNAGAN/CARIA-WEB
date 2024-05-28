<?php
// Vérification des identifiants de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $pseudoCo = $_POST['pseudo_connect'] ?? '';
    $passwordCo = $_POST['password_connect'] ?? '';

    // Vérification des identifiants
    $connexionModel = new Connexion();
    $userData = $connexionModel->check_Password($pseudoCo);

    if ($userData && $connexionModel->checkCredentials($pseudoCo, $passwordCo)) {
        // Les identifiants sont corrects, connecter l'utilisateur
        //require_once './vue/home.html';
        // displaySuccessMessage();
        connectUser($userData);
    } else {
        // Afficher un message d'erreur en cas de connexion échouée
        require './vue/connexion.html';
        displayErrorMessage();
    }  
}
// Fonction pour connecter l'utilisateur
function connectUser($userData) {
    $_SESSION['pseudo_session'] = $userData['pseudo'];
    $_SESSION['privilege_session'] = $userData['privilege'];
    $_SESSION['id_session'] = $userData['id'];
    // Redirection vers la page principale
    header('Location: index.php');
    exit();
}
// Fonction pour afficher un message d'erreur
function displayErrorMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var errorMessage = `
                <div class="alert alert-danger">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Echec de la connexion</h3>
                            </div>
                            <div class="card-body">
                                <p>La combinaison du pseudo et mot de passe saisie n\'est pas correcte.</p>
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
function displaySuccessMessage() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            var successMessage = `
                <div class="alert alert-success">
                    <section id="content" class="page-content">
                        <div class="card text-center">
                            <div class="card-header">
                                <h3>Connexion réussie</h3>
                            </div>
                            <div class="card-body">
                                <p>Bonjour ' . htmlspecialchars($_SESSION['pseudo_session'], ENT_QUOTES, 'UTF-8') . '.</p>
                            </div>
                        </div>
                    </section>
                </div>
            `;
            messageDiv.innerHTML = successMessage;
            // Redirection après un délai
            setTimeout(function() {
                window.location.href = "index.php";
            }, 3000); // 3 secondes
        });
    </script>';
}
?>