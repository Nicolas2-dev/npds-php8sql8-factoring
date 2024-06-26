<?php

namespace Modules\TwoEphemerids\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Ephemerids extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'ephem';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'Ephemerids';

        $this->f_titre = __d('two_ephemerids', 'Ephémérides');

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
     * [Ephemerids description]
     *
     * @return  void
     */
    function Ephemerids(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('ephem'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_ephemerids', 'Ajouter un éphéméride') .'</h3>
        <form action="'. site_url('admin.php') .'" method="post">
            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <div class="form-floating">
                    <select class="form-select" id="did" name="did">';

        $nday = '1';
        while ($nday <= 31) {
            echo '<option name="did">'. $nday .'</option>';
            $nday++;
        }

        echo '
                    </select>
                    <label for="did">'. __d('two_ephemerids', 'Jour') .'</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating">
                    <select class="form-select" id="mid" name="mid">';

        $nmonth = "1";                
        while ($nmonth <= 12) {
            echo '<option name="mid">'. $nmonth .'</option>';
            $nmonth++;
        }

        echo '
                    </select>
                    <label for="mid">'. __d('two_ephemerids', 'Mois') .'</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating">
                    <input class="form-control" type="number" id="yid" name="yid" maxlength="4" size="5" />
                    <label for="yid">'. __d('two_ephemerids', 'Année') .'</label>
                    </div>
                </div>
            </div>
            <div class="form-floating mb-3">
                <textarea name="content" class="form-control" style="height:120px;"></textarea>
                <label for="content">'. __d('two_ephemerids', 'Description de l\'éphéméride') .'</label>
            </div>
            <button class="btn btn-primary" type="submit">'. __d('two_ephemerids', 'Envoyer') .'</button>
            <input type="hidden" name="op" value="Ephemeridsadd" />
        </form>
        <hr />
        <h3 class="mb-3">'. __d('two_ephemerids', 'Maintenance des Ephémérides (Editer/Effacer)') .'</h3>
        <form action="'. site_url('admin.php') .'" method="post">
            <div class="row g-3">
                <div class="col-4">
                    <div class="form-floating mb-3">
                    <select class="form-select" id="did" name="did">';

        $nday = "1";                
        while ($nday <= 31) {
            echo '<option name="did">'. $nday .'</option>';
            $nday++;
        }

        echo '
                    </select>
                    <label for="did">'. __d('two_ephemerids', 'Jour') .'</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-floating mb-3">
                    <select class="form-select" id="mid" name="mid">';

        $nmonth = "1";
        while ($nmonth <= 12) {
            echo '<option name="mid">'. $nmonth .'</option>';
            $nmonth++;
        }

        echo '
                    </select>
                    <label for="mid">'. __d('two_ephemerids', 'Mois') .'</label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="op" value="Ephemeridsmaintenance" />
            <button class="btn btn-primary" type="submit">'. __d('two_ephemerids', 'Editer') .'</button>
        </form>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [Ephemeridsadd description]
     *
     * @param   int     $did      [$did description]
     * @param   int     $mid      [$mid description]
     * @param   int     $yid      [$yid description]
     * @param   string  $content  [$content description]
     *
     * @return  void
     */
    function Ephemeridsadd(int $did, int $mid, int $yid, string $content): void
    {
        DB::table('ephem')->insert(array(
            'did'       => $did,
            'mid'       => $mid,
            'yid'       => $yid,
            'content '  => stripslashes(str::FixQuotes($content) . ""),
        ));

        Header('Location: '. site_url('admin.php?op=Ephemerids'));
    }

    /**
     * [Ephemeridsmaintenance description]
     *
     * @param   int   $did  [$did description]
     * @param   int   $mid  [$mid description]
     *
     * @return  void
     */
    function Ephemeridsmaintenance(int $did, int $mid): void
    {
        global $f_meta_nom, $f_titre;

        $resultX = DB::table('ephem')
                        ->select('eid', 'did', 'mid', 'yid', 'content')
                        ->where('did', $did)
                        ->where('mid', $mid)
                        ->orderBy('yid', 'ASC')
                        ->get();

        if (!$resultX) {
            header('location: '. site_url('admin.php?op=Ephemerids'));
        }

        include("themes/default/header.php");

        GraphicAdmin(manuel('ephem'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3>'. __d('two_ephemerids', 'Maintenance des Ephémérides') .'</h3>
        <table data-toggle="table" data-striped="true" data-mobile-responsive="true" data-search="true" data-show-toggle="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <tr>
                    <th class="n-t-col-xs-2" data-sortable="true" data-halign="center" data-align="right" >
                        '. __d('two_ephemerids', 'Année') .'
                    </th>
                    <th data-halign="center" >
                        '. __d('two_ephemerids', 'Description') .'
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="center" >
                        '. __d('two_ephemerids', 'Fonctions') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        foreach ($resultX as $ephem) {
            echo '
                <tr>
                    <td>
                        '. $ephem['yid'] .'
                    </td>
                    <td>
                        '. language::aff_langue($ephem['content']) .'
                    </td>
                    <td>
                        <a href="'. site_url('admin.php?op=Ephemeridsedit&amp;eid='. $ephem['eid'] .'&amp;did='. $ephem['did'] .'&amp;mid='. $ephem['mid']) .'" title="'. __d('two_ephemerids', 'Editer') .'" data-bs-toggle="tooltip" >
                            <i class="fa fa-edit fa-lg me-2"></i>
                        </a>&nbsp;
                        <a href="'. site_url('admin.php?op=Ephemeridsdel&amp;eid='. $ephem['eid'] .'&amp;did='. $ephem['did'] .'&amp;mid='. $ephem['mid']) .'" title="'. __d('two_ephemerids', 'Effacer') .'" data-bs-toggle="tooltip">
                            <i class="fas fa-trash fa-lg text-danger"></i>
                        </a>
                </tr>';
        }

        echo '
                </tbody>
            </table>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [Ephemeridsdel description]
     *
     * @param   int   $eid  [$eid description]
     * @param   int   $did  [$did description]
     * @param   int   $mid  [$mid description]
     *
     * @return  void
     */
    function Ephemeridsdel(int $eid, int $did, int $mid): void
    {
        DB::table('ephem')->where('eid', $eid)->delete();

        Header('Location: '. site_url('admin.php?op=Ephemeridsmaintenance&did='. $did .'&mid='. $mid));
    }

    /**
     * [Ephemeridsedit description]
     *
     * @param   int   $eid  [$eid description]
     * @param   int   $did  [$did description]
     * @param   int   $mid  [$mid description]
     *
     * @return  void
     */
    function Ephemeridsedit(int $eid, int $did, int $mid): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('ephem'));
        adminhead($f_meta_nom, $f_titre);

        $ephem = DB::table('ephem')->select('yid', 'content')->where('eid', $eid)->first();

        echo '
        <hr />
        <h3>'. __d('two_ephemerids', 'Editer éphéméride') .'</h3>
        <form action="'. site_url('admin.php') .'" method="post">
            <div class="form-floating mb-3">
                <input class="form-control" type="number" name="yid" value="'. $ephem['yid'] .'" max="2500" />
                <label for="yid">'. __d('two_ephemerids', 'Année') .'</label>
            </div>
            <div class="form-floating mb-3">
                <textarea name="content" id="content" class="form-control" style="height:120px;">'. $ephem['content'] .'</textarea>
                <label for="content">'. __d('two_ephemerids', 'Description de l\'éphéméride') .'</label>
            </div>
            <input type="hidden" name="did" value="'. $did .'" />
            <input type="hidden" name="mid" value="'. $mid .'" />
            <input type="hidden" name="eid" value="'. $eid .'" />
            <input type="hidden" name="op" value="Ephemeridschange" />
            <button class="btn btn-primary" type="submit">'. __d('two_ephemerids', 'Envoyer') .'</button>
        </form>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [Ephemeridschange description]
     *
     * @param   int     $eid      [$eid description]
     * @param   int     $did      [$did description]
     * @param   int     $mid      [$mid description]
     * @param   int     $yid      [$yid description]
     * @param   string  $content  [$content description]
     *
     * @return  void
     */
    function Ephemeridschange(int $eid, int $did, int $mid, int $yid, string $content): void
    {
        DB::table('ephem')->where('eid', $eid)->update(array(
            'yid'       => $yid,
            'content'   => stripslashes(str::FixQuotes($content) . ""),
        ));

        Header('Location: '. site_url('admin.php?op=Ephemeridsmaintenance&did='. $did .'&mid='. $mid));
    }

}