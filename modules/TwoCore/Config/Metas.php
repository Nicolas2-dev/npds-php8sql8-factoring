<?php
/**
 * Two - Metatag
 *
 * Oder meta aucun impact sur les moteur de recherche les meta non aucune prioprité. 
 * Cett option est juste mis en place pour le visuel du code source
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(

    // charset
    'charset' => array(
        array(
            'type' => 'charset',
            'name' => 'charset',
            'content' => 'utf-8',
            'order' => 1,
        ),
    ),

    // metatags
    'metatag' => array(
        array(
            'type' => 'name',
            'name' => 'viewport',
            'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no',
            'order' => 2,
        ),
        array(
            'type' => 'name',
            'name' => 'author',
            'content' => 'Developpeur',
            'order' => 3,
        ),
        array(
            'type' => 'name',
            'name' => 'owner',
            'content' => 'two.nicodev.fr',
            'order' => 4,
        ),
        array(
            'type' => 'name',
            'name' => 'reply-to',
            'content' => 'developpeur@two.nicodev.fr',
            'order' => 5,
        ),
        array(
            'type' => 'name',
            'name' => 'description',
            'content' => 'Générateur de portail Français en Open-Source sous licence Gnu-Gpl utilisant Php et MySql',
            'order' => 6,
        ),
        array(
            'type' => 'name',
            'name' => 'keywords',
            'content' => 'solution,solutions,portail,portails,generateur,générateur,nouveau,Nouveau,Technologie,technologie,npds,NPDS,Npds,nuke,Nuke,PHP-Nuke,phpnuke,php-nuke,nouvelle,Nouvelle,nouvelles,histoire,Histoire,histoires,article,Article,articles,Linux,linux,Windows,windows,logiciel,Logiciel,téléchargement,téléchargements,Téléchargement,Téléchargements,gratuit,Gratuit,Communauté,communauté;,Forum,forum,Forums,forums,Bulletin,bulletin,application,Application,dynamique,Dynamique,PHP,Php,php,sondage,Sondage,Commentaire,commentaire,Commentaires,commentaires,annonce,annonces,petite,Petite,petite annonce,mailling,mail,faq,Faq,faqs,lien,Lien,liens,france,francais,français,France,Francais,Français,libre,Libre,Open,open,Open Source,OpenSource,Opensource,GNU,gnu,GPL,gpl,License,license,Unix,UNIX,unix,MySQL,mysql,SQL,sql,Database,DataBase,database,Red Hat,RedHat,red hat,Web Site,web site,site,sites,web,Web',
            'order' => 7,
        ),
        array(
            'type' => 'name',
            'name' => 'rating',
            'content' => 'general',
            'order' => 8,
        ),
        array(
            'type' => 'name',
            'name' => 'distribution',
            'content' => 'global',
            'order' => 9,
        ),
        array(
            'type' => 'name',
            'name' => 'copyright',
            'content' => 'two cms 2023',
            'order' => 10,
        ),
        array(
            'type' => 'name',
            'name' => 'revisit-after',
            'content' => '15 days',
            'order' => 11,
        ),
        array(
            'type' => 'name',
            'name' => 'resource-type',
            'content' => 'document',
            'order' => 12,
        ),
        array(
            'type' => 'name',
            'name' => 'robots',
            'content' => 'all',
            'order' => 13,
        ),
        array(
            'type' => 'name',
            'name' => 'generator',
            'content' => 'TWO CMS v.1.0 SP8',
            'order' => 14,
        ),
        array(
            'type' => 'equiv',
            'name' => 'X-UA-Compatible',
            'content' => 'IE=edge',
            'order' => 15,
        ),
        array(
            'type' => 'equiv',
            'name' => 'content-script-type',
            'content' => 'text/javascript',
            'order' => 16,
        ),
        array(
            'type' => 'equiv',
            'name' => 'content-style-type',
            'content' => 'text/css',
            'order' => 17,
        ),
        array(
            'type' => 'equiv',
            'name' => 'expires',
            'content' => '0',
            'order' => 18,
        ),
        array(
            'type' => 'equiv',
            'name' => 'pragma',
            'content' => 'no-cache',
            'order' => 19,
        ),
        array(
            'type' => 'equiv',
            'name' => 'cache-control',
            'content' => 'no-cache',
            'order' => 20,
        ),
        array(
            'type' => 'equiv',
            'name' => 'identifier-url',
            'content' => 'http://www.two-npds-fram.local/',
            'order' => 21,
        ),
        array(
            'type' => 'property',
            'name' => 'og:type',
            'content' => 'website',
            'order' => 22,
        ),
        array(
            'type' => 'property',
            'name' => 'og:url',
            'content' => 'http://www.two-npds-fram.local/',
            'order' => 23,
        ),
        array(
            'type' => 'property',
            'name' => 'og:title',
            'content' => 'Two cms',
            'order' => 24,
        ),
        array(
            'type' => 'property',
            'name' => 'og:description',
            'content' => 'Générateur de portail Français en Open-Source sous licence Gnu-Gpl utilisant Php et MySql',
            'order' => 25,
        ),
        array(
            'type' => 'property',
            'name' => 'og:image',
            'content' => 'http://www.two-npds-fram.local/assets/images/ogimg.jpg',
            'order' => 26,
        ),
        array(
            'type' => 'property',
            'name' => 'twitter:card',
            'content' => 'summary',
            'order' => 14.1, // 27 test position
        ),
    )
);
