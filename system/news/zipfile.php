<?php
/*--------------------------------------------------
 | GZIP/ZIP ARCHIVE CLASSES
 | By Devin Doucette
 | Copyright (c) 2003 Devin Doucette
 | Email: darksnoopy@shaw.ca
 |
 | Modified by Hexagone pour NPDS Narval
 | Modified by M. PASCAL aKa EBH (plan.net@free.fr)
 | Modified by Developpeur (developpeur@npds.org)
 | Corrected by Developpeur for Sable Evolution
 | Corrected by jpb for REvolution 16 php > 7
 +--------------------------------------------------
 | Email bugs/suggestions to darksnoopy@shaw.ca
 +--------------------------------------------------
 | This script has been created and released under
 | the GNU GPL and is free to use and redistribute
 | only if this copyright statement is not removed
 +--------------------------------------------------*/

/*------------------------------------------------------------
 | To create gzip files:
 | $example = new gzfile($cwd,$flags); // args optional
 | -current working directory
 | -flags (array):
 |  -overwrite - whether to overwrite existing files or return
 |    an error message
 |  -defaultperms - default file permissions (like chmod(),
 |    must include 0 in front of value [eg. 0777, 0644])
 +------------------------------------------------------------*/

/*------------------------------------------------------------
 | To create zip files:
 | $example = new zipfile($cwd,$flags); // args optional
 | -current working directory
 | -flags (array):
 |  -overwrite - whether to overwrite existing files or return
 |    an error message
 |  -defaultperms - default file permissions (like chmod(),
 |    must include 0 in front of value [eg. 0777, 0644])
 |  -time - timestamp to use to replace the mtime from files
 |  -recursesd[1,0] - whether or not to include subdirs
 |  -storepath[1,0] - whether or not to store relative paths
 |  -level[0-9] - compression level (0 = none, 9 = max)
 |  -comment - comment to add to the archive
 +------------------------------------------------------------*/

/*------------------------------------------------------------
 | To add files:
 | $example->addfile($data,$filename,$flags);
 | -data - file contents
 | -filename - name of file to be put in archive
 | -flags (all flags are optional)
 | -flags (tar) [array]: -same flags as tarfile()
 | -flags (gzip) [string]: -comment to add to archive
 | -flags (zip) [array] -time - last modification time
 |
 | $example->addfiles($filelist);
 | -filelist - array of file names relative to CWD
 |
 | $example->adddirectories($dirlist);
 | -dirlist - array of directory names relative to CWD
 +------------------------------------------------------------*/

/*------------------------------------------------------------
 | To output files:
 | $example->arc_getdata();
 | -returns file contents
 |
 | $example->filedownload($filename);
 | -filename - the name to give the file that is being sent
 |
 | $example->filewrite($filename,$perms); // perms optional
 | -filename - the name (including path) of the file to write
 | -perms - permissions to give the file after it is written
 +------------------------------------------------------------*/

/*------------------------------------------------------------
 | To extract files (gzip)
 | $example->extract($data);
 | -data - data to extract files from
 | -returns an array containing file attributes and contents
 |
 | $example->extractfile($filename);
 | -filename - the name (including path) of the file to use
 | -returns an array containing file attributes and contents
 |
 | Both functions will return a string containing any errors
 +------------------------------------------------------------*/

declare(strict_types=1);

namespace npds\system\news;

class zipfile extends archive
{
    var $cwd = "./";

    var $comment = "";

    var $level = 9;

    var $offset = 0;

    var $recursesd = 1;

    var $storepath = 1;

    var $replacetime = 0;

    var $central = array();

    var $zipdata = array();


    public function __construct($cwd = "./", $flags = array())
    {
        $this->cwd = $cwd;

        if (isset($flags['time'])) {
            $this->replacetime = $flags['time'];
        }

        if (isset($flags['recursesd'])) {
            $this->recursesd = $flags['recursesd'];
        }

        if (isset($flags['storepath'])) {
            $this->storepath = $flags['storepath'];
        }

        if (isset($flags['level'])) {
            $this->level = $flags['level'];
        }

        if (isset($flags['comment'])) {
            $this->comment = $flags['comment'];
        }

        //$this->archive($flags); ????
    }

    function addfile($data, $filename, $flags = array())
    {
        if ($this->storepath != 1) {
            $filename = strstr($filename, "/") ? substr($filename, strrpos($filename, "/") + 1) : $filename;
        } else {
            $filename = preg_replace("/^(\.{1,2}(\/|\\\))+/", "", $filename);
        }

        $mtime = !empty($this->replacetime) ? getdate($this->replacetime) : (isset($flags['time']) ? getdate($flags['time']) : getdate());
        $mtime = preg_replace("/(..){1}(..){1}(..){1}(..){1}/", "\\x\\4\\x\\3\\x\\2\\x\\1", dechex(($mtime['year'] - 1980 << 25) | ($mtime['mon'] << 21) | ($mtime['mday'] << 16) | ($mtime['hours'] << 11) | ($mtime['minutes'] << 5) | ($mtime['seconds'] >> 1)));
        
        eval('$mtime = "' . $mtime . '";');
        
        $crc32 = crc32($data);
        $normlength = strlen($data);
        $data = gzcompress($data, $this->level);
        $data = substr($data, 2, strlen($data) - 6);
        $complength = strlen($data);

        $this->zipdata[] = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $mtime . pack("VVVvv", $crc32, $complength, $normlength, strlen($filename), 0x00) . $filename . $data . pack("VVV", $crc32, $complength, $normlength);
        $this->central[] = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00" . $mtime . pack("VVVvvvvvVV", $crc32, $complength, $normlength, strlen($filename), 0x00, 0x00, 0x00, 0x00, 0x0000, $this->offset) . $filename;
        $this->offset = strlen(implode("", $this->zipdata));
    }

    function addfiles($filelist)
    {
        $pwd = getcwd();
        @chdir($this->cwd);
        foreach ($filelist as $current) {
            if (!@file_exists($current)) {
                continue;
            }

            $stat = stat($current);

            if ($fp = @fopen($current, "rb")) {
                if ($stat[7] > 0) {
                    $data = fread($fp, $stat[7]);
                }

                fclose($fp);
            } else {
                $data = "";
            }

            $flags = array('time' => $stat[9]);

            $this->addfile($data, $current, $flags);
        }
        @chdir($pwd);
    }

    function arc_getdata()
    {
        $central = implode("", $this->central);
        $zipdata = implode("", $this->zipdata);

        return $zipdata . $central . "\x50\x4b\x05\x06\x00\x00\x00\x00" . pack("vvVVv", sizeof($this->central), sizeof($this->central), strlen($central), strlen($zipdata), strlen($this->comment)) . $this->comment;
    }

    function filedownload($filename)
    {
        @header("Content-Type: application/zip; name=\"$filename\"");
        @header("Content-Disposition: attachment; filename=\"$filename\"");
        @header("Pragma: no-cache");
        @header("Expires: 0");

        print($this->arc_getdata());
    }
}
