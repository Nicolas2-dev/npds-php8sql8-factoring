<?php

use Modules\TwoNews\Compressed\GzFile;
use Modules\TwoNews\Compressed\Archive;
use Modules\TwoNews\Compressed\ZipFile;


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

        if(class_exists(Archive::class) && method_exists(Archive::class, 'gzcompress')) {
            $compressed = true;
        }

        if ($compressed) {
            if ($MSos) {
                $arc = new ZipFile();
                $filez = $filename . ".zip";
            } else {
                $arc = new GzFile();
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

        if(class_exists(Archive::class) && method_exists(Archive::class, 'gzcompress')) {
            $compressed = true;
        }

        if ($compressed) {
            if ($MSos) {
                $arc = new ZipFile();
                $filez = $filename . ".zip";
            } else {
                $arc = new GzFile();
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
