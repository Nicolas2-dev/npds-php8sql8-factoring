<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Cache;
use Two\Support\Facades\Config;
use Modules\TwoCore\Support\Sanitize;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoGroupes\Support\Facades\Groupe;


if (!function_exists('RecentForumPosts')) {
    /**
     * Bloc Forums
     *
     * syntaxe : function#RecentForumPosts
     * nb_max_forum (O=tous) nb_max_topic, affiche_l'emetteur(true / false), topic_nb_max_char, affiche_HR(true / false)
     *
     * @param   string  $title          [$title description]
     * @param   int     $maxforums      [$maxforums description]
     * @param   int     $maxtopics      [$maxtopics description]
     * @param   bool    $displayposter  [$displayposter description]
     * @param   false                   [ description]
     * @param   int     $topicmaxchars  [$topicmaxchars description]
     * @param   bool    $hr             [$hr description]
     * @param   false                   [ description]
     * @param   string  $decoration     [$decoration description]
     *
     * @return  void                    [return description]
     */
    function RecentForumPosts(string $title, int $maxforums, int $maxtopics, bool $displayposter = false, int $topicmaxchars = 15, bool $hr = false, string $decoration = ''): void
    {
        $boxstuff = RecentForumPosts_fab($title, $maxforums, $maxtopics, $displayposter, $topicmaxchars, $hr, $decoration);

        global $block_title;

        if ($title == '') {
            $title = $block_title == '' ? __d('two_forum', 'Forums infos') : $block_title;
        }

        Theme::themesidebox($title, $boxstuff);
    }
}

if (!function_exists('RecentForumPosts_fab')) {
    /**
     * 
     *
     * @param   string  $title          [$title description]
     * @param   int     $maxforums      [$maxforums description]
     * @param   int     $maxtopics      [$maxtopics description]
     * @param   bool    $displayposter  [$displayposter description]
     * @param   int     $topicmaxchars  [$topicmaxchars description]
     * @param   bool    $hr             [$hr description]
     * @param   string  $decoration     [$decoration description]
     *
     * @return  string                  [return description]
     */
    function RecentForumPosts_fab(string $title, int $maxforums, int $maxtopics, bool $displayposter, int $topicmaxchars, bool $hr, string $decoration): string
    {
        $query = DB::table('forums')->select('*');

        $user = User::getUser();

        if (!$user) {
            $query->where('forum_type', '!=', 9)
                ->where('forum_type', '!=', 7)
                ->where('forum_type', '!=', 5);
        }

        $query->orderBy('cat_id', 'forum_index', 'forum_id');

        if ($maxforums > 0) {
            $query->limit($maxforums);
        }

        if (!$result = $query->get()) {
            exit();
        }

        $boxstuff = '<ul>';

        foreach ($result as $row) {

            if (($row->forum_type == "5") or ($row->forum_type == "7")) {
                $ok_affich = false;
                $tab_groupe = Groupe::valid_group($user);
                $ok_affich = Groupe::groupe_forum($row->forum_pass, $tab_groupe);
            } else {
                $ok_affich = true;
            }

            if ($ok_affich) {
                $forumid    = $row->forum_id;
                $forumname  = $row->forum_name;
                $forum_desc = $row->forum_desc;

                if ($hr) {
                    $boxstuff .= '<li><hr /></li>';
                }

                if (Config::get('two_core::config.parse') == 0) {
                    $forumname  = Sanitize::FixQuotes($forumname);
                    $forum_desc = Sanitize::FixQuotes($forum_desc);
                } else {
                    $forumname  = stripslashes($forumname);
                    $forum_desc = stripslashes($forum_desc);
                }

                $res_forumtopics = DB::table('forumtopics')
                    ->select('*')
                    ->where('forum_id', $forumid)
                    ->orderBy('topic_time', 'desc')
                    ->get();

                $boxstuff .= '<li class="list-unstyled border-0 p-2 mt-1">
                      <h6>
                          <a href="' . site_url('viewforum.php?forum=' . $forumid) . '" title="' . strip_tags($forum_desc) . '" data-bs-toggle="tooltip">
                              ' . $forumname . '
                          </a>
                          <span class="float-end badge bg-secondary" title="' . __d('two_forum', 'Sujets') . '" data-bs-toggle="tooltip">
                              ' . count($res_forumtopics) . '
                          </span>
                      </h6>
                  </li>';

                $topics = 0;
                foreach ($res_forumtopics as $topicrow) {
                    if ($topics < $maxtopics) {

                        $postquery = DB::table('posts')
                            ->select(DB::raw('COUNT(*) AS total'))
                            ->where('topic_id', $topicrow->topic_id)
                            ->first();

                        if ($postquery) {
                            $replies = $postquery->total;
                        } else {
                            $replies = 0;
                        }

                        $topic_title = $topicrow->topic_title;

                        if (strlen($topic_title) > $topicmaxchars) {
                            $topic_title = substr($topic_title, 0, $topicmaxchars);
                            $topic_title .= '..';
                        }

                        if ($displayposter) {
                            $posterid = $topicrow->topic_poster;

                            $RowQ1 = Cache::remember('users', 3600, function () use ($posterid) {
                                return DB::table('users')
                                    ->select('uname')
                                    ->where('uid', $posterid)
                                    ->first();
                            });


                            $postername = $RowQ1->uname;
                        }

                        if (Config::get('two_core::config.parse') == 0) {
                            $topictitle = Sanitize::FixQuotes($topic_title);
                        } else {
                            $topictitle = stripslashes($topic_title);
                        }

                        $boxstuff .= '<li class="list-group-item p-1 border-right-0 border-left-0 list-group-item-action">
                              <div class="n-ellipses">
                                  <span class="badge bg-secondary mx-2" title="' . __d('two_forum', 'Réponses') . '" data-bs-toggle="tooltip" data-bs-placement="top">
                                      ' . $replies . '
                                  </span>
                                  <a href="' . site_url('viewtopic.php?topic=' . $topicrow->topic_id . '&amp;forum=' . $forumid) . '" >
                                      ' . $topictitle . '
                                  </a>
                              </div>';

                        if ($displayposter) {
                            $boxstuff .= $decoration . '<span class="ms-1">' . $postername . '</span>';
                        }

                        $boxstuff .= '</li>';

                        $topics++;
                    }
                }
            }
        }

        $boxstuff .= '</ul>';

        return $boxstuff;
    }
}
