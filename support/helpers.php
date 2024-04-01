<?php

use npds\system\news\news;
use npds\system\block\boxe;
use npds\system\auth\groupe;
use npds\system\block\block;
use npds\system\cache\cache;
use npds\system\forum\forum;
use npds\system\support\str;
use npds\system\theme\theme;
use npds\system\mail\mailler;
use npds\system\utility\spam;
use npds\system\support\edito;
use npds\system\support\online;
use npds\system\language\language;
use npds\system\language\metalang;

// use npds\system\assets\js;
// use npds\system\chat\chat;
// use npds\system\date\date;
// use npds\system\logs\logs;
// use npds\system\news\news;
// use npds\system\assets\css;
// use npds\system\auth\users;
// use npds\system\block\boxe;
// use npds\system\assets\java;
// use npds\system\auth\groupe;
// use npds\system\block\block;
// use npds\system\cache\cache;
// use npds\system\forum\forum;
// use npds\system\routing\url;
// use npds\system\support\str;
// use npds\system\theme\theme;
// use npds\system\auth\authors;
// use npds\system\mail\mailler;
// use npds\system\pixels\image;
// use npds\system\utility\code;
// use npds\system\utility\spam;
// use npds\system\cookie\cookie;
// use npds\system\http\response;
// use npds\system\security\hack;
// use npds\system\support\edito;
// use npds\system\support\polls;
// use npds\system\support\stats;
// use npds\system\utility\crypt;
// use npds\system\support\online;
// use npds\system\session\session;
// use npds\system\support\counter;
// use npds\system\support\editeur;
// use npds\system\support\referer;
// use npds\system\security\protect;
// use npds\system\support\download;
// use npds\system\language\language;
// use npds\system\language\metalang;
// use npds\system\messenger\messenger;
// use npds\system\subscribe\subscribe;

// // function provisoire

// // assets css
// function  import_css($tmp_theme, $language, $fw_css, $css_pages_ref, $css)
// {
//     return css::import_css($tmp_theme, $language, $fw_css, $css_pages_ref, $css);
// }

// function import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css)
// {
//     return css::import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css);
// }

// function adminfoot($fv, $fv_parametres, $arg1, $foo)
// {
//     return css::adminfoot($fv, $fv_parametres, $arg1, $foo);
// }

// // assets js

// function auto_complete($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $temps_cache)
// {
//     return js::auto_complete($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $temps_cache);
// }

// function auto_complete_multi($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $req)
// {
//     return js::auto_complete_multi($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $req);
// }

// // assets java

// function JavaPopUp($F, $T, $W, $H)
// {
//     return java::JavaPopUp($F, $T, $W, $H);
// }


// // auth

// // authors.php

// function is_admin($xadmin)
// {
//     return authors::is_admin($xadmin);
// }


// function formatAidHeader($aid)
// {
//     return authors::formatAidHeader($aid);
// }

// // groupe.php

// function valid_group($user)
// {
//     return groupe::valid_group($user);
// }

// function liste_group()
// {
//     return groupe::liste_group();
// }

// function groupe_forum($forum_groupeX, $tab_groupeX)
// {
//     return groupe::groupe_forum($forum_groupeX, $tab_groupeX);
// }

// function groupe_autorisation($groupeX, $tab_groupeX)
// {
//     return groupe::groupe_autorisation($groupeX, $tab_groupeX);
// }

// function fab_espace_groupe($gr, $t_gr, $i_gr)
// {
//     return groupe::fab_espace_groupe($gr, $t_gr, $i_gr);
// }

// function fab_groupes_bloc($user, $im)
// {
//     return groupe::fab_groupes_bloc($user, $im);
// }

// // users.php

// function is_user($xuser)
// {
//     return users::is_user($xuser);
// }

// function getusrinfo($user)
// {
//     return users::getusrinfo($user);
// }

// function AutoReg()
// {
//     return users::AutoReg();
// }

// function getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms)
// {
//     return users::getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms);
// }

// function secur_static($sec_type)
// {
//     return users::secur_static($sec_type);
// }

// function autorisation($auto)
// {
//     return users::autorisation($auto);
// }

// function member_menu($mns, $qui)
// {
//     return users::member_menu($mns, $qui);
// }


// // blocks

// // block.php

// function block_fonction($title, $contentX)
// {
//     return block::block_fonction($title, $contentX);
// }

// function fab_block($title, $member, $content, $Xcache)
// {
//     return block::fab_block($title, $member, $content, $Xcache);
// }

// function leftblocks($moreclass)
// {
//     return block::leftblocks($moreclass);
// }

// function rightblocks($moreclass)
// {
//     return block::rightblocks($moreclass);
// }

// function oneblock($Xid, $Xblock)
// {
//     return block::oneblock($Xid, $Xblock);
// }

// function Pre_fab_block($Xid, $Xblock, $moreclass)
// {
//     return block::Pre_fab_block($Xid, $Xblock, $moreclass);
// }

// function niv_block($Xcontent)
// {
//     return block::niv_block($Xcontent);
// }

// function autorisation_block($Xcontent)
// {
//     return block::autorisation_block($Xcontent);
// }

// // boxe.php


function Site_Activ()
{
    return boxe::Site_Activ();
}

function online()
{
    return boxe::online();
}

function lnlbox()
{
    return boxe::lnlbox();
}

function searchbox()
{
    return boxe::searchbox();
}

function adminblock()
{
    return boxe::adminblock();
}

function mainblock()
{
    return boxe::mainblock();
}

function ephemblock()
{
    return boxe::ephemblock();
}

function loginbox()
{
    return boxe::loginbox();
}

function userblock()
{
    return boxe::userblock();
}

function topdownload()
{
    return boxe::topdownload();
}

function lastdownload()
{
    return boxe::lastdownload();
}

function oldNews($storynum, $typ_aff = '')
{
    return boxe::oldNews($storynum, $typ_aff);
}

function bigstory()
{
    return boxe::bigstory();
}

function category()
{
    return boxe::category();
}

function headlines($hid, $block)
{
    return boxe::headlines($hid, $block);
}

function bloc_rubrique()
{
    return boxe::bloc_rubrique();
}

function bloc_espace_groupe($gr, $i_gr)
{
    return boxe::bloc_espace_groupe($gr, $i_gr);
}

function bloc_groupes($im)
{
    return boxe::bloc_groupes($im);
}

function bloc_langue()
{
    return boxe::bloc_langue();
}

function blockSkin()
{
    return boxe::blockSkin();
}

function pollMain($pollID, $pollClose)
{
    return boxe::pollMain($pollID, $pollClose);
}

// // cache

// /// cache.php

function SC_infos()
{
    return cache::SC_infos();
}

function cacheManagerStart()
{
    return cache::cacheManagerStart();
}

function cacheManagerEnd()
{
    return cache::cacheManagerEnd();
}

// function Q_Select($Xquery, $retention)
// {
//     return cache::Q_Select($Xquery, $retention);
// }

// function PG_clean($request)
// {
//     return cache::PG_clean($request);
// }

// function Q_Clean()
// {
//     return cache::Q_Clean();
// }

// function SC_clean()
// {
//     return cache::SC_clean();
// }

// // cookie

// // cookie.php

// function cookiedecode($user)
// {
//     return cookie::cookiedecode($user);
// }


// // date

// // date.php

// function NightDay()
// {
//     return date::NightDay();
// }

// function formatTimestamp($time)
// {
//     return date::formatTimestamp($time);
// }

// function convertdateTOtimestamp($myrow)
// {
//     return date::convertdateTOtimestamp($myrow);
// }

// function post_convertdate($tmst)
// {
//     return date::post_convertdate($tmst);
// }

// function convertdate($myrow)
// {
//     return date::convertdate($myrow);
// }


// // http

// // response.php

// function file_contents_exist($url, $response_code)
// {
//     return response::file_contents_exist($url, $response_code);
// }

// // language

// // language.php

// function language_iso($l, $s, $c)
// {
//     return language::language_iso($l, $s, $c);
// }

// function languageList()
// {
//     return language::languageList();
// }

// function languageWhiteToCache($languageslist)
// {
//     return language::languageWhiteToCache($languageslist);
// }

// function languages()
// {
//     return language::languages();
// }

// function getLocale2()
// {
//     return language::getLocale2();
// }

// function getLocaleIso()
// {
//     return language::getLocaleIso();
// }


// function getLocale()
// {
//     return language::getLocale();
// }

// function aff_langue($ibid)
// {
//     return language::aff_langue($ibid);
// }

// function make_tab_langue()
// {
//     return language::make_tab_langue();
// }

// function aff_localzone_langue($ibid)
// {
//     return language::aff_localzone_langue($ibid);
// }

// function aff_local_langue($ibid_index, $ibid, $mess)
// {
//     return language::aff_local_langue($ibid_index, $ibid, $mess);
// }

// function preview_local_langue($local_user_language, $ibid)
// {
//     return language::preview_local_langue($local_user_language, $ibid);
// }

// function initLocale()
// {
//     return language::initLocale();
// }

// //metalang.php

// function arg_filter($arg)
// {
//     return metalang::arg_filter($arg);
// }

// function MM_img($ibid)
// {
//     return metalang::MM_img($ibid);
// }

// function charg($funct, $arguments)
// {
//     return metalang::charg($funct, $arguments);
// }

// function match_uri($racine, $R_uri)
// {
//     return metalang::match_uri($racine, $R_uri);
// }

// function charg_metalang()
// {
//     return metalang::charg_metalang();
// }

// function ana_args($arg)
// {
//     return metalang::ana_args($arg);
// }

// function meta_lang($Xcontent)
// {
//     return metalang::meta_lang($Xcontent);
// }

// // logs

// // logs.php

// function Ecr_Log($fic_log, $req_log, $mot_log)
// {
//     return logs::Ecr_Log($fic_log, $req_log, $mot_log);
// }

// // mail

// // mailler.php

// function send_email($email, $subject, $message, $from, $priority, $mime, $file)
// {
//     return mailler::send_email($email, $subject, $message, $from, $priority, $mime, $file);
// }

// function copy_to_email($to_userid, $sujet, $message)
// {
//     return mailler::copy_to_email($to_userid, $sujet, $message);
// }

// function Mess_Check_Mail($username)
// {
//     return mailler::Mess_Check_Mail($username);
// }

// function Mess_Check_Mail_interface($username, $class)
// {
//     return mailler::Mess_Check_Mail_interface($username, $class);
// }

// function Mess_Check_Mail_Sub($username, $class)
// {
//     return mailler::Mess_Check_Mail_Sub($username, $class);
// }

// function checkdnsmail($email)
// {
//     return mailler::checkdnsmail($email);
// }

// function isbadmailuser($utilisateur)
// {
//     return mailler::isbadmailuser($utilisateur);
// }

// function fakedmail($r)
// {
//     return mailler::fakedmail($r);
// }


// // news

// // news.php

// function ultramode()
// {
//     return news::ultramode();
// }

// function ctrl_aff($ihome, $catid)
// {
//     return news::ctrl_aff($ihome, $catid);
// }

// function news_aff($type_req, $sel, $storynum, $oldnum)
// {
//     return news::news_aff($type_req, $sel, $storynum, $oldnum);
// }

// function automatednews()
// {
//     return news::automatednews();
// }

// function aff_news($op, $catid, $marqeur)
// {
//     return news::aff_news($op, $catid, $marqeur);
// }

// function prepa_aff_news($op, $catid, $marqeur)
// {
//     return news::prepa_aff_news($op, $catid, $marqeur);
// }

// function getTopics($s_sid)
// {
//     return news::getTopics($s_sid);
// }


// // pixels

// // image.php

// function dataimagetofileurl($base_64_string, $output_path)
// {
//     return image::dataimagetofileurl($base_64_string, $output_path);
// }


// // routing

// // url.php

// function redirect_url($urlx)
// {
//     return url::redirect_url($urlx);
// }

// // security

// // hack.php

// function removeHack($Xstring)
// {
//     return hack::removeHack($Xstring);
// }

// // protect.php

// function url_protect($arr, $key) 
// {
//     return protect::url($arr, $key);
// }


// // session

// // session.php

// function session_manage()
// {
//     return session::session_manage();
// }


// // subcribe

// // subscribe.php

// function subscribe_mail($Xtype, $Xtopic, $Xforum, $Xresume, $Xsauf)
// {
//     return subscribe::subscribe_mail($Xtype, $Xtopic, $Xforum, $Xresume, $Xsauf);
// }

// function subscribe_query($Xuser, $Xtype, $Xclef)
// {
//     return subscribe::subscribe_query($Xuser, $Xtype, $Xclef);
// }

// // support

// // counter.php

// function counterUpadate()
// {
//     return counter::counterUpadate();
// }

// // download.php

// function topdownload_data($form, $ordre)
// {
//     return download::topdownload_data($form, $ordre);
// }


// // editeur.php

// function aff_editeur($Xzone, $Xactiv)
// {
//     return editeur::aff_editeur($Xzone, $Xactiv);
// }


// // edito.php

// function fab_edito()
// {
//     return edito::fab_edito();
// }

// function aff_edito()
// {
//     return edito::aff_edito();
// }

// // online.php

// function Who_Online()
// {
//     return online::Who_Online();
// }

// function Who_Online_Sub()
// {
//     return online::Who_Online_Sub();
// }

// function Site_Load()
// {
//     return online::Site_Load();
// }

// function online_members()
// {
//     return online::online_members();  
// }


// // polls.php

// function pollSecur($pollID)
// {
//     return polls::pollSecur($pollID);
// }

// function PollNewest(?int $id = null)
// {
//     return polls::PollNewest($id);
// }


// // referer.php

// function refererUpdate()
// {
//     return referer::refererUpdate();
// }


// // stat.php

// function req_stat()
// {
//     return stats::req_stat();
// }


// // str.php

// function conv2br($txt)
// {
//     return str::conv2br($txt);
// }

// function hexfromchr($txt)
// {
//     return str::hexfromchr($txt);
// }

// function wrh($ibid)
// {
//     return str::wrh($ibid);
// }

// function split_string_without_space($msg, $split)
// {
//     return str::split_string_without_space($msg, $split);
// }

// function wrapper_f($string, $key, $cols)
// {
//     return str::wrapper_f($string, $key, $cols);
// }

// function changetoamp($r)
// {
//     return str::changetoamp($r);
// }

// function changetoampadm($r)
// {
//     return str::changetoampadm($r);
// }

// function utf8_java($ibid)
// {
//     return str::utf8_java($ibid);
// }

// function FixQuotes($what)
// {
//     return str::FixQuotes($what);
// }

// function addslashes_GPC(&$arr) {
//     return str::addslashes_GPC($arr);
// }


// // .php

// function theme_image($theme_img)
// {
//     return theme::theme_image($theme_img);
// }

// function getSkin()
// {
//     return theme::getSkin();
// }

// function getTheme()
// {
//     return theme::getTheme();
// }

// function themeLists(?bool $implode = true, ?string $separator = ' ')
// {
//     return theme::themeLists($implode, $separator);
// }

// function themepreview($title, $hometext, $bodytext, $notes)
// {
//     return theme::themepreview($title, $hometext, $bodytext, $notes);
// }

// // utility

// // code.php

// function change_cod($r)
// {
//     return code::change_cod($r);
// }

// function af_cod($ibid)
// {
//     return code::af_cod($ibid);
// }

// function desaf_cod($ibid)
// {
//     return code::desaf_cod($ibid);
// }

// function aff_code($ibid)
// {
//     return code::aff_code($ibid);
// }


// // crypt.php

// function keyED($txt, $encrypt_key)
// {
//     return crypt::keyED($txt, $encrypt_key);
// }

// function encrypt($txt)
// {
//     return crypt::encrypt($txt);
// }

// function encryptK($txt, $C_key)
// {
//     return crypt::encryptK($txt, $C_key);
// }

// function decrypt($txt)
// {
//     return crypt::decrypt($txt);
// }

// function decryptK($txt, $C_key)
// {
//     return crypt::decryptK($txt, $C_key);
// }

// // spam.php

// function preg_anti_spam($ibid)
// {
//     return spam::preg_anti_spam($ibid);
// }

// function anti_spam($str, $highcode)
// {
//     return spam::anti_spam($str, $highcode);
// }

// function Q_spambot()
// {
//     return spam::Q_spambot();
// }

// function L_spambot($ip, $status)
// {
//     return spam::L_spambot($ip, $status);
// }

// function R_spambot($asb_question, $asb_reponse, $message)
// {
//     return spam::R_spambot($asb_question, $asb_reponse, $message);
// }

// // debug

function vd() {
    array_map(function($value)
    {
       echo '<pre>'.var_dump($value).'</pre>';
    }, func_get_args());
}

function dd() {
    array_map(function($value)
    {
       echo '<pre>'.var_dump($value).'</pre>';
    }, func_get_args());
    die();
}

// // chat

// function if_chat($pour) {
//    return  chat::if_chat($pour);
// }

// function insertChat($username, $message, $dbname, $id) {
//    return  chat::insertChat($username, $message, $dbname, $id);
// }

// function makeChatBox($pour) {
//    return  chat::makeChatBox($pour);
// }

// // messenger

// function Form_instant_message($to_userid) {
//    return  messenger::Form_instant_message($to_userid);
// }

// function writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie) {
//    return  messenger::writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie);
// }

// function write_short_private_message($to_userid) {
//    return  messenger::write_short_private_message($to_userid);
// }

// function instant_members_message() {
//    return  messenger::instant_members_message();
// }


// // forum


// function RecentForumPosts($title, $maxforums, $maxtopics, $displayposter = false, $topicmaxchars = 15, $hr = false, $decoration = '') {
//    return forum::RecentForumPosts($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration);
// }

// function RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration) {
//    return forum::RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration);
// }

// function get_total_topics($forum_id) 
// {
//    return forum::get_total_topics($forum_id);
// }

// function get_contributeurs($fid, $tid) {
//     return forum::get_contributeurs($fid, $tid);
// }

// function get_total_posts($fid, $tid, $type, $Mmod) 
// {
//     return forum::get_total_posts($fid, $tid, $type, $Mmod);
// }

// function get_last_post($id, $type, $cmd, $Mmod) 
// {
//     return forum::get_last_post($id, $type, $cmd, $Mmod);
// }

// function get_moderator($user_id) 
// {
//     return forum::get_moderator($user_id);
// }

// function user_is_moderator($uidX, $passwordX, $forum_accessX) 
// {
//     return forum::user_is_moderator($uidX, $passwordX, $forum_accessX);
// }

// function get_userdata_from_id($userid) 
// {
//     return forum::get_userdata_from_id($userid);
// }

// function get_userdata_extend_from_id($userid) 
// {
//     return forum::get_userdata_extend_from_id($userid);
// }

// function get_userdata($username) 
// {
//     return forum::get_userdata($username);
//  }

// function  does_exists($id, $type)
// {
//     return forum::does_exists($id, $type);
// }

// function is_locked($topic) 
// {
//     return forum::is_locked($topic);
// }

// function smilie($message) 
// {
//     return forum::smilie($message);
// }

// function smile($message) 
// {
//     return forum::smile($message);
// }

// function aff_video_yt($ibid) 
// {
//     return forum::aff_video_yt($ibid);
// }

// function putitems_more() 
// {
//     return forum::putitems_more();
// }

// function putitems($targetarea) 
// {
//     return forum::putitems($targetarea);
// }

// function HTML_Add() 
// {
//     return forum::HTML_Add();
// }

// function emotion_add($image_subject) 
// {
//     return forum::emotion_add($image_subject);
// }

// function make_clickable($text) 
// {
//     return forum::make_clickable($text);
// }

// function undo_htmlspecialchars($input) 
// {
//     return forum::undo_htmlspecialchars($input);
// }

// function searchblock() {
//     return forum::searchblock();
// }

// function member_qualif($poster, $posts, $rank) 
// {
//     return forum::member_qualif($poster, $posts, $rank);
// }

// function forumerror($e_code) 
// {
//     return forum::forumerror($e_code);
// }

// function control_efface_post($apli, $post_id, $topic_id, $IdForum) 
// {
//     return forum::control_efface_post($apli, $post_id, $topic_id, $IdForum);
// }

// function autorize() 
// {
//     return forum::autorize();
// }

// function anti_flood($modoX, $paramAFX, $poster_ipX, $userdataX, $gmtX) 
// {
//     return forum::anti_flood($modoX, $paramAFX, $poster_ipX, $userdataX, $gmtX);
// }

// function forum($rowQ1) 
// {
//     return forum::forum($rowQ1);
// }

// function sub_forum_folder($forum) 
// {
//     return forum::sub_forum_folder($forum);
// }


// // .php

// // function  {
// //    return  ::;
// // }

//metalang function database 


function MM_Scalcul($opex, $premier, $deuxieme)
{
    if ($opex == "+") {
        $tmp = $premier + $deuxieme;
    }
    
    if ($opex == "-") {
        $tmp = $premier - $deuxieme;
    }

    if ($opex == "*") {
        $tmp = $premier * $deuxieme;
    }
    
    if ($opex == "/") {
        if ($deuxieme == 0) {
            $tmp = "Division by zero !";
        } else {
            $tmp = $premier / $deuxieme;
        }
    }

    return $tmp;
}

function MM_anti_spam($arg)
{
    return ("<a href=\"mailto:" . spam::anti_spam($arg, 1) . "\" target=\"_blank\">" . spam::anti_spam($arg, 0) . "</a>");
}

function MM_msg_foot()
{
    global $foot1, $foot2, $foot3, $foot4;

    if ($foot1) {
        $MT_foot = stripslashes($foot1) . "<br />";
    }

    if ($foot2) {
        $MT_foot .= stripslashes($foot2) . "<br />";
    }

    if ($foot3) {
        $MT_foot .= stripslashes($foot3) . "<br />";
    }

    if ($foot4) {
        $MT_foot .= stripslashes($foot4);
    }

    return language::aff_langue($MT_foot);
}

function MM_date()
{
    $locale = language::getLocale();

    return ucfirst(htmlentities(\PHP81_BC\strftime(translate("daydate"), time(), $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));
}

function MM_banner()
{
    global $banners, $hlpfile;

    if (($banners) and (!$hlpfile)) {
        ob_start();
            include("banners.php");
            $MT_banner = ob_get_contents();
        ob_end_clean();
    } else {
        $MT_banner = "";
    }

    return $MT_banner;
}

function MM_search_topics()
{
    global $NPDS_Prefix;

    $MT_search_topics = "<form action=\"search.php\" method=\"post\"><label class=\"col-form-label\">" . translate("Sujets") . " </label>";
    $MT_search_topics .= "<select class=\"form-select\" name=\"topic\"onChange='submit()'>";
    $MT_search_topics .= "<option value=\"\">" . translate("Tous les sujets") . "</option>\n";

    $rowQ = cache::Q_select("select topicid, topictext from " . $NPDS_Prefix . "topics order by topictext", 86400);
    
    foreach ($rowQ as $myrow) {
        $MT_search_topics .= "<option value=\"" . $myrow['topicid'] . "\">" . language::aff_langue($myrow['topictext']) . "</option>\n";
    }

    $MT_search_topics .= "</select></form>";

    return $MT_search_topics;
}

function MM_search()
{
    $MT_search = "<form action=\"search.php\" method=\"post\"><label>" . translate("Recherche") . "</label>
    <input class=\"form-control\" type=\"text\" name=\"query\" size=\"10\"></form>";

    return $MT_search;
}

function MM_member()
{
    global $cookie, $anonymous;

    $username = $cookie[1];

    if ($username == "") {
        $username = $anonymous;
    }

    ob_start();
        mailler::Mess_Check_Mail($username);
        $MT_member = ob_get_contents();
    ob_end_clean();

    return $MT_member;
}

function MM_nb_online()
{
    list($MT_nb_online, $MT_whoim) = online::Who_Online();

    return $MT_nb_online;
}

function MM_whoim()
{
    list($MT_nb_online, $MT_whoim) = online::Who_Online();

    return $MT_whoim;
}

function MM_membre_nom()
{
    global $NPDS_Prefix, $cookie;

    if (isset($cookie[1])) {

        $uname = metalang::arg_filter($cookie[1]);
        $MT_name = "";

        $rowQ = cache::Q_select("SELECT name FROM " . $NPDS_Prefix . "users WHERE uname='$uname'", 3600);
        $myrow = $rowQ[0];

        $MT_name = $myrow['name'];

        return $MT_name;
    }
}

function MM_membre_pseudo()
{
    global $cookie;

    return $cookie[1];
}

function MM_blocID($arg)
{
    return @block::oneblock(substr($arg, 1), substr($arg, 0, 1) . "B");
}

function MM_block($arg)
{
    return metalang::meta_lang("blocID($arg)");
}

function MM_leftblocs($arg)
{
    ob_start();
        block::leftblocks($arg);
        $M_Lblocs = ob_get_contents();
    ob_end_clean();

    return $M_Lblocs;
}

function MM_rightblocs($arg)
{
    ob_start();
        block::rightblocks($arg);
        $M_Lblocs = ob_get_contents();
    ob_end_clean();

    return $M_Lblocs;
}

function MM_article($arg)
{
    return metalang::meta_lang("articleID($arg)");
}

function MM_articleID($arg)
{
    global $NPDS_Prefix, $nuke_url;

    $arg = metalang::arg_filter($arg);
    
    $rowQ = cache::Q_select("SELECT title FROM " . $NPDS_Prefix . "stories WHERE sid='$arg'", 3600);
    $myrow = $rowQ[0];

    return "<a href=\"$nuke_url/article.php?sid=$arg\">" . $myrow['title'] . "</a>";
}

function MM_article_completID($arg)
{
    if ($arg > 0) {
        $story_limit = 1;
        $news_tab = news::prepa_aff_news("article", $arg, "");
    } else {
        $news_tab = news::prepa_aff_news("index", "", "");
        $story_limit = abs($arg) + 1;
    }

    $aid = unserialize($news_tab[$story_limit]['aid']);
    $informant = unserialize($news_tab[$story_limit]['informant']);
    $datetime = unserialize($news_tab[$story_limit]['datetime']);
    $title = unserialize($news_tab[$story_limit]['title']);
    $counter = unserialize($news_tab[$story_limit]['counter']);
    $topic = unserialize($news_tab[$story_limit]['topic']);
    $hometext = unserialize($news_tab[$story_limit]['hometext']);
    $notes = unserialize($news_tab[$story_limit]['notes']);
    $morelink = unserialize($news_tab[$story_limit]['morelink']);
    $topicname = unserialize($news_tab[$story_limit]['topicname']);
    $topicimage = unserialize($news_tab[$story_limit]['topicimage']);
    $topictext = unserialize($news_tab[$story_limit]['topictext']);
    $s_id = unserialize($news_tab[$story_limit]['id']);
    
    if ($aid) {
        ob_start();
            themeindex($aid, $informant, $datetime, $title, $counter, $topic, $hometext, $notes, $morelink, $topicname, $topicimage, $topictext, $s_id);
            $remp = ob_get_contents();
        ob_end_clean();
    } else {
        $remp = "";
    }

    return $remp;
}

function MM_article_complet($arg)
{
    return metalang::meta_lang("article_completID($arg)");
}

function MM_headlineID($arg)
{
    return @boxe::headlines($arg, "");
}

function MM_headline($arg)
{
    return metalang::meta_lang("headlineID($arg)");
}

function MM_list_mns()
{
    global $NPDS_Prefix;

    $query = sql_query("SELECT uname FROM " . $NPDS_Prefix . "users WHERE mns='1'");

    $MT_mns = "<ul class=\"list-group list-group-flush\">";
   
    while (list($uname) = sql_fetch_row($query)) {
        $MT_mns .= "<li class=\"list-group-item\"><a href=\"minisite.php?op=$uname\" target=\"_blank\">$uname</a></li>";
    }

    $MT_mns .= "</ul>";

    return $MT_mns;
}

function MM_LastMember()
{
    global $NPDS_Prefix;

    $query = sql_query("SELECT uname FROM " . $NPDS_Prefix . "users ORDER BY uid DESC LIMIT 0,1");
    $result = sql_fetch_row($query);

    return $result[0];
}

function MM_edito()
{
    list($affich, $M_edito) = edito::fab_edito();

    if ((!$affich) or ($M_edito == "")) {
        $M_edito = "";
    }

    return $M_edito;
}

function MM_groupe_text($arg)
{
    global $user;

    $affich = false;
    $remp = "";

    if ($arg != "") {
        if (groupe::groupe_autorisation($arg, groupe::valid_group($user))) {
            $affich = true;
        }
    } else {
        if ($user) {
            $affich = true;
        }
    }

    if (!$affich) {
        $remp = "!delete!";
    }

    return $remp;
}

function MM_no_groupe_text($arg)
{
    global $user;

    $affich = true;
    $remp = "";

    if ($arg != "") {
        if (groupe::groupe_autorisation($arg, groupe::valid_group($user))) {
            $affich = false;
        }
        
        if (!$user) {
            $affich = false;
        }
    } else {
        if ($user) {
            $affich = false;
        }
    }

    if (!$affich) {
        $remp = "!delete!";
    }

    return $remp;
}

function MM_note()
{
    return ("!delete!");
}

function MM_note_admin()
{
    global $admin;

    if (!$admin) {
        return "!delete!";
    } else {
        return "<b>nota</b> : ";
    }
}

function MM_debugON()
{
    global $NPDS_debug, $NPDS_debug_str, $NPDS_debug_time, $NPDS_debug_cycle;

    $NPDS_debug_cycle = 1;
    $NPDS_debug = true;
    $NPDS_debug_str = "<br />";
    $NPDS_debug_time = microtime(true);

    return "";
}

function MM_debugOFF()
{
    global $NPDS_debug, $NPDS_debug_str, $NPDS_debug_time, $NPDS_debug_cycle;

    $time_end = microtime(true);
    $NPDS_debug_str .= "=> !DebugOFF!<br /><b>=> exec time for meta-lang : " . round($time_end - $NPDS_debug_time, 4) . " / cycle(s) : $NPDS_debug_cycle</b><br />";
    $NPDS_debug = false;

    echo $NPDS_debug_str;

    return "";
}

function MM_forum_all()
{
    global $NPDS_Prefix;

    $rowQ1 = cache::Q_Select("SELECT * FROM " . $NPDS_Prefix . "catagories ORDER BY cat_id", 3600);

    $Xcontent = @forum::forum($rowQ1);

    return $Xcontent;
}

function MM_forum_categorie($arg)
{
    global $NPDS_Prefix;

    $arg = metalang::arg_filter($arg);
    $bid_tab = explode(",", $arg);
    $sql = "";

    foreach ($bid_tab as $cat) {
        $sql .= "cat_id='$cat' OR ";
    }

    $sql = substr($sql, 0, -4);
    $rowQ1 = cache::Q_Select("SELECT * FROM " . $NPDS_Prefix . "catagories WHERE $sql", 3600);

    $Xcontent = @forum::forum($rowQ1);

    return $Xcontent;
}

function MM_forum_message()
{
    global $subscribe, $user;

    $ibid = "";

    if (!$user) {
        $ibid = translate("Devenez membre et vous disposerez de fonctions spécifiques : abonnements, forums spéciaux (cachés, membres, ..), statut de lecture, ...");
    }

    if (($subscribe) and ($user)) {
        $ibid = translate("Cochez un forum et cliquez sur le bouton pour recevoir un Email lors d'une nouvelle soumission dans celui-ci.");
    }

    return $ibid;
}

function MM_forum_recherche()
{
    $Xcontent = @forum::searchblock();

    return $Xcontent;
}

function MM_forum_icones()
{
    if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
        $imgtmpR = $ibid;
    } else {
        $imgtmpR = "images/forum/icons/red_folder.gif";
    }

    if ($ibid = theme::theme_image("forum/icons/folder.gif")) {
        $imgtmp = $ibid;
    } else {
        $imgtmp = "images/forum/icons/folder.gif";
    }

    $ibid = "<img src=\"$imgtmpR\" border=\"\" alt=\"\" /> = " . translate("Les nouvelles contributions depuis votre dernière visite.") . "<br />";
    $ibid .= "<img src=\"$imgtmp\" border=\"\" alt=\"\" /> = " . translate("Aucune nouvelle contribution depuis votre dernière visite.");
    
    return $ibid;
}

function MM_forum_subscribeON()
{
    global $subscribe, $user;

    $ibid = "";
    
    if (($subscribe) and ($user)) {
        $userX = base64_decode($user);
        $userR = explode(':', $userX);
        
        if (mailler::isbadmailuser($userR[0]) === false) {
            $ibid = "<form action=\"forum.php\" method=\"post\">
            <input type=\"hidden\" name=\"op\" value=\"maj_subscribe\" />";
        }
    }

    return $ibid;
}

function MM_forum_bouton_subscribe()
{
    global $subscribe, $user;

    if (($subscribe) and ($user)) {
        $userX = base64_decode($user);
        $userR = explode(':', $userX);
        
        if (mailler::isbadmailuser($userR[0]) === false) {
            return '<input class="btn btn-secondary" type="submit" name="Xsub" value="' . translate("OK") . '" />';
        }
    } else {
        return '';
    }
}

function MM_forum_subscribeOFF()
{
    global $subscribe, $user;

    $ibid = "";

    if (($subscribe) and ($user)) {
        $userX = base64_decode($user);
        $userR = explode(':', $userX);
        
        if (mailler::isbadmailuser($userR[0]) === false) {
            $ibid = "</form>";
        }
    }

    return $ibid;
}

function MM_forum_subfolder($arg)
{
    $forum = metalang::arg_filter($arg);
    $content = forum::sub_forum_folder($forum);

    return $content;
}


function MM_insert_flash($name, $width, $height, $bgcol)
{
    return ("<object codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflas
    classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\"
    h.cab#version=6,0,0,0\" width=\"" . $width . "\"
    height=\"" . $height . "\"
    id=\"" . $name . "\" align=\"middle\">
  
    <param name=\"allowScriptAccess\"
    value=\"sameDomain\" />
  
    <param name=\"movie\"
    value=\"flash/" . $name . "\" />
  
    <param name=\"quality\" value=\"high\" />
    <param name=\"bgcolor\"
    value=\"" . $bgcol . "\" />
 
    <embed src=\"flash/" . $name . "\"
    quality=\"high\" bgcolor=\"" . $bgcol . "\"
    width=\"" . $width . "\"
    height=\"" . $height . "\"
    name=\"" . $name . "\" align=\"middle\"
    allowScriptAccess=\"sameDomain\"
    type=\"application/x-shockwave-flash\"
    pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
 
    </object>");
}

function MM_login()
{
    global $user;

    $boxstuff = '
    <div class="card card-body m-3">
       <h5><a href="user.php?op=only_newuser" role="button" title="' . translate("Nouveau membre") . '"><i class="fa fa-user-plus"></i>&nbsp;' . translate("Nouveau membre") . '</a></h5>
    </div>
    <div class="card card-body m-3">
       <h5 class="mb-3"><i class="fas fa-sign-in-alt fa-lg"></i>&nbsp;' . translate("Connexion") . '</h5>
       <form action="user.php" method="post" name="userlogin_b">
          <div class="row g-2">
             <div class="col-12">
                <div class="mb-3 form-floating">
                   <input type="text" class="form-control" name="uname" id="inputuser_b" placeholder="' . translate("Identifiant") . '" required="required" />            
                   <label for="inputuser_b" >' . translate("Identifiant") . '</label>
               </div>
            </div>
            <div class="col-12">
               <div class="mb-0 form-floating">
                  <input type="password" class="form-control" name="pass" id="inputPassuser_b" placeholder="' . translate("Mot de passe") . '" required="required" />
                  <label for="inputPassuser_b">' . translate("Mot de passe") . '</label>
                  <span class="help-block small"><a href="user.php?op=forgetpassword" role="button" title="' . translate("Vous avez perdu votre mot de passe ?") . '">' . translate("Vous avez perdu votre mot de passe ?") . '</a></span>
                </div>
             </div>
          </div>
          <input type="hidden" name="op" value="login" />
          <div class="mb-3 row">
             <div class="ms-sm-auto">
                <button class="btn btn-primary" type="submit" title="' . translate("Valider") . '">' . translate("Valider") . '</button>
             </div>
          </div>
       </form>
    </div>';

    if (isset($user)) {
        $boxstuff = '<h5><a class="text-danger" href="user.php?op=logout"><i class="fas fa-sign-out-alt fa-lg align-middle text-danger me-2"></i>' . translate("Déconnexion") . '</a></h5>';
    }

    return $boxstuff;
}

function MM_administration()
{
    global $admin;

    if ($admin) {
        return "<a href=\"admin.php\">" . translate("Outils administrateur") . "</a>";
    } else {
        return "";
    }
}

function MM_admin_infos($arg)
{
    global $NPDS_Prefix;

    $arg = metalang::arg_filter($arg);
    $rowQ1 = cache::Q_select("SELECT url, email FROM " . $NPDS_Prefix . "authors WHERE aid='$arg'", 86400);

    $myrow = $rowQ1[0];
    
    if ($myrow['url'] != '') {
        $auteur = "<a href=\"" . $myrow['url'] . "\">$arg</a>";
    } elseif ($myrow['email'] != '') {
        $auteur = "<a href=\"mailto:" . $myrow['email'] . "\">$arg</a>";
    } else {
        $auteur = $arg;
    }

    return $auteur;
}

function MM_theme_img($arg)
{
    return metalang::MM_img($arg);
}

function MM_rotate_img($arg)
{
    mt_srand((float) microtime() * 1000000);
    
    $arg = metalang::arg_filter($arg);
    $tab_img = explode(",", $arg);

    if (count($tab_img) > 1) {
        $imgnum = mt_rand(0, count($tab_img) - 1);
    } else if (count($tab_img) == 1) {
        $imgnum = 0;
    } else {
        $imgnum = -1;
    }

    if ($imgnum != -1) {
        $Xcontent = "<img src=\"" . $tab_img[$imgnum] . "\" border=\"0\" alt=\"" . $tab_img[$imgnum] . "\" title=\"" . $tab_img[$imgnum] . "\" />";
    }

    return $Xcontent;
}

function MM_sql_nbREQ()
{
    global $sql_nbREQ;

    return "SQL REQ : $sql_nbREQ";
}

function MM_top_stories($arg)
{
    $content = '';
    $arg = metalang::arg_filter($arg);

    $xtab = news::news_aff("libre", "ORDER BY counter DESC LIMIT 0, " . $arg * 2, 0, $arg * 2);

    $story_limit = 0;
    
    while (($story_limit < $arg) and ($story_limit < sizeof($xtab))) {
        list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter) = $xtab[$story_limit];
        $story_limit++;
        
        if ($counter > 0) {
            $content .= '<li class="ms-4 my-1"><a href="article.php?sid=' . $sid . '" >' . language::aff_langue($title) . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($counter) . ' ' . translate("Fois") . '</span></li>';
        }
    }

    return $content;
}

function MM_comment_system($file_name, $topic)
{
    global $NPDS_Prefix, $anonpost, $moderate, $admin, $user;

    ob_start();
        
        if (file_exists("modules/comments/$file_name.conf.php")) {
            include("modules/comments/$file_name.conf.php");
            include("modules/comments/comments.php");
        }

        $output = ob_get_contents();
    ob_end_clean();

    return $output;
}

function MM_top_commented_stories($arg)
{
    $content = '';
    $arg = metalang::arg_filter($arg);
    
    $xtab = news::news_aff("libre", "ORDER BY comments DESC  LIMIT 0, " . $arg * 2, 0, $arg * 2);
    
    $story_limit = 0;
    
    while (($story_limit < $arg) and ($story_limit < sizeof($xtab))) {
        list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments) = $xtab[$story_limit];
        $story_limit++;
        
        if ($comments > 0) {
            $content .= '<li class="ms-4 my-1"><a href="article.php?sid=' . $sid . '" >' . language::aff_langue($title) . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($comments) . '</span></li>';
        }
    }

    return $content;
}

function MM_top_categories($arg)
{
    global $NPDS_Prefix;

    $content = '';
    $arg = metalang::arg_filter($arg);

    $result = sql_query("SELECT catid, title, counter FROM " . $NPDS_Prefix . "stories_cat order by counter DESC limit 0,$arg");
    while (list($catid, $title, $counter) = sql_fetch_row($result)) {
        if ($counter > 0) {
            $content .= '<li class="ms-4 my-1"><a href="index.php?op=newindex&amp;catid=' . $catid . '" >' . language::aff_langue($title) . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($counter) . '</span></li>';
        }
    }

    sql_free_result($result);

    return $content;
}

function MM_top_sections($arg)
{
    global $NPDS_Prefix;

    $content = '';
    $arg = metalang::arg_filter($arg);

    $result = sql_query("SELECT artid, title, counter FROM " . $NPDS_Prefix . "seccont ORDER BY counter DESC LIMIT 0,$arg");
    while (list($artid, $title, $counter) = sql_fetch_row($result)) {
        $content .= '<li class="ms-4 my-1"><a href="sections.php?op=viewarticle&amp;artid=' . $artid . '" >' . aff_langue($title) . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($counter) . ' ' . translate("Fois") . '</span></li>';
    }

    sql_free_result($result);

    return $content;
}

function MM_top_reviews($arg)
{
    global $NPDS_Prefix;

    $content = '';
    $arg = metalang::arg_filter($arg);

    $result = sql_query("SELECT id, title, hits FROM " . $NPDS_Prefix . "reviews ORDER BY hits DESC LIMIT 0,$arg");
    
    while (list($id, $title, $hits) = sql_fetch_row($result)) {
        if ($hits > 0) {
            $content .= '<li class="ms-4 my-1"><a href="reviews.php?op=showcontent&amp;id=' . $id . '" >' . $title . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($hits) . ' ' . translate("Fois") . '</span></li>';
        }
    }
    sql_free_result($result);

    return $content;
}

function MM_top_authors($arg)
{
    global $NPDS_Prefix;

    $content = '';
    $arg = metalang::arg_filter($arg);

    $result = sql_query("SELECT aid, counter FROM " . $NPDS_Prefix . "authors ORDER BY counter DESC LIMIT 0,$arg");
    
    while (list($aid, $counter) = sql_fetch_row($result)) {
        if ($counter > 0) {
            $content .= '<li class="ms-4 my-1"><a href="search.php?query=&amp;author=' . $aid . '" >' . $aid . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($counter) . '</span></li>';
        }
    }

    sql_free_result($result);

    return $content;
}

function MM_top_polls($arg)
{
    global $NPDS_Prefix;

    $content = '';
    $arg = metalang::arg_filter($arg);

    $result = sql_query("SELECT pollID, pollTitle, voters FROM " . $NPDS_Prefix . "poll_desc ORDER BY voters DESC LIMIT 0,$arg");
    while (list($pollID, $pollTitle, $voters) = sql_fetch_row($result)) {
        
        if ($voters > 0) {
            $content .= '<li class="ms-4 my-1"><a href="pollBooth.php?op=results&amp;pollID=' . $pollID . '" >' . language::aff_langue($pollTitle) . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($voters) . '</span></li>';
        }
    }

    sql_free_result($result);

    return $content;
}


function MM_top_storie_authors($arg)
{
    global $NPDS_Prefix;

    $content = '';
    $arg = metalang::arg_filter($arg);

    $result = sql_query("SELECT uname, counter FROM " . $NPDS_Prefix . "users ORDER BY counter DESC LIMIT 0,$arg");
    while (list($uname, $counter) = sql_fetch_row($result)) {
        
        if ($counter > 0) {
            $content .= '<li class="ms-4 my-1"><a href="user.php?op=userinfo&amp;uname=' . $uname . '" >' . $uname . '</a>&nbsp;<span class="badge bg-secondary float-end">' . str::wrh($counter) . '</span></li>';
        }
    }

    sql_free_result($result);

    return $content;
}

function MM_topic_all()
{
    global $NPDS_Prefix, $tipath;

    $aff = '';
    $aff = '<div class="">';

    $result = sql_query("SELECT topicid, topicname, topicimage, topictext FROM " . $NPDS_Prefix . "topics ORDER BY topicname");
    
    while (list($topicid, $topicname, $topicimage, $topictext) = sql_fetch_row($result)) {
        $resultn = sql_query("SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "stories WHERE topic='$topicid'");
        $total_news = sql_fetch_assoc($resultn);
        
        $aff .= '
           <div class="col-sm-6 col-lg-4 mb-2 griditem px-2">
              <div class="card my-2">';
        
        if ((($topicimage) or ($topicimage != '')) and (file_exists("$tipath$topicimage"))) {
            $aff .= '<img class="mt-3 ms-3 n-sujetsize" src="' . $tipath . $topicimage . '" alt="topic_icon" />';
        }

        $aff .= '<div class="card-body">';
        
        if ($total_news['total'] != '0') {
            $aff .= '<a href="index.php?op=newtopic&amp;topic=' . $topicid . '"><h4 class="card-title">' . language::aff_langue($topicname) . '</h4></a>';
        } else {
            $aff .= '<h4 class="card-title">' . language::aff_langue($topicname) . '</h4>';
        }

        $aff .= '<p class="card-text">' . language::aff_langue($topictext) . '</p>
                    <p class="card-text text-end"><span class="small">' . translate("Nb. d'articles") . '</span> <span class="badge bg-secondary">' . $total_news['total'] . '</span></p>
                 </div>
              </div>
           </div>';
    }

    $aff .= '</div>';

    sql_free_result($result);

    return $aff;
}

function MM_topic_subscribeOFF()
{
    $aff = '<div class="mb-3 row"><input type="hidden" name="op" value="maj_subscribe" />';
    $aff .= '<button class="btn btn-primary ms-3" type="submit" name="ok">' . translate("Valider") . '</button>';
    $aff .= '</div></fieldset></form>';

    return $aff;
}

function MM_topic_subscribeON()
{
    global $subscribe, $user, $cookie;
    
    if ($subscribe and $user) {
        if (mailler::isbadmailuser($cookie[0]) === false) {
            return ('<form action="topics.php" method="post"><fieldset>');
        }
    }
}

function MM_topic_subscribe($arg)
{
    global $NPDS_Prefix, $subscribe, $user, $cookie;

    $segment = metalang::arg_filter($arg);
    $aff = '';
    
    if ($subscribe) {
        if ($user) {
            $aff = '
              <div class="mb-3 row">';
            
            $result = sql_query("SELECT topicid, topictext, topicname FROM " . $NPDS_Prefix . "topics ORDER BY topicname");
            
              while (list($topicid, $topictext, $topicname) = sql_fetch_row($result)) {
                $resultX = sql_query("SELECT topicid FROM " . $NPDS_Prefix . "subscribe WHERE uid='$cookie[0]' AND topicid='$topicid'");
                
                if (sql_num_rows($resultX) == "1") {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }

                $aff .= '
                    <div class="' . $segment . '">
                       <div class="form-check">
                          <input type="checkbox" class="form-check-input" name="Subtopicid[' . $topicid . ']" id="subtopicid' . $topicid . '" ' . $checked . ' />
                          <label class="form-check-label" for="subtopicid' . $topicid . '">' . language::aff_langue($topicname) . '</label>
                       </div>
                    </div>';
            }

            $aff .= '</div>';
            sql_free_result($result);
        }
    }

    return $aff;
}

function MM_yt_video($id_yt_video)
{
    $content = '';
    $id_yt_video = metalang::arg_filter($id_yt_video);
    
    if (!defined('CITRON')) {
        $content .= '
           <div class="ratio ratio-16x9">
              <iframe src="https://www.youtube.com/embed/' . $id_yt_video . '" allowfullscreen="" frameborder="0"></iframe>
           </div>';
    } else {
        $content .= '<div class="youtube_player" videoID="' . $id_yt_video . '"></div>';
    }

    return $content;
}

function MM_espace_groupe($gr, $t_gr, $i_gr)
{
    $gr = metalang::arg_filter($gr);
    $t_gr = metalang::arg_filter($t_gr);
    $i_gr = metalang::arg_filter($i_gr);

    return groupe::fab_espace_groupe($gr, $t_gr, $i_gr);
}

function MM_blocnote($arg)
{
    global $REQUEST_URI;

    if (!stristr($REQUEST_URI, "admin.php")) {
        return @block::oneblock($arg, "RB");
    } else {
        return "";
    }
}

function MM_forumP()
{
    global $NPDS_Prefix, $cookie, $user;

    /*Sujet chaud*/
    $hot_threshold = 10;

    /*Nbre posts a afficher*/
    $maxcount = "15";

    $MM_forumP = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
        . '<tr align="center" class="ligna">'
        . '<th width="5%">' . language::aff_langue('[french]Etat[/french][english]State[/english]') . '</th>'
        . '<th width="20%">' . language::aff_langue('[french]Forum[/french][english]Forum[/english]') . '</th>'
        . '<th width="30%">' . language::aff_langue('[french]Sujet[/french][english]Topic[/english]') . '</th>'
        . '<th width="5%">' . language::aff_langue('[french]RÃ©ponse[/french][english]Replie[/english]') . '</th>'
        . '<th width="20%">' . language::aff_langue('[french]Dernier Auteur[/french][english]Last author[/english]') . '</th>'
        . '<th width="20%">' . language::aff_langue('[french]Date[/french][english]Date[/english]') . '</th>'
        . '</tr>';

    /*Requete liste dernier post*/
    $result = sql_query("SELECT MAX(post_id) FROM " . $NPDS_Prefix . "posts WHERE forum_id > 0 GROUP BY topic_id ORDER BY MAX(post_id) DESC LIMIT 0,$maxcount");
    while (list($post_id) = sql_fetch_row($result)) {

        /*Requete detail dernier post*/
        $res = sql_query("SELECT 
                      us.topic_id, us.forum_id, us.poster_id, us.post_time, 
                      uv.topic_title, 
                      ug.forum_name, ug.forum_type, ug.forum_pass, 
                      ut.uname 
                  FROM 
                      " . $NPDS_Prefix . "posts us, 
                      " . $NPDS_Prefix . "forumtopics uv, 
                      " . $NPDS_Prefix . "forums ug, 
                      " . $NPDS_Prefix . "users ut 
                  WHERE 
                      us.post_id = $post_id 
                      AND uv.topic_id = us.topic_id 
                      AND uv.forum_id = ug.forum_id 
                      AND ut.uid = us.poster_id LIMIT 1");
        list($topic_id, $forum_id, $poster_id, $post_time, $topic_title, $forum_name, $forum_type, $forum_pass, $uname) = sql_fetch_row($res);

        if (($forum_type == "5") or ($forum_type == "7")) {

            $ok_affich = false;
            $tab_groupe = groupe::valid_group($user);
            $ok_affich = groupe::groupe_forum($forum_pass, $tab_groupe);
        } else {

            $ok_affich = true;
        }

        if ($ok_affich) {

            /*Nbre de postes par sujet*/
            $TableRep = sql_query("SELECT * FROM " . $NPDS_Prefix . "posts WHERE forum_id > 0 AND topic_id = '$topic_id'");
            $replys = sql_num_rows($TableRep) - 1;

            /*Gestion lu / non lu*/
            $sqlR = "SELECT rid FROM " . $NPDS_Prefix . "forum_read WHERE topicid = '$topic_id' AND uid = '$cookie[0]' AND status != '0'";

            if ($ibid = theme::theme_image("forum/icons/hot_red_folder.gif")) {
                $imgtmpHR = $ibid;
            } else {
                $imgtmpHR = "images/forum/icons/hot_red_folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/hot_folder.gif")) {
                $imgtmpH = $ibid;
            } else {
                $imgtmpH = "images/forum/icons/hot_folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
                $imgtmpR = $ibid;
            } else {
                $imgtmpR = "images/forum/icons/red_folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/folder.gif")) {
                $imgtmpF = $ibid;
            } else {
                $imgtmpF = "images/forum/icons/folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/lock.gif")) {
                $imgtmpL = $ibid;
            } else {
                $imgtmpL = "images/forum/icons/lock.gif";
            }

            if ($replys >= $hot_threshold) {

                if (sql_num_rows(sql_query($sqlR)) == 0) {
                    $image = $imgtmpHR;
                } else {
                    $image = $imgtmpH;}
            } else {

                if (sql_num_rows(sql_query($sqlR)) == 0) {
                    $image = $imgtmpR;
                } else {
                    $image = $imgtmpF;}
            }

            // ?????? $myrow
            if ($myrow['topic_status'] != 0) {
                $image = $imgtmpL;
            }

            $MM_forumP .= '<tr class="lignb">'
                . '<td align="center"><img src="' . $image . '"></td>'
                . '<td><a href="viewforum.php?forum=' . $forum_id . '">' . $forum_name . '</a></td>'
                . '<td><a href="viewtopic.php?topic=' . $topic_id . '&forum=' . $forum_id . '">' . $topic_title . '</a></td>'
                . '<td align="center">' . $replys . '</td>'
                . '<td><a href="user.php?op=userinfo&uname=' . $uname . '">' . $uname . '</a></td>'
                . '<td align="center">' . $post_time . '</td>'
                . '</tr>';
        }
    }

    $MM_forumP .= '</table>';

    return $MM_forumP;
}

function MM_forumL()
{
    global $NPDS_Prefix, $cookie, $user;

    /*Sujet chaud*/
    $hot_threshold = 10;

    /*Nbre posts a afficher*/
    $maxcount = "10";

    $MM_forumL = '<table cellspacing="3" cellpadding="3" width="top" border="0">'
        . '<tr align="center" class="ligna">'
        . '<td width="8%">' . language::aff_langue('[french]Etat[/french][english]State[/english]') . '</td>'
        . '<td width="35%">' . language::aff_langue('[french]Forum[/french][english]Forum[/english]') . '</td>'
        . '<td width="50%">' . language::aff_langue('[french]Sujet[/french][english]Topic[/english]') . '</td>'
        . '<td width="7%">' . language::aff_langue('[french]RÃ©ponses[/french][english]Replies[/english]') . '</td>'
        . '</tr>';

    /*Requete liste dernier post*/
    $result = sql_query("SELECT MAX(post_id) FROM " . $NPDS_Prefix . "posts WHERE forum_id > 0 GROUP BY topic_id ORDER BY MAX(post_id) DESC LIMIT 0,$maxcount");
    while (list($post_id) = sql_fetch_row($result)) {

        /*Requete detail dernier post*/
        $res = sql_query("SELECT 
                      us.topic_id, us.forum_id, us.poster_id, 
                      uv.topic_title, 
                      ug.forum_name, ug.forum_type, ug.forum_pass 
                  FROM 
                      " . $NPDS_Prefix . "posts us, 
                      " . $NPDS_Prefix . "forumtopics uv, 
                      " . $NPDS_Prefix . "forums ug 
                  WHERE 
                      us.post_id = $post_id 
                      AND uv.topic_id = us.topic_id 
                      AND uv.forum_id = ug.forum_id LIMIT 1");

        list($topic_id, $forum_id, $poster_id, $topic_title, $forum_name, $forum_type, $forum_pass) = sql_fetch_row($res);

        if (($forum_type == "5") or ($forum_type == "7")) {

            $ok_affich = false;
            $tab_groupe = groupe::valid_group($user);
            $ok_affich = groupe::groupe_forum($forum_pass, $tab_groupe);
        } else {

            $ok_affich = true;
        }

        if ($ok_affich) {

            /*Nbre de postes par sujet*/
            $TableRep = sql_query("SELECT * FROM " . $NPDS_Prefix . "posts WHERE forum_id > 0 AND topic_id = '$topic_id'");
            $replys = sql_num_rows($TableRep) - 1;

            /*Gestion lu / non lu*/
            $sqlR = "SELECT rid FROM " . $NPDS_Prefix . "forum_read WHERE topicid = '$topic_id' AND uid = '$cookie[0]' AND status != '0'";

            if ($ibid = theme::theme_image("forum/icons/hot_red_folder.gif")) {
                $imgtmpHR = $ibid;
            } else {
                $imgtmpHR = "images/forum/icons/hot_red_folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/hot_folder.gif")) {
                $imgtmpH = $ibid;
            } else {
                $imgtmpH = "images/forum/icons/hot_folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
                $imgtmpR = $ibid;
            } else {
                $imgtmpR = "images/forum/icons/red_folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/folder.gif")) {
                $imgtmpF = $ibid;
            } else {
                $imgtmpF = "images/forum/icons/folder.gif";
            }

            if ($ibid = theme::theme_image("forum/icons/lock.gif")) {
                $imgtmpL = $ibid;
            } else {
                $imgtmpL = "images/forum/icons/lock.gif";
            }

            if ($replys >= $hot_threshold) {

                if (sql_num_rows(sql_query($sqlR)) == 0) {
                    $image = $imgtmpHR;
                } else {
                    $image = $imgtmpH;}
            } else {

                if (sql_num_rows(sql_query($sqlR)) == 0) {
                    $image = $imgtmpR;
                } else {
                    $image = $imgtmpF;
                }
            }

            // ??????? $myrow 
            if ($myrow['topic_status'] != 0) {
                $image = $imgtmpL;
            }

            $MM_forumL .= '<tr class="lignb">'
                . '<td align="center"><img src="' . $image . '"></td>'
                . '<td><a href="viewforum.php?forum=' . $forum_id . '">' . $forum_name . '</a></td>'
                . '<td><a href="viewtopic.php?topic=' . $topic_id . '&forum=' . $forum_id . '">' . $topic_title . '</a></td>'
                . '<td align="center">' . $replys . '</td>'
                . '</tr>';
        }
    }

    $MM_forumL .= '</table>';

    return $MM_forumL;
}

function MM_vm_video($id_vm_video)
{
    $content = '';
    $id_vm_video = metalang::arg_filter($id_vm_video);

    if (!defined('CITRON')) {
        $content .= '
           <div class="ratio ratio-16x9">
              <iframe src="https://player.vimeo.com/video/' . $id_vm_video . '" allowfullscreen="" frameborder="0"></iframe>
           </div>';
    } else {
        $content .= '<div class="vimeo_player" videoID="' . $id_vm_video . '"></div>';
    }
    
    return $content;
}

function MM_dm_video($id_dm_video)
{
    $content = '';
    $id_dm_video = metalang::arg_filter($id_dm_video);

    if (!defined('CITRON')) {
        $content .= '
           <div class="ratio ratio-16x9">
              <iframe src="https://www.dailymotion.com/embed/video/' . $id_dm_video . '" allowfullscreen="" frameborder="0"></iframe>
           </div>';
    } else {
        $content .= '<div class="dailymotion_player" videoID="' . $id_dm_video . '"></div>';
    }

    return $content;
}

function MM_noforbadmail()
{
    global $subscribe, $user, $cookie;

    $remp = '';

    if ($subscribe and $user) {
        if (mailler::isbadmailuser($cookie[0]) === true)
            $remp = '!delete!';
    }

    return $remp;
}
