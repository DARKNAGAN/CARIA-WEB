//# AFFICHE MY EVENT
var calendarMyEvents = $("#calendar-my-events").fullCalendar({
  events: "./modele/event/fetch-my-events.php",
  displayEventTime: false,
  eventRender: function (event, element, view) {
    if (event.allDay === "true") {
      event.allDay = true;
    } else {
      event.allDay = false;
    }
  },
  selectable: true,
  selectHelper: true,
  editable: false,
  eventClick: function (event) {
    // Afficher une pop-up avec les détails de l'événement
    alert(
      event.title +
        " : \n" +
        event.vehicule +
        " pour le client " +
        event.utilisateur +
        "\n" +
        "\n" +
        "Période de réservation : " +
        "\n" +
        event.start.format("dddd DD MMMM YYYY HH:mm") +
        " --> " +
        event.end.format("dddd DD MMMM YYYY HH:mm")
    );
  },
  eventRender: function (event, element, view) {
    element.bind("contextmenu", function (e) {
      e.preventDefault(); // Empêche le menu contextuel par défaut de s'afficher
      var deleteMsg = confirm("Voulez-vous vraiment supprimer la réservation?");
      if (deleteMsg) {
        $.ajax({
          type: "POST",
          url: "./modele/event/delete-event.php",
          data: "&id=" + event.id,
          success: function (response) {
            if (parseInt(response) > 0) {
              $("#calendar-my-events").fullCalendar("removeEvents", event.id);
              displayMessage("Réservation supprimée avec succès");
            }
          },
        });
      }
    });
  },
  eventMouseover: function (event, jsEvent, view) {
    // Change la forme du curseur lorsque vous survolez l'événement
    $(this).css("cursor", "pointer");
  },
  eventMouseout: function (event, jsEvent, view) {
    // Réinitialise la forme du curseur lorsque vous ne survolez plus l'événement
    $(this).css("cursor", "auto");
  },
});

//# AFFICHE ALL EVENTS
$(document).ready(function () {
var calendarAllEvents = $("#calendar-all-events").fullCalendar({
  events: "./modele/event/fetch-all-events.php",
  displayEventTime: false,
  eventRender: function (event, element, view) {
    if (event.allDay === "true") {
      event.allDay = true;
    } else {
      event.allDay = false;
    }
  },
  selectable: true,
  selectHelper: true,
  editable: false,
  eventClick: function (event) {
    // Afficher une pop-up avec les détails de l'événement
    alert(
      event.title +
        " : \n" +
        event.vehicule +
        " pour le client " +
        event.utilisateur +
        "\n" +
        "\n" +
        "Période de réservation : " +
        "\n" +
        event.start.format("dddd DD MMMM YYYY HH:mm") +
        " --> " +
        event.end.format("dddd DD MMMM YYYY HH:mm")
    );
  },
  eventRender: function (event, element, view) {
    element.bind("contextmenu", function (e) {
      e.preventDefault(); // Empêche le menu contextuel par défaut de s'afficher
      var deleteMsg = confirm("Voulez-vous vraiment supprimer la réservation?");
      if (deleteMsg) {
        $.ajax({
          type: "POST",
          url: "./modele/event/delete-event.php",
          data: "&id=" + event.id,
          success: function (response) {
            if (parseInt(response) > 0) {
              $("#calendar-all-events").fullCalendar("removeEvents", event.id);
              displayMessage("Réservation supprimée avec succès");
            }
          },
        });
      }
    });
  },
  eventMouseover: function (event, jsEvent, view) {
    // Change la forme du curseur lorsque vous survolez l'événement
    $(this).css("cursor", "pointer");
  },
  eventMouseout: function (event, jsEvent, view) {
    // Réinitialise la forme du curseur lorsque vous ne survolez plus l'événement
    $(this).css("cursor", "auto");
  },
});

//# AJOUTE EVENT ADMIN
$(document).ready(function () {
  $("#eventForm").submit(function (event) {
    event.preventDefault(); // Empêche la soumission du formulaire par défaut
    // Récupérer les valeurs des champs
    var id_user = $("#eventidUserSelect").val();
    var id_vehicule = $("#eventidVehiculeSelect").val();
    var start = $("#eventStart").val();
    var end = $("#eventEnd").val();
    // Validation de base des champs
    if (!id_user || !id_vehicule || !start || !end) {
      displayMessage("Tous les champs sont requis.");
      return;
    }
    // Convertir le format datetime-local en format MySQL datetime
    function convertToMySQLDateTime(datetimeLocal) {
      return datetimeLocal.replace("T", " ") + ":00";
    }
    var startFormatted = convertToMySQLDateTime(start);
    var endFormatted = convertToMySQLDateTime(end);

    $.ajax({
      url: "./modele/event/add-event.php",
      type: "POST",
      data: {
        id_user: id_user,
        id_vehicule: id_vehicule,
        start: startFormatted,
        end: endFormatted,
      },
      success: function (response) {
        displayMessage("Réservation créée avec succès.");
        $("#eventForm")[0].reset();
        $("#calendar-all-events").fullCalendar("refetchEvents"); // Met à jour les événements dans le calendrier
      },
      error: function (xhr, status, error) {
        console.error("Erreur lors de la création de l'événement:", error);
        displayMessage("Erreur lors de la création de l'événement.");
      },
    });
  });
  function displayMessage(message) {
    alert(message);
  }
});

//# AJOUTE MY EVENT
$(document).ready(function () {
  $("#eventMyForm").submit(function (event) {
    event.preventDefault(); // Empêche la soumission du formulaire par défaut

    var id_vehicule = $("#eventidVehiculeSelect").val();
    var start = $("#eventStart").val();
    var end = $("#eventEnd").val();

    // Convertir le format datetime-local en format MySQL datetime
    function convertToMySQLDateTime(datetimeLocal) {
      return datetimeLocal.replace("T", " ") + ":00";
    }
    var startFormatted = convertToMySQLDateTime(start);
    var endFormatted = convertToMySQLDateTime(end);

    $.ajax({
      url: "./modele/event/add-my-event.php",
      type: "POST",
      data: {
        id_vehicule: id_vehicule,
        start: startFormatted,
        end: endFormatted,
      },
      success: function (response) {
        displayMessage("Réservation créée avec succès.");
        $("#eventMyForm")[0].reset();
        $("#calendar-my-events").fullCalendar("refetchEvents"); // Met à jour les événements dans le calendrier
      },
      error: function (xhr, status, error) {
        console.error("Erreur lors de la création de l'événement:", error);
        displayMessage("Erreur lors de la création de l'événement.");
      },
    });
  });

  // Fonction pour afficher les messages à l'utilisateur
  function displayMessage(message) {
    // Implémentez votre méthode d'affichage des messages ici
    alert(message);
  }
});

  // Fonction pour afficher les messages à l'utilisateur
  function displayMessage(message) {
    // Implémentez votre méthode d'affichage des messages ici
    alert(message);
  }
});

function displayMessage(message) {
	    $("#message").html("<div class='alert alert-success'>"+message+"</div>");
    setInterval(function() { $(".alert-success").fadeOut(); }, 3000);
}