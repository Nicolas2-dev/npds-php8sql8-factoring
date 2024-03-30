<?php

use npds\system\news\gzfile;
use npds\system\support\env;
use npds\system\news\zipfile;
use npds\system\container\container;

#autodoc get_os() : retourne true si l'OS de la station cliente est Windows sinon false
function get_os()
{
    $client = getenv("HTTP_USER_AGENT");
    
    if (preg_match('#(\(|; )(Win)#', $client, $regs)) {
        if ($regs[2] == "Win") {
            $MSos = true;
        } else {
            $MSos = false;
        }
    } else {
        $MSos = false;
    }

    return $MSos;
}

#autodoc send_file($line,$filename,$extension,$MSos) : compresse et t&eacute;l&eacute;charge un fichier / $line : le flux, $filename et $extension le fichier, $MSos (voir fonction get_os)
function send_file($line, $filename, $extension, $MSos)
{
    $compressed = false;
    if (file_exists("system/news/archive.php")) {
        if (function_exists("gzcompress")) {
            $compressed = true;
        }
    }

    if ($compressed) {
        if ($MSos) {
            $arc = new zipfile();
            $filez = $filename . ".zip";
        } else {
            $arc = new gzfile();
            $filez = $filename . ".gz";
        }

        $arc->addfile($line, $filename . "." . $extension, "");
        $arc->arc_getdata();
        $arc->filedownload($filez);
    } else {
        if ($MSos) {
            header("Content-Type: application/octetstream");
        } else {
            header("Content-Type: application/octet-stream");
        }

        header("Content-Disposition: attachment; filename=\"$filename." . "$extension\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $line;
    }
}

#autodoc send_tofile($line,$repertoire,$filename,$extension,$MSos) : compresse et enregistre un fichier / $line : le flux, $repertoire $filename et $extension le fichier, $MSos (voir fonction get_os)
function send_tofile($line, $repertoire, $filename, $extension, $MSos)
{
    $compressed = false;

    if (file_exists("system/news/archive.php")) {
        if (function_exists("gzcompress")) {
            $compressed = true;
        }
    }

    if ($compressed) {
        if ($MSos) {
            $arc = new zipfile();
            $filez = $filename . ".zip";
        } else {
            $arc = new gzfile();
            $filez = $filename . ".gz";
        }

        $arc->addfile($line, $filename . "." . $extension, "");
        $arc->arc_getdata();

        if (file_exists($repertoire . "/" . $filez)) {
            unlink($repertoire . "/" . $filez);
        }

        $arc->filewrite($repertoire . "/" . $filez, $perms = null);
    } else {
        if ($MSos) {
            header("Content-Type: application/octetstream");
        } else {
            header("Content-Type: application/octet-stream");
        }

        header("Content-Disposition: attachment; filename=\"$filename." . "$extension\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo $line;
    }
}


/**
 * Get an item from an array using "dot" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key)) {
        return $array;
    } else if (isset($array[$key])) {
        return $array[$key];
    }
    
    foreach (explode('.', $key) as $segment) {
        if (! is_array($array) || ! array_key_exists($segment, $array)) {
            return $default;
        }

        $array = $array[$segment];
    }

    return $array;
}

/**
 * Check if an item exists in an array using "dot" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @return bool
 */
function array_has($array, $key)
{
    if (empty($array) || is_null($key)) {
        return false;
    } else if (array_key_exists($key, $array)) {
        return true;
    }

    foreach (explode('.', $key) as $segment) {
        if (! is_array($array) || ! array_key_exists($segment, $array)) {
            return false;
        }

        $array = $array[$segment];
    }

    return true;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return array
 */
function array_set(&$array, $key, $value)
{
    if (is_null($key)) {
        return $array = $value;
    }

    $keys = explode('.', $key);

    while (count($keys) > 1) {
        $key = array_shift($keys);

        if (! isset($array[$key]) || ! is_array($array[$key])) {
            $array[$key] = array();
        }

        $array =& $array[$key];
    }

    $key = array_shift($keys);

    $array[$key] = $value;

    return $array;
}