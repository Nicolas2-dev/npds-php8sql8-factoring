<?php

declare(strict_types=1);

namespace Modules\TwoForum\Library;


use Two\Support\Facades\DB;
use Two\Support\Facades\Cache;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\TwoError;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoCore\Support\Facades\Mailer;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoCore\Support\Facades\Metalang;
use Modules\TwoAuthors\Support\Facades\Author;
use Modules\TwoGroupes\Support\Facades\Groupe;


class forum
{
    
    /**
     * 
     *
     * @param   int    $forum_id  [$forum_id description]
     *
     * @return  array
     */
    public static function get_total_topics(int $forum_id): array
    {
        $result = DB::table('forumtopics')
                    ->select(DB::raw('COUNT(*) AS total'))
                    ->where('forum_id', $forum_id)
                    ->first();

        if (!$result) {
            return "ERROR";
        }
    
        return $result['total'];
    }
    
    /**
     * Retourne une chaine des id des contributeurs du sujet
     *
     * @param   string        [ description]
     * @param   int     $fid  [$fid description]
     * @param   string        [ description]
     * @param   int     $tid  [$tid description]
     *
     * @return  string
     */
    public static function get_contributeurs(string|int $fid, string|int $tid): string 
    {
        $posterids = '';
        
        foreach (Cache::Q_Select3(DB::table('posts')
            ->distinct()
            ->select('poster_id')
            ->where('topic_id', $tid)
            ->where('forum_id', $fid)
            ->get(), 2, Crypt::encrypt('post(poster_id)')) as $contribs) 
        {
            foreach ($contribs as $contrib) {
                $posterids .= $contrib . ' ';
            }
        }
    
        return chop($posterids);
    }
    
    /**
     * 
     *
     * @param   string         [ description]
     * @param   int     $fid   [$fid description]
     * @param   string         [ description]
     * @param   int     $tid   [$tid description]
     * @param   string         [ description]
     * @param   int     $type  [$type description]
     * @param   bool    $Mmod  [$Mmod description]
     *
     * @return  int
     */
    public static function get_total_posts(string|int $fid, string|int $tid, string|int $type, bool $Mmod): int
    {
        switch ($type) {
            case 'forum':
                $query = DB::table('posts')
                            ->select(DB::raw('COUNT(*) AS total'))
                            ->where('forum_id', $fid);
                break;
    
            case 'topic':
                $query = DB::table('posts')
                            ->select(DB::raw('COUNT(*) AS total'))
                            ->where('topic_id', $tid)
                            ->where('forum_id', $fid);
                break;
    
            case 'user':
                TwoError::code('0031');
        }
    
        if (!$Mmod) {
            $query->where('post_aff', 1);
        }

        if (!$result = $query->first()) {
            return 0;
        }

        return $result['total'];
    }

    /**
     * 
     *
     * @param   string         [ description]
     * @param   int     $id    [$id description]
     * @param   string         [ description]
     * @param   int     $type  [$type description]
     * @param   string  $cmd   [$cmd description]
     *
     * @return  string
     */
    public static function get_last_post(string|int $id, string|int $type, string $cmd): string 
    {
        // $Mmod ne sert plus - maintenu pour compatibilité
        switch ($type) {
            case 'forum':
                $sql1= DB::table('forumtopics')
                        ->select('topic_time', 'current_poster')
                        ->where('forum_id', $id)
                        ->orderBy('topic_time', 'desc')
                        ->limit(1)
                        ->offset(0)
                        ->first();
                break;
    
            case 'topic':
                $sql1 = DB::table('forumtopics')
                            ->select('topic_time', 'current_poster')
                            ->where('topic_id', $id)
                            ->first();
                break;
        }
    
        if ($cmd == 'infos') {
            if (!$myrow = $sql1) { 
                $val = __d('two_forum', 'Rien');
            } else {
                $rowQ1 = Cache::Q_Select3(
                    DB::table('users')
                        ->select('uname')
                        ->where('uid', $myrow['current_poster'])
                        ->first(), 3600, Crypt::encrypt('user(uname_uid)'));

                $val = convertdate($myrow['topic_time']);
                $val .= $rowQ1 ? ' '. userpopover($rowQ1['uname'], 40, 2) : '';
            }
        }
    
        return $val;
    }

    /**
     * 
     *
     * @param   string  $user_id  [$user_id description]
     *
     * @return  string
     */
    public static function get_moderator(string $user_id): string 
    {
        if ($user_id == 0) {
            return "None";
        }
    
        $query = DB::table('users')->select('uname');

        $count_id = 0;
        foreach (explode(',', $user_id) as $user) {
            if ($count_id == 0) {
                $query->where('uid', $user);
            } else {
                $query->orWhere('uid', $user);
            }
            $count_id++;
        }

        $rowQ1 = Cache::Q_Select3(
            $query->where('uname', '!=' , 'Anonyme')
                  ->get(), 3600, Crypt::encrypt('user(uid_uname)')
        );

        $modslist = '';

        foreach ($rowQ1 as $modnames) {
            foreach ($modnames as $modname) {
                $modslist .= $modname . ' ';
            }
        }

        return chop($modslist);
    }

    /**
     * [user_is_moderator description]
     *
     * @param   string                  [ description]
     * @param   int     $uidX           [$uidX description]
     * @param   string  $passwordX      [$passwordX description]
     * @param   string                  [ description]
     * @param   int     $forum_accessX  [$forum_accessX description]
     *
     * @return  int|bool
     */
    public static function user_is_moderator(string|int $uidX, string $passwordX, string|int $forum_accessX): int|bool
    {
        $user = DB::table('users')
                    ->select('pass')
                    ->where('uid', $uidX)
                    ->first();

        $user_status = DB::table('users_status')
                    ->select('level')
                    ->where('uid', $uidX)
                    ->first();

        if ((md5($user['pass']) == $passwordX) and ($forum_accessX <= $user_status['level']) and ($user_status['level'] > 1)) {
            return $user_status['level'];
        } else {
            return false;
        }
    }
    
    /**
     * 
     *
     * @param   string|int  $userid  [$userid description]
     *
     * @return  array
     */
    public static function get_userdata_from_id(string|int $userid): array
    {
        $sql1 = DB::table('users')
                    ->select('*')
                    ->where('uid', $userid)
                    ->first();

        $sql2 = DB::table('users_status')
                    ->select('*')
                    ->where('uid', $userid)
                    ->first();

        if (!$sql1) {
            TwoError::code('0016');
        }

        if (!$sql1) {
            $myrow = array("uid" => 1);
        } else {
            $myrow = array_merge($sql1, (array) $sql2);
        }

        return $myrow;
    }
    
    /**
     * 
     *
     * @param   string  $userid  [$userid description]
     *
     * @return  array
     */
    public static function get_userdata_extend_from_id( $userid): array 
    {
        $sql1 = DB::table('users_extend')
                    ->select('*')
                    ->where('uid', $userid)
                    ->first();

        if (!$sql1) { 
            TwoError::code('0016');
        }

        return $sql1;
    }
    
    /**
     * 
     *
     * @param   string  $username  [$username description]
     *
     * @return  array
     */
    public static function get_userdata(string $username): array
    {
        $sql = DB::table('users')
                    ->select('*')
                    ->where('uname', $username)
                    ->first();

        if (!$sql) {
            TwoError::code('0016');
        }
    
        if (!$myrow = $sql) {
            $myrow = array("uid" => 1);
        }
    
        return (array) $myrow;
    }
    
    /**
     * 
     *
     * @param   string         [ description]
     * @param   int     $id    [$id description]
     * @param   string         [ description]
     * @param   int     $type  [$type description]
     *
     * @return  int
     */
    public static function does_exists(string|int $id, string|int $type): int
    {
        switch ($type) {
            case 'forum':
                $sql = DB::table('forums')
                            ->select('forum_id')
                            ->where('forum_id', $id)
                            ->first();
                break;
    
            case 'topic':
                $sql = DB::table('forumtopics')
                            ->select('topic_id')
                            ->where('topic_id', $id)
                            ->first();
                break;
        }
    
        if (!$sql) {
            return 0;
        }
    
        return 1;
    }
    
    /**
     * 
     *
     * @param   string          [ description]
     * @param   int     $topic  [$topic description]
     *
     * @return  bool
     */
    public static function is_locked(string|int $topic): bool
    {
        $sql = DB::table('forumtopics')
                    ->select('topic_status')
                    ->where('topic_id', $topic)
                    ->first();

        if (!$sql) {
            return false;
        }
    
        if (($sql['topic_status'] == 1) or ($sql['topic_status'] == 2)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 
     *
     * @param   string  $message  [$message description]
     *
     * @return  string
     */
    public static function smilie(string $message): string
    {
        $theme = Theme::getTheme();
    
        if (Theme::theme_image("forum/smilies/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/";
        } else {
            $imgtmp = "assets/images/forum/smilies/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
    
            foreach ($smilies as $tab_smilies) {
                $suffix = strtoLower(substr(strrchr($tab_smilies[1], '.'), 1));
    
                if (($suffix == "gif") or ($suffix == "png")) {
                    $message = str_replace($tab_smilies[0], "<img class='n-smil' src='". $imgtmp . $tab_smilies[1] ."' loading='lazy' />", $message);
                } else {
                    $message = str_replace($tab_smilies[0], $tab_smilies[1], $message);
                }
            }
        }
    
        if (Theme::theme_image("forum/smilies/more/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/more/";
        } else {
            $imgtmp = "assets/images/forum/smilies/more/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
            
            foreach ($smilies as $tab_smilies) {
                $message = str_replace($tab_smilies[0], "<img class='n-smil' src='". $imgtmp . $tab_smilies[1] ."' loading='lazy' />", $message);
            }
        }
    
        return $message;
    }
    
    /**
     * 
     *
     * @param   string  $message  [$message description]
     *
     * @return  string
     */
    public static function smile(string $message): string 
    {
        $theme = Theme::getTheme();
    
        if (Theme::theme_image("forum/smilies/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/";
        } else {
            $imgtmp = "assets/images/forum/smilies/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
            
            foreach ($smilies as $tab_smilies) {
                $message = str_replace("<img class='n-smil' src='". $imgtmp . $tab_smilies[1] ."' loading='lazy' />", $tab_smilies[0], $message);
            }
        }
    
        if (Theme::theme_image("forum/smilies/more/smilies.php")) {
            $imgtmp = "themes/$theme/images/forum/smilies/more/";
        } else {
            $imgtmp = "assets/images/forum/smilies/more/";
        }
    
        if (file_exists($imgtmp . "smilies.php")) {
            include($imgtmp . "smilies.php");
            
            foreach ($smilies as $tab_smilies) {
                $message = str_replace("<img class='n-smil' src='". $imgtmp . $tab_smilies[1] ."' loading='lazy' />", $tab_smilies[0],  $message);
            }
        }
    
        return $message;
    }
     
    /**
     * analyse et génère un tag à la volée pour les video youtube,vimeo, dailymotion $ibid - JPB 01-2011/18
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  string         [return description]
     */
    public static function aff_video_yt(string $ibid): string 
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
    
        return $ibid;
    }
    
    /**
     * ne fonctionne pas dans tous les contextes car on a pas la variable du theme !?
     *
     * @return  void    [return description]
     */
    public static function putitems_more(): void
    {
        $theme = Theme::getTheme();

        if (stristr($_SERVER['PHP_SELF'], "more_emoticon.php")) {
            $theme = $theme;
        }
    
        echo '<p align="center">' . __d('two_forum', 'Cliquez pour insérer des émoticons dans votre message') . '</p>';
    
        if (Theme::theme_image("forum/smilies/more/smilies.php")) {
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
                <span class ="d-inline-block m-2"><a href="#" onclick="javascript: DoAdd(\'true\',\'message\',\' '. $tab_smilies[0] .'\');"><img src="'. $imgtmp . $tab_smilies[1] .'" width="32" height="32" alt="'. $tab_smilies[2];
                    
                    if ($tab_smilies[2]) {
                        echo ' => ';
                    }
    
                    echo $tab_smilies[0] .'" loading="lazy" /></a></span>';
                }
            }
            echo '
            </div>';
        }
    }
    
    /**
     * appel un popover pour la saisie des emoji (Unicode v13) dans un textarea défini par $targetarea
     *
     * @param   string  $targetarea  [$targetarea description]
     *
     * @return  void
     */
    public static function putitems(string $targetarea): void
    {
        echo '
        <div title="'. __d('two_forum', 'Cliquez pour insérer des emoji dans votre message') .'" data-bs-toggle="tooltip">
            <button class="btn btn-link ps-0" type="button" id="button-textOne" data-bs-toggle="emojiPopper" data-bs-target="#'. $targetarea .'">
                <i class="far fa-smile fa-lg" aria-hidden="true"></i>
            </button>
        </div>
        <script src="assets/shared/emojipopper/js/emojiPopper.min.js"></script>
        <script type="text/javascript">
        //<![CDATA[
            $(function() {
                "use strict"
                var emojiPopper = $(\'[data-bs-toggle="emojiPopper"]\').emojiPopper({
                    url: "assets/shared/emojipopper/php/emojicontroller.php",
                    title:"Choisir un emoji"
                });
            });
        //]]>
        </script>';
    }
    
    /**
     * 
     *
     * @return  string  [return description]
     */
    public static function HTML_Add(): string
    {
        $affich = '
                        <div class="mt-2">
                            <a href="javascript: addText(\'&lt;b&gt;\',\'&lt;/b&gt;\');" title="'. __d('two_forum', 'Gras') .'" data-bs-toggle="tooltip" ><i class="fa fa-bold fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;i&gt;\',\'&lt;/i&gt;\');" title="'. __d('two_forum', 'Italique') .'" data-bs-toggle="tooltip" ><i class="fa fa-italic fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;u&gt;\',\'&lt;/u&gt;\');" title="'. __d('two_forum', 'Souligné') .'" data-bs-toggle="tooltip" ><i class="fa fa-underline fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;span style=\\\'text-decoration:line-through;\\\'&gt;\',\'&lt;/span&gt;\');" title="" data-bs-toggle="tooltip" ><i class="fa fa-strikethrough fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p class=\\\'text-start\\\'&gt;\',\'&lt;/p&gt;\');" title="'. __d('two_forum', 'Texte aligné à gauche') .'" data-bs-toggle="tooltip" ><i class="fa fa-align-left fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p class=\\\'text-center\\\'&gt;\',\'&lt;/p&gt;\');" title="'. __d('two_forum', 'Texte centré') .'" data-bs-toggle="tooltip" ><i class="fa fa-align-center fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p class=\\\'text-end\\\'&gt;\',\'&lt;/p&gt;\');" title="'. __d('two_forum', 'Texte aligné à droite') .'" data-bs-toggle="tooltip" ><i class="fa fa-align-right fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;p align=\\\'justify\\\'&gt;\',\'&lt;/p&gt;\');" title="'. __d('two_forum', 'Texte justifié') .'" data-bs-toggle="tooltip" ><i class="fa fa-align-justify fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;ul&gt;&lt;li&gt;\',\'&lt;/li&gt;&lt;/ul&gt;\');" title="'. __d('two_forum', 'Liste non ordonnnée') .'" data-bs-toggle="tooltip" ><i class="fa fa-list-ul fa-lg me-2 mb-3"></i></a>
                            <a href="javascript: addText(\'&lt;ol&gt;&lt;li&gt;\',\'&lt;/li&gt;&lt;/ol&gt;\');" title="'. __d('two_forum', 'Liste ordonnnée') .'" data-bs-toggle="tooltip" ><i class="fa fa-list-ol fa-lg me-2 mb-3"></i></a>
                            <div class="dropdown d-inline me-2 mb-3" title="'. __d('two_forum', 'Lien web') .'" data-bs-toggle="tooltip" data-bs-placement="left">
                                <a class=" dropdown-toggle" href="#" role="button" id="protocoletype" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-link fa-lg"></i></a>
                                <div class="dropdown-menu" aria-labelledby="protocoletype">
                                <a class="dropdown-item" href="javascript: addText(\' http://\',\'\');">http</a>
                                <a class="dropdown-item" href="javascript: addText(\' https://\',\'\');">https</a>
                                <a class="dropdown-item" href="javascript: addText(\' ftp://\',\'\');">ftp</a>
                                <a class="dropdown-item" href="javascript: addText(\' sftp://\',\'\');">sftp</a>
                                </div>
                            </div>
                            <a href="javascript: addText(\'&lt;table class=\\\'table table-bordered table-striped table-sm\\\'&gt;&lt;thead&gt;&lt;tr&gt;&lt;th&gt;&lt;/th&gt;&lt;th&gt;&lt;/th&gt;&lt;th&gt;&lt;/th&gt;&lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;\',\'\'); " title="' . __d('two_forum', 'Tableau') . '" data-bs-toggle="tooltip"><i class="fa fa-table fa-lg me-2 mb-3"></i></a>
                            <div class="dropdown d-inline me-2 mb-3" title="'. __d('two_forum', 'Code') .'" data-bs-toggle="tooltip" data-bs-placement="left">
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
                            <div class="dropdown d-inline me-2 mb-3" title="'. __d('two_forum', 'Vidéos') .'" data-bs-toggle="tooltip" data-bs-placement="left">
                                <a class=" dropdown-toggle" href="#" role="button" id="typevideo" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-film fa-lg"></i></a>
                                <div class="dropdown-menu" aria-labelledby="typevideo">
                                <p class="dropdown-header">'. __d('two_forum', 'Coller l\'ID de votre vidéo entre les deux balises') .' : <br />[video_yt]xxxx[/video_yt]<br />[video_vm]xxxx[/video_vm]<br />[video_dm]xxxx[/video_dm]</p>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript: addText(\'[video_yt]\',\'[/video_yt]\');"><i class="fab fa-youtube fa-lg fa-fw me-1"></i>Youtube</a>
                                <a class="dropdown-item" href="javascript: addText(\'[video_vm]\',\'[/video_vm]\');"><i class="fab fa-vimeo fa-lg fa-fw me-1"></i>Vimeo</a>
                                <a class="dropdown-item" href="javascript: addText(\'[video_dm]\',\'[/video_dm]\');"><i class="fas fa-video fa-fw fa-lg me-1"></i>Dailymotion</a>
                                </div>
                            </div>
                        </div>';
    
        return $affich;
    }
    
    /**
     * 
     *
     * @param   [type]  $image_subject  [$image_subject description]
     *
     * @return  string
     */
    public static function emotion_add($image_subject): string 
    {
        $theme = Theme::getTheme();
    
        if (Theme::theme_image('forum/subject/index.html')) {
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
                    <input type="radio" value="'. $file .'" id="image_subject'. $j .'" name="image_subject" class="form-check-input" checked="checked" />';
                } else {
                    $temp .= '
                    <input type="radio" value="'. $file .'" id="image_subject'. $j .'" name="image_subject" class="form-check-input" />';
                }
            } else {
                $temp .= '
                    <input type="radio" value="'. $file . '" id="image_subject'. $j .'" name="image_subject" class="form-check-input" checked="checked" />';
                $image_subject = 'no image';
            }
    
            $temp .= '<label class="form-check-label" for="image_subject'. $j .'" ><img class="n-smil d-block" src="'. $imgtmp .'/'. $file .'" alt="" loading="lazy" /></label>
                </div>';
            $j++;
        }
    
        return $temp;
    }
    
    /**
     * 
     *
     * @param   string  $text  [$text description]
     *
     * @return  string
     */
    public static function make_clickable(string $text): string 
    {
        $ret = '';
        $ret = preg_replace('#(^|\s)(http|https|ftp|sftp)(://)([^\s]*)#i', ' <a href="$2$3$4" target="_blank">$2$3$4</a>', $text);
        $ret = preg_replace_callback('#([_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4})#i', [Mailer::class, 'fakedmail'], $ret);
    
        return $ret;
    }
    

    
    /**
     * 
     *
     * @return  string
     */
    public static function searchblock(): string
    {
        $ibid = '
                <form class="row" id="searchblock" action="'. site_url('searchbb.php') .'" method="post" name="forum_search">
                    <input type="hidden" name="addterm" value="any" />
                    <input type="hidden" name="sortby" value="0" />
                    <div class="col">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="term" id="term" placeholder="'. __d('two_forum', 'Recherche') .'" required="required" />
                        <label for="term"><i class="fa fa-search fa-lg me-2"></i>'. __d('two_forum', 'Recherche') .'</label>
                    </div>
                    </div>
                </form>';
    
        return $ibid;
    }
    
    /**
     * 
     *
     * @param   string  $poster  [$poster description]
     * @param   int     $posts   [$posts description]
     * @param   int     $rank    [$rank description]
     *
     * @return  string
     */
    public static function member_qualif(string $poster, int $posts, int $rank): string
    {
        $tmp = '';
    
        if ($ibid = Theme::theme_image('forum/rank/post.gif')) { 
            $imgtmpP = $ibid;
        } else {
            $imgtmpP = 'assets/images/forum/rank/post.gif';
        }
    
        $tmp = '<img class="n-smil" src="'. $imgtmpP .'" alt="" loading="lazy" />'. $posts .'&nbsp;';
    
        if ($poster != Config::get('two_core::config.anonymous')) {
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
                if ($ibid = Theme::theme_image("forum/rank/" . $rank . ".gif") or $ibid = Theme::theme_image("forum/rank/" . $rank . ".png")) { 
                    $imgtmpA = $ibid;
                } else {
                    $imgtmpA = "assets/images/forum/rank/" . $rank . ".png";
                }
                
                $rank = 'rank' . $rank;
    
                global $$rank;
                $tmp .= '<div class="my-2"><img class="n-smil" src="'. $imgtmpA .'" alt="logo rôle" loading="lazy" />&nbsp;'. Language::aff_langue($$rank) .'</div>';
            }
        }
    
        return $tmp;
    }
    

    
    /**
     * 
     *
     * @param   string          $apli      [$apli description]
     * @param   string|int      $post_id   [$post_id description]
     * @param   string|int      $topic_id  [$topic_id description]
     * @param   string|int      $IdForum   [$IdForum description]
     *
     * @return  void
     */
    public static function control_efface_post(string $apli, string|int $post_id, string|int $topic_id, string|int $IdForum): void
    {
        $upload_table = Config::get('two_forum::config.upload_table');

        include("modules/upload/config/upload.conf.php");
    
        $query = DB::table($upload_table)
                    ->select('att_id', 'att_name', 'att_path')
                    ->where('apli', $apli);


        $query_delete = DB::table($upload_table)
                            ->where('apli', $apli);

        if ($IdForum != '') {
            $query->where('forum_id', $IdForum);
            $query_delete->where('forum_id', $IdForum);

        } elseif ($post_id != '') {
            $query->where('post_id', $post_id);
            $query_delete->where('post_id', $post_id);
            
        } elseif ($topic_id != '') {
            $query->where('topic_id', $topic_id); 
            $query_delete->where('topic_id', $topic_id); 
        }

        foreach($query->get() as $apli) {    
            $fic = $DOCUMENTROOT . $app['att_path'] . $app['att_id'] . "." . $apli . "." . $app['att_name'];
            @unlink($fic);
        }
    
        $query_delete->delete();
    }
    
    /**
     * 
     *
     * @return  bool
     */
    public static function autorize(): bool
    {
        global $IdPost, $IdTopic, $IdForum;
    
        $poster_id = DB::table('posts')
                        ->select('poster_id')
                        ->where('post_id', $IdPost)
                        ->where('topic_id', $IdTopic)
                        ->first();

        $Mmod = false;
    
        if ($poster_id) {
            $myrow = DB::table('forums')
                        ->select('forum_moderator')
                        ->where('forum_id', $IdForum)
                        ->first();

            if ($myrow) {
                $moderator = static::get_moderator($myrow['forum_moderator']);
                $moderator = explode(' ', $moderator);
                
                $user = User::getUser();

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
    


    /**
     * 
     *
     * @param   bool    $modoX       [$modoX description]
     * @param   string  $paramAFX    [$paramAFX description]
     * @param   string  $poster_ipX  [$poster_ipX description]
     * @param   array   $userdataX   [$userdataX description]
     * @param   int     $gmtX        [$gmtX description]
     *
     * @return  void
     */
    public static function anti_flood(int|bool $modoX, string $paramAFX, string $poster_ipX, array $userdataX, int $gmtX): void
    {
        // anti_flood : nb de post dans les 90 puis 30 dernières minutes / les modérateurs echappent à cette règle
        // security.log est utilisée pour enregistrer les tentatives
        $compte = !array_key_exists('uname', $userdataX) ? Config::get('two_core::config.anonymous') : $userdataX['uname'];
    
        if ((!$modoX) and ($paramAFX > 0)) {
            $query = DB::table('posts')->select(DB::raw('COUNT(poster_ip) AS total'));

            if ($userdataX['uid'] != 1) {
                $query->where('poster_ip', $poster_ipX)->orWhere('poster_id', '=', $userdataX['uid']);
            } else {
                $query->where('poster_ip', $poster_ipX);
            }
            
            $timebase = date("Y-m-d H:i", time() + ($gmtX * 3600) - 5400);

            $time90 = $query->where('post_time', '>', $timebase)->first();

            if ($time90['total'] > ($paramAFX * 2)) {
                Ecr_Log("security", "Forum Anti-Flood : " . $compte, '');
                
                TwoError::code(__d('two_forum', 'Vous n\'êtes pas autorisé à participer à ce forum'));
            } else {
                $timebase = date("Y-m-d H:i", time() + ($gmtX * 3600) - 1800);
                
                $time30 = $query->where('post_time', '>', $timebase)->first();

                if ($time30['total'] > $paramAFX) {
                    Ecr_Log("security", "Forum Anti-Flood : " . $compte, '');
                    
                    TwoError::code(__d('two_forum', 'Vous n\'êtes pas autorisé à participer à ce forum'));
                }
            }
        }
    }

    /**
     * 
     *
     * @param   array   $forum_categorie  [$rowQ1 description]
     *
     * @return  string
     */
    public static function forum(array $forum_categorie): string 
    {
        global $adminforum;
    
        // droits des admin sur les forums (superadmin et admin avec droit gestion forum)
        $adminforum = false;

        $admin = Author::getAdmin();

        if ($admin) {
            $adminX = base64_decode($admin);
            $adminR = explode(':', $adminX);
    
            $Q = DB::table('authors')
                    ->select('*')
                    ->where('aid', $adminR[0])
                    ->limit(1)
                    ->first();

            if ($Q['radminsuper'] == 1) {
                $adminforum = 1;
            } else {   
                $R = DB::table('authors')
                    ->select('fonctions.fnom', 'fonctions.fid', 'authors.aid', 'authors.radminsuper')
                    ->leftJoin('droits', 'authors.aid', '=', 'droits.d_aut_aid')
                    ->leftJoin('fonctions', 'droits.d_fon_fid', '=', 'fonctions.fid')
                    ->where('authors.aid', '=', $adminR[0])
                    ->where('fonctions.fid', 'BETWEEN', DB::raw('13 AND 15'))
                    ->get();

                if ($R >= 1) {
                    $adminforum = 1;
                }
            }
        }
        // droits des admin sur les forums (superadmin et admin avec droit gestion forum)
    
        $user = User::getUser();

        if ($user) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);
            $tab_groupe = Groupe::valid_group($user);
        } else {
            $userR[0] = null;
        }
    
        $tab_folder = null;

        // preparation de la gestion des folders
        foreach (DB::table('forumtopics')
            ->select('forum_id', DB::raw('COUNT(topic_id) AS total'))
            ->groupeBy('forum_id')
            ->get() as $forumtopic) 
        {
            //$tab_folder[$forumtopic['forum_id']][0] = $forumtopic['total']; // Topic
            $tab_folder[0][$forumtopic['forum_id']] = $forumtopic['total']; // Topic
        }
    
        foreach (DB::table('forum_read')
            ->select('forum_id', DB::raw('COUNT(DISTINCT topicid) AS total'))
            ->where('uid', $userR[0])
            ->where('topicid', '>', 0)
            ->where('status', '!=', 0)
            ->groupeBy('forum_id')
            ->get() as $forum_read) 
        { 
            //$tab_folder[$forum_read['forum_id']][1] = $forum_read['total']; // Folder
            $tab_folder[1][$forum_read['forum_id']] = $forum_read['total']; // Folder
        }

        // préparation de la gestion des abonnements
        foreach (DB::table('subscribe')
                ->select('forumid')
                ->where('uid', $userR[0])
                ->get() as $subscribe) 
        {
            $tab_subscribe[$subscribe['forum_id']] = true;
        }
    
        // preparation du compteur total_post
        foreach (Cache::Q_Select3(
            DB::table('posts')
                ->select('forum_id', DB::raw('COUNT(post_aff) AS total'))
                ->groupeBy('forum_id')
                ->get(), 600, Crypt::encrypt('COUNT(post_aff)')
        )  as  $posts) {
            $tab_total_post[$posts['forum_id']] = $posts['total'];
        }
    
        $ibid = '';
    
        if ($forum_categorie) {
            foreach ($forum_categorie as $row) {
                $title_aff = true;

                $forums_list = Cache::Q_Select3(
                    DB::table('forums')
                        ->select('*')
                        ->where('cat_id', $row['cat_id'])
                        ->where(DB::raw('SUBSTRING(forum_name, 1, 3)'), '!=', '<!>')
                        ->get(), 600, Crypt::encrypt('SUBSTRING(forum_name, 1, 3)')
                );

                if ($forums_list) {
                    foreach ($forums_list as $myrow) {

                        // Gestion des Forums Cachés aux non-membres
                        if (($myrow['forum_type'] != "9") or ($userR)) {
                            
                            // Gestion des Forums réservés à un groupe de membre
                            if (($myrow['forum_type'] == "7") or ($myrow['forum_type'] == "5")) {
                                $ok_affich = Groupe::groupe_forum($myrow['forum_pass'], $tab_groupe);
                                
                                if ((isset($admin)) and ($adminforum == 1)) {
                                    // à voir quand admin mais pas assez précis !!!!
                                    $ok_affich = true;
                                }
                            } else {
                                $ok_affich = true; 
                            }
                            
                            if ($ok_affich) {
                                if ($title_aff) {
                                    $title = stripslashes($row['cat_title']);

                                    $theme = Theme::getTheme();

                                    if ((file_exists("themes/$theme/view/forum-cat". $row['cat_id'] .".html")) or (file_exists("themes/default/view/forum-cat". $row['cat_id'] .".html"))) {
                                        $ibid .= '
                                        <div class=" mt-3" id="catfo_'. $row['cat_id'] .'" >
                                            <a class="list-group-item list-group-item-action active" href="'. site_url('forum.php?catid='. $row['cat_id']) .'"><h5>'. $title .'</h5></a>';
                                    } else {
                                        $ibid .= '
                                        <div class=" mt-3" id="catfo_'. $row['cat_id'] .'">
                                            <div class="list-group-item list-group-item-action active"><h5>'. $title .'</h5></div>';
                                    }
    
                                    $title_aff = false;
                                }
    
                                //$forum_moderator = explode(' ', static::get_moderator($myrow['forum_moderator']));
                                // $Mmod = false;
    
                                // for ($i = 0; $i < count($forum_moderator); $i++) {
                                //     if (($userR[1] == $forum_moderator[$i])) {
                                //         $Mmod = true;
                                //     }
                                // }
                                
                                $last_post = static::get_last_post($myrow['forum_id'], "forum", "infos");
                            
                                $ibid .= '
                                    <p class="mb-0 list-group-item list-group-item-action flex-column align-items-start">
                                        <span class="d-flex w-100 mt-1">';

                                //if (array_key_exists($myrow['forum_id'], $tab_folder[0])) {                                        
                                    //if (($tab_folder[$myrow['forum_id']][0] - $tab_folder[$myrow['forum_id']][1]) > 0) {
                                if (!empty($tab_folder[0][$myrow['forum_id']]) && !empty($tab_folder[1][$myrow['forum_id']])) {           
                                    if (($tab_folder[0][$myrow['forum_id']] - $tab_folder[1][$myrow['forum_id']]) > 0) {
                                        $ibid .= '<i class="fa fa-folder text-primary fa-lg me-2 mt-1" title="'. __d('two_forum', 'Les nouvelles contributions depuis votre dernière visite.') .'" data-bs-toggle="tooltip" data-bs-placement="right"></i>';
                                    }
                                } else {
                                    $ibid .= '<i class="far fa-folder text-primary fa-lg me-2 mt-1" title="'. __d('two_forum', 'Aucune nouvelle contribution depuis votre dernière visite.') .'" data-bs-toggle="tooltip" data-bs-placement="right"></i>';
                                }                          

                                $name = stripslashes($myrow['forum_name']);
                                $redirect = false;
    
                                if (strstr(strtoupper($name), "<a HREF")) {
                                    $redirect = true;
                                } else {
                                    $ibid .= '<a href="'. site_url('viewforum.php?forum=' . $myrow['forum_id']) . '" >' . $name . '</a>';
                                }
    
                                if (!$redirect) {
                                    $ibid .= '
                                        <span class="ms-auto">';
                                
                                    if (!empty($tab_total_post[$myrow['forum_id']])) {          
                                            $ibid .= '<span class="badge bg-secondary ms-1" title="'. __d('two_forum', 'Contributions') .'" data-bs-toggle="tooltip">'. $tab_total_post[$myrow['forum_id']] .'</span>';
                                    }            
                                        
                                    // if (!empty($tab_folder[$myrow['forum_id']][0])) {          
                                    //     $ibid .= '<span class="badge bg-secondary ms-1" title="'. __d('two_forum', 'Sujets') .'" data-bs-toggle="tooltip">'. $tab_folder[$myrow['forum_id']][0] .'</span>';
                                    // }  

                                    if (!empty($tab_folder[0][$myrow['forum_id']])) {          
                                        $ibid .= '<span class="badge bg-secondary ms-1" title="'. __d('two_forum', 'Sujets') .'" data-bs-toggle="tooltip">'. $tab_folder[0][$myrow['forum_id']] .'</span>';
                                    } 

                                    $ibid .= '</span>
                                    </span>';
                                }
    
                                $desc = stripslashes(Metalang::meta_lang($myrow['forum_desc']));
    
                                if ($desc != '') {
                                    $ibid .= '<span class="d-flex w-100 mt-1">'. $desc .'</span>';
                                }
    
                                if (!$redirect) {
                                    $ibid .= '<span class="d-flex w-100 mt-1"> [ ';
    
                                    if ($myrow['forum_access'] == "0" && $myrow['forum_type'] == "0") {
                                        $ibid .= __d('two_forum', 'Accessible à tous');
                                    }
    
                                    if ($myrow['forum_type'] == "1") {
                                        $ibid .= __d('two_forum', 'Privé');
                                    }
    
                                    if ($myrow['forum_type'] == "5") {
                                        $ibid .= "PHP Script + ". __d('two_forum', 'Groupe');
                                    }
    
                                    if ($myrow['forum_type'] == "6") {
                                        $ibid .= "PHP Script";
                                    }
    
                                    if ($myrow['forum_type'] == "7") {
                                        $ibid .= __d('two_forum', 'Groupe');
                                    }
    
                                    if ($myrow['forum_type'] == "8") {
                                        $ibid .= __d('two_forum', 'Texte étendu');
                                    }
    
                                    if ($myrow['forum_type'] == "9") {
                                        $ibid .= __d('two_forum', 'Caché');
                                    }
    
                                    if ($myrow['forum_access'] == "1" && $myrow['forum_type'] == "0") {
                                        $ibid .= __d('two_forum', 'Utilisateur enregistré');}
    
                                    if ($myrow['forum_access'] == "2" && $myrow['forum_type'] == "0") {
                                        $ibid .= __d('two_forum', 'Modérateur');
                                    }
    
                                    if ($myrow['forum_access'] == "9") {
                                        $ibid .= '<span class="text-danger mx-2"><i class="fa fa-lock me-2"></i>'. __d('two_forum', 'Fermé') .'</span>';
                                    }
    
                                    $ibid .= ' ] </span>';
    
                                    // Subscribe
                                    if ((Config::get('two_core::config.subscribe')) and ($user)) {
                                        if (!$redirect) {
                                            //proto
                                            if (Mailer::isbadmailuser($userR[0]) === false) { 
                                                $ibid .= '
                                                <span class="d-flex w-100 mt-1" >
                                                    <span class="form-check">';
    
                                                // ajout isset bug $tab_subscribe non definie    
                                                if (!isset($tab_subscribe[$myrow['forum_id']])) { 
                                                    $ibid .= '<input class="form-check-input n-ckbf" type="checkbox" id="subforumid'. $myrow['forum_id'] .'" name="Subforumid['. $myrow['forum_id'] .']" checked="checked" />';
                                                } else {
                                                    $ibid .= '<input class="form-check-input n-ckbf" type="checkbox" id="subforumid'. $myrow['forum_id'] .'" name="Subforumid['. $myrow['forum_id'] .']" />';
                                                }
    
                                                $ibid .= '<label class="form-check-label" for="subforumid'. $myrow['forum_id'] .'" title="'. __d('two_forum', 'Cochez et cliquez sur le bouton OK pour recevoir un Email lors d\'une nouvelle soumission dans ce forum.') . '" data-bs-toggle="tooltip" data-bs-placement="right">&nbsp;&nbsp;</label>
                                                    </span>
                                                </span>';

                                            }
                                        }
                                    }

                                    $ibid .= '<span class="d-flex w-100 justify-content-end"><span class="small">'. __d('two_forum', 'Dernière contribution') .' : '. $last_post .'</span></span>';
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
    
        if ((Config::get('npds.subscribe')) and ($user) and ($ok_affich)) {
            //proto
            if (Mailer::isbadmailuser($userR[0]) === false) {
                $ibid .= '
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="ckball_f" />
                    <label class="form-check-label text-muted" for="ckball_f" id="ckb_status_f">'. __d('two_forum', 'Tout cocher') .'</label>
                </div>';
            }
        }
    
        return $ibid;
    }
    
    /**
     * fonction appelée par le meta-mot forum_subfolder()
     *
     * @param   string    $forum  [$forum description]
     *
     * @return  string
     */
    public static function sub_forum_folder(string $forum): string 
    {
        $user = User::getUser();
    
        if ($user) {
            $userX = base64_decode($user);
            $userR = explode(':', $userX);
        }
    
        $totalT = DB::table('forumtopics')
                    ->select(DB::raw('COUNT(topic_id) AS total'))
                    ->where('forum_id', $forum)
                    ->first();
    
        $totalF = DB::table('forum_read')
                    ->select(DB::raw('COUNT(DISTINCT topicid) AS total'))
                    ->where('uid', $userR[0])
                    ->where('topicid', '>', 0)
                    ->where('status', '!=', 0)
                    ->where('forum_id', $forum)
                    ->first();
    
        if (($totalT['total'] - $totalF['total']) > 0) {
            $ibid = '<img src="'. Theme::theme_image_row('forum/icons/red_sub_folder.gif', 'assets/images/forum/icons/red_sub_folder.gif') .'" alt="" loading="lazy" />';
        } else {
            $ibid = '<img src="'. Theme::theme_image_row('forum/icons/sub_folder.gif', 'assets/images/forum/icons/sub_folder.gif') .'" alt="" loading="lazy" />';
        }
    
        return $ibid;
    }
}
