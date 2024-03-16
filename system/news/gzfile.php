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

class gzfile extends archive
{

    var $gzdata = "";

    
    function addfile($data, $filename = null, $comment = null)
    {
        $flags = bindec("000" . (!empty($comment) ? "1" : "0") . (!empty($filename) ? "1" : "0") . "000");

        $this->gzdata .= pack("C1C1C1C1VC1C1", 0x1f, 0x8b, 8, $flags, time(), 2, 0xFF);
        
        if (!empty($filename)) {
            $this->gzdata .= "$filename\0";
        }

        if (!empty($comment)) {
            $this->gzdata .= "$comment\0";
        }

        $this->gzdata .= gzdeflate($data);
        $this->gzdata .= pack("VV", crc32($data), strlen($data));
    }

    function extract($data)
    {
        $id = unpack("H2id1/H2id2", substr($data, 0, 2));

        if ($id['id1'] != "1f" || $id['id2'] != "8b") {
            return $this->error("Données non valide.");
        }

        $temp = unpack("Cflags", substr($data, 2, 1));
        $temp = decbin($temp['flags']);

        if ($temp & 0x8) {
            $flags['name'] = 1;
        }

        if ($temp & 0x4) {
            $flags['comment'] = 1;
        }

        $offset = 10;
        $filename = "";

        while (!empty($flags['name'])) {
            $char = substr($data, $offset, 1);
            $offset++;

            if ($char == "\0") {
                break;
            }
            $filename .= $char;
        }

        if ($filename == "") {
            $filename = "file";
        }

        $comment = "";
        while (!empty($flags['comment'])) {
            $char = substr($data, $offset, 1);
            $offset++;

            if ($char == "\0") {
                break;
            }
            $comment .= $char;
        }

        $temp = unpack("Vcrc32/Visize", substr($data, strlen($data) - 8, 8));
        $crc32 = $temp['crc32'];
        $isize = $temp['isize'];
        $data = gzinflate(substr($data, $offset, strlen($data) - 8 - $offset));

        if ($crc32 != crc32($data)) {
            return $this->error("Erreur de contrôle");
        }

        return array('filename' => $filename, 'comment' => $comment, 'size' => $isize, 'data' => $data);
    }

    function arc_getdata()
    {
        return $this->gzdata;
    }

    function filedownload($filename)
    {
        @header("Content-Type: application/x-gzip; name=\"$filename\"");
        @header("Content-Disposition: attachment; filename=\"$filename\"");
        @header("Pragma: no-cache");
        @header("Expires: 0");

        print($this->arc_getdata());
    }
}
