<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/*                                                                      */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/* module geoloc version 4.1                                            */
/* geoloc_refip.php file 2008-2023 by Jean Pierre Barbary (jpb)         */
/* dev team : Philippe Revilliod (Phr), A.NICOL                         */
/************************************************************************/

use npds\system\config\Config;
use npds\system\http\response;
use npds\support\security\hack;

    /**
     * Controle de réponse// c'est pas encore assez fin not work with https probably
     *
     * @param   string  $url            [$url description]
     * @param   int     $response_code  [$response_code description]
     *
     * @return  bool
     */
    function file_contents_exist(string $url, int $response_code = 200): bool
    {
        $headers = get_headers($url);
        
        if (substr($headers[0], 9, 3) == $response_code) {
            return true;
        } else {
            return false; 
        }
    }


#autodoc geoloc_refip : contrôle si l'ip est déjà dans la base et incrémentation du compteur de visite de l'ip <br /> ou choisi un fournisseur en fonction du protocol du site et des clefs disponibles et complète la table ip_loc. 

    $file_path = array(
    'https://ipapi.co/'.urldecode($ip).'/json/',
    'https://api.ipdata.co/'.urldecode($ip).'?api-key='.$api_key_ipdata,
    'https://extreme-ip-lookup.com/json/'.urldecode($ip).'?key='.$key_lookup,
    'http://ip-api.com/json/'.urldecode($ip),
    'http://extreme-ip-lookup.com/json/'.urldecode($ip).'?key='.$key_lookup
    );
    $ousursit='';

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $resultat=sql_query("SELECT * FROM ".$NPDS_Prefix."ip_loc WHERE ip_ip LIKE \"$ip\"");
    $controle=sql_num_rows($resultat);
    while ($row = sql_fetch_array($resultat)) {
    $ousursit= preg_replace("#/.*?/#",'',$_SERVER['PHP_SELF']);
    }
    if($controle != 0)

    //DB::table('')->where('', )->update(array(
    //    ''       => ,
    //));

    sql_query("UPDATE ".$NPDS_Prefix."ip_loc SET ip_visite= ip_visite +1 , ip_visi_pag = \"$ousursit\" WHERE ip_ip LIKE \"$ip\" ");
    else {
    $ibid=false;
    
    $nuke_url = Config::get('npds.nuke_url');
    
    if(strstr($nuke_url,'https')) {
    if(response::file_contents_exist($file_path[0])) {
        $loc = file_get_contents($file_path[0]);
        $loc_obj = json_decode($loc);
        if($loc_obj) {
            $error = property_exists($loc_obj, "error");
            if($error === false) {
                $ibid=true;
                $pay= !empty($loc_obj->country_name)? hack::removeHack($loc_obj->country_name): '';
                $codepay= !empty($loc_obj->country)? hack::removeHack($loc_obj->country): '';
                $vi= !empty($loc_obj->city)? hack::removeHack($loc_obj->city): '';
                $lat= !empty($loc_obj->latitude)? (float)$loc_obj->latitude: ''; 
                $long= !empty($loc_obj->longitude)? (float)$loc_obj->longitude: '';
            }
        }
    }
    if($ibid===false and $api_key_ipdata !='') {
        if(response::file_contents_exist($file_path[1])) {
            $loc = file_get_contents($file_path[1]);
            $loc_obj = json_decode($loc);
            if($loc_obj) {
                $error = property_exists($loc_obj, "message");
                if($error === false) {
                $ibid=true;
                $pay= !empty($loc_obj->country_name)? hack::removeHack($loc_obj->country_name): '';
                $codepay= !empty($loc_obj->country_code)? hack::removeHack($loc_obj->country_code): '';
                $vi= !empty($loc_obj->city)? hack::removeHack($loc_obj->city): '';
                $lat= !empty($loc_obj->latitude)? (float)$loc_obj->latitude: '';
                $long= !empty($loc_obj->longitude)? (float)$loc_obj->longitude: '';
                }
            }
        }
    }
    if($ibid===false and $key_lookup !='') {
        if(response::file_contents_exist($file_path[2])) {
            $loc = file_get_contents($file_path[2]);
            $loc_obj = json_decode($loc);
            if ($loc_obj->status=='success') {
                $ibid=true;
                $pay= !empty($loc_obj->country)? hack::removeHack($loc_obj->country): '';
                $codepay= !empty($loc_obj->countryCode)? hack::removeHack($loc_obj->countryCode): '';
                $vi= !empty($loc_obj->city)? hack::removeHack($loc_obj->city): '';
                $lat= !empty($loc_obj->lat)? (float)$loc_obj->lat: '';
                $long= !empty($loc_obj->lon)? (float)$loc_obj->lon: '';
            }
        }
    }
    }
    else if(strstr($nuke_url,'http')) {
    if(response::file_contents_exist($file_path[3])) {
        $loc = file_get_contents($file_path[3]);
        $loc_obj = json_decode($loc);
        if($loc_obj) {
            if ($loc_obj->status=='success') {
                $ibid=true;
                $pay= !empty($loc_obj->country)? hack::removeHack($loc_obj->country): '';
                $codepay= !empty($loc_obj->countryCode)? hack::removeHack($loc_obj->countryCode): '';
                $vi= !empty($loc_obj->city)? hack::removeHack($loc_obj->city): '';
                $lat= !empty($loc_obj->lat)? (float)$loc_obj->lat: '';
                $long= !empty($loc_obj->lon)? (float)$loc_obj->lon: '';
            }
        }
    }
    if($ibid===false and $key_lookup !='') {
        if(response::file_contents_exist($file_path[4])) {
            $loc = file_get_contents($file_path[4]);
            $loc_obj = json_decode($loc);
            if ($loc_obj->status=='success') {
                $ibid=true;
                $pay= !empty($loc_obj->country)? hack::removeHack($loc_obj->country): '';
                $codepay= !empty($loc_obj->countryCode)? hack::removeHack($loc_obj->countryCode): '';
                $vi= !empty($loc_obj->city)? hack::removeHack($loc_obj->city): '';
                $lat= !empty($loc_obj->lat)? (float)$loc_obj->lat: '';
                $long= !empty($loc_obj->lon)? (float)$loc_obj->lon: '';
            }
        }
    }
    }
    if ($ibid===false)
    return ;
    else {

    //DB::table('')->insert(array(
    //    ''       => ,
    //));

    sql_query("INSERT INTO ".$NPDS_Prefix."ip_loc (ip_long, ip_lat, ip_ip, ip_country, ip_code_country, ip_city) VALUES ('$long', '$lat', '$ip', '$pay', '$codepay', '$vi')");

    //DB::table('')->where('', )->update(array(
    //    ''       => ,
    //));

    sql_query("UPDATE ".$NPDS_Prefix."ip_loc SET ip_visite= ip_visite +1, ip_visi_pag = \"$ousursit\" WHERE ip_ip LIKE \"$ip\" ");
    }
    }