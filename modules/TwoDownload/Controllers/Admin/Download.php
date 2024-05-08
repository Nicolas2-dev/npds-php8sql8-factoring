<?php

namespace Modules\TwoDownload\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Download extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'downloads';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'DownloadAdmin';

        $this->f_titre = __d('two_download', 'Téléchargements');

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
     * [DownloadAdmin description]
     *
     * @return  void
     */
    function DownloadAdmin(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('downloads'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="my-3">'. __d('two_download', 'Catégories') .'</h3>';

        $pseudocatid = '';

        $downloads = DB::table('downloads')->select('dcategory')->orderBy('dcategory')->get();

        foreach ($downloads as $download) {

            echo '
            <h4 class="mb-2">
                <a class="tog" id="show_cat_'. $pseudocatid .'" title="Déplier la liste">
                    <i id="i_cat_'. $pseudocatid .'" class="fa fa-caret-down fa-lg text-primary"></i>
                </a>
                '. language::aff_langue(stripslashes($download['dcategory'])) .'
            </h4>';
            
            echo '
            <div class="mb-3" id="cat_'. $pseudocatid .'" style="display:none;">
            <table data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-show-columns="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons-prefix="fa" data-icons="icons">
                <thead>
                    <tr>
                        <th data-sortable="true" data-halign="center" data-align="right">
                            '. __d('two_download', 'ID') .'
                        </th>
                        <th data-sortable="true" data-halign="center" data-align="right">
                            '. __d('two_download', 'Compteur') .'
                        </th>
                        <th data-sortable="true" data-halign="center" data-align="center">
                            Typ.
                        </th>
                        <th data-halign="center" data-align="center">
                            '. __d('two_download', 'URL') .'
                        </th>
                        <th data-sortable="true" data-halign="center" >
                            '. __d('two_download', 'Nom de fichier') .'
                        </th>
                        <th data-halign="center" data-align="center">
                            '. __d('two_download', 'Version') .'
                        </th>
                        <th data-halign="center" data-align="right">
                            '. __d('two_download', 'Taille de fichier') .'
                        </th>
                        <th data-halign="center" >
                            '. __d('two_download', 'Date') .'
                        </th>
                        <th data-halign="center" data-align="center">
                            '. __d('two_download', 'Fonctions') .'
                        </th>
                    </tr>
                </thead>
                <tbody>';

            $downloadsX = DB::table('downloads')
                            ->select('did', 'dcounter', 'durl', 'dfilename', 'dfilesize', 'ddate', 'dver', 'perms')
                            ->where('dcategory', addslashes($download['dcategory']))
                            ->orderBy('did', 'ASC')
                            ->get();

            foreach ($downloadsX as $download) {

                if ($download['perms'] == '0') {
                    $dperm = '<span title="'. __d('two_download', 'Anonymes') .'<br />'. __d('two_download', 'Membres') .'<br />'. __d('two_download', 'Administrateurs') .'" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"><i class="far fa-user fa-lg"></i><i class="fas fa-user fa-lg"></i><i class="fa fa-user-cog fa-lg"></i></span>';
                } else if ($download['perms'] == '1') { 
                    $dperm = '<span title="'. __d('two_download', 'Membres') .'" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-user fa-lg"></i></span>';
                } else if ($download['perms'] == '-127') { 
                    $dperm = '<span title="'. __d('two_download', 'Administrateurs') .'" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fas fa-user-cog fa-lg"></i></span>';
                } else if ($download['perms'] == '-1') {
                    $dperm = '<span title="'. __d('two_download', 'Anonymes') .'"  data-bs-toggle="tooltip" data-bs-placement="right"><i class="far fa-user fa-lg"></i></span>';
                } else {
                    $dperm = '<span title="'. __d('two_download', 'Groupes') .'" data-bs-toggle="tooltip" data-bs-placement="right"><i class="fa fa-users fa-lg"></i></span>';
                }

                echo '
                    <tr>
                    <td>
                        '. $download['did'] .'
                    </td>
                    <td>
                        '. $download['dcounter'] .'
                    </td>
                    <td>
                        '. $dperm .'
                    </td>
                    <td>
                        <a href="'. $download['durl'] .'" title="'. __d('two_download', 'Téléchargements') .'<br />'. $download['durl'] .'" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true"><i class="fa fa-download fa-2x"></i></a>
                    </td>
                    <td>
                        '. $download['dfilename'] .'
                    </td>
                    <td>
                        <span class="small">'. $download['dver'] .'</span>
                    </td>
                    <td>
                        <span class="small">';
                    
                $Fichier = new FileManagement;
                
                if ($download['dfilesize'] != 0) {
                    echo $Fichier->file_size_format($download['dfilesize'], 1);
                } else {
                    echo $Fichier->file_size_auto($download['durl'], 2);
                }

                echo '</span>
                        </td>
                        <td class="small">
                            '. $download['ddate'] .'
                        </td>
                        <td>
                            <a href="'. site_url('admin.php?op=DownloadEdit&amp;did='. $download['did']) .'" title="'. __d('two_download', 'Editer') .'" data-bs-toggle="tooltip" data-bs-placement="right">
                                <i class="fa fa-edit fa-lg"></i>
                            </a>
                            <a href="'. site_url('admin.php?op=DownloadDel&amp;did='. $download['did'] .'&amp;ok=0') .'" title="'. __d('two_download', 'Effacer') .'" data-bs-toggle="tooltip" data-bs-placement="right">
                                <i class="fas fa-trash fa-lg text-danger ms-2"></i>
                            </a>
                        </td>
                    </tr>';
            }

            echo '
                    </tbody>
                </table>
            </div>
            <script type="text/javascript">
                //<![CDATA[
                    $( document ).ready(function() {
                        tog("cat_'. $pseudocatid .'","show_cat_'. $pseudocatid .'","hide_cat_'. $pseudocatid .'");
                    })
                //]]>
            </script>';

            $pseudocatid++;
        }

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_download', 'Ajouter un Téléchargement') .'</h3>
        <form action="'. site_url('admin.php') .'" method="post" id="downloadadd" name="adminForm">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="durl">'. __d('two_download', 'Télécharger URL') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="durl" name="durl" maxlength="320" required="required" />
        &nbsp;<a href="javascript:void(0);" onclick="window.open(\''. site_url('admin.php?op=FileManagerDisplay') .'\', \'wdir\', \'width=650, height=450, menubar=no, location=no, directories=no, status=no, copyhistory=no, toolbar=no, scrollbars=yes, resizable=yes\');">
        <span class="">['. __d('two_download', 'Parcourir') .']</span></a>
                    <span class="help-block text-end" id="countcar_durl"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dcounter">'. __d('two_download', 'Compteur') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="number" id="dcounter" name="dcounter" maxlength="30" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dfilename">'. __d('two_download', 'Nom de fichier') .'</label>
                    <div class="col-sm-8">
                    <input class="form-control" type="text" id="dfilename" name="dfilename" maxlength="255" required="required" />
                    <span class="help-block text-end" id="countcar_dfilename"></span>
                    </div>
                </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dver">'. __d('two_download', 'Version') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" name="dver" id="dver" maxlength="6" />
                    <span class="help-block text-end" id="countcar_dver"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dfilesize">'. __d('two_download', 'Taille de fichier') .' (bytes)</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="dfilesize" name="dfilesize" maxlength="31" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dweb">'. __d('two_download', 'Propriétaire de la page Web') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="dweb" name="dweb" maxlength="255" />
                    <span class="help-block text-end" id="countcar_dweb"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="duser">'. __d('two_download', 'Propriétaire') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="duser" name="duser" maxlength="30" />
                    <span class="help-block text-end" id="countcar_duser"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dcategory">'. __d('two_download', 'Catégorie') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="dcategory" name="dcategory" maxlength="250" required="required"/>
                    <span class="help-block text-end" id="countcar_dcategory"></span>
                    <select class="form-select" name="sdcategory" onchange="adminForm.dcategory.value=options[selectedIndex].value">
                    <option>'. __d('two_download', 'Catégorie') .'</option>';


        $download_categorie = DB::table('downloads')->select('dcategory')->orderBy('dcategory')->distinct()->get();

        foreach ($download_categorie as $categ) {
            $dcategory = stripslashes($categ['dcategory']);
            echo '<option value="'. $dcategory .'">'. language::aff_langue($dcategory) .'</option>';
        }

        echo '
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="xtext">'. __d('two_download', 'Description') .'</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" id="xtext" name="xtext" rows="20" ></textarea>
                </div>
            </div>
            '. editeur::aff_editeur('xtext', '') .'
            <fieldset>
                <legend>'. __d('two_download', 'Droits') .'</legend>';

        groupe::droits('0');

        echo '
            </fieldset>
            <input type="hidden" name="op" value="DownloadAdd" />
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <button class="btn btn-primary" type="submit">'. __d('two_download', 'Ajouter') .'</button>
                </div>
            </div>
        </form>';

        $arg1 = '
                var formulid = ["downloadadd"];
                inpandfieldlen("durl",320);
                inpandfieldlen("dfilename",255);
                inpandfieldlen("dver",6);
                inpandfieldlen("dfilesize",31);
                inpandfieldlen("dweb",255);
                inpandfieldlen("duser",30);
                inpandfieldlen("dcategory",250);';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [DownloadEdit description]
     *
     * @param   int   $did  [$did description]
     *
     * @return  void
     */
    function DownloadEdit(int $did): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('downloads'));
        adminhead($f_meta_nom, $f_titre);

        $download = DB::table('downloads')
                        ->select('did', 'dcounter', 'durl', 'dfilename', 'dfilesize', 'ddate', 'dweb', 'duser', 'dver', 'dcategory', 'ddescription', 'perms')
                        ->where('did', $did)->first();

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_download', 'Editer un Téléchargement') .'</h3>
        <form action="'. site_url('admin.php') .'" method="post" id="downloaded" name="adminForm">
            <input type="hidden" name="did" value="'. $download['did'] .'" />
            <input type="hidden" name="dcounter" value="'. $download['dcounter'] .'" />
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="durl">'. __d('two_download', 'Télécharger URL') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="durl" name="durl" value="'. $download['durl'] .'" maxlength="320" required="required" />
                    <span class="help-block text-end" id="countcar_durl"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dfilename">'. __d('two_download', 'Nom de fichier') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="dfilename" name="dfilename" id="dfilename" value="'. $download['dfilename'] .'" maxlength="255" required="required" />
                    <span class="help-block text-end" id="countcar_dfilename"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dver">'. __d('two_download', 'Version') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" name="dver" id="dver" value="'. $download['dver'] .'" maxlength="6" />
                    <span class="help-block text-end" id="countcar_dver"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dfilesize">'. __d('two_download', 'Taille de fichier') .' (bytes)</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="dfilesize" name="dfilesize" value="'. $download['dfilesize'] .'" maxlength="31" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dweb">'. __d('two_download', 'Propriétaire de la page Web') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="dweb" name="dweb" value="'. $download['dweb'] .'" maxlength="255" />
                    <span class="help-block text-end" id="countcar_dweb"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="duser">'. __d('two_download', 'Propriétaire') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="duser" name="duser" value="'. $download['duser'] .'" maxlength="30" />
                    <span class="help-block text-end" id="countcar_duser"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="dcategory">'. __d('two_download', 'Catégorie') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="dcategory" name="dcategory" value="'. stripslashes($download['dcategory']) .'" maxlength="250" required="required" />
                    <span class="help-block text-end"><span id="countcar_dcategory"></span></span>
                    <select class="form-select" name="sdcategory" onchange="adminForm.dcategory.value=options[selectedIndex].value">';
        
        $download_categorie = DB::table('downloads')->select('dcategory')->orderBy('dcategory')->distinct()->get();

        foreach ($download_categorie as $categ) {

            $sel = (($categ['dcategory'] == $download['dcategory']) ? 'selected' : '');
            $Xdcategory = stripslashes($categ['dcategory']);
            echo '<option '. $sel .' value="'. $Xdcategory .'">'. language::aff_langue($Xdcategory) .'</option>';
        }

        echo '
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="xtext">'. __d('two_download', 'Description') .'</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" id="xtext" name="xtext" rows="20" >'. stripslashes($download['ddescription']) .'</textarea>
                </div>
            </div>
            '. editeur::aff_editeur('xtext', '');

        echo '
            <fieldset>
                <legend>'. __d('two_download', 'Droits') .'</legend>';

        groupe::droits($download['perms']);

        echo '
            </fieldset>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4">'. __d('two_download', 'Changer la date') .'</label>
                <div class="col-sm-8">
                    <div class="form-check my-2">
                    <input type="checkbox" id="ddate" name="ddate" class="form-check-input" value="yes" />
                    <label class="form-check-label" for="ddate">'. __d('two_download', 'Oui') .'</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <input type="hidden" name="op" value="DownloadSave" />
                    <input class="btn btn-primary" type="submit" value="'. __d('two_download', 'Sauver les modifications') .'" />
                </div>
            </div>
        </form>';

        $arg1 = '
            var formulid = ["downloaded"];
            inpandfieldlen("durl",320);
            inpandfieldlen("dfilename",255);
            inpandfieldlen("dver",6);
            inpandfieldlen("dfilesize",31);
            inpandfieldlen("dweb",255);
            inpandfieldlen("duser",30);
            inpandfieldlen("dcategory",250);';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [DownloadSave description]
     *
     * @param   int     $did          [$did description]
     * @param   int     $dcounter     [$dcounter description]
     * @param   string  $durl         [$durl description]
     * @param   string  $dfilename    [$dfilename description]
     * @param   int     $dfilesize    [$dfilesize description]
     * @param   string  $dweb         [$dweb description]
     * @param   string  $duser        [$duser description]
     * @param   string  $ddate        [$ddate description]
     * @param   string  $dver         [$dver description]
     * @param   string  $dcategory    [$dcategory description]
     * @param   string  $sdcategory   [$sdcategory description]
     * @param   string  $description  [$description description]
     * @param   string  $privs        [$privs description]
     * @param   array   $Mprivs       [$Mprivs description]
     *
     * @return  void
     */
    function DownloadSave(int $did, int $dcounter, string $durl, string $dfilename, int $dfilesize, string $dweb, string $duser, ?string $ddate = 'no', string $dver, string $dcategory, string $sdcategory, string $description, string $privs, array $Mprivs): void
    {
        if ($privs == 1) {
            if ($Mprivs != '') {
                $privs = implode(',', $Mprivs);
            }
        }

        $sdcategory = addslashes($sdcategory);
        $dcategory = (!$dcategory) ? $sdcategory : addslashes($dcategory);
        $description = addslashes($description);

        if ($ddate == "yes") {
            DB::table('downloads')->where('did', $did)->update(array(
                'dcounter'      => $dcounter,
                'durl'          => $durl,
                'dfilename'     => $dfilename,
                'dfilesize'     => $dfilesize,
                'ddate'         => date("Y-m-d"),
                'dweb'          => $dweb,
                'duser'         => $duser,
                'dver'          => $dver,
                'dcategory'     => $dcategory,
                'ddescription'  => $description,
                'perms'         => $privs,

            ));
        } else {
            DB::table('downloads')->where('did', $did)->update(array(
                'dcounter'      => $dcounter,
                'durl'          => $durl,
                'dfilename'     => $dfilename,
                'dfilesize'     => $dfilesize,
                'dweb'          => $dweb,
                'duser'         => $duser,
                'dver'          => $dver,
                'dcategory'     => $dcategory,
                'ddescription'  => $description,
                'perms'         => $privs,
            ));
        }

        Header('Location: '. site_url('admin.php?op=DownloadAdmin'));
    }

    /**
     * [DownloadAdd description]
     *
     * @param   int     $dcounter     [$dcounter description]
     * @param   string  $durl         [$durl description]
     * @param   string  $dfilename    [$dfilename description]
     * @param   int     $dfilesize    [$dfilesize description]
     * @param   string  $dweb         [$dweb description]
     * @param   string  $duser        [$duser description]
     * @param   string  $dver         [$dver description]
     * @param   string  $dcategory    [$dcategory description]
     * @param   string     $sdcategory   [$sdcategory description]
     * @param   string  $description  [$description description]
     * @param   string  $privs        [$privs description]
     * @param   array   $Mprivs       [$Mprivs description]
     *
     * @return  void
     */
    function DownloadAdd(int $dcounter, string $durl, string $dfilename, int $dfilesize, string $dweb, string $duser, string $dver, string $dcategory, string $sdcategory, string $description, string $privs, array $Mprivs): void
    {
        if ($privs == 1) {
            if ($Mprivs > 1 and $Mprivs <= 127 and $Mprivs != '') {
                $privs = $Mprivs;
            }
        }

        $sdcategory = addslashes($sdcategory);
        $dcategory = (!$dcategory) ? $sdcategory : addslashes($dcategory);
        $description = addslashes($description);

        if (($durl) and ($dfilename)) {
            DB::table('downloads')->insert(array(
                'dcounter'      => 0,
                'durl'          => $durl,
                'dfilename'     => $dfilename,
                'dfilesize'     => 0,
                'ddate'         => date("Y-m-d"),
                'dweb'          => $dweb,
                'duser'         => $duser,
                'dver'          => $dver,
                'dcategory'     => $dcategory,
                'ddescription'  => $description,
                'perms'         => $privs,
            ));
        }
        
        Header('Location: '. site_url('admin.php?op=DownloadAdmin'));
    }

    /**
     * [DownloadDel description]
     *
     * @param   int   $did  [$did description]
     * @param   int   $ok   [$ok description]
     *
     * @return  void
     */
    function DownloadDel(int $did, int $ok = 0): void
    {
        global $f_meta_nom;

        if ($ok == 1) {
            DB::table('downloads')->where('did', $did)->delete();

            Header('Location: '. site_url('admin.php?op=DownloadAdmin'));
        } else {
            global $f_titre;

            include("themes/default/header.php");

            GraphicAdmin(manuel('downloads'));
            adminhead($f_meta_nom, $f_titre);

            echo ' 
            <div class="alert alert-danger">
                <strong>'. __d('two_download', 'ATTENTION : êtes-vous sûr de vouloir supprimer ce fichier téléchargeable ?') .'</strong>
            </div>
            <a class="btn btn-danger" href="'. site_url('admin.php?op=DownloadDel&amp;did='. $did .'&amp;ok=1') .'" >
                '. __d('two_download', 'Oui') .'
            </a>
            &nbsp;
            <a class="btn btn-secondary" href="'. site_url('admin.php?op=DownloadAdmin') .'" >
                '. __d('two_download', 'Non') .'
            </a>';
            
            css::adminfoot('', '', '', '');
        }
    }

}