<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class GroupeForum extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'groupes';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'groupes';

        $this->f_titre = __d('two_groupes', 'Gestion des groupes');

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
     * [forum_groupe_create description]
     *
     * @param   int     $groupe_id    [$groupe_id description]
     * @param   string  $groupe_name  [$groupe_name description]
     * @param   string  $description  [$description description]
     * @param   int     $moder        [$moder description]
     *
     * @return  void
     */
    function forum_groupe_create(int $groupe_id, string $groupe_name, string $description, int $moder): void
    {
        // creation forum
        // creation catégorie forum_groupe
        $catagories = DB::table('catagories')->select('cat_id')->where('cat_id', -1)->first();

        if (!$catagories['cat_id']) {
            DB::table('catagories')->insert(array(
                'cat_id'       => -1,
                'cat_title'    => __d('two_groupes', 'Groupe de travail'),
            ));

        }
        //==>creation forum

        //echo "$groupe_id, $groupe_name, $description, $moder";

        DB::table('forums')->insert(array(
            'forum_name'        => $groupe_name,
            'forum_desc'        => $description,
            'forum_access'      => 1,
            'forum_moderateur'  => $moder,
            'cat_id'            => -1,
            'forum_type'        => 7,
            'forum_pass'        => $groupe_id,
            'arbre'             => 0,
            'attachement'       => 0,
            'forum_index'       => 0,
        ));

        //=> ajout etat forum (1 ou 0) dans le groupe
        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_forum'  => 1,
        ));

        global $aid;
        logs::Ecr_Log("security", "CreateForumWS($groupe_id) by AID : $aid", '');
    }

    /**
     * [moderateur_update description]
     *
     * @param   int     $forum_id         [$forum_id description]
     * @param   string  $forum_moderator  [$forum_moderator description]
     *
     * @return  void
     */
    function moderateur_update(int $forum_id, string $forum_moderator): void
    {
        DB::table('forums')->where('forum_id', $forum_id)->update(array(
            'forum_moderator'   => $forum_moderator,
        ));
    }

    /**
     * [forum_groupe_delete description]
     *
     * @param   int   $groupe_id  [$groupe_id description]
     *
     * @return  void
     */
    function forum_groupe_delete(int $groupe_id): void
    {
        $forum = DB::table('forums')->select('forum_id')->where('forum_pass', $groupe_id)->where('cat_id', -1)->first();

        // suppression des topics
        DB::table('forumtopics')->where('forum_id', $forum['forum_id'])->delete();

        // maj table lecture
        DB::table('forum_read')->where('forum_id', $forum['forum_id'])->delete();

        //=> suppression du forum
        DB::table('forums')->where('forum_id', $forum['forum_id'])->delete();

        // =>remise à 0 forum dans le groupe
        DB::table('groupes')->where('groupe_id', $groupe_id)->update(array(
            'groupe_forum'  => 0,
        ));

        global $aid;
        logs::Ecr_Log('security', "DeleteForumWS(". $forum['forum_id'] .") by AID : $aid", '');
    }

}