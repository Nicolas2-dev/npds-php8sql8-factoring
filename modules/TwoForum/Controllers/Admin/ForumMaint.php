<?php

namespace Modules\TwoForum\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class ForumMaint extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'forummaint';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'MaintForumAdmin';

        $this->f_titre = __d('two_forum', 'Maintenance des Forums');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_', ''));
    }

    /**
     * [ForumMaintMarkTopics description]
     *
     * @return  void
     */
    function ForumMaintMarkTopics(): void 
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('forummaint'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <h3>'. __d('two_forum', 'Marquer tous les Topics comme lus') .'</h3>
        <table data-toggle="table" data-striped="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Topics ID</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

        if (!DB::table('forum_read')->delete()) {
            forum::forumerror('0001');
        } else {
            $time_actu = time() + ((int) Config::get('npds.gmt') * 3600);

            $forums = DB::table('forums')->select('forum_id')->orderBy('forum_id', 'asc')->get();

            foreach ($forums as $forum) {
                echo '
                <tr>
                    <td align="center">'. $forum['forum_id'] .'</td>
                    <td align="left">';

                $forumtopic = DB::table('forumtopics')->select('topic_id')->where('forum_id', $forum['forum_id'])->orderBy('topic_id', 'asc')->get();

                foreach ($forumtopic as $ftopic) {
                    $users = DB::table('users')->select('uid')->orderBy('uid', 'desc')->get();

                    foreach ($users as $user) {  
                        if ($user['uid'] > 1) {
                            DB::table('forum_read')->insert(array(
                                'forum_id'      => $forum['forum_id'],
                                'topicid'       => $ftopic['topic_id'],
                                'uid'           => $user['uid'],
                                'last_read'     => $time_actu,
                                'status'        => 1,
                            ));
                        
                        }
                    }

                    echo $ftopic['topic_id'] .' ';
                }

                echo '
                    </td>
                    <td align="center">'. __d('two_forum', 'Ok') .'</td>
                </tr>';
            }
        }

        echo '
        </tbody>
        </table>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [ForumMaintTopics description]
     *
     * @param   string  $before      [$before description]
     * @param   string  $forum_name  [$forum_name description]
     *
     * @return  void
     */
    function ForumMaintTopics(string $before, string $forum_name): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('forummaint'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="text-danger">'. __d('two_forum', 'Supprimer massivement les Topics') .'</h3>';

        if ($before != '') {
            echo '&nbsp;<span class="text-danger">< '. $before .'</span>';
            $topic_check = ' checked="checked"';
        } else {
            $topic_check = '';
        }

        echo '
        <form action="'. site_url('admin.php') .'" method="post">';

        $query = DB::table('forums')->select('forum_id', 'forum_name');

        if ($forum_name != '') {
            $query->where('forum_name', $forum_name);
        }

        $forums = $query->orderBy('forum_id', 'asc')->get();

        foreach ($forums as $forum) {
            echo '
            <h4>'. $forum['forum_name'] .'</h4>
            <div class="mb-3 border p-4">';

            $query = DB::table('forumtopics')->select('topic_id', 'topic_title')->where('forum_id', $forum['forum_id']);
            
            if ($before != '') {
                $query->where('topic_time', '>', $before);
            }
            
            $forumtopic = $query->orderBy('topic_id', 'asc')->get();

            foreach ($forumtopic as $ftopic) {

                $topic_title = ((Config::get('npds.parse') == 0) ? str::FixQuotes($ftopic['topic_title']) : stripslashes($ftopic['topic_title']));
                
                echo '
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="topics['. $ftopic['topic_id'] .']" id="topics'. $ftopic['topic_id'] .'" '. $topic_check .'/>
                    <label class="form-check-label" for="topics'. $ftopic['topic_id'] .'"><a href="'. site_url('admin.php?op=MaintForumTopicDetail&amp;topic='. $ftopic['topic_id'] .'&amp;topic_title='. $topic_title) .'" data-bs-toggle="tooltip" title="'. $topic_title .'" >'. $ftopic['topic_id'] .'</a></label>
                </div>';
            }

            echo '</div>';
        }

        echo '
            <div class="mb-3>"
                <input type="hidden" name="op" value="ForumMaintTopicMassiveSup" />
                <input class="btn btn-danger" type="submit" name="Topics_Del" value="'. __d('two_forum', 'Supprimer massivement les Topics') .'" />
            </div>
        </form>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [ForumMaintTopicDetail description]
     *
     * @param   int     $topic        [$topic description]
     * @param   string  $topic_title  [$topic_title description]
     *
     * @return  void
     */
    function ForumMaintTopicDetail(int $topic, string $topic_title): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('forummaint'));
        adminhead($f_meta_nom, $f_titre);

        $post = DB::table('posts')
            ->select('post_text', 'post_time')
            ->where('topic_id', $topic)
            ->orderBy('post_time', 'desc')
            ->limite(1)
            ->offset(0)
            ->first();

        echo '
        <hr />
        <h3 class="mb-3 text-danger">'. __d('two_forum', 'Supprimer massivement les Topics') .'</h3>
        <div class="lead">Topic : '. $topic .' | '. stripslashes($post['topic_title']) .'</div>
        <div class="card p-4 my-3 border-danger">
            <p class="text-end small text-muted">[ '. date::convertdate($post['post_time']) .' ]</p>'. stripslashes($post['post_text']) .'
        </div>
        <form action="'. site_url('admin.php') .'" method="post">
            <input type="hidden" name="op" value="ForumMaintTopicSup" />
            <input type="hidden" name="topic" value="'. $topic .'" />
            <input class="btn btn-danger" type="submit" name="Topics_Del" value="'. __d('two_forum', 'Effacer') .'" />
        </form>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [ForumMaintTopicMassiveSup description]
     *
     * @param   array   $topics  [$topics description]
     *
     * @return  void
     */
    function ForumMaintTopicMassiveSup(array $topics): void
    {
        if ($topics) {
            foreach ($topics as $topic_id => $value) {
                
                if ($value == 'on') {
                    $r = DB::table('posts')->where('topic_id', $topic_id)->delete();

                    if (!$r) {
                        forum::forumerror('0009');
                    }
                    
                    $r = DB::table('forumtopics')->where('topic_id', $topic_id)->delete();

                    if (!$r) {
                        forum::forumerror('0010');
                    }

                    $r = DB::table('forum_read')->where('topicid', $topic_id)->delete();

                    if (!$r) {
                        forum::forumerror('0001');
                    }

                    forum::control_efface_post("forum_npds", "", $topic_id, "");
                }
            }
        }

        cache::Q_Clean();

        header('location: '. site_url('admin.php?op=MaintForumAdmin'));
    }

    /**
     * [ForumMaintTopicSup description]
     *
     * @param   int   $topic  [$topic description]
     *
     * @return  void
     */
    function ForumMaintTopicSup(int $topic): void
    {
        if (!DB::table('posts')->where('topic_id', $topic)->delete()) {
            forum::forumerror('0009');
        }

        if (!DB::table('forumtopics')->where('topic_id', $topic)->delete()) {
            forum::forumerror('0010');
        }

        if (!DB::table('forum_read')->where('topicid', $topic)->delete()) {
            forum::forumerror('0001');
        }

        forum::control_efface_post("forum_npds", "", $topic, "");

        cache::Q_Clean();

        header('location: '. site_url('admin.php?op=MaintForumTopics'));
    }

    /**
     * [SynchroForum description]
     *
     * @return  void
     */
    function SynchroForum(): void 
    {
        // affectation d'un topic à un forum
        $result = DB::table('forumtopics')->select('topic_id', 'forum_id')->orderBy('topic_id', 'asc')->get();

        if (!$result) {
            forum::forumerror('0009');
        }

        foreach ($result as $forumtopic) {
            DB::table('posts')->where('topic_id', $forumtopic['topic_id'])->where('forum_id', '>', 0)->update(array(
                'forum_id'  => $forumtopic['forum_id'],
            ));
        }

        // table forum_read et contenu des topic
        $result = DB::table('forum_read')->select('topicid', 'uid', 'rid')->orderBy('topicid', 'asc')->get();

        if (!$result) {
            forum::forumerror('0009');
        }

        foreach ($result as $forum_read) {
            
            // a controller et confirmer !!!!
            //$tmp = $topicid . $uid;
            /// ???????? $tmp do not exist
            // if (($forumtopic['topicid'] . $forum_read['uid']) == $tmp) {
            //    DB::table('forum_read')->where('topicid', $forum_read['topicid'])->where('uid', $forum_read['uid'])->where('rid', $forum_read['rid'])->delete();
            // }

            if ($result = DB::table('forumtopics')->select('topic_id')->where('topic_id', $forum_read['topicid'])->first()) {
                if (!$result['topic_id']) {
                    DB::table('forum_read')->where('topicid', $forum_read['topicid'])->delete();
                }
            }
        }

        header('location: '. site_url('admin.php?op=MaintForumAdmin'));
    }

    /**
     * [MergeForum description]
     *
     * @return  void
     */
    function MergeForum(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('forummaint'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr/>
        <h3 class="mb-3">'. __d('two_forum', 'Fusionner des forums') .'</h3>
        <form id="fad_mergeforum" action="'. site_url('admin.php') .'" method="post">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="oriforum">'. __d('two_forum', 'Forum d\'origine') .'</label>
                    <div class="col-sm-8">
                    <select class="form-select" id="oriforum" name="oriforum">';

        $result = DB::table('forums')->select('forum_id', 'forum_name')->orderBy('forum_index, forum_id')->get();

        if ($result) {
            foreach($result as $myrow) {
                if ($myrow['forum_id']) {
                    echo '<option value="'. $myrow['forum_id'] .'">'. $myrow['forum_name'] .'</option>';
                } else {
                    echo '<option value="-1">'. __d('two_forum', 'No More Forums') .'</option>';
                }
            }
        } else {
            echo '<option value="-1">Database Error</option>';
        }

        echo '
                    </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="destforum">'. __d('two_forum', 'Forum de destination') .'</label>
                    <div class="col-sm-8">
                    <select class="form-select" id="destforum" name="destforum">';

        if ($result) {
            foreach($result as $myrow) {
                if ($myrow['forum_id']) {
                    echo '<option value="'. $myrow['forum_id'] .'">'. $myrow['forum_name'] .'</option>';
                } else {
                    echo '<option value="-1">'. __d('two_forum', 'No More Forums') .'</option>';
                }
            }
        } else {
            echo '<option value="-1">Database Error</option>';
        }

        echo '
                    </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="op" value="MergeForumAction" />
                    <button class="btn btn-primary col-12" type="submit" name="Merge_Forum_Action">'. __d('two_forum', 'Fusionner') .'</button>
                    </div>
                </div>
            </fieldset>
        </form>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [MergeForumAction description]
     *
     * @param   int   $oriforum   [$oriforum description]
     * @param   int   $destforum  [$destforum description]
     *
     * @return  void
     */
    function MergeForumAction(int $oriforum, int $destforum): void
    {
        global $upload_table;

        $r = DB::table('forumtopics')->where('forum_id', $oriforum)->update(array(
            'forum_id'  => $destforum,
        ));

        if (!$r) {
            forum::forumerror('0010');
        }

        $r = DB::table('posts')->where('forum_id', $oriforum)->update(array(
            'forum_id'  => $destforum,
        ));

        if (!$r) {
            forum::forumerror('0010');
        }

        $r = DB::table('forum_read')->where('forum_id', $oriforum)->update(array(
            'forum_id'  => $destforum,
        ));

        if (!$r) {
            forum::forumerror('0001');
        }

        DB::table($upload_table)->where('apli', 'forum_npds')->where('forum_id', $oriforum)->update(array(
            'forum_id'  => $destforum,
        ));

        cache::Q_Clean();

        header('location: '. site_url('admin.php?op=MaintForumAdmin'));
    }

    /**
     * [ForumMaintAdmin description]
     *
     * @return  void
     */
    function ForumMaintAdmin(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('forummaint'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_forum', 'Maintenance des Forums') .'</h3>';

        // Mark Topics, Synchro Forum_read table, Merge Forums
        echo '
        <div class="row">
            <div class="col-12">
                <form id="fad_forumaction" action="'. site_url('admin.php') .'" method="post">
                    <input type="hidden" name="op" value="MaintForumMarkTopics" />
                    <button class="btn btn-primary btn-block mt-1" type="submit" name="Topics_Mark"><i class="far fa-check-square fa-lg"></i>&nbsp;'. __d('two_forum', 'Marquer tous les Topics comme lus') .'</button>
                </form>
            </div>
            <div class="col-12">
                <form action="'. site_url('admin.php') .'" method="post">
                    <input type="hidden" name="op" value="SynchroForum" />
                    <button class="btn btn-primary btn-block mt-1 " type="submit" name="Synchro_Forum"><i class="fas fa-sync fa-lg"></i>&nbsp;'. __d('two_forum', 'Synchroniser les forums') .'</button>
                </form>
            </div>
            <div class="col-12">
                <form action="'. site_url('admin.php') .'" method="post">
                    <input type="hidden" name="op" value="MergeForum" />
                    <button class="btn btn-primary btn-block mt-1" type="submit" name="Merge_Forum"><i class="fa fa-compress fa-lg"></i>&nbsp;'. __d('two_forum', 'Fusionner des forums') .'</button>
                </form>
            </div>
        </div>
        <h3 class="my-3">'. __d('two_forum', 'Supprimer massivement les Topics') .'</h3>
        <form id="faddeletetop" action="'. site_url('admin.php') .'" method="post" autocomplete="nope" >
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="titreforum">'. __d('two_forum', 'Nom du forum') .'</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="forum_name" id="titreforum" maxlength="150" autocomplete="nope" placeholder="   " />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="before">'. __d('two_forum', 'Date') .'</label>
                <div class="col-sm-8">
                    <div class="input-group">
                    <span id="datePicker" class="input-group-text bg-light date"><i class="far fa-calendar-check fa-lg"></i></span>
                    <input type="text" class="form-control" name="before" id="before" />
                    </div>
                    <span class="help-block text-end">Avant cette date !</span>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="op" value="MaintForumTopics" />
                    <button class="btn btn-primary" type="submit" name="Topics_Mark">'. __d('two_forum', 'Envoyer') .'</button>
                </div>
            </div>
        </form>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/flatpickr.min.js"></script>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/l10n/'. language::language_iso(1, '', '') .'.js"></script>
        <script type="text/javascript">
        //<![CDATA[
            $(document).ready(function() {
                $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/flatpickr/dist/themes/npds.css"});
            })
        //]]>
        </script>';

        $fv_parametres = '
            before:{},
            !###!
            flatpickr("#before", {
                altInput: true,
                altFormat: "l j F Y",
                dateFormat:"Y-m-d",
                "locale": "'. language::language_iso(1, '', '') .'",
                onChange: function() {
                    fvitem.revalidateField(\'before\');
                }
            });';

        $arg1 = '
        var formulid = ["faddeletetop"];';
        
        echo js::auto_complete('forname', 'forum_name', 'forums', 'titreforum', 86400);

        css::adminfoot('fv', $fv_parametres, $arg1, '');
    }

}