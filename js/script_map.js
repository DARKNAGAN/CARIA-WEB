const DEFAULT_LATITUDE = 48.852969;
const DEFAULT_LONGITUDE = 2.349903;
// Event de pour initialisé la récuperation des voitures pour la MAP
document.addEventListener("DOMContentLoaded", () => {
  // Vérifier si nous sommes sur la page d'accueil
  if (["/index.php", "/"].includes(window.location.pathname)) {
    getCars();
  }
});
// Récuperation des voitures pour la MAP
function getCars() {
  fetch("./modele/get_cars.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      initMap(data);
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des véhicules:", error);
    });
}
// Initialisation de la MAP
function initMap(vehicules) {
  const markers = [];
  const macarte = L.map("map");
  // Utiliser la géolocalisation pour définir la vue de la carte
  navigator.geolocation.getCurrentPosition(
    (position) => {
      const { latitude, longitude } = position.coords;
      macarte.setView([latitude, longitude], 11);
    },
    () => {
      // En cas d'erreur de géolocalisation, utiliser les coordonnées par défaut
      macarte.setView([DEFAULT_LATITUDE, DEFAULT_LONGITUDE], 11);
    }
  );
  const markerClusters = L.markerClusterGroup();
  L.tileLayer("https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png", {
    attribution: "données © OpenStreetMap/ODbL - rendu OSM France",
    minZoom: 1,
    maxZoom: 20,
  }).addTo(macarte);
  for (const vehicule of Object.values(vehicules)) {
    const { id, lat, lon, img, dispo, marque, modele, annee } = vehicule;
    const iconBase = img;
    const disponibilite = dispo ? "Disponible" : "Indisponible";
    const dispochar = dispo ? "disponible" : "indisponible";
    const myIcon = L.icon({
      iconUrl: iconBase,
      iconSize: [50, 50],
      iconAnchor: [25, 50],
      popupAnchor: [-3, -76],
    });
    const popupContent = `
            <div style='text-align: center;'>
                Modèle: ${marque} ${modele}
                <br>
                Année: ${annee}
                <br>
                <span class='${dispochar}'>${disponibilite}</span>
            </div>
        `;
    const marker = L.marker([lat, lon], { icon: myIcon }).bindPopup(
      popupContent
    );
    markerClusters.addLayer(marker);
    markers.push(marker);
  }
  const group = L.featureGroup(markers);
  macarte.fitBounds(group.getBounds().pad(0.5));
  macarte.addLayer(markerClusters);
}
