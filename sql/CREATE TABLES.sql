use CARIA;
DROP TABLE IF EXISTS Reservations,Clients,Vehicules;

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
  mdp varchar(32) NOT NULL,
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
  image varchar(250) NOT NULL DEFAULT '/images/vehicules/img_voiture.png',
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
  end timestamp NULL DEFAULT NULL
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;





use CARIA;
--
-- Déchargement des données de la table Clients
--
INSERT INTO Clients (privilege, dateenregistre, pseudo, prenom, nom, adresse, mdp, email, avatar, phone) VALUES
(1, '2016-09-11 22:00:00', 'sa', 'sa', 'pc', 'sa', 'sa', 'sa@gmail.com', '/images/avatars/sa/img_user.jpg', "0658293476"),
(2, NOW(), 'utilisateur1', 'John', 'Doe', '123 Rue de Paris', 'motdepasse1', 'john.doe@example.com', '/images/avatars/utilisateur1/img_user.jpg', '0123456789'),
(2, NOW(), 'utilisateur2', 'Alice', 'Smith', '456 Avenue des Champs-Élysées', 'motdepasse2', 'alice.smith@example.com', '/images/avatars/utilisateur2/img_user.jpg', '9876543210'),
(2, NOW(), 'utilisateur3', 'Michael', 'Johnson', '789 Boulevard Saint-Michel', 'motdepasse3', 'michael.johnson@example.com', '/images/avatars/utilisateur3/img_user.jpg', '1234567890'),
(2, NOW(), 'utilisateur4', 'Emma', 'Brown', '1011 Rue de Rivoli', 'motdepasse4', 'emma.brown@example.com', '/images/avatars/utilisateur4/img_user.jpg', '0987654321'),
(2, NOW(), 'utilisateur5', 'David', 'Wilson', '1213 Rue du Faubourg Saint-Honoré', 'motdepasse5', 'david.wilson@example.com', '/images/avatars/utilisateur5/img_user.jpg', '1122334455'),
(2, NOW(), 'utilisateur6', 'Sophia', 'Martinez', '1415 Avenue Montaigne', 'motdepasse6', 'sophia.martinez@example.com', '/images/avatars/utilisateur6/img_user.jpg', '5544332211'),
(2, NOW(), 'utilisateur7', 'Matthew', 'Anderson', '1617 Avenue de la Grande Armée', 'motdepasse7', 'matthew.anderson@example.com', '/images/avatars/utilisateur7/img_user.jpg', '6677889900'),
(2, NOW(), 'utilisateur8', 'Olivia', 'Taylor', '1819 Rue de la Paix', 'motdepasse8', 'olivia.taylor@example.com', '/images/avatars/utilisateur8/img_user.jpg', '7788990011'),
(2, NOW(), 'utilisateur9', 'Daniel', 'Thomas', '2021 Boulevard Haussmann', 'motdepasse9', 'daniel.thomas@example.com', '/images/avatars/utilisateur9/img_user.jpg', '8899001122'),
(2, NOW(), 'utilisateur10', 'Emily', 'Jackson', '2223 Avenue Victor Hugo', 'motdepasse10', 'emily.jackson@example.com', '/images/avatars/utilisateur10/img_user.jpg', '9900112233'),
(2, NOW(), 'utilisateur11', 'Sophie', 'Martin', '2325 Rue Saint-Antoine', 'motdepasse11', 'sophie.martin@example.com', '/images/avatars/utilisateur11/img_user.jpg', '1122334455'),
(2, NOW(), 'utilisateur12', 'William', 'Thompson', '2427 Avenue des Ternes', 'motdepasse12', 'william.thompson@example.com', '/images/avatars/utilisateur12/img_user.jpg', '2244668899'),
(2, NOW(), 'utilisateur13', 'Charlotte', 'Clark', '2529 Rue de la Pompe', 'motdepasse13', 'charlotte.clark@example.com', '/images/avatars/utilisateur13/img_user.jpg', '3366990022'),
(2, NOW(), 'utilisateur14', 'James', 'White', '2631 Boulevard Malesherbes', 'motdepasse14', 'james.white@example.com', '/images/avatars/utilisateur14/img_user.jpg', '4488112233'),
(2, NOW(), 'utilisateur15', 'Ava', 'Hall', '2733 Avenue de la République', 'motdepasse15', 'ava.hall@example.com', '/images/avatars/utilisateur15/img_user.jpg', '5511223344'),
(2, NOW(), 'utilisateur16', 'Noah', 'Lewis', '2835 Rue de Passy', 'motdepasse16', 'noah.lewis@example.com', '/images/avatars/utilisateur16/img_user.jpg', '6633445566'),
(2, NOW(), 'utilisateur17', 'Mia', 'Adams', '2937 Boulevard Saint-Germain', 'motdepasse17', 'mia.adams@example.com', '/images/avatars/utilisateur17/img_user.jpg', '7755668899'),
(2, NOW(), 'utilisateur18', 'Benjamin', 'Young', '3039 Rue du Bac', 'motdepasse18', 'benjamin.young@example.com', '/images/avatars/utilisateur18/img_user.jpg', '8877665544'),
(2, NOW(), 'utilisateur19', 'Ella', 'Harris', '3141 Avenue Marceau', 'motdepasse19', 'ella.harris@example.com', '/images/avatars/utilisateur19/img_user.jpg', '9900887766'),
(2, NOW(), 'utilisateur20', 'Lucas', 'King', '3243 Rue de Sèvres', 'motdepasse20', 'lucas.king@example.com', '/images/avatars/utilisateur20/img_user.jpg', '0011223344');

--
-- Déchargement des données de la table Vehicules
--
INSERT INTO Vehicules (plaque, image, marque, modele, annee, disponible, latitude, longitude, ip) VALUES
("GH209RH", '/images/vehicules/GH209RH/img_voiture.png', 'Tesla', 'Model S', 2023, 1, 48.358844, 3.294351, '192.168.74.194'),
("PH940OP", '/images/vehicules/PH940OP/img_voiture.png', 'Nissan', 'Leaf', 2022, 1, 48.8566, 6.3522, '127.0.0.1'),
("XD738UJ", '/images/vehicules/XD738UJ/img_voiture.png', 'BMW', 'i3 S', 2024, 0, 48.6566, 2.3522, '127.0.0.1'),
("WSD754YH", '/images/vehicules/WSD754YH/img_voiture.png', 'Toyota', 'Prius', 2023, 1, 48.0566, 3.3522, '127.0.0.1'),
("IO334PO", '/images/vehicules/IO334PO/img_voiture.png', 'Chevrolet', 'Bolt', 2023, 0, 48.7566, 7.3522, '127.0.0.1'),
('AA123AA', '/images/vehicules/AA123AA/img_voiture.png', 'Renault', 'Clio', 2018, 1, 48.558844, 2.294351, '127.0.0.1'),
('BB456BB', '/images/vehicules/BB456BB/img_voiture.png', 'Peugeot', '208', 2016, 1, 48.2566, 1.3522, '127.0.0.1'),
('CC789CC', '/images/vehicules/CC789CC/img_voiture.png', 'Volkswagen', 'Golf', 2019, 1, 48.8566, 9.3522, '127.0.0.1'),
('DD012DD', '/images/vehicules/DD012DD/img_voiture.png', 'Ford', 'Fiesta', 2015, 1, 49.8566, 6.3522, '127.0.0.1'),
('EE345EE', '/images/vehicules/EE345EE/img_voiture.png', 'Toyota', 'Yaris', 2017, 1, 45.8566, 4.3522, '127.0.0.1'),
('FF678FF', '/images/vehicules/FF678FF/img_voiture.png', 'Audi', 'A3', 2014, 1, 42.8566, 2.3522, '127.0.0.1'),
('GG901GG', '/images/vehicules/GG901GG/img_voiture.png', 'BMW', 'Series 1', 2020, 1, 46.8566, 3.3522, '127.0.0.1'),
('HH234HH', '/images/vehicules/HH234HH/img_voiture.png', 'Mercedes-Benz', 'A-Class', 2019, 1, 48.8566, 2.3522, '127.0.0.1'),
('II567II', '/images/vehicules/II567II/img_voiture.png', 'Hyundai', 'i30', 2016, 1, 50.8566, 7.3522, '127.0.0.1'),
('JJ890JJ', '/images/vehicules/JJ890JJ/img_voiture.png', 'Kia', 'Rio', 2017, 1, 49.8566, 4.3522, '127.0.0.1'),
('KK123KK', '/images/vehicules/KK123KK/img_voiture.png', 'Nissan', 'Micra', 2018, 1, 44.8566, 2.3522, '127.0.0.1'),
('LL456LL', '/images/vehicules/LL456LL/img_voiture.png', 'Fiat', '500', 2015, 1, 43.8566, 2.6522, '127.0.0.1'),
('MM789MM', '/images/vehicules/MM789MM/img_voiture.png', 'Skoda', 'Fabia', 2019, 1, 47.8566, 2.8522, '127.0.0.1'),
('NN012NN', '/images/vehicules/NN012NN/img_voiture.png', 'Volvo', 'V40', 2016, 1, 45.8566, 2.4522, '127.0.0.1'),
('OO345OO', '/images/vehicules/OO345OO/img_voiture.png', 'Seat', 'Ibiza', 2017, 1, 48.8566, 2.3522, '127.0.0.1'),
('PP678PP', '/images/vehicules/PP678PP/img_voiture.png', 'Mini', 'Cooper', 2020, 1, 43.8566, 2.7522, '127.0.0.1'),
('QQ901QQ', '/images/vehicules/QQ901QQ/img_voiture.png', 'Citroen', 'C3', 2018, 1, 44.8566, 2.2522, '127.0.0.1'),
('RR234RR', '/images/vehicules/RR234RR/img_voiture.png', 'Dacia', 'Sandero', 2015, 1, 45.8566, 4.3522, '127.0.0.1'),
('SS567SS', '/images/vehicules/SS567SS/img_voiture.png', 'Land Rover', 'Evoque', 2021, 1, 46.8566, 8.3522, '127.0.0.1'),
('TT890TT', '/images/vehicules/TT890TT/img_voiture.png', 'Jeep', 'Renegade', 2019, 1, 47.8566, 2.9522, '127.0.0.1');
--
-- Déchargement des données de la table Reservations
--
INSERT INTO Reservations (id_user, id_vehicule, title, start, end)
SELECT 
    FLOOR(RAND() * (SELECT MAX(id) FROM Clients)) + 1 AS id_user,
    FLOOR(RAND() * (SELECT MAX(id) FROM Vehicules)) + 1 AS id_vehicule,
    CONCAT('Réservation ', FLOOR(RAND() * 100)) AS title,
    NOW() + INTERVAL FLOOR(RAND() * 30) DAY AS start,
    NOW() + INTERVAL FLOOR(RAND() * 30) DAY + INTERVAL FLOOR(RAND() * 10) HOUR AS end
FROM
    INFORMATION_SCHEMA.TABLES AS t1,
    INFORMATION_SCHEMA.TABLES AS t2
LIMIT 30;
