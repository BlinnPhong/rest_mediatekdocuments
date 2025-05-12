-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 03, 2025 at 08:57 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: mediatek86
--

-- --------------------------------------------------------

--
-- Table structure for table abonnement
--

DROP TABLE IF EXISTS abonnement;
CREATE TABLE abonnement (
  id varchar(5) NOT NULL,
  dateFinAbonnement date DEFAULT NULL,
  idRevue varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table abonnement
--

INSERT INTO abonnement (id, dateFinAbonnement, idRevue) VALUES
('00010', '2025-05-01', '10002');

-- --------------------------------------------------------

--
-- Table structure for table commande
--

DROP TABLE IF EXISTS commande;
CREATE TABLE commande (
  id varchar(5) NOT NULL,
  dateCommande date DEFAULT NULL,
  montant double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table commande
--

INSERT INTO commande (id, dateCommande, montant) VALUES
('00010', '2020-04-04', 2),
('00020', '2025-04-24', 4),
('00025', '2025-04-24', 10);

-- --------------------------------------------------------

--
-- Table structure for table commandedocument
--

DROP TABLE IF EXISTS commandedocument;
CREATE TABLE commandedocument (
  id varchar(5) NOT NULL,
  nbExemplaire int DEFAULT NULL,
  idLivreDvd varchar(10) NOT NULL,
  idSuivi char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table commandedocument
--

INSERT INTO commandedocument (id, nbExemplaire, idLivreDvd, idSuivi) VALUES
('00020', 31, '20003', '00001'),
('00025', 1, '20003', '00004');

--
-- Triggers commandedocument
--
DROP TRIGGER IF EXISTS `onCommandeLivree`;
DELIMITER $$
CREATE TRIGGER `onCommandeLivree` AFTER UPDATE ON `commandedocument` FOR EACH ROW BEGIN
	DECLARE nbExemplaire INT;
    DECLARE i INT DEFAULT 0;
    DECLARE numeroExemplaire INT;
    DECLARE dateAchatExemplaire DATE;
	IF OLD.idSuivi <> '00003' AND NEW.idSuivi = '00003' THEN
		SET nbExemplaire = NEW.nbExemplaire;
		SET numeroExemplaire = (SELECT IFNULL(MAX(e.numero), 0) FROM exemplaire e WHERE e.id = NEW.idLivreDvd);
        SET dateAchatExemplaire = (SELECT c.dateCommande FROM commande c WHERE c.id = NEW.id);
        WHILE i < nbExemplaire DO
			SET numeroExemplaire = numeroExemplaire + 1;
			INSERT INTO exemplaire (id, numero, dateAchat, photo, idEtat)
            VALUES (NEW.idLivreDvd, numeroExemplaire, dateAchatExemplaire, '', '00001');
            SET i = i + 1;
		END WHILE;
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table document
--

DROP TABLE IF EXISTS document;
CREATE TABLE document (
  id varchar(10) NOT NULL,
  titre varchar(60) DEFAULT NULL,
  image varchar(500) DEFAULT NULL,
  idRayon varchar(5) NOT NULL,
  idPublic varchar(5) NOT NULL,
  idGenre varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table document
--

INSERT INTO document (id, titre, image, idRayon, idPublic, idGenre) VALUES
('00001', 'Quand sort la recluse', '', 'LV003', '00002', '10014'),
('00002', 'Un pays à l\'aube', '', 'LV001', '00002', '10004'),
('00003', 'Et je danse aussi', '', 'LV002', '00003', '10013'),
('00004', 'L\'armée furieuse', '', 'LV003', '00002', '10014'),
('00005', 'Les anonymes', '', 'LV001', '00002', '10014'),
('00006', 'La marque jaune', '', 'BD001', '00003', '10001'),
('00007', 'Dans les coulisses du musée', '', 'LV001', '00003', '10006'),
('00008', 'Histoire du juif errant', '', 'LV002', '00002', '10006'),
('00009', 'Pars vite et reviens tard', '', 'LV003', '00002', '10014'),
('00010', 'Le vestibule des causes perdues', '', 'LV001', '00002', '10006'),
('00011', 'L\'île des oubliés', '', 'LV002', '00003', '10006'),
('00012', 'La souris bleue', '', 'LV002', '00003', '10006'),
('00013', 'Sacré Pêre Noël', '', 'JN001', '00001', '10001'),
('00014', 'Mauvaise étoile', '', 'LV003', '00003', '10014'),
('00015', 'La confrérie des téméraires', '', 'JN002', '00004', '10014'),
('00016', 'Le butin du requin', '', 'JN002', '00004', '10014'),
('00017', 'Catastrophes au Brésil', '', 'JN002', '00004', '10014'),
('00018', 'Le Routard - Maroc', '', 'DV005', '00003', '10011'),
('00019', 'Guide Vert - Iles Canaries', '', 'DV005', '00003', '10011'),
('00020', 'Guide Vert - Irlande', '', 'DV005', '00003', '10011'),
('00021', 'Les déferlantes', '', 'LV002', '00002', '10006'),
('00022', 'Une part de Ciel', '', 'LV002', '00002', '10006'),
('00023', 'Le secret du janissaire', '', 'BD001', '00002', '10001'),
('00024', 'Pavillon noir', '', 'BD001', '00002', '10001'),
('00025', 'L\'archipel du danger', '', 'BD001', '00002', '10001'),
('00026', 'La planète des singes', '', 'LV002', '00003', '10002'),
('00030', 'ja', 'mid', 'DV003', '00003', '10011'),
('10001', 'Arts Magazine', '', 'PR002', '00002', '10016'),
('10002', 'Alternatives Economiques', '', 'PR002', '00002', '10015'),
('10003', 'Challenges', '', 'PR002', '00002', '10015'),
('10004', 'Rock and Folk', '', 'PR002', '00002', '10016'),
('10005', 'Les Echos', '', 'PR001', '00002', '10015'),
('10006', 'Le Monde', '', 'PR001', '00002', '10018'),
('10007', 'Telerama', '', 'PR002', '00002', '10016'),
('10008', 'L\'Obs', '', 'PR002', '00002', '10018'),
('10009', 'L\'Equipe', '', 'PR001', '00002', '10017'),
('10010', 'L\'Equipe Magazine', '', 'PR002', '00002', '10017'),
('10011', 'Geo', '', 'PR002', '00003', '10016'),
('20001', 'Star Wars 5 L\'empire contre attaque', '', 'DF001', '00003', '10002'),
('20002', 'Le seigneur des anneaux : la communauté de l\'anneau', '', 'DF001', '00003', '10019'),
('20003', 'Jurassic Park', '', 'DF001', '00003', '10002'),
('20004', 'Matrix', '', 'DF001', '00003', '10002');

-- --------------------------------------------------------

--
-- Table structure for table dvd
--

DROP TABLE IF EXISTS dvd;
CREATE TABLE dvd (
  id varchar(10) NOT NULL,
  synopsis text,
  realisateur varchar(20) DEFAULT NULL,
  duree int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table dvd
--

INSERT INTO dvd (id, synopsis, realisateur, duree) VALUES
('20001', 'Luc est entraîné par Yoda pendant que Han et Leia tentent de se cacher dans la cité des nuages.', 'George Lucas', 124),
('20002', 'L\'anneau unique, forgé par Sauron, est porté par Fraudon qui l\'amène à Foncombe. De là, des représentants de peuples différents vont s\'unir pour aider Fraudon à amener l\'anneau à la montagne du Destin.', 'Peter Jackson', 228),
('20003', 'Un milliardaire et des généticiens créent des dinosaures à partir de clonage.', 'Steven Spielberg', 128),
('20004', 'Un informaticien réalise que le monde dans lequel il vit est une simulation gérée par des machines.', 'Les Wachowski', 136);

-- --------------------------------------------------------

--
-- Table structure for table etat
--

DROP TABLE IF EXISTS etat;
CREATE TABLE etat (
  id char(5) NOT NULL,
  libelle varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table etat
--

INSERT INTO etat (id, libelle) VALUES
('00001', 'neuf'),
('00002', 'usagé'),
('00003', 'détérioré'),
('00004', 'inutilisable');

-- --------------------------------------------------------

--
-- Table structure for table exemplaire
--

DROP TABLE IF EXISTS exemplaire;
CREATE TABLE exemplaire (
  id varchar(10) NOT NULL,
  numero int NOT NULL,
  dateAchat date DEFAULT NULL,
  photo varchar(500) NOT NULL,
  idEtat char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table exemplaire
--

INSERT INTO exemplaire (id, numero, dateAchat, photo, idEtat) VALUES
('10002', 418, '2021-12-01', '', '00001'),
('10007', 3237, '2021-11-23', '', '00001'),
('10007', 3238, '2021-11-30', '', '00001'),
('10007', 3239, '2021-12-07', '', '00001'),
('10007', 3240, '2021-12-21', '', '00001'),
('10011', 505, '2022-10-16', '', '00001'),
('10011', 506, '2021-04-01', '', '00001'),
('10011', 507, '2021-05-03', '', '00001'),
('10011', 508, '2021-06-05', '', '00001'),
('10011', 509, '2021-07-01', '', '00001'),
('10011', 510, '2021-08-04', '', '00001'),
('10011', 511, '2021-09-01', '', '00001'),
('10011', 512, '2021-10-06', '', '00001'),
('10011', 513, '2021-11-01', '', '00001'),
('10011', 514, '2021-12-01', '', '00001'),
('20003', 1, '2025-04-24', '', '00001'),
('20003', 2, '2025-04-24', '', '00001');

-- --------------------------------------------------------

--
-- Table structure for table genre
--

DROP TABLE IF EXISTS genre;
CREATE TABLE genre (
  id varchar(5) NOT NULL,
  libelle varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table genre
--

INSERT INTO genre (id, libelle) VALUES
('10000', 'Humour'),
('10001', 'Bande dessinée'),
('10002', 'Science Fiction'),
('10003', 'Biographie'),
('10004', 'Historique'),
('10006', 'Roman'),
('10007', 'Aventures'),
('10008', 'Essai'),
('10009', 'Documentaire'),
('10010', 'Technique'),
('10011', 'Voyages'),
('10012', 'Drame'),
('10013', 'Comédie'),
('10014', 'Policier'),
('10015', 'Presse Economique'),
('10016', 'Presse Culturelle'),
('10017', 'Presse sportive'),
('10018', 'Actualités'),
('10019', 'Fantazy');

-- --------------------------------------------------------

--
-- Table structure for table livre
--

DROP TABLE IF EXISTS livre;
CREATE TABLE livre (
  id varchar(10) NOT NULL,
  ISBN varchar(13) DEFAULT NULL,
  auteur varchar(20) DEFAULT NULL,
  collection varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table livre
--

INSERT INTO livre (id, ISBN, auteur, collection) VALUES
('00001', '1234569877896', 'Fred Vargas', 'Commissaire Adamsberg'),
('00002', '1236547896541', 'Dennis Lehanne', ''),
('00003', '6541236987410', 'Anne-Laure Bondoux', ''),
('00004', '3214569874123', 'Fred Vargas', 'Commissaire Adamsberg'),
('00005', '3214563214563', 'RJ Ellory', ''),
('00006', '3213213211232', 'Edgar P. Jacobs', 'Blake et Mortimer'),
('00007', '6541236987541', 'Kate Atkinson', ''),
('00008', '1236987456321', 'Jean d\'Ormesson', ''),
('00009', '', 'Fred Vargas', 'Commissaire Adamsberg'),
('00010', '', 'Manon Moreau', ''),
('00011', '', 'Victoria Hislop', ''),
('00012', '', 'Kate Atkinson', ''),
('00013', '', 'Raymond Briggs', ''),
('00014', '', 'RJ Ellory', ''),
('00015', '', 'Floriane Turmeau', ''),
('00016', '', 'Julian Press', ''),
('00017', '', 'Philippe Masson', ''),
('00018', '', '', 'Guide du Routard'),
('00019', '', '', 'Guide Vert'),
('00020', '', '', 'Guide Vert'),
('00021', '', 'Claudie Gallay', ''),
('00022', '', 'Claudie Gallay', ''),
('00023', '', 'Ayrolles - Masbou', 'De cape et de crocs'),
('00024', '', 'Ayrolles - Masbou', 'De cape et de crocs'),
('00025', '', 'Ayrolles - Masbou', 'De cape et de crocs'),
('00026', '', 'Pierre Boulle', 'Julliard'),
('00030', '1234', 'so', 'av');

-- --------------------------------------------------------

--
-- Table structure for table livres_dvd
--

DROP TABLE IF EXISTS livres_dvd;
CREATE TABLE livres_dvd (
  id varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table livres_dvd
--

INSERT INTO livres_dvd (id) VALUES
('00001'),
('00002'),
('00003'),
('00004'),
('00005'),
('00006'),
('00007'),
('00008'),
('00009'),
('00010'),
('00011'),
('00012'),
('00013'),
('00014'),
('00015'),
('00016'),
('00017'),
('00018'),
('00019'),
('00020'),
('00021'),
('00022'),
('00023'),
('00024'),
('00025'),
('00026'),
('00030'),
('20001'),
('20002'),
('20003'),
('20004');

-- --------------------------------------------------------

--
-- Table structure for table public
--

DROP TABLE IF EXISTS public;
CREATE TABLE public (
  id varchar(5) NOT NULL,
  libelle varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table public
--

INSERT INTO public (id, libelle) VALUES
('00001', 'Jeunesse'),
('00002', 'Adultes'),
('00003', 'Tous publics'),
('00004', 'Ados');

-- --------------------------------------------------------

--
-- Table structure for table rayon
--

DROP TABLE IF EXISTS rayon;
CREATE TABLE rayon (
  id char(5) NOT NULL,
  libelle varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table rayon
--

INSERT INTO rayon (id, libelle) VALUES
('BD001', 'BD Adultes'),
('BL001', 'Beaux Livres'),
('DF001', 'DVD films'),
('DV001', 'Sciences'),
('DV002', 'Maison'),
('DV003', 'Santé'),
('DV004', 'Littérature classique'),
('DV005', 'Voyages'),
('JN001', 'Jeunesse BD'),
('JN002', 'Jeunesse romans'),
('LV001', 'Littérature étrangère'),
('LV002', 'Littérature française'),
('LV003', 'Policiers français étrangers'),
('PR001', 'Presse quotidienne'),
('PR002', 'Magazines');

-- --------------------------------------------------------

--
-- Table structure for table revue
--

DROP TABLE IF EXISTS revue;
CREATE TABLE revue (
  id varchar(10) NOT NULL,
  periodicite varchar(2) DEFAULT NULL,
  delaiMiseADispo int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table revue
--

INSERT INTO revue (id, periodicite, delaiMiseADispo) VALUES
('10001', 'MS', 52),
('10002', 'MS', 52),
('10003', 'HB', 15),
('10004', 'HB', 15),
('10005', 'QT', 5),
('10006', 'QT', 5),
('10007', 'HB', 26),
('10008', 'HB', 26),
('10009', 'QT', 5),
('10010', 'HB', 12),
('10011', 'MS', 52);

-- --------------------------------------------------------

--
-- Table structure for table service
--

DROP TABLE IF EXISTS service;
CREATE TABLE service (
  id int NOT NULL,
  nom varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table service
--

INSERT INTO service (id, nom) VALUES
(1, 'administratif'),
(2, 'prêts'),
(3, 'culture');

-- --------------------------------------------------------

--
-- Table structure for table suivi
--

DROP TABLE IF EXISTS suivi;
CREATE TABLE suivi (
  id char(5) NOT NULL,
  libelle varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table suivi
--

INSERT INTO suivi (id, libelle) VALUES
('00001', 'en cours'),
('00002', 'relancée'),
('00003', 'livrée'),
('00004', 'réglée');

-- --------------------------------------------------------

--
-- Table structure for table utilisateur
--

DROP TABLE IF EXISTS utilisateur;
CREATE TABLE utilisateur (
  id int NOT NULL,
  login varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  pwd varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  idService int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table utilisateur
--

INSERT INTO utilisateur (id, login, pwd, idService) VALUES
(1, 'Eric', '4cb39c1bdf5da40bb629d3fb041a2af0c9d388370c9e5ea5369f01aa23b02589', 1),
(2, 'Kyle', 'b9ecfab5789d7b96f8765c147fff488e9946ede2797a4ec149c2cc5b35054799', 2),
(3, 'Stan', '317ecd32308a1385e1d3cef0744eafbeb55366d15e2ae711085532132795675e', 2),
(4, 'Butters', '7978652e3a882f64c2d4e175059f4b886df93d272764f092fe59068f8dc60867', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table abonnement
--
ALTER TABLE abonnement
  ADD PRIMARY KEY (id),
  ADD KEY idRevue (idRevue);

--
-- Indexes for table commande
--
ALTER TABLE commande
  ADD PRIMARY KEY (id);

--
-- Indexes for table commandedocument
--
ALTER TABLE commandedocument
  ADD PRIMARY KEY (id),
  ADD KEY idLivreDvd (idLivreDvd),
  ADD KEY commandedocument_ibfk_3 (idSuivi);

--
-- Indexes for table document
--
ALTER TABLE document
  ADD PRIMARY KEY (id),
  ADD KEY idRayon (idRayon),
  ADD KEY idPublic (idPublic),
  ADD KEY idGenre (idGenre);

--
-- Indexes for table dvd
--
ALTER TABLE dvd
  ADD PRIMARY KEY (id);

--
-- Indexes for table etat
--
ALTER TABLE etat
  ADD PRIMARY KEY (id);

--
-- Indexes for table exemplaire
--
ALTER TABLE exemplaire
  ADD PRIMARY KEY (id,numero),
  ADD KEY idEtat (idEtat);

--
-- Indexes for table genre
--
ALTER TABLE genre
  ADD PRIMARY KEY (id);

--
-- Indexes for table livre
--
ALTER TABLE livre
  ADD PRIMARY KEY (id);

--
-- Indexes for table livres_dvd
--
ALTER TABLE livres_dvd
  ADD PRIMARY KEY (id);

--
-- Indexes for table public
--
ALTER TABLE public
  ADD PRIMARY KEY (id);

--
-- Indexes for table rayon
--
ALTER TABLE rayon
  ADD PRIMARY KEY (id);

--
-- Indexes for table revue
--
ALTER TABLE revue
  ADD PRIMARY KEY (id);

--
-- Indexes for table service
--
ALTER TABLE service
  ADD PRIMARY KEY (id);

--
-- Indexes for table suivi
--
ALTER TABLE suivi
  ADD PRIMARY KEY (id);

--
-- Indexes for table utilisateur
--
ALTER TABLE utilisateur
  ADD PRIMARY KEY (id),
  ADD KEY utilisateur_ibfk_1 (idService);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table service
--
ALTER TABLE service
  MODIFY id int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table utilisateur
--
ALTER TABLE utilisateur
  MODIFY id int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table abonnement
--
ALTER TABLE abonnement
  ADD CONSTRAINT abonnement_ibfk_1 FOREIGN KEY (id) REFERENCES commande (id),
  ADD CONSTRAINT abonnement_ibfk_2 FOREIGN KEY (idRevue) REFERENCES revue (id);

--
-- Constraints for table commandedocument
--
ALTER TABLE commandedocument
  ADD CONSTRAINT commandedocument_ibfk_1 FOREIGN KEY (id) REFERENCES commande (id),
  ADD CONSTRAINT commandedocument_ibfk_2 FOREIGN KEY (idLivreDvd) REFERENCES livres_dvd (id),
  ADD CONSTRAINT commandedocument_ibfk_3 FOREIGN KEY (idSuivi) REFERENCES suivi (id);

--
-- Constraints for table document
--
ALTER TABLE document
  ADD CONSTRAINT document_ibfk_1 FOREIGN KEY (idRayon) REFERENCES rayon (id),
  ADD CONSTRAINT document_ibfk_2 FOREIGN KEY (idPublic) REFERENCES public (id),
  ADD CONSTRAINT document_ibfk_3 FOREIGN KEY (idGenre) REFERENCES genre (id);

--
-- Constraints for table dvd
--
ALTER TABLE dvd
  ADD CONSTRAINT dvd_ibfk_1 FOREIGN KEY (id) REFERENCES livres_dvd (id);

--
-- Constraints for table exemplaire
--
ALTER TABLE exemplaire
  ADD CONSTRAINT exemplaire_ibfk_1 FOREIGN KEY (id) REFERENCES document (id),
  ADD CONSTRAINT exemplaire_ibfk_2 FOREIGN KEY (idEtat) REFERENCES etat (id);

--
-- Constraints for table livre
--
ALTER TABLE livre
  ADD CONSTRAINT livre_ibfk_1 FOREIGN KEY (id) REFERENCES livres_dvd (id);

--
-- Constraints for table livres_dvd
--
ALTER TABLE livres_dvd
  ADD CONSTRAINT livres_dvd_ibfk_1 FOREIGN KEY (id) REFERENCES document (id);

--
-- Constraints for table revue
--
ALTER TABLE revue
  ADD CONSTRAINT revue_ibfk_1 FOREIGN KEY (id) REFERENCES document (id);

--
-- Constraints for table utilisateur
--
ALTER TABLE utilisateur
  ADD CONSTRAINT utilisateur_ibfk_1 FOREIGN KEY (idService) REFERENCES service (id);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
