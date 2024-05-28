// DECONNECTION
$(document).ready(function () {
  // Fonction pour gérer la déconnexion
  function deconnexion() {
    $.ajax({
      url: "deconnexion.php",
      type: "POST",
      success: function (data) {
        // Afficher le message de succès
        $("#message").html(
          "<div class='alert alert-success'>" + data + "</div>"
        );
        // Redirection après 5 secondes
        setTimeout(function () {
          window.location.href = "index.php";
        }, 5000);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Afficher un message d'erreur si la déconnexion échoue
        $("#message").html(
          "<div class='alert alert-danger'>Erreur de déconnexion</div>"
        );
      },
    });
  }
  // Gérer le clic sur le lien de déconnexion
  $("#deconnexionLink").click(function (e) {
    e.preventDefault(); // Empêcher le lien de déclencher une action par défaut
    deconnexion(); // Déclencher la fonction de déconnexion
  });
});

function getCookie(name) {
    let cookieArr = document.cookie.split(";");
    for(let i = 0; i < cookieArr.length; i++) {
        let cookiePair = cookieArr[i].split("=");
        if(name == cookiePair[0].trim()) {
            return decodeURIComponent(cookiePair[1]);
        }
    }
    return null;
}

function acceptCookies() {
    document.cookie = "cookieConsent=true; max-age=" + 60*60*24*365 + "; path=/";
    document.getElementById("cookieConsent").classList.add("d-none");
}

window.onload = function() {
    if (getCookie("cookieConsent") !== "true") {
        document.getElementById("cookieConsent").classList.remove("d-none");
    }
};
