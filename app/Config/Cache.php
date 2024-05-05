<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Cache Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "file", "database", "array"
    */

    'driver' => 'file',

    /*
    |--------------------------------------------------------------------------
    | File Cache Location
    |--------------------------------------------------------------------------
    */

    'path' => STORAGE_PATH . 'cache',

    /*
    |--------------------------------------------------------------------------
    | Database Cache Connection
    |--------------------------------------------------------------------------
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Database Cache Table
    |--------------------------------------------------------------------------
    */

    'table' => 'cache',

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    */

    'prefix' => 'npds_',

    /**
    * 
    *
    */
    'config' => array(

        /**
         * 
         */
        'SuperCache' => true,

        /**
         * 
         */
        'data_dir' =>  'storage/cache/',

        /**
         * How the Auto_Cleanup process is run : 0 no cleanup - 1 auto_cleanup
         */
        'run_cleanup' =>  1,

        /**
         * value between 1 and 100. The most important is the value, the most "probabilidad", cleanup process as chance to be runed
         */
        'cleanup_freq' =>  20,

        /**
         * maximum age - 24 Hours
         */
        'max_age'=>  86400,

        /**
         * Instant Stats : 0 no - 1 Yes
         */
        'save_stats'=>  0,

        /**
         * Terminate send http process after sending cache page : 0 no - 1 Yes
         */
        'exit'=>  0,

        /**
         * If the maximum number of "webuser" is ritched : SuperCache not clean the cache
         * compare with the value store in storage/cache/site_load.log updated by the site_load() function of mainfile.php
         */
        'clean_limit'=>  300,

        /**
         * Same standard cache (not the functions for members) for anonymous and members : 0 no - 1 Yes
         */
        'non_differentiate'=>  0,
        
    ),

    'timings' => array(

        'index' => array(
            'timings'   => 300,
            'query'     => '^',
        ),

        'article' => array(
            'timings'   => 300,
            'query'     => '^',
        ),

        'sections' => array(
            'timings'   => 300,
            'query'     => '^op',
        ),

        'faq' => array(
            'timings'   => 86400,
            'query'     => '^myfaq',
        ),

        'links' => array(
            'timings'   => 28800,
            'query'     => '^',
        ),

        'forum' => array(
            'timings'   => 3600,
            'query'     => '^',
        ),

        'memberslist' => array(
            'timings'   => 1800,
            'query'     => '^',
        ),

        'modules' => array(
            'timings'   => 3600,
            'query'     => '^',
        ),
    )

);
