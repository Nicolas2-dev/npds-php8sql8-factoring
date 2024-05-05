<?php
declare(strict_types=1);

use Npds\Config\Config;


/**
 * load_Driver
 */
if (! function_exists('load_Driver'))
{
    /**
     * [load_Driver description]
     * @return [type] [description]
     */
    function load_Driver() 
    {
        if (Config::get('app.database.mysql_i') == 1)
        {
            include(APPPATH."Support/deprecated/drivers/mysqli.php");
        }
    }
}

/**
 * Mysql_Connexion
 */
if (! function_exists('Mysql_Connexion'))
{
    /**
     * Connexion plus détaillée 
     * $mysql_p=true => persistente connexion 
     * Attention : le type de SGBD n'a pas de lien avec le nom de cette fontion
     */
    function Mysql_Connexion() 
    {
        // Loading driver
        load_Driver();

        // connecxion base
        $ret_p = sql_connect();
        
        if (!$ret_p) 
        {
            $Titlesitename = 'Npds';
            
            if (file_exists('storage/meta/meta.php'))
            {
                include ('storage/meta/meta.php');
            }

            if (file_exists('storage/static/database.txt'))
            {
                include ('storage/static/database.txt');
            }
            
            die();
        }

        // Init mysql charset
        mysql_Charset($ret_p);

        return $ret_p;
    }
}

/**
 * mysql_Charset
 */
if (! function_exists('mysql_Charset'))
{
    /**
     * [mysql_Charset description]
     * @return [type] [description]
     */
    function mysql_Charset($ret_p) 
    {
        if (Config::get('app.database.mysql_i') == 1)
        {
            mysqli_set_charset($ret_p, 'utf8mb4');
        }
    }
}