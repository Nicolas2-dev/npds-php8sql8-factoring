<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Kill the Ereg by JPB on 24-01-2011                                   */
/* This version name NPDS Copyright (c) 2001-2023 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

use npds\support\logs\logs;
use npds\support\cache\cache;
use npds\support\str;
use npds\support\config\ConfigSaveFile;

/**
 * [ConfigSave description]
 *
 * @param   int     $xparse                  [$xparse description]
 * @param   string  $xsitename               [$xsitename description]
 * @param   string  $xnuke_url               [$xnuke_url description]
 * @param   string  $xsite_logo              [$xsite_logo description]
 * @param   string  $xslogan                 [$xslogan description]
 * @param   string  $xstartdate              [$xstartdate description]
 * @param   string  $xadminmail              [$xadminmail description]
 * @param   int     $xtop                    [$xtop description]
 * @param   int     $xstoryhome              [$xstoryhome description]
 * @param   int     $xoldnum                 [$xoldnum description]
 * @param   int     $xultramode              [$xultramode description]
 * @param   int     $xanonpost               [$xanonpost description]
 * @param   string  $xDefault_Theme          [$xDefault_Theme description]
 * @param   int     $xbanners                [$xbanners description]
 * @param   string  $xmyIP                   [$xmyIP description]
 * @param   string  $xfoot1                  [$xfoot1 description]
 * @param   string  $xfoot2                  [$xfoot2 description]
 * @param   string  $xfoot3                  [$xfoot3 description]
 * @param   string  $xfoot4                  [$xfoot4 description]
 * @param   string  $xbackend_title          [$xbackend_title description]
 * @param   string  $xbackend_language       [$xbackend_language description]
 * @param   string  $xbackend_image          [$xbackend_image description]
 * @param   int     $xbackend_width          [$xbackend_width description]
 * @param   int     $xbackend_height         [$xbackend_height description]
 * @param   string  $xlanguage               [$xlanguage description]
 * @param   string  $xlocale                 [$xlocale description]
 * @param   int     $xperpage                [$xperpage description]
 * @param   int     $xpopular                [$xpopular description]
 * @param   int     $xnewlinks               [$xnewlinks description]
 * @param   int     $xtoplinks               [$xtoplinks description]
 * @param   int     $xlinksresults           [$xlinksresults description]
 * @param   int     $xlinks_anonaddlinklock  [$xlinks_anonaddlinklock description]
 * @param   int     $xnotify                 [$xnotify description]
 * @param   string  $xnotify_email           [$xnotify_email description]
 * @param   string  $xnotify_subject         [$xnotify_subject description]
 * @param   string  $xnotify_message         [$xnotify_message description]
 * @param   string  $xnotify_from            [$xnotify_from description]
 * @param   int     $xmoderate               [$xmoderate description]
 * @param   string  $xanonymous              [$xanonymous description]
 * @param   int     $xmaxOptions             [$xmaxOptions description]
 * @param   int     $xsetCookies             [$xsetCookies description]
 * @param   string  $xtipath                 [$xtipath description]
 * @param   string  $xuserimg                [$xuserimg description]
 * @param   string  $xadminimg               [$xadminimg description]
 * @param   int     $xadmingraphic           [$xadmingraphic description]
 * @param   int     $xadmart                 [$xadmart description]
 * @param   int     $xminpass                [$xminpass description]
 * @param   int     $xhttpref                [$xhttpref description]
 * @param   int     $xhttprefmax             [$xhttprefmax description]
 * @param   int     $xpollcomm               [$xpollcomm description]
 * @param   int     $xlinkmainlogo           [$xlinkmainlogo description]
 * @param   string  $xstart_page             [$xstart_page description]
 * @param   int     $xsmilies                [$xsmilies description]
 * @param   int     $xOnCatNewLink           [$xOnCatNewLink description]
 * @param   string  $xEmailFooter            [$xEmailFooter description]
 * @param   int     $xshort_user             [$xshort_user description]
 * @param   int     $xgzhandler              [$xgzhandler description]
 * @param   bool    $xrss_host_verif         [$xrss_host_verif description]
 * @param   bool    $xcache_verif            [$xcache_verif description]
 * @param   int     $xmember_list            [$xmember_list description]
 * @param   string  $xdownload_cat           [$xdownload_cat description]
 * @param   int     $xmod_admin_news         [$xmod_admin_news description]
 * @param   int     $xgmt                    [$xgmt description]
 * @param   int     $xAutoRegUser            [$xAutoRegUser description]
 * @param   string  $xTitlesitename          [$xTitlesitename description]
 * @param   bool    $xfilemanager            [$xfilemanager description]
 * @param   int     $xshort_review           [$xshort_review description]
 * @param   int     $xnot_admin_count        [$xnot_admin_count description]
 * @param   int     $xadmin_cook_duration    [$xadmin_cook_duration description]
 * @param   int     $xuser_cook_duration     [$xuser_cook_duration description]
 * @param   int     $xtroll_limit            [$xtroll_limit description]
 * @param   int     $xsubscribe              [$xsubscribe description]
 * @param   int     $xCloseRegUser           [$xCloseRegUser description]
 * @param   int     $xshort_menu_admin       [$xshort_menu_admin description]
 * @param   int     $xmail_fonction          [$xmail_fonction description]
 * @param   int     $xmemberpass             [$xmemberpass description]
 * @param   int     $xshow_user              [$xshow_user description]
 * @param   bool    $xdns_verif              [$xdns_verif description]
 * @param   int     $xmember_invisible       [$xmember_invisible description]
 * @param   string  $xavatar_size            [$xavatar_size description]
 * @param   string  $xlever                  [$xlever description]
 * @param   string  $xcoucher                [$xcoucher description]
 * @param   bool    $xmulti_langue           [$xmulti_langue description]
 * @param   string  $xadmf_ext               [$xadmf_ext description]
 * @param   int     $xsavemysql_size         [$xsavemysql_size description]
 * @param   int     $xsavemysql_mode         [$xsavemysql_mode description]
 * @param   bool    $xtiny_mce               [$xtiny_mce description]
 * @param   int     $xnpds_twi               [$xnpds_twi description]
 * @param   int     $xnpds_fcb               [$xnpds_fcb description]
 * @param   string  $xDefault_Skin           [$xDefault_Skin description]
 * @param   bool    $xmail_debug             [$xmail_debug description]
 * @param   string  $xsmtp_host              [$xsmtp_host description]
 * @param   int     $xsmtp_auth              [$xsmtp_auth description]
 * @param   string  $xsmtp_username          [$xsmtp_username description]
 * @param   string  $xsmtp_password          [$xsmtp_password description]
 * @param   int     $xsmtp_secure            [$xsmtp_secure description]
 * @param   string  $xsmtp_crypt             [$xsmtp_crypt description]
 * @param   string  $xsmtp_port              [$xsmtp_port description]
 * @param   int     $xdkim_auto              [$xdkim_auto description]
 *
 * @return  void                             [return description]
 */
function ConfigSave(int $xparse, string $xsitename, string $xnuke_url, string $xsite_logo, string $xslogan, string $xstartdate, string $xadminmail, int $xtop, int $xstoryhome, int $xoldnum, int $xultramode, int $xanonpost, string $xDefault_Theme, int $xbanners, string $xmyIP, string $xfoot1, string $xfoot2, string $xfoot3, string $xfoot4, string $xbackend_title, string $xbackend_language, string $xbackend_image, int $xbackend_width, int $xbackend_height, string $xlanguage, string $xlocale, int $xperpage, int $xpopular, int $xnewlinks, int $xtoplinks, int $xlinksresults, int $xlinks_anonaddlinklock, int $xnotify, string $xnotify_email, string $xnotify_subject,  string $xnotify_message, string $xnotify_from, int $xmoderate, string $xanonymous, int $xmaxOptions, int $xsetCookies, string $xtipath, string $xuserimg, string $xadminimg, int $xadmingraphic, int $xadmart, int $xminpass, int $xhttpref, int $xhttprefmax, int $xpollcomm, int $xlinkmainlogo, string $xstart_page, int $xsmilies, int $xOnCatNewLink, string $xEmailFooter, int  $xshort_user, int $xgzhandler, bool $xrss_host_verif, bool $xcache_verif, int $xmember_list, string $xdownload_cat, int $xmod_admin_news, int $xgmt, int $xAutoRegUser, string $xTitlesitename, bool $xfilemanager, int $xshort_review, int $xnot_admin_count, int $xadmin_cook_duration, int $xuser_cook_duration, int $xtroll_limit, int $xsubscribe, int $xCloseRegUser, int $xshort_menu_admin, int $xmail_fonction, int $xmemberpass, int $xshow_user, bool $xdns_verif, int $xmember_invisible, string $xavatar_size, string $xlever, string $xcoucher, bool $xmulti_langue, string $xadmf_ext, int $xsavemysql_size, int $xsavemysql_mode, bool $xtiny_mce, int $xnpds_twi, int $xnpds_fcb, string $xDefault_Skin, bool $xmail_debug, string $xsmtp_host, int $xsmtp_auth, string $xsmtp_username, string $xsmtp_password, int $xsmtp_secure, string $xsmtp_crypt, string $xsmtp_port, int $xdkim_auto): void
{
    if ($xparse == 0) {
        $xsitename =  str::FixQuotes($xsitename);
        $xTitlesitename = str::FixQuotes($xTitlesitename);
    } else {
        $xsitename =  stripslashes($xsitename);
        $xTitlesitename = stripslashes($xTitlesitename);
    }

    $xnuke_url = str::FixQuotes($xnuke_url);
    $xsite_logo = str::FixQuotes($xsite_logo);

    if ($xparse == 0) {
        $xslogan = str::FixQuotes($xslogan);
        $xstartdate = str::FixQuotes($xstartdate);
    } else {
        $xslogan = stripslashes($xslogan);
        $xstartdate = stripslashes($xstartdate);
    }

    // Theme
    $Default_Theme = str::FixQuotes($xDefault_Theme);

    if ($xDefault_Theme != $Default_Theme) {
        include("config/cache.config.php");

        $dh = opendir($CACHE_CONFIG['data_dir']);
        
        while (false !== ($filename = readdir($dh))) {
            if ($filename === '.' or $filename === '..' or $filename === 'ultramode.txt' or $filename === 'net2zone.txt' or $filename === 'sql') {
                continue;
            }

            unlink($CACHE_CONFIG['data_dir'] . $filename);
        }
    }

    $xmyIP = str::FixQuotes($xmyIP);

    $xfoot1 = str_replace(chr(13) . chr(10), "\n", $xfoot1);
    $xfoot2 = str_replace(chr(13) . chr(10), "\n", $xfoot2);
    $xfoot3 = str_replace(chr(13) . chr(10), "\n", $xfoot3);
    $xfoot4 = str_replace(chr(13) . chr(10), "\n", $xfoot4);

    if ($xparse == 0) {
        $xbackend_title = str::FixQuotes($xbackend_title);
    } else {
        $xbackend_title = stripslashes($xbackend_title);
    }

    $xbackend_language = str::FixQuotes($xbackend_language);
    $xbackend_image = str::FixQuotes($xbackend_image);
    $xbackend_width = str::FixQuotes($xbackend_width);
    $xbackend_height = str::FixQuotes($xbackend_height);
    $xlanguage = str::FixQuotes($xlanguage);
    $xlocale = str::FixQuotes($xlocale);
    $xnotify_email = str::FixQuotes($xnotify_email);

    if ($xparse == 0) {
        $xnotify_subject = str::FixQuotes($xnotify_subject);
        $xdownload_cat = str::FixQuotes($xdownload_cat);
    } else {
        $xnotify_subject = stripslashes($xnotify_subject);
        $xdownload_cat = stripslashes($xdownload_cat);
    }

    $xnotify_message = str_replace(chr(13) . chr(10), "\n", $xnotify_message);

    $xnotify_from = str::FixQuotes($xnotify_from);
    $xanonymous = str::FixQuotes($xanonymous);
    $xtipath = str::FixQuotes($xtipath);
    $xuserimg = str::FixQuotes($xuserimg);
    $xadminimg = str::FixQuotes($xadminimg);

    ConfigSaveFile::save_setting_npds($xparse, $xsitename, $xnuke_url, $xsite_logo, $xslogan, $xstartdate, $xadminmail, $xtop, $xstoryhome, $xoldnum, 
    $xultramode, $xanonpost, $xDefault_Theme, $xbanners, $xmyIP, $xfoot1, $xfoot2, $xfoot3, $xfoot4, $xbackend_title, $xbackend_language, $xbackend_image, 
    $xbackend_width, $xbackend_height, $xlanguage, $xlocale, $xperpage, $xpopular, $xnewlinks, $xtoplinks, $xlinksresults, $xlinks_anonaddlinklock, $xnotify, 
    $xnotify_email, $xnotify_subject, $xnotify_message, $xnotify_from, $xmoderate, $xanonymous, $xmaxOptions, $xsetCookies, $xtipath, $xuserimg, $xadminimg, 
    $xadmingraphic, $xadmart, $xminpass, $xhttpref, $xhttprefmax, $xpollcomm, $xlinkmainlogo, $xstart_page, $xsmilies, $xOnCatNewLink, $xshort_user, 
    $xgzhandler, $xrss_host_verif, $xcache_verif, $xmember_list, $xdownload_cat, $xmod_admin_news, $xgmt, $xAutoRegUser, $xTitlesitename, 
    $xshort_review, $xnot_admin_count, $xadmin_cook_duration, $xuser_cook_duration, $xtroll_limit, $xsubscribe, $xCloseRegUser, $xshort_menu_admin, $xmail_fonction, 
    $xmemberpass, $xshow_user, $xdns_verif, $xmember_invisible, $xavatar_size, $xlever, $xcoucher, $xmulti_langue, $xadmf_ext, $xsavemysql_size, $xsavemysql_mode, 
    $xtiny_mce, $xnpds_twi, $xnpds_fcb, $xDefault_Skin);

    // Versioning npds core
    ConfigSaveFile::save_setting_versioning();

    //Save Configuration FileManager
    ConfigSaveFile::save_setting_filemanager($xfilemanager);

    // Save Configuration Signature
    ConfigSaveFile::save_setting_signature($xEmailFooter);
    
    // Save configuration Mailer
    ConfigSaveFile::save_setting_mailler($xmail_debug, $xsmtp_host, $xsmtp_port, $xsmtp_auth, $xsmtp_username, $xsmtp_password, $xsmtp_secure, $xsmtp_crypt, $xdkim_auto);
    
    global $aid;
    logs::Ecr_Log("security", "ConfigSave() by AID : $aid", "");

    cache::SC_Clean();

    Header('Location: '. site_url('admin.php?op=AdminMain'));
}



