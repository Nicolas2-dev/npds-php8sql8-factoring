<?php

return array(

    /**
    * Debug
    *
    */
    'debug'  => false,

    /**
    * Configurer le serveur SMTP
    *
    */
    'smtp_host'  => 'sandbox.smtp.mailtrap.io',

    /**
    * Port TCP, utilisez 587 si vous avez activé le chiffrement TLS
    *
    */
    'smtp_port'  => '2525',

    /**
    * Activer l'authentification SMTP
    *
    */
    'smtp_auth'  => 1,

    /**
    * Nom d'utilisateur SMTP
    *
    */
    'smtp_username'  => 'c1bf9df387c5df',

    /**
    * Mot de passe SMTP
    *
    */
    'smtp_password'  => '919c5e3c6d6888',

    /**
    * Activer le chiffrement TLS
    *
    */
    'smtp_secure'  => 1,

    /**
    * Type du chiffrement TLS
    *
    */
    'smtp_crypt'  => 'tls',

    /**
    * DKIM 1 pour celui du dns 2 pour une génération automatique
    *
    */
    'dkim_auto'  => 1,

);
