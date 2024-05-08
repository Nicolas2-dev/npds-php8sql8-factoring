<?php

namespace Modules\TwoGroupes\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class GroupeMembre extends AdminController
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
     * [membre_add description]
     *
     * @param   int   $gp  [$gp description]
     *
     * @return  void
     */
    function membre_add(int $gp): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('groupes'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3>'. __d('two_groupes', 'Ajouter des membres')  .' / '. __d('two_groupes', 'Groupe')  .' : '. $gp  .'</h3>
        <form id="groupesaddmb" class="admform" action="' . site_url('admin.php') .'" method="post">
            <fieldset>
                <legend><i class="fa fa-users fa-2x text-muted"></i></legend>
                <div class="mb-3">
                    <label class="col-form-label" for="luname">'. __d('two_groupes', 'Liste des membres')  .'</label>
                    <input type="text" class="form-control" id="luname" name="luname" maxlength="255" value="" required="required" />
                    <span class="help-block text-end"><span id="countcar_luname"></span></span>
                </div>
                <input type="hidden" name="op" value="membre_add_finish" />
                <input type="hidden" name="groupe_id" value="'. $gp  .'" />
                <div class="mb-3">
                    <input class="btn btn-primary" type="submit" name="sub_op" value="'. __d('two_groupes', 'Sauver les modifications')  .'" />
                </div>
            </fieldset>
        </form>';

        $arg1 = '
        var formulid = ["groupesaddmb"];
        inpandfieldlen("luname",255);';

        // echo (mysqli_get_client_info() <= '8.0') 
        //     ? js::auto_complete_multi_query('membre', 'uname', 'luname', DB::table('users')->join('users_status', 'users.uid', '=', 'users_status.uid', 'inner')->where('users.uid', '<>', 1)->where('groupe', 'NOT REGEXP', '[[:<:]]'. $gp  .'[[:>:]]')->get()) 
        //     : js::auto_complete_multi_query('membre', 'uname', 'luname', DB::table('users')->join('users_status', 'users.uid', '=', 'users_status.uid', 'inner')->where('users.uid', '<>', 1)->where('groupe', 'NOT REGEXP', '\\b'. $gp  .'\\b\'')->get());
        
        js::auto_complete_multi_query('membre', 'uname', 'luname', 
            DB::table('users')
                ->join('users_status', 'users.uid', '=', 'users_status.uid', 'inner')
                ->where('users.uid', '<>', 1)
                ->where('groupe', 'NOT REGEXP', $gp )
                ->get());

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [membre_add_finish description]
     *
     * @param   int     $groupe_id  [$groupe_id description]
     * @param   string  $luname     [$luname description]
     *
     * @return  void
     */
    function membre_add_finish(int $groupe_id, string $luname): void
    {
        $image = '18.png';

        $groupes = DB::table('groupes')->select('groupe_name')->where('groupe_id', $groupe_id)->first();

        $gn = $groupes['groupe_name'];

        $luname = rtrim($luname, ', ');
        $luname = str_replace(' ', '', $luname);

        $list_membres = explode(',', $luname);
        $nbremembres = count($list_membres);

        $subject = __d('two_groupes', 'Nouvelles du groupe')  .' '. $gn;
        $message = __d('two_groupes', 'Vous faites désormais partie des membres du groupe')  .' : '. $gn  .' ['. $groupe_id  .'].';

        $copie = '';
        $from_userid = 1;

        for ($j = 0; $j < $nbremembres; $j++) {
            $uname = $list_membres[$j];

            $ibid = DB::table('users')->select('uid')->where('uname', $uname)->first();

            if ($ibid['uid']) {
                $to_userid = $uname;

                $ibid2 = DB::table('users_status')->select('groupe')->where('uid', $ibid['uid'])->first();

                $lesgroupes = explode(',', $ibid2['groupe']);
                $nbregroupes = count($lesgroupes);

                $groupeexistedeja = false;
                for ($i = 0; $i < $nbregroupes; $i++) {
                    if ($lesgroupes[$i] == $groupe_id) {
                        $groupeexistedeja = true;
                        break;
                    }
                }

                if (!$groupeexistedeja) {
                    if ($ibid2['groupe']) {
                        $groupesmodif = $ibid2['groupe']  .','. $groupe_id;
                    } else {
                        $groupesmodif = $groupe_id;
                    }

                    DB::table('users_status')->where('uid', $ibid['uid'])->update(array(
                        'groupe'       => $groupesmodif,
                    ));
                }

                messenger::writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie);
            }
        }

        global $aid;
        logs::Ecr_Log('security', "AddMemberToGroup($groupe_id, $luname) by AID : $aid", '');

        Header('Location: ' . site_url('admin.php?op=groupes'));
    }

    /**
     * [retiredugroupe description]
     *
     * @param   int     $groupe_id  [$groupe_id description]
     * @param   int     $uid        [$uid description]
     * @param   string  $uname      [$uname description]
     *
     * @return  void
     */
    function retiredugroupe(int $groupe_id, int $uid, string $uname): void
    {
        $image = '18.png';

        $gn = DB::table('groupes')->select('groupe_name')->where('groupe_id', $groupe_id)->first();

        $pat = '#^\b'. $uid  .'\b$#';
        $mes_sys = '';
        $q = '';
        $ok = 0;

        $res = DB::table('forums')->select('forum_moderator')->where('forum_pass', $groupe_id)->where('cat_id', -1)->get();
        
        foreach($res as $row) {
            
            if (preg_match($pat, $row['forum_moderator'])) {
                $mes_sys = 'mod_'. $uname;
                $q = '&al='. $mes_sys;
                $ok = 1;
            }
        }

        if ($ok == 0) {
            $pat = '#\b'. $uid  .'\b#';

            $res = DB::table('forums')->select('forum_id', 'forum_moderator')->where('forum_pass', $groupe_id)->where('cat_id', -1)->get();

            foreach($res as $r) {
                $new_moder = preg_replace('#,,#', ',', trim(preg_replace($pat, '', $r['forum_moderator']), ','));
                
                DB::table('forums')->where('forum_id', $r['forum_id'])->update(array(
                    'forum_moderator'   => $new_moder,
                ));
            }

            $subject = __d('two_groupes', 'Nouvelles du groupe')  .' '. $gn;
            $message = __d('two_groupes', 'Vous ne faites plus partie des membres du groupe')  .' : '. $gn  .' ['. $groupe_id  .'].';

            $copie = '';
            $from_userid = 1;
            $to_userid = $uname;

            $valeurs = DB::table('users_status')->select('groupe')->where('uid', $uid)->first();

            $lesgroupes = explode(',', $valeurs['groupe']);
            $nbregroupes = count($lesgroupes);

            $groupesmodif = '';

            for ($i = 0; $i < $nbregroupes; $i++) {
                if ($lesgroupes[$i] != $groupe_id) {
                    if ($groupesmodif == '') $groupesmodif .= $lesgroupes[$i];
                    else $groupesmodif .= ','. $lesgroupes[$i];
                }
            }

            DB::table('users_status')->where('uid', $uid)->update(array(
                'groupe'    => $groupesmodif,
            ));

            messenger::writeDB_private_message($to_userid, $image, $subject, $from_userid, $message, $copie);

            global $aid;
            logs::Ecr_Log('security', "DeleteMemberToGroup($groupe_id, $uname) by AID : $aid", '');
        }

        Header('Location: ' . site_url('admin.php?op=groupes'. $q));
    }

    /**
     * [retiredugroupe_all description]
     *
     * @param   int     $groupe_id   [$groupe_id description]
     * @param   string  $tab_groupe  [$tab_groupe description]
     *
     * @return  void
     */
    function retiredugroupe_all(int $groupe_id, string $tab_groupe): void
    {
        $tab_groupe = explode(',', $tab_groupe);

        foreach ($tab_groupe as $bidon => $uidZ) {
            if ($uidZ) {
                // a rajouter enlever modérateur forum
                $valeurs = DB::table('users_status')->select('groupe')->where('uid', $uidZ)->first();

                $lesgroupes = explode(',', $valeurs['groupe']);
                $nbregroupes = count($lesgroupes);
                $groupesmodif = '';
                
                for ($i = 0; $i < $nbregroupes; $i++) {
                    if ($lesgroupes[$i] != $groupe_id) {
                        if ($groupesmodif == '') $groupesmodif .= $lesgroupes[$i];
                        else $groupesmodif .= ','. $lesgroupes[$i];
                    }
                }

                DB::table('users_status')->where('uid', $uidZ)->update(array(
                    'groupe'    => $groupesmodif,
                ));

                global $aid;
                logs::Ecr_Log('security', "DeleteAllMemberToGroup($groupe_id, $uidZ) by AID : $aid", '');
            }
        }

        Header('Location: ' . site_url('admin.php?op=groupes'));
    }

}