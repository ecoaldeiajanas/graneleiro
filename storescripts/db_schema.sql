/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table block
# ------------------------------------------------------------

DROP TABLE IF EXISTS `block`;

CREATE TABLE `block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id_category` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table delivery
# ------------------------------------------------------------

DROP TABLE IF EXISTS `delivery`;

CREATE TABLE `delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `local` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `hora` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table encomenda
# ------------------------------------------------------------

DROP TABLE IF EXISTS `encomenda`;

CREATE TABLE `encomenda` (
  `id_encomenda` int(11) NOT NULL AUTO_INCREMENT,
  `id_people` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `semana` int(11) NOT NULL,
  PRIMARY KEY (`id_encomenda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table encomenda_has_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `encomenda_has_products`;

CREATE TABLE `encomenda_has_products` (
  `id_encomenda` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `id_produtor` int(11) NOT NULL,
  `quant` decimal(10,2) NOT NULL,
  KEY `id_encomenda` (`id_encomenda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table encomendaprodutor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `encomendaprodutor`;

CREATE TABLE `encomendaprodutor` (
  `id_encomendaProdutor` int(11) NOT NULL AUTO_INCREMENT,
  `id_people` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id_encomendaProdutor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table encomendaprodutor_has_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `encomendaprodutor_has_products`;

CREATE TABLE `encomendaprodutor_has_products` (
  `id_encomendaProdutor` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `id_produtor` int(11) NOT NULL,
  `quant` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table people
# ------------------------------------------------------------

DROP TABLE IF EXISTS `people`;

CREATE TABLE `people` (
  `id_people` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `concelho` varchar(100) DEFAULT NULL,
  `freguesia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(50) DEFAULT NULL,
  `flag` varchar(10) DEFAULT NULL,
  `permissao` int(11) NOT NULL,
  `ferias` int(11) NOT NULL,
  PRIMARY KEY (`id_people`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_produtor` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `id_category` int(11) NOT NULL,
  `quantidade` varchar(16) NOT NULL,
  `cultura` varchar(50) NOT NULL,
  `stock` varchar(16) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `peso` varchar(10) NOT NULL,
  `details` text NOT NULL,
  `obs` varchar(60) NOT NULL,
  `imagem` varchar(300) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
