<?php

declare(strict_types=1);

namespace npds\system\forum;

use npds\system\date\date;
use npds\system\logs\logs;
use npds\system\auth\groupe;
use npds\system\cache\cache;
use npds\system\theme\theme;
use npds\system\mail\mailler;
use npds\system\language\language;
use npds\system\language\metalang;

class forum
{

    #autodoc RecentForumPosts($title, $maxforums, $maxtopics, $dposter, $topicmaxchars,$hr,$decoration) : Bloc Forums <br />=> syntaxe :<br />function#RecentForumPosts<br />params#titre, nb_max_forum (O=tous), nb_max_topic, affiche_l'emetteur(true / false), topic_nb_max_char, affiche_HR(true / false),
    public static function RecentForumPosts($title, $maxforums, $maxtopics, $displayposter = false, $topicmaxchars = 15, $hr = false, $decoration = '')
    {
        $boxstuff = static::RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration);

        global $block_title;

        if ($title == '') {
            $title = $block_title == '' ? translate("Forums infos") : $block_title;
        }

        themesidebox($title, $boxstuff);
    }

    public static function RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration)
    {
        global $parse, $user, $NPDS_Prefix;

        $topics = 0;

        settype($maxforums, "integer");
        settype($maxtopics, "integer");

        $lim = $maxforums == 0 ? '' : " LIMIT $maxforums";

        $query = $user ?
            "SELECT * FROM " . $NPDS_Prefix . "forums ORDER BY cat_id,forum_index,forum_id" . $lim :
            "SELECT * FROM " . $NPDS_Prefix . "forums WHERE forum_type!='9' AND forum_type!='7' AND forum_type!='5' ORDER BY cat_id,forum_index,forum_id" . $lim;
        $result = sql_query($query);

        if (!$result) {
            exit();
        }

        $boxstuff = '<ul>';

        while ($row = sql_fetch_row($result)) {
            if (($row[6] == "5") or ($row[6] == "7")) {
                $ok_affich = false;
                $tab_groupe = groupe::valid_group($user);
                $ok_affich = groupe::groupe_forum($row[7], $tab_groupe);
            } else {
                $ok_affich = true;
            }

            if ($ok_affich) {
                $forumid = $row[0];
                $forumname = $row[1];
                $forum_desc = $row[2];

                if ($hr) {
                    $boxstuff .= '<li><hr /></li>';
                }

                if ($parse == 0) {
                    $forumname = FixQuotes($forumname);
                    $forum_desc = FixQuotes($forum_desc);
                } else {
                    $forumname = stripslashes($forumname);
                    $forum_desc = stripslashes($forum_desc);
                }

                $res = sql_query("SELECT * FROM " . $NPDS_Prefix . "forumtopics WHERE forum_id = '$forumid' ORDER BY topic_time DESC");
                $ibidx = sql_num_rows($res);

                $boxstuff .= '
                <li class="list-unstyled border-0 p-2 mt-1"><h6><a href="viewforum.php?forum=' . $forumid . '" title="' . strip_tags($forum_desc) . '" data-bs-toggle="tooltip">' . $forumname . '</a><span class="float-end badge bg-secondary" title="' . translate("Sujets") . '" data-bs-toggle="tooltip">' . $ibidx . '</span></h6></li>';

                $topics = 0;
                while (($topics < $maxtopics) && ($topicrow = sql_fetch_row($res))) {
                    $topicid = $topicrow[0];
                    $tt = $topictitle = $topicrow[1];
                    $date = $topicrow[3];
                    $replies = 0;

                    $postquery = "SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "posts WHERE topic_id = '$topicid'";

                    if ($pres = sql_query($postquery)) {
                        if ($myrow = sql_fetch_assoc($pres)) {
                            $replies = $myrow['total'];
                        }
                    }

                    if (strlen($topictitle) > $topicmaxchars) {
                        $topictitle = substr($topictitle, 0, $topicmaxchars);
                        $topictitle .= '..';
                    }

                    if ($displayposter) {
                        $posterid = $topicrow[2];
                        $RowQ1 = cache::Q_Select("SELECT uname FROM " . $NPDS_Prefix . "users WHERE uid = '$posterid'", 3600);
                        $myrow = $RowQ1[0];
                        $postername = $myrow['uname'];
                    }

                    if ($parse == 0) {
                        $tt =  strip_tags(FixQuotes($tt));
                        $topictitle = FixQuotes($topictitle);
                    } else {
                        $tt =  strip_tags(stripslashes($tt));
                        $topictitle = stripslashes($topictitle);
                    }

                    $boxstuff .= '<li class="list-group-item p-1 border-right-0 border-left-0 list-group-item-action"><div class="n-ellipses"><span class="badge bg-secondary mx-2" title="' . translate("Réponses") . '" data-bs-toggle="tooltip" data-bs-placement="top">' . $replies . '</span><a href="viewtopic.php?topic=' . $topicid . '&amp;forum=' . $forumid . '" >' . $topictitle . '</a></div>';
                    
                    if ($displayposter) {
                        $boxstuff .= $decoration . '<span class="ms-1">' . $postername . '</span>';
                    }

                    $boxstuff .= '</li>';
                    $topics++;
                }
            }
        }

        $boxstuff .= '
            </ul>';
            
        return ($boxstuff);
    }

    public static function get_total_topics($forum_id)
    {
        global $NPDS_Prefix;
    
        $sql = "SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "forumtopics WHERE forum_id='$forum_id'";
    
        if (!$result = sql_query($sql)) {
            return ("ERROR");
        }
    
        if (!$myrow = sql_fetch_assoc($result)) {
            return ("ERROR");
        }
    
        sql_free_result($result);
    
        return $myrow['total'];
    }
    
    #autodoc get_contributeurs($fid, $tid) : Retourne une chaine des id des contributeurs du sujet
    public static function get_contributeurs($fid, $tid)
    {
        global $NPDS_Prefix;
    
        $rowQ1 = cache::Q_Select("SELECT DISTINCT poster_id FROM " . $NPDS_Prefix . "posts WHERE topic_id='$tid' AND forum_id='$fid'", 2);
    
        $posterids = '';
        foreach ($rowQ1 as $contribs) {
            foreach ($contribs as $contrib) {
                $posterids .= $contrib . ' ';
            }
        }
    
        return chop($posterids);
    }
    
    public static function get_total_posts($fid, $tid, $type, $Mmod)
    {
        global $NPDS_Prefix;
    
        $post_aff = $Mmod ? '' : " AND post_aff='1'";
    
        switch ($type) {
            case 'forum':
                $sql = "SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "posts WHERE forum_id='$fid'$post_aff";
                break;
    
            case 'topic':
                $sql = "SELECT COUNT(*) AS total FROM " . $NPDS_Prefix . "posts WHERE topic_id='$tid' AND forum_id='$fid' $post_aff";
                break;
    
            case 'user':
                static::forumerror('0031');
        }
    
        if (!$result = sql_query($sql)) {
            return ("ERROR");
        }
    
        if (!$myrow = sql_fetch_assoc($result)) {
            return ("0");
        }
    
        sql_free_result($result);
    
        return $myrow['total'];
    }
    
    public static function get_last_post($id, $type, $cmd, $Mmod)
    {
        global $NPDS_Prefix;
    
        // $Mmod ne sert plus - maintenu pour compatibilité
        switch ($type) {
            case 'forum':
                $sql1 = "SELECT topic_time, current_poster FROM " . $NPDS_Prefix . "forumtopics WHERE forum_id = '$id' ORDER BY topic_time DESC LIMIT 0,1";
                $sql2 = "SELECT uname FROM " . $NPDS_Prefix . "users WHERE uid=";
                break;
    
            case 'topic':
                $sql1 = "SELECT topic_time, current_poster FROM " . $NPDS_Prefix . "forumtopics WHERE topic_id = '$id'";
                $sql2 = "SELECT uname FROM " . $NPDS_Prefix . "users WHERE uid=";
                break;
        }
    
        if (!$result = sql_query($sql1)) {
            return ("ERROR");
        }
    
        if ($cmd == 'infos') {
            if (!$myrow = sql_fetch_row($result)) { 
                $val = translate("Rien");
            } else {
                $rowQ1 = cache::Q_Select($sql2 . "'" . $myrow[1] . "'", 3600);
                $val = date::convertdate($myrow[0]);
                $val .= $rowQ1 ? ' ' . userpopover($rowQ1[0]['uname'], 40, 2) : '';
            }
        }
    
        sql_free_result($result);
    
        return $val;
    }
    
    public static function get_moderator($user_id)
    {
        global $NPDS_Prefix;
    
        $user_id = str_replace(",", "' or uid='", $user_id);
        if ($user_id == 0) {
            return ("None");
        }
    
        $rowQ1 = cache::Q_Select("SELECT uname FROM " . $NPDS_Prefix . "users WHERE uid='$user_id'", 3600);
        $modslist = '';
    
        foreach ($rowQ1 as $modnames) {
            foreach ($modnames as $modname) {
                $modslist .= $modname . ' ';
            }
        }
    
        return chop($modslist);
    }

    public static function user_is_moderator($uidX, $passwordX, $forum_accessX)
    {
        global $NPDS_Prefix;
    
        $result1 = sql_query("SELECT pass FROM " . $NPDS_Prefix . "users WHERE uid = '$uidX'");
        $userX = sql_fetch_assoc($result1);
    
        $password = $userX['pass'];
    
        $result2 = sql_query("SELECT level FROM " . $NPDS_Prefix . "users_status WHERE uid = '$uidX'");
        $userX = sql_fetch_assoc($result2);
    
        if ((md5($password) == $passwordX) and ($forum_accessX <= $userX['level']) and ($userX['level'] > 1)) {
            return $userX['level'];
        } else {
            return false;
        }
    }
    
    public static function get_userdata_from_id($userid)
    {
        global $NPDS_Prefix;
    
        $sql1 = "SELECT * FROM " . $NPDS_Prefix . "users WHERE uid='$userid'";
        $sql2 = "SELECT * FROM " . $NPDS_Prefix . "users_status WHERE uid='$userid'";
    
        if (!$result = sql_query($sql1)) {
            static::forumerror('0016');
        }
    
        if (!$myrow = sql_fetch_assoc($result)) {
            $myrow = array("uid" => 1);
        } else {
            $myrow = array_merge($myrow, (array)sql_fetch_assoc(sql_query($sql2)));
        }
    
        return $myrow;
    }
    
    public static function get_userdata_extend_from_id($userid)
    {
        global $NPDS_Prefix;
    
        $sql1 = "SELECT * FROM " . $NPDS_Prefix . "users_extend WHERE uid='$userid'";
        // $sql2 = "SELECT * FROM ".$NPDS_Prefix."users_status WHERE uid='$userid'";
    
        // if (!$result = sql_query($sql1)) { 
        //     forumerror('0016');
        // }
    
        // if (!$myrow = sql_fetch_assoc($result)) {
        //     $myrow = array( "uid" => 1);
        // } else {
        //     $myrow=array_merge($myrow,(array)sql_fetch_assoc(sql_query($sql1)));
        // }
    
        $myrow = (array) sql_fetch_assoc(sql_query($sql1));
    
        return $myrow;
    }
    
    public static function get_userdata($username)
    {
        global $NPDS_Prefix;
    
        $sql = "SELECT * FROM " . $NPDS_Prefix . "users WHERE uname='$username'";
    
        if (!$result = sql_query($sql)) {
            static::forumerror('0016');
        }
    
        if (!$myrow = sql_fetch_assoc($result)) {
            $myrow = array("uid" => 1);
        }
    
        return $myrow;
    }
    
    public static function does_exists($id, $type)
    {
        global $NPDS_Prefix;
    
        switch ($type) {
            case 'forum':
                $sql = "SELECT forum_id FROM " . $NPDS_Prefix . "forums WHERE forum_id = '$id'";
                break;
    
            case 'topic':
                $sql = "SELECT topic_id FROM " . $NPDS_Prefix . "forumtopics WHERE topic_id = '$id'";
                break;
        }
    
        if (!$result = sql_query($sql)) {
            return 0;
        }
    
        if (!$myrow = sql_fetch_row($result)) {
            return 0;
        }
    
        return 1;
    }
    
    public static function is_locked($topic)
    {
        global $NPDS_Prefix;
    
        $sql = "SELECT topic_status FROM " . $NPDS_Prefix . "forumtopics WHERE topic_id = '$topic'";
    
        if (!$r = sql_query($sql)) {
            return false;
        }
    
        if (!$m = sql_fetch_assoc($r)) {
            return false;
        }
    
        if (($m['topic_status'] == 1) or ($m['topic_status'] == 2)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function smilie($message)
    {
        // Tranforme un :-) en IMG
        global $theme;
    
        if ($ibid = theme::theme_image("forum/smilies/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/";
        } else {
            $imgtmp = "assets/images/forum/smilies/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
    
            foreach ($smilies as $tab_smilies) {
                $suffix = strtoLower(substr(strrchr($tab_smilies[1], '.'), 1));
    
                if (($suffix == "gif") or ($suffix == "png")) {
                    $message = str_replace($tab_smilies[0], "<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $message);
                } else {
                    $message = str_replace($tab_smilies[0], $tab_smilies[1], $message);
                }
            }
        }
    
        if ($ibid = theme::theme_image("forum/smilies/more/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/more/";
        } else {
            $imgtmp = "assets/images/forum/smilies/more/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
            
            foreach ($smilies as $tab_smilies) {
                $message = str_replace($tab_smilies[0], "<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $message);
            }
        }
    
        return $message;
    }
    
    public static function smile($message)
    {
        // Tranforme une IMG en :-)
        global $theme;
    
        if ($ibid = theme::theme_image("forum/smilies/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/";
        } else {
            $imgtmp = "assets/images/forum/smilies/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
            
            foreach ($smilies as $tab_smilies) {
                $message = str_replace("<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $tab_smilies[0], $message);
            }
        }
    
        if ($ibid = theme::theme_image("forum/smilies/more/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/more/";
        } else {
            $imgtmp = "assets/images/forum/smilies/more/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
            
            foreach ($smilies as $tab_smilies) {
                $message = str_replace("<img class='n-smil' src='" . $imgtmp . $tab_smilies[1] . "' loading='lazy' />", $tab_smilies[0],  $message);
            }
        }
    
        return $message;
    }
    
    #autodoc aff_video_yt($ibid) : analyse et génère un tag à la volée pour les video youtube,vimeo, dailymotion $ibid - JPB 01-2011/18
    public static function aff_video_yt($ibid)
    {
        $videoprovider = array('yt', 'vm', 'dm');
    
        foreach ($videoprovider as $v) {
            $pasfin = true;
    
            while ($pasfin) {
                $pos_deb = strpos($ibid, "[video_$v]", 0);
                $pos_fin = strpos($ibid, "[/video_$v]", 0);
    
                // ne pas confondre la position ZERO et NON TROUVE !
                if ($pos_deb === false) {
                    $pos_deb = -1;
                }
    
                if ($pos_fin === false) {
                    $pos_fin = -1;
                }
    
                if (($pos_deb >= 0) and ($pos_fin >= 0)) {
                    $id_vid = substr($ibid, $pos_deb + 10, ($pos_fin - $pos_deb - 10));
                    $fragment = substr($ibid, 0, $pos_deb);
                    $fragment2 = substr($ibid, ($pos_fin + 11));
    
                    switch ($v) {
                        case 'yt':
                            if (!defined('CITRON')) {
                                $ibid_code = '
                            <div class="ratio ratio-16x9 my-3">
                            <iframe src="https://www.youtube.com/embed/' . $id_vid . '?rel=0" allowfullscreen></iframe>
                            </div>';
                            } else {
                                $ibid_code = '
                            <div class="youtube_player" videoID="' . $id_vid . '"></div>';
                        }
                            break;
    
                        case 'vm':
                            if (!defined('CITRON')) {
                                $ibid_code = '
                            <div class="ratio ratio-16x9 my-3">
                                <iframe src="https://player.vimeo.com/video/' . $id_vid . '" allowfullscreen="" frameborder="0"></iframe>
                            </div>';
                            } else {
                                $ibid_code = '
                            <div class="vimeo_player" videoID="' . $id_vid . '"></div>';
                        }
                            break;
    
                        case 'dm':
                            if (!defined('CITRON')) {
                                $ibid_code = '
                            <div class="ratio ratio-16x9 my-3">
                                <iframe src="https://www.dailymotion.com/embed/video/' . $id_vid . '" allowfullscreen="" frameborder="0"></iframe>
                            </div>';
                            } else {
                                $ibid_code = '
                            <div class="dailymotion_player" videoID="' . $id_vid . '"></div>';
                        }
                            break;
                    }
    
                    $ibid = $fragment . $ibid_code . $fragment2;
                } else {
                    $pasfin = false;
                }
            }
        }
    
        return ($ibid);
    }
    
    // ne fonctionne pas dans tous les contextes car on a pas la variable du theme !?
    public static function putitems_more()
    {
        global $theme, $tmp_theme;
    
        if (stristr($_SERVER['PHP_SELF'], "more_emoticon.php")) {
            $theme = $tmp_theme;
        }
    
        echo '<p align="center">' . translate("Cliquez pour insérer des émoticons dans votre message") . '</p>';
    
        if ($ibid = theme::theme_image("forum/smilies/more/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/more/";
        } else {
            $imgtmp = "assets/images/forum/smilies/more/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
            
            echo '
            <div>';
    
            foreach ($smilies as $tab_smilies) {
                if ($tab_smilies[3]) {
                    echo '
                <span class ="d-inline-block m-2"><a href="#" onclick="javascript: DoAdd(\'true\',\'message\',\' ' . $tab_smilies[0] . '\');"><img src="' . $imgtmp . $tab_smilies[1] . '" width="32" height="32" alt="' . $tab_smilies[2];
                    
                    if ($tab_smilies[2]) {
                        echo ' => ';
                    }
    
                    echo $tab_smilies[0] . '" loading="lazy" /></a></span>';
                }
            }
            echo '
            </div>';
        }
    }
    
    #autodoc putitems($targetarea) : appel un popover pour la saisie des emoji (Unicode v13) dans un textarea défini par $targetarea
    public static function putitems($targetarea)
    {
        global $theme;
    
        echo '
        <div title="' . translate("Cliquez pour insérer des emoji dans votre message") . '" data-bs-toggle="tooltip">
            <button class="btn btn-link ps-0" type="button" id="button-textOne" data-bs-toggle="emojiPopper" data-bs-target="#' . $targetarea . '">
                <i class="far fa-smile fa-lg" aria-hidden="true"></i>
            </button>
        </div>
        <script src="assets/shared/emojipopper/js/emojiPopper.min.js"></script>
        <script type="text/javascript">
        //<![CDATA[
            $(public static function () {
                "use strict"
                var emojiPopper = $(\'[data-bs-toggle="emojiPopper"]\').emojiPopper({
                    url: "assets/shared/emojipopper/php/emojicontroller.php",
                    title:"Choisir un emoji"
                });
            });
        //]]>
        </script>';
    }
    
    public static function HTML_Add()
    {
        $affich = '
                        <div class="mt-2">
                            <a href="javascript: addText(\'&lt;b&gt;\',\'&lt;/b&gt;\');" title="' . translate("Gras") . '" data-bs-toggle="tooltip" ><i class="fa fa-bold fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;i&gt;\',\'&lt;/i&gt;\');" title="' . translate("Italique") . '" data-bs-toggle="tooltip" ><i class="fa fa-italic fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;u&gt;\',\'&lt;/u&gt;\');" title="' . translate("Souligné") . '" data-bs-toggle="tooltip" ><i class="fa fa-underline fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;span style=\\\'text-decoration:line-through;\\\'&gt;\',\'&lt;/span&gt;\');" title="" data-bs-toggle="tooltip" ><i class="fa fa-strikethrough fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p class=\\\'text-start\\\'&gt;\',\'&lt;/p&gt;\');" title="' . translate("Texte aligné à gauche") . '" data-bs-toggle="tooltip" ><i class="fa fa-align-left fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p class=\\\'text-center\\\'&gt;\',\'&lt;/p&gt;\');" title="' . translate("Texte centré") . '" data-bs-toggle="tooltip" ><i class="fa fa-align-center fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p class=\\\'text-end\\\'&gt;\',\'&lt;/p&gt;\');" title="' . translate("Texte aligné à droite") . '" data-bs-toggle="tooltip" ><i class="fa fa-align-right fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p align=\\\'justify\\\'&gt;\',\'&lt;/p&gt;\');" title="' . translate("Texte justifié") . '" data-bs-toggle="tooltip" ><i class="fa fa-align-justify fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;ul&gt;&lt;li&gt;\',\'&lt;/li&gt;&lt;/ul&gt;\');" title="' . translate("Liste non ordonnnée") . '" data-bs-toggle="tooltip" ><i class="fa fa-list-ul fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;ol&gt;&lt;li&gt;\',\'&lt;/li&gt;&lt;/ol&gt;\');" title="' . translate("Liste ordonnnée") . '" data-bs-toggle="tooltip" ><i class="fa fa-list-ol fa-lg me-2 mb-3"></i></a>
                            <div class="dropdown d-inline me-2 mb-3" title="' . translate("Lien web") . '" data-bs-toggle="tooltip" data-bs-placement="left">
                                <a class=" dropdown-toggle" href="#" role="button" id="protocoletype" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-link fa-lg"></i></a>
                                <div class="dropdown-menu" aria-labelledby="protocoletype">
                                <a class="dropdown-item" href="javascript: addText(\' http://\',\'\');">http</a>
                                <a class="dropdown-item" href="javascript: addText(\' https://\',\'\');">https</a>
                                <a class="dropdown-item" href="javascript: addText(\' ftp://\',\'\');">ftp</a>
                                <a class="dropdown-item" href="javascript: addText(\' sftp://\',\'\');">sftp</a>
                                </div>
                            </div>
                            <a href="javascript: addText(\'&lt;table class=\\\'table table-bordered table-striped table-sm\\\'&gt;&lt;thead&gt;&lt;tr&gt;&lt;th&gt;&lt;/th&gt;&lt;th&gt;&lt;/th&gt;&lt;th&gt;&lt;/th&gt;&lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;\',\'\'); " title="' . translate("Tableau") . '" data-bs-toggle="tooltip"><i class="fa fa-table fa-lg me-2 mb-3"></i></a>
                            <div class="dropdown d-inline me-2 mb-3" title="' . translate("Code") . '" data-bs-toggle="tooltip" data-bs-placement="left">
                                <a class=" dropdown-toggle" href="#" role="button" id="codeclasslanguage" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-code fa-lg"></i></a>
                                <div class="dropdown-menu" aria-labelledby="codeclasslanguage">
                                <h6 class="dropdown-header">Languages</h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript: addText(\'&lt;pre&gt;[code markup]\',\'[/code]&lt;/pre&gt;\');">Markup</a>
                                <a class="dropdown-item" href="javascript: addText(\'&lt;pre&gt;[code php]\',\'[/code]&lt;/pre&gt;\');">Php</a>
                                <a class="dropdown-item" href="javascript: addText(\'&lt;pre&gt;[code css]\',\'[/code]&lt;/pre&gt;\');">Css</a>
                                <a class="dropdown-item" href="javascript: addText(\'&lt;pre&gt;[code js]\',\'[/code]&lt;/pre&gt;\');">js</a>
                                <a class="dropdown-item" href="javascript: addText(\'&lt;pre&gt;[code sql]\',\'[/code]&lt;/pre&gt;\');">SQL</a>
                                </div>
                            </div>
                            <div class="dropdown d-inline me-2 mb-3" title="' . translate("Vidéos") . '" data-bs-toggle="tooltip" data-bs-placement="left">
                                <a class=" dropdown-toggle" href="#" role="button" id="typevideo" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-film fa-lg"></i></a>
                                <div class="dropdown-menu" aria-labelledby="typevideo">
                                <p class="dropdown-header">' . translate("Coller l'ID de votre vidéo entre les deux balises") . ' : <br />[video_yt]xxxx[/video_yt]<br />[video_vm]xxxx[/video_vm]<br />[video_dm]xxxx[/video_dm]</p>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript: addText(\'[video_yt]\',\'[/video_yt]\');"><i class="fab fa-youtube fa-lg fa-fw me-1"></i>Youtube</a>
                                <a class="dropdown-item" href="javascript: addText(\'[video_vm]\',\'[/video_vm]\');"><i class="fab fa-vimeo fa-lg fa-fw me-1"></i>Vimeo</a>
                                <a class="dropdown-item" href="javascript: addText(\'[video_dm]\',\'[/video_dm]\');"><i class="fas fa-video fa-fw fa-lg me-1"></i>Dailymotion</a>
                                </div>
                            </div>
                        </div>';
    
        return $affich;
    }
    
    public static function emotion_add($image_subject)
    {
        global $theme;
    
        if ($ibid = theme::theme_image('forum/subject/index.html')) {
            $imgtmp = "themes/$theme/images/forum/subject";
        } else {
            $imgtmp = 'assets/images/forum/subject';
        }
    
        $handle = opendir($imgtmp);
        
        while (false !== ($file = readdir($handle))) {
            $filelist[] = $file;
        }
    
        asort($filelist);
    
        $temp = '';
        $j = 0;
    
        foreach ($filelist as $key => $file) {
            if (!preg_match('#\.gif|\.jpg|\.png$#i', $file)) {
                continue;
            }
            
            $temp .= '<div class="form-check form-check-inline mb-3">';
            
            if ($image_subject != '') {
                if ($file == $image_subject) {
                    $temp .= '
                    <input type="radio" value="' . $file . '" id="image_subject' . $j . '" name="image_subject" class="form-check-input" checked="checked" />';
                } else {
                    $temp .= '
                    <input type="radio" value="' . $file . '" id="image_subject' . $j . '" name="image_subject" class="form-check-input" />';
                }
            } else {
                $temp .= '
                    <input type="radio" value="' . $file . '" id="image_subject' . $j . '" name="image_subject" class="form-check-input" checked="checked" />';
                $image_subject = 'no image';
            }
    
            $temp .= '<label class="form-check-label" for="image_subject' . $j . '" ><img class="n-smil d-block" src="' . $imgtmp . '/' . $file . '" alt="" loading="lazy" /></label>
                </div>';
            $j++;
        }
    
        return $temp;
    }
    
    public static function make_clickable($text)
    {
        $ret = '';
        $ret = preg_replace('#(^|\s)(http|https|ftp|sftp)(://)([^\s]*)#i', ' <a href="$2$3$4" target="_blank">$2$3$4</a>', $text);
        $ret = preg_replace_callback('#([_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4})#i', [mailler::class, 'fakedmail'], $ret);
    
        return $ret;
    }
    
    public static function undo_htmlspecialchars($input)
    {
        $input = preg_replace("/&gt;/i", ">", $input);
        $input = preg_replace("/&lt;/i", "<", $input);
        $input = preg_replace("/&quot;/i", "\"", $input);
        $input = preg_replace("/&amp;/i", "&", $input);
    
        return $input;
    }
    
    public static function searchblock()
    {
        $ibid = '
                <form class="row" id="searchblock" action="searchbb.php" method="post" name="forum_search">
                    <input type="hidden" name="addterm" value="any" />
                    <input type="hidden" name="sortby" value="0" />
                    <div class="col">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="term" id="term" placeholder="' . translate('Recherche') . '" required="required" />
                        <label for="term"><i class="fa fa-search fa-lg me-2"></i>' . translate('Recherche') . '</label>
                    </div>
                    </div>
                </form>';
    
        return $ibid;
    }
    
    public static function member_qualif($poster, $posts, $rank)
    {
        global $anonymous;
    
        $tmp = '';
    
        if ($ibid = theme::theme_image('forum/rank/post.gif')) { 
            $imgtmpP = $ibid;
        } else {
            $imgtmpP = 'assets/images/forum/rank/post.gif';
        }
    
        $tmp = '<img class="n-smil" src="' . $imgtmpP . '" alt="" loading="lazy" />' . $posts . '&nbsp;';
    
        if ($poster != $anonymous) {
            $nux = 0;
    
            if ($posts >= 10 and $posts < 30) { 
                $nux = 1;
            }
    
            if ($posts >= 30 and $posts < 100) {
                $nux = 2;
            }
    
            if ($posts >= 100 and $posts < 300) {
                $nux = 3;
            }
    
            if ($posts >= 300 and $posts < 1000) { 
                $nux = 4;
            }
    
            if ($posts >= 1000) {
                $nux = 5;
            }
    
            for ($i = 0; $i < $nux; $i++) {
                $tmp .= '<i class="fa fa-star-o text-success me-1"></i>';
            }
    
            if ($rank) {
                if ($ibid = theme::theme_image("forum/rank/" . $rank . ".gif") or $ibid = theme::theme_image("forum/rank/" . $rank . ".png")) { 
                    $imgtmpA = $ibid;
                } else {
                    $imgtmpA = "assets/images/forum/rank/" . $rank . ".png";
                }
                
                $rank = 'rank' . $rank;
    
                global $$rank;
                $tmp .= '<div class="my-2"><img class="n-smil" src="' . $imgtmpA . '" alt="logo rôle" loading="lazy" />&nbsp;' . language::aff_langue($$rank) . '</div>';
            }
        }
    
        return $tmp;
    }
    
    public static function forumerror($e_code)
    {
        global $sitename, $header;
        if ($e_code == "0001") {
            $error_msg = translate("Pas de connexion à la base forums.");
        }
    
        if ($e_code == "0002") {
            $error_msg = translate("Le forum sélectionné n'existe pas.");
        }
    
        if ($e_code == "0004") {
            $error_msg = translate("Pas de connexion à la base topics.");
        }
    
        if ($e_code == "0005") {
            $error_msg = translate("Erreur lors de la récupération des messages depuis la base.");
        }
    
        if ($e_code == "0006") {
            $error_msg = translate("Entrer votre pseudonyme et votre mot de passe.");
        }
    
        if ($e_code == "0007") {
            $error_msg = translate("Vous n'êtes pas le modérateur de ce forum, vous ne pouvez utiliser cette fonction.");
        }
    
        if ($e_code == "0008") {
            $error_msg = translate("Mot de passe erroné, refaites un essai.");
        }
    
        if ($e_code == "0009") {
            $error_msg = translate("Suppression du message impossible.");
        }
    
        if ($e_code == "0010") {
            $error_msg = translate("Impossible de déplacer le topic dans le Forum, refaites un essai.");
        }
    
        if ($e_code == "0011") {
            $error_msg = translate("Impossible de verrouiller le topic, refaites un essai.");
        }
    
        if ($e_code == "0012") {
            $error_msg = translate("Impossible de déverrouiller le topic, refaites un essai.");
        }
    
        if ($e_code == "0013") {
            $error_msg = translate("Impossible d'interroger la base.") . "<br />Error: sql_error()";
        }
    
        if ($e_code == "0014") {
            $error_msg = translate("Utilisateur ou message inexistant dans la base.");
        }
    
        if ($e_code == "0015") {
            $error_msg = translate("Le moteur de recherche ne trouve pas la base forum.");
        }
    
        if ($e_code == "0016") {
            $error_msg = translate("Cet utilisateur n'existe pas, refaites un essai.");
        }
    
        if ($e_code == "0017") {
            $error_msg = translate("Vous devez obligatoirement saisir un sujet, refaites un essai.");
        }
    
        if ($e_code == "0018") {
            $error_msg = translate("Vous devez choisir un icône pour votre message, refaites un essai.");
        }
    
        if ($e_code == "0019") {
            $error_msg = translate("Message vide interdit, refaites un essai.");
        }
    
        if ($e_code == "0020") {
            $error_msg = translate("Mise à jour de la base impossible, refaites un essai.");
        }
    
        if ($e_code == "0021") {
            $error_msg = translate("Suppression du message sélectionné impossible.");
        }
    
        if ($e_code == "0022") {
            $error_msg = translate("Une erreur est survenue lors de l'interrogation de la base.");
        }
    
        if ($e_code == "0023") {
            $error_msg = translate("Le message sélectionné n'existe pas dans la base forum.");
        }
    
        if ($e_code == "0024") {
            $error_msg = translate("Vous ne pouvez répondre à ce message, vous n'en êtes pas le destinataire.");
        }
        
        if ($e_code == "0025") {
            $error_msg = translate("Vous ne pouvez répondre à ce topic il est verrouillé. Contacter l'administrateur du site.");
        }
    
        if ($e_code == "0026") {
            $error_msg = translate("Le forum ou le topic que vous tentez de publier n'existe pas, refaites un essai.");
        }
    
        if ($e_code == "0027") {
            $error_msg = translate("Vous devez vous identifier.");
        }
    
        if ($e_code == "0028") {
            $error_msg = translate("Mot de passe erroné, refaites un essai.");
        }
    
        if ($e_code == "0029") {
            $error_msg = translate("Mise à jour du compteur des envois impossible.");
        }
    
        if ($e_code == "0030") {
            $error_msg = translate("Le forum dans lequel vous tentez de publier n'existe pas, merci de recommencez");
        }
    
        if ($e_code == "0031") {
            return (0);
        }
    
        if ($e_code == "0035") {
            $error_msg = translate("Vous ne pouvez éditer ce message, vous n'en êtes pas le destinataire.");
        }
    
        if ($e_code == "0036") {
            $error_msg = translate("Vous n'avez pas l'autorisation d'éditer ce message.");
        }
    
        if ($e_code == "0037") {
            $error_msg = translate("Votre mot de passe est erroné ou vous n'avez pas l'autorisation d'éditer ce message, refaites un essai.");
        }
    
        if ($e_code == "0101") {
            $error_msg = translate("Vous ne pouvez répondre à ce message.");
        }
    
        if (!isset($header)){
            include("themes/default/header.php");
        }
    
        echo '
        <div class="alert alert-danger"><strong>' . $sitename . '<br />' . translate("Erreur du forum") . '</strong><br />';
        echo translate("Code d'erreur :") . ' ' . $e_code . '<br /><br />';
        echo $error_msg . '<br /><br />';
        echo '<a href="javascript:history.go(-1)" class="btn btn-secondary">' . translate("Retour en arrière") . '</a><br /></div>';
    
        include("themes/default/footer.php");
    
        die('');
    }
    
    public static function control_efface_post($apli, $post_id, $topic_id, $IdForum)
    {
        global $upload_table;
        global $NPDS_Prefix;
    
        include("modules/upload/include_forum/upload.conf.forum.php");
    
        $sql1 = "SELECT att_id, att_name, att_path FROM " . $NPDS_Prefix . "$upload_table WHERE apli='$apli' AND";
        $sql2 = "DELETE FROM " . $NPDS_Prefix . "$upload_table WHERE apli='$apli' AND";
    
        if ($IdForum != '') {
            $sql1 .= " forum_id = '$IdForum'";
            $sql2 .= " forum_id = '$IdForum'";
        } elseif ($post_id != '') {
            $sql1 .= " post_id = '$post_id'";
            $sql2 .= " post_id = '$post_id'";
        } elseif ($topic_id != '') {
            $sql1 .= " topic_id = '$topic_id'";
            $sql2 .= " topic_id = '$topic_id'";
        }
    
        $result = sql_query($sql1);
    
        while (list($att_id, $att_name, $att_path) = sql_fetch_row($result)) {
            $fic = $DOCUMENTROOT . $att_path . $att_id . "." . $apli . "." . $att_name;
            @unlink($fic);
        }
    
        @sql_query($sql2);
    }
    
    public static function autorize()
    {
        global $IdPost, $IdTopic, $IdForum, $user, $NPDS_Prefix;
    
        list($poster_id) = sql_fetch_row(sql_query("SELECT poster_id FROM " . $NPDS_Prefix . "posts WHERE post_id='$IdPost' AND topic_id='$IdTopic'"));
    
        $Mmod = false;
    
        if ($poster_id) {
            $myrow = sql_fetch_assoc(sql_query("SELECT forum_moderator FROM " . $NPDS_Prefix . "forums WHERE (forum_id='$IdForum')"));
            
            if ($myrow) {
                $moderator = static::get_moderator($myrow['forum_moderator']);
                $moderator = explode(' ', $moderator);
                
                if (isset($user)) {
                    $userX = base64_decode($user);
                    $userdata = explode(":", $userX);
                    
                    for ($i = 0; $i < count($moderator); $i++) {
                        if (($userdata[1] == $moderator[$i])) {
                            $Mmod = true;
                            break;
                        }
                    }
    
                    if ($userdata[0] == $poster_id) {
                        $Mmod = true;
                    }
                }
            }
        }
    
        return $Mmod;
    }
    
    public static function anti_flood($modoX, $paramAFX, $poster_ipX, $userdataX, $gmtX)
    {
        // anti_flood : nb de post dans les 90 puis 30 dernières minutes / les modérateurs echappent à cette règle
        // security.log est utilisée pour enregistrer les tentatives
        global $NPDS_Prefix, $anonymous;
    
        $compte = !array_key_exists('uname', $userdataX) ? $anonymous : $userdataX['uname'];
    
        if ((!$modoX) and ($paramAFX > 0)) {
            $sql = "SELECT COUNT(poster_ip) AS total FROM " . $NPDS_Prefix . "posts WHERE post_time>'";
            
            $sql2 = $userdataX['uid'] != 1 ?
                "' AND (poster_ip='$poster_ipX' OR poster_id='" . $userdataX['uid'] . "')" :
                "' AND poster_ip='$poster_ipX'";
            
                $timebase = date("Y-m-d H:i", time() + ($gmtX * 3600) - 5400);
            list($time90) = sql_fetch_row(sql_query($sql . $timebase . $sql2));
    
            if ($time90 > ($paramAFX * 2)) {
                logs::Ecr_Log("security", "Forum Anti-Flood : " . $compte, '');
                
                static::forumerror(translate("Vous n'êtes pas autorisé à participer à ce forum"));
            } else {
                $timebase = date("Y-m-d H:i", time() + ($gmtX * 3600) - 1800);
                list($time30) = sql_fetch_row(sql_query($sql . $timebase . $sql2));
                
                if ($time30 > $paramAFX) {
                    logs::Ecr_Log("security", "Forum Anti-Flood : " . $compte, '');
                    
                    static::forumerror(translate("Vous n'êtes pas autorisé à participer à ce forum"));
                }
            }
        }
    }
    
    public static function forum($rowQ1)
    {
        global $user, $subscribe, $theme, $NPDS_Prefix, $admin, $adminforum;
    
        //==> droits des admin sur les forums (superadmin et admin avec droit gestion forum)
        $adminforum = false;
        if ($admin) {
            $adminX = base64_decode($admin);
            $adminR = explode(':', $adminX);
    
            $Q = sql_fetch_assoc(sql_query("SELECT * FROM " . $NPDS_Prefix . "authors WHERE aid='$adminR[0]' LIMIT 1"));
            
            if ($Q['radminsuper'] == 1) {
                $adminforum = 1;
            } else {
                $R = sql_query("SELECT fnom, fid, radminsuper FROM " . $NPDS_Prefix . "authors a LEFT JOIN " . $NPDS_Prefix . "droits d ON a.aid = d.d_aut_aid LEFT JOIN " . $NPDS_Prefix . "fonctions f ON d.d_fon_fid = f.fid WHERE a.aid='$adminR[0]' AND f.fid BETWEEN 13 AND 15");
                
                if (sql_num_rows($R) >= 1) {
                    $adminforum = 1;
                }
            }
        }
        //<== droits des admin sur les forums (superadmin et admin avec droit gestion forum)
    
        if ($user) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);
            $tab_groupe = groupe::valid_group($user);
        }
    
        if ($ibid = theme::theme_image("forum/icons/red_folder.gif")) {
            $imgtmpR = $ibid;
        } else {
            $imgtmpR = "assets/images/forum/icons/red_folder.gif";
        }
    
        if ($ibid = theme::theme_image("forum/icons/folder.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = "assets/images/forum/icons/folder.gif";
        }
    
        // preparation de la gestion des folders
        $result = sql_query("SELECT forum_id, COUNT(topic_id) AS total FROM " . $NPDS_Prefix . "forumtopics GROUP BY (forum_id)");
        while (list($forumid, $total) = sql_fetch_row($result)) {
            $tab_folder[$forumid][0] = $total; // Topic
        }
    
        $result = sql_query("SELECT forum_id, COUNT(DISTINCT topicid) AS total FROM " . $NPDS_Prefix . "forum_read WHERE uid='$userR[0]' AND topicid>'0' AND status!='0' GROUP BY (forum_id)");
        while (list($forumid, $total) = sql_fetch_row($result)) {
            $tab_folder[$forumid][1] = $total; // Folder
        }
    
        // préparation de la gestion des abonnements
        $result = sql_query("SELECT forumid FROM " . $NPDS_Prefix . "subscribe WHERE uid='$userR[0]'");
        while (list($forumid) = sql_fetch_row($result)) {
            $tab_subscribe[$forumid] = true;
        }
    
        // preparation du compteur total_post
        $rowQ0 = cache::Q_Select("SELECT forum_id, COUNT(post_aff) AS total FROM " . $NPDS_Prefix . "posts GROUP BY forum_id", 600);
        foreach ($rowQ0 as $row0) {
            $tab_total_post[$row0['forum_id']] = $row0['total'];
        }
    
        $ibid = '';
    
        if ($rowQ1) {
            foreach ($rowQ1 as $row) {
                $title_aff = true;
                
                $rowQ2 = cache::Q_Select("SELECT * FROM " . $NPDS_Prefix . "forums WHERE cat_id = '" . $row['cat_id'] . "' AND SUBSTRING(forum_name,1,3)!='<!>' ORDER BY forum_index,forum_id", 21600);
               
                if ($rowQ2) {
                    foreach ($rowQ2 as $myrow) {
                        
                        // Gestion des Forums Cachés aux non-membres
                        if (($myrow['forum_type'] != "9") or ($userR)) {
                            
                            // Gestion des Forums réservés à un groupe de membre
                            if (($myrow['forum_type'] == "7") or ($myrow['forum_type'] == "5")) {
                                $ok_affich = groupe::groupe_forum($myrow['forum_pass'], $tab_groupe);
                                
                                if ((isset($admin)) and ($adminforum == 1)) {
                                    $ok_affich = true; // to see when admin mais pas assez precis
                                }
                            } else {
                                $ok_affich = true; }
                            
                            if ($ok_affich) {
                                if ($title_aff) {
                                    $title = stripslashes($row['cat_title']);
                                    if ((file_exists("themes/$theme/view/forum-cat" . $row['cat_id'] . ".html")) or (file_exists("themes/default/view/forum-cat" . $row['cat_id'] . ".html"))) {
                                        $ibid .= '
                                <div class=" mt-3" id="catfo_' . $row['cat_id'] . '" >
                                    <a class="list-group-item list-group-item-action active" href="forum.php?catid=' . $row['cat_id'] . '"><h5>' . $title . '</h5></a>';
                                    } else {
                                        $ibid .= '
                                <div class=" mt-3" id="catfo_' . $row['cat_id'] . '">
                                    <div class="list-group-item list-group-item-action active"><h5>' . $title . '</h5></div>';
                                    }
    
                                    $title_aff = false;
                                }
    
                                $forum_moderator = explode(' ', static::get_moderator($myrow['forum_moderator']));
                                $Mmod = false;
    
                                for ($i = 0; $i < count($forum_moderator); $i++) {
                                    if (($userR[1] == $forum_moderator[$i])) {
                                        $Mmod = true;
                                    }
                                }
    
                                $last_post = static::get_last_post($myrow['forum_id'], "forum", "infos", $Mmod);
                                $ibid .= '
                                    <p class="mb-0 list-group-item list-group-item-action flex-column align-items-start">
                                        <span class="d-flex w-100 mt-1">';
    
                                if (($tab_folder[$myrow['forum_id']][0] - $tab_folder[$myrow['forum_id']][1]) > 0) {
                                    $ibid .= '<i class="fa fa-folder text-primary fa-lg me-2 mt-1" title="' . translate("Les nouvelles contributions depuis votre dernière visite.") . '" data-bs-toggle="tooltip" data-bs-placement="right"></i>';
                                } else {
                                    $ibid .= '<i class="far fa-folder text-primary fa-lg me-2 mt-1" title="' . translate("Aucune nouvelle contribution depuis votre dernière visite.") . '" data-bs-toggle="tooltip" data-bs-placement="right"></i>';
                                }
    
                                $name = stripslashes($myrow['forum_name']);
                                $redirect = false;
    
                                if (strstr(strtoupper($name), "<a HREF")) {
                                    $redirect = true;
                                } else {
                                    $ibid .= '
                                        <a href="viewforum.php?forum=' . $myrow['forum_id'] . '" >' . $name . '</a>';
                                    }
    
                                if (!$redirect) {
                                    $ibid .= '
                                        <span class="ms-auto"> 
                                            <span class="badge bg-secondary ms-1" title="' . translate("Contributions") . '" data-bs-toggle="tooltip">' . $tab_total_post[$myrow['forum_id']] . '</span>
                                            <span class="badge bg-secondary ms-1" title="' . translate("Sujets") . '" data-bs-toggle="tooltip">' . $tab_folder[$myrow['forum_id']][0] . '</span>
                                        </span>
                                    </span>';
                                }
    
                                $desc = stripslashes(metalang::meta_lang($myrow['forum_desc']));
    
                                if ($desc != '') {
                                    $ibid .= '<span class="d-flex w-100 mt-1">' . $desc . '</span>';
                                }
    
                                if (!$redirect) {
                                    $ibid .= '<span class="d-flex w-100 mt-1"> [ ';
    
                                    if ($myrow['forum_access'] == "0" && $myrow['forum_type'] == "0") {
                                        $ibid .= translate("Accessible à tous");
                                    }
    
                                    if ($myrow['forum_type'] == "1") {
                                        $ibid .= translate("Privé");
                                    }
    
                                    if ($myrow['forum_type'] == "5") {
                                        $ibid .= "PHP Script + " . translate("Groupe");
                                    }
    
                                    if ($myrow['forum_type'] == "6") {
                                        $ibid .= "PHP Script";
                                    }
    
                                    if ($myrow['forum_type'] == "7") {
                                        $ibid .= translate("Groupe");
                                    }
    
                                    if ($myrow['forum_type'] == "8") {
                                        $ibid .= translate("Texte étendu");
                                    }
    
                                    if ($myrow['forum_type'] == "9") {
                                        $ibid .= translate("Caché");
                                    }
    
                                    if ($myrow['forum_access'] == "1" && $myrow['forum_type'] == "0") {
                                        $ibid .= translate("Utilisateur enregistré");}
    
                                    if ($myrow['forum_access'] == "2" && $myrow['forum_type'] == "0") {
                                        $ibid .= translate("Modérateur");
                                    }
    
                                    if ($myrow['forum_access'] == "9") {
                                        $ibid .= '<span class="text-danger mx-2"><i class="fa fa-lock me-2"></i>' . translate("Fermé") . '</span>';
                                    }
    
                                    $ibid .= ' ] </span>';
    
                                    // Subscribe
                                    if (($subscribe) and ($user)) {
                                        if (!$redirect) {
                                            if (mailler::isbadmailuser($userR[0]) === false) { //proto
                                                $ibid .= '
                                <span class="d-flex w-100 mt-1" >
                                <span class="form-check">';
    
                                                if (!isset($tab_subscribe[$myrow['forum_id']])) { // ajout isset bug $tab_subscribe non definie
                                                    $ibid .= '
                                    <input class="form-check-input n-ckbf" type="checkbox" id="subforumid' . $myrow['forum_id'] . '" name="Subforumid[' . $myrow['forum_id'] . ']" checked="checked" />';
                                                } else {
                                                    $ibid .= '
                                    <input class="form-check-input n-ckbf" type="checkbox" id="subforumid' . $myrow['forum_id'] . '" name="Subforumid[' . $myrow['forum_id'] . ']" />';
                                                }
    
                                                $ibid .= '
                                    <label class="form-check-label" for="subforumid' . $myrow['forum_id'] . '" title="' . translate("Cochez et cliquez sur le bouton OK pour recevoir un Email lors d'une nouvelle soumission dans ce forum.") . '" data-bs-toggle="tooltip" data-bs-placement="right">&nbsp;&nbsp;</label>
                                    </span>
                                </span>';
                                            }
                                        }
                                    }
    
                                    $ibid .= '<span class="d-flex w-100 justify-content-end"><span class="small">' . translate("Dernière contribution") . ' : ' . $last_post . '</span></span>';
                                } else {
                                    $ibid .= '';
                                }
                            }
                        }
                    }
    
                    if (($ok_affich == false and $title_aff == false) or $ok_affich == true) {
                        $ibid .= '
                                </p>
                                </div>';
                    }
                }
            }
        }
    
        if (($subscribe) and ($user) and ($ok_affich)) {
            if (mailler::isbadmailuser($userR[0]) === false) { //proto
                $ibid .= '
            <div class="form-check mt-1">
                <input class="form-check-input" type="checkbox" id="ckball_f" />
                <label class="form-check-label text-muted" for="ckball_f" id="ckb_status_f">Tout cocher</label>
            </div>';
            }
        }
    
        return $ibid;
    }
    
    // fonction appelée par le meta-mot forum_subfolder()
    public static function sub_forum_folder($forum)
    {
        global $user, $NPDS_Prefix;
    
        if ($user) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);
        }
    
        $result = sql_query("SELECT COUNT(topic_id) AS total FROM " . $NPDS_Prefix . "forumtopics WHERE forum_id='$forum'");
        list($totalT) = sql_fetch_row($result);
    
        $result = sql_query("SELECT COUNT(DISTINCT topicid) AS total FROM " . $NPDS_Prefix . "forum_read WHERE uid='$userR[0]' AND topicid>'0' AND status!='0' AND forum_id='$forum'");
        list($totalF) = sql_fetch_row($result);
    
        if ($ibid = theme::theme_image("forum/icons/red_sub_folder.gif")) {
            $imgtmpR = $ibid;
        } else {
            $imgtmpR = "assets/images/forum/icons/red_sub_folder.gif";
        }
    
        if ($ibid = theme::theme_image("forum/icons/sub_folder.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = "assets/images/forum/icons/sub_folder.gif";
        }
    
        if (($totalT - $totalF) > 0) {
            $ibid = '<img src="' . $imgtmpR . '" alt="" loading="lazy" />';
        } else {
            $ibid = '<img src="' . $imgtmp . '" alt="" loading="lazy" />';
        }
    
        return $ibid;
    }
}