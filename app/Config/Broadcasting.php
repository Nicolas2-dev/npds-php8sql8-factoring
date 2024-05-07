<?php
/**
 * Two - Broadcasting
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

return array(

    /*
    |--------------------------------------------------------------- -------------------------
    | Diffuseur par défaut
    |--------------------------------------------------------------- -------------------------
    |
    | Cette option contrôle le diffuseur par défaut qui sera utilisé par le
    | lorsqu'un événement doit être diffusé. Vous pouvez le régler sur
    | n'importe laquelle des connexions définies dans le tableau "connexions" ci-dessous.
    |
    */

    'default' => env('BROADCAST_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------- -------------------------
    | Connexions de diffusion
    |--------------------------------------------------------------- -------------------------
    |
    | Ici, vous pouvez définir toutes les connexions de diffusion qui seront utilisées
    | pour diffuser des événements vers d'autres systèmes ou via des websockets. Des échantillons de
    | chaque type de connexion disponible est fourni à l'intérieur de ce tableau.
    |
    */

    'connections' => array(

        'quasar' => array(
            'driver' => 'quasar',

            'key'    => env('QUASAR_KEY'),
            'secret' => env('QUASAR_SECRET'),

            'options' => array(
                'authEndpoint' => site_url('broadcasting/auth'),

                // Le nom d'hôte et le port du serveur HTTP.
                'httpHost'   => env('QUASAR_HTTP_HOST', '127.0.0.1'),
                'httpPort'   => env('QUASAR_HTTP_PORT', 2121),

                // Le nom d'hôte et le port du serveur SocketIO.
                'socketHost' => env('QUASAR_SOCKET_HOST', '127.0.0.1'), // Facultatif, par défaut l'hôte HTTP..
                'socketPort' => env('QUASAR_SOCKET_PORT', 2120),
            ),
        ),

        'pusher' => array(
            'driver'  => 'pusher',

            'key'     => env('PUSHER_KEY'),
            'secret'  => env('PUSHER_SECRET'),
            'app_id'  => env('PUSHER_APP_ID'),

            'options' => array(
                'authEndpoint' => site_url('broadcasting/auth'),
            ),
        ),

        'redis' => array(
            'driver'     => 'redis',
            'connection' => 'default',
        ),

        'log' => array(
            'driver' => 'log',
        ),

        'null' => array(
            'driver' => 'null',
        ),
    ),

);
