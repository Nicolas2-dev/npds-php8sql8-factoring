<?php

namespace Modules\TwoPollbooth\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Pollbooths extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'surveys';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'create';

        $this->f_titre = __d('two_pollbooth', 'Les sondages');

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
     * [poll_createPoll description]
     *
     * @return  void
     */
    function poll_createPoll(): void
    {
        global $maxOptions, $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('surveys'));
        adminhead($f_meta_nom, $f_titre);

        echo '
            <hr />
                <h3 class="mb-3">'. __d('two_polbooth', 'Liste des sondages') .'</h3>
                <table id="tad_pool" data-toggle="table" data-striped="true" data-show-toggle="true" data-search="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa">
                <thead>
                    <tr>
                    <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">ID</th>
                    <th data-sortable="true" data-halign="center">'. __d('two_polbooth', 'Intitulé du Sondage') .'</th>
                    <th class="n-t-col-xs-2" data-sortable="true" data-halign="center" data-align="right">'. __d('two_polbooth', 'Vote') .'</th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="center">'. __d('two_polbooth', 'Fonctions') .'</th>
                    </tr>
                </thead>
                <tbody>';

        $poll_desc = DB::table('poll_desc')->select('pollID', 'pollTitle', 'voters')->orderBy('timeStamp')->get();

        foreach ($poll_desc as $poll) {
            echo '
                    <tr>
                    <td>'. $poll['pollID'] .'</td>
                    <td>'. language::aff_langue($poll['pollTitle']) .'</td>
                    <td>'. $poll['voters'] .'</td>
                    <td>
                        <a href="'. site_url('admin.php?op=editpollPosted&amp;id='. $poll['pollID']) .'"><i class="fa fa-edit fa-lg" title="'. __d('two_polbooth', 'Editer ce sondage') .'" data-bs-toggle="tooltip"></i></a>
                        <a href="'. site_url('admin.php?op=removePosted&amp;id='. $poll['pollID']) .'"><i class="fas fa-trash fa-lg text-danger ms-2" title="'. __d('two_polbooth', 'Effacer ce sondage') .'" data-bs-toggle="tooltip"></i></a>
                    </td>
                    </tr>';

            // not used sum
            //$sum = DB::table('poll_data')->select(DB::raw('SUM(optionCount) AS SUM'))->where('pollID', $poll['pollID'])->get();
        }

        echo '
                </tbody>
            </table>
            <hr />
            <h3 class="mb-3">'. __d('two_polbooth', 'Créer un nouveau Sondage') .'</h3>
            <form id="pollssondagenew" action="'. site_url('admin.php') .'" method="post">
                <input type="hidden" name="op" value="createPosted" />
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="pollTitle" name="pollTitle" id="pollTitle" maxlength="100" required="required" />
                    <label for="pollTitle">'. __d('two_polbooth', 'Intitulé du Sondage') .'</label>
                    <span class="help-block">'. __d('two_polbooth', 'S.V.P. entrez chaque option disponible dans un seul champ') .'</span>
                    <span class="help-block text-end"><span id="countcar_pollTitle"></span></span>
                </div>';

        $requi = '';
        for ($i = 1; $i <= $maxOptions; $i++) {
            $requi = $i < 3 ? ' required="required" ' : '';

            echo '
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="optionText'. $i .'" name="optionText['. $i .']" maxlength="255" '. $requi .' />
                    <label for="optionText'. $i .'">'. __d('two_polbooth', 'Option') .' '. $i .'</label>
                    <span class="help-block text-end"><span id="countcar_optionText'. $i .'"></span></span>
                </div>';
        }

        echo '
                <div class="form-check form-check-inline mb-3">
                    <input class="form-check-input" type="checkbox" id="poll_type" name="poll_type" value="1" />
                    <label class="form-check-label" for="poll_type">'. __d('two_polbooth', 'Seulement aux membres') .'</label>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">'. __d('two_polbooth', 'Créer') .'</button>
                </div>
            </form>';

        $arg1 = '
        var formulid = ["pollssondagenew"];
        inpandfieldlen("pollTitle",100)';

        for ($i = 1; $i <= $maxOptions; $i++) {
            $arg1 .= 'inpandfieldlen("optionText'. $i .'",255)';
        }

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [poll_createPosted description]
     *
     * @return  void
     */
    function poll_createPosted(): void 
    {
        global $maxOptions, $pollTitle, $optionText, $poll_type;

        $timeStamp = time();
        $pollTitle = str::FixQuotes($pollTitle);

        DB::table('poll_desc')->insert(array(
            'pollTitle'    => $pollTitle,
            'timeStamp'    => $timeStamp,
            'voters'       => 0,
        ));

        $poll_desc = DB::table('poll_desc')->select('pollID')->where('pollTitle', $pollTitle)->first();

        $id = $poll_desc['pollID'];

        for ($i = 1; $i <= sizeof($optionText); $i++) {
            if ($optionText[$i] != '') {
                $optionText[$i] = str::FixQuotes($optionText[$i]);
            }

            DB::table('poll_data')->insert(array(
                'pollID'       => $id,
                'optionText'   => $optionText[$i],
                'optionCount'  => 0,
                'voteID'       => $i,
                'pollType'     => $poll_type,
            ));
        }

        Header('Location: '. site_url('admin.php?op=adminMain'));
    }

    /**
     * [poll_removePoll description]
     *
     * @return  void
     */
    function poll_removePoll(): void
    {
        global $f_meta_nom, $f_titre, $NPDS_Prefix;

        include("themes/default/header.php");

        GraphicAdmin(manuel('surveys'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_polbooth', 'Retirer un Sondage existant') .'</h3>
        <span class="help-block">'. __d('two_polbooth', 'S.V.P. Choisissez un sondage dans la liste suivante.') .'</span>
        <p align="center"><span class="text-danger">'. __d('two_polbooth', 'ATTENTION : Le Sondage choisi va être supprimé IMMEDIATEMENT de la base de données !') .'</span></p>
        ';

        echo '
        <form action="'. site_url('admin.php') .'" method="post">
            <input type="hidden" name="op" value="removePosted" />
            <table id="tad_delepool" data-toggle="table" data-striped="true" data-show-toggle="true" data-search="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
                <thead>
                    <tr>
                    <th></th>
                    <th data-sortable="true">'. __d('two_polbooth', 'Intitulé du Sondage') .'</th>
                    <th data-sortable="true">ID</th>
                    </tr>
                </thead>
                <tbody>';

        $poll_desc = DB::table('poll_desc')->select('pollID', 'pollTitle')->orderBy('timeStamp')->get();

        foreach ($poll_desc as $poll) {
            echo '
                    <tr>
                    <td><input type="radio" name="id" value="'. $poll['pollID'] .'" /></td>
                    <td> '. $poll['pollTitle'] .'</td>
                    <td>ID : '. $poll['pollID'] .'</td>
                    </tr>
            ';
        }

        echo '
                </tbody>
            </table>
            <br />
            <div class="mb-3">
                <button class="btn btn-danger" type="submit">'. __d('two_polbooth', 'Retirer') .'</button>
            </div>
        </form>';

        include("themes/default/footer.php");;
    }

    /**
     * [poll_removePosted description]
     *
     * @return  void
     */
    function poll_removePosted(): void 
    {
        global $id, $setCookies;

        // ----------------------------------------------------------------------------
        // Specified the index and the name off the application for the table appli_log
        $al_id = 1;
        $al_nom = 'Poll';
        // ----------------------------------------------------------------------------

        if ($setCookies == '1') {
            DB::table('appli_log')->where('al_id', $al_id)->where('al_subid', $id)->delete();
        }

        DB::table('poll_desc')->where('pollID', $id)->delete();
        DB::table('poll_data')->where('pollID', $id)->delete();

        include('modules/comments/config/pollBoth.conf.php');

        DB::table('posts')->where('topic_id', $id)->where('forum_id', $forum)->delete();

        Header('Location: '. site_url('admin.php?op=create'));
    }

    /**
     * [poll_editPoll description]
     *
     * @return  void
     */
    function poll_editPoll(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('surveys'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_polbooth', 'Edition des sondages') .'</h3>
        <span class="help-block">'. __d('two_polbooth', 'S.V.P. Choisissez un sondage dans la liste suivante.') .'</span>
        <form id="fad_editpool" action="'. site_url('admin.php') .'" method="post">
            <input type="hidden" name="op" value="editpollPosted" />
            <table id="tad_editpool" data-toggle="table" data-striped="true" data-show-toggle="true" data-search="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
                <thead>
                    <tr>
                    <th></th>
                    <th data-sortable="true">'. __d('two_polbooth', 'Intitulé du Sondage') .'</th>
                    <th data-sortable="true">ID</th>
                    </tr>
                </thead>
                <tbody>';

        $poll_desc = DB::table('poll_desc')->select('pollID', 'pollTitle', 'timeStamp')->orderBy('timeStamp')->get();

        foreach ($poll_desc as $poll) {
            echo '
                    <tr>
                    <td><input type="radio" name="id" value="'. $poll['pollID'] .'" /></td>
                    <td>'. $poll['pollTitle'] .'</td>
                    <td>ID : '. $poll['pollID'] .'</td>
                    </tr>';
        }

        echo '
                </tbody>
            </table>
            <br />
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">'. __d('two_polbooth', 'Editer') .'</button>
            </div>
        </form>';

        //   adminfoot('','','','');

        include("themes/default/footer.php");;
    }

    /**
     * [poll_editPollPosted description]
     *
     * @return  void
     */
    function poll_editPollPosted(): void
    {
        global $id, $maxOptions, $f_meta_nom, $f_titre;

        if ($id) {
            include("themes/default/header.php");

            GraphicAdmin(manuel('surveys'));
            adminhead($f_meta_nom, $f_titre);

            $holdtitle = DB::table('poll_desc')->select('pollID', 'pollTitle', 'timeStamp')->where('pollID', $id)->first();

            echo '
            <hr />
            <h3 class="mb-3">'. __d('two_polbooth', 'Edition des sondages') .'</h3>
            <form id="pollssondageed" method="post" action="'. site_url('admin.php') .'">
                <input type="hidden" name="op" value="SendEditPoll">
                <input type="hidden" name="pollID" value="'. $id .'" />
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="pollTitle" name="pollTitle" value="'. $holdtitle['pollTitle'] .'" maxlength="100" required="required" />
                    <label for="pollTitle">'. __d('two_polbooth', 'Intitulé du Sondage') .'</label>
                    <span class="help-block">'. __d('two_polbooth', 'S.V.P. entrez chaque option disponible dans un seul champ') .'</span>
                    <span class="help-block text-end"><span id="countcar_pollTitle"></span></span>
                </div>';

            $poll_data = DB::table('poll_data')->select('optionText', 'voteID', 'pollType')->where('pollID', $id)->orderBy('voteID', 'asc')->first();

            $requi = '';
            for ($i = 1; $i <= $maxOptions; $i++) {
                if ($i < 3) {
                    $requi = ' required="required" ';
                } else {
                    $requi = '';
                }
                
                echo '
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" id="optionText'. $i .'" name="optionText['. $poll_data['voteID'] .']" maxlength="255" value="'. $poll_data['optionText'] .'" '. $requi .' />
                    <label for="optionText'. $i .'">'. __d('two_polbooth', 'Option') .' '. $i .'</label>
                    <span class="help-block text-end"><span id="countcar_optionText'. $i .'"></span></span>
                </div>';

            }

            $pollClose = (($poll_data['pollType'] / 128) >= 1 ? 1 : 0);
            $pollType = $poll_data['pollType'] % 128;

            echo '
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="poll_type" name="poll_type" value="1"';

            if ($pollType == "1") {
                echo ' checked="checked"';
            }

            echo ' />
                    <label class="form-check-label" for="poll_type">'. __d('two_polbooth', 'Seulement aux membres') .'</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check text-danger">
                    <input class="form-check-input" type="checkbox" id="poll_close" name="poll_close" value="1"';

            if ($pollClose == 1) {
                echo ' checked="checked"';
            }

            echo ' />
                        <label class="form-check-label" for="poll_close">'. __d('two_polbooth', 'Vote fermé') .'</label>
                    </div>
                </div>
                <div class="mb-3">
                        <button class="btn btn-primary" type="submit">Ok</button>
                </div>
            </form>';

            $arg1 = '
            var formulid = ["pollssondageed"];
            inpandfieldlen("pollTitle",100)';

            for ($i = 1; $i <= $maxOptions; $i++) {
                $arg1 .= '
            inpandfieldlen("optionText'. $i .'",255)';
            }

            css::adminfoot('fv', '', $arg1, '');
        } else {
            header('location: '. site_url('admin.php?op=editpoll'));
        }
    }

    /**
     * [poll_SendEditPoll description]
     *
     * @return  void
     */
    function poll_SendEditPoll(): void
    {
        global $pollTitle, $optionText, $poll_type, $pollID, $poll_close;

        $poll_type = $poll_type + 128 * $poll_close;

        DB::table('poll_desc')->where('pollID', $pollID)->update(array(
            'pollTitle'       => $pollTitle,
        ));

        for ($i = 1; $i <= sizeof($optionText); $i++) {
            if ($optionText[$i] != '') {
                $optionText[$i] = str::FixQuotes($optionText[$i]);
            }

            DB::table('poll_data')->where('pollID', $pollID)->where('voteID', $i)->update(array(
                'optionText'     => $optionText[$i],
                'pollType'       => $poll_type,
            ));

        }

        Header('Location: '. site_url('admin.php?op=create'));
    }


}