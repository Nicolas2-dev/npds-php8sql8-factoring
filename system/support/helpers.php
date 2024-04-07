<?php

use npds\system\logs\logs;
use npds\system\news\gzfile;
use npds\system\news\zipfile;


if (! function_exists('Admin_alert'))
{
    function Admin_alert($motif)
    {
        global $admin;

        setcookie('admin', '', 0);
        unset($admin);

        logs::Ecr_Log('security', 'auth.inc.php/Admin_alert : ' . $motif, '');
        
        if (file_exists("storage/meta/meta.php")) {
            $Titlesitename = 'NPDS';
            include("storage/meta/meta.php");
        }

        echo '
            </head>
            <body>
                <br /><br /><br />
                <p style="font-size: 24px; font-family: Tahoma, Arial; color: red; text-align:center;"><strong>.: ' . translate("Votre adresse Ip est enregistrée") . ' :.</strong></p>
            </body>
        </html>';
        die();
    }
}

if (! function_exists('get_os'))
{
    /**
     * retourne true si l'OS de la station cliente est Windows sinon false
     *
     * @return  [type]  [return description]
     */
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
}

if (! function_exists('send_file'))
{
    /**
     * compresse et télécharge un fichier
     *
     * @param   [type]  $line       le flux
     * @param   [type]  $filename   
     * @param   [type]  $extension  le fichier
     * @param   [type]  $MSos       (voir fonction get_os)
     *
     * @return  [type]              [return description]
     */    
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
}

if (! function_exists('send_tofile'))
{
    /**
     * compresse et enregistre un fichier
     *
     * @param   [type]  $line        le flux
     * @param   [type]  $repertoire  
     * @param   [type]  $filename    
     * @param   [type]  $extension   
     * @param   [type]  $MSos        (voir fonction get_os)
     *
     * @return  [type]               [return description]
     */
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
}

if (! function_exists('getip'))
{
    /**
     * [getip description]
     *
     * @return  [type]  [return description]
     */
    function getip()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        if (strpos($realip, ",") > 0) {
            $realip = substr($realip, 0, strpos($realip, ",") - 1);
        }

        // from Gu1ll4um3r0m41n - 08-05-2007 - dev 2012
        return urlencode(trim($realip));
    }
}

if (! function_exists('access_denied'))
{
    /**
     * [access_denied description]
     *
     * @return  [type]  [return description]
     */
    function access_denied()
    {
        include("admin/die.php");
    }
}

if (! function_exists('array_get'))
{
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
}

if (! function_exists('array_has'))
{
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
}

if (! function_exists('array_set'))
{
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
}