<?php
/**
 * Two - Rgpd Citron Configuration
 *
 * @author  Nicolas Devoy
 * @email   nicolas@Two-framework.fr 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(

    /** 
     * Privacy policy url.
     */
    'privacyUrl' => '',

    /** 
     * Ouverture automatique du panel avec le hashtag.
     */    
    'hashtag' => '#tarteaucitron',

    /** 
     * Cookie name.
     */ 
    'cookieName' => 'tarteaucitron',

    /** 
     * le bandeau doit être en haut (top) ou en bas (bottom) ?.
     */ 
    'orientation' => 'top',

    /** 
     * afficher le petit bandeau en bas à droite ?.
     */ 
    'showAlertSmall' => 'true',

    /** 
     * Afficher la liste des cookies installés ?.
     */ 
    'cookieslist' => 'true',

    /** 
     * Show cookie icon to manage cookies.
     */ 
    'showIcon' => 'false',

    /** 
     * BottomRight, BottomLeft, TopRight and TopLeft.
     */ 
    'iconPosition' => 'BottomRight',

    /** 
     * Afficher un message si un adblocker est détecté.
     */ 
    'adblocker' => 'false',
    
    /** 
     * Show the accept all button when highPrivacy on.
     */ 
    'AcceptAllCta' => 'true',

    /** 
     * désactiver le consentement implicite (en naviguant) ?.
     */ 
    'highPrivacy' => 'false',

    /** 
     * If Do Not Track == 1, disallow all.
     */ 
    'handleBrowserDNTRequest' => 'false',

    /** 
     * supprimer le lien vers la source ?.
     */ 
    'removeCredit' => 'true',

    /** 
     * Show more info link.
     */ 
    'moreInfoLink' => 'true',

    /** 
     * If false, the tarteaucitron.css file will be loaded.
     */ 
    'useExternalCss' => 'false',

    /** 
     * Nom de domaine sur lequel sera posé le cookie - pour les multisites / sous-domaines - Facultatif.
     */
    'cookieDomain' => '',

    /** 
     * Change the default readmore link.
     */
    'readmoreLink' => 'static.php?op=politiqueconf.html&npds=1&metalang=1',

    /** 
     * Show a message about mandatory cookies.
     */
    'mandatory' => 'true',

);
