<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2001-2023 by Philippe Brunier                     */
/* =========================                                            */
/* Snipe 2003                                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\assets\css;
use npds\support\auth\users;
use npds\system\config\Config;
use npds\support\utility\crypt;
use npds\system\support\facades\DB;
use npds\system\support\facades\Request;


if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

switch ($apli = Request::query('apli')) {
    case 'f-manager':
        $fma = rawurldecode(crypt::decrypt(Request::query('att_id')));
        $fma = explode('#fma#', $fma);
        $att_id = crypt::decrypt($fma[0]);
        $att_name = crypt::decrypt($fma[1]);

    case 'forum_npds':
        $user = users::getUser();
        if (isset($user)) {
            $userX = base64_decode($user);
            $userdata = explode(':', $userX);
            $marqueurM = substr($userdata[2], 8, 6);
        }

    case 'minisite':
    case 'getfile':
        $att_name = StripSlashes(str_replace("\"", '', rawurldecode(Request::query('att_name'))));
        
        $att_id = Request::query('att_id');

        if ((preg_match('#^[a-z0-9_\.-]#i', $att_name) 
        or ($Mmod == $marqueurM)) 
        and !stristr($att_name, ".*://") 
        and !stristr($att_name, "..") 
        and !stristr($att_name, "../") 
        and !stristr($att_name, "script") 
        and !stristr($att_name, "cookie") 
        and !stristr($att_name, "iframe") 
        and  !stristr($att_name, "applet") 
        and !stristr($att_name, "object")) {
            if (preg_match('#^[a-z0-9_/\.-]#i', $att_id) 
            and !stristr($att_id, ".*://") 
            and !stristr($att_id, "..") 
            and !stristr($att_id, "../") 
            and !stristr($att_id, "script") 
            and !stristr($att_id, "cookie") 
            and !stristr($att_id, "iframe") 
            and  !stristr($att_id, "applet") 
            and !stristr($att_id, "object")) {
                $fic = '';
                switch ($apli) {
                        // Forum
                    case 'forum_npds':
                        $fic = 'modules/upload/storage/upload_forum/'. $att_id . $apli . $att_name;
                        break;

                        // MiniSite
                    case 'minisite':
                        $fic = 'storage/users_private/' . $att_id .'/mns/'. $att_name;
                        break;

                        // Application générique : la présence de getfile.conf.php est nécessaire
                    case 'getfile':
                        if (file_exists($att_id .'/getfile.conf.php') or file_exists($att_id .'/.getfile.conf.php')) {
                            $fic = $att_id .'/'. $att_name;
                        } else {
                            header('location: '. site_url('index.php'));
                        }
                        break;

                    case 'f-manager';
                        $fic = $att_id .'/'. $att_name;
                        break;
                }

                include('modules/upload/language/'. Config::get('npds.languaghe') .'/language.php');
                include('modules/upload/http/include/mimetypes.php');

                $suffix = strtoLower(substr(strrchr($att_name, '.'), 1));
                
                $type = Request::query('type');
                
                if (isset($type)) {
                    // strip "; name=.... " (Opera6)
                    list($type, $garbage) = explode(';', $type); 
                }     

                if (isset($mimetypes[$suffix])) {
                    $type = $mimetypes[$suffix];
                } elseif (empty($type) || ($type == 'application/octet-stream')) {
                    $type = $mimetype_default;
                }

                $att_type = $type;
                $att_size = @filesize($fic);

                if (file_exists($fic)) {
                    if ($apli == 'forum_npds') {
                        
                        include('auth.php');

                        DB::table(Config::get('forum.config.upload_table'))->where('att_id', $att_id)->update(array(
                            'compteur'       => DB::raw('compteur+1'),
                        ));
                    }

                    // Output file to the browser
                    header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
                    header('Cache-Control: max-age=60, must-revalidate');

                    // work with mimetypes.php for showing source'code
                    if ($att_type == 'text/source') {
                        include('storage/meta/meta.php');

                        echo css::import_css(Config::get('npds.Default_Theme'), Config::get('npds.language'), '', '', '');
                        
                        echo '
                        </head>
                        <body>
                            <div style="background-color:white; padding:4px;">';
                            show_source($fic);
                            echo '
                            </div>
                        </body>
                        </html>';
                        die();
                    }

                    if ($att_type == "application/x-shockwave-flash") {
                        header("Content-type: application/x-shockwave-flash");
                    } else {
                        header("Content-Type: $att_type; name=\"" . basename($att_name) . "\"");
                    }

                    header("Content-length: $att_size");
                    header("Content-Disposition: inline; filename=\"" . basename($att_name) . "\"");

                    readfile($fic);
                } else {
                    header('location: '. site_url('index.php'));
                }
            } else {
                header('location: '. site_url('index.php'));
            }
        } else {
            header('location: '. site_url('index.php'));
        }
        break;

    case 'captcha':
        $mot = rawurldecode(crypt::decrypt(Request::query('att_id')));
        $font = 16;

        $width = imagefontwidth($font) * strlen($mot);
        $height = imagefontheight($font);

        $img = imagecreate($width + 4, $height + 4);
        $blanc = imagecolorallocate($img, 255, 255, 255);
        $noir = imagecolorallocate($img, 0, 0, 0);

        imagecolortransparent($img, $blanc);
        
        // if ('utf-8'=="utf-8") {
        //    $mot=utf8_decode($mot);
        // }

        imagestring($img, $font, 1, 1, $mot, $noir);
        imagepng($img);
        imagedestroy($img);
        // header('Content-type: image/png');// no need ? as included in other pages we have header already send ..?!
        break;

    default:
        header('location: '. site_url('index.php'));
        break;
}
