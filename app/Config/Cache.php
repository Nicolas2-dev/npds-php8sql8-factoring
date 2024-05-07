<?php
/**
 * Two - Cache
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(
    /*
    |--------------------------------------------------------------- -------------------------
    | Pilote de cache par défaut
    |--------------------------------------------------------------- -------------------------
    |
    | Cette option contrôle le "pilote" de cache par défaut qui sera utilisé lorsque
    | à l'aide de la bibliothèque de mise en cache. Bien entendu, vous pouvez utiliser d'autres pilotes
    | le temps que vous souhaitez. C'est la valeur par défaut lorsqu'un autre n'est pas spécifié.
    |
    | Pris en charge : "file", "database", "apc", "memcached", "redis", "tableau"
    */

    'driver' => 'file',

    /*
    |--------------------------------------------------------------- -------------------------
    | Emplacement du cache de fichiers
    |--------------------------------------------------------------- -------------------------
    |
    | Lors de l'utilisation du pilote de cache "fichier", nous avons besoin d'un emplacement où le cache
    | les fichiers peuvent être stockés. Une valeur par défaut sensible a été spécifiée, mais vous
    | sont libres de le changer à n'importe quel autre endroit sur le disque que vous désirez.
    */

    'path' => STORAGE_PATH .'framework' .DS .'cache',

    /*
    |--------------------------------------------------------------- -------------------------
    | Connexion au cache de la base de données
    |--------------------------------------------------------------- -------------------------
    |
    | Lorsque vous utilisez le pilote de cache "base de données", vous pouvez spécifier la connexion
    | qui doit être utilisé pour stocker les éléments mis en cache. Lorsque cette option est
    | null la connexion à la base de données par défaut sera utilisée pour le cache.
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------- -------------------------
    | Table de cache de base de données
    |--------------------------------------------------------------- -------------------------
    |
    | Lors de l'utilisation du pilote de cache "base de données", nous devons connaître la table qui
    | doit être utilisé pour stocker les éléments mis en cache. Un nom de table par défaut a
    | été fourni, mais vous êtes libre de le modifier comme bon vous semble.
    */

    'table' => 'cache',

    /*
    |--------------------------------------------------------------- -------------------------
    | Serveurs Memcaché
    |--------------------------------------------------------------- -------------------------
    |
    | Vous pouvez maintenant spécifier un tableau de vos serveurs Memcached qui doivent être
    | utilisé lors de l'utilisation du pilote de cache Memcached. Tous les serveurs
    | doit contenir une valeur pour les options "host", "port" et "weight".
    */

    'memcached' => array(
        array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
    ),

    /*
    |--------------------------------------------------------------- -------------------------
    | Préfixe de clé de cache
    |--------------------------------------------------------------- -------------------------
    |
    | Lors de l'utilisation d'un magasin basé sur la RAM tel qu'APC ou Memcached, il peut y avoir
    | être d'autres applications utilisant le même cache. Donc, nous allons spécifier un
    | value pour être préfixé à toutes nos clés afin que nous puissions éviter les collisions.
    */

    'prefix' => 'Two',

);
