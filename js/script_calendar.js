$(document).ready(function () {
  // Initialisation du calendrier pour les événements de l'utilisateur actuel
  var calendarMyEvents = $("#calendar-my-events").fullCalendar({
    events: "./modele/event/fetch-my-events.php",
    displayEventTime: false,
    selectable: true,
    selectHelper: true,
    editable: false,
    eventRender: eventRenderFunction,
    eventClick: eventClickFunction,
    eventMouseover: eventMouseoverFunction,
    eventMouseout: eventMouseoutFunction,
  });

  // Initialisation du calendrier pour tous les événements
  var calendarAllEvents = $("#calendar-all-events").fullCalendar({
    events: "./modele/event/fetch-all-events.php",
    displayEventTime: false,
    selectable: true,
    selectHelper: true,
    editable: false,
    eventRender: eventRenderFunction,
    eventClick: eventClickFunction,
    eventMouseover: eventMouseoverFunction,
    eventMouseout: eventMouseoutFunction,
  });

  // Fonction pour rendre les événements
  function eventRenderFunction(event, element, view) {
    if (event.allDay === "true") {
      event.allDay = true;
    } else {
      event.allDay = false;
    }
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
              $(this).fullCalendar("removeEvents", event.id);
              displayMessage("Réservation supprimée avec succès");
            }
          },
        });
      }
    });
  }

  // Fonction pour gérer le clic sur un événement
  function eventClickFunction(event) {
    // Afficher une pop-up avec les détails de l'événement
    alert(
      event.title + " - " + event.vehicule + " pour le client " + event.utilisateur + "\n" + 
        "\n" + "Date et lieu de début : " + 
        "\n" + event.start.format("dddd DD MMMM YYYY HH:mm") + 
        "\n" + event.adresse +"\n" + 
        "\n" + "Date et lieu de fin : " + 
        "\n" + event.end.format("dddd DD MMMM YYYY HH:mm") + 
        "\n" + event.adresse

    );
  }

  // Fonction pour gérer le survol d'un événement
  function eventMouseoverFunction(event, jsEvent, view) {
    // Change la forme du curseur lorsque vous survolez l'événement
    $(this).css("cursor", "pointer");
  }

  // Fonction pour gérer la sortie du survol d'un événement
  function eventMouseoutFunction(event, jsEvent, view) {
    // Réinitialise la forme du curseur lorsque vous ne survolez plus l'événement
    $(this).css("cursor", "auto");
  }

  // Fonction pour afficher un message
  function displayMessage(message) {
     alert(message);
  }

  // Fonction pour ajouter un événement
  function addAdminEvent(url, formId, calendarId) {
    $(formId).submit(function (event) {
      event.preventDefault(); // Empêche la soumission du formulaire par défaut

      // Récupérer les valeurs des champs
      var id_vehicule = $(formId + " #eventidVehiculeSelect").val();
      var id_user = $(formId + " #eventidUserSelect").val();
      var start = $(formId + " #eventStart").val();
      var end = $(formId + " #eventEnd").val();

      // Validation de base des champs
      if (!id_vehicule || !id_user || !start || !end) {
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
        url: url,
        type: "POST",
        data: {
          id_vehicule: id_vehicule,
          id_user: id_user,
          start: startFormatted,
          end: endFormatted,
        },
        success: function (response) {
          var data = JSON.parse(response);
          if (data.status === "success") {
            alert(data.message);
            $("#calendar-all-events").fullCalendar("refetchEvents"); // Met à jour les événements dans le calendrier
          } else {
            alert(data.message);
            $("#calendar-all-events").fullCalendar("refetchEvents"); // Met à jour les événements dans le calendrier
          }
        },
        error: function (xhr, status, error) {
          console.error("Erreur lors de la création de l'événement:", error);
          displayMessage("Erreur lors de la création de l'événement.");
        },
      });
    });
  }

  // Fonction pour ajouter un événement utilisateur
  function addUserEvent(url, formId, calendarId) {
    $(formId).submit(function (event) {
      event.preventDefault(); // Empêche la soumission du formulaire par défaut

      // Récupérer les valeurs des champs
      var id_vehicule = $(formId + " #eventidVehiculeSelect").val();
      var start = $(formId + " #eventStart").val();
      var end = $(formId + " #eventEnd").val();

      // Validation de base des champs
      if (!id_vehicule || !start || !end) {
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
        url: url,
        type: "POST",
        data: {
          id_vehicule: id_vehicule,
          start: startFormatted,
          end: endFormatted,
        },
        success: function (response) {
          var data = JSON.parse(response);
          if (data.status === "success") {
            alert(data.message);
            $("#eventMyForm")[0].reset();
            $("#calendar-my-events").fullCalendar("refetchEvents"); // Met à jour les événements dans le calendrier
          } else {
            alert(data.message);
            $("#calendar-my-events").fullCalendar("refetchEvents"); // Met à jour les événements dans le calendrier
          }
        },
        error: function (xhr, status, error) {
          console.error("Erreur lors de la création de l'événement:", error);
          displayMessage("Erreur lors de la création de l'événement.");
        },
      });
    });
  }

  // Appel de la fonction pour ajouter un événement administratif
  addAdminEvent(
    "./modele/event/add-event.php",
    "#eventForm",
    "#calendar-all-events"
  );

  // Appel de la fonction pour ajouter un événement de l'utilisateur
  addUserEvent(
    "./modele/event/add-my-event.php",
    "#eventMyForm",
    "#calendar-my-events"
  );
});
