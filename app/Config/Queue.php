<?php
/**
 * Two - Queue
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(

    /*
    |--------------------------------------------------------------- -------------------------
    | Pilote de file d'attente par défaut
    |--------------------------------------------------------------- -------------------------
    |
    | L'API de file d'attente Laravel prend en charge une variété de back-ends via un unifié
    | API, vous donnant un accès pratique à chaque back-end en utilisant le même
    | syntaxe pour chacun. Ici, vous pouvez définir le pilote de file d'attente par défaut.
    |
    | Pris en charge : "sync", "database", "beanstalkd", "sqs", "iron", "redis"
    |
    */

    'default' => 'sync',

    /*
    |--------------------------------------------------------------- -------------------------
    | Connexions à la file d'attente
    |--------------------------------------------------------------- -------------------------
    |
    | Ici, vous pouvez configurer les informations de connexion pour chaque serveur qui
    | est utilisé par votre application. Une configuration par défaut a été ajoutée
    | pour chaque back-end livré avec Laravel. Vous êtes libre d'en ajouter d'autres.
    |
    */

    'connections' => array(
        'sync' => array(
            'driver'  => 'sync',
        ),
        'database' => array(
            'driver'  => 'database',
            'table'   => 'jobs',
            'queue'   => 'default',
            'expire'  => 60,
        ),
        'beanstalkd' => array(
            'driver'  => 'beanstalkd',
            'host'    => 'localhost',
            'queue'   => 'default',
            'ttr'     => 60,
        ),
        'sqs' => array(
            'driver'  => 'sqs',
            'key'     => 'your-public-key',
            'secret'  => 'your-secret-key',
            'queue'   => 'your-queue-url',
            'region'  => 'us-east-1',
        ),
        'iron' => array(
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1.iron.io',
            'token'   => 'your-token',
            'project' => 'your-project-id',
            'queue'   => 'your-queue-name',
            'encrypt' => true,
        ),
        'redis' => array(
            'driver'  => 'redis',
            'queue'   => 'default',
        ),
    ),

    /*
    |--------------------------------------------------------------- -------------------------
    | Échec des tâches de la file d'attente
    |--------------------------------------------------------------- -------------------------
    |
    | Ces options configurent le comportement de la journalisation des travaux de file d'attente ayant 
    | échoué afin que vous
    | peut contrôler quelle base de données et quelle table sont utilisées pour stocker les travaux qui
    | ont échoué. Vous pouvez les changer en n'importe quelle base de données / table que vous souhaitez.
    |
    */

    'failed' => array(
        'database' => 'mysql',
        'table'    => 'failed_jobs',
    ),
);
