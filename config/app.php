<?php

return array(


    // Provisoire le temp de fincr l'organisation des fichiers de configuration

    // Database

    'database' => array(
        
        /**
         * MySQL Database Hostname
         */
        'dbhost' => 'localhost',
        
        /**
         * MySQL Username
         */
        'dbuname' => 'npds',
        
        /**
         * MySQL Password
         */
        'dbpass' => 'password',
        
        /**
         * MySQL Database Name
         */
        'dbname' => 'npds',
        # dbhost:      
    
        /**
         * Persistent connection to MySQL Server (1) or Not (0)
         */
        'mysql_p' => 1,
        
        /**
         * Use MySQLi (1) instead of MySQL interface (0)
         */
        'mysql_i' => 1,        
    ),
    
    /**
     * NPDS_Key
     */

    'NPDS_Key' => 'DU5Soda5BABJnQN6CzryBTiBDXCaSX3q',

    /**
     * The registered Class Aliases.
     */
    'aliases' => array(
 
        /**
         * Facades
         */
        'DB' => 'npds\system\support\facades\DB',

    ),
);