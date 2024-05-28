use CARIA;
--
-- Index pour la table Reservations
--
ALTER TABLE Reservations
  ADD UNIQUE KEY uc_vehicule_reservation (id_vehicule,start,end),
  ADD UNIQUE KEY chevauchement_reservation (id_vehicule,start),
  ADD KEY id_user (id_user),
  ADD KEY id_vehicule (id_vehicule) USING BTREE;

--
-- Contraintes pour la table Reservations
--
ALTER TABLE Reservations
  ADD CONSTRAINT Reservations_idfk_1 FOREIGN KEY (id_vehicule) REFERENCES Vehicules (id),
  ADD CONSTRAINT Reservations_idfk_2 FOREIGN KEY (id) REFERENCES Clients (id);
COMMIT;
