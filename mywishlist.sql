SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `wishlist`
--

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `liste_id` int(11) NOT NULL,
                                      `nom` text NOT NULL,
                                      `descr` text,
                                      `img` text,
                                      `url` text,
                                      `tarif` decimal(5,2) DEFAULT NULL,
                                      PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `item`
--

INSERT INTO `item` (`id`, `liste_id`, `nom`, `descr`, `img`, `url`, `tarif`) VALUES
(1, 2, 'Champagne', 'Bouteille de champagne + flutes + jeux à gratter', 'champagne.jpg', '', '20.00'),
(2, 2, 'Musique', 'Partitions de piano à 4 mains', 'musique.jpg', '', '25.00'),
(3, 2, 'Exposition', 'Visite guidée de l’exposition ‘REGARDER’ à la galerie Poirel', 'poirelregarder.jpg', '', '14.00'),
(4, 3, 'Goûter', 'Goûter au FIFNL', 'gouter.jpg', '', '20.00'),
(5, 3, 'Projection', 'Projection courts-métrages au FIFNL', 'film.jpg', '', '10.00'),
(6, 2, 'Bouquet', 'Bouquet de roses et Mots de Marion Renaud', 'rose.jpg', '', '16.00'),
(7, 2, 'Diner Stanislas', 'Diner à La Table du Bon Roi Stanislas (Apéritif /Entrée / Plat / Vin / Dessert / Café / Digestif)', 'bonroi.jpg', '', '60.00'),
(8, 3, 'Origami', 'Baguettes magiques en Origami en buvant un thé', 'origami.jpg', '', '12.00'),
(9, 3, 'Livres', 'Livre bricolage avec petits-enfants + Roman', 'bricolage.jpg', '', '24.00'),
(10, 2, 'Diner  Grand Rue ', 'Diner au Grand’Ru(e) (Apéritif / Entrée / Plat / Vin / Dessert / Café)', 'grandrue.jpg', '', '59.00'),
(11, 0, 'Visite guidée', 'Visite guidée personnalisée de Saint-Epvre jusqu’à Stanislas', 'place.jpg', '', '11.00'),
(12, 2, 'Bijoux', 'Bijoux de manteau + Sous-verre pochette de disque + Lait après-soleil', 'bijoux.jpg', '', '29.00'),
(19, 25, 'Jeu contacts', 'Jeu pour échange de contacts', 'contact.png', '', '5.00'),
(22, 0, 'Concert', 'Un concert à Nancy', 'concert.jpg', '', '17.00'),
(23, 1, 'Appart Hotel', 'Appart’hôtel Coeur de Ville, en plein centre-ville', 'apparthotel.jpg', '', '56.00'),
(24, 2, 'Hôtel d\'Haussonville', 'Hôtel d\'Haussonville, au coeur de la Vieille ville à deux pas de la place Stanislas', 'hotel_haussonville_logo.jpg', '', '169.00'),
(25, 1, 'Boite de nuit', 'Discothèque, Boîte tendance avec des soirées à thème & DJ invités', 'boitedenuit.jpg', '', '32.00'),
(26, 1, 'Planètes Laser', 'Laser game : Gilet électronique et pistolet laser comme matériel, vous voilà équipé.', 'laser.jpg', '', '15.00'),
(27, 1, 'Fort Aventure', 'Découvrez Fort Aventure à Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut à l\'élastique inversé, Toboggan géant... et bien plus encore.', 'fort.jpg', '', '25.00');

-- --------------------------------------------------------

--
-- Structure de la table `liste`
--

DROP TABLE IF EXISTS `liste`;
CREATE TABLE IF NOT EXISTS `liste` (
                                       `no` int(11) NOT NULL AUTO_INCREMENT,
                                       `user_id` int(11) DEFAULT NULL,
                                       `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                       `description` text COLLATE utf8_unicode_ci,
                                       `expiration` date DEFAULT NULL,
                                       `token_edit` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                                       `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                                       PRIMARY KEY (`no`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `liste`
--

INSERT INTO `liste` (`no`, `user_id`, `titre`, `description`, `expiration`, `token_edit`, `token`) VALUES
(1, 1, 'Pour fêter le bac !', 'Pour un week-end à Nancy qui nous fera oublier les épreuves. ', '2018-06-27', 'edit1', 'share1'),
(2, 2, 'Liste de mariage d\'Alice et Bob', 'Nous souhaitons passer un week-end royal à Nancy pour notre lune de miel :)', '2020-03-19', 'edit2', 'share2'),
(3, 3, 'C\'est l\'anniversaire de Charlie', 'Pour lui préparer une fête dont il se souviendra :)', '2017-12-12', 'edit3', 'share3'),
(18, NULL, ' Je teste', 'Ceci est un test\r\n', '2020-04-23', 'e17666a4d79eb51f3b17', '9c8d698b1835b30e989f');


CREATE TABLE `message` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `liste_id` int(11) NOT NULL,
                           `message` text NOT NULL,
                           `expediteur` text,
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                             `item_id` int(11) NOT NULL,
                                             `nom` text COLLATE utf8_unicode_ci NOT NULL,
                                             `message` text COLLATE utf8_unicode_ci,
                                             PRIMARY KEY (`id`),
                                             UNIQUE KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                             `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                                             `prenom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                                             `mail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NOT NULL,
                                             `login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NOT NULL,
                                             `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NOT NULL,
                                             PRIMARY KEY (`id`)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
