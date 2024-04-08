<?php

declare(strict_types=1);

namespace npds\system\block;

use npds\system\date\date;
use npds\system\news\news;
use npds\system\assets\css;
use npds\system\auth\users;
use npds\system\auth\groupe;
use npds\system\cache\cache;
use npds\system\support\str;
use npds\system\theme\theme;
use npds\system\config\Config;
use npds\system\support\stats;
use npds\system\support\download;
use npds\system\language\language;
use npds\system\support\facades\DB;

class boxe
{

    /**
     * Bloc activité du site
     * syntaxe : function#Site_Activ
     *
     * @return  void    [return description]
     */
    public static function Site_Activ(): void
    {
        list($membres, $totala, $totalb, $totalc, $totald, $totalz) = stats::req_stat();
        $aff = '
        <p class="text-center">' . translate("Pages vues depuis") . ' ' . Config::get('npds.startdate') . ' : <span class="fw-semibold">' . str::wrh($totalz) . '</span></p>
        <ul class="list-group mb-3" id="site_active">
        <li class="my-1">' . translate("Nb. de membres") . ' <span class="badge rounded-pill bg-secondary float-end">' . str::wrh(($membres)) . '</span></li>
        <li class="my-1">' . translate("Nb. d'articles") . ' <span class="badge rounded-pill bg-secondary float-end">' . str::wrh($totala) . '</span></li>
        <li class="my-1">' . translate("Nb. de forums") . ' <span class="badge rounded-pill bg-secondary float-end">' . str::wrh($totalc) . '</span></li>
        <li class="my-1">' . translate("Nb. de sujets") . ' <span class="badge rounded-pill bg-secondary float-end">' . str::wrh($totald) . '</span></li>
        <li class="my-1">' . translate("Nb. de critiques") . ' <span class="badge rounded-pill bg-secondary float-end">' . str::wrh($totalb) . '</span></li>
        </ul>';

        if ($ibid = theme::theme_image("box/top.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = false;
        } // no need

        if ($imgtmp) {
            $aff .= '<p class="text-center"><a href="top.php"><img src="' . $imgtmp . '" alt="' . translate("Top") . ' ' . Config::get('npds.top') . '" /></a>&nbsp;&nbsp;';

            if ($ibid = theme::theme_image("box/stat.gif")) {
                $imgtmp = $ibid;
            } else {
                $imgtmp = false;
            } // no need

            $aff .= '<a href="stats.php"><img src="' . $imgtmp . '" alt="' . translate("Statistiques") . '" /></a></p>';
        } else {
            $aff .= '<p class="text-center"><a href="top.php">' . translate("Top") . ' ' . Config::get('npds.top') . '</a>&nbsp;&nbsp;<a href="stats.php" >' . translate("Statistiques") . '</a></p>';
        }

        global $block_title;
        $title = $block_title == '' ? translate("Activité du site") : $block_title;

        themesidebox($title, $aff);
    }

    /**
     * Bloc Online (Who_Online)
     * syntaxe : function#online
     *
     * @return  void    [return description]
     */
    public static function online(): void
    {
        global $NPDS_Prefix, $user, $cookie;

        $ip = getip();
        $username = isset($cookie[1]) ? $cookie[1] : '';

        if ($username == '') {
            $username = $ip;
            $guest = 1;
        } else {
            $guest = 0;
        }

        $past = time() - 300;

        DB::table('session')->where('time', '<', $past)->delete();

        $result = sql_query("SELECT time FROM " . $NPDS_Prefix . "session WHERE username='$username'");
        $ctime = time();

        if ($row = sql_fetch_row($result)) {
            sql_query("UPDATE " . $NPDS_Prefix . "session SET username='$username', time='$ctime', host_addr='$ip', guest='$guest' WHERE username='$username'");
        } else {
            sql_query("INSERT INTO " . $NPDS_Prefix . "session (username, time, host_addr, guest) VALUES ('$username', '$ctime', '$ip', '$guest')");
        }

        $result = sql_query("SELECT username FROM " . $NPDS_Prefix . "session WHERE guest=1");
        $guest_online_num = sql_num_rows($result);

        $result = sql_query("SELECT username FROM " . $NPDS_Prefix . "session WHERE guest=0");
        $member_online_num = sql_num_rows($result);

        $who_online_num = $guest_online_num + $member_online_num;
        $who_online = '<p class="text-center">' . translate("Il y a actuellement") . ' <span class="badge bg-secondary">' . $guest_online_num . '</span> ' . translate("visiteur(s) et") . ' <span class="badge bg-secondary">' . $member_online_num . ' </span> ' . translate("membre(s) en ligne.") . '<br />';
        $content = $who_online;

        if ($user) {
            $content .= '<br />' . translate("Vous êtes connecté en tant que") . ' <strong>' . $username . '</strong>.<br />';
            $result = cache::Q_select("SELECT uid FROM " . $NPDS_Prefix . "users WHERE uname='$username'", 86400);
            $uid = $result[0];

            $result2 = sql_query("SELECT to_userid FROM " . $NPDS_Prefix . "priv_msgs WHERE to_userid='" . $uid['uid'] . "' AND type_msg='0'");
            $numrow = sql_num_rows($result2);

            $content .= translate("Vous avez") . ' <a href="viewpmsg.php"><span class="badge bg-primary">' . $numrow . '</span></a> ' . translate("message(s) personnel(s).") . '</p>';
        } else {
            $content .= '<br />' . translate("Devenez membre privilégié en cliquant") . ' <a href="user.php?op=only_newuser">' . translate("ici") . '</a></p>';
        }

        global $block_title;
        $title = $block_title == '' ? translate("Qui est en ligne ?") : $block_title;

        themesidebox($title, $content);
    }

    /**
     * Bloc Little News-Letter
     * syntaxe : function#lnlbox
     *
     * @return  void    [return description]
     */
    public static function lnlbox(): void
    {
        global $block_title;

        $title = $block_title == '' ? translate("La lettre") : $block_title;

        $arg1 = '
        var formulid = ["lnlblock"]';

        $boxstuff = '
          <form id="lnlblock" action="lnl.php" method="get">
             <div class="mb-3">
                <select name="op" class=" form-select">
                   <option value="subscribe">' . translate("Abonnement") . '</option>
                   <option value="unsubscribe">' . translate("Désabonnement") . '</option>
                </select>
             </div>
             <div class="form-floating mb-3">
                <input type="email" id="email_block" name="email" maxlength="254" class="form-control" required="required"/>
                <label for="email_block">' . translate("Votre adresse Email") . '</label>
                <span class="help-block">' . translate("Recevez par mail les nouveautés du site.") . '</span>
             </div>
             <button type="submit" class="btn btn-outline-primary btn-block btn-sm"><i class ="fa fa-check fa-lg me-2"></i>' . translate("Valider") . '</button>
          </form>'
            . css::adminfoot('fv', '', $arg1, '0');

        themesidebox($title, $boxstuff);
    }

    /**
     * Bloc Search-engine
     * syntaxe : function#searchbox
     *
     * @return  void    [return description]
     */
    public static function searchbox(): void
    {
        global $block_title;

        $title = $block_title == '' ? translate("Recherche") : $block_title;

        $content = '
        <form id="searchblock" action="search.php" method="get">
        <input class="form-control" type="text" name="query" />
        </form>';

        themesidebox($title, $content);
    }

    /**
     * Bloc Admin
     * syntaxe : function#adminblock
     *
     * @return  void    [return description]
     */
    public static function adminblock(): void
    {
        global $NPDS_Prefix, $admin, $aid;

        $bloc_foncts_A = '';

        if ($admin) {
            $Q = sql_fetch_assoc(sql_query("SELECT * FROM " . $NPDS_Prefix . "authors WHERE aid='$aid' LIMIT 1"));

            $R = $Q['radminsuper'] == 1 ?
                sql_query("SELECT * FROM " . $NPDS_Prefix . "fonctions f WHERE f.finterface =1 AND f.fetat != '0' ORDER BY f.fcategorie") :
                sql_query("SELECT * FROM " . $NPDS_Prefix . "fonctions f LEFT JOIN " . $NPDS_Prefix . "droits d ON f.fdroits1 = d.d_fon_fid LEFT JOIN " . $NPDS_Prefix . "authors a ON d.d_aut_aid =a.aid WHERE f.finterface =1 AND fetat!=0 AND d.d_aut_aid='$aid' AND d.d_droits REGEXP'^1' ORDER BY f.fcategorie");

            while ($SAQ = sql_fetch_assoc($R)) {
                $arraylecture = explode('|', $SAQ['fdroits1_descr']);
                $cat[] = $SAQ['fcategorie'];
                $cat_n[] = $SAQ['fcategorie_nom'];
                $fid_ar[] = $SAQ['fid'];

                if ($SAQ['fcategorie'] == 9) {
                    $adminico = Config::get('npds.adminimg') . $SAQ['ficone'] . '.' . Config::get('npds.admf_ext');
                }

                if ($SAQ['fcategorie'] == 9 and strstr($SAQ['furlscript'], "op=Extend-Admin-SubModule")) {
                    if (file_exists('modules/' . $SAQ['fnom'] . '/' . $SAQ['fnom'] . '.' . Config::get('npds.admf_ext'))) {
                        $adminico = 'modules/' . $SAQ['fnom'] . '/' . $SAQ['fnom'] . '.' . Config::get('npds.admf_ext');
                    } else {
                        $adminico = Config::get('npds.adminimg') . 'module.' . Config::get('npds.admf_ext');
                    }
                }

                if ($SAQ['fcategorie'] == 9) {
                    if (preg_match('#messageModal#', $SAQ['furlscript'])) {
                        $furlscript = 'data-bs-toggle="modal" data-bs-target="#bl_messageModal"';
                    }

                    if (preg_match('#mes_npds_\d#', $SAQ['fnom'])) {
                        if (!in_array($aid, $arraylecture, true)) {
                            $bloc_foncts_A .= '
                            <a class=" btn btn-outline-primary btn-sm me-2 my-1 tooltipbyclass" title="' . $SAQ['fretour_h'] . '" data-id="' . $SAQ['fid'] . '" data-bs-html="true" ' . $furlscript . ' >
                            <img class="adm_img" src="' . $adminico . '" alt="icon_message" loading="lazy" />
                            <span class="badge bg-danger ms-1">' . $SAQ['fretour'] . '</span>
                            </a>';
                        }
                    } else {
                        $furlscript = preg_match('#versusModal#', $SAQ['furlscript'])
                            ? 'data-bs-toggle="modal" data-bs-target="#bl_versusModal"'
                            : $SAQ['furlscript'];

                        if (preg_match('#NPDS#', $SAQ['fretour_h'])) {
                            $SAQ['fretour_h'] = str_replace('NPDS', 'NPDS^', $SAQ['fretour_h']);
                        }

                        $bloc_foncts_A .= '
                   <a class=" btn btn-outline-primary btn-sm me-2 my-1 tooltipbyclass" title="' . $SAQ['fretour_h'] . '" data-id="' . $SAQ['fid'] . '" data-bs-html="true" ' . $furlscript . ' >
                     <img class="adm_img" src="' . $adminico . '" alt="icon_' . $SAQ['fnom_affich'] . '" loading="lazy" />
                     <span class="badge bg-danger ms-1">' . $SAQ['fretour'] . '</span>
                   </a>';
                    }
                }
            }

            $result = sql_query("SELECT title, content FROM " . $NPDS_Prefix . "block WHERE id=2");
            list($title, $content) = sql_fetch_row($result);

            global $block_title;
            $title = $title == '' ? $block_title : language::aff_langue($title);
            $content = language::aff_langue(preg_replace_callback('#<a href=[^>]*(&)[^>]*>#', [str::class, 'changetoampadm'], $content));

            //==> recuperation
            // voir pour foskopen 
            $messagerie_npds = file_get_contents('https://raw.githubusercontent.com/npds/npds_dune/master/versus.txt');
            $messages_npds = explode("\n", $messagerie_npds);
            array_pop($messages_npds);

            // traitement specifique car fonction permanente versus
            $versus_info = explode('|', $messages_npds[0]);
            if ($versus_info[1] == Config::get('versioning.Version_Sub') and $versus_info[2] == Config::get('versioning.Version_Num')) {
                sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1', fretour='', fretour_h='Version NPDS " . Config::get('versioning.Version_Sub') . " " . Config::get('versioning.Version_Num') . "', furlscript='' WHERE fid='36'");
            } else {
                sql_query("UPDATE " . $NPDS_Prefix . "fonctions SET fetat='1', fretour='N', furlscript='data-bs-toggle=\"modal\" data-bs-target=\"#versusModal\"', fretour_h='Une nouvelle version NPDS est disponible !<br />" . $versus_info[1] . " " . $versus_info[2] . "<br />Cliquez pour télécharger.' WHERE fid='36'");
            }

            $content .= '
            <div class="d-flex justify-content-start flex-wrap" id="adm_block">
            ' . $bloc_foncts_A;

            if ($Q['radminsuper'] == 1) {
                $content .= '<a class="btn btn-outline-primary btn-sm me-2 my-1" title="' . translate("Vider la table chatBox") . '" data-bs-toggle="tooltip" href="powerpack.php?op=admin_chatbox_write&amp;chatbox_clearDB=OK" ><img src="assets/images/admin/chat.png" class="adm_img" />&nbsp;<span class="badge bg-danger ms-1">X</span></a>';
            }

            $content .= '</div>
        <div class="mt-3">
            <small class="text-muted"><i class="fas fa-user-cog fa-2x align-middle"></i> ' . $aid . '</small>
        </div>
        <div class="modal fade" id="bl_versusModal" tabindex="-1" aria-labelledby="bl_versusModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="bl_versusModalLabel"><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" loading="lazy" />' . translate("Version") . ' NPDS^</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <p>Vous utilisez NPDS^ ' . Config::get('versioning.Version_Sub') . ' ' . Config::get('versioning.Version_Num') . '</p>
                    <p>' . translate("Une nouvelle version de NPDS^ est disponible !") . '</p>
                    <p class="lead mt-3">' . $versus_info[1] . ' ' . $versus_info[2] . '</p>
                    <p class="my-3">
                        <a class="me-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.zip" target="_blank" title="" data-bs-toggle="tooltip" data-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.zip</a>
                        <a class="mx-3" href="https://github.com/npds/npds_dune/archive/refs/tags/' . $versus_info[2] . '.tar.gz" target="_blank" title="" data-bs-toggle="tooltip" data-original-title="Charger maintenant"><i class="fa fa-download fa-2x me-1"></i>.tar.gz</a>
                    </p>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>';
            $content .= '<div class="modal fade" id="bl_messageModal" tabindex="-1" aria-labelledby="bl_messageModalLabel" aria-hidden="true">
          <div class="modal-dialog">
             <div class="modal-content">
                <div class="modal-header">
                   <h5 class="modal-title" id=""><span id="bl_messageModalIcon" class="me-2"></span><span id="bl_messageModalLabel"></span></h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <p id="bl_messageModalContent"></p>
                   <form class="mt-3" id="bl_messageModalForm" action="" method="POST">
                      <input type="hidden" name="id" id="bl_messageModalId" value="0" />
                      <button type="submit" class="btn btn btn-primary btn-sm">' . translate("Confirmer la lecture") . '</button>
                   </form>
                </div>
                <div class="modal-footer">
                <span class="small text-muted">Information de npds.org</span><img class="adm_img me-2" src="assets/images/admin/message_npds.png" alt="icon_" loading="lazy" />
                </div>
             </div>
          </div>
       </div>
       <script>
          $(function () {
            $("#bl_messageModal").on("show.bs.modal", function (event) {
                var button = $(event.relatedTarget); 
                var id = button.data("id");
                $("#bl_messageModalId").val(id);
                $("#bl_messageModalForm").attr("action", "' . Config::get('npds.nuke_url') . '/admin.php?op=alerte_update");
                $.ajax({
                   url:"' . Config::get('npds.nuke_url') . '/admin.php?op=alerte_api",
                   method: "POST",
                   data:{id:id},
                   dataType:"JSON",
                   success:function(data) {
                      var fnom_affich = JSON.stringify(data["fnom_affich"]),
                          fretour_h = JSON.stringify(data["fretour_h"]),
                          ficone = JSON.stringify(data["ficone"]);
                      $("#bl_messageModalLabel").html(JSON.parse(fretour_h));
                      $("#bl_messageModalContent").html(JSON.parse(fnom_affich));
                      $("#bl_messageModalIcon").html("<img src=\"assets/images/admin/"+JSON.parse(ficone)+".png\" />");
                   }
                });
             });
          });
       </script>';

            themesidebox($title, $content);
        }
    }

    /**
     * Bloc principal
     * syntaxe : function#mainblock
     *
     * @return  void    [return description]
     */
    public static function mainblock(): void
    {
        global $NPDS_Prefix;

        $result = sql_query("SELECT title, content FROM " . $NPDS_Prefix . "block WHERE id=1");
        list($title, $content) = sql_fetch_row($result);

        global $block_title;
        if ($title == '') {
            $title = $block_title;
        }

        //must work from php 4 to 7 !..?..
        themesidebox(language::aff_langue($title), language::aff_langue(preg_replace_callback('#<a href=[^>]*(&)[^>]*>#', [str::class, 'changetoamp'], $content)));
    }

    /**
     * Bloc ephemerid
     * syntaxe : function#ephemblock
     *
     * @return  void    [return description]
     */
    public static function ephemblock(): void
    {
        global $NPDS_Prefix;

        $cnt = 0;
        $eday = date("d", time() + ((int) Config::get('npds.gmt') * 3600));
        $emonth = date("m", time() + ((int) Config::get('npds.gmt') * 3600));

        $result = sql_query("SELECT yid, content FROM " . $NPDS_Prefix . "ephem WHERE did='$eday' AND mid='$emonth' ORDER BY yid ASC");
        $boxstuff = '<div>' . translate("En ce jour...") . '</div>';

        while (list($yid, $content) = sql_fetch_row($result)) {
            if ($cnt == 1) {
                $boxstuff .= "\n<br />\n";
            }

            $boxstuff .= "<b>$yid</b>\n<br />\n";
            $boxstuff .= language::aff_langue($content);
            $cnt = 1;
        }

        $boxstuff .= "<br />\n";

        global $block_title;
        $title = $block_title == '' ? translate("Ephémérides") : $block_title;

        themesidebox($title, $boxstuff);
    }

    /**
     * Bloc Login
     * syntaxe : function#loginbox
     *
     * @return  void    [return description]
     */
    public static function loginbox(): void
    {
        global $user;

        $boxstuff = '';

        if (!$user) {
            $boxstuff = '
       <form action="user.php" method="post">
          <div class="mb-3">
             <label for="uname">' . translate("Identifiant") . '</label>
             <input class="form-control" type="text" name="uname" maxlength="25" />
          </div>
          <div class="mb-3">
             <label for="pass">' . translate("Mot de passe") . '</label>
             <input class="form-control" type="password" name="pass" maxlength="20" />
          </div>
          <div class="mb-3">
             <input type="hidden" name="op" value="login" />
             <button class="btn btn-primary" type="submit">' . translate("Valider") . '</button>
          </div>
          <div class="help-block">
          ' . translate("Vous n'avez pas encore de compte personnel ? Vous devriez") . ' <a href="user.php">' . translate("en créer un") . '</a>. ' . translate("Une fois enregistré") . ' ' . translate("vous aurez certains avantages, comme pouvoir modifier l'aspect du site,") . ' ' . translate("ou poster des commentaires signés...") . '
          </div>
       </form>';

            global $block_title;
            $title = $block_title == '' ? translate("Se connecter") : $block_title;

            themesidebox($title, $boxstuff);
        }
    }

    /**
     * Bloc membre
     * syntaxe : function#userblock
     *
     * @return  void    [return description]
     */
    public static function userblock(): void
    {
        global $NPDS_Prefix, $user, $cookie;

        if (($user) and ($cookie[8])) {
            $getblock = cache::Q_select("SELECT ublock FROM " . $NPDS_Prefix . "users WHERE uid='$cookie[0]'", 86400);
            $ublock = $getblock[0];

            global $block_title;
            $title = $block_title == '' ? translate("Menu de") . ' ' . $cookie[1] : $block_title;

            themesidebox($title, $ublock['ublock']);
        }
    }

    /**
     * Bloc topdownload
     * syntaxe : function#topdownload
     *
     * @return  void    [return description]
     */
    public static function topdownload(): void
    {
        global $block_title;

        $title = $block_title == '' ? translate("Les plus téléchargés") : $block_title;
        $boxstuff = '<ul>';
        $boxstuff .= download::topdownload_data('short', 'dcounter');
        $boxstuff .= '</ul>';

        if ($boxstuff == '<ul></ul>') {
            $boxstuff = '';
        }

        themesidebox($title, $boxstuff);
    }

    /**
     * Bloc lastdownload
     * syntaxe : function#lastdownload
     *
     * @return  void    [return description]
     */
    public static function lastdownload(): void
    {
        global $block_title;

        $title = $block_title == '' ? translate("Fichiers les + récents") : $block_title;

        $boxstuff = '<ul>';
        $boxstuff .= download::topdownload_data('short', 'ddate');
        $boxstuff .= '</ul>';

        if ($boxstuff == '<ul></ul>') {
            $boxstuff = '';
        }

        themesidebox($title, $boxstuff);
    }

    /**
     * Bloc Anciennes News
     * syntaxe : function#oldNews
     * params#$storynum,lecture (affiche le NB de lecture) - facultatif
     *
     * @param   string  $storynum  [$storynum description]
     * @param   string  $typ_aff   [$typ_aff description]
     *
     * @return  void               [return description]
     */
    public static function oldNews(string $storynum, ?string $typ_aff = ''): void
    {
        global $locale, $categories, $cat, $user, $cookie;

        $boxstuff = '<ul class="list-group">';
        $storynum = isset($cookie[3]) ? $cookie[3] : Config::get('npds.storyhome');

        if (($categories == 1) and ($cat != '')) {
            $sel = $user ? "WHERE catid='$cat'" : "WHERE catid='$cat' AND ihome=0";
        } else {
            $sel = $user ? '' : "WHERE ihome=0";
        }

        $sel =  "WHERE ihome=0"; // en dur pour test
        $vari = 0;
        $xtab = news::news_aff('old_news', $sel, $storynum, Config::get('npds.oldnum'));
        $story_limit = 0;
        $time2 = 0;
        $a = 0;

        while (($story_limit < Config::get('npds.oldnum')) and ($story_limit < sizeof($xtab))) {
            list($sid, $title, $time, $comments, $counter) = $xtab[$story_limit];
            $story_limit++;

            // a revoir entre locale user et local site
            $locale = language::getLocale();
            $datetime2 = ucfirst(htmlentities(\PHP81_BC\strftime(translate("datestring2"), $time, $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));

            if (Config::get('npds.language') != 'chinese') {
                $datetime2 = ucfirst($datetime2);
            }

            $comments = $typ_aff == 'lecture' ?
                '<span class="badge rounded-pill bg-secondary ms-1" title="' . translate("Lu") . '" data-bs-toggle="tooltip">' . $counter . '</span>' : '';

            if ($time2 == $datetime2) {
                $boxstuff .= '
          <li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center"><a class="n-ellipses" href="article.php?sid=' . $sid . '">' . language::aff_langue($title) . '</a>' . $comments . '</li>';
            } else {
                if ($a == 0) {
                    $boxstuff .= '<li class="list-group-item fs-6">' . $datetime2 . '</li><li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center"><a href="article.php?sid=' . $sid . '">' . language::aff_langue($title) . '</a>' . $comments . '</li>';
                    $time2 = $datetime2;
                    $a = 1;
                } else {
                    $boxstuff .= '<li class="list-group-item fs-6">' . $datetime2 . '</li><li class="list-group-item list-group-item-action d-inline-flex justify-content-between align-items-center"><a href="article.php?sid=' . $sid . '">' . language::aff_langue($title) . '</a>' . $comments . '</li>';
                    $time2 = $datetime2;
                }
            }

            $vari++;

            if ($vari == Config::get('npds.oldnum')) {
                $storynum = isset($cookie[3]) ? $cookie[3] : Config::get('npds.storyhome');
                $min = Config::get('npds.oldnum') + $storynum;
                $boxstuff .= "<li class=\"text-center mt-3\" ><a href=\"search.php?min=$min&amp;type=stories&amp;category=$cat\"><strong>" . translate("Articles plus anciens") . "</strong></a></li>\n";
            }
        }

        $boxstuff .= '</ul>';

        if ($boxstuff == '<ul></ul>') {
            $boxstuff = '';
        }

        global $block_title;
        $boxTitle = $block_title == '' ? translate("Anciens articles") : $block_title;

        themesidebox($boxTitle, $boxstuff);
    }

    /**
     * Bloc BigStory
     * syntaxe : function#bigstory
     *
     * @return  void    [return description]
     */
    public static function bigstory(): void
    {
        $content = '';
        $today = getdate();
        $day = $today['mday'];

        if ($day < 10) {
            $day = "0$day";
        }

        $month = $today['mon'];

        if ($month < 10) {
            $month = "0$month";
        }

        $year = $today['year'];
        $tdate = "$year-$month-$day";
        $xtab = news::news_aff("big_story", "WHERE (time LIKE '%$tdate%')", 1, 1);

        if (sizeof($xtab)) {
            list($fsid, $ftitle) = $xtab[0];
        } else {
            $fsid = '';
            $ftitle = '';
        }

        $content .= ($fsid == '' and $ftitle == '') ?
            '<span class="fw-semibold">' . translate("Il n'y a pas encore d'article du jour.") . '</span>' :
            '<span class="fw-semibold">' . translate("L'article le plus consulté aujourd'hui est :") . '</span><br /><br /><a href="article.php?sid=' . $fsid . '">' . language::aff_langue($ftitle) . '</a>';

        global $block_title;
        $boxtitle = $block_title == '' ? translate("Article du Jour") : $block_title;

        themesidebox($boxtitle, $content);
    }

    /**
     * Bloc de gestion des catégories
     * syntaxe : function#category
     *
     * @return  void
     */
    public static function category(): void
    {
        global $NPDS_Prefix, $cat;

        $result = sql_query("SELECT catid, title FROM " . $NPDS_Prefix . "stories_cat ORDER BY title");
        $numrows = sql_num_rows($result);

        if ($numrows == 0) {
            return;
        } else {
            $boxstuff = '<ul>';

            while (list($catid, $title) = sql_fetch_row($result)) {
                $result2 = sql_query("SELECT sid FROM " . $NPDS_Prefix . "stories WHERE catid='$catid' LIMIT 0,1");
                $numrows = sql_num_rows($result2);

                if ($numrows > 0) {
                    $res = sql_query("SELECT time FROM " . $NPDS_Prefix . "stories WHERE catid='$catid' ORDER BY sid DESC LIMIT 0,1");
                    list($time) = sql_fetch_row($res);

                    $boxstuff .= (($cat == $catid)
                        ? '<li><strong>' . language::aff_langue($title) . '</strong></li>'
                        : '<li class="list-group-item list-group-item-action hyphenate"><a href="index.php?op=newcategory&amp;catid=' . $catid . '" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="right" title="' . translate("Dernière contribution") . ' <br />' . date::formatTimestamp($time) . ' ">' . language::aff_langue($title) . '</a></li>'
                    );
                }
            }

            $boxstuff .= '</ul>';

            global $block_title;
            $title = $block_title == '' ? translate("Catégories") : $block_title;

            themesidebox($title, $boxstuff);
        }
    }

    /**
     * Bloc HeadLines
     * syntaxe : function#headlines
     * params#ID_du_canal
     *
     * @param   string  $hid    [$hid description]
     * @param   bool    $block  [$block description]
     * @param   string|true            [ description]
     *
     * @return  string          [return description]
     */
    public static function headlines(string $hid = '', string|bool $block = true): string|bool
    {
        global $NPDS_Prefix;

        if (file_exists("config/proxy.php")) {
            include("config/proxy.php");
        }

        if ($hid == '') {
            $result = sql_query("SELECT sitename, url, headlinesurl, hid FROM " . $NPDS_Prefix . "headlines WHERE status=1");
        } else {
            $result = sql_query("SELECT sitename, url, headlinesurl, hid FROM " . $NPDS_Prefix . "headlines WHERE hid='$hid' AND status=1");
        }

        while (list($sitename, $url, $headlinesurl, $hid) = sql_fetch_row($result)) {
            $boxtitle = $sitename;

            $cache_file = 'storage/cache/' . preg_replace('[^a-z0-9]', '', strtolower($sitename)) . '_' . $hid . '.cache';
            $cache_time = 1200; //3600 origine

            //$items = 0; // ?????
            $max_items = 6;
            $rss_timeout = 15;
            $rss_font = '<span class="small">';

            if ((!(file_exists($cache_file))) or (filemtime($cache_file) < (time() - $cache_time)) or (!(filesize($cache_file)))) {
                $rss = parse_url($url);

                if (Config::get('npds.rss_host_verif') == true) {
                    $verif = fsockopen($rss['host'], 80, $errno, $errstr, $rss_timeout);

                    if ($verif) {
                        fclose($verif);
                        $verif = true;
                    }
                } else {
                    $verif = true;
                }

                if (!$verif) {
                    // a revoir ???? pas clair !!!!
                    $cache_file_sec = $cache_file . ".security";

                    if (file_exists($cache_file)) {
                        $ibid = rename($cache_file, $cache_file_sec);
                    }

                    themesidebox($boxtitle, "Security Error");
                    return true;
                } else {
                    if (!Config::get('npds.theme.long_chain')) {
                        Config::set('npds.theme.long_chain', 15);
                    }

                    $fpwrite = fopen($cache_file, 'w');

                    if ($fpwrite) {
                        fputs($fpwrite, "<ul>\n");
                        $flux = simplexml_load_file($headlinesurl, 'SimpleXMLElement', LIBXML_NOCDATA);

                        //$namespaces = $flux->getNamespaces(true); // get namespaces ?????
                        //$ic = ''; // ?????

                        //ATOM//
                        if ($flux->entry) {
                            $j = 0;
                            $cont = '';

                            foreach ($flux->entry as $entry) {
                                if ($entry->content) {
                                    $cont = (string) $entry->content;
                                }

                                fputs($fpwrite, '<li><a href="' . (string)$entry->link['href'] . '" target="_blank" >' . (string) $entry->title . '</a><br />' . $cont . '</li>');

                                if ($j == $max_items) {
                                    break;
                                }
                                $j++;
                            }
                        }

                        if ($flux->{'item'}) {
                            $j = 0;
                            $cont = '';
                            foreach ($flux->item as $item) {
                                if ($item->description) {
                                    $cont = (string) $item->description;
                                }

                                fputs($fpwrite, '<li><a href="' . (string)$item->link['href'] . '"  target="_blank" >' . (string) $item->title . '</a><br /></li>');

                                if ($j == $max_items) {
                                    break;
                                }
                                $j++;
                            }
                        }

                        //RSS
                        if ($flux->{'channel'}) {
                            $j = 0;
                            $cont = '';
                            foreach ($flux->channel->item as $item) {
                                if ($item->description) {
                                    $cont = (string) $item->description;
                                }

                                fputs($fpwrite, '<li><a href="' . (string)$item->link . '"  target="_blank" >' . (string) $item->title . '</a><br />' . $cont . '</li>');

                                if ($j == $max_items) {
                                    break;
                                }
                                $j++;
                            }
                        }

                        $j = 0;
                        if ($flux->image) {
                            $ico = '<img class="img-fluid" src="' . $flux->image->url . '" />&nbsp;';
                        }

                        foreach ($flux->item as $item) {
                            fputs($fpwrite, '<li>' . $ico . '<a href="' . (string) $item->link . '" target="_blank" >' . (string) $item->title . '</a></li>');

                            if ($j == $max_items) {
                                break;
                            }
                            $j++;
                        }

                        fputs($fpwrite, "\n" . '</ul>');
                        fclose($fpwrite);
                    }
                }
            }

            // a revoir ??? pas clair !!!! 
            if (file_exists($cache_file)) {
                ob_start();
                $ibid = readfile($cache_file); // ??????
                $boxstuff = $rss_font . ob_get_contents() . '</span>';
                ob_end_clean();
            }

            $boxstuff .= '<div class="text-end"><a href="' . $url . '" target="_blank">' . translate("Lire la suite...") . '</a></div>';

            if ($block) {
                themesidebox($boxtitle, $boxstuff);
                $boxstuff = '';
                return true;
            } else {
                return $boxstuff;
            }
        }
    }

    /**
     * Bloc des Rubriques
     * syntaxe : function#bloc_rubrique
     *
     * @return  void
     */
    public static function bloc_rubrique(): void
    {
        global $NPDS_Prefix;

        $result = sql_query("SELECT rubid, rubname, ordre FROM " . $NPDS_Prefix . "rubriques WHERE enligne='1' AND rubname<>'divers' ORDER BY ordre");
        $boxstuff = '<ul>';

        while (list($rubid, $rubname) = sql_fetch_row($result)) {
            $title = language::aff_langue($rubname);
            $result2 = sql_query("SELECT secid, secname, userlevel, ordre FROM " . $NPDS_Prefix . "sections WHERE rubid='$rubid' ORDER BY ordre");

            $boxstuff .= '<li><strong>' . $title . '</strong></li>';

            //$ibid++;//??? only for notice ???
            while (list($secid, $secname, $userlevel) = sql_fetch_row($result2)) {

                $query3 = "SELECT artid FROM " . $NPDS_Prefix . "seccont WHERE secid='$secid'";
                $result3 = sql_query($query3);
                $nb_article = sql_num_rows($result3);

                if ($nb_article > 0) {
                    $boxstuff .= '<ul>';
                    $tmp_auto = explode(',', $userlevel);

                    foreach ($tmp_auto as $userlevel) {
                        $okprintLV1 = users::autorisation($userlevel);
                        if ($okprintLV1) {
                            break;
                        }
                    }

                    if ($okprintLV1) {
                        $sec = language::aff_langue($secname);
                        $boxstuff .= '<li><a href="sections.php?op=listarticles&amp;secid=' . $secid . '">' . $sec . '</a></li>';
                    }

                    $boxstuff .= '</ul>';
                }
            }
        }

        $boxstuff .= '</ul>';

        global $block_title;
        $title = $block_title == '' ? translate("Rubriques") : $block_title;

        themesidebox($title, $boxstuff);
    }

    /**
     * Bloc du WorkSpace
     * syntaxe : function#bloc_espace_groupe
     * params#ID_du_groupe, Aff_img_groupe(0 ou 1) 
     * Si le bloc n'a pas de titre, Le nom du groupe sera utilisé
     *
     * @param   string   $gr    [$gr description]
     * @param   string   $i_gr  [$i_gr description]
     *
     * @return  void
     */
    public static function bloc_espace_groupe(string $gr, string $i_gr): void
    {
        global $NPDS_Prefix, $block_title;

        if ($block_title == '') {
            $rsql = sql_fetch_assoc(sql_query("SELECT groupe_name FROM " . $NPDS_Prefix . "groupes WHERE groupe_id='$gr'"));
            $title = $rsql['groupe_name'];
        } else {
            $title = $block_title;
        }

        themesidebox($title, groupe::fab_espace_groupe($gr, "0", $i_gr));
    }

    /**
     * Bloc des groupes
     * syntaxe : function#bloc_groupes
     * params#Aff_img_groupe(0 ou 1) Si le bloc n'a pas de titre,
     * 'Les groupes' sera utilisé. Liste des groupes AVEC membres et lien pour demande d'adhésion pour l'utilisateur.
     *
     * @param   string   $im  [$im description]
     *
     * @return  void
     */
    public static function bloc_groupes(string $im): void
    {
        global $block_title, $user;

        $title = $block_title == '' ? 'Les groupes' : $block_title;
        themesidebox($title, groupe::fab_groupes_bloc($user, $im));
    }

    /**
     * Bloc langue 
     * syntaxe : function#bloc_langue
     *
     * @return  void
     */
    public static function bloc_langue(): void
    {
        global $block_title;

        if (Config::get('npds.multi_langue')) {
            $title = $block_title == '' ? translate("Choisir une langue") : $block_title;
            themesidebox($title, language::aff_local_langue("index.php", "choice_user_language", ''));
        }
    }

    /**
     * [blockSkin description]
     *
     * @return  void
     */
    public static function blockSkin(): void
    {
        global $user;

        $skinOn = '';

        if ($user) {
            $user2 = base64_decode($user);
            $cookie = explode(':', $user2);
            $ibix = explode('+', urldecode($cookie[9]));
            $skinOn = substr($ibix[0], -3) != '_sk' ? '' : $ibix[1];
        } else {
            $skinOn = substr(Config::get('npds.Default_Theme'), -3) != '_sk' ? '' : Config::get('npds.Default_Skin');;
        }

        $content = '';

        if ($skinOn != '') {
            $content .= '
       <div class="form-floating">
          <select class="form-select" id="blocskinchoice"><option>' . $skinOn . '</option></select>
          <label for="blocskinchoice">Choisir un skin</label>
       </div>
       <script type="text/javascript">
          //<![CDATA[
          fetch("themes/_skins/api/skins.json")
             .then(response => response.json())
             .then(data => load(data));
          function load(data) {
             const skins = data.skins;
             const select = document.querySelector("#blocskinchoice");
             skins.forEach((value, index) => {
                const option = document.createElement("option");
                option.value = index;
                option.textContent = value.name;
                select.append(option);
             });
             select.addEventListener("change", (e) => {
                const skin = skins[e.target.value];
                if (skin) {
                   document.querySelector("#bsth").setAttribute("href", skin.css);
                   document.querySelector("#bsthxtra").setAttribute("href", skin.cssxtra);
                }
             });
             const changeEvent = new Event("change");
             select.dispatchEvent(changeEvent);
          }
          //]]>
       </script>';
        } else {
            $content .= '<div class="alert alert-danger">Thème non skinable</div>';
        }

        themesidebox('Theme Skin', $content);
    }

    /**
     * Construit le bloc sondage
     *
     * @param   int  $pollID     [$pollID description]
     * @param   string              [ description]
     * @param   int     $pollClose  [$pollClose description]
     *
     * @return  void
     */
    public static function pollMain(int $pollID, string|int $pollClose): void
    {
        global $NPDS_Prefix, $boxTitle, $boxContent;

        if (!isset($pollID)) {
            $pollID = 1;
        }

        if (!isset($url)) {
            $url = sprintf("pollBooth.php?op=results&amp;pollID=%d", $pollID);
        }

        $boxContent = '
        <form action="pollBooth.php" method="post">
        <input type="hidden" name="pollID" value="' . $pollID . '" />
        <input type="hidden" name="forwarder" value="' . $url . '" />';

        $result = sql_query("SELECT pollTitle, voters FROM " . $NPDS_Prefix . "poll_desc WHERE pollID='$pollID'");
        list($pollTitle, $voters) = sql_fetch_row($result);

        global $block_title;
        $boxTitle = $block_title == '' ? translate("Sondage") :  $block_title;

        $boxContent .= '<legend>' . language::aff_langue($pollTitle) . '</legend>';
        $result = sql_query("SELECT pollID, optionText, optionCount, voteID FROM " . $NPDS_Prefix . "poll_data WHERE (pollID='$pollID' AND optionText<>'') ORDER BY voteID");

        $sum = 0;
        $j = 0;

        if (!$pollClose) {
            $boxContent .= '<div class="mb-3">';

            while ($object = sql_fetch_assoc($result)) {
                $boxContent .= '
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="voteID' . $j . '" name="voteID" value="' . $object['voteID'] . '" />
                    <label class="form-check-label d-block" for="voteID' . $j . '" >' . language::aff_langue($object['optionText']) . '</label>
                </div>';
                $sum = $sum + $object['optionCount'];
                $j++;
            }

            $boxContent .= '</div>';
        } else {
            while ($object = sql_fetch_assoc($result)) {
                $boxContent .= '&nbsp;' . language::aff_langue($object['optionText']) . '<br />';
                $sum = $sum + $object['optionCount'];
            }
        }

        settype($inputvote, 'string');

        if (!$pollClose) {
            $inputvote = '<button class="btn btn-outline-primary btn-sm btn-block" type="submit" value="' . translate("Voter") . '" title="' . translate("Voter") . '" ><i class="fa fa-check fa-lg"></i> ' . translate("Voter") . '</button>';
        }

        $boxContent .= '
        <div class="mb-3">' . $inputvote . '</div>
        </form>
        <a href="pollBooth.php?op=results&amp;pollID=' . $pollID . '" title="' . translate("Résultats") . '">' . translate("Résultats") . '</a>&nbsp;&nbsp;<a href="pollBooth.php">' . translate("Anciens sondages") . '</a>
        <ul class="list-group mt-3">
        <li class="list-group-item">' . translate("Votes : ") . ' <span class="badge rounded-pill bg-secondary float-end">' . $sum . '</span></li>';

        if (Config::get('npds.pollcomm')) {
            if (file_exists("modules/comments/config/pollBoth.conf.php")) {
                include("modules/comments/config/pollBoth.conf.php");
            }

            list($numcom) = sql_fetch_row(sql_query("SELECT COUNT(*) FROM " . $NPDS_Prefix . "posts WHERE forum_id='$forum' AND topic_id='$pollID' AND post_aff='1'"));

            $boxContent .= '<li class="list-group-item">' . translate("Commentaire(s) : ") . ' <span class="badge rounded-pill bg-secondary float-end">' . $numcom . '</span></li>';
        }

        $boxContent .= '</ul>';

        themesidebox($boxTitle, $boxContent);
    }
}
