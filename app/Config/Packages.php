<?php
/**
 * Two - Packages
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(

    /*
    |----------------------------------------------------------------------------------------
    | Informations sur l'auteur pour la génération de packages
    |----------------------------------------------------------------------------------------
    |
    */

    'author' => array(
        'name'     => 'John Doe',
        'email'    => 'john.doe@Twol.dev',
        'homepage' => 'http://www.Two.dev',
    ),

    //---------------------------------------------------------------------------------------
    // Chemin vers le fichier cache
    //---------------------------------------------------------------------------------------

    'cache' => STORAGE_PATH .'framework' .DS .'packages.php',

    /*
    |-----------------------------------------------------------------------------------------
    | Configuration des modules
    |-----------------------------------------------------------------------------------------
    |
    |*/

    'modules' => array(

        //-----------------------------------------------------------------------------------
        // Chemin vers les modules
        //-----------------------------------------------------------------------------------
        'path' => BASEPATH .'modules',

        //------------------------------------------------------------- ----------------------
        // Espace de noms de base des modules
        //------------------------------------------------------------- ----------------------

        'namespace' => 'Modules\\',
    ),

    /*
    |--------------------------------------------------------------- -------------------------
    | Configuration des thèmes
    |--------------------------------------------------------------- -------------------------
    |
    */

    'themes' => array(

        //------------------------------------------------------------- ----------------------
        // Chemin vers les thèmes
        //------------------------------------------------------------- ----------------------


        'path' => BASEPATH .'themes',

        //------------------------------------------------------------- ----------------------
        // Espace de noms de base des thèmes
        //------------------------------------------------------------- ----------------------

        'namespace' => 'Themes\\',
    ),

    /*
    |--------------------------------------------------------------- --------------------------
    | Options de chargement pour les packages installés
    |--------------------------------------------------------------- --------------------------
    |
    */

    'options' => array(
        
        // modules
        'two_archive_stories' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_authors' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_banners' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_blocks' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_blocnotes' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_carnet' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_chat' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_cluter_paradise' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_comments' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_contact' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_core' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_download' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_edito' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_ephemerids' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_faqs' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_fmanager' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_forum' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_friend' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_geoloc' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_groupes' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_headlines' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_install' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_ipban' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_links' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_manuels' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_maps' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_marque_pages' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_memberlist' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_messenger' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_minisites' => array(
            'enabled'  => true,
            'order'    => 20,
        ),   
        'two_modules' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_news' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_newsleter' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_pages' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_pollbooth' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_push' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_reseaux_sociaux' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_reviews' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_rssfeed' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_search' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_session_log' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_sitemap' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_stat' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_themes' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_tops' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_twi' => array(
            'enabled'  => true,
            'order'    => 20,
        ),  
        'two_upload' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_users' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_wspad' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 

        // Themes
        'two_backent' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
        'two_frontand' => array(
            'enabled'  => true,
            'order'    => 20,
        ), 
    ),

);
