<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2001-2023 by Philippe Brunier                     */
/* =========================                                            */
/*                                                                      */
/* Multi DataBase Support - MysqlI                                      */
/* Copyright (c) JIRECK 2013                                            */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/**
 * [$sql_nbREQ description]
 *
 * @var int
 */
$sql_nbREQ = 0;

 
/**
 * Escape string
 */
if (! function_exists('SQL_escape_string'))
{
    /**
     * 
     *
     * @param   string  $arr  [$arr description]
     *
     * @return  string
     */
    function SQL_escape_string(string $arr): string
    {
        global $dblink;

        if (function_exists("mysqli_real_escape_string")) {
            @mysqli_real_escape_string($dblink, $arr);
        }

        return $arr;
    }
}

/**
 * Connexion
 */
if (! function_exists('sql_connect'))
{
    /**
     * [sql_connect description]
     *
     * @return  mysqli|false
     */
    function sql_connect(): mysqli|bool
    {
        global $mysql_p, $dbhost, $dbuname, $dbpass, $dbname, $dblink;

        if (($mysql_p) or (!isset($mysql_p))) {
            $dblink = @mysqli_connect('p:' . $dbhost, $dbuname, $dbpass);
        } else {
            $dblink = @mysqli_connect($dbhost, $dbuname, $dbpass);
        }

        if (!$dblink) {
            return false;
        } else {
            if (!@mysqli_select_db($dblink, $dbname)) {
                return false;
            } else {
                return $dblink;
            }
        }
    }
}

/**
 * Erreur survenue
 */
if (! function_exists('sql_error'))
{
    /**
     * [sql_error description]
     *
     * @return  string
     */
    function sql_error(): string
    {
        global $dblink;

        return mysqli_error($dblink);
    }
}
 
/**
 * Exécution de requête
 */
if (! function_exists('sql_query'))
{
    /**
     * [sql_query description]
     *
     * @param   string  $sql  [$sql description]
     *
     * @return  mysqli_result|bool
     */
    function sql_query(string $sql): mysqli_result|bool
    {
        global $sql_nbREQ, $dblink;

        $sql_nbREQ++;

        if (!$query_id = @mysqli_query($dblink, SQL_escape_string($sql))) {
            return false;
        } else {
            return $query_id;
        }
    }
}
 
/**
 * Tableau Associatif du résultat
 */
if (! function_exists('sql_fetch_assoc'))
{
    /**
     * [sql_fetch_assoc description]
     *
     * @param    $q_id  [$q_id description]
     *
     * @return  array|false|null
     */
    function sql_fetch_assoc($q_id = ''): array|null|false
    {

        if (empty($q_id)) {
            global $query_id;
            $q_id = $query_id;
        }

        return @mysqli_fetch_assoc($q_id);
    }
}

/**
 * Tableau Numérique du résultat
 */
if (! function_exists('sql_fetch_row'))
{
    /**
     * [sql_fetch_row description]
     *
     * @param   $q_id  [$q_id description]
     *
     * @return  array|false|null
     */
    function sql_fetch_row($q_id = ''): array|null|false
    {

        if (empty($q_id)) {
            global $query_id;
            $q_id = $query_id;
        }

        return @mysqli_fetch_row($q_id);
    }
}

/**
 * Tableau du résultat
 */
if (! function_exists('sql_fetch_array'))
{
    /**
     * [sql_fetch_array description]
     *
     * @param   $q_id  [$q_id description]
     *
     * @return  array|false|null
     */
    function sql_fetch_array($q_id = ''): array|false|null
    {

        if (empty($q_id)) {
            global $query_id;
            $q_id = $query_id;
        }

        return @mysqli_fetch_array($q_id);
    }
}

/**
 * Resultat sous forme d'objet
 */
if (! function_exists('sql_fetch_object'))
{
    /**
     * [sql_fetch_object description]
     *
     * @param   $q_id  [$q_id description]
     *
     * @return  object|null|false
     */
    function sql_fetch_object($q_id = ''): object|null|false
    {

        if (empty($q_id)) {
            global $query_id;
            $q_id = $query_id;
        }

        return @mysqli_fetch_object($q_id);
    }
}

/**
 * Nombre de lignes d'un résultat
 */
if (! function_exists('sql_num_rows'))
{
    /**
     * [sql_num_rows description]
     *
     * @param   $q_id  [$q_id description]
     *
     * @return  string|int
     */
    function sql_num_rows($q_id = ''): string|int
    {

        if (empty($q_id)) {
            global $query_id;
            $q_id = $query_id;
        }

        return @mysqli_num_rows($q_id);
    }
}

/**
 * Nombre de champs d'une requête
 */
if (! function_exists('sql_num_fields'))
{
    /**
     * [sql_num_fields description]
     *
     * @param   $q_id  [$q_id description]
     *
     * @return  int
     */
    function sql_num_fields($q_id = ''): int
    {
        global $dblink;

        if (empty($q_id)) {
            global $query_id;
            $q_id = $query_id;
        }

        return mysqli_field_count($dblink);
    }
}

/**
 * Nombre de lignes affectées par les requêtes de type INSERT, UPDATE et DELETE
 */
if (! function_exists('sql_affected_rows'))
{
    /**
     * [sql_affected_rows description]
     *
     * @return  string|int
     */
    function sql_affected_rows(): string|int
    {
        global $dblink;

        return @mysqli_affected_rows($dblink);
    }
}
 
/**
 * Le dernier identifiant généré par un champ de type AUTO_INCREMENT
 */
if (! function_exists('sql_last_id'))
{
    /**
     * [sql_last_id description]
     *
     * @return  string|int
     */
    function sql_last_id(): string|int
    {
        global $dblink;

        return @mysqli_insert_id($dblink);
    }
}

/**
 * Lister les tables
 */
if (! function_exists('sql_list_tables'))
{
    /**
     * [sql_list_tables description]
     *
     * @param            $dbnom  [$dbnom description]
     *
     * @return  mysqli_result|bool
     */
    function sql_list_tables($dbnom = ''): mysqli_result|bool 
    {

        if (empty($dbnom)) {
            global $dbname;
            $dbnom = $dbname;
        }

        return @sql_query("SHOW TABLES FROM $dbnom");
    }
}

/**
 * Controle
 */
if (! function_exists('sql_select_db'))
{
    /**
     * [sql_select_db description]
     *
     * @return  bool
     */
    function sql_select_db(): bool
    {
        global $dbname, $dblink;

        if (!@mysqli_select_db($dblink, $dbname)) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * Libère toute la mémoire et les ressources utilisées par la requête $query_id
 */
if (! function_exists('sql_free_result'))
{
    /**
     * [sql_free_result description]
     *
     * @param   $q_id  [$q_id description]
     *
     * @return  void
     */
    function sql_free_result($q_id)
    {
        if ($q_id instanceof mysqli_result) {
            return @mysqli_free_result($q_id);
        }
    }
}

/**
 * Ferme la connexion avec la Base de données
 */
if (! function_exists('sql_close'))
{
    /**
     * [sql_close description]
     *
     * @param   mysqli  $dblink  [$dblink description]
     *
     * @return  true
     */
    function sql_close(mysqli $dblink): true
    {
        return @mysqli_close($dblink);
    }
}
