<?php

namespace Modules\TwoHeadlines\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Headlines extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'headlines';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'HeadlinesAdmin';

        $this->f_titre = __d('two_headlines', 'Grands Titres de sites de News');

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
     * [HeadlinesAdmin description]
     *
     * @return  void
     */
    function HeadlinesAdmin(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('headlines'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_headlines', 'Liste des Grands Titres de sites de News')  .'</h3>
        <table id="tad_headline" data-toggle="table" data-classes="table table-striped table-borderless" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <tr>
                    <th class="n-t-col-xs-1" data-sortable="true" data-halign="center" data-align="right">
                        '. __d('two_headlines', 'ID')  .'
                    </th>
                    <th data-sortable="true" data-halign="center">
                        '. __d('two_headlines', 'Nom du site')  .'
                    </th>
                    <th data-sortable="true" data-halign="center">
                        '. __d('two_headlines', 'URL')  .'
                    </th>
                    <th data-sortable="true" data-halign="center" data-align="right">
                        '. __d('two_headlines', 'Etat')  .'
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="center">
                        '. __d('two_headlines', 'Fonctions')  .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $headlines = DB::table('headlines')->select('hid', 'sitename', 'url', 'headlinesurl', 'status')->orderBy('hid')->get();

        foreach($headlines as $headline) {
            echo '
                <tr>
                    <td>
                        '. $headline['hid']  .'
                    </td>
                    <td>
                        '. $headline['sitename']  .'
                    </td>
                    <td>
                        '. $headline['url']  .'
                    </td>';
            
            if ($headline['status'] == 1) {
                $status = '<span class="text-success">'. __d('two_headlines', 'Actif(s)')  .'</span>';
            } else {
                $status = '<span class="text-danger">'. __d('two_headlines', 'Inactif(s)')  .'</span>';
            }

            echo '
                    <td>'. $status  .'</td>
                    <td>
                        <a href="'. site_url('admin.php?op=HeadlinesEdit&amp;hid='. $headline['hid']) .'">
                            <i class="fa fa-edit fa-lg" title="'. __d('two_headlines', 'Editer')  .'" data-bs-toggle="tooltip"></i>
                        </a>&nbsp;
                        <a href="'. $headline['url']  .'" target="_blank">
                            <i class="fas fa-external-link-alt fa-lg" title="'. __d('two_headlines', 'Visiter')  .'" data-bs-toggle="tooltip"></i>
                        </a>&nbsp;
                        <a href="'. site_url('admin.php?op=HeadlinesDel&amp;hid='. $headline['hid']  .'&amp;ok=0') .'" class="text-danger">
                            <i class="fas fa-trash fa-lg" title="'. __d('two_headlines', 'Effacer')  .'" data-bs-toggle="tooltip"></i>
                        </a>
                    </td>
                </tr>';
        }

        echo '
            </tbody>
        </table>
        <hr />
        <h3 class="mb-3">'. __d('two_headlines', 'Nouveau Grand Titre')  .'</h3>
        <form id="fad_newheadline" action="'. site_url('admin.php') .'" method="post">
            <fieldset>
                <div class="form-floating mb-3">
                    <input id="xsitename" class="form-control" type="text" name="xsitename" placeholder="'. __d('two_headlines', 'Nom du site')  .'" maxlength="30" required="required" />
                    <label for="xsitename">'. __d('two_headlines', 'Nom du site')  .'</label>
                </div>
                <div class="form-floating mb-3">
                    <input id="url" class="form-control" type="url" name="url" placeholder="'. __d('two_headlines', 'URL')  .'" maxlength="320" required="required" />
                    <label for="url">'. __d('two_headlines', 'URL')  .'</label>
                    <span class="help-block text-end"><span id="countcar_url"></span></span>
                </div>
                <div class="form-floating mb-3">
                    <input id="headlinesurl" class="form-control" type="url" name="headlinesurl" placeholder="'. __d('two_headlines', 'URL pour le fichier RDF/XML')  .'" maxlength="320" required="required" />
                    <label for="headlinesurl">'. __d('two_headlines', 'URL pour le fichier RDF/XML')  .'</label>
                    <span class="help-block text-end"><span id="countcar_headlinesurl"></span></span>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" id="status" name="status">
                    <option name="status" value="1">'. __d('two_headlines', 'Actif(s)')  .'</option>
                    <option name="status" value="0" selected="selected">'. __d('two_headlines', 'Inactif(s)')  .'</option>
                    </select>
                    <label class="col-form-label col-sm-4" for="status">'. __d('two_headlines', 'Etat')  .'</label>
                </div>
                <button class="btn btn-primary" type="submit"><i class="fa fa-plus-square fa-lg me-2"></i>'. __d('two_headlines', 'Ajouter')  .'</button>
                <input type="hidden" name="op" value="HeadlinesAdd" />
            </fieldset>
        </form>';

        $arg1 = '
            var formulid = ["fad_newheadline"];
            inpandfieldlen("xsitename",30);
            inpandfieldlen("url",320);
            inpandfieldlen("headlinesurl",320);';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [HeadlinesEdit description]
     *
     * @param   int   $hid  [$hid description]
     *
     * @return  void
     */
    function HeadlinesEdit(int $hid): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('headlines'));
        adminhead($f_meta_nom, $f_titre);

        $headline = DB::table('headlines')->select('hid', 'sitename', 'url', 'headlinesurl', 'status')->where('hid', $hid)->first();

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_headlines', 'Editer paramètres Grand Titre')  .'</h3>
        <form id="fed_headline" action="'. site_url('admin.php') .'" method="post">
            <fieldset>
                <input type="hidden" name="hid" value="'. $headline['hid']  .'" />
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="xsitename">'. __d('two_headlines', 'Nom du site')  .'</label>
                    <div class="col-sm-8">
                    <input class="form-control" type="text" name="xsitename" id="xsitename"  maxlength="30" value="'. $headline['sitename']  .'" required="required" />
                    <span class="help-block text-end"><span id="countcar_xsitename"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="url">'. __d('two_headlines', 'URL')  .'&nbsp;<a href="'. $headline['url']  .'" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a></label>
                    <div class="col-sm-8">
                    <input class="form-control" type="url" id="url" name="url" maxlength="320" value="'. $headline['url']  .'" required="required" />
                    <span class="help-block text-end"><span id="countcar_url"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="headlinesurl">'. __d('two_headlines', 'URL pour le fichier RDF/XML')  .'&nbsp;<a href="'. $headline['headlinesurl']  .'" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a></label>
                    <div class="col-sm-8">
                    <input class="form-control" type="url" name="headlinesurl" id="headlinesurl" maxlength="320" value="'. $headline['headlinesurl']  .'" required="required" />
                    <span class="help-block text-end"><span id="countcar_headlinesurl"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="status">'. __d('two_headlines', 'Etat')  .'</label>
                    <div class="col-sm-8">
                    <select class="form-select" name="status">
                        <option name="status" value="1" '. (($headline['status'] == 1) ? 'selected="selected"' : '')  .'>'. __d('two_headlines', 'Actif(s)')  .'</option>
                        <option name="status" value="0" '. (($headline['status'] == 0) ? 'selected="selected"' : '')  .'>'. __d('two_headlines', 'Inactif(s)')  .'</option>
                    </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <input type="hidden" name="op" value="HeadlinesSave" />
                    <div class="col-sm-8 ms-sm-auto">
                    <button class="btn btn-primary col-12" type="submit"><i class="fa fa-edit fa-lg"></i>&nbsp;'. __d('two_headlines', 'Sauver les modifications')  .'</button>
                    </div>
                </div>
            </fieldset>
        </form>';

        $arg1 = '
            var formulid = ["fed_headline"];
            inpandfieldlen("xsitename",30);
            inpandfieldlen("url",320);
            inpandfieldlen("headlinesurl",320);';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [HeadlinesSave description]
     *
     * @param   int     $hid           [$hid description]
     * @param   string  $xsitename     [$xsitename description]
     * @param   string  $url           [$url description]
     * @param   string  $headlinesurl  [$headlinesurl description]
     * @param   int     $status        [$status description]
     *
     * @return  void
     */
    function HeadlinesSave(int $hid, string $xsitename, string $url, string $headlinesurl, int $status): void
    {
        DB::table('headlines')->where('hid', $hid)->update(array(
            'sitename'      => $xsitename,
            'url'           => $url,
            'headlinesurl'  => $headlinesurl,
            'status'        => $status,
        ));
        
        Header('Location: '. site_url('admin.php?op=HeadlinesAdmin'));
    }

    /**
     * [HeadlinesAdd description]
     *
     * @param   string  $xsitename     [$xsitename description]
     * @param   string  $url           [$url description]
     * @param   string  $headlinesurl  [$headlinesurl description]
     * @param   int     $status        [$status description]
     *
     * @return  void
     */
    function HeadlinesAdd(string $xsitename, string $url, string $headlinesurl, int $status): void
    {
        DB::table('headlines')->insert(array(
            'sitename'      => $xsitename,
            'url'           => $url,
            'headlinesurl'  => $headlinesurl,
            'status'        => $status,
        ));

        Header('Location: '. site_url('admin.php?op=HeadlinesAdmin'));
    }

    /**
     * [HeadlinesDel description]
     *
     * @param   int   $hid  [$hid description]
     * @param   int   $ok   [$ok description]
     *
     * @return  void
     */
    function HeadlinesDel(int $hid, int $ok = 0): void
    {
        global $f_meta_nom, $f_titre;

        if ($ok == 1) {
            DB::table('headlines')->where('hid', $hid)->delete();

            Header('Location: '. site_url('admin.php?op=HeadlinesAdmin'));
        } else {
            include("themes/default/header.php");

            GraphicAdmin(manuel('headlines'));
            adminhead($f_meta_nom, $f_titre);

            echo '
            <hr />
            <p class="alert alert-danger">
                <strong class="d-block mb-1">'. __d('two_headlines', 'Etes-vous sûr de vouloir supprimer cette boîte de Titres ?')  .'</strong>
                <a class="btn btn-danger btn-sm" href="'. site_url('admin.php?op=HeadlinesDel&amp;hid='. $hid  .'&amp;ok=1') .'" role="button">
                    '. __d('two_headlines', 'Oui')  .'
                </a>
                &nbsp;
                <a class="btn btn-secondary btn-sm" href="'. site_url('admin.php?op=HeadlinesAdmin') .'" role="button">
                    '. __d('two_headlines', 'Non')  .'
                </a>
            </p>';

            include("themes/default/footer.php");
        }
    }

}