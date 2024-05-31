use CARIA;
DROP TABLE IF EXISTS tentatives_connexion,Reservations,Clients,Vehicules;

CREATE TABLE tentatives_connexion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(25) NOT NULL,
    timestamp INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
--
-- Structure de la table Clients
--
CREATE TABLE Clients (
  id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  privilege int(11) DEFAULT 2,
  dateenregistre timestamp NOT NULL DEFAULT current_timestamp(),
  pseudo varchar(25),
  prenom varchar(25) DEFAULT NULL,
  nom varchar(25) DEFAULT NULL,
  adresse varchar(50) DEFAULT NULL,
  phone varchar(15) DEFAULT NULL,
  email varchar(30) DEFAULT NULL,
  mdp varchar(60) NOT NULL,
  avatar varchar(250) DEFAULT '/images/avatars/img_user.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Structure de la table Vehicules
--
CREATE TABLE Vehicules (
  id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  plaque varchar(9) NOT NULL,
  marque varchar(50) DEFAULT NULL,
  modele varchar(50) DEFAULT NULL,
  annee int(4) DEFAULT NULL,
  image varchar(250) NOT NULL DEFAULT '/images/vehicules/img_vehicule.png',
  disponible tinyint(1) NOT NULL DEFAULT 0,
  latitude decimal(9,6) NOT NULL DEFAULT 48.000000,
  longitude decimal(9,6) NOT NULL DEFAULT 2.000000,
  ip varchar(40) NOT NULL DEFAULT '127.0.0.1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Structure de la table Reservations
--
CREATE TABLE Reservations (
  id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  id_user int(11) DEFAULT NULL,
  id_vehicule int(11) DEFAULT NULL,
  title varchar(255) DEFAULT NULL,
  start timestamp NULL DEFAULT NULL,
  end timestamp NULL DEFAULT NULL,
  CONSTRAINT uc_vehicule_reservation UNIQUE (id_vehicule, start, end),
  CONSTRAINT uc_client_reservation UNIQUE (id_user, start, end),
  CONSTRAINT Reservations_idfk_1 FOREIGN KEY (id_vehicule) REFERENCES Vehicules (id),
  CONSTRAINT Reservations_idfk_2 FOREIGN KEY (id_user) REFERENCES Clients (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Index pour accélérer les recherches
CREATE INDEX idx_id_user ON Reservations (id_user);
CREATE INDEX idx_id_vehicule ON Reservations (id_vehicule) USING BTREE;



use CARIA;
--
-- Déchargement des données de la table Clients
--
INSERT INTO Clients (privilege, dateenregistre, pseudo, prenom, nom, adresse, mdp, email, avatar, phone) VALUES
(1, '2016-09-11 22:00:00', 'sa', 'sa', 'pc', 'sa', '$2y$10$SGOcYko1PzYekMHfLBvlPOFUL3V7r0zKSxrrfaNUMXv.Mm3mQizvC', 'sa@gmail.com', '/images/avatars/sa/img_user.jpg', "0658293476"),
(2, NOW(), 'utilisateur1', 'John', 'Doe', '123 Rue de Paris', '$2y$10$OkaujySOK.TWqiNOX4BSreuc7OFYuWji9EvruDlo71RRoXNR0HCbC', 'john.doe@example.com', '/images/avatars/utilisateur1/img_user.jpg', '0123456789'),
(2, NOW(), 'utilisateur2', 'Alice', 'Smith', '456 Avenue des Champs-Élysées', '$2y$10$rhZHTmq9QZA5uRyUr22sauEvHXKAZo.mjrLAsZ.SpImF.aqG5GSj6', 'alice.smith@example.com', '/images/avatars/utilisateur2/img_user.jpg', '9876543210'),
(2, NOW(), 'utilisateur3', 'Michael', 'Johnson', '789 Boulevard Saint-Michel', '$2y$10$/b9EglJ5RlO8vZK36PjjAuosbVkuB6T6xnr2uvq.sMtIg3hNjN0nG', 'michael.johnson@example.com', '/images/avatars/utilisateur3/img_user.jpg', '1234567890'),
(2, NOW(), 'utilisateur4', 'Emma', 'Brown', '1011 Rue de Rivoli', 'motdepasse4', 'emma.brown@example.com', '/images/avatars/utilisateur4/img_user.jpg', '0987654321'),
(2, NOW(), 'utilisateur5', 'David', 'Wilson', '1213 Rue du Faubourg Saint-Honoré', '$2y$10$9E1c4mmQqpgry7m/TuDU/eCsIb5HAWzPpK1BkP6Slg.4r.huK1gMS', 'david.wilson@example.com', '/images/avatars/utilisateur5/img_user.jpg', '1122334455'),
(2, NOW(), 'utilisateur6', 'Sophia', 'Martinez', '1415 Avenue Montaigne', '$2y$10$ghZ9/hsN4EdhaJ0JyUbbVO5KaYpRMx.ax9yz4ZCAzAQ5NXL9vzuIW', 'sophia.martinez@example.com', '/images/avatars/utilisateur6/img_user.jpg', '5544332211'),
(2, NOW(), 'utilisateur7', 'Matthew', 'Anderson', '1617 Avenue de la Grande Armée', '$2y$10$kpaoSFeugXSq8R461rQJ/uH6zM/k841Oi5tz8zjmUboiM7swsI9vC', 'matthew.anderson@example.com', '/images/avatars/utilisateur7/img_user.jpg', '6677889900'),
(2, NOW(), 'utilisateur8', 'Olivia', 'Taylor', '1819 Rue de la Paix', '$2y$10$5qpp5BCM1BcYMt2MdTld9.6MeCUo96yl0T1TqLtxlrojE1GD8H0z.', 'olivia.taylor@example.com', '/images/avatars/utilisateur8/img_user.jpg', '7788990011'),
(2, NOW(), 'utilisateur9', 'Daniel', 'Thomas', '2021 Boulevard Haussmann', '$2y$10$lxeSumVQvBQ2i4u9EH2a1OnmfvKnWtpSzbAQ6H/f8e/nSOvJJZ8NW', 'daniel.thomas@example.com', '/images/avatars/utilisateur9/img_user.jpg', '8899001122'),
(2, NOW(), 'utilisateur10', 'Emily', 'Jackson', '2223 Avenue Victor Hugo', '$2y$10$i2.MtLwCRowHpdxbPB//EeGHEOD0jlKH.7T5/5UIFiFy/0WZedspW', 'emily.jackson@example.com', '/images/avatars/utilisateur10/img_user.jpg', '9900112233'),
(2, NOW(), 'utilisateur11', 'Sophie', 'Martin', '2325 Rue Saint-Antoine', '$2y$10$b.rhvW.vknrfCgRNFIIp3Oha/Hq1.jSpn90DUnPxPW3/kJdqtbGCK', 'sophie.martin@example.com', '/images/avatars/utilisateur11/img_user.jpg', '1122334455'),
(2, NOW(), 'utilisateur12', 'William', 'Thompson', '2427 Avenue des Ternes', '$2y$10$HUqNXYpyo2uP1E.tgR/z8uU5O4n365JBiUH5p7qB2le5gO0.infHe', 'william.thompson@example.com', '/images/avatars/utilisateur12/img_user.jpg', '2244668899'),
(2, NOW(), 'utilisateur13', 'Charlotte', 'Clark', '2529 Rue de la Pompe', '$2y$10$SuYzFoQdjROkK6xOaBm1L.4higlSCJX5Z0QVItBjkx5/qUMsX9tTS', 'charlotte.clark@example.com', '/images/avatars/utilisateur13/img_user.jpg', '3366990022'),
(2, NOW(), 'utilisateur14', 'James', 'White', '2631 Boulevard Malesherbes', '$2y$10$21dxDMovRzObUfIO1Ykh9eiOnW0tjbZE7JbgnZMLWlA3kJ/BZEVuy', 'james.white@example.com', '/images/avatars/utilisateur14/img_user.jpg', '4488112233'),
(2, NOW(), 'utilisateur15', 'Ava', 'Hall', '2733 Avenue de la République', '$2y$10$E4NL1lLTNfptHjeozX2x9OYji2H5uldcvrEd7QaezvmAWnWc7XGFK', 'ava.hall@example.com', '/images/avatars/utilisateur15/img_user.jpg', '5511223344'),
(2, NOW(), 'utilisateur16', 'Noah', 'Lewis', '2835 Rue de Passy', '$2y$10$xjrpINALKuGkFLKTNlMcVOk9NoYBfoc6yA4Pvt66mMGHykPPLlKzW', 'noah.lewis@example.com', '/images/avatars/utilisateur16/img_user.jpg', '6633445566'),
(2, NOW(), 'utilisateur17', 'Mia', 'Adams', '2937 Boulevard Saint-Germain', '$2y$10$rQuvGd48.B6EmoYAl7d2PeEMvSMQsDduQPvcWzxbtLSGVL/066hpW', 'mia.adams@example.com', '/images/avatars/utilisateur17/img_user.jpg', '7755668899'),
(2, NOW(), 'utilisateur18', 'Benjamin', 'Young', '3039 Rue du Bac', '$2y$10$wMFNjwJJPkAXYF31Ot3Ko.L8acwtiCOx2waqQKzqQ8z/PJkLuDz1i', 'benjamin.young@example.com', '/images/avatars/utilisateur18/img_user.jpg', '8877665544'),
(2, NOW(), 'utilisateur19', 'Ella', 'Harris', '3141 Avenue Marceau', '$2y$10$q4G7cKwWSFvxcTf95L72s./aqtuN0h.3gcc3w4pfFX.EeUgDIZAea', 'ella.harris@example.com', '/images/avatars/utilisateur19/img_user.jpg', '9900887766'),
(2, NOW(), 'utilisateur20', 'Lucas', 'King', '3243 Rue de Sèvres', '$2y$10$U3B8F76RFmcsQdTP.QxApeLQHBkH1HSKzISz5ndXS9jNpF8uc4dQG', 'lucas.king@example.com', '/images/avatars/utilisateur20/img_user.jpg', '0011223344');

--
-- Déchargement des données de la table Vehicules
--
INSERT INTO Vehicules (plaque, image, marque, modele, annee, disponible, latitude, longitude, ip) VALUES
("GH209RH", '/images/vehicules/GH209RH/img_vehicule.png', 'Tesla', 'Model S', 2023, 1, 48.358844, 3.294351, '192.168.74.194'),
("PH940OP", '/images/vehicules/PH940OP/img_vehicule.png', 'Nissan', 'Leaf', 2022, 1, 48.8566, 6.3522, '127.0.0.1'),
("XD738UJ", '/images/vehicules/XD738UJ/img_vehicule.png', 'BMW', 'i3 S', 2024, 0, 48.6566, 2.3522, '127.0.0.1'),
("WSD754YH", '/images/vehicules/WSD754YH/img_vehicule.png', 'Toyota', 'Prius', 2023, 1, 48.0566, 3.3522, '127.0.0.1'),
("IO334PO", '/images/vehicules/IO334PO/img_vehicule.png', 'Chevrolet', 'Bolt', 2023, 0, 48.7566, 7.3522, '127.0.0.1'),
('AA123AA', '/images/vehicules/AA123AA/img_vehicule.png', 'Renault', 'Clio', 2018, 1, 48.558844, 2.294351, '127.0.0.1'),
('BB456BB', '/images/vehicules/BB456BB/img_vehicule.png', 'Peugeot', '208', 2016, 1, 48.2566, 1.3522, '127.0.0.1'),
('CC789CC', '/images/vehicules/CC789CC/img_vehicule.png', 'Volkswagen', 'Golf', 2019, 1, 48.8566, 9.3522, '127.0.0.1'),
('DD012DD', '/images/vehicules/DD012DD/img_vehicule.png', 'Ford', 'Fiesta', 2015, 1, 49.8566, 6.3522, '127.0.0.1'),
('EE345EE', '/images/vehicules/EE345EE/img_vehicule.png', 'Toyota', 'Yaris', 2017, 1, 45.8566, 4.3522, '127.0.0.1'),
('FF678FF', '/images/vehicules/FF678FF/img_vehicule.png', 'Audi', 'A3', 2014, 1, 42.8566, 2.3522, '127.0.0.1'),
('GG901GG', '/images/vehicules/GG901GG/img_vehicule.png', 'BMW', 'Series 1', 2020, 1, 46.8566, 3.3522, '127.0.0.1'),
('HH234HH', '/images/vehicules/HH234HH/img_vehicule.png', 'Mercedes-Benz', 'A-Class', 2019, 1, 48.8566, 2.3522, '127.0.0.1'),
('II567II', '/images/vehicules/II567II/img_vehicule.png', 'Hyundai', 'i30', 2016, 1, 50.8566, 7.3522, '127.0.0.1'),
('JJ890JJ', '/images/vehicules/JJ890JJ/img_vehicule.png', 'Kia', 'Rio', 2017, 1, 49.8566, 4.3522, '127.0.0.1'),
('KK123KK', '/images/vehicules/KK123KK/img_vehicule.png', 'Nissan', 'Micra', 2018, 1, 44.8566, 2.3522, '127.0.0.1'),
('LL456LL', '/images/vehicules/LL456LL/img_vehicule.png', 'Fiat', '500', 2015, 1, 43.8566, 2.6522, '127.0.0.1'),
('MM789MM', '/images/vehicules/MM789MM/img_vehicule.png', 'Skoda', 'Fabia', 2019, 1, 47.8566, 2.8522, '127.0.0.1'),
('NN012NN', '/images/vehicules/NN012NN/img_vehicule.png', 'Volvo', 'V40', 2016, 1, 45.8566, 2.4522, '127.0.0.1'),
('OO345OO', '/images/vehicules/OO345OO/img_vehicule.png', 'Seat', 'Ibiza', 2017, 1, 48.8566, 2.3522, '127.0.0.1'),
('PP678PP', '/images/vehicules/PP678PP/img_vehicule.png', 'Mini', 'Cooper', 2020, 1, 43.8566, 2.7522, '127.0.0.1'),
('QQ901QQ', '/images/vehicules/QQ901QQ/img_vehicule.png', 'Citroen', 'C3', 2018, 1, 44.8566, 2.2522, '127.0.0.1'),
('RR234RR', '/images/vehicules/RR234RR/img_vehicule.png', 'Dacia', 'Sandero', 2015, 1, 45.8566, 4.3522, '127.0.0.1'),
('SS567SS', '/images/vehicules/SS567SS/img_vehicule.png', 'Land Rover', 'Evoque', 2021, 1, 46.8566, 8.3522, '127.0.0.1'),
('TT890TT', '/images/vehicules/TT890TT/img_vehicule.png', 'Jeep', 'Renegade', 2019, 1, 47.8566, 2.9522, '127.0.0.1');
--
-- Déchargement des données de la table Reservations
--
INSERT INTO Reservations (id_user, id_vehicule, title, start, end) VALUES 
(1, 1, 'Reservation 1', '2024-06-01 08:00:00', '2024-06-01 10:00:00'),
(2, 2, 'Reservation 2', '2024-06-02 09:00:00', '2024-06-02 11:00:00'),
(3, 3, 'Reservation 3', '2024-06-03 10:00:00', '2024-06-03 12:00:00'),
(4, 4, 'Reservation 4', '2024-06-04 11:00:00', '2024-06-04 13:00:00'),
(5, 5, 'Reservation 5', '2024-06-05 12:00:00', '2024-06-05 14:00:00'),
(6, 6, 'Reservation 6', '2024-06-06 13:00:00', '2024-06-06 15:00:00'),
(7, 7, 'Reservation 7', '2024-06-07 14:00:00', '2024-06-07 16:00:00'),
(8, 8, 'Reservation 8', '2024-06-08 15:00:00', '2024-06-08 17:00:00'),
(9, 9, 'Reservation 9', '2024-06-09 16:00:00', '2024-06-09 18:00:00'),
(10, 10, 'Reservation 10', '2024-06-10 17:00:00', '2024-06-10 19:00:00'),
(1, 2, 'Reservation 11', '2024-07-01 08:00:00', '2024-07-01 10:00:00'),
(2, 3, 'Reservation 12', '2024-07-02 09:00:00', '2024-07-02 11:00:00'),
(3, 4, 'Reservation 13', '2024-07-03 10:00:00', '2024-07-03 12:00:00'),
(4, 5, 'Reservation 14', '2024-07-04 11:00:00', '2024-07-04 13:00:00'),
(5, 6, 'Reservation 15', '2024-07-05 12:00:00', '2024-07-05 14:00:00'),
(6, 7, 'Reservation 16', '2024-07-06 13:00:00', '2024-07-06 15:00:00'),
(7, 8, 'Reservation 17', '2024-07-07 14:00:00', '2024-07-07 16:00:00'),
(8, 9, 'Reservation 18', '2024-07-08 15:00:00', '2024-07-08 17:00:00'),
(9, 10, 'Reservation 19', '2024-07-09 16:00:00', '2024-07-09 18:00:00'),
(10, 1, 'Reservation 20', '2024-07-10 17:00:00', '2024-07-10 19:00:00'),
(1, 3, 'Reservation 21', '2024-08-01 08:00:00', '2024-08-01 10:00:00'),
(2, 4, 'Reservation 22', '2024-08-02 09:00:00', '2024-08-02 11:00:00'),
(3, 5, 'Reservation 23', '2024-08-03 10:00:00', '2024-08-03 12:00:00'),
(4, 6, 'Reservation 24', '2024-08-04 11:00:00', '2024-08-04 13:00:00'),
(5, 7, 'Reservation 25', '2024-08-05 12:00:00', '2024-08-05 14:00:00'),
(6, 8, 'Reservation 26', '2024-08-06 13:00:00', '2024-08-06 15:00:00'),
(7, 9, 'Reservation 27', '2024-08-07 14:00:00', '2024-08-07 16:00:00'),
(8, 10, 'Reservation 28', '2024-08-08 15:00:00', '2024-08-08 17:00:00'),
(9, 1, 'Reservation 29', '2024-08-09 16:00:00', '2024-08-09 18:00:00'),
(10, 2, 'Reservation 30', '2024-08-10 17:00:00', '2024-08-10 19:00:00');