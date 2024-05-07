<?php
/**
 * Two - Auth
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(
    /*
    |--------------------------------------------------------------- -------------------------
    | Paramètres d'authentification par défaut
    |--------------------------------------------------------------- -------------------------
    |
    | Cette option contrôle le "gardien" et le mot de passe d'authentification par défaut
    | réinitialiser les options de votre application. Vous pouvez modifier ces valeurs par défaut
    | selon les besoins, mais ils sont un début parfait pour la plupart des applications.
    |
    */

    'defaults' => array(
        'guard'    => 'web',
        'reminder' => 'users',
    ),

    /*
    |--------------------------------------------------------------- -------------------------
    | Gardes d'authentification
    |--------------------------------------------------------------- -------------------------
    |
    | Ensuite, vous pouvez définir chaque garde d'authentification pour votre application.
    | Bien sûr, une super configuration par défaut a été définie pour vous
    | ici qui utilise le stockage de session et le fournisseur d'utilisateurs étendu.
    |
    | Tous les pilotes d'authentification ont un fournisseur d'utilisateurs. Cela définit comment le
    | les utilisateurs sont en fait extraits de votre base de données ou d'un autre stockage
    | mécanismes utilisés par cette application pour conserver les données de votre utilisateur.
    |
    | Pris en charge : "session", "jeton"
    |
    */

    'guards' => array(
        'web' => array(
            'driver'   => 'session',
            'provider' => 'users',

            'paths' => array(
                'authorize' => 'login',
                'dashboard' => 'dashboard',
            ),
        ),
        'api' => array(
            'driver'   => 'token',
            'provider' => 'users',
        ),
    ),

    /*
    |--------------------------------------------------------------- -------------------------
    | Fournisseurs d'utilisateurs
    |--------------------------------------------------------------- -------------------------
    |
    | Tous les pilotes d'authentification ont un fournisseur d'utilisateurs. Cela définit comment le
    | les utilisateurs sont en fait extraits de votre base de données ou d'un autre stockage
    | mécanismes utilisés par cette application pour conserver les données de votre utilisateur.
    |
    | Si vous avez plusieurs tables ou modèles d'utilisateurs, vous pouvez configurer plusieurs
    | sources qui représentent chaque modèle / table. Ces sources peuvent alors
    | être affecté à tous les gardes d'authentification supplémentaires que vous avez définis.
    |
    | Pris en charge : "base de données", "étendue"
    |
    */

    'providers' => array(
        'users' => array(
            'driver' => 'extended',
            'model'  => 'App\Models\User',
        ),
    ),

    /*
    |--------------------------------------------------------------- -------------------------
    | Réinitialisation des mots de passe
    |--------------------------------------------------------------- -------------------------
    |
    | Ici, vous pouvez définir les options de réinitialisation des mots de passe, y compris la vue
    | c'est votre e-mail de réinitialisation de mot de passe. Vous pouvez également définir le nom du
    | table qui conserve tous les jetons de réinitialisation pour votre application.
    |
    | Vous pouvez spécifier plusieurs configurations de réinitialisation de mot de passe si vous avez plus
    | plus d'une table utilisateur ou d'un modèle dans l'application et vous souhaitez avoir
    | paramètres de réinitialisation de mot de passe distincts en fonction des types d'utilisateurs 
    | spécifiques.
    |
    | Le délai d'expiration est le nombre de minutes pendant lesquelles le jeton de réinitialisation doit 
    | être considéré comme valable. Cette fonctionnalité de sécurité maintient les jetons de courte durée 
    | afin ils ont moins de temps pour être devinés. Vous pouvez modifier cela si nécessaire.
    |
    */

    'reminders' => array(
        'users' => array(
            'provider' => 'users',
            'email'    => 'Emails/Auth/Reminder',
            'table'    => 'password_reminders',
            'expire'   => 60,
        ),
    ),
);
