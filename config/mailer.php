<?php

return array(

    /**
     * Debug
     */
    'debug' => false,
    
    /**
     * Configurer le serveur SMTP
     *
     * @var [type]
     */
    'smtp_host' => "",

    /**
     * Port TCP, utilisez 587 si vous avez activé le chiffrement TLS
     */
    'smtp_port' => "",

    /**
     * Activer l'authentification SMTP
     */
    'smtp_auth' => 0,

    /**
     * Nom d'utilisateur SMTP
     */
    'smtp_username' => "",

    /**
     * Mot de passe SMTP
     */
    'smtp_password' => "",

    /**
     * Activer le chiffrement TLS
     */
    'smtp_secure' => 0,

    /**
     * Type du chiffrement TLS
     */
    'smtp_crypt' => "tls",
 
    /**
     * DKIM 1 pour celui du dns 2 pour une génération automatique
     */
    'dkim_auto' => 1,

);
