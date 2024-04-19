<?php

declare(strict_types=1);

namespace npds\support\news;

class archive
{

    var $cwd = "./";

    var $recursesd = 1;

    var $errors = [];

    var $overwrite = 0;

    var $defaultperms   = 0644;


    public function __construct($flags = array())
    {
        if (isset($flags['overwrite'])) {
            $this->overwrite = $flags['overwrite'];
        }

        if (isset($flags['defaultperms'])) {
            $this->defaultperms = $flags['defaultperms'];
        }
    }

    function adddirectories($dirlist)
    {
        $pwd = getcwd();
        @chdir($this->cwd);
        $filelist = array();

        foreach ($dirlist as $current) {
            if (@is_dir($current)) {
                $temp = $this->parsedirectories($current);

                foreach ($temp as $filename) {
                    $filelist[] = $filename;
                }
            } elseif (@file_exists($current)) {
                $filelist[] = $current;
            }
        }
        @chdir($pwd);

        $this->addfiles($filelist);
    }

    function parsedirectories($dirname)
    {
        $filelist = array();
        $dir = @opendir($dirname);

        while (false !== ($file = readdir($dir))) {
            if ($file == "." || $file == ".." || $file == "default.html" || $file == "index.html") {
                continue;
            } elseif (@is_dir($dirname . "/" . $file)) {
                if ($this->recursesd != 1) {
                    continue;
                }

                $temp = $this->parsedirectories($dirname . "/" . $file);

                foreach ($temp as $file2) {
                    $filelist[] = $file2;
                }
            } elseif (@file_exists($dirname . "/" . $file)) {
                $filelist[] = $dirname . "/" . $file;
            }
        }
        @closedir($dir);

        return $filelist;
    }

    function filewrite($filename, $perms = null)
    {
        if ($this->overwrite != 1 && @file_exists($filename)) {
            return $this->error("Le fichier $filename existe déjà.");
        }

        if (@file_exists($filename)) {
            @unlink($filename);
        }

        $fp = @fopen($filename, "wb");

        if (!fwrite($fp, (string) $this->arc_getdata())) {
            return $this->error("Impossible d'écrire les données dans le fichier $filename.");
        }

        @fclose($fp);

        if (!isset($perms)) {
            $perms = $this->defaultperms;
        }

        @chmod($filename, $perms);
    }

    function extractfile($filename)
    {
        if ($fp = @fopen($filename, "rb")) {
            if (filesize($filename) > 0) {
                return $this->extract(fread($fp, filesize($filename)));
            } else {
                return $this->error("Fichier $filename vide.");
            }
            @fclose($fp);
        } else {
            return $this->error("Impossible d'ouvrir le fichier $filename.");
        }
    }

    function error($error)
    {
        $this->errors[] = $error;

        return 0;
    }

    function addfiles($filelist)
    {
        //
    }

    function extract($data)
    {
        //
    }

    function arc_getdata()
    {
        //
    }
}
