<?php

use npds\system\assets\js;
use npds\system\date\date;
use npds\system\logs\logs;
use npds\system\news\news;
use npds\system\assets\css;
use npds\system\auth\users;
use npds\system\block\boxe;
use npds\system\assets\java;
use npds\system\auth\groupe;
use npds\system\block\block;
use npds\system\cache\cache;
use npds\system\routing\url;
use npds\system\support\str;
use npds\system\theme\theme;
use npds\system\auth\authors;
use npds\system\mail\mailler;
use npds\system\pixels\image;
use npds\system\utility\code;
use npds\system\utility\spam;
use npds\system\cookie\cookie;
use npds\system\http\response;
use npds\system\security\hack;
use npds\system\support\edito;
use npds\system\support\polls;
use npds\system\support\stats;
use npds\system\utility\crypt;
use npds\system\support\online;
use npds\system\session\session;
use npds\system\support\counter;
use npds\system\support\editeur;
use npds\system\support\referer;
use npds\system\support\download;
use npds\system\language\language;
use npds\system\language\metalang;
use npds\system\subscribe\subscribe;

// function provisoire

// assets css
function  import_css($tmp_theme, $language, $fw_css, $css_pages_ref, $css)
{
    return css::import_css($tmp_theme, $language, $fw_css, $css_pages_ref, $css);
}

function import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css)
{
    return css::import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css);
}

function adminfoot($fv, $fv_parametres, $arg1, $foo)
{
    return css::adminfoot($fv, $fv_parametres, $arg1, $foo);
}

// assets js

function auto_complete($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $temps_cache)
{
    return js::auto_complete($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $temps_cache);
}

function auto_complete_multi($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $req)
{
    return js::auto_complete_multi($nom_array_js, $nom_champ, $nom_tabl, $id_inpu, $req);
}

// assets java

function JavaPopUp($F, $T, $W, $H)
{
    return java::JavaPopUp($F, $T, $W, $H);
}


// auth

// authors.php

function is_admin($xadmin)
{
    return authors::is_admin($xadmin);
}


function formatAidHeader($aid)
{
    return authors::formatAidHeader($aid);
}

// groupe.php

function valid_group($user)
{
    return groupe::valid_group($user);
}

function liste_group()
{
    return groupe::liste_group();
}

function groupe_forum($forum_groupeX, $tab_groupeX)
{
    return groupe::groupe_forum($forum_groupeX, $tab_groupeX);
}

function groupe_autorisation($groupeX, $tab_groupeX)
{
    return groupe::groupe_autorisation($groupeX, $tab_groupeX);
}

function fab_espace_groupe($gr, $t_gr, $i_gr)
{
    return groupe::fab_espace_groupe($gr, $t_gr, $i_gr);
}

function fab_groupes_bloc($user, $im)
{
    return groupe::fab_groupes_bloc($user, $im);
}

// users.php

function is_user($xuser)
{
    return users::is_user($xuser);
}

function getusrinfo($user)
{
    return users::getusrinfo($user);
}

function AutoReg()
{
    return users::AutoReg();
}

function getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms)
{
    return users::getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms);
}

function secur_static($sec_type)
{
    return users::secur_static($sec_type);
}

function autorisation($auto)
{
    return users::autorisation($auto);
}


// blocks

// block.php

function block_fonction($title, $contentX)
{
    return block::block_fonction($title, $contentX);
}

function fab_block($title, $member, $content, $Xcache)
{
    return block::fab_block($title, $member, $content, $Xcache);
}

function leftblocks($moreclass)
{
    return block::leftblocks($moreclass);
}

function rightblocks($moreclass)
{
    return block::rightblocks($moreclass);
}

function oneblock($Xid, $Xblock)
{
    return block::oneblock($Xid, $Xblock);
}

function Pre_fab_block($Xid, $Xblock, $moreclass)
{
    return block::Pre_fab_block($Xid, $Xblock, $moreclass);
}

function niv_block($Xcontent)
{
    return block::niv_block($Xcontent);
}

function autorisation_block($Xcontent)
{
    return block::autorisation_block($Xcontent);
}

// boxe.php


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

// cache

/// cache.php

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

function Q_Select($Xquery, $retention)
{
    return cache::Q_Select($Xquery, $retention);
}

function PG_clean($request)
{
    return cache::PG_clean($request);
}

function Q_Clean()
{
    return cache::Q_Clean();
}

function SC_clean()
{
    return cache::SC_clean();
}

// cookie

// cookie.php

function cookiedecode($user)
{
    return cookie::cookiedecode($user);
}


// date

// date.php

function NightDay()
{
    return date::NightDay();
}

function formatTimestamp($time)
{
    return date::formatTimestamp($time);
}

function convertdateTOtimestamp($myrow)
{
    return date::convertdateTOtimestamp($myrow);
}

// http

// response.php

function file_contents_exist($url, $response_code)
{
    return response::file_contents_exist($url, $response_code);
}

// language

// language.php

function language_iso($l, $s, $c)
{
    return language::language_iso($l, $s, $c);
}

function languageList()
{
    return language::languageList();
}

function languageWhiteToCache($languageslist)
{
    return language::languageWhiteToCache($languageslist);
}

function languages()
{
    return language::languages();
}

function getLocale2()
{
    return language::getLocale2();
}

function getLocaleIso()
{
    return language::getLocaleIso();
}


function getLocale()
{
    return language::getLocale();
}

function aff_langue($ibid)
{
    return language::aff_langue($ibid);
}

function make_tab_langue()
{
    return language::make_tab_langue();
}

function aff_localzone_langue($ibid)
{
    return language::aff_localzone_langue($ibid);
}

function aff_local_langue($ibid_index, $ibid, $mess)
{
    return language::aff_local_langue($ibid_index, $ibid, $mess);
}

function preview_local_langue($local_user_language, $ibid)
{
    return language::preview_local_langue($local_user_language, $ibid);
}

function initLocale()
{
    return language::initLocale();
}

//metalang.php

function arg_filter($arg)
{
    return metalang::arg_filter($arg);
}

function MM_img($ibid)
{
    return metalang::MM_img($ibid);
}

function charg($funct, $arguments)
{
    return metalang::charg($funct, $arguments);
}

function match_uri($racine, $R_uri)
{
    return metalang::match_uri($racine, $R_uri);
}

function charg_metalang()
{
    return metalang::charg_metalang();
}

function ana_args($arg)
{
    return metalang::ana_args($arg);
}

function meta_lang($Xcontent)
{
    return metalang::meta_lang($Xcontent);
}

// logs

// logs.php

function Ecr_Log($fic_log, $req_log, $mot_log)
{
    return logs::Ecr_Log($fic_log, $req_log, $mot_log);
}

// mail

// mailler.php

function send_email($email, $subject, $message, $from, $priority, $mime, $file)
{
    return mailler::send_email($email, $subject, $message, $from, $priority, $mime, $file);
}

function copy_to_email($to_userid, $sujet, $message)
{
    return mailler::copy_to_email($to_userid, $sujet, $message);
}

function Mess_Check_Mail($username)
{
    return mailler::Mess_Check_Mail($username);
}

function Mess_Check_Mail_interface($username, $class)
{
    return mailler::Mess_Check_Mail_interface($username, $class);
}

function Mess_Check_Mail_Sub($username, $class)
{
    return mailler::Mess_Check_Mail_Sub($username, $class);
}


// news

// news.php

function ultramode()
{
    return news::ultramode();
}

function ctrl_aff($ihome, $catid)
{
    return news::ctrl_aff($ihome, $catid);
}

function news_aff($type_req, $sel, $storynum, $oldnum)
{
    return news::news_aff($type_req, $sel, $storynum, $oldnum);
}

function automatednews()
{
    return news::automatednews();
}

function aff_news($op, $catid, $marqeur)
{
    return news::aff_news($op, $catid, $marqeur);
}

function prepa_aff_news($op, $catid, $marqeur)
{
    return news::prepa_aff_news($op, $catid, $marqeur);
}

function getTopics($s_sid)
{
    return news::getTopics($s_sid);
}


// pixels

// image.php

function dataimagetofileurl($base_64_string, $output_path)
{
    return image::dataimagetofileurl($base_64_string, $output_path);
}


// routing

// url.php

function redirect_url($urlx)
{
    return url::redirect_url($urlx);
}

// security

// hack.php

function removeHack($Xstring)
{
    return hack::removeHack($Xstring);
}


// session

// session.php

function session_manage()
{
    return session::session_manage();
}


// subcribe

// subscribe.php

function subscribe_mail($Xtype, $Xtopic, $Xforum, $Xresume, $Xsauf)
{
    return subscribe::subscribe_mail($Xtype, $Xtopic, $Xforum, $Xresume, $Xsauf);
}

function subscribe_query($Xuser, $Xtype, $Xclef)
{
    return subscribe::subscribe_query($Xuser, $Xtype, $Xclef);
}

// support

// counter.php

function counterUpadate()
{
    return counter::counterUpadate();
}

// download.php

function topdownload_data($form, $ordre)
{
    return download::topdownload_data($form, $ordre);
}


// editeur.php

function aff_editeur($Xzone, $Xactiv)
{
    return editeur::aff_editeur($Xzone, $Xactiv);
}


// edito.php

function fab_edito()
{
    return edito::fab_edito();
}

function aff_edito()
{
    return edito::aff_edito();
}

// online.php

function Who_Online()
{
    return online::Who_Online();
}

function Who_Online_Sub()
{
    return online::Who_Online_Sub();
}

function Site_Load()
{
    return online::Site_Load();
}


// polls.php

function pollSecur($pollID)
{
    return polls::pollSecur($pollID);
}

function PollNewest(?int $id = null)
{
    return polls::PollNewest($id);
}


// referer.php

function refererUpdate()
{
    return referer::refererUpdate();
}


// stat.php

function req_stat()
{
    return stats::req_stat();
}


// str.php

function conv2br($txt)
{
    return str::conv2br($txt);
}

function hexfromchr($txt)
{
    return str::hexfromchr($txt);
}

function wrh($ibid)
{
    return str::wrh($ibid);
}

function split_string_without_space($msg, $split)
{
    return str::split_string_without_space($msg, $split);
}

function wrapper_f($string, $key, $cols)
{
    return str::wrapper_f($string, $key, $cols);
}

function changetoamp($r)
{
    return str::changetoamp($r);
}

function changetoampadm($r)
{
    return str::changetoampadm($r);
}

function utf8_java($ibid)
{
    return str::utf8_java($ibid);
}

function FixQuotes($what)
{
    return str::FixQuotes($what);
}

// .php

function theme_image($theme_img)
{
    return theme::theme_image($theme_img);
}

function getSkin()
{
    return theme::getSkin();
}

function getTheme()
{
    return theme::getTheme();
}

function themeLists(?bool $implode = true, ?string $separator = ' ')
{
    return theme::themeLists($implode, $separator);
}

function themepreview($title, $hometext, $bodytext, $notes)
{
    return theme::themepreview($title, $hometext, $bodytext, $notes);
}

// utility

// code.php

function change_cod($r)
{
    return code::change_cod($r);
}

function af_cod($ibid)
{
    return code::af_cod($ibid);
}

function desaf_cod($ibid)
{
    return code::desaf_cod($ibid);
}

function aff_code($ibid)
{
    return code::aff_code($ibid);
}


// crypt.php

function keyED($txt, $encrypt_key)
{
    return crypt::keyED($txt, $encrypt_key);
}

function encrypt($txt)
{
    return crypt::encrypt($txt);
}

function encryptK($txt, $C_key)
{
    return crypt::encryptK($txt, $C_key);
}

function decrypt($txt)
{
    return crypt::decrypt($txt);
}

function decryptK($txt, $C_key)
{
    return crypt::decryptK($txt, $C_key);
}

// spam.php

function preg_anti_spam($ibid)
{
    return spam::preg_anti_spam($ibid);
}

function anti_spam($str, $highcode)
{
    return spam::anti_spam($str, $highcode);
}

function Q_spambot()
{
    return spam::Q_spambot();
}

function L_spambot($ip, $status)
{
    return spam::L_spambot($ip, $status);
}

function R_spambot($asb_question, $asb_reponse, $message)
{
    return spam::R_spambot($asb_question, $asb_reponse, $message);
}

// debug

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

// .php

// function  {
//    return  ::;
// }
