<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

//------------
// SYNTAXE 1 :
//------------
// $PAGES['index.php']['title']="TITRE de la page";
//   => Votre_titre+ : rajoute le titre de la page devant le titre du site
//   => Votre_titre- : ne rajoute pas le titre du site
//   => "" ou pas +- : n'affiche que le titre du site
// TITRE ALTERNATIF :
//   => Il est possible de mettre un titre de cette forme :
//      $PAGES['index.php']['title']="Index du site+|$title-";
//      Dans ce cas SI $title n'est pas vide ALORS "$title-" sera utilisé SINON se sera "Index du site+"
//      le | représente donc un OU (OR)
// TITRE MUTLI-LANGUE :
//   Les titres supportent le Multi-langue comme par exemple :
//   $PAGES['index.php']['title']="[fr]Index[/fr][en]Home[/en]+";

// $PAGES['index.php']['blocs']="valeur d'affichage des blocs";
//   => -1 : pas de blocs de Gauche ET pas de blocs de Droite
//   =>  0 : blocs de Gauche ET pas de blocs Droite
//   =>  1 : blocs de Gauche ET blocs de Droite
//   =>  2 : pas de blocs Gauche ET blocs de Droite
//   --> Nouveau --- Ajout Canasson --- Nouveau --- Ajout Canasson --- Nouveau <--
//   => 3 : Colonne gauche (Blocs) + Colonne Droite (Blocs) + Central
//   => 4 : Central + Colonne Gauche(Blocs) + Colonne Droite (Blocs)
//      Si Aucune Variable de renseigné : Affichage par défaut = 0
//   ATTENTION cette Variable se Renseigne Maintenant sur cette page et non plus dans votre thème !

// $PAGES['index.php']['run']="yes or no or script";
//   => "" ou "yes" : le script aura l'autorisation de s'executer
//   => "no"        : le script sera redirigé sur index.php
//   $PAGES['index.php']['run']="no" affichera un message : "Site Web fermé"
//   => "script like xxxx.php : autorise le re-routage vers un autre script / exemple : user.php reroute vers user2.php
//
// Pour les modules il existe deux forme d'écriture :
// la syntaxe : $PAGES['modules.php?ModPath=links&ModStart=links']['title']=... qui permet d'affecter un titre, le run et le type de bloc pour chaque 'sous-url' du module
// la syntaxe : $PAGES['mdoules.php?ModPath=links&ModStart=links*']['title']=... (rajout d'une * à la fin) qui permet de faire la même chose mais en indiquant que TOUTES les pages du module seront traitÈes de la même manière

// TinyMCE
// $PAGES['index.php']['TinyMce']=1 or 0;
//   => Permet d'indiquer que TinyMCE doit être initialisé pour ce script
// $PAGES['index.php']['TinyMce-theme']="full or short";
//   => Permet d'indiquer le theme qui sera utilisé
//
// => Si ces deux lignes ne sont pas présentes : TinyMce ne sera pas initialisé
//
// $PAGES['index.php']['TinyMceRelurl']="true or false";
//   => Permet d'indiquer si TinyMce utilise - "fabrique" un chemins relatif (par défaut) ou un chemin absolu (par exemple pour le script LNL de l'admin)

// CSS
// $PAGES['index.php']['css']="css-specifique.css+-"; OU $PAGES['index.php']['css']=array("css-specifique.css+-","http://www.exemple.com/css/.min.css+-","... ...");
//   => Permet de charger une ou plusieurs css spécifiques (aussi bien local que distant) en complément ou en remplacement de la CSS du theme de NPDS
//
//   si "css-specifique.css+" => La CSS sera rajouter en PLUS de la CSS de base
//   si "css-specifique.css-" => La CSS specifique sera LA SEULE chargée (dans le cas d'un tableau - les options sont cumulatives)
//   => La CSS LOCALE DOIT IMPERATIVEMENT se trouver dans le repertoire style de votre theme (theme/votre_theme/style) OU LE CHEMIN doit-être explicite depuis la racine du site("themes/.../style/specif.css")
//   => La CSS DISTANTE DOIT IMPERATIVEMENT se charger via http:// et l'URL ne doit pas contenir d'erreur

// JS
// $PAGES['index.php']['js']="javascript"; OU $PAGES['index.php']['js']=array("javascript","http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js","... ...");
//   => Permet de charger un ou plusieurs javascript spÈcifiques (aussi bien local que distant)
//
//   => Le JS LOCAL DOIT IMPERATIVEMENT se trouver dans le répertoire js de votre thème (theme/votre_theme/js) OU LE CHEMIN doit-être explicite depuis la racine du site("lib/yui/build/...")
//   => Le JS DISTANT DOIT IMPERATIVEMENT se charger via http:// et l'URL ne doit pas contenir d'erreur

/// --- SEO ---///

// SITEMAP
// $PAGES['index.php']['sitemap']="priorite";
//   => Priorité = 0.1 à 1 
//   => Permet de configurer le sitemap.xml généré par le fichier sitemap.php
//   => Pour article.php, forum.php, sections.php et download.php - sitemap.php génère un ensemble de paragraphes correspondant à l'intégralité des données disponibles.

// META-DESCRIPTION
// $PAGES['index.php']['meta-description']="votre phrase de description";
//   => Permet de remplacer le contenu du meta-tags 'description'

// META-KEYWORDS
// $PAGES['index.php']['meta-keywords']="vos mots clefs";
//   => Permet de remplacer le contenu du meta-tags 'keywords'

//------------
// SYNTAXE 2 :
//------------
// L'objectif est de permettre de filtrer l'usage d'un script, d'un module pour les user, les admin ou en fonction de la valeur d'une variable en s'appuyant sur un composant de l'URI
//$PAGES['forum=1']['title']="script vers lequel je serais dirigé si je ne vérifie pas le paramètre run";
//$PAGES['forum=1']['run']="variable X"; (user, admin ou le nom de votre variable)
//
// Par exemple : le forum 1 doit être réservé aux membres
//     $PAGES['forum=1']['title']="forum.php";
//     $PAGES['forum=1']['run']="user";

// Attention cette faculté n'est pas aussi parfaite que l'intégration de la gestion des droits de NPDS mais rend bien des services
// ---------------

// DEFINITION et CAST VARIABLES
settype($title, 'string');
settype($post, 'string');
settype($nuke_url, 'string');
settype($api_key, 'string');
settype($ModPath, 'string');
settype($title, 'string');
// ----------------------------

global $PAGES;

$PAGES['index.php']['title'] = "[fr]Index[/fr][en]Home[/en][es]Index[/es][de]Index[/de][zh]&#x7D22;&#x5F15;[/zh]+";
$PAGES['index.php']['blocs'] = "0";
$PAGES['index.php']['run'] = "yes";
$PAGES['index.php']['sitemap'] = "0.8";
//$PAGES['index.php']['meta-description']="meta a la con avec c'est condition ternere de merde";

$PAGES['user.php']['title'] = "[fr]Section membre pour personnaliser le site[/fr][en]Your personal page to customize the site [/en][es]Secci&oacute;n para personalizar el sitio[/es][de]Mitglied Abschnitt auf der Website anpassen[/de][zh]&#x4E2A;&#x4EBA;&#x8BBE;&#x7F6E;&#x9875;&#x9762;, &#x5141;&#x8BB8;&#x4F7F;&#x7528;&#x6237;&#x7684;&#x7AD9;&#x70B9;&#x5B9E;&#x73B0;&#x4E2A;&#x4EBA;&#x5316;[/zh]+";
$PAGES['user.php']['blocs'] = "0";
$PAGES['user.php']['run'] = "yes";
$PAGES['user.php']['TinyMce'] = 1;
$PAGES['user.php']['TinyMce-theme'] = "short";
$PAGES['user.php']['css'] = array($nuke_url . "assets/shared/ol/ol.css+");


$PAGES['memberslist.php']['title'] = "[fr]Liste des membres[/fr][en]Members list[/en][es]Lista de Miembros[/es][de]Mitglieder[/de][zh]&#x4F1A;&#x5458;&#x5217;&#x8868;[/zh]+";
$PAGES['memberslist.php']['blocs'] = "0";
$PAGES['memberslist.php']['run'] = "yes";

$PAGES['searchbb.php']['title'] = "[fr]Recherche dans les forums[/fr][en]Search in the forums[/en][es]B&uacute;squeda en los foros[/es][de]Die Foren durchsuchen[/de][zh]&#x5728;&#x8BBA;&#x575B;&#x4E2D;&#x67E5;&#x627E;[/zh]+";
$PAGES['searchbb.php']['blocs'] = "0";
$PAGES['searchbb.php']['run'] = "yes";

$PAGES['article.php']['title'] = "$title+";
$PAGES['article.php']['blocs'] = "0";
$PAGES['article.php']['run'] = "yes";
$PAGES['article.php']['sitemap'] = "1";

$PAGES['submit.php']['title'] = "[fr]Soumettre un nouvel article[/fr][en]Submit a new[/en][es]Someter una noticia[/es][de]Einen neuen Artikel[/de][zh]&#x63D0;&#x4EA4;&#x4E00;&#x7BC7;&#x65B0;&#x6587;&#x7AE0;[/zh]+";
$PAGES['submit.php']['blocs'] = "0";
$PAGES['submit.php']['run'] = "yes";
$PAGES['submit.php']['TinyMce'] = 1;
$PAGES['submit.php']['TinyMce-theme'] = "full";

$PAGES['sections.php']['title'] = "[fr]Les articles de fond[/fr][en]Articles in special sections[/en][es]Art&iacute;culos especiales[/es][de]Fachartikel[/de][zh]&#x4E3B;&#x9898;&#x6027;&#x6587;&#x7AE0;[/zh]+|$title+";
$PAGES['sections.php']['blocs'] = "1";
$PAGES['sections.php']['run'] = "yes";
$PAGES['sections.php']['sitemap'] = "0.8";

$PAGES['faq.php']['title'] = "[fr]FAQs / Questions Fr&eacute;quentes[/fr][en]FAQs (Frequently Asked Question)[/en][es]Preguntas frecuentes[/es][de]FAQs[/de][zh]&#x5E38;&#x89C1;&#x95EE;&#x9898; (FAQ)[/zh]+|$title+";
$PAGES['faq.php']['blocs'] = "0";
$PAGES['faq.php']['run'] = "yes";

$PAGES['download.php']['title'] = "[fr]Les t&eacute;l&eacute;chargements[/fr][en]Downloads[/en][es]Descargas[/es][de]Downloads[/de][zh]&#x4E0B;&#x8F7D;[/zh]+|$title+";
$PAGES['download.php']['run'] = "yes";
$PAGES['download.php']['sitemap'] = "0.8";

$PAGES['topics.php']['title'] = "[fr]Les sujets actifs[/fr][en]The actives topics[/en][es]Temas activos[/es][de]Aktive Themen[/de][zh]&#x5F53;&#x524D;&#x6D3B;&#x8DC3;&#x7684;&#x4E3B;&#x9898;[/zh]+";
$PAGES['topics.php']['blocs'] = "1";
$PAGES['topics.php']['run'] = "yes";

$PAGES['search.php']['title'] = "[fr]Rechercher dans les sujets[/fr][en]Search in the topics[/en][es]Buscar en este Temas[/es][de]Suche in diesem Themen[/de][zh]&#x5728;&#x4E3B;&#x9898;&#x4E2D;&#x67E5;&#x627E;[/zh]+";
$PAGES['search.php']['blocs'] = "1";
$PAGES['search.php']['run'] = "yes";

$PAGES['friend.php']['title'] = "[fr]Envoyer un Article / Pr&eacute;venir un ami[/fr][en]Send Story to a Friend[/en][es]Enviar el art&iacute;culo[/es][de]Artikel versenden[/de][zh]&#x53D1;&#x9001;&#x4E00;&#x7BC7;&#x6587;&#x7AE0; / &#x901A;&#x77E5;&#x53CB;&#x4EBA;[/zh]+|$title+";
$PAGES['friend.php']['blocs'] = "1";
$PAGES['friend.php']['run'] = "yes";

$PAGES['top.php']['title'] = "[fr]Le top du site[/fr][en]Top[/en][es]Top[/es][de]Top[/de][zh]&#x4F18;&#x79C0;&#x7AD9;&#x70B9;[/zh]+";
$PAGES['top.php']['blocs'] = "0";
$PAGES['top.php']['run'] = "yes";
$PAGES['top.php']['sitemap'] = "0.5";

$PAGES['stats.php']['title'] = "[fr]Statistiques du site[/fr][en]Web site statistics[/en][es]Estad&iacute;sticas del sitio[/es][de]Site-Statistik[/de][zh]&#x884C;&#x7EDF;&#x8BA1;[/zh]+";
$PAGES['stats.php']['blocs'] = "1";
$PAGES['stats.php']['run'] = "yes";
$PAGES['stats.php']['sitemap'] = "0.5";

// admin why charge the tiny on each admin page 360k !! ??
$PAGES['admin.php']['title'] = ""; // obligatoirement à vide
$PAGES['admin.php']['blocs'] = "0";
$PAGES['admin.php']['run'] = "yes";
$PAGES['admin.php']['TinyMce'] = 1;
$PAGES['admin.php']['TinyMce-theme'] = "full";
$PAGES['admin.php']['css'] = array("admin.css+", $nuke_url . "assets/shared/ol/ol.css+");
$PAGES['admin.php']['TinyMceRelurl'] = "false";

$PAGES['forum.php']['title'] = "[fr]Les forums de discussion[/fr][en]Forums[/en][es]Foros de discusi&oacute;n[/es][de]Diskussionsforen[/de][zh]&#x7248;&#x9762;&#x7BA1;&#x7406;[/zh]+";
$PAGES['forum.php']['run'] = "yes";
$PAGES['forum.php']['sitemap'] = "0.9";
$PAGES['forum.php']['meta-keywords'] = "forum,forums,discussion,discussions,aide,entraide,échange,échanges";
$PAGES['forum.php']['blocs'] = "0";

$PAGES['viewforum.php']['title'] = "[fr]Forum[/fr][en]Forum[/en][es]Foro[/es][de]Forum[/de][zh]&#x7248;&#x9762;&#x7BA1;&#x7406;[/zh] : $title+";
$PAGES['viewforum.php']['run'] = "yes";

$PAGES['viewtopic.php']['title'] = "[fr]Forum[/fr][en]Forum[/en][es]Foro[/es][de]Forum[/de][zh]&#x7248;&#x9762;&#x7BA1;&#x7406;[/zh] : $title / $post+";
$PAGES['viewtopic.php']['run'] = "yes";

$PAGES['viewtopicH.php']['title'] = "[fr]Forum[/fr][en]Forum[/en][es]Foro[/es][de]Forum[/de][zh]&#x7248;&#x9762;&#x7BA1;&#x7406;[/zh] : $title / $post+";
$PAGES['viewtopicH.php']['run'] = "yes";

$PAGES['reply.php']['title'] = "[fr]R&eacute;pondre &#xE0; un post sur le forum[/fr][en]Forum : reply to a post[/en][es]Responder a un mensaje en el foro[/es][de]Antwort auf einen Beitrag im Forum[/de][zh]&#x56DE;&#x590D;&#x8BBA;&#x575B;&#x4E2D;&#x7684;&#x4E00;&#x4E2A;&#x5E16;&#x5B50;[/zh]+";
$PAGES['reply.php']['run'] = "yes";

$PAGES['replyH.php']['title'] = "[fr]R&eacute;pondre &#xE0; un post sur le forum[/fr][en]Forum : reply to a post[/en][es]Responder a un mensaje en el foro[/es][de]Antwort auf einen Beitrag im Forum[/de][zh]&#x56DE;&#x590D;&#x8BBA;&#x575B;&#x4E2D;&#x7684;&#x4E00;&#x4E2A;&#x5E16;&#x5B50;[/zh]+";
$PAGES['replyH.php']['run'] = "yes";

$PAGES['newtopic.php']['title'] = "[fr]Poster un nouveau sujet[/fr][en]Post a new topic[/en][es]Publicar nuevo tema[/es][de]Neues Thema erˆffnen[/de][zh]&#x5F20;&#x8D34;&#x4E00;&#x4E2A;&#x65B0;&#x4E3B;&#x9898;[/zh]+";
$PAGES['newtopic.php']['run'] = "yes";

$PAGES['topicadmin.php']['title'] = "[fr]Gestion des forums[/fr][en]Forum admin[/en][es]Gesti&oacute;n de los foros[/es][de]Management-Foren[/de][zh]&#x5BF9;&#x8BBA;&#x575B;&#x7684;&#x7BA1;&#x7406;[/zh]+";
$PAGES['topicadmin.php']['run'] = "yes";

$PAGES['editpost.php']['title'] = "";
$PAGES['editpost.php']['run'] = "yes";

$PAGES['reviews.php']['title'] = "[fr]Les critiques[/fr][en]Reviews[/en][es]los cr&iacute;ticos[/es][de]Kritik[/de][zh]&#x8BC4;&#x8BBA;[/zh]+";
$PAGES['reviews.php']['blocs'] = "1";
$PAGES['reviews.php']['run'] = "yes";

$PAGES['abla.php']['title'] = "[fr]Admin Blackboard[/fr][en]Admin Blackboard[/en][es]Admin Blackboard[/es][de]Admin Blackboard[/de][zh]Admin Blackboard[/zh]+";
$PAGES['abla.php']['run'] = "yes";
$PAGES['abla.php']['blocs'] = "0";

$PAGES['replypmsg.php']['title'] = "[fr]R&eacute;pondre &agrave; un MP[/fr][en]Reply to a MP[/en][es]Responder a un MP[/es][de]Antwort auf eine MP[/de][zh]Reply to a MP[/zh]+";
$PAGES['replypmsg.php']['run'] = "yes";
$PAGES['replypmsg.php']['blocs'] = "1";

$PAGES['readpmsg.php']['title'] = "[fr]Lire un MP[/fr][en]Read a MP[/en][es]Leer un MP[/es][de]Lesen Sie einen MP[/de][zh]Read a MP[/zh]+";
$PAGES['readpmsg.php']['run'] = "yes";
$PAGES['readpmsg.php']['blocs'] = "1";

$PAGES['map.php']['title'] = "[fr]Plan du Site[/fr][en]SiteMap[/en][es]Mapa del Sitio[/es][de]Site Map[/de][zh]SiteMap[/zh]";
$PAGES['map.php']['blocs'] = "0";
$PAGES['map.php']['run'] = "yes";

$PAGES['pollBooth.php']['title'] = "[fr]Les Sondages[/fr][en]Opinion poll[/en][es]las encuestas[/es][de]die Umfragen[/de][zh]Opinion poll[/zh]";
$PAGES['pollBooth.php']['blocs'] = "2";
$PAGES['pollBooth.php']['run'] = "yes";

// Page static
$PAGES['static.php?op=statik.txt']['title'] = "[fr]Page de d&eacute;monstration[/fr][en]Demo page[/en][es]Demostraci&oacute;n p&aacute;gina[/es][de]Demo-Seite[/de][zh]Demo page[/zh]+";
$PAGES['static.php?op=statik.txt']['blocs'] = "-1";
$PAGES['static.php?op=statik.txt']['run'] = "yes";

// Modules
// Pour les modules il existe deux forme d'écriture :
// la syntaxe : modules.php?ModPath=links&ModStart=links ==> qui permet d'affecter un titre, un run et un type de bloc pour chaque 'sous-url' du module
// la syntaxe : mdoules.php?ModPath=links&ModStart=links* (rajout d'une * à la fin) ==> qui permet de faire la même chose mais en indiquant que TOUTES les pages du module seront traitées de la même manière
$PAGES['modules.php?ModPath=links&ModStart=links*']['title'] = "[fr]Liens et annuaires[/fr][en]Web Links[/en][es]Enlaces y Directorios[/es][de]Links und Verzeichnisse[/de][zh]&#x7F51;&#x7AD9;&#x94FE;&#x63A5;[/zh]+|$title+";
$PAGES['modules.php?ModPath=links&ModStart=links*']['run'] = "yes";
$PAGES['modules.php?ModPath=links&ModStart=links*']['blocs'] = "2";
$PAGES['modules.php?ModPath=links&ModStart=links*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=links&ModStart=links*']['TinyMce-theme'] = "short";

$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['title'] = "[fr]Administration des liens et annuaires[/fr][en]Web Links[/en][es]Gesti&oacute;n de enlaces y directorios[/es][de]Verwaltung Links und Verzeichnisse[/de][zh]&#x7F51;&#x7AD9;&#x94FE;&#x63A5;[/zh]+|$title+";
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['run'] = "yes";
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['blocs'] = "2";
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=links/admin&ModStart=links*']['TinyMce-theme'] = "full";

$PAGES['modules.php?ModPath=f-manager&ModStart=f-manager*']['title'] = "[fr]Gestionnaire de fichiers[/fr][en]Files manager[/en][es]Administrador de Ficheros[/es][de]Datei-Manager[/de][zh]Files manager[/zh]";
$PAGES['modules.php?ModPath=f-manager&ModStart=f-manager*']['run'] = "yes";
$PAGES['modules.php?ModPath=f-manager&ModStart=f-manager*']['blocs'] = "0";
$PAGES['modules.php?ModPath=f-manager&ModStart=f-manager*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=f-manager&ModStart=f-manager*']['TinyMce-theme'] = "short";

$PAGES['modules.php?ModPath=comments&ModStart=reply*']['title'] = "[fr]Commentaires[/fr][en]Comments[/en][es]Comentarios[/es][de]Kommentare[/de][zh]Comments[/zh]";
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['run'] = "yes";
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['blocs'] = "2";
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['TinyMce'] = 0;
$PAGES['modules.php?ModPath=comments&ModStart=reply*']['TinyMce-theme'] = "short";

$PAGES['modules.php?ModPath=contact&ModStart=contact']['title'] = "[fr]Nous Contacter[/fr][en]Contact us[/en][es]Contacte con nosotros[/es][de]Kontakt[/de][zh]Contact us[/zh]-";
$PAGES['modules.php?ModPath=contact&ModStart=contact']['run'] = "yes";
$PAGES['modules.php?ModPath=contact&ModStart=contact']['blocs'] = "0";

$PAGES['modules.php?ModPath=archive-stories&ModStart=archive-stories*']['title'] = "[fr]Les Nouvelles[/fr][en]News[/en][es]Noticias[/es][de]Nachrichten[/de][zh]News[/zh]+";
$PAGES['modules.php?ModPath=archive-stories&ModStart=archive-stories*']['run'] = "yes";
$PAGES['modules.php?ModPath=archive-stories&ModStart=archive-stories*']['blocs'] = "0";

$PAGES['modules.php?ModPath=f-manager&ModStart=pic-manager*']['title'] = "[fr]Afficheur de fichiers multim&eacute;dia[/fr][en]Multimedia files viewer[/en][es]Visualizaci&oacute;n de Ficheros multimedia[/es][de]Anzeige von Multimedia-Dateien[/de][zh]Multimedia files viewer[/zh]";
$PAGES['modules.php?ModPath=f-manager&ModStart=pic-manager*']['run'] = "yes";
$PAGES['modules.php?ModPath=f-manager&ModStart=pic-manager*']['blocs'] = "0";

// CSS sur fichiers particuliers car n'utilisant pas header.php
global $nuke_url;
$PAGES['chatrafraich.php']['css'] = array("chat.css+");
$PAGES['chatinput.php']['css'] = array("chat.css+");

$PAGES['modules.php?ModPath=reseaux-sociaux&ModStart=reseaux-sociaux*']['title'] = "[fr]R&eacute;seaux Sociaux[/fr][en]Social Networks[/en]";
$PAGES['modules.php?ModPath=reseaux-sociaux&ModStart=reseaux-sociaux*']['run'] = "yes";
$PAGES['modules.php?ModPath=reseaux-sociaux&ModStart=reseaux-sociaux*']['blocs'] = "0";

// Filtre sur l'URI
// $PAGES['forum=1']['title']="forum.php";
// $PAGES['forum=1']['run']="user";

$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['title'] = "[fr]Galerie d'images[/fr][en]Pictures galery[/en][es]Galeria de imagenes[/es]+";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['js'] = array($nuke_url . '/modules/npds_galerie/js/jquery.watermark.min.js');
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['run'] = "yes";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['blocs'] = "0";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['TinyMce-theme'] = "short";
$PAGES['modules.php?ModPath=npds_galerie&ModStart=gal*']['css'] = array($nuke_url . '/modules/npds_galerie/css/galerie.css+');

global $language;

$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['title'] = "[fr]Localisation[/fr][en]Geolocation[/en][es]Geolocalizaci&oacute;n[/es][de]Geolocation[/de][zh]&#22320;&#29702;&#20301;&#32622;[/zh]+|$title+";
$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['run'] = "yes";
$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['blocs'] = "-1";
$PAGES['modules.php?ModPath=geoloc&ModStart=geoloc*']['css'] = array($nuke_url . '/assets/shared/ol/ol.css+', $nuke_url . '/modules/geoloc/assets/css/geoloc_style.css+');
