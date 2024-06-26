<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/* Based on Parts of phpBB                                              */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\assets\css;
use npds\support\auth\groupe;
use npds\support\cache\cache;
use npds\support\forum\forum;
use npds\support\theme\theme;
use npds\system\config\Config;
use npds\support\security\hack;
use npds\system\cache\cacheManager;
use npds\support\pagination\paginator;
use npds\system\cache\SuperCacheEmpty;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

// if ($SuperCache) {
//     $cache_obj = new cacheManager();
// } else {
//     $cache_obj = new SuperCacheEmpty();
// }

include('auth.php');

global $NPDS_Prefix, $admin;

//==> droits des admin sur les forums (superadmin et admin avec droit gestion forum)
$adminforum = false;
if ($admin) {
    $adminX = base64_decode($admin);
    $adminR = explode(':', $adminX);

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $Q = sql_fetch_assoc(sql_query("SELECT * FROM " . $NPDS_Prefix . "authors WHERE aid='$adminR[0]' LIMIT 1"));

    if ($Q['radminsuper'] == 1) {
        $adminforum = 1;
    } else {

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $R = sql_query("SELECT fnom, fid, radminsuper FROM " . $NPDS_Prefix . "authors a LEFT JOIN " . $NPDS_Prefix . "droits d ON a.aid = d.d_aut_aid LEFT JOIN " . $NPDS_Prefix . "fonctions f ON d.d_fon_fid = f.fid WHERE a.aid='$adminR[0]' AND f.fid BETWEEN 13 AND 15");
        
        if (sql_num_rows($R) >= 1) {
            $adminforum = 1;
        }
    }
}
//<== droits des admin sur les forums (superadmin et admin avec droit gestion forum)

settype($op, 'string');

if (($op == "mark") and ($forum)) {
    if ($user) {
        $userX = base64_decode($user);
        $userR = explode(':', $userX);

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $resultT = sql_query("SELECT topic_id FROM " . $NPDS_Prefix . "forumtopics WHERE forum_id='$forum' ORDER BY topic_id ASC");
        $time_actu = time() + ((int)$gmt * 3600);

        while (list($topic_id) = sql_fetch_row($resultT)) {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $r = sql_query("SELECT rid FROM " . $NPDS_Prefix . "forum_read WHERE forum_id='$forum' AND uid='$userR[0]' AND topicid='$topic_id'");
            
            if ($r) {
                if (!list($rid) = sql_fetch_row($r)) {

                    //DB::table('')->insert(array(
                    //    ''       => ,
                    //));

                    $r = sql_query("INSERT INTO " . $NPDS_Prefix . "forum_read (forum_id, topicid, uid, last_read, status) VALUES ('$forum', '$topic_id', '$userR[0]', $time_actu, '1')");
                } else {

                    //DB::table('')->where('', )->update(array(
                    //    ''       => ,
                    //));

                    $r = sql_query("UPDATE " . $NPDS_Prefix . "forum_read SET last_read='$time_actu', status='1' WHERE rid='$rid'");
                }
            }
        }

        header('location: '. site_url('forum.php'));
    }
}

if ($forum == "index") {
    header('location: '. site_url('forum.php'));
}

settype($forum, "integer");

// = DB::table('')->select()->where('', )->orderBy('')->get();

$rowQ1 = cache::Q_Select("SELECT forum_name, forum_moderator, forum_type, forum_pass, forum_access, arbre FROM " . $NPDS_Prefix . "forums WHERE forum_id = '$forum'", 3600);
if (!$rowQ1) {
    forum::forumerror('0002');
}

$myrow = $rowQ1[0];

$forum_name = stripslashes($myrow['forum_name']);
$moderator = forum::get_moderator($myrow['forum_moderator']);
$forum_access = $myrow['forum_access'];

if (($op == "solved") and ($topic_id) and ($forum) and ($sec_clef)) {
    if ($user) {
        $local_sec_clef = md5($forum . $topic_id . md5($NPDS_Key));

        if ($local_sec_clef == $sec_clef) {

            //DB::table('')->where('', )->update(array(
            //    ''       => ,
            //));

            $sqlS = "UPDATE " . $NPDS_Prefix . "forumtopics SET topic_status='2', topic_title='[" . __d('two_forum', 'Résolu') . "] - " . hack::removehack($topic_title) . "' WHERE topic_id='$topic_id'";
            
            if (!$r = sql_query($sqlS)) {
                forum::forumerror('0011');
            }
        }
        unset($local_sec_clef);
    }
    unset($sec_clef);
}

// Pour les forums de type Groupe, le Mot de Passe stock l'ID du groupe ...
// Pour les forums de type Extended Text, le Mot de Passe stock le nom du fichier de formulaire ...
if (($myrow['forum_type'] == 5) or ($myrow['forum_type'] == 7)) {
    $ok_affiche = false;

    if (isset($user)) {
        $tab_groupe = groupe::valid_group($user);
        $ok_affiche = groupe::groupe_forum($myrow['forum_pass'], $tab_groupe);
    }

    if ($ok_affiche) {
        $Forum_passwd = $myrow['forum_pass'];
    }
}

if ($myrow['forum_type'] == 8) {
    $Forum_passwd = $myrow['forum_pass'];
} else {
    settype($Forum_passwd, 'string');
}

$hrefX = $myrow['arbre'] ? 'viewtopicH.php' : 'viewtopic.php';

if (($myrow['forum_type'] == 1) and (($myrow['forum_name'] != $forum_name) or ($Forum_passwd != $myrow['forum_pass']))) {

    include('themes/default/header.php');

    echo '
    <h3 class="mb-3">' . stripslashes($forum_name) . '</h3>
        <p class="lead">' . __d('two_forum', 'Modéré par : ') . '';

    $moderator_data = explode(' ', $moderator);
    for ($i = 0; $i < count($moderator_data); $i++) {
        $modera = forum::get_userdata($moderator_data[$i]);

        if ($modera['user_avatar'] != '') {
            if (stristr($modera['user_avatar'], "users_private")) {
                $imgtmp = $modera['user_avatar'];
            } else {
                if ($ibid = theme::theme_image("forum/avatar/" . $modera['user_avatar'])) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = "assets/images/forum/avatar/" . $modera['user_avatar'];
                }
            }
        }
        echo '<a href="'. site_url('user.php?op=userinfo&amp;uname=' . $moderator_data[$i]) .'"><img width="48" height="48" class=" img-thumbnail img-fluid n-ava" src="' . $imgtmp . '" alt="' . $modera['uname'] . '" title="' . $modera['uname'] . '" data-bs-toggle="tooltip" /></a>';
    }

    echo '</p>';
    echo '
        <p class="lead">
            <a href="'. site_url('forum.php') .'">' . __d('two_forum', 'Index du forum') . '</a>&nbsp;&raquo;&raquo;&nbsp;' . stripslashes($forum_name) . '
        </p>
        <div class="card p-3">
            <form id="privforumentry" action="'. site_url('viewforum.php') .'" method="post">
                <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="forum_pass">' . __d('two_forum', 'Ceci est un forum privé. Vous devez entrer le mot de passe pour y accéder') . '</label>
                <div class="col-sm-12">
                    <input class="form-control" type="password" id="forum_pass" name="Forum_passwd"  placeholder="' . __d('two_forum', 'Mot de passe') . '" required="required"/>
                    <span class="help-block text-end" id="countcar_forum_pass"></span>
                </div>
                </div>
                <input type="hidden" name="forum" value="' . $forum . '" />
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary me-2" name="submitpass" title="' . __d('two_forum', 'Valider') . '"><i class="fa fa-check me-1"></i>' . __d('two_forum', 'Valider') . '</button>
                    <button type="reset" class="btn btn-secondary" name="reset" title="' . __d('two_forum', 'Annuler') . '"><i class="fas fa-sync me-1"></i>' . __d('two_forum', 'Annuler') . '</button>
                </div>
            </form>
        </div>';

    $arg1 = '
            var formulid=["privforumentry"];
            inpandfieldlen("forum_pass",60);';

    css::adminfoot('fv', '', $arg1, '');

} elseif (($Forum_passwd == $myrow['forum_pass']) or ($adminforum == 1)) {
    if (($myrow['forum_type'] == 9) and (!$user)) {
        header('location: '. site_url('forum.php'));
    }

    $title = $forum_name;
    include('themes/default/header.php');

    if ($user) {
        $userX = base64_decode($user);
        $userR = explode(':', $userX);
    }

    if (Config::get('forum.config.solved')) {
        if (isset($closoled)) {
            $closol = "and topic_status='2'";
            $mess_closoled = '<a href="'. site_url('viewforum.php?forum=' . $forum) .'">' . __d('two_forum', 'Sans') . ' ' . __d('two_forum', 'Résolu') . '</a>';
        } else {
            $closol = "and topic_status!='2'";
            $mess_closoled = '<a href="'. site_url('viewforum.php?forum=' . $forum . '&amp;closoled=on') .'">' . __d('two_forum', 'Seulement') . ' ' . __d('two_forum', 'Résolu') . '</a>';
        }

    } else {
        $closol = '';
        $mess_closoled = '';
    }

    echo '
    <p class="lead">
        <a href="'. site_url('forum.php') .'" >' . __d('two_forum', 'Index du forum') . '</a>&nbsp;&raquo;&raquo;&nbsp;' . stripslashes($forum_name) . '
    </p>
    <h3 class="mb-3">';

    if ($forum_access != 9) {
        $allow_to_post = true;

        if ($forum_access == 2) {
            if (!forum::user_is_moderator($userR[0], $userR[2], $forum_access)) { 
                $allow_to_post = false;
            }
        }

        if ($allow_to_post){
            echo '<a href="'. site_url('newtopic.php?forum=' . $forum) .'" title="' . __d('two_forum', 'Nouveau') . '"><i class="fa fa-plus-square me-2"></i><span class="d-none d-sm-inline">' . __d('two_forum', 'Nouveau sujet') . '<br /></span></a>';
        }
    }

    echo stripslashes($forum_name) . '<span class="text-muted">&nbsp;#' . $forum . '</span>
    </h3>';

    $moderator_data = explode(' ', $moderator);
    $ibidcountmod = count($moderator_data);

    echo '
        <div class="card mb-3">
            <div class="card-body p-2">
                <div class="d-flex ">
                <div class="badge bg-secondary align-self-center mx-2 col-2 col-md-3 col-xl-2 bg-white text-muted py-2 px-1"><span class="me-1 lead">' . $ibidcountmod . '<i class="fa fa-balance-scale fa-fw ms-1 d-inline d-md-none" title="' . __d('two_forum', 'Modérateur(s)') . '" data-bs-toggle="tooltip"></i></span><span class=" d-none d-md-inline">' . __d('two_forum', 'Modérateur(s)') . '</span></div>
                <div class=" align-self-center me-auto">';

    $Mmod = false;

    for ($i = 0; $i < count($moderator_data); $i++) {
        $modera = forum::get_userdata($moderator_data[$i]);

        if ($modera['user_avatar'] != '') {
            if (stristr($modera['user_avatar'], 'users_private')) {
                $imgtmp = $modera['user_avatar'];
            } else {
                if ($ibid = theme::theme_image("forum/avatar/" . $modera['user_avatar'])) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = "assets/images/forum/avatar/" . $modera['user_avatar'];
                }
            }
        }

        if ($user){
            if (($userR[1] == $moderator_data[$i])) {
                $Mmod = true;
            }
        }

        echo '<a href="'. site_url('user.php?op=userinfo&amp;uname=' . $moderator_data[$i]) .'"><img class=" img-thumbnail img-fluid n-ava-small me-1" src="' . $imgtmp . '" alt="' . $modera['uname'] . '" title="' . __d('two_forum', 'Modéré par : ') . ' ' . $modera['uname'] . '" data-bs-toggle="tooltip" /></a>';
    }

    echo '
                </div>
                </div>
            </div>
        </div>';

    settype($start, "integer");
    //settype($topics_per_page, "integer");

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $sql = "SELECT * FROM " . $NPDS_Prefix . "forumtopics WHERE forum_id='$forum' $closol ORDER BY topic_first,topic_time DESC LIMIT $start, ". Config::get('forum.config.topics_per_page');
    if (!$result = sql_query($sql)) {
        forum::forumerror('0004');
    }

    if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
        $imgtmpR = $ibid;
    } else {
        $imgtmpR = "assets/images/forum/icons/red_folder.gif";
    }

    if ($ibid = theme::theme_image("forum/icons/posticon.gif")) {
        $imgtmpP = $ibid;
    } else {
        $imgtmpP = "assets/images/forum/icons/posticon.gif";
    }

    if ($myrow = sql_fetch_assoc($result)) {
        echo '
        <h4 class="my-2">' . __d('two_forum', 'Sujets') . ' <span class="text-muted">' . $mess_closoled . '</span></h4>
        <table id ="lst_forum" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                <th class="n-t-col-xs-1" data-align="center"></th>
                <th class="n-t-col-xs-1" data-align="center"></th>
                <th class="" data-sortable="true" data-sorter="htmlSorter">' . __d('two_forum', 'Sujet') . '&nbsp;&nbsp;</th>
                <th class="n-t-col-xs-1" class="text-center" data-sortable="true" data-align="right" ><i class="fa fa-reply fa-lg text-muted" title="' . __d('two_forum', 'Réponses') . '" data-bs-toggle="tooltip" ></i></th>
                <th data-sortable="true" data-halign="center" data-align="left" ><i class="fa fa-user fa-lg text-muted" title="' . __d('two_forum', 'Emetteur') . '" data-bs-toggle="tooltip"></i></th>
                <th class="n-t-col-xs-1" class="text-center" data-sortable="true" data-align="right" ><i class="fa fa-eye fa-lg text-muted" title="' . __d('two_forum', 'Lectures') . '" data-bs-toggle="tooltip" ></i></th>
                <th data-align="right" >' . __d('two_forum', 'Dernières contributions') . '</th>
                </tr>
            </thead>
            <tbody>';

        do {
            echo '
                <tr>';

            $replys = forum::get_total_posts($forum, $myrow['topic_id'], "topic", $Mmod);
            $replys--;

            if ($replys >= 0) {
                
                if (Config::get('npds.smilies')) {

                    // = DB::table('')->select()->where('', )->orderBy('')->get();

                    $rowQ1 = cache::Q_Select("SELECT image FROM " . $NPDS_Prefix . "posts WHERE topic_id='" . $myrow['topic_id'] . "' AND forum_id='$forum' LIMIT 0,1", 86400);
                    $image_subject = $rowQ1[0]['image'];
                }

                if (($replys + 1) > Config::get('forum.config.posts_per_page')) {
                    $pages = 0;
                    
                    for ($x = 0; $x < ($replys + 1); $x += Config::get('forum.config.posts_per_page')) {
                        $pages++;
                    }

                    $last_post_url = "$hrefX?topic=" . $myrow['topic_id'] . "&amp;forum=$forum&amp;start=" . (($pages - 1) * Config::get('forum.config.posts_per_page'));
                } else {
                    $last_post_url = "$hrefX?topic=" . $myrow['topic_id'] . "&amp;forum=$forum";
                }

                if ($user) {

                    // = DB::table('')->select()->where('', )->orderBy('')->get();

                    $sqlR = "SELECT rid FROM " . $NPDS_Prefix . "forum_read WHERE forum_id='$forum' AND uid='$userR[0]' AND topicid='" . $myrow['topic_id'] . "' AND status!='0'";
                    
                    if ($replys >= Config::get('forum.config.hot_threshold')) {
                        $image = sql_num_rows(sql_query($sqlR)) == 0 ?
                            '<a href="' . $last_post_url . '#lastpost" title="' . __d('two_forum', 'Dernières contributions') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-lg fa-file-alt faa-shake animated"></i></a>' :
                            '<a href="' . $last_post_url . '#lastpost" title="' . __d('two_forum', 'Dernières contributions') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-lg fa-file-alt"></i></a>';
                    } else {
                        $image = sql_num_rows(sql_query($sqlR)) == 0 ?
                            '<a href="' . $last_post_url . '#lastpost" title="' . __d('two_forum', 'Dernières contributions') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="far fa-lg fa-file-alt faa-shake animated"></i></a>' :
                            '<a href="' . $last_post_url . '#lastpost" title="' . __d('two_forum', 'Dernières contributions') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="far fa-lg fa-file-alt"></i></a>';
                    }
                } else {
                    $image = ($replys >= Config::get('forum.config.hot_threshold')) ?
                        '<a href="' . $last_post_url . '#lastpost" title="' . __d('two_forum', 'Dernières contributions') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-lg fa-file-alt"></i></a>' :
                        '<a href="' . $last_post_url . '#lastpost" title="' . __d('two_forum', 'Dernières contributions') . '" data-bs-toggle="tooltip" data-bs-placement="right"><i class="far fa-lg fa-file-alt"></i></a>';
                }

                if ($myrow['topic_status'] != 0) {
                    $image = '<i class="fa fa-lg fa-lock text-danger"></i>';
                }

                echo '
                <td>' . $image . '</td>';

                if ($image_subject != '') {
                    if ($ibid = theme::theme_image("forum/subject/$image_subject")) {
                        $imgtmp = $ibid;
                    } else {
                        $imgtmp = "assets/images/forum/subject/$image_subject";
                    }

                    echo '<td><img class="n-smil" src="' . $imgtmp . '" alt="" /></td>';
                } else {
                    echo '<td><img class="n-smil" src="' . $imgtmpP . '" alt="" /></td>';
                }

                $topic_title = stripslashes($myrow['topic_title']);
                
                if (!stristr($topic_title, '<a href=')) {
                    $last_post_url = "$hrefX?topic=" . $myrow['topic_id'] . "&amp;forum=$forum";
                    echo '<td><a href="' . $last_post_url . '" >' . ucfirst($topic_title) . '</a></td>';
                    $Sredirection = false;
                } else {
                    echo '<td>' . $topic_title . '</td>';
                    $Sredirection = true;
                }

                if ($Sredirection){
                    echo '<td>&nbsp;</td>';
                } else {
                    echo '<td>' . $replys . '</td>';
                }
                
                if ($Sredirection) {
                    if (!$Mmod) {
                        echo '<td>&nbsp;</td>';
                    } else {
                        echo "<td>[ <a href=\"$hrefX?topic=" . $myrow['topic_id'] . "&amp;forum=$forum\">" . __d('two_forum', 'Editer') . "</a> ]</td>";
                    }

                    echo '<td>&nbsp;</td>';
                } else {
                    if ($myrow['topic_poster'] == 1) {
                        echo '<td></td>';
                    } else {

                        // = DB::table('')->select()->where('', )->orderBy('')->get();

                        $rowQ1 = cache::Q_Select("SELECT uname FROM " . $NPDS_Prefix . "users WHERE uid='" . $myrow['topic_poster'] . "'", 3600);
                        
                        if ($rowQ1) {
                            echo '<td>' . userpopover($rowQ1[0]['uname'], 40, 2) . $rowQ1[0]['uname'] . '</td>';
                        } else {
                            echo '<td>' . Config::get('npds.anonymous') . '</td>';
                        }
                    }
                    echo '<td>' . $myrow['topic_views'] . '</td>';
                }

                if ($Sredirection){
                    echo '
                        <td>&nbsp;</td>
                    </tr>';
                } else {
                    echo '
                        <td class="small">' . forum::get_last_post($myrow['topic_id'], "topic", "infos") . '</td>
                    </tr>';
                }
            }

        } while ($myrow = sql_fetch_assoc($result));

        sql_free_result($result);

        echo '
            </tbody>
        </table>';

        if ($user){
            echo '<p class="mt-1"><a href="'. site_url('viewforum.php?op=mark&amp;forum=' . $forum) .'"><i class="far fa-check-square fa-lg"></i></a>&nbsp;' . __d('two_forum', 'Marquer tous les messages comme lus') . '</p>';
        }
    } else {
        if ($forum_access != 9) {
            echo '<div class="alert alert-danger my-3">' . __d('two_forum', 'Il n\'y a aucun sujet pour ce forum. ') . '<br /><a href="'. site_url('newtopic.php?forum=' . $forum) .'" >' . __d('two_forum', 'Vous pouvez en poster un ici.') . '</a></div>';
        }
    }

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $sql = "SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "forumtopics WHERE forum_id='$forum' $closol";
    if (!$r = sql_query($sql)) {
        forum::forumerror('0001');
    }

    list($all_topics) = sql_fetch_row($r);
    sql_free_result($r);

    if (isset($closoled)) {
        $closol = '&amp;closoled=on';
    } else {
        $closol = '';
    }

    $count = 1;
    $nbPages = ceil($all_topics / Config::get('forum.config.topics_per_page'));

    $current = 1;
    if ($start >= 1) {
        $current = $start / Config::get('forum.config.topics_per_page');
    } else if ($start < 1) {
        $current = 0;
    } else {
        $current = $nbPages;
    }

    echo '<div class="mb-2"></div>' . paginator::paginate(site_url('viewforum.php?forum=' . $forum . '&amp;start='), $closol, $nbPages, $current, 1, Config::get('forum.config.topics_per_page'), $start);

    echo forum::searchblock();

    echo '
    <blockquote class="blockquote my-3">';

    if ($user) {
        echo '
        <i class="far fa-file-alt fa-lg faa-shake animated text-primary"></i> = ' . __d('two_forum', 'Les nouvelles contributions depuis votre dernière visite.') . '<br />
        <i class="fas fa-file-alt fa-lg faa-shake animated text-primary"></i> = ' . __d('two_forum', 'Plus de') . ' ' . Config::get('forum.config.hot_threshold') . ' ' . __d('two_forum', 'Contributions') . '<br />
        <i class="far fa-file-alt fa-lg text-primary"></i> = ' . __d('two_forum', 'Aucune nouvelle contribution depuis votre dernière visite.') . '<br />
        <i class="fas fa-file-alt fa-lg text-primary"></i> = ' . __d('two_forum', 'Plus de') . ' ' . Config::get('forum.config.hot_threshold') . ' ' . __d('two_forum', 'Contributions') . '<br />';
    } else {
        echo '
        <i class="fas fa-file-alt fa-lg text-primary"></i> = ' . __d('two_forum', 'Plus de') . ' ' . Config::get('forum.config.hot_threshold') . ' ' . __d('two_forum', 'Contributions') . '<br />
        <i class="far fa-file-alt fa-lg text-primary"></i> = ' . __d('two_forum', 'Contributions') . '.<br />';
    }

    echo '
        <i class="fa fa-lock fa-lg text-danger"></i> = ' . __d('two_forum', 'Ce sujet est verrouillé : il ne peut accueillir aucune nouvelle contribution.') . '<br />
    </blockquote>';

    // if ($SuperCache) {
    //     $cache_clef = "forum-jump-to";
    //     $CACHE_TIMINGS[$cache_clef] = 3600;
    //     $cache_obj->startCachingBlock($cache_clef);
    // }

    // if (($cache_obj->genereting_output == 1) or ($cache_obj->genereting_output == -1) or (!$SuperCache)) {

        echo '
        <form class="my-3" action="'. site_url('viewforum.php') .'" method="post">
            <div class="mb-3 row">
                <div class="col-12">
                    <label class="visually-hidden" for="forum">' . __d('two_forum', 'Sauter à : ') . '</label>
                    <select class="form-select" name="forum" onchange="submit();">
                    <option value="index">' . __d('two_forum', 'Sauter à : ') . '</option>
                    <option value="index">' . __d('two_forum', 'Index du forum') . '</option>';

        // = DB::table('')->select()->where('', )->orderBy('')->get();

        $sub_sql = "SELECT forum_id, forum_name, forum_type, forum_pass FROM " . $NPDS_Prefix . "forums ORDER BY cat_id,forum_index,forum_id";
        if ($res = sql_query($sub_sql)) {

            while (list($forum_id, $forum_name, $forum_type, $forum_pass) = sql_fetch_row($res)) {

                if (($forum_type != '9') or ($userdata)) {
                    
                    if (($forum_type == '7') or ($forum_type == '5')) {
                        $ok_affich = false;
                    } else {
                        $ok_affich = true;
                    }

                    if ($ok_affich) {
                        echo '<option value="' . $forum_id . '">&nbsp;&nbsp;' . stripslashes($forum_name) . '</option>';
                    }
                }
            }
        }

        echo '
                    </select>
                </div>
            </div>
        </form>';
        include("themes/default/footer.php");
    //}
    
    // if ($SuperCache) {
    //     $cache_obj->endCachingBlock($cache_clef);
    // }
} else {
    header('location: '. site_url('forum.php'));
}
