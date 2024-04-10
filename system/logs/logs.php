<?php

declare(strict_types=1);

namespace npds\system\logs;

class logs
{
 
    /**
     * Pour écrire dans un log (security.log par exemple)
     *
     * @param   string  $fic_log  [$fic_log description]
     * @param   string  $req_log  [$req_log description]
     * @param   string  $mot_log  [$mot_log description]
     *
     * @return  void              [return description]
     */
    public static function Ecr_Log(string $fic_log, string $req_log, string $mot_log): void
    {
        // $Fic_log= the file name :
        //  => "security" for security maters
        //  => ""
        // $req_log= a phrase describe the infos
        //
        // $mot_log= if "" the Ip is recorded, else extend status infos
        $logfile = "storage/logs/$fic_log.log";
        
        $fp = fopen($logfile, 'a');
        flock($fp, 2);
        fseek($fp, filesize($logfile));
        
        if ($mot_log == "") {
            $mot_log = "IP=>" . getip();
        }

        $ibid = sprintf("%-10s %-60s %-10s\r\n", date("m/d/Y H:i:s", time()), basename($_SERVER['PHP_SELF']) ."=>". strip_tags(urldecode($req_log)), strip_tags(urldecode($mot_log)));
        
        fwrite($fp, $ibid);
        flock($fp, 3);
        fclose($fp);
    }
}
