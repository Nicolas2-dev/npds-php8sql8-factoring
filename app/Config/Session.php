<?php
/**
 * Two - Session
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(
    'driver' => 'file',  // Le pilote de session utilisé pour stocker les données de session ; pris en charge : 'fichier', 'base de données' ou 'cookie'.

    // La configuration du pilote de session de base de données.
    'table'      => 'sessions', // La table de base de données hébergeant les données de session.
    'connection' => null,       // Le nom de connexion à la base de données utilisé par le pilote.

    // Durée de vie de la session.
    'lifetime'      => 180,     // Nombre de minutes pendant lesquelles la session est autorisée à rester inactive avant son expiration.
    'expireOnClose' => false,   // Si vous voulez qu'ils expirent immédiatement à la fermeture du navigateur, définissez-le.

    // La configuration du pilote de session de fichiers.
    'files'    => STORAGE_PATH .'framework' .DS .'sessions', // File Session Handler - où les fichiers de session peuvent être stockés.

    'lottery' => array(2, 100), // Option utilisée par le Garbage Collector, pour supprimer les fichiers de session bloqués.

    // Configuration des cookies.
    'cookie'  => PREFIX .'session',
    'path'    => '/',
    'domain'  => null,
    'secure'  => false,
);
