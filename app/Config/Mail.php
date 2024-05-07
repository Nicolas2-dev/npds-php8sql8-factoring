<?php
/**
 * Two - Mail
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(
    /*
    |--------------------------------------------------------------- -------------------------
    | Pilote de messagerie
    |--------------------------------------------------------------- -------------------------
    |
    | Two prend en charge les fonctions "mail" de SMTP et PHP en tant que pilotes pour le
    | envoi de courrier électronique. Vous pouvez spécifier celui que vous utilisez tout au long
    | votre candidature ici. Par défaut, Two est configuré pour le courrier SMTP.
    |
    | Pris en charge : "smtp", "mail", "sendmail", "mailgun", "mandrill", "log"
    |
    */

    'driver' => 'smtp',

    /*
    |--------------------------------------------------------------- -------------------------
    | Adresse de l'hôte SMTP
    |--------------------------------------------------------------- -------------------------
    |
    | Ici, vous pouvez fournir l'adresse hôte du serveur SMTP utilisé par votre
    | applications. Une option par défaut est fournie qui est compatible avec
    | le service de courrier Mailgun qui assurera des livraisons fiables.
    |
    */

    'host' => '',

    /*
    |--------------------------------------------------------------- -------------------------
    | Port hôte SMTP
    |--------------------------------------------------------------- -------------------------
    |
    | Il s'agit du port SMTP utilisé par votre application pour envoyer des e-mails à
    | utilisateurs de l'application. Comme l'hôte, nous avons défini cette valeur sur
    | rester compatible avec l'application de messagerie Mailgun par défaut.
    |
    */

    'port' => 587,

    /*
    |--------------------------------------------------------------- -------------------------
    | Adresse "de" globale
    |--------------------------------------------------------------- -------------------------
    |
    | Vous pouvez souhaiter que tous les e-mails envoyés par votre application soient envoyés de
    | la même adresse. Ici, vous pouvez spécifier un nom et une adresse qui sont
    | utilisé globalement pour tous les e-mails envoyés par votre application.
    |
    */

    'from' => array(
        'address' => 'admin@two.dev',
        'name'    => 'The Two Staff',
    ),

    /*
    |--------------------------------------------------------------- -------------------------
    | Protocole de cryptage des e-mails
    |--------------------------------------------------------------- -------------------------
    |
    | Ici, vous pouvez spécifier le protocole de cryptage qui doit être utilisé lorsque
    | l'application envoie des messages électroniques. Un défaut sensé en utilisant le
    | Le protocole de sécurité de la couche de transport devrait fournir une grande sécurité.
    |
    */

    'encryption' => 'tls',

    /*
    |--------------------------------------------------------------- -------------------------
    | Nom d'utilisateur du serveur SMTP
    |--------------------------------------------------------------- -------------------------
    |
    | Si votre serveur SMTP nécessite un nom d'utilisateur pour l'authentification, vous devez
    | Réglez-le ici. Cela s'habituera à s'authentifier auprès de votre serveur sur
    | connexion. Vous pouvez également définir la valeur "mot de passe" en dessous de celle-ci.
    |
    */

    'username' => '',

    /*
    |--------------------------------------------------------------- -------------------------
    | Mot de passe du serveur SMTP
    |--------------------------------------------------------------- -------------------------
    |
    | Ici, vous pouvez définir le mot de passe requis par votre serveur SMTP pour envoyer
    | messages de votre application. Celui-ci sera remis au serveur le
    | connexion afin que l'application puisse envoyer des messages.
    |
    */

    'password' => '',

    /*
    |--------------------------------------------------------------- -------------------------
    | Chemin système Sendmail
    |--------------------------------------------------------------- -------------------------
    |
    | Lors de l'utilisation du pilote "sendmail" pour envoyer des e-mails, nous aurons besoin de savoir
    | le chemin d'accès à l'emplacement de Sendmail sur ce serveur. Un chemin par défaut a
    | fourni ici, qui fonctionnera bien sur la plupart de vos systèmes.
    |
    */

    'sendmail' => '/usr/sbin/sendmail -bs',

    /*
    |--------------------------------------------------------------- -------------------------
    | Mail "Faire semblant"
    |--------------------------------------------------------------- -------------------------
    |
    | Lorsque cette option est activée, les e-mails ne seront pas réellement envoyés via
    | web et sera plutôt écrit dans les fichiers journaux de votre application afin
    | vous pouvez inspecter le message. C'est très bien pour le développement local.
    |
    */

    'pretend' => true,

    /*
    |--------------------------------------------------------------- -------------------------
    | Configuration du tiroir
    |--------------------------------------------------------------- -------------------------
    |
    | Lors de l'utilisation du spool de l'expéditeur, nous avons besoin d'un emplacement où le message spool
    | les fichiers peuvent être stockés. Une valeur par défaut a été définie pour vous, mais une autre
    | l'emplacement peut être spécifié. Ceci n'est nécessaire que pour le pilote de bobine.
    |
    */

    'spool' => array(
        'driver' => 'file', // // Le pilote utilisé pour la mise en file d'attente ; pris en charge : 'fichier' ou 'base de données'.

        // Common options:
        'messageLimit'    => 10,  // Le nombre maximum de messages à envoyer par vidage.
        'timeLimit'       => 100, // La limite de temps (en secondes) par flush
        'retryLimit'      => 10,  // Permet de gérer la limite de tentatives de mise en file d'attente.
        'recoveryTimeout' => 900, // en secondes - les valeurs par défaut sont pour les réponses smtp très lentes.

        // Pour le spouleur de fichiers :
        'files' => STORAGE_PATH .'emails',  // Où les fichiers de file d'attente spool sont stockés.

        // Pour le spool de base de données :
        'table'      => 'spool',  // La table de base de données hébergeant les données Mailer Spool.
        'connection' => null,     // Le nom de connexion à la base de données utilisé par le pilote.
    ),
);
