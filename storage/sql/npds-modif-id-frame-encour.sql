-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 12 avr. 2024 à 16:18
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `npds`
--

-- --------------------------------------------------------

--
-- Structure de la table `access`
--

DROP TABLE IF EXISTS `access`;
CREATE TABLE IF NOT EXISTS `access` (
  `access_id` int NOT NULL AUTO_INCREMENT,
  `access_title` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`access_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `access`
--

INSERT INTO `access` (`access_id`, `access_title`) VALUES
(1, 'User'),
(2, 'Moderator'),
(3, 'Super Moderator');

-- --------------------------------------------------------

--
-- Structure de la table `appli_log`
--

DROP TABLE IF EXISTS `appli_log`;
CREATE TABLE IF NOT EXISTS `appli_log` (
  `al_id` int NOT NULL DEFAULT '0',
  `al_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `al_subid` int NOT NULL DEFAULT '0',
  `al_date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `al_uid` int NOT NULL DEFAULT '0',
  `al_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `al_ip` varchar(54) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `al_hostname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  KEY `al_id` (`al_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `appli_log`
--

INSERT INTO `appli_log` (`al_id`, `al_name`, `al_subid`, `al_date`, `al_uid`, `al_data`, `al_ip`, `al_hostname`) VALUES
(1, 'Poll', 2, '2012-07-15 13:35:32', 1, '2', '1.1.76.115', ''),
(1, 'Poll', 2, '2024-04-01 09:45:54', 2, '1', '%3A%3A1', '');

-- --------------------------------------------------------

--
-- Structure de la table `authors`
--

DROP TABLE IF EXISTS `authors`;
CREATE TABLE IF NOT EXISTS `authors` (
  `aid` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pwd` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hashkey` tinyint(1) NOT NULL DEFAULT '0',
  `counter` int NOT NULL DEFAULT '0',
  `radminsuper` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `authors`
--

INSERT INTO `authors` (`aid`, `name`, `url`, `email`, `pwd`, `hashkey`, `counter`, `radminsuper`) VALUES
('Root', 'Root', '', 'root@npds.org', '$2y$11$1PWZG0asoJ8UaqNf.4OLy.OuR9wNCIHCLTjEnoqZxtOYYYrpf4SuK', 1, 6, 1);

-- --------------------------------------------------------

--
-- Structure de la table `autonews`
--

DROP TABLE IF EXISTS `autonews`;
CREATE TABLE IF NOT EXISTS `autonews` (
  `anid` int NOT NULL AUTO_INCREMENT,
  `catid` int NOT NULL DEFAULT '0',
  `aid` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hometext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bodytext` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `topic` int NOT NULL DEFAULT '1',
  `informant` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ihome` int NOT NULL DEFAULT '0',
  `date_debval` datetime DEFAULT NULL,
  `date_finval` datetime DEFAULT NULL,
  `auto_epur` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`anid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `banner`
--

DROP TABLE IF EXISTS `banner`;
CREATE TABLE IF NOT EXISTS `banner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL DEFAULT '0',
  `imptotal` int NOT NULL DEFAULT '0',
  `impmade` int NOT NULL DEFAULT '0',
  `clicks` int NOT NULL DEFAULT '0',
  `imageurl` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `clickurl` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `userlevel` int NOT NULL DEFAULT '0',
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `banner`
--

INSERT INTO `banner` (`id`, `cid`, `imptotal`, `impmade`, `clicks`, `imageurl`, `clickurl`, `userlevel`, `date`) VALUES
(1, 1, 0, 6328, 4, 'https://labo.infocapagde.com/images/banners/ban_rev_16.png', 'http://www.google.fr', 0, '2024-03-30 00:51:21'),
(3, 1, 0, 2566, 3, 'https://marketplace.canva.com/EAEg1P45hCw/1/0/1600w/canva-simple-travail-linkedin-banni%C3%A8re-VoKP4HFhK8A.jpg', 'http://www.npds.org/', 0, '2024-03-30 00:58:44');

-- --------------------------------------------------------

--
-- Structure de la table `bannerclient`
--

DROP TABLE IF EXISTS `bannerclient`;
CREATE TABLE IF NOT EXISTS `bannerclient` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `contact` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `login` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `passwd` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `extrainfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bannerclient`
--

INSERT INTO `bannerclient` (`id`, `name`, `contact`, `email`, `login`, `passwd`, `extrainfo`) VALUES
(1, 'nicolas', 'nicolas', 'nicolas.l.devoy@gmail.com', 'nicolas', 'Kyld2s9&7201', 'test');

-- --------------------------------------------------------

--
-- Structure de la table `bannerfinish`
--

DROP TABLE IF EXISTS `bannerfinish`;
CREATE TABLE IF NOT EXISTS `bannerfinish` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL DEFAULT '0',
  `impressions` int NOT NULL DEFAULT '0',
  `clicks` int NOT NULL DEFAULT '0',
  `datestart` datetime DEFAULT NULL,
  `dateend` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `block`
--

DROP TABLE IF EXISTS `block`;
CREATE TABLE IF NOT EXISTS `block` (
  `id` tinyint NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `block`
--

INSERT INTO `block` (`id`, `title`, `content`) VALUES
(1, 'Menu', '<ul class=\"list-group list-group-flush\"><li class=\"my-2\"><a href=\"modules.php?ModPath=archive-stories&ModStart=archive-stories\">[fr]Archives[/fr][en]Archives[/en][zh]档案资料[/zh][es]Archivos[/es][de]Archives[/de]</a></li><li class=\"my-2\"><a href=\"forum.php\">[fr]Forums[/fr][en]Forums[/en][zh]版面管理[/zh][es]Foros[/es][de]Foren[/de]</a></li><li class=\"my-2\"><a href=\"sections.php\">[fr]Rubriques[/fr][en]Sections[/en][zh]精华区[/zh][es]Secciones[/es][de]Rubriken[/de]</a></li><li class=\"my-2\"><a href=\"topics.php\">[fr]Sujets actifs[/fr][en]Topics[/en][zh]主题[/zh][es]Asuntos[/es][de]Themen[/de]</a></li><li class=\"my-2\"><a href=\"modules.php?ModPath=links&ModStart=links\">[fr]Liens[/fr][en]Links[/en][zh]网页链接[/zh][es]Enlaces web[/es][de]Internetlinks[/de]</a></li><li class=\"my-2\"><a href=\"download.php\">[fr]Téléchargements[/fr][en]Downloads[/en][zh]Downloads[/zh][es]Descargas[/es][de]Downloads[/de]</a></li><li class=\"my-2\"><a href=\"faq.php\">FAQ</a></li><li class=\"my-2\"><a href=\"static.php?op=statik.txt&npds=1\">[fr]Page statique[/fr][en]Static page[/en][zh]静态页面[/zh][es]Página estática[/es][de]Statische Seite[/de]</a></li><li class=\"my-2\"><a href=\"reviews.php\">[fr]Critiques[/fr][en]Reviews[/en][zh]评论[/zh][es]Criticas[/es][de]Kritiken[/de]</a></li><li class=\"my-2\"><a href=\"memberslist.php\">[fr]Annuaire[/fr][en]Members List[/en][zh]会员列表[/zh][es]Lista de miembros[/es][de]Liste der registrierten Benutzer[/de]</a></li><li class=\"my-2\"><a href=\"map.php\">[fr]Plan du site[/fr][en]Site Map[/en][zh]站点地图[/zh][es]Mapa del sitio[/es][de]Sitemap[/de]</a></li><li class=\"my-2\"><a href=\"friend.php\">[fr]Faire notre pub[/fr][en]Recommend us[/en][zh]推荐我们[/zh][es]Recomiendanos[/es][de]Empfehlen uns[/de]</a></li><li class=\"my-2\"><a href=\"user.php\">[fr]Votre compte[/fr][en]Your account[/en][zh]您的帐号[/zh][es]Su cuenta[/es][de]Ihr Account[/de]</a></li><li class=\"my-2\"><a href=\"submit.php\">[fr]Nouvel article[/fr][en]Submit News[/en][zh]提交文章设置[/zh][es]Someter una noticia[/es][de]Beitrag freigeben[/de]</a></li><li class=\"my-2\"><a href=\"admin.php\">[fr]Administration[/fr][en]Administration[/en][zh]管理[/zh][es]Administración[/es][de]Verwaltung[/de]</a></li></ul>'),
(2, 'Administration', '<ul><li><a href=\"admin.php\"><i class=\"fas fa-sign-in-alt fa-2x align-middle\"></i> Administration</a></li><li><a href=\"admin.php?op=logout\" class=\" text-danger\"><i class=\"fas fa-sign-out-alt fa-2x align-middle\"></i> [fr]Déconnexion[/fr][en]Logout[/en][zh]登出[/zh][es]Cerrar sesión[/es][de]Ausloggen[/de]</a></li></ul>');

-- --------------------------------------------------------

--
-- Structure de la table `blocnotes`
--

DROP TABLE IF EXISTS `blocnotes`;
CREATE TABLE IF NOT EXISTS `blocnotes` (
  `bnid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `texte` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`bnid`(32))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `catagories`
--

DROP TABLE IF EXISTS `catagories`;
CREATE TABLE IF NOT EXISTS `catagories` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `cat_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `catagories`
--

INSERT INTO `catagories` (`cat_id`, `cat_title`) VALUES
(1, 'Demo'),
(3, 'categ test');

-- --------------------------------------------------------

--
-- Structure de la table `chatbox`
--

DROP TABLE IF EXISTS `chatbox`;
CREATE TABLE IF NOT EXISTS `chatbox` (
  `username` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip` varchar(54) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date` int NOT NULL DEFAULT '0',
  `id` int DEFAULT '0',
  `dbname` tinyint DEFAULT '0',
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chatbox`
--

INSERT INTO `chatbox` (`username`, `ip`, `message`, `date`, `id`, `dbname`) VALUES
('', '%3A%3A1', 'nicolas good !', 1712934793, 0, 1),
('', '%3A%3A1', 'sdfgds', 1712934783, 0, 1),
('user', '%3A%3A1', 'dfsgfdsg', 1712922031, 0, 1),
('user', '%3A%3A1', 'qsdfqsd', 1712921984, 0, 1),
('user', '%3A%3A1', 'dfsqg', 1712921979, 0, 1),
('user', '%3A%3A1', 'dfghdfghdf', 1712921866, 0, 1),
('user', '%3A%3A1', 'dfhfh', 1712921861, 0, 1),
('user', '%3A%3A1', 'df', 1712921839, 0, 1),
('user', '%3A%3A1', 'sdfgsd', 1712921435, 0, 1),
('user', '%3A%3A1', 'ghsdfg', 1712921432, 0, 1),
('user', '%3A%3A1', 'dsqfqf', 1712921362, 0, 1),
('user', '%3A%3A1', 'hdfh', 1712921287, 0, 1),
('user', '%3A%3A1', 'coucou c\'est moi le coco', 1712920675, 0, 1),
('user', '%3A%3A1', 'cocou c\'\'est moi le lapin rouge', 1712920547, 0, 1),
('user', '%3A%3A1', 'fjfghj', 1712919924, 0, 1),
('user', '%3A%3A1', 'dhdf', 1712919884, 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `compatsujet`
--

DROP TABLE IF EXISTS `compatsujet`;
CREATE TABLE IF NOT EXISTS `compatsujet` (
  `id1` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `id2` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `allow_html` int DEFAULT NULL,
  `allow_bbcode` int DEFAULT NULL,
  `allow_sig` int DEFAULT NULL,
  `posts_per_page` int DEFAULT NULL,
  `hot_threshold` int DEFAULT NULL,
  `topics_per_page` int DEFAULT NULL,
  `allow_upload_forum` int UNSIGNED NOT NULL DEFAULT '0',
  `allow_forum_hide` int UNSIGNED NOT NULL DEFAULT '0',
  `upload_table` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'forum_attachments',
  `rank1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rank5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anti_flood` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solved` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `config`
--

INSERT INTO `config` (`allow_html`, `allow_bbcode`, `allow_sig`, `posts_per_page`, `hot_threshold`, `topics_per_page`, `allow_upload_forum`, `allow_forum_hide`, `upload_table`, `rank1`, `rank2`, `rank3`, `rank4`, `rank5`, `anti_flood`, `solved`) VALUES
(1, 1, 1, 10, 10, 10, 0, 0, 'forum_attachments', '', '', '', '', '', '0', 0);

-- --------------------------------------------------------

--
-- Structure de la table `counter`
--

DROP TABLE IF EXISTS `counter`;
CREATE TABLE IF NOT EXISTS `counter` (
  `id_stat` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `var` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `count` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stat`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `counter`
--

INSERT INTO `counter` (`id_stat`, `type`, `var`, `count`) VALUES
(1, 'total', 'hits', 2241),
(2, 'browser', 'WebTV', 54),
(3, 'browser', 'Lynx', 54),
(4, 'browser', 'MSIE', 54),
(5, 'browser', 'Opera', 54),
(6, 'browser', 'Konqueror', 54),
(7, 'browser', 'Netscape', 54),
(8, 'browser', 'Chrome', 2253),
(9, 'browser', 'Safari', 54),
(10, 'browser', 'Bot', 54),
(11, 'browser', 'Other', 54),
(12, 'os', 'Windows', 2253),
(13, 'os', 'Linux', 54),
(14, 'os', 'Mac', 54),
(15, 'os', 'FreeBSD', 54),
(16, 'os', 'SunOS', 54),
(17, 'os', 'IRIX', 54),
(18, 'os', 'BeOS', 54),
(19, 'os', 'OS/2', 54),
(20, 'os', 'AIX', 54),
(21, 'os', 'Other', 54),
(25, 'os', 'Android', 54),
(22, 'os', 'iOS', 54);

-- --------------------------------------------------------

--
-- Structure de la table `downloads`
--

DROP TABLE IF EXISTS `downloads`;
CREATE TABLE IF NOT EXISTS `downloads` (
  `did` int NOT NULL AUTO_INCREMENT,
  `dcounter` int NOT NULL DEFAULT '0',
  `durl` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dfilename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dfilesize` bigint UNSIGNED DEFAULT NULL,
  `ddate` date NOT NULL DEFAULT '1000-01-01',
  `dweb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duser` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dver` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dcategory` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ddescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `perms` varchar(480) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`did`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `downloads`
--

INSERT INTO `downloads` (`did`, `dcounter`, `durl`, `dfilename`, `dfilesize`, `ddate`, `dweb`, `duser`, `dver`, `dcategory`, `ddescription`, `perms`) VALUES
(1, 35, 'http://www.npds.org/themes/NPDS-Bmag/images/topics/npds.png', 'dfq', 156, '2024-03-30', 'http://localhost:8080/admin.php?op=DownloadAdmin', 'erza', '1', 'test', '<p>qserfqsdfqf</p>', '1');

-- --------------------------------------------------------

--
-- Structure de la table `droits`
--

DROP TABLE IF EXISTS `droits`;
CREATE TABLE IF NOT EXISTS `droits` (
  `d_aut_aid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'id administrateur',
  `d_fon_fid` tinyint UNSIGNED NOT NULL COMMENT 'id fonction',
  `d_droits` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dune_proto';

-- --------------------------------------------------------

--
-- Structure de la table `ephem`
--

DROP TABLE IF EXISTS `ephem`;
CREATE TABLE IF NOT EXISTS `ephem` (
  `eid` int NOT NULL AUTO_INCREMENT,
  `did` int NOT NULL DEFAULT '0',
  `mid` int NOT NULL DEFAULT '0',
  `yid` int NOT NULL DEFAULT '0',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`eid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faqanswer`
--

DROP TABLE IF EXISTS `faqanswer`;
CREATE TABLE IF NOT EXISTS `faqanswer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_categorie` tinyint DEFAULT NULL,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `faqanswer`
--

INSERT INTO `faqanswer` (`id`, `id_categorie`, `question`, `answer`) VALUES
(1, 3, 'ezrtzert fdgs', '<p>azerazerazeraze dfgdsgsdg</p>'),
(7, 3, 'fgvbcgq', '<p>qsdfqsf</p>'),
(8, 3, 'qsdfqsdfqsd', '<p>qsdfqsdfqsdfqsdf</p>'),
(9, 3, 'qsdfqsdf', '<p>qsdfqsdfqsdf</p>');

-- --------------------------------------------------------

--
-- Structure de la table `faqcategories`
--

DROP TABLE IF EXISTS `faqcategories`;
CREATE TABLE IF NOT EXISTS `faqcategories` (
  `id` tinyint NOT NULL AUTO_INCREMENT,
  `categories` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `faqcategories`
--

INSERT INTO `faqcategories` (`id`, `categories`) VALUES
(3, 'avion f');

-- --------------------------------------------------------

--
-- Structure de la table `fonctions`
--

DROP TABLE IF EXISTS `fonctions`;
CREATE TABLE IF NOT EXISTS `fonctions` (
  `fid` mediumint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id unique auto incrémenté',
  `fnom` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fdroits1` tinyint UNSIGNED DEFAULT NULL,
  `fdroits1_descr` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `finterface` tinyint UNSIGNED NOT NULL COMMENT '1 ou 0 : la fonction dispose ou non d''une interface',
  `fetat` tinyint(1) NOT NULL COMMENT '0 ou 1  9 : non active ou installé, installé',
  `fretour` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'utiliser par les fonctions de categorie Alerte : nombre, ou ',
  `fretour_h` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fnom_affich` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ficone` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `furlscript` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'attribut et contenu  de balise A : href="xxx", onclick="xxx"  etc',
  `fcategorie` tinyint UNSIGNED NOT NULL,
  `fcategorie_nom` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fordre` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dune_proto';

--
-- Déchargement des données de la table `fonctions`
--

INSERT INTO `fonctions` (`fid`, `fnom`, `fdroits1`, `fdroits1_descr`, `finterface`, `fetat`, `fretour`, `fretour_h`, `fnom_affich`, `ficone`, `furlscript`, `fcategorie`, `fcategorie_nom`, `fordre`) VALUES
(1, 'edito', 1, '', 1, 1, '', '', 'Edito', 'edito', 'href=\"admin.php?op=Edito\"', 1, 'Contenu', 0),
(2, 'adminStory', 2, '', 1, 1, '', '', 'Nouvel Article', 'postnew', 'href=\"admin.php?op=adminStory\"', 1, 'Contenu', 1),
(3, 'sections', 3, '', 1, 1, '', '', 'Rubriques', 'sections', 'href=\"admin.php?op=sections\"', 1, 'Contenu', 2),
(4, 'topicsmanager', 4, '', 1, 1, '', '', 'Gestion des Sujets', 'topicsman', 'href=\"admin.php?op=topicsmanager\"', 1, 'Contenu', 3),
(5, 'links', 5, '', 1, 1, '', '', 'Liens Web', 'links', 'href=\"admin.php?op=links\"', 1, 'Contenu', 5),
(6, 'FaqAdmin', 6, '', 1, 1, '1', '', 'FAQ', 'faq', 'href=\"admin.php?op=FaqAdmin\"', 1, 'Contenu', 6),
(7, 'Ephemerids', 7, '', 1, 1, '1', '', 'Ephémérides', 'ephem', 'href=\"admin.php?op=Ephemerids\"', 1, 'Contenu', 7),
(8, 'HeadlinesAdmin', 8, '', 1, 1, '', '', 'News externes', 'headlines', 'href=\"admin.php?op=HeadlinesAdmin\"', 1, 'Contenu', 8),
(9, 'DownloadAdmin', 9, '', 1, 1, '', '', 'Téléchargements', 'download', 'href=\"admin.php?op=DownloadAdmin\"', 1, 'Contenu', 9),
(10, 'mod_users', 10, '', 1, 1, '', '', 'Utilisateurs', 'users', 'href=\"admin.php?op=mod_users\"', 2, 'Utilisateurs', 1),
(11, 'groupes', 11, '', 1, 1, '', '', 'Groupes', 'groupes', 'href=\"admin.php?op=groupes\"', 2, 'Utilisateurs', 2),
(12, 'mod_authors', 12, '', 1, 1, '', '', 'Administrateurs', 'authors', 'href=\"admin.php?op=mod_authors\"', 2, 'Utilisateurs', 3),
(13, 'MaintForumAdmin', 13, '', 1, 1, '', '', 'Maintenance Forums', 'forum', 'href=\"admin.php?op=MaintForumAdmin\"', 3, 'Communication', 0),
(14, 'ForumConfigAdmin', 14, '', 1, 1, '', '', 'Configuration Forums', 'forum', 'href=\"admin.php?op=ForumConfigAdmin\"', 3, 'Communication', 0),
(15, 'ForumAdmin', 15, '', 1, 1, '', '', 'Edition Forums', 'forum', 'href=\"admin.php?op=ForumAdmin\"', 3, 'Communication', 0),
(16, 'lnl', 16, '', 1, 1, '', '', 'Lettre D\'info', 'lnl', 'href=\"admin.php?op=lnl\"', 3, 'Communication', 0),
(17, 'email_user', 17, '', 1, 1, '', '', 'Message Interne', 'email_user', 'href=\"admin.php?op=email_user\"', 3, 'Communication', 0),
(18, 'BannersAdmin', 18, '', 1, 1, '', '', 'Bannières', 'banner', 'href=\"admin.php?op=BannersAdmin\"', 3, 'Communication', 0),
(19, 'create', 19, '', 1, 1, '', '', 'Sondages', 'newpoll', 'href=\"admin.php?op=create\"', 3, 'Communication', 0),
(20, 'reviews', 20, '', 1, 1, '', '', 'Critiques', 'reviews', 'href=\"admin.php?op=reviews\"', 3, 'Communication', 0),
(21, 'hreferer', 21, '', 1, 1, '', '', 'Sites Référents', 'referer', 'href=\"admin.php?op=hreferer\"', 3, 'Communication', 0),
(22, 'blocks', 22, '', 1, 1, '', '', 'Blocs', 'block', 'href=\"admin.php?op=blocks\"', 4, 'Interface', 0),
(23, 'mblock', 23, '', 1, 1, '', '', 'Bloc Principal', 'blockmain', 'href=\"admin.php?op=mblock\"', 4, 'Interface', 0),
(24, 'ablock', 24, '', 1, 1, '', '', 'Bloc Administration', 'blockadm', 'href=\"admin.php?op=ablock\"', 4, 'Interface', 0),
(25, 'Configure', 25, '', 1, 1, '', '', 'Préférences', 'preferences', 'href=\"admin.php?op=Configure\"', 5, 'Système', 0),
(26, 'ConfigFiles', 26, '', 1, 1, '', '', 'Fichiers configurations', 'preferences', 'href=\"admin.php?op=ConfigFiles\"', 5, 'Système', 0),
(27, 'FileManager', 27, '', 1, 0, '', '', 'Gestionnaire Fichiers', 'filemanager', 'href=\"admin.php?op=FileManager\"', 5, 'Système', 0),
(28, 'supercache', 28, '', 1, 1, '', '', 'SuperCache', 'overload', 'href=\"admin.php?op=supercache\"', 5, 'Système', 0),
(29, 'OptimySQL', 29, '', 1, 1, '', '', 'OptimySQL', 'optimysql', 'href=\"admin.php?op=OptimySQL\"', 5, 'Système', 0),
(30, 'SavemySQL', 30, '', 1, 1, '', '', 'SavemySQL', 'savemysql', 'href=\"admin.php?op=SavemySQL\"', 5, 'Système', 0),
(31, 'MetaTagAdmin', 31, '', 1, 1, '', '', 'MétaTAGs', 'metatags', 'href=\"admin.php?op=MetaTagAdmin\"', 5, 'Système', 0),
(32, 'MetaLangAdmin', 32, '', 1, 1, '', '', 'META-LANG', 'metalang', 'href=\"admin.php?op=Meta-LangAdmin\"', 5, 'Système', 0),
(33, 'setban', 33, '', 1, 1, '', '', 'IP', 'ipban', 'href=\"admin.php?op=Extend-Admin-SubModule&amp;ModPath=ipban&amp;ModStart=setban\"', 5, 'Système', 0),
(34, 'session_log', 34, '', 1, 1, '', '', 'Logs', 'logs', 'href=\"admin.php?op=Extend-Admin-SubModule&ModPath=session-log&ModStart=session-log\"', 5, 'Système', 0),
(35, 'reviews', 20, '', 1, 0, '0', 'Critique en atttente de validation.', 'Critiques', 'reviews', 'href=\"admin.php?op=reviews\"', 9, 'Alerte', 0),
(36, 'mes_npds_versus', 36, '', 1, 1, 'N', 'Une nouvelle version NPDS est disponible !<br />REvolution v.16.3.0<br />Cliquez pour télécharger.', '', 'message_npds', 'data-bs-toggle=\"modal\" data-bs-target=\"#versusModal\"', 9, 'Alerte', 0),
(37, 'autoStory', 2, '', 1, 0, '0', '', 'Auto-Articles', 'autonews', 'href=\"admin.php?op=autoStory\"', 9, 'Alerte', 0),
(76, 'mes_npds_0', NULL, '', 1, 1, NULL, 'Patch de sécurité 2022 !', '<span class=\"text-danger\">Niveau critique appliquez ce patch !!!!</span><br /> Ce patch de sécurité répond aux problèmes décrits sommairement ci-dessous. Il est disponible ici <a href=\"http://www.npds.org/download.php?op=mydown&did=201\">patch2022.txt</a>.<br /><span class=\"text-danger\">Au vu de la nature des problèmes nous vous conseillons fortement d\'appliquer ce patch sur tous vos sites.</span><br /><b>Problème :</b> sécurité<br /><b>Description :</b> faiblesse XSS dans chat, renforcement du contrôle des urls <br /><b>Versions concernées :</b> de 13 à 16.3<br /><b>Niveau :</b> sérieux<br /><b>Fichiers concernés :</b> chatrafraich.php, chatinput.php, grab_globals.php, modules/include/url_protect.php<br /><b>NB :</b> les versions antérieures à 13 sont aussi très probablement affectées mais nous n\'avons pas écrit de correction. ! (Merci dans ce cas là de mettre à jour avec une version plus récente de npds).<br />@Nosp merci pour l\'audit.<br />[@Dev, @jpb]<br />', 'message_a', 'data-bs-toggle=\"modal\" data-bs-target=\"#messageModal\"', 9, 'Alerte', 0),
(38, 'submissions', 2, '', 1, 0, '0', 'Articles en attente de validation !', 'Articles', 'submissions', 'href=\"admin.php?op=submissions\"', 9, 'Alerte', 0),
(40, 'abla', 40, '', 1, 1, '', '', 'Blackboard', 'abla', 'href=\"admin.php?op=abla\"', 5, 'Système', 0),
(41, 'newlink', 5, '', 1, 0, '0', 'Lien &#xE0; valider', 'Lien', 'links', 'href=\"admin.php?op=links\"', 9, 'Alerte', 0),
(42, 'brokenlink', 5, '', 1, 0, '0', 'Lien rompu &#xE0; valider', 'Lien rompu', 'links', 'href=\"admin.php?op=LinksListBrokenLinks\"', 9, 'Alerte', 0),
(43, 'archive-stories', 43, '', 1, 1, '', '', 'Archives articles', 'archive-stories', 'href=\"admin.php?op=Extend-Admin-SubModule&ModPath=archive-stories&ModStart=admin/archive-stories_set\"', 1, 'Contenu', 4),
(44, 'mod_users', 10, '', 1, 1, '1', 'Utilisateur en attente de validation !', 'Utilisateurs', 'users', 'href=\"admin.php?op=nonallowed_users\"', 9, 'Alerte', 0),
(45, 'upConfigure', 45, '', 1, 1, '', '', 'Upload', 'upload', 'href=\"admin.php?op=Extend-Admin-SubModule&ModPath=upload&ModStart=admin/upload\"', 5, 'Système', 0),
(46, 'groupe_member_ask', 11, '', 0, 0, '0', 'Utilisateur en attente de groupe !', 'Groupes', 'groupes', 'href=\"admin.php?op=groupe_member_ask\"', 9, 'Alerte', 0),
(49, 'npds_twi', 49, '', 1, 1, '', '', 'Npds_Twitter', 'npds_twi', 'href=\"admin.php?op=Extend-Admin-SubModule&ModPath=npds_twi&ModStart=admin/npds_twi_set\"', 6, 'Modules', 0),
(50, 'publications', 3, '', 1, 0, '0', 'Publication(s) en attente de validation', 'Rubriques', 'sections', 'href=\"admin.php?op=sections#publications en attente\"', 9, 'Alerte', 0),
(51, 'modules', 51, '', 1, 1, '', '', 'Gestion modules', 'modules', 'href=\"admin.php?op=modules\"', 5, 'Système', 0),
(74, 'reseaux-sociaux', 74, '', 1, 1, '', '', 'Réseaux sociaux', 'reseaux-sociaux', 'href=\"admin.php?op=Extend-Admin-SubModule&ModPath=reseaux-sociaux&ModStart=admin/reseaux-sociaux_set\"', 2, 'Utilisateurs', 4),
(75, 'geoloc', 75, '', 1, 1, '', '', 'geoloc', 'geoloc', 'href=\"admin.php?op=Extend-Admin-SubModule&ModPath=geoloc&ModStart=admin/geoloc_set\"', 6, 'Modules', 0),
(77, 'mes_npds_1', NULL, 'Root|', 1, 1, NULL, 'Les messages de npds', '<br /> Ce système nous permet d\'envoyer des messages concernant l\'actualité de npds et de son développement à tous les webmaster directement dans leur portail.<br /> Il monitore également la version utilisée et emet une alerte quand une nouvelle version de npds est disponible. Du côté de l\'interface admin ces messages peuvent être marqués comme lus par chaque admin et donc ne sont plus affichés jusqu\'à leur modification ou leur suppression par npds.org.<br /> [@nico2 @jpb].', 'message_i', 'data-bs-toggle=\"modal\" data-bs-target=\"#messageModal\"', 9, 'Alerte', 0);

-- --------------------------------------------------------

--
-- Structure de la table `forums`
--

DROP TABLE IF EXISTS `forums`;
CREATE TABLE IF NOT EXISTS `forums` (
  `forum_id` int NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forum_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `forum_access` int DEFAULT '1',
  `forum_moderator` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cat_id` int DEFAULT NULL,
  `forum_type` int DEFAULT '0',
  `forum_pass` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arbre` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `attachement` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `forum_index` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`forum_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `forums`
--

INSERT INTO `forums` (`forum_id`, `forum_name`, `forum_desc`, `forum_access`, `forum_moderator`, `cat_id`, `forum_type`, `forum_pass`, `arbre`, `attachement`, `forum_index`) VALUES
(1, 'Demo', '', 0, '2,3', 1, 0, '', 0, 1, 0),
(2, 'Message', 'un forum à l\'ancienne forme', 0, '2', 1, 0, '', 1, 1, 0),
(8, 'test 3', 'ddd', 0, '2,3', 1, 0, '', 0, 0, 3);

-- --------------------------------------------------------

--
-- Structure de la table `forumtopics`
--

DROP TABLE IF EXISTS `forumtopics`;
CREATE TABLE IF NOT EXISTS `forumtopics` (
  `topic_id` int NOT NULL AUTO_INCREMENT,
  `topic_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic_poster` int DEFAULT NULL,
  `topic_time` datetime DEFAULT NULL,
  `topic_views` int NOT NULL DEFAULT '0',
  `forum_id` int DEFAULT NULL,
  `topic_status` int NOT NULL DEFAULT '0',
  `topic_notify` int DEFAULT '0',
  `current_poster` int DEFAULT NULL,
  `topic_first` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `topic_first` (`topic_first`,`topic_time`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `forumtopics`
--

INSERT INTO `forumtopics` (`topic_id`, `topic_title`, `topic_poster`, `topic_time`, `topic_views`, `forum_id`, `topic_status`, `topic_notify`, `current_poster`, `topic_first`) VALUES
(1, 'Demo', 2, '2024-04-08 03:08:21', 92, 1, 0, 0, 2, 1),
(2, 'Message 1', 1, '2013-05-14 22:55:00', 19, 2, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `forum_attachments`
--

DROP TABLE IF EXISTS `forum_attachments`;
CREATE TABLE IF NOT EXISTS `forum_attachments` (
  `att_id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL DEFAULT '0',
  `topic_id` int NOT NULL DEFAULT '0',
  `forum_id` int NOT NULL DEFAULT '0',
  `unixdate` int NOT NULL DEFAULT '0',
  `att_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `att_type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `att_size` int NOT NULL DEFAULT '0',
  `att_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inline` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `apli` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `compteur` int NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  KEY `att_id` (`att_id`),
  KEY `post_id` (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `apli` (`apli`),
  KEY `visible` (`visible`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `forum_read`
--

DROP TABLE IF EXISTS `forum_read`;
CREATE TABLE IF NOT EXISTS `forum_read` (
  `rid` int NOT NULL AUTO_INCREMENT,
  `forum_id` int NOT NULL DEFAULT '0',
  `topicid` int NOT NULL DEFAULT '0',
  `uid` int NOT NULL DEFAULT '0',
  `last_read` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rid`),
  KEY `topicid` (`topicid`),
  KEY `forum_id` (`forum_id`),
  KEY `uid` (`uid`),
  KEY `forum_read_mcl` (`forum_id`,`uid`,`topicid`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `forum_read`
--

INSERT INTO `forum_read` (`rid`, `forum_id`, `topicid`, `uid`, `last_read`, `status`) VALUES
(4, 1, 1, 3, 1712234016, 0),
(5, 1, 1, 2, 1712908394, 1),
(6, 2, 2, 3, 1712234016, 1),
(7, 2, 2, 2, 1712559697, 1);

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

DROP TABLE IF EXISTS `groupes`;
CREATE TABLE IF NOT EXISTS `groupes` (
  `groupe_id` int DEFAULT NULL,
  `groupe_name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `groupe_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `groupe_forum` int UNSIGNED NOT NULL DEFAULT '0',
  `groupe_mns` int UNSIGNED NOT NULL DEFAULT '0',
  `groupe_chat` int UNSIGNED NOT NULL DEFAULT '0',
  `groupe_blocnote` int UNSIGNED NOT NULL DEFAULT '0',
  `groupe_pad` int UNSIGNED NOT NULL DEFAULT '0',
  UNIQUE KEY `groupe_id` (`groupe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `groupes`
--

INSERT INTO `groupes` (`groupe_id`, `groupe_name`, `groupe_description`, `groupe_forum`, `groupe_mns`, `groupe_chat`, `groupe_blocnote`, `groupe_pad`) VALUES
(2, 'test', 'pour tester', 0, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `headlines`
--

DROP TABLE IF EXISTS `headlines`;
CREATE TABLE IF NOT EXISTS `headlines` (
  `hid` int NOT NULL AUTO_INCREMENT,
  `sitename` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `headlinesurl` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `headlines`
--

INSERT INTO `headlines` (`hid`, `sitename`, `url`, `headlinesurl`, `status`) VALUES
(1, 'NPDS', 'http://www.npds.org', 'http://www.npds.org/backend.php', 0),
(2, 'Modules', 'http://modules.npds.org', 'http://modules.npds.org/backend.php', 0),
(3, 'Styles', 'http://styles.npds.org', 'http://styles.npds.org/backend.php', 0);

-- --------------------------------------------------------

--
-- Structure de la table `ip_loc`
--

DROP TABLE IF EXISTS `ip_loc`;
CREATE TABLE IF NOT EXISTS `ip_loc` (
  `ip_id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_long` float NOT NULL DEFAULT '0',
  `ip_lat` float NOT NULL DEFAULT '0',
  `ip_visi_pag` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ip_visite` mediumint UNSIGNED NOT NULL DEFAULT '0',
  `ip_ip` varchar(54) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ip_country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `ip_code_country` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_city` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lblocks`
--

DROP TABLE IF EXISTS `lblocks`;
CREATE TABLE IF NOT EXISTS `lblocks` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `member` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `Lindex` tinyint NOT NULL DEFAULT '0',
  `cache` mediumint UNSIGNED NOT NULL DEFAULT '0',
  `actif` smallint UNSIGNED NOT NULL DEFAULT '1',
  `css` tinyint(1) NOT NULL DEFAULT '0',
  `aide` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `Lindex` (`Lindex`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lblocks`
--

INSERT INTO `lblocks` (`id`, `title`, `content`, `member`, `Lindex`, `cache`, `actif`, `css`, `aide`) VALUES
(1, '[fr]Un Bloc ...[/fr][en]One Block ...[/en][zh]一块...[/zh][es]Un Bloque...[/es][de]Ein Block[/de]', 'Vous pouvez ajouter, éditer et supprimer des Blocs à votre convenance.', '0', 99, 0, 1, 0, ''),
(2, '[fr]Menu[/fr][en]Menu[/en][zh]菜单[/zh][es]Menú[/es][de]Menü[/de]', 'function#mainblock', '0', 1, 86400, 1, 0, 'Ce menu contient presque toutes les fonctions de base disponibles dans NPDS'),
(3, '[fr]Message à un membre[/fr][en]Message to Member[/en][zh]给会员发送的消息[/zh][es]Mensaje a un miembro[/es][de]Nachricht an einen Benutzer[/de]', 'function#instant_members_message', '0', 4, 0, 1, 0, ''),
(4, 'Chat Box', 'function#makeChatBox\r\nparams#chat_tous', '0', 2, 10, 1, 0, ''),
(5, '[fr]Forums Infos[/fr][en]Forums Infos[/en][zh]论坛信息[/zh][es]Foros infos[/es][de]Foreninfos[/de]', 'function#RecentForumPosts\r\nparams#Forums Infos,15,4,false,10,false,-:\r\n', '0', 5, 60, 1, 0, ''),
(6, '[fr]Les plus téléchargés[/fr][en]Most downloaded[/en][zh]个被下载最多的文件[/zh][es]Los mas descargados[/es][de]Am meisten heruntergeladen[/de]]', 'function#topdownload', '0', 6, 3600, 1, 0, ''),
(7, '[fr]Administration[/fr][en]Administration[/en][zh]网站治理[/zh][es]Administración[/es][de]Verwaltung[/de]', 'function#adminblock', '0', 3, 0, 1, 0, ''),
(8, '[fr]Ephémérides[/fr][en]Ephemerids[/en][zh]历史上的今天[/zh][es]Efemérides[/es][de]Ephemeriden[/de]', 'function#ephemblock', '0', 7, 28800, 1, 0, ''),
(15, 'Languages', 'function#bloc_langue', '0', 1, 60, 1, 0, ''),
(9, '[fr]Grands Titres de sites de News[/fr][en]headlines[/en][zh]新闻站点头条标题[/zh][es]Grandes titulos[/es][de]Informations Kanäle[/de]]', 'function#headlines', '0', 9, 3600, 1, 0, ''),
(16, '', 'function#bloc_espace_groupe\r\nparams#2,1', '2', 3, 0, 1, 0, NULL),
(10, '[fr]Activité du Site[/fr][en]Website Activity[/en][zh]本网站的活动信息[/zh][es]Actividad del sitio web[/es][de]Tätigkeit auf der Website[/de]', 'function#Site_Activ', '0', 8, 10, 1, 0, ''),
(11, '[fr]Sondage[/fr][en]Survey[/en][zh]调查[/zh][es]Encuesta[/es][de]Umfrage[/de]', 'function#pollNewest', '0', 1, 60, 1, 0, ''),
(12, '[fr]Carte[/fr][en]Map[/en][zh]地图[/zh][es]Mapa[/es][de]Landkarte[/de]', 'include#modules/geoloc/http/geoloc_bloc.php', '0', 1, 86400, 0, 0, ''),
(13, 'theme skin', 'function#blockSkin', '0', 1, 60, 1, 0, ''),
(18, 'fgds', 'function#online', '0', 2, 60, 1, 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `links_categories`
--

DROP TABLE IF EXISTS `links_categories`;
CREATE TABLE IF NOT EXISTS `links_categories` (
  `cid` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cdescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `links_categories`
--

INSERT INTO `links_categories` (`cid`, `title`, `cdescription`) VALUES
(1, 'Mod&egrave;le', ''),
(2, 'test categ', 'test categ pour voir ');

-- --------------------------------------------------------

--
-- Structure de la table `links_editorials`
--

DROP TABLE IF EXISTS `links_editorials`;
CREATE TABLE IF NOT EXISTS `links_editorials` (
  `linkid` int NOT NULL DEFAULT '0',
  `adminid` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `editorialtimestamp` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `editorialtext` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `editorialtitle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`linkid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `links_editorials`
--

INSERT INTO `links_editorials` (`linkid`, `adminid`, `editorialtimestamp`, `editorialtext`, `editorialtitle`) VALUES
(1, 'root', '2024-04-03 01:14:42', 'fghdfg', 'dfghdfg');

-- --------------------------------------------------------

--
-- Structure de la table `links_links`
--

DROP TABLE IF EXISTS `links_links`;
CREATE TABLE IF NOT EXISTS `links_links` (
  `lid` int NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL DEFAULT '0',
  `sid` int NOT NULL DEFAULT '0',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hits` int NOT NULL DEFAULT '0',
  `submitter` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `linkratingsummary` double(6,4) NOT NULL DEFAULT '0.0000',
  `totalvotes` int NOT NULL DEFAULT '0',
  `totalcomments` int NOT NULL DEFAULT '0',
  `topicid_card` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`lid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `links_modrequest`
--

DROP TABLE IF EXISTS `links_modrequest`;
CREATE TABLE IF NOT EXISTS `links_modrequest` (
  `requestid` int NOT NULL AUTO_INCREMENT,
  `lid` int NOT NULL DEFAULT '0',
  `cid` int NOT NULL DEFAULT '0',
  `sid` int NOT NULL DEFAULT '0',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `modifysubmitter` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `brokenlink` int NOT NULL DEFAULT '0',
  `topicid_card` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`requestid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `links_newlink`
--

DROP TABLE IF EXISTS `links_newlink`;
CREATE TABLE IF NOT EXISTS `links_newlink` (
  `lid` int NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL DEFAULT '0',
  `sid` int NOT NULL DEFAULT '0',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `submitter` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `topicid_card` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`lid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `links_subcategories`
--

DROP TABLE IF EXISTS `links_subcategories`;
CREATE TABLE IF NOT EXISTS `links_subcategories` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL DEFAULT '0',
  `title` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `links_subcategories`
--

INSERT INTO `links_subcategories` (`sid`, `cid`, `title`) VALUES
(1, 2, 'test sous categ');

-- --------------------------------------------------------

--
-- Structure de la table `lnl_body`
--

DROP TABLE IF EXISTS `lnl_body`;
CREATE TABLE IF NOT EXISTS `lnl_body` (
  `ref` int NOT NULL AUTO_INCREMENT,
  `html` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stb',
  PRIMARY KEY (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lnl_head_foot`
--

DROP TABLE IF EXISTS `lnl_head_foot`;
CREATE TABLE IF NOT EXISTS `lnl_head_foot` (
  `ref` int NOT NULL AUTO_INCREMENT,
  `type` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `html` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OK',
  PRIMARY KEY (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lnl_outside_users`
--

DROP TABLE IF EXISTS `lnl_outside_users`;
CREATE TABLE IF NOT EXISTS `lnl_outside_users` (
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `host_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `status` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OK',
  PRIMARY KEY (`email`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lnl_outside_users`
--

INSERT INTO `lnl_outside_users` (`email`, `host_name`, `date`, `status`) VALUES
('admin@gmail.com', '%3A%3A1', '2024-04-11 15:04:28', 'OK');

-- --------------------------------------------------------

--
-- Structure de la table `lnl_send`
--

DROP TABLE IF EXISTS `lnl_send`;
CREATE TABLE IF NOT EXISTS `lnl_send` (
  `ref` int NOT NULL AUTO_INCREMENT,
  `header` int NOT NULL DEFAULT '0',
  `body` int NOT NULL DEFAULT '0',
  `footer` int NOT NULL DEFAULT '0',
  `number_send` int NOT NULL DEFAULT '0',
  `type_send` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ALL',
  `date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `status` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OK',
  PRIMARY KEY (`ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `metalang`
--

DROP TABLE IF EXISTS `metalang`;
CREATE TABLE IF NOT EXISTS `metalang` (
  `def` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_meta` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mot',
  `type_uri` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `uri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `obligatoire` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`def`),
  KEY `type_meta` (`type_meta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `metalang`
--

INSERT INTO `metalang` (`def`, `content`, `type_meta`, `type_uri`, `uri`, `description`, `obligatoire`) VALUES
('!forumL!', 'function MM_forumL(){}', 'meta', '-', '', '[fr].Retourne les derniers posts des forums en tenant compte des groupes\r\nVariables que vous devez configurer :\r\nmaxcount : nombre de posts que vous voulez afficher...[/fr][en]...[/en]', '0'),
('^', '', 'docu', '-', NULL, '[fr]Dans un texte quelconque, ^ &agrave; la fin d&#39;un mot permet de le prot&eacute;ger contre meta-lang / Ex : Dev Dev^ ne donne pas un r&eacute;sultat identique[/fr]', '1'),
('!N_publicateur!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le nom de l&#39;administrateur ayant publi&eacute; l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_emetteur!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le nom de l&#39;auteur de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_date!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par la date de publication de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_date_y!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par l&#39;ann&eacute;e de publication de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_date_m!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le mois de publication de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_date_d!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le jour de publication de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_date_h!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par l&#39;heure de publication de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_print!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le lien pour imprimer l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_friend!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par lien pour envoyer l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_titre!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le titre de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_texte!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le texte de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_id!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le num&eacute;ro de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_sujet!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par un lien HTML et l&#39;image du sujet de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_note!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le de la note de l&#39;article si elle existe / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_nb_lecture!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le nombre de lecture effective de l&#39;article / actif dans index-news.html et detail-news.html[/fr]', '1'),
('!N_suite!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le lien HTML permettant de lire la suite de l&#39;article / actif dans index-news.html[/fr]', '1'),
('!N_nb_carac!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le nombre de caract&egrave;re suppl&eacute;mentaire de l&#39;article / actif dans index-news.html[/fr]', '1'),
('!N_read_more!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le lien &#39;lire la suite&#39; de l&#39;article / actif dans index-news.html[/fr]', '1'),
('!N_nb_comment!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le nombre de commentaire de l&#39;article / actif dans index-news.html[/fr]', '1'),
('!N_link_comment!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par lien vers les commentaires de l&#39;article / actif dans index-news.html[/fr]', '1'),
('!N_categorie!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par lien vers la cat&eacute;gorie de l&#39;article / actif dans index-news.html[/fr]', '1'),
('!N_previous_article!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le lien HTML pointant sur l&#39;article pr&eacute;c&eacute;dent / actif detail-news.html[/fr]', '1'),
('!N_next_article!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le lien HTML sur l&#39;article suivant / actif dans detail-news.html[/fr]', '1'),
('!N_boxrel_title!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par titre du bloc &#39;lien relatif&#39; / actif dans detail-news.html[/fr]', '1'),
('!N_boxrel_stuff!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le contenu du bloc &#39;lien relatif&#39; / actif dans detail-news.html[/fr]', '1'),
('!B_title!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le titre du bloc / actif dans bloc.html[/fr]', '1'),
('!B_content!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par par le contenu du bloc / actif dans bloc.html[/fr]', '1'),
('!B_class_title!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par la class CSS du titre - voir le gestionnaire de bloc de NPDS / actif dans bloc.html[/fr]', '1'),
('!B_class_content!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par la class CSS du contenu - voir le gestionnaire de bloc de NPDS  / actif dans bloc.html[/fr]', '1'),
('!editorial_content!', '', 'them', '-', NULL, '[fr]Sera remplac&eacute; par le contenu de l&#39;Edito / actif que si editorial.html existe dans votre theme[/fr]', '1'),
('!PHP!', '', 'them', '-', NULL, '[fr]Int&eacute;gration de code PHP \"noy&eacute;\" dans vos fichiers html de th&egrave;mes :<br />\r\n=> !PHP! commentaire vous permettant de trouver le php noy&eacute; -> in fine sera remplac&eacute; par \"\"<br />\r\n=> &lt;!--meta  doit pr&eacute;c&eacute;der votre code php -> in fine sera remplac&eacute; par \"\"<br />\r\n   => meta-->   doit suivre votre code php -> in fine sera remplac&eacute; par \"\"<br />\r\n<br />\r\n&nbsp;Exemple :<br />\r\n&nbsp;&nbsp;!PHP!&lt;!--meta<br />\r\n&nbsp;&nbsp;&lt;?php<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;global $cookie;<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;$username = $cookie[1];<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;if ($username == \"\") {<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo \"Create an account\";<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;} else {<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo \"Welcome : $username\";<br />\r\n&nbsp;&nbsp;&nbsp;&nbsp;}<br />\r\n&nbsp;&nbsp;?><br />\r\n&nbsp;&nbsp;meta-->[/fr]', '1'),
('!blocnote!', 'function MM_blocnote($arg) {}', 'meta', '-', NULL, '[fr]Fabrique un blocnote contextuel en lieu et place du meta-mot / syntaxe : !blocnote!ID - ID = Id du bloc de droite dans le gestionnaire de bloc de NPDS[/fr]', '0'),
('!forumP!', 'function MM_forumP() {}', 'meta', '-', '', '[fr].les derniers posts sur les forums.[/fr][en]...[/en]', '0'),
('espace_groupe', 'function MM_espace_groupe($gr, $t_gr, $i_gr) {}', 'meta', '-', NULL, '[fr]Fabrique un WorkSpace / syntaxe : espace_groupe(groupe_id, aff_name_groupe, aff_img_groupe) ou groupe_id est l\'ID du groupe - aff_name_groupe(0 ou 1) permet d\'afficher le nom du groupe - aff_img_groupe(0 ou 1) permet d\'afficher l\'image associ&eacute;e au groupe.[/fr]', '1'),
('yt_video', 'function MM_yt_video($id_yt_video) {}', 'meta', '-', '', '[fr]Inclusion video Youtube. Syntaxe : yt_video(ID de la vid&eacute;o)[/fr][en]Include a Youtube video. Syntax : yt_video(video ID)[/en][zh]&#x5305;&#x542B;Youtube&#x89C6;&#x9891;&#x3002;&#x53E5;&#x6CD5; : yt_video(&#x89C6;&#x9891; ID)[/zh][es]Incluye un video de Youtube. Sintaxis : yt_video(video ID)[/es][de]F&uuml;gen Sie ein Youtube-Video hinzu. Syntax : yt_video(video ID)[/de]', '1'),
('topic_subscribe', 'function MM_topic_subscribe($arg) {}', 'meta', '-', '', '[fr]Affiche les noms des sujets avec la situation de l\'abonnement du membre. Permet au membre de g&eacute;rer ces abonnements (aux sujets).\r\nSyntaxe : topic_subscribe(X) ou X indique le niveau de rupture dans la liste[/fr][en]...[/en]', '1'),
('dm_video', 'function MM_dm_video($id_dm_video) {}\r\n', 'meta', '-', NULL, '[fr]Inclusion video Dailymotion. Syntaxe : dm_video(ID de la vid&eacute;o)[/fr][en]Include a Dailymotion video. Syntax : dm_video(video ID)[/en][zh]&#x5305;&#x542B;Dailymotion&#x89C6;&#x9891;&#x3002;&#x53E5;&#x6CD5; : dm_video(&#x89C6;&#x9891; ID)[/zh][es]Incluye un video de Dailymotion. Sintaxis : dm_video(video ID)[/es][de]F&uuml;gen Sie ein Dailymotion-Video hinzu. Syntax : dm_video(video ID)[/de]\r\n', '1'),
('vm_video', 'function MM_vm_video($id_vm_video) {}', 'meta', '-', '', '[fr]Inclusion video Vimeo. Syntaxe : vm_video(ID de la vid&eacute;o)[/fr][en]Include a Vimeo video. Syntax : vm_video(video ID)[/en][zh]&#x5305;&#x542B;Vimeo&#x89C6;&#x9891;&#x3002;&#x53E5;&#x6CD5; : vm_video(&#x89C6;&#x9891; ID)[/zh][es]Incluye un video de Vimeo. Sintaxis : vm_video(video ID)[/es][de]F&uuml;gen Sie ein Vimeo-Video hinzu. Syntax : vm_video(video ID)[/de]', '1'),
('topic_subscribeOFF', 'function MM_topic_subscribeOFF() {}', 'meta', '-', '', '[fr]Ferme la FORM de gestion des abonnements (attention a l\'imbrication de FORM)[/fr][en]...[/en]', '1'),
('topic_subscribeON', 'function MM_topic_subscribeON() {}', 'meta', '-', '', '[fr]Ouvre la FORM de gestion des abonnements (attention a l\'imbrication de FORM)[/fr][en]...[/en]', '1'),
('top_reviews', 'function MM_top_reviews ($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x des critiques / syntaxe : top_reviews(x)[/fr]', '1'),
('top_authors', 'function MM_top_authors ($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x des auteurs / syntaxe : top_authors(x)[/fr]', '1'),
('top_polls', 'function MM_top_polls ($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x des sondages / syntaxe : top_polls(x)[/fr]', '1'),
('top_storie_authors', 'function MM_top_storie_authors ($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x des auteurs de news (membres) / syntaxe : top_storie_authors(x)[/fr]', '1'),
('topic_all', 'function MM_topic_all() {}', 'meta', '-', '', '[fr]Affiche les sujets avec leurs images.<br />Syntaxe : topic_all()[/fr][en]...[/en]', '1'),
('forum_recherche', 'function MM_forum_recherche() {}', 'meta', '-', NULL, '[fr]Affiche la zone de saisie du moteur de recherche des forums[/fr]', '1'),
('forum_icones', 'function MM_forum_icones() {}', 'meta', '-', NULL, '[fr]Affiche les icones + legendes decrivant les marqueurs des forums[/fr]', '1'),
('forum_subscribeON', 'function MM_forum_subscribeON() {}', 'meta', '-', NULL, '[fr]Ouvre la FORM de gestion des abonnements (attention a l\'imbrication de FORM)[/fr]', '1'),
('forum_bouton_subscribe', 'function MM_forum_bouton_subscribe() {}', 'meta', '-', NULL, '[fr]Affiche le bouton de gestion des abonnements[/fr]', '1'),
('forum_subscribeOFF', 'function MM_forum_subscribeOFF() {}', 'meta', '-', NULL, '[fr]Ferme la FORM de gestion des abonnements (attention a l\'imbrication de FORM)[/fr]', '1'),
('forum_subfolder', 'function MM_forum_subfolder($arg) {}', 'meta', '-', '', '[fr]Renvoie le gif permettant de savoir si de nouveaux messages sont disponibles dans le forum X<br />Syntaxe : sub_folder(X) ou X est le num&eacute;ro du forum[/fr][en][/en]', '1'),
('insert_flash', 'function MM_insert_flash($name,$width,$height,$bgcol) {}', 'meta', '-', NULL, '[fr]Insert un fichier flash (.swf) se trouvant dans un dossier \"flash\" de la racine du site. Syntaxe : insert_flash (nom du fichier.swf, largeur, hauteur, couleur fond : #XXYYZZ).[/fr]', '1'),
('!mailadmin!', '$cmd=\"<a href=\\\"mailto:\".\\npds\\system\\utility\\spam::anti_spam(\\npds\\system\\config\\Config::get(\'app.adminmail\'), 1).\"\\\" target=\\\"_blank\\\">\".\\npds\\system\\utility\\spam::anti_spam(\\npds\\system\\config\\Config::get(\'app.adminmail\'), 0).\"</a>\";', 'meta', '-', NULL, '[fr]Affiche un lien vers l\'adresse mail de l\'administrateur.[/fr]', '1'),
('!login!', 'function MM_login() {}', 'meta', '-', NULL, '[fr]Affiche les champs de connexion et d\'inscription au site, ou le lien de d&eacute;connexion si vous &ecirc;tes connect&eacute; en tant que membre.[/fr][en]Shows the site login and registration fields, or the logout link if you are logged in as a member.[/en][zh]&#x663E;&#x793A;&#x7AD9;&#x70B9;&#x767B;&#x5F55;&#x548C;&#x6CE8;&#x518C;&#x5B57;&#x6BB5;&#xFF0C;&#x5982;&#x679C;&#x60A8;&#x4EE5;&#x4F1A;&#x5458;&#x8EAB;&#x4EFD;&#x767B;&#x5F55;&#xFF0C;&#x5219;&#x663E;&#x793A;&#x6CE8;&#x9500;&#x94FE;&#x63A5;&#x3002;[/zh][es]Muestra los campos de inicio de sesi&oacute;n y registro del sitio, o el enlace de cierre de sesi&oacute;n si ha iniciado sesi&oacute;n como miembro.[/es][de]Zeigt die Anmelde- und Registrierungsfelder der Website oder den Abmeldelink an, wenn Sie als Mitglied angemeldet sind.[/de]', '1'),
('!connexion!', '$cmd=\\npds\\system\\language\\metalang::meta_lang(\"!login!\");', 'meta', '-', NULL, '[fr]Alias de !login![/fr]', '1'),
('!administration!', 'function MM_administration() {}', 'meta', '-', NULL, '[fr]Affiche un lien vers l\'administration du site uniquement si l\'on est connect&eacute; en tant qu\'admin[/fr]', '1'),
('admin_infos', 'function MM_admin_infos($arg) {}', 'meta', '-', NULL, '[fr]Affiche le Nom ou le WWW ou le Mail de l\'administrateur / syntaxe : admin_infos(nom_de_admin)[/fr]', '1'),
('theme_img', 'function MM_theme_img($arg) {}', 'meta', '-', NULL, '[fr]Localise l\'image et affiche une ressource de type &lt;img src= / syntaxe : theme_img(forum/onglet.gif)[/fr]', '1'),
('!logo!', '$cmd=\"<img src=\\\"\".\\npds\\system\\config\\Config::get(\'npds.site_logo\').\"\\\" border=\\\"0\\\" alt=\\\"\\\">\";', 'meta', '-', NULL, '[fr]Affiche le logo du site (admin/preferences).[/fr]', '1'),
('rotate_img', 'function MM_rotate_img($arg) {}', 'meta', '-', NULL, '[fr]Affiche une image al&eacute;atoire - les images de la liste sont s&eacute;par&eacute;e par une virgule / syntaxe rotate_img(\"http://www.npds.org/storage/users_private/user/1.gif,http://www.npds.org/storage/users_private/user/2.gif, ...\")[/fr]', '1'),
('!sql_nbREQ!', 'function MM_sql_nbREQ() {}', 'meta', '-', NULL, '[fr]Affiche le nombre de requ&ecirc;te SQL pour la page courante[/fr]', '1'),
('comment_system', 'function MM_comment_system ($file_name,$topic) {}', 'meta', '-', '', '[fr]Permet de mettre en oeuvre un syst&egrave;me de commentaire complet / la mise en oeuvre n&eacute;cessite :<br /> - un fichier dans modules/comments/xxxx.conf.php de la m&ecirc;me structure que les autres<br /> - un appel coh&eacute;rent avec la configuration de ce fichier<br /><br />L\'appel est du type : comments($file_name, $topic) - exemple comment_system(edito,1) - le fichier s\'appel donc config/edito.conf.php[/fr]', '1'),
('top_stories', 'function MM_top_stories ($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x articles / syntaxe : top_stories(x)[/fr]', '1'),
('top_commented_stories', 'function MM_top_commented_stories ($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x articles les plus comment&eacute;s / syntaxe : top_commented_stories(x)[/fr]', '1'),
('top_categories', 'function MM_top_categories($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x cat&eacute;gories des articles / syntaxe : top_categories(x)[/fr]', '1'),
('top_sections', 'function MM_top_sections ($arg) {}', 'meta', '-', '', '[fr]Affiche le titre et un lien sur les Top x articles des rubriques / syntaxe : top_sections(x)[/fr]', '1'),
('forum_message', 'function MM_forum_message() {}', 'meta', '-', NULL, '[fr]Affiche les messages en pied de forum (devenez membre, abonnement ...)[/fr]', '1'),
('forum_categorie', 'function MM_forum_categorie($arg) {}', 'meta', '-', NULL, '[fr]affiche la (les) categorie(s) XX (en fonction des droits) / liste de categories : \"XX,YY,ZZ\" [/fr]', '1'),
('forum_all', 'function MM_forum_all() {}', 'meta', '-', NULL, '[fr]Affiche toutes les categories et tous les forums (en fonction des droits)[/fr]', '1'),
('!debugOFF!', 'function MM_debugOFF() {}', 'meta', '-', NULL, '[fr]D&eacute;sactive le mode debug[/fr]', '1'),
('!debugON!', 'function MM_debugON() {}', 'meta', '-', NULL, '[fr]Active le mode debug[/fr]', '1'),
('!/!', '!\\/!', 'meta', '-', NULL, '[fr]Termine LES meta-mot ENCADRANTS (!groupe_text!, !note!, !note_admin!, ...) : le fonctionnement est assez similaire &agrave; [langue] ...[/fr]', '1'),
('!note_admin!', 'function MM_note_admin() {}', 'meta', '-', NULL, '[fr]Permet de stocker une note en ligne qui ne sera affich&eacute;e que pour les administrateurs !note_admin! .... !/![/fr]', '1'),
('!note!', 'function MM_note() {}', 'meta', '-', NULL, '[fr]Permet de stocker une note en ligne qui ne sera jamais affich&eacute;e !note! .... !/![/fr]', '1'),
('no_groupe_text', 'function MM_no_groupe_text($arg) {}', 'meta', '-', NULL, '[fr]Forme de ELSE de groupe_text / Test si le membre n\'appartient pas aux(x) groupe(s) et n\'affiche que le texte encadr&eacute; par no_groupe_textID(ID_group) ... !/!<br />Si no_groupe_ID est nul, la v&eacute;rification portera sur qualit&eacute; d\'anonyme<br />Syntaxe : no_groupe_text(), no_groupe_text(10) ou no_groupe_textID(\"gp1,gp2,gp3\") ... !/![/fr]', '1'),
('groupe_text', 'function MM_groupe_text($arg) {}', 'meta', '-', NULL, '[fr]Test si le membre appartient aux(x) groupe(s) et n\'affiche que le texte encadr&eacute; par groupe_textID(ID_group) ... !/!<br />Si groupe_ID est nul, la v&eacute;rification portera simplement sur la qualit&eacute; de membre<br />Syntaxe : groupe_text(), groupe_text(10) ou groupe_textID(\"gp1,gp2,gp3\") ... !/![/fr]', '1'),
('!langue!', '$cmd=\\npds\\system\\language\\language::aff_local_langue(\"index.php\", \"choice_user_language\",\"\");', 'meta', '-', NULL, '[fr]Fabrique une zone de selection des langues disponibles[/fr]', '1'),
(':=!', '$cmd=MM_img(\"forum/smilies/yaisse.gif\");', 'smil', '-', NULL, NULL, '1'),
(':b', '$cmd=MM_img(\"forum/smilies/icon_tongue.gif\");', 'smil', '-', NULL, NULL, '1'),
(':D', '$cmd=MM_img(\"forum/smilies/icon_grin.gif\");', 'smil', '-', NULL, NULL, '1'),
(':#', '$cmd=MM_img(\"forum/smilies/icon_ohwell.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-o', '$cmd=MM_img(\"forum/smilies/icon_eek.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-?', '$cmd=MM_img(\"forum/smilies/icon_confused.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-|', '$cmd=MM_img(\"forum/smilies/icon_mad.gif\");', 'smil', '-', NULL, NULL, '1'),
(':|', '$cmd=MM_img(\"forum/smilies/icon_mad2.gif\");', 'smil', '-', NULL, NULL, '1'),
(':paf', '$cmd=MM_img(\"forum/smilies/pafmur.gif\");', 'smil', '-', NULL, NULL, '1'),
('dateL', '$cmd=date(\"d/m/Y\");', 'meta', '-', NULL, '[fr]Date longue JJ/MM/YYYY[/fr]', '1'),
('heureC', '$cmd=date(\"H:i\");', 'meta', '-', NULL, '[fr]Heure courte HH:MM[/fr]', '1'),
('heureL', '$cmd=date(\"H:i:s\");', 'meta', '-', NULL, '[fr]Heure longue HH:MM:SS[/fr]', '1'),
('dateC', '$cmd=date(\"d/m/y\");', 'meta', '-', NULL, '[fr]Date longue JJ/MM[/fr]', '1'),
('!a/!', '&#92;', 'meta', '-', NULL, '[fr]anti-slash[/fr]', '1'),
('!sc_infos_else!', '&nbsp;', 'meta', '-', NULL, '[fr]affiche les informations de SuperCache[/fr]', '1'),
('!sc_infos!', '$cmd=\\npds\\system\\cache\\cache::SC_infos();', 'meta', '-', NULL, '[fr]affiche les informations de SuperCache[/fr]', '1'),
('!slogan!', '$cmd=\\npds\\system\\config\\Config::get(\'npds.slogan\');', 'meta', '-', NULL, '[fr]variable global $slogan[/fr]', '1'),
('!theme!', '$cmd=\\npds\\system\\theme\\theme::getTheme();', 'meta', '-', NULL, '[fr]variable global $theme[/fr]', '1'),
('!sitename!', '$cmd=\\npds\\system\\config\\Config::get(\'npds.sitename\');', 'meta', '-', NULL, '[fr]variable global $sitename[/fr]', '1'),
('Scalcul', 'function (){}', 'meta', '-', NULL, '[fr]Retourne la valeur du calcul : syntaxe Scalcul(op,nombre,nombre) ou op peut &ecirc;tre : + - * /[/fr]', '1'),
('!anti_spam!', 'function MM_anti_spam ($arg) {}', 'meta', '-', NULL, '[fr]Encode un email et cr&eacute;e un &lta href=mailto ...&gtEmail&lt/a&gt[/fr]', '1'),
('!msg_foot!', 'function MM_msg_foot() {}', 'meta', '-', NULL, '[fr]Gestion des messages du footer du theme (les 4 pieds de page dans Admin / Pr&eacute;f&eacute;rences)[/fr]', '1'),
('!date!', 'function MM_date () {}', 'meta', '-', NULL, '[fr]Date du jour - se base sur le format de daydate (fichier de traduction)[/fr]', '1'),
('!banner!', 'function MM_banner () {}', 'meta', '-', NULL, '[fr]Syst&egrave;me de banni&egrave;re[/fr]', '1'),
('!search_topics!', 'function MM_search_topics() {}', 'meta', '-', NULL, '[fr]Liste des Topic => Moteur de recherche interne (Combo)[/fr]', '1'),
('!search!', 'function MM_search() {}', 'meta', '-', NULL, '[fr]Ligne de saisie => Moteur de recherche[/fr]', '1'),
('!member!', 'function MM_member() {}', 'meta', '-', NULL, '[fr]Ligne Anonyme / membre gestion du compte / Message Interne (MI)[/fr]', '1'),
('!nb_online!', 'function MM_nb_online() {}', 'meta', '-', NULL, '[fr]Nombre de session active[/fr]', '1'),
('!whoim!', 'function MM_whoim() {}', 'meta', '-', NULL, '[fr]Affiche Qui est en ligne ? + message de bienvenue[/fr]', '1'),
('!membre_nom!', 'function MM_membre_nom() {}', 'meta', '-', NULL, '[fr]R&eacute;cup&eacute;ration du nom du membre ou rien[/fr]', '1'),
('!membre_pseudo!', 'function MM_membre_pseudo() {}', 'meta', '-', NULL, '[fr]R&eacute;cup&eacute;ration du pseudo du membre ou rien[/fr]', '1'),
('blocID', 'function MM_blocID($arg) {}', 'meta', '-', NULL, '[fr]Fabrique un bloc R (droite) ou L (gauche) en s\'appuyant sur l\'ID (voir gestionnaire de blocs) pour incorporation / syntaxe : blocID(R1) ou blocID(L2)[/fr]', '1'),
('!block!', 'function MM_block($arg) {}', 'meta', '-', NULL, '[fr]Alias de blocID()[/fr]', '1'),
('leftblocs', 'function MM_leftblocs($arg) {}', 'meta', '-', NULL, '[fr]Fabrique tous les blocs de gauche[/fr]', '1'),
('rightblocs', 'function MM_rightblocs($arg) {}', 'meta', '-', NULL, '[fr]Fabrique tous les blocs de droite[/fr]', '1'),
('articleID', 'function MM_articleID($arg) {}', 'meta', '-', NULL, '[fr]R&eacute;cup&eacute;ration du titre et fabrication d\'une url pointant sur l\'article (ID)[/fr]', '1'),
('!article!', 'function MM_article($arg) {}', 'meta', '-', NULL, '[fr]Alias d\'articleID[/fr]', '1'),
('article_completID', 'function MM_article_completID($arg) {}', 'meta', '-', NULL, '[fr]R&eacute;cup&eacute;ration de l\'article complet (ID) et themisation pour incorporation<br />si ID > 0   : l\'article publi&eacute; avec l\'ID indiqu&eacute;e<br />si ID = 0   : le dernier article publi&eacute;<br />si ID = -1  : l\'avant dernier ... jusqu\'&agrave; -9 (limite actuelle)[/fr]', '1'),
('!article_complet!', 'function MM_article_complet($arg) {}', 'meta', '-', NULL, '[fr]Alias de article_completID[/fr]', '1'),
('headlineID', 'function MM_headlineID($arg) {}', 'meta', '-', NULL, '[fr]R&eacute;cup&eacute;ration du canal RSS (ID) et fabrication d\'un retour pour affichage[/fr]', '1'),
('!headline!', 'function MM_headline($arg) {}', 'meta', '-', NULL, '[fr]Alias de headlineID[/fr]', '1'),
('!list_mns!', 'function MM_list_mns() {}', 'meta', '-', NULL, '[fr]Affiche une liste de tout les membres poss&eacute;dant un minisite avec un lien vers ceux-ci[/fr]', '1'),
('!LastMember!', 'function MM_LastMember() {}', 'meta', '-', NULL, '[fr]Renvoie le pseudo du dernier membre inscrit[/fr]', '1'),
('!edito!', 'function MM_edito() {}', 'meta', '-', NULL, '[fr]Fabrique et affiche l\'EDITO[/fr]', '1'),
('!edito-notitle!', '$cmd=\"!edito-notitle!\";', 'meta', '-', NULL, '[fr]Supprime le Titre EDITO et le premier niveau de tableau dans l\'edito (ce meta-mot n\'est actif que dans l\'Edito)[/fr]', '1'),
('Dev', 'Developpeur', 'mot', '-', NULL, NULL, '0'),
('NPDS', '<a href=\"http://www.npds.org\" target=\"_blank\" title=\"www.npds.org\">NPDS</a>', 'mot', '-', NULL, NULL, '1'),
(':-)', '$cmd=MM_img(\"forum/smilies/icon_smile.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-]', '$cmd=MM_img(\"forum/smilies/icon_smile.gif\");', 'smil', '-', NULL, NULL, '1'),
(';-)', '$cmd=MM_img(\"forum/smilies/icon_wink.gif\");', 'smil', '-', NULL, NULL, '1'),
(';-]', '$cmd=MM_img(\"forum/smilies/icon_wink.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-(', '$cmd=MM_img(\"forum/smilies/icon_frown.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-[', '$cmd=MM_img(\"forum/smilies/icon_frown.gif\");', 'smil', '-', NULL, NULL, '1'),
('8-)', '$cmd=MM_img(\"forum/smilies/icon_cool.gif\");', 'smil', '-', NULL, NULL, '1'),
('8-]', '$cmd=MM_img(\"forum/smilies/icon_cool.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-P', '$cmd=MM_img(\"forum/smilies/icon_razz.gif\");', 'smil', '-', NULL, NULL, '1'),
(':-D', '$cmd=MM_img(\"forum/smilies/icon_biggrin.gif\");', 'smil', '-', NULL, NULL, '1'),
('noforbadmail', 'function MM_noforbadmail() {}', 'meta', '-', NULL, '[fr]Test si le membre est dans la liste des mails incorrects.\r\n Syntaxe : noforbadmail() ... !/![/fr]\r\n', '1');

-- --------------------------------------------------------

--
-- Structure de la table `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `mid` int NOT NULL AUTO_INCREMENT,
  `mnom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `minstall` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`mid`),
  KEY `mnom` (`mnom`(100))
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `modules`
--

INSERT INTO `modules` (`mid`, `mnom`, `minstall`) VALUES
(1, 'marquetapage', 0),
(2, 'push', 0),
(3, 'marquetapage', 0),
(4, 'push', 0);

-- --------------------------------------------------------

--
-- Structure de la table `optimy`
--

DROP TABLE IF EXISTS `optimy`;
CREATE TABLE IF NOT EXISTS `optimy` (
  `optid` int NOT NULL AUTO_INCREMENT,
  `optgain` decimal(10,3) DEFAULT NULL,
  `optdate` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opthour` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `optcount` int DEFAULT '0',
  PRIMARY KEY (`optid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `optimy`
--

INSERT INTO `optimy` (`optid`, `optgain`, `optdate`, `opthour`, `optcount`) VALUES
(1, '0.000', '04-04-24', '12:53 pm', 8);

-- --------------------------------------------------------

--
-- Structure de la table `poll_data`
--

DROP TABLE IF EXISTS `poll_data`;
CREATE TABLE IF NOT EXISTS `poll_data` (
  `pollID` int NOT NULL DEFAULT '0',
  `optionText` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `optionCount` int NOT NULL DEFAULT '0',
  `voteID` int NOT NULL DEFAULT '0',
  `pollType` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `poll_data`
--

INSERT INTO `poll_data` (`pollID`, `optionText`, `optionCount`, `voteID`, `pollType`) VALUES
(2, '', 0, 12, 0),
(2, '', 0, 11, 0),
(2, '', 0, 10, 0),
(2, '', 0, 9, 0),
(2, '', 0, 8, 0),
(2, '', 0, 7, 0),
(2, '', 0, 6, 0),
(2, '', 0, 5, 0),
(2, 'Passable', 0, 4, 0),
(2, 'Moyen', 0, 3, 0),
(2, 'Bien', 1, 2, 0),
(2, 'Super', 1, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `poll_desc`
--

DROP TABLE IF EXISTS `poll_desc`;
CREATE TABLE IF NOT EXISTS `poll_desc` (
  `pollID` int NOT NULL AUTO_INCREMENT,
  `pollTitle` char(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timeStamp` int NOT NULL DEFAULT '0',
  `voters` mediumint NOT NULL DEFAULT '0',
  PRIMARY KEY (`pollID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `poll_desc`
--

INSERT INTO `poll_desc` (`pollID`, `pollTitle`, `timeStamp`, `voters`) VALUES
(2, 'NPDS', 1004108978, 2);

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` int NOT NULL AUTO_INCREMENT,
  `post_idH` int NOT NULL DEFAULT '0',
  `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `topic_id` int NOT NULL DEFAULT '0',
  `forum_id` int NOT NULL DEFAULT '0',
  `poster_id` int DEFAULT NULL,
  `post_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `post_time` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster_ip` varchar(54) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster_dns` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `post_aff` tinyint UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`post_id`),
  KEY `forum_id` (`forum_id`),
  KEY `topic_id` (`topic_id`),
  KEY `post_aff` (`post_aff`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`post_id`, `post_idH`, `image`, `topic_id`, `forum_id`, `poster_id`, `post_text`, `post_time`, `poster_ip`, `poster_dns`, `post_aff`) VALUES
(1, 0, '00.png', 1, 1, 2, 'Demo', '2011-10-26 17:00', '1.1.76.115', '', 1),
(2, 0, '01.png', 1, 1, 2, 'R&eacute;ponse', '2012-03-05 22:36', '1.1.76.115', '', 1),
(3, 0, '00.png', 2, 2, 1, 'Message 1', '2013-05-14 22:54', '1.1.76.115', '', 1),
(4, 3, '01.png', 2, 2, 1, 'R&eacute;ponse au Message 1', '2003-05-14 22:54', '1.1.76.115', '', 1),
(5, 4, '02.png', 2, 2, 1, 'R&eacute;ponse &agrave; la r&eacute;ponse du Message 1', '2013-05-14 22:55', '1.1.76.115', '', 1),
(6, 0, '03.png', 2, 2, 1, 'R&eacute;ponse au Message 1', '2013-05-14 22:55', '1.1.76.115', '', 1),
(7, 0, '04.png', 2, -2, 2, 'Bien, bien et m&ecirc;me mieux encore', '2012-07-22 13:42:22', '1.1.76.115', '', 1),
(8, 0, '00.png', 1, 1, 1, 'sdfgsdfg', '2024-03-15 17:09:02', '%3A%3A1', '', 1),
(9, 0, '00.png', 1, 1, 1, 'fgsdgsfgsdfg', '2024-03-15 17:11:00', '%3A%3A1', '', 1),
(10, 0, '00.png', 1, 1, 1, 'qsdfqsdfqsd', '2024-03-15 17:22:03', '%3A%3A1', '', 1),
(11, 0, '00.png', 1, 1, 1, 'qsdfqsdfqsd', '2024-03-15 17:22:58', '%3A%3A1', '', 1),
(12, 0, '00.png', 1, 1, 1, 'qsdfqsdfqsd', '2024-03-15 17:23:16', '%3A%3A1', '', 1),
(13, 0, '00.png', 1, 1, 1, 'qsdfqsdfqsd', '2024-03-15 17:23:30', '%3A%3A1', '', 1),
(14, 0, '00.png', 1, 1, 1, 'qsdfqsdfqsd', '2024-03-15 17:23:50', '%3A%3A1', '', 1),
(15, 0, '00.png', 1, 1, 1, 'ghsdfgdsgsdfgs\r\nsdfg\r\ndsf\r\ngds\r\nfg\r\ndsf\r\ngs\r\ndf\r\ngds\r\nfg', '2024-03-15 17:24:36', '%3A%3A1', '', 1),
(16, 0, '', 8, -1, 2, 'gfh', '2024-03-16 05:11:55', '%3A%3A1', '%3A%3A1', 1),
(17, 0, '00.png', 1, 1, 2, 'dsfgsd dsf gdfd fg\r\ndsg \r\n\r\ngdsf\r\ngsdf\r\ngds\r\ngds\r\nfgsd\r\nfdsf\r\n', '2024-04-04 14:33:16', '%3A%3A1', '', 1),
(18, 0, '02.png', 1, 1, 2, 'sdfgsdf', '2024-04-08 03:00:26', '%3A%3A1', '', 1),
(19, 0, '02.png', 1, 1, 2, 'sdfgsdf', '2024-04-08 03:06:12', '%3A%3A1', '', 1),
(20, 0, '02.png', 1, 1, 2, 'sdfgsdf', '2024-04-08 03:07:41', '%3A%3A1', '', 1),
(21, 0, '02.png', 1, 1, 2, 'sdfgsdf', '2024-04-08 03:08:21', '%3A%3A1', '', 1),
(23, 0, '02.png', 1, 8, 2, 'sdfgsdf', '2024-04-08 03:08:21', '%3A%3A1', '', 1);

-- --------------------------------------------------------

--
-- Structure de la table `priv_msgs`
--

DROP TABLE IF EXISTS `priv_msgs`;
CREATE TABLE IF NOT EXISTS `priv_msgs` (
  `msg_id` int NOT NULL AUTO_INCREMENT,
  `msg_image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_userid` int NOT NULL DEFAULT '0',
  `to_userid` int NOT NULL DEFAULT '0',
  `msg_time` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msg_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `read_msg` tinyint NOT NULL DEFAULT '0',
  `type_msg` int NOT NULL DEFAULT '0',
  `dossier` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '...',
  PRIMARY KEY (`msg_id`),
  KEY `to_userid` (`to_userid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `priv_msgs`
--

INSERT INTO `priv_msgs` (`msg_id`, `msg_image`, `subject`, `from_userid`, `to_userid`, `msg_time`, `msg_text`, `read_msg`, `type_msg`, `dossier`) VALUES
(1, '18.png', 'Nouvelles du groupe test', 1, 2, '30-03-2024 21:51', 'Vous faites d&eacute;sormais partie des membres du groupe : test [2].', 1, 0, '...');

-- --------------------------------------------------------

--
-- Structure de la table `publisujet`
--

DROP TABLE IF EXISTS `publisujet`;
CREATE TABLE IF NOT EXISTS `publisujet` (
  `aid` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `secid2` int NOT NULL DEFAULT '0',
  `type` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `queue`
--

DROP TABLE IF EXISTS `queue`;
CREATE TABLE IF NOT EXISTS `queue` (
  `qid` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` mediumint NOT NULL DEFAULT '0',
  `uname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `story` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bodytext` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `timestamp` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `topic` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Linux',
  `date_debval` datetime DEFAULT NULL,
  `date_finval` datetime DEFAULT NULL,
  `auto_epur` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`qid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rblocks`
--

DROP TABLE IF EXISTS `rblocks`;
CREATE TABLE IF NOT EXISTS `rblocks` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `member` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `Rindex` tinyint NOT NULL DEFAULT '0',
  `cache` mediumint UNSIGNED NOT NULL DEFAULT '0',
  `actif` smallint UNSIGNED NOT NULL DEFAULT '1',
  `css` tinyint(1) NOT NULL DEFAULT '0',
  `aide` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `Rindex` (`Rindex`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rblocks`
--

INSERT INTO `rblocks` (`id`, `title`, `content`, `member`, `Rindex`, `cache`, `actif`, `css`, `aide`) VALUES
(1, '[fr]Un Bloc ...[/fr][en]One Block ...[/en][zh]一块...[/zh][es]Un Bloque...[/es][de]Ein Block[/de]', 'Vous pouvez ajouter, éditer et supprimer des Blocs à votre convenance.', '0', 99, 0, 1, 0, ''),
(2, 'Information', '<p align=\"center\"><a href=\"http://www.npds.org\" target=\"_blank\"><img src=\"assets/images/powered/miniban-bleu.png\" border=\"0\" alt=\"npds_logo\" /></a></p>', '0', 0, 0, 1, 0, ''),
(3, 'Bloc membre', 'function#userblock', '0', 5, 0, 1, 0, ''),
(4, 'Lettre d\'information', 'function#lnlbox', '0', 6, 86400, 1, 0, ''),
(5, 'Anciens Articles', 'function#oldNews\r\nparams#$storynum', '0', 4, 3600, 1, 0, ''),
(7, 'Catégories', 'function#category', '0', 2, 28800, 1, 0, ''),
(8, 'Article du Jour', 'function#bigstory', '0', 3, 60, 1, 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `referer`
--

DROP TABLE IF EXISTS `referer`;
CREATE TABLE IF NOT EXISTS `referer` (
  `rid` int NOT NULL AUTO_INCREMENT,
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `related`
--

DROP TABLE IF EXISTS `related`;
CREATE TABLE IF NOT EXISTS `related` (
  `rid` int NOT NULL AUTO_INCREMENT,
  `tid` int NOT NULL DEFAULT '0',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewer` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `score` int NOT NULL DEFAULT '0',
  `cover` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hits` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reviews`
--

INSERT INTO `reviews` (`id`, `date`, `title`, `text`, `reviewer`, `email`, `score`, `cover`, `url`, `url_title`, `hits`) VALUES
(1, '2024-03-16', 'gs', 'sdfg', 'user', 'user@user.land', 10, '', '', '', 1),
(3, '2024-03-16', 'ertyery', 'eryerty', 'user', 'user@user.land', 10, '', '', '', 1);

-- --------------------------------------------------------

--
-- Structure de la table `reviews_add`
--

DROP TABLE IF EXISTS `reviews_add`;
CREATE TABLE IF NOT EXISTS `reviews_add` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewer` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `score` int NOT NULL DEFAULT '0',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews_main`
--

DROP TABLE IF EXISTS `reviews_main`;
CREATE TABLE IF NOT EXISTS `reviews_main` (
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reviews_main`
--

INSERT INTO `reviews_main` (`title`, `description`) VALUES
('[fr]Votre point de vue nous intéresse[/fr][en]Your point of view interests us[/en]', '[fr]Participez à la vie du site en apportant vos critiques mais restez toujours positif.[/fr][en]Participate in the life of the website by bringing your criticisms but always remain positive.[/en][zh]通过提出批评来参与网站的生活，但始终保持积极态度。[/zh][es]Participe en la vida del sitio web aportando sus críticas, pero siempre sea positivo.[/es][de]Nehmen Sie am Leben der Website teil, indem Sie Ihre Kritik einbringen, aber immer positiv bleiben.[/de]');

-- --------------------------------------------------------

--
-- Structure de la table `rubriques`
--

DROP TABLE IF EXISTS `rubriques`;
CREATE TABLE IF NOT EXISTS `rubriques` (
  `rubid` int NOT NULL AUTO_INCREMENT,
  `rubname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `intro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `enligne` tinyint(1) NOT NULL DEFAULT '0',
  `ordre` int NOT NULL DEFAULT '0',
  UNIQUE KEY `rubid` (`rubid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rubriques`
--

INSERT INTO `rubriques` (`rubid`, `rubname`, `intro`, `enligne`, `ordre`) VALUES
(1, 'Divers', '', 1, 9998),
(2, 'Presse-papiers', '', 0, 9999),
(3, 'Mod&egrave;le', '', 1, 0),
(4, 'test rubrique', '<p>un test</p>', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `seccont`
--

DROP TABLE IF EXISTS `seccont`;
CREATE TABLE IF NOT EXISTS `seccont` (
  `artid` int NOT NULL AUTO_INCREMENT,
  `secid` int NOT NULL DEFAULT '0',
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `counter` int NOT NULL DEFAULT '0',
  `author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ordre` int NOT NULL DEFAULT '0',
  `userlevel` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `timestamp` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`artid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `seccont`
--

INSERT INTO `seccont` (`artid`, `secid`, `title`, `content`, `counter`, `author`, `ordre`, `userlevel`, `timestamp`) VALUES
(1, 3, 'fghedh', '<p>dfhdfghdfghdfgh sdfgsdg fg dsdgsg sd ssdgsdgs</p>', 0, 'root', 99, '0', '1712355906');

-- --------------------------------------------------------

--
-- Structure de la table `seccont_tempo`
--

DROP TABLE IF EXISTS `seccont_tempo`;
CREATE TABLE IF NOT EXISTS `seccont_tempo` (
  `artid` int NOT NULL AUTO_INCREMENT,
  `secid` int NOT NULL DEFAULT '0',
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `counter` int NOT NULL DEFAULT '0',
  `author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ordre` int NOT NULL DEFAULT '0',
  `userlevel` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`artid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `secid` int NOT NULL AUTO_INCREMENT,
  `secname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `userlevel` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `rubid` int NOT NULL DEFAULT '3',
  `intro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ordre` int NOT NULL DEFAULT '0',
  `counter` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`secid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sections`
--

INSERT INTO `sections` (`secid`, `secname`, `image`, `userlevel`, `rubid`, `intro`, `ordre`, `counter`) VALUES
(1, 'Pages statiques', '', '0', 1, NULL, 0, 0),
(2, 'En instance', '', '0', 2, NULL, 0, 0),
(3, 'Modifications des th&egrave;mes', '', '', 3, '', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `username` varchar(54) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `time` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `host_addr` varchar(54) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `guest` int NOT NULL DEFAULT '0',
  `uri` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `agent` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  KEY `username` (`username`),
  KEY `time` (`time`),
  KEY `guest` (`guest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `session`
--

INSERT INTO `session` (`username`, `time`, `host_addr`, `guest`, `uri`, `agent`) VALUES
('user', '1712938580', '%3A%3A1', 0, '/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Structure de la table `sform`
--

DROP TABLE IF EXISTS `sform`;
CREATE TABLE IF NOT EXISTS `sform` (
  `cpt` int NOT NULL AUTO_INCREMENT,
  `id_form` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `passwd` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`cpt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stories`
--

DROP TABLE IF EXISTS `stories`;
CREATE TABLE IF NOT EXISTS `stories` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `catid` int NOT NULL DEFAULT '0',
  `aid` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `hometext` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bodytext` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `comments` int DEFAULT '0',
  `counter` mediumint UNSIGNED DEFAULT NULL,
  `topic` int NOT NULL DEFAULT '1',
  `informant` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ihome` int NOT NULL DEFAULT '0',
  `archive` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `date_finval` datetime DEFAULT NULL,
  `auto_epur` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`),
  KEY `catid` (`catid`),
  KEY `topic` (`topic`),
  KEY `informant` (`informant`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stories`
--

INSERT INTO `stories` (`sid`, `catid`, `aid`, `title`, `time`, `hometext`, `bodytext`, `comments`, `counter`, `topic`, `informant`, `notes`, `ihome`, `archive`, `date_finval`, `auto_epur`) VALUES
(1, 0, 'Root', 'Comment modifier et / ou supprimer EDITO', '2023-02-28 05:01:52', '<b>L\'EDITO </b>est la <b>premi&egrave;re chose que les visiteurs visualiseront</b> en arrivant sur votre nouveau <b>site NPDS</b>.<br /><br />Vous pouvez l\'<b>&eacute;diter</b> pour le personnaliser, ainsi que choisir de l\'afficher ou non. <br />Pour toute modification, l\'<b>&eacute;diteur int&eacute;gr&eacute; &agrave; NPDS</b> vous simplifiera &eacute;norm&eacute;ment la t&acirc;che !<br /><br />Enfin, vous pouvez d&eacute;cider dans les <i>pr&eacute;f&eacute;rences administrateur</i> de la page que vous souhaitez utiliser <b>comme index de votre site</b>:\r\nce n\'est donc pas forc&eacute;ment l\'EDITO, et votre imagination laissera entrevoir bien d\'autres possibilit&eacute;s !<br />', 'Vous pouvez, par exemple:<br /><ul>\r\n  <li>faire arriver vos visiteurs sur la <b>page des forums</b></li>\r\n  <li>faire arriver vos visiteurs sur <b>une page d&eacute;crivant votre site en utilisant les rubriques</b></li>\r\n  <li>....<br />\r\n  </li>\r\n</ul>', 0, 12, 1, 'user', '', 0, 0, '2112-01-01 00:00:00', 0),
(2, 0, 'Root', 'NPDS embarque un excellent &eacute;diteur HTML !', '2023-02-28 01:08:39', '<div class=\"mceTmpl\"><br /><div class=\"row my-2\"><br /><div class=\"col-sm-6\"><br /><p>L\'<span style=\"font-weight: bold;\">&eacute;diteur HTML</span> int&eacute;gr&eacute; dans <span style=\"font-weight: bold;\">NPDS</span> est vraiment <span style=\"font-style: italic;\">tr&egrave;s puissant</span> ! <a href=\"https://www.tiny.cloud\" target=\"_blank\" rel=\"noopener\">TinyMCE</a>, c\'est son nom, vous permet de saisir et de mettre en forme le texte directement depuis votre navigateur.</p><br /></div><br /><div class=\"col-sm-6\"><br /><p><span style=\"font-weight: bold;\">L\'envoi d\'images</span> sur votre site est <span style=\"font-style: italic;\">tr&egrave;s simple</span> si vous souhaitez illustrer vos textes, et vous pouvez aussi faire des <span style=\"font-weight: bold;\">copier/coller</span> depuis nimporte quel logiciel de <span style=\"font-weight: bold;\">traitement de texte</span> !</p><br /></div><br /><div class=\"col-sm-12\"><br /><p style=\"text-align: justify;\">Combin&eacute; au fonctions sp&eacute;cifique de NPDS : mod&egrave;les de pages, banques d\'images, metalang, &nbsp;upload ... etc &nbsp;cet <span style=\"font-weight: bold;\">&eacute;diteur HTML</span> vous permettra vraiment de publier un contenu richement mis en forme que ce soit depuis votre ordinateur ou votre smartphone !</p><br /></div><br /></div><br /></div>', '', 0, 17, 1, 'user', '', 0, 0, '2112-01-01 00:00:00', 0),
(3, 0, 'Root', 'Les modules de NPDS', '2023-02-28 15:11:32', '<div style=\"text-align: left;\"><br /><p>NPDS dispose de nombreux <strong>modules</strong> ajoutant des fonctionnalit&eacute;s tr&egrave;s diverses &agrave; votre site.</p><br /><p>Certain sont embarqu&eacute;s avec l\'archive : les <strong>modules du core&nbsp;</strong>(pr&ecirc;t &agrave; l\'emploi et non d&eacute;sinstallables car ils sont directement utilis&eacute;s par le core ) ; les <strong>modules externes&nbsp;</strong>installables et d&eacute;sinstallables &agrave; tout moment...</p><br /><p><strong>- Les modules core</strong></p><br /><ul><li>module contact</li><li>module upload</li><li>module de g&eacute;olocalisation</li><li>module de liens</li><li>module twitter</li><li>etc ...</li></ul><br /><p><strong>- Les modules externes disponibles dans l\'archive</strong></p><br /><ul><li>module d\'archive d\'article</li><li>module de marque page</li><li>module de bloc note</li><li>etc ...</li></ul><br /><strong>- Les modules externes non disponible dans l\'archive </strong></div><br /><div style=\"text-align: left;\"><br />Actuellement seulement 5 modules ont &eacute;t&eacute; mis &agrave; niveau (responsive design et compatibilit&eacute; 16.8) ils b&eacute;n&eacute;ficient tous d\'une installation automatique facile ...<br /><ul><li>npds_galerie</li><li>npds_glossaire</li><li>npds_annonce</li><li>npds_agenda</li><li>quizz</li></ul><br /><p>Vous pouvez les trouver en t&eacute;l&eacute;chargement ici <a href=\"https://github.com/npds\" target=\"_blank\" rel=\"noopener\">https://github.com/npds</a></p><br /><p>Beaucoup d\'autre modules (compatible avec la version 13) restent &agrave; mettre &agrave; jour pour qu\'ils fonctionnent avec les versions =&gt; 16 &nbsp;de NPDS. Ils sont disponibles ici <a href=\"http://modules.npds.org/\" target=\"_blank\" rel=\"noopener\">http://modules.npds.org</a></p><br /></div>', '', 0, 10, 2, 'user', '', 0, 0, '2112-01-01 00:00:00', 0),
(4, 0, 'Root', 'Les th&egrave;mes de NPDS', '2023-02-28 18:59:36', '<p style=\"text-align: left;\">NPDS 16.8 dispose de 9 th&egrave;mes graphiques dont 4 skinable (26 skins disponibles *)&nbsp;ce qui donne donc 109 visualisations diff&eacute;rentes du portail (et bien plus encore en utilisant les possibilit&eacute;s de configuration du fichier pages.php) !</p><br /><p style=\"text-align: left;\">* Vous pouvez visualiser ces diff&eacute;rents skins ici &nbsp;<a href=\"/themes/_skins/default\"><span class=\"fa fa-paint-brush fa-2x\">&nbsp;</span></a></p><br /><p>Vous pouvez aussi visiter <a title=\"Tous les THEMES pour NPDS\" href=\"http://styles.npds.org/\" target=\"_blank\" rel=\"noopener\">http://styles.npds.org</a>, bien que non &agrave; jour ce site avec plus de 100 th&egrave;mes disponibles pour les versions inf&eacute;rieures &agrave; 16 vous permettra certainement de trouver des id&eacute;es, conseils et tutoriels encore utiles pour cr&eacute;er votre propre th&egrave;me.</p>', '', 0, 58, 3, 'user', '', 0, 0, '2112-01-01 00:00:00', 0),
(5, 0, 'Root', 'qsdfqsdf', '2024-03-07 03:16:19', '<p>fQFqdsQDS</p>', '<p>qdsqS</p>', 0, 8, 2, 'Visiteur', '', 0, 0, '2123-03-07 02:46:01', 0),
(6, 0, 'Root', 'dfqsdqsf', '2024-03-07 03:28:42', '<p>qsdqQDS</p>', '<p>qdsQDS</p>', 0, 7, 2, 'Root', '', 0, 0, '2123-03-07 03:28:01', 0),
(7, 1, 'Root', 'qdqs', '2024-03-07 03:28:58', '<p>QDSQsqS</p>', '<p>qdsqSQd</p>', 0, 19, 2, 'Root', '', 0, 0, '2123-03-07 03:28:01', 0),
(8, 0, 'Root', 'dqsQSQd', '2024-03-07 03:29:32', '<p>qdsqQ</p>', '<p>QDSqsqDS</p>', 1, 20, 2, 'Root', '', 0, 0, '2123-03-07 03:29:01', 0),
(9, 0, 'Root', 'qdQDS', '2024-03-07 03:29:45', '<p>qdsQDS</p>', '<p>qdsqDSQ</p>', 0, 12, 2, 'Root', '', 0, 0, '2123-03-07 03:29:01', 0),
(10, 0, 'Root', 'cvbncvbn', '2024-03-16 05:18:18', '<p>cvbncvn</p>', '<p>vbncv</p>', 0, 37, 2, 'user', '', 0, 0, '2123-03-16 05:16:01', 0),
(11, 0, 'Root', 'cvbncvbn', '2024-03-16 05:18:18', '<p>cvbncvn</p>', '<p>vbncv</p>', 0, 40, 2, 'user', '', 0, 0, '2123-03-16 05:16:01', 0);

-- --------------------------------------------------------

--
-- Structure de la table `stories_cat`
--

DROP TABLE IF EXISTS `stories_cat`;
CREATE TABLE IF NOT EXISTS `stories_cat` (
  `catid` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`catid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stories_cat`
--

INSERT INTO `stories_cat` (`catid`, `title`, `counter`) VALUES
(1, 'test', 1);

-- --------------------------------------------------------

--
-- Structure de la table `subscribe`
--

DROP TABLE IF EXISTS `subscribe`;
CREATE TABLE IF NOT EXISTS `subscribe` (
  `topicid` tinyint DEFAULT NULL,
  `forumid` int DEFAULT NULL,
  `lnlid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `uid` int NOT NULL DEFAULT '0',
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `topics`
--

DROP TABLE IF EXISTS `topics`;
CREATE TABLE IF NOT EXISTS `topics` (
  `topicid` int NOT NULL AUTO_INCREMENT,
  `topicname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topicimage` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topictext` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter` int NOT NULL DEFAULT '0',
  `topicadmin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`topicid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `topics`
--

INSERT INTO `topics` (`topicid`, `topicname`, `topicimage`, `topictext`, `counter`, `topicadmin`) VALUES
(1, 'npds', 'npds.gif', 'NPDS', 0, NULL),
(2, 'modules', 'modules.gif', 'Modules', 0, NULL),
(3, 'styles', 'styles.gif', 'Styles', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `uname` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `femail` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_avatar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_regdate` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_occ` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_from` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_intrest` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_sig` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_viewemail` tinyint DEFAULT NULL,
  `user_theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_journal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hashkey` tinyint(1) NOT NULL DEFAULT '0',
  `storynum` tinyint NOT NULL DEFAULT '10',
  `umode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `uorder` tinyint(1) NOT NULL DEFAULT '0',
  `thold` tinyint(1) NOT NULL DEFAULT '0',
  `noscore` tinyint(1) NOT NULL DEFAULT '0',
  `bio` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ublockon` tinyint(1) NOT NULL DEFAULT '0',
  `ublock` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `commentmax` int NOT NULL DEFAULT '4096',
  `counter` int NOT NULL DEFAULT '0',
  `send_email` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `is_visible` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `mns` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `user_langue` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_lastvisit` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_lnl` tinyint UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`uid`, `name`, `uname`, `email`, `femail`, `url`, `user_avatar`, `user_regdate`, `user_occ`, `user_from`, `user_intrest`, `user_sig`, `user_viewemail`, `user_theme`, `user_journal`, `pass`, `hashkey`, `storynum`, `umode`, `uorder`, `thold`, `noscore`, `bio`, `ublockon`, `ublock`, `theme`, `commentmax`, `counter`, `send_email`, `is_visible`, `mns`, `user_langue`, `user_lastvisit`, `user_lnl`) VALUES
(1, '', 'Anonyme', '', '', '', 'blank.gif', '989445600', '', '', '', '', 0, '0', '', '', 0, 10, '', 0, 0, 0, '', 0, '', '', 4096, 0, 0, 1, 0, NULL, NULL, 1),
(2, 'user', 'user', 'user@user.land', '', 'http://www.userland.com', '014.gif', '989445600', '', '', '', 'User of the Land', 0, '0', '', '$2y$11$Pt1PNMwcqJN.vjo2HmQPhuq2WguLvNeyXjasMV90ygAj0r0s7tVs2', 1, 16, '', 0, 0, 0, '', 1, '<ul><li><a href=http://www.npds.org target=_blank>NPDS.ORG</a></li></ul>', 'npds-boost_sk+default', 4096, 5, 0, 1, 1, 'fr', '1712937999', 1),
(3, 'user2', 'user2', 'user@user.land', '', 'http://www.userland.com', '014.gif', '989445600', '', '', '', 'User of the Land', 0, '0', '', '$2y$11$Pt1PNMwcqJN.vjo2HmQPhuq2WguLvNeyXjasMV90ygAj0r0s7tVs2', 1, 8, '', 0, 0, 0, '', 1, '<ul><li><a href=http://www.npds.org target=_blank>NPDS.ORG</a></li></ul>', 'npds-boost_sk+default', 4096, 5, 0, 1, 1, 'fr', '1712099730', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users_extend`
--

DROP TABLE IF EXISTS `users_extend`;
CREATE TABLE IF NOT EXISTS `users_extend` (
  `uid` int NOT NULL AUTO_INCREMENT,
  `C1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C6` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C7` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C8` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `M1` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `M2` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `T1` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `T2` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `B1` blob,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users_extend`
--

INSERT INTO `users_extend` (`uid`, `C1`, `C2`, `C3`, `C4`, `C5`, `C6`, `C7`, `C8`, `M1`, `M2`, `T1`, `T2`, `B1`) VALUES
(2, '', '', '', '', '', '', '45.728712', '4.818514', '', '', '15/07/2015', '', ''),
(3, '', '', '', '', '', '', '45.728712', '4.818514', '', '', '15/07/2015', '', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users_status`
--

DROP TABLE IF EXISTS `users_status`;
CREATE TABLE IF NOT EXISTS `users_status` (
  `uid` int NOT NULL AUTO_INCREMENT,
  `posts` int DEFAULT '0',
  `attachsig` int DEFAULT '0',
  `rang` int DEFAULT '0',
  `level` int DEFAULT '1',
  `open` tinyint(1) NOT NULL DEFAULT '1',
  `groupe` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users_status`
--

INSERT INTO `users_status` (`uid`, `posts`, `attachsig`, `rang`, `level`, `open`, `groupe`) VALUES
(1, 27, 0, 0, 1, 1, ''),
(2, 9, 0, 0, 2, 1, '2'),
(3, 4, 0, 0, 2, 0, '2');

-- --------------------------------------------------------

--
-- Structure de la table `wspad`
--

DROP TABLE IF EXISTS `wspad`;
CREATE TABLE IF NOT EXISTS `wspad` (
  `ws_id` int NOT NULL AUTO_INCREMENT,
  `page` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `modtime` int NOT NULL,
  `editedby` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ranq` smallint NOT NULL DEFAULT '1',
  `member` int NOT NULL DEFAULT '1',
  `verrou` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ws_id`),
  KEY `page` (`page`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
