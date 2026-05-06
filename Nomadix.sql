-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 06 mai 2026 à 10:10
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nomadix`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int NOT NULL,
  `idDestination` int NOT NULL,
  `note` int NOT NULL,
  `commentaire` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dateAvis` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verified` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idDestination` (`idDestination`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `idUtilisateur`, `idDestination`, `note`, `commentaire`, `dateAvis`, `verified`) VALUES
(1, 1, 1, 5, 'Incroyable, surtout la nuit !', '2025-05-12 10:44:26', 1),
(2, 2, 2, 4, 'Très impressionnant mais un peu bondé.', '2025-05-12 10:44:26', 1),
(3, 3, 3, 5, 'Un voyage dans le temps !', '2025-05-12 10:44:26', 1),
(4, 1, 3, 4, 'Magnifique architecture.', '2025-05-12 10:44:26', 1),
(5, 2, 1, 3, 'Belle vue, mais beaucoup de monde.', '2025-05-12 10:44:26', 0),
(6, 6, 4, 3, 'Super endroit à visiter !', '2025-05-16 10:44:25', 1),
(7, 12, 4, 4, 'Le Taj Mahal est un chef-d\'œuvre architectural impressionnant, symbole d\'amour éternel. Son marbre blanc, ses jardins symétriques et son histoire émouvante en font une destination incontournable en Inde.', '2025-05-16 10:46:10', 1),
(8, 12, 1, 5, 'Réservation pour le sommet impossible à J-60 : complet. Mais quelques jours avant ,oh miracle,beaucoup de places ont été disponibles sans doute redonnés par des tours opérateurs. J ai ainsi constaté que chaque jour, un jour était libéré, mais vite pris. Sinon pour nous, peu d\'attente avec nos réservations. Un peu plus long pour le 2é ascenseur alors que c’était les vacances scolaires. Paysage vraiment plus impressionnant du sommet !', '2025-05-16 12:26:37', 1),
(9, 3, 1, 5, 'Beau pays , ma belle france\r\n', '2025-05-23 07:41:07', 1),
(14, 1, 2, 5, '', '2025-05-21 12:03:37', 1),
(19, 16, 1, 5, 'Ma belle france', '2025-05-23 07:31:16', 1),
(31, 6, 3, 4, 'Super ! J\'étais petite lorsque j\'y suis allée et j\'en garde de super souvenirs. Le seul problème c\'est les personnes qui arnaque les gens.', '2025-05-25 20:52:53', 1),
(32, 28, 3, 4, 'Superbe !', '2026-05-05 16:23:15', 1),
(34, 28, 2, 5, 'Super haut !', '2026-05-05 17:13:25', 1),
(35, 30, 9, 5, 'tellement impressionnant ces constructions sachant qu&#039;il n&#039;y avait aucune machine et technologie pour aider les maçons', '2026-05-05 19:29:39', 0),
(36, 31, 10, 5, 'J&#039;y suis allé l&#039;année dernière (dans mes rêves), c&#039;était super!!!!!!!!!!!!!!!!', '2026-05-05 21:20:44', 0),
(37, 31, 18, 5, 'En temps que sœur de cette statue, je peux dire que son rire fait peur. Elle sent mauvais. Et elle est très sage ça va on l&#039;entend pas contrairement à elle dans la vrai vie. Plus belle en statue qu&#039;en vrai.\r\nJ&#039;ai mis une bonne note car c&#039;est moi qui ai eu l&#039;idée de la mettre en statue super comme idée!!!!\r\nJe suis la meilleure, pas besoin de me le dire, je le sais. Bisous les copains!!!!\r\nA la prochaine.', '2026-05-05 21:42:52', 0);

-- --------------------------------------------------------

--
-- Structure de la table `destinations`
--

DROP TABLE IF EXISTS `destinations`;
CREATE TABLE IF NOT EXISTS `destinations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pays` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ville` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `destinations`
--

INSERT INTO `destinations` (`id`, `nom`, `description`, `pays`, `ville`, `image`) VALUES
(1, 'Tour Eiffel', 'La Tour Eiffel est un monument célèbre situé à Paris, en France . Construite par Gustave Eiffel pour l’Exposition universelle de 1889, elle mesure environ 330 mètres de haut.\n\nAvec ses trois étages ouverts au public, elle offre une vue spectaculaire sur Paris. La Tour Eiffel attire chaque année des millions de visiteurs venus du monde entier, ce qui en fait l’un des monuments les plus visités et appréciés au monde.', 'France', 'Paris', 'https://images.unsplash.com/photo-1570097703229-b195d6dd291f?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8dG91ciUyMGVpZmZlbHxlbnwwfHwwfHx8MA%3D%3D'),
(2, 'Statue de la Liberté', 'La Statue de la Liberté est un monument emblématique situé à New York, aux États-Unis. Offerte par la France en 1886, elle symbolise la liberté et l’amitié entre les deux pays.\n\nCette grande statue en cuivre mesure environ 93 mètres de hauteur (avec son socle) et représente une femme tenant une torche et une tablette.\n\nElle attire des millions de visiteurs chaque année, qui viennent admirer sa grandeur, son histoire et la vue magnifique sur la baie de New York depuis Liberty Island.', 'États-Unis', 'New York', 'https://images.pexels.com/photos/2179602/pexels-photo-2179602.jpeg'),
(3, 'Colisée', 'Le Colisée est un ancien amphithéâtre situé à Rome, en Italie. Construit il y a près de 2 000 ans, c’était le plus grand stade de l’Antiquité, utilisé pour des combats de gladiateurs et des spectacles publics.\n\nCe monument impressionnant peut accueillir des dizaines de milliers de spectateurs. Aujourd’hui, il est l’un des sites touristiques les plus visités au monde, attirant des millions de personnes chaque année grâce à son histoire fascinante et son architecture spectaculaire.', 'Italie', 'Rome', 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y29saXMlQzMlQTllfGVufDB8fDB8fHww'),
(4, 'Taj Mahal', 'Mausolée emblématique construit en marbre blanc.', 'Inde', 'Agra', 'https://static.nationalgeographic.fr/files/styles/image_3200/public/taj-mahal.jpg?w=1900&h=1188'),
(5, 'Machu Picchu', 'Ancienne cité inca perchée dans les Andes.', 'Pérou', 'Cusco', 'https://images5.alphacoders.com/361/thumb-1920-361088.jpg'),
(6, 'Grande Muraille de Chine', 'Monument historique s’étendant sur des milliers de kilomètres.', 'Chine', 'Pékin', 'https://images.unsplash.com/photo-1608037521277-154cd1b89191?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Z3JhbmRlJTIwbXVyYWlsbGUlMjBkZSUyMGNoaW5lfGVufDB8fDB8fHww'),
(7, 'Christ Rédempteur', 'Statue célèbre surplombant Rio de Janeiro.', 'Brésil', 'Rio de Janeiro', 'https://www.voyagebresil.com/cdn/br-public/s_bresil_rio_de_janeiro_christ_the_redeemer.jpg'),
(8, 'Sagrada Família', 'Basilique emblématique conçue par Gaudí.', 'Espagne', 'Barcelone', 'https://wallpapercat.com/w/full/e/c/b/783968-2800x1575-desktop-hd-sagrada-familia-wallpaper-photo.jpg'),
(9, 'Pyramides de Gizeh', 'Trésors de l’Égypte antique toujours debout.', 'Égypte', 'Gizeh', 'https://plus.unsplash.com/premium_photo-1697730240200-e8b757834c7e?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1yZWxhdGVkfDEyfHx8ZW58MHx8fHx8'),
(10, 'Mont Fuji', 'Volcan emblématique et sacré du Japon.', 'Japon', 'Honshu', 'https://images8.alphacoders.com/609/609072.jpg'),
(11, 'Cap de Bonne-Espérance', 'Lieu mythique entre deux océans.', 'Afrique du Sud', 'Le Cap', 'https://upload.wikimedia.org/wikipedia/commons/2/24/Playa_Dias%2C_Cape_Point%2C_Sud%C3%A1frica%2C_2018-07-23%2C_DD_92.jpg'),
(18, 'Statue de la Daphné', 'Cheveux noir, court. Lunettes bleues ainsi que des boucles d\'oreilles créole en argent rose. Elle a un rire, un rire ....laissez tomber vous verrez par vous même (le son du vent qui traverse la statue ). Elle porte un collier. Elle fait  1,66m. Elle est mal habillée et sent mauvais. Je vais vous laissez le découvrir par vous même. BISOUS LES COPAINS !!!!!!', 'Daphnéland', 'Daphnécity', 'https://scontent.frun3-2.fna.fbcdn.net/v/t1.6435-9/71491952_410244206539497_7035606594960228352_n.jpg?stp=dst-jpg_p526x296_tt6&_nc_cat=108&ccb=1-7&_nc_sid=53a332&_nc_ohc=fho0-t3nuDEQ7kNvwGLXGmc&_nc_oc=Adpf7-3bwB0f5v5XlXzQwkqsGMXXc1pnzzGSNxXmDtdQ74NT9lLyeZKWb9Gcytxjfj4&_nc_zt=23&_nc_ht=scontent.frun3-2.fna&_nc_gid=GnNSHzeQOiAayudbPuNMYA&_nc_ss=7b289&oh=00_Af5aDxmZxrdZApqJv8jXjOlnsxh2F90FnB3L2cXjiBibLQ&oe=6A21979E');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `motDePasse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dateCreation` datetime DEFAULT CURRENT_TIMESTAMP,
  `login_changed_at` datetime DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `login`, `motDePasse`, `email`, `dateCreation`, `login_changed_at`, `avatar`, `admin`) VALUES
(1, 'alice', '$2y$10$yDBsU/c9fS8cK2j8XdAkW.zkMr9VGwDeSnDNjRLIi6eewDv6VQG/2', 'alice@example.com', '2025-05-12 10:44:26', NULL, NULL, 0),
(2, 'bob', '$2y$10$iDwu1C0kWRu8XDYjd5EzSuadXUdZw5fYXmoAu5XMviHJg1kw4wU7.', 'bob@example.com', '2025-05-12 10:44:26', NULL, NULL, 0),
(3, 'charlie', '$2y$10$Vuo0MgqL3J09TgejsPx7v.li.arqWE197IwiTsmUmH27zmQVhunRW', 'charlie55000@example.com', '2025-05-12 10:44:26', NULL, NULL, 0),
(4, 'Lori', '$2y$10$IgYYXAmQyNXZZVJpROSq9uPWSLkeLkELgfexuJEdcC9T7Dy8lp6iu', 'Lori55@gmail.com', '2025-05-15 11:47:12', NULL, NULL, 0),
(5, 'Lola', '$2y$10$WAOOcXfjmdd8.EcTZX1Sl.MScrK21Km9lcPCKcwtOPVrp6sEYbkM2', 'Lola78@gmail.com', '2025-05-15 11:59:45', NULL, NULL, 0),
(6, 'Daphné', '$2y$10$fU1wABvU/fPDYCohFD0Xp.XAGLGcdyvzeqfqFh/sk9iZKa03I1wMi', 'daphneliret@gmail.com', '2025-05-15 12:02:03', NULL, NULL, 0),
(12, 'lola', '$2y$10$RRgzneU.7PVeXvFnU1ZUVeiSD4EVbefCEDNL0u1/Ok.9PJ3XOI6zK', 'lola@bdye.com', '2025-05-15 13:55:34', NULL, NULL, 0),
(14, 'Laura', '$2y$10$eUxNt6s11AIlcSwplveE3uQc7ZKrFV2RSSWXyvF3O1NDh68BGz54a', 'Laura10@gmail.com', '2025-05-21 11:32:39', NULL, NULL, 0),
(16, 'oui', '$2y$10$otiTBFkSJAUdhSxH1VVwqOoDAYKfZvlfDqh8PMFeQZXoXSdaROy/a', 'oui72@gmail.com', '2025-05-23 07:29:24', NULL, NULL, 0),
(17, 'oYI', '$2y$10$s0OWJrZrJJP5q34R9AHD9uKz2LvhCD2sHTPBaHPB2lzejTirimdxa', 'daphneliret9@gmail.com', '2025-05-23 07:45:11', NULL, NULL, 0),
(18, 'dada', '$2y$10$kBCoezd2wp0Xe1dx737pLui6ZPITmfLD7pkDp7b3FO2o7pAdfwd1e', 'cmoidada@gmail.com', '2025-05-23 07:48:04', NULL, NULL, 0),
(19, 'Roka', '$2y$10$Y4pnx6rI1Dn2hG1fhKLRF.SNYuKOBNUyWiUx4EgzwRk8jzYifmnIa', 'Roka@gmail.com', '2025-05-23 07:53:24', NULL, NULL, 0),
(20, 'Lori', '$2y$10$DM396FR.7h3K.BVcOE3hsu5mSA0d9cfKTV0WXqOLw5vamE2sPxOWe', 'Lori@gmail.com', '2025-05-23 07:58:39', NULL, NULL, 0),
(23, 'Laura', '$2y$10$z/q9LDEsIO7GuFCmTOTcv.lxqwKnPA0xgyciX3a0.38Eprbpwabku', 'laura@gmail.com', '2025-05-25 19:38:25', NULL, NULL, 0),
(24, 'daphné', '$2y$10$LpgdYdt7A1yvxUy7vKLso.BysgZ89nEhA1OmSP7/bTBu1dIo93Ode', 'oui@ouioui.fr', '2025-05-25 20:49:25', NULL, NULL, 0),
(25, 'Lise', '$2y$10$v9.MUHHVEtgzX/xrTAg8be4v5ziTk6gsbJI4/svL.MSXx3rhGWAuG', 'lise@gmail.com', '2025-05-26 10:24:49', NULL, NULL, 0),
(26, 'Morine', '$2y$10$9Yh6FSfgLKv.SffrUmzeJ.TL5Duko3Fak6MSDHSUgG8rQUEp1iBr6', 'morine@gmail.com', '2025-05-26 11:39:24', NULL, NULL, 0),
(27, 'dadapiou', '$2y$10$G7FvW6FGEPjYfsIGItMcp.49GIQ7DaayynQbbjy3xHI.fItzQnkJy', 'dadapiou@gmail.com', '2026-04-20 06:25:13', NULL, NULL, 1),
(28, 'bryan', '$2y$10$QTeRowaqdna1tLLnRnTuhex07UJ1YtJE/Jri5Wy5yFjHgVW.nmGYi', 'bryan@gmail.com', '2026-04-23 08:55:41', NULL, 'public/profil/temple.svg', 1),
(29, 'tablette', '$2y$10$DDNGlgCTDviNsgVaYzFLo.4scfjeTPkcA5ldOzscAAYiaPQG.hqPK', 'chocolat@gmail.com', '2026-04-23 09:52:52', NULL, NULL, 0),
(30, 'vivi7181', '$2y$10$owuBOGGvdsoJIRKTO1pJUOhUUK309Wyxby8IoPcb/cqmZptU6QkFC', 'vivi7181@yahoo.fr', '2026-05-05 19:28:16', NULL, 'public/profil/desert.svg', 0),
(31, 'Ta soeur préférée', '$2y$10$rCTA9gGVv31YKE4TK1RWyu4OljqjFBO5S0gcXFS0D11STaq7VEhty', 'noemie.liret@nat-immobilier.fr', '2026-05-05 21:19:37', NULL, 'public/profil/montagne.svg', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
