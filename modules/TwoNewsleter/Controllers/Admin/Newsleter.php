<?php

namespace Modules\TwoNewsleter\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Newsleter extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'lnl';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'lnl';

        $this->f_titre = __d('two_newsleter', 'Petite Lettre D\'information');

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
     * [error_handler description]
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  void
     */
    function error_handler(string $ibid): void
    {
        echo '<p align="center"><span class="rouge">'. __d('two_newsleter', 'Merci d\'entrer l\'information en fonction des spécifications') .'<br /><br />';
        echo $ibid .'</span><br /><a href="' . site_url('index.php') .'" class="noir">'. __d('two_newsleter', 'Retour en arrière') .'</a></p>';
    }

    /**
     * [ShowHeader description]
     *
     * @return  void
     */
    function ShowHeader(): void 
    {
        echo '
        <table data-toggle="table" class="table-no-bordered">
            <thead class="d-none">
                <tr>
                    <th class="n-t-col-xs-1" data-align="">
                        ID
                    </th>
                    <th class="n-t-col-xs-8" data-align="">
                        '. __d('two_newsleter', 'Entête') .'
                    </th>
                    <th class="n-t-col-xs-1" data-align="">
                        Type
                    </th>
                    <th class="n-t-col-xs-2" data-align="right">
                        '. __d('two_newsleter', 'Fonctions') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $lnl_head_foot = DB::table('lnl_head_foot')->select('ref', 'text', 'html')->where('type', 'HED')->orderBy('ref')->get();

        foreach($lnl_head_foot as $head_foot) {

            $text = nl2br(htmlspecialchars($head_foot['text'], ENT_COMPAT | ENT_HTML401, 'utf-8'));
            
            if (strlen($text) > 100) {
                $text = substr($text, 0, 100) .'<span class="text-danger"> .....</span>';
            }
            
            if ($head_foot['html'] == 1) { 
                $head_foot['html'] = 'html';
            } else {
                $head_foot['html'] = 'txt';
            }
            
            echo '
                <tr>
                    <td>
                        '. $head_foot['ref'] .'
                    </td>
                    <td>
                        '. $text .'
                    </td>
                    <td>
                        <code>'. $head_foot['html'] .'</code>
                    </td>
                    <td>
                        <a href="' . site_url('admin.php?op=lnl_Shw_Header&amp;Headerid='. $head_foot['ref']) .'" >
                            <i class="fa fa-edit fa-lg me-2" title="'. __d('two_newsleter', 'Editer') .'" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                        </a>
                        <a href="' . site_url('admin.php?op=lnl_Sup_Header&amp;Headerid='. $head_foot['ref']) .'" class="text-danger">
                            <i class="fas fa-trash fa-lg" title="'. __d('two_newsleter', 'Effacer') .'" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                        </a>
                    </td>
                </tr>';
        }

        echo '
            </tbody>
        </table>';
    }

    /**
     * [Detail_Header_Footer description]
     *
     * @param   string  $ibid  [$ibid description]
     * @param   string  $type  [$type description]
     *
     * @return  void
     */
    function Detail_Header_Footer(string $ibid, string $type): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        $head_foot = DB::table('lnl_head_foot')->select('text', 'html')->where('type', $type)->where('ref', $ibid)->first();

        echo '
        <hr />
        <h3 class="mb-2">';

        if ($type == "HED") {
            echo __d('two_newsleter', 'Message d\'entête');
        } else {
            echo __d('two_newsleter', 'Message de pied de page');
        }

        echo ' - '. __d('two_newsleter', 'Prévisualiser');

        if ($head_foot['html'] == 1) {
            echo '<code> HTML</code></h3>
            <div class="card card-body">'. $head_foot['text'] .'</div>';
        } else {
            echo '<code>'. __d('two_newsleter', 'TEXTE') .'</code></h3>
            <div class="card card-body">'. nl2br($head_foot['text']) .'</div>';
        }

        echo '
        <hr />
        <form action="' . site_url('admin.php') .'" method="post" name="adminForm">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="xtext">'. __d('two_newsleter', 'Texte') .'</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" cols="70" rows="20" name="xtext" >'. htmlspecialchars($head_foot['text'], ENT_COMPAT | ENT_HTML401, 'utf-8') .'</textarea>
                </div>
            </div>';

        if ($head_foot['html'] == 1) {
            Config::get('editeur.tiny_mce_relurl', false);

            echo editeur::aff_editeur('xtext', '');
        }

        if ($type == 'HED') {
            echo '<input type="hidden" name="op" value="lnl_Add_Header_Mod" />';
        } else {
            echo '<input type="hidden" name="op" value="lnl_Add_Footer_Mod" />';
        }

        echo '
            <input type="hidden" name="ref" value="'. $ibid .'" />
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <button class="btn btn-primary me-1" type="submit">'. __d('two_newsleter', 'Valider') .'</button>
                    <a class="btn btn-secondary" href="' . site_url('admin.php?op=lnl') .'" >'. __d('two_newsleter', 'Retour en arrière') .'</a>
                </div>
            </div>
        </form>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [ShowBody description]
     *
     * @return  void
     */
    function ShowBody(): void
    {
        echo '
        <table data-toggle="table" class="table-no-bordered">
            <thead class="d-none">
                <tr>
                    <th class="n-t-col-xs-1" data-align="">
                        ID
                    </th>
                    <th class="n-t-col-xs-8" data-align="">
                        '. __d('two_newsleter', 'Corps de message') .'
                    </th>
                    <th class="n-t-col-xs-1" data-align="">
                        Type
                    </th>
                    <th class="n-t-col-xs-2" data-align="right">
                        '. __d('two_newsleter', 'Fonctions') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $lnl_body = DB::table('lnl_body')->select('ref', 'text', 'html')->orderBy('ref')->get();

        foreach ($lnl_body as $body) {
            $text = nl2br(htmlspecialchars($body['text'], ENT_COMPAT | ENT_HTML401, 'utf-8'));

            if (strlen($text) > 200) {
                $text = substr($text, 0, 200) .'<span class="text-danger"> .....</span>';
            }

            if ($body['html'] == 1) {
                $body['html'] = 'html';
            } else {
                $body['html'] = 'txt';
            }

            echo '
            <tr>
                <td>
                    '. $body['ref'] .'
                </td>
                <td>
                    '. $text .'
                </td>
                <td>
                    <code>'. $body['html'] .'</code>
                </td>
                <td>
                    <a href="' . site_url('admin.php?op=lnl_Shw_Body&amp;Bodyid='. $body['ref']) .'">
                        <i class="fa fa-edit fa-lg me-2" title="'. __d('two_newsleter', 'Editer') .'" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                    </a>
                    <a href="' . site_url('admin.php?op=lnl_Sup_Body&amp;Bodyid='. $body['ref']) .'" class="text-danger">
                        <i class="fas fa-trash fa-lg" title="'. __d('two_newsleter', 'Effacer') .'" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                    </a>
                </td>
            </tr>';
        }

        echo '
            </tbody>
        </table>';
    }

    /**
     * [Detail_Body description]
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  void
     */
    function Detail_Body(string $ibid): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-2">'. __d('two_newsleter', 'Corps de message') .' - ';

        $body = DB::table('lnl_body')->select('text', 'html')->where('ref', $ibid)->first();

        if ($body['html'] == 1) {
            echo __d('two_newsleter', 'Prévisualiser') .' <code>HTML</code></h3>
            <div class="card card-body">'. $body['text'] .'</div>';
        } else {
            echo __d('two_newsleter', 'Prévisualiser') .' <code>'. __d('two_newsleter', 'TEXTE') .'</code></h3>
            <div class="card card-body">'. nl2br($body['text']) .'</div>';
        }

        echo '
        <form action="' . site_url('admin.php') .'" method="post" name="adminForm">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="xtext">'. __d('two_newsleter', 'Corps de message') .'</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" rows="30" name="xtext" >'. htmlspecialchars($body['text'], ENT_COMPAT | ENT_HTML401, 'utf-8') .'</textarea>
                </div>
            </div>';

        if ($body['html'] == 1) {
            Config::get('editeur.tiny_mce_relurl', false);

            echo editeur::aff_editeur("xtext", "false");
        }

        echo '
            <input type="hidden" name="op" value="lnl_Add_Body_Mod" />
            <input type="hidden" name="ref" value="'. $ibid .'" />
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <button class="btn btn-primary" type="submit">'. __d('two_newsleter', 'Valider') .'</button>&nbsp;
                    <button href="javascript:history.go(-1)" class="btn btn-secondary">'. __d('two_newsleter', 'Retour en arrière') .'</button>
                </div>
            </div>
        </form>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [Add_Body description]
     *
     * @return  void
     */
    function Add_Body(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-2">'. __d('two_newsleter', 'Corps de message') .'</h3>
        <form id="lnlbody" action="' . site_url('admin.php') .'" method="post" name="adminForm">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="html">'. __d('two_newsleter', 'Format de données') .'</label>
                    <div class="col-sm-8">
                    <input class="form-control" id="html" type="number" min="0" max="1" step="1" value="1" name="html" required="required" />
                    <span class="help-block"> <code>html</code> ==&#x3E; [1] / <code>text</code> ==&#x3E; [0]</span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="xtext">'. __d('two_newsleter', 'Texte') .'</label>
                    <div class="col-sm-12">
                    <textarea class="tin form-control" id="xtext" rows="30" name="xtext" ></textarea>
                    </div>
                </div>';

        config::get('editeru.tiny_mce_relurl', false);

        echo editeur::aff_editeur("xtext", "false");

        echo '
                <div class="mb-3 row">
                    <input type="hidden" name="op" value="lnl_Add_Body_Submit" />
                    <button class="btn btn-primary col-sm-12 col-md-6" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. __d('two_newsleter', 'Ajouter') .' '. __d('two_newsleter', 'corps de message') .'</button>
                    <a href="' . site_url('admin.php?op=lnl') .'" class="btn btn-secondary col-sm-12 col-md-6">'. __d('two_newsleter', 'Retour en arrière') .'</a>
                </div>
            </fieldset>
        </form>';

        $fv_parametres = '
            html: {
            validators: {
                regexp: {
                    regexp:/[0-1]$/,
                    message: "0 | 1"
                }
            }
        },';

        $arg1 = '
        var formulid = ["lnlbody"];';

        css::adminfoot('fv', $fv_parametres, $arg1, '');
    }

    /**
     * [Add_Body_Submit description]
     *
     * @param   string  $Ytext  [$Ytext description]
     * @param   string  $Yhtml  [$Yhtml description]
     *
     * @return  void
     */
    function Add_Body_Submit(string $Ytext, string $Yhtml): void
    {
        DB::table('lnl_body')->insert(array(
            'ref'        => 0,
            'html'       => $Yhtml,
            'text'       => $Ytext,
            'status'     => 'OK',
        ));
    }

    /**
     * [ShowFooter description]
     *
     * @return  void
     */
    function ShowFooter(): void
    {
        echo '
        <table data-toggle="table" class="table-no-bordered">
            <thead class="d-none">
                <tr>
                    <th class="n-t-col-xs-1" data-align="">
                        ID
                    </th>
                    <th class="n-t-col-xs-8" data-align="">
                        '. __d('two_newsleter', 'Pied') .'
                    </th>
                    <th class="n-t-col-xs-1" data-align="">
                        Type
                    </th>
                    <th class="n-t-col-xs-2" data-align="right">
                        '. __d('two_newsleter', 'Fonctions') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $lnl_head_foot = DB::table('lnl_head_foot')->select('ref', 'text', 'html')->where('type', 'FOT')->orderBy('ref')->get();

        foreach($lnl_head_foot as $head_foot) {
            $text = nl2br(htmlspecialchars($head_foot['text'], ENT_COMPAT | ENT_HTML401, 'utf-8'));

            if (strlen($text) > 100) {
                $text = substr($text, 0, 100) .'<span class="text-danger"> .....</span>';
            }

            if ($head_foot['html'] == 1) { 
                $head_foot['html'] = 'html';
            } else {
                $head_foot['html'] = 'txt';
            }

            echo '
                <tr>
                    <td>
                        '. $head_foot['ref'] .'</td>
                    <td>
                        '. $text .'</td>
                    <td>
                        <code>'. $head_foot['html'] .'</code></td>
                    <td>
                        <a href="' . site_url('admin.php?op=lnl_Shw_Footer&amp;Footerid='. $head_foot['ref']) .'" >
                            <i class="fa fa-edit fa-lg me-2" title="'. __d('two_newsleter', 'Editer') .'" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                        </a>
                        <a href="' . site_url('admin.php?op=lnl_Sup_Footer&amp;Footerid='. $head_foot['ref']) .'" class="text-danger">
                            <i class="fas fa-trash fa-lg" title="'. __d('two_newsleter', 'Effacer') .'" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                        </a>
                    </td>
                </tr>';
        }

        echo '
            </tbody>
        </table>';
    }

    /**
     * [Add_Header_Footer description]
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  void
     */
    function Add_Header_Footer(string $ibid): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        if ($ibid == 'HED') {
            $ti = "message d'entête";
            $va = 'lnl_Add_Header_Submit';
        } else {
            $ti = "Message de pied de page";
            $va = 'lnl_Add_Footer_Submit';
        }

        echo '
            <hr />
            <h3 class="mb-2">'. ucfirst(__d('two_newsleter', $ti)) .'</h3>
            <form id="lnlheadfooter" action="' . site_url('admin.php') .'" method="post" name="adminForm">
            <fieldset>
                <div class="mb-3">
                    <label class="col-form-label" for="html">'. __d('two_newsleter', 'Format de données') .'</label>
                    <div>
                        <input class="form-control" id="html" type="number" min="0" max="1" value="1" name="html" required="required" />
                        <span class="help-block"> <code>html</code> ==&#x3E; [1] / <code>text</code> ==&#x3E; [0]</span>
                    </div>
                    </div>
                <div class="mb-3">
                    <label class="col-form-label" for="xtext">'. __d('two_newsleter', 'Texte') .'</label>
                    <div>
                    <textarea class="form-control" id="xtext" rows="20" name="xtext" ></textarea>
                    </div>
                </div>
                <div class="mb-3">';

        Config::get('editeur.tiny_mce_relurl', false);

        echo editeur::aff_editeur('xtext', 'false');

        echo '
                    <input type="hidden" name="op" value="'. $va .'" />
                    <button class="btn btn-primary col-sm-12 col-md-6" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. __d('two_newsleter', 'Ajouter') .' '. __d('two_newsleter', $ti) .'</button>
                </div>
            </fieldset>
        </form>';

        $fv_parametres = '
            html: {
            validators: {
                regexp: {
                    regexp:/[0-1]$/,
                    message: "0 | 1"
                }
            }
        },';

        $arg1 = '
        var formulid = ["lnlheadfooter"];';

        css::adminfoot('fv', $fv_parametres, $arg1, '');
    }

    /**
     * [Add_Header_Footer_Submit description]
     *
     * @param   string  $ibid   [$ibid description]
     * @param   string  $xtext  [$xtext description]
     * @param   string  $xhtml  [$xhtml description]
     *
     * @return  void
     */
    function Add_Header_Footer_Submit(string $ibid, string $xtext, string $xhtml): void 
    {
        if ($ibid == "HED") {
            DB::table('lnl_head_foot')->insert(array(
                'ref'        => 0,
                'type'       => 'HED',
                'html'       => $xhtml,
                'text'       => $xtext,
                'status'     => 'OK',
            ));

        } else {
            DB::table('lnl_head_foot')->insert(array(
                'ref'        => 0,
                'type'       => 'FOT',
                'html'       => $xhtml,
                'text'       => $xtext,
                'status'     => 'OK',
            ));
        }
    }

    /**
     * [main description]
     *
     * @return  void
     */
    function main(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-2">'. __d('two_newsleter', 'Petite Lettre D\'information') .'</h3>
        <ul class="nav flex-md-row flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="' . site_url('admin.php?op=lnl_List') .'">'. __d('two_newsleter', 'Liste des LNL envoyées') .'</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="' . site_url('admin.php?op=lnl_User_List') .'">'. __d('two_newsleter', 'Afficher la liste des prospects') .'</a>
            </li>
        </ul>
        <h4 class="my-3"><a href="' . site_url('admin.php?op=lnl_Add_Header') .'" ><i class="fa fa-plus-square me-2"></i></a>'. __d('two_newsleter', 'Message d\'entête') .'</h4>';
        
        ShowHeader();

        echo '
        <h4 class="my-3"><a href="' . site_url('admin.php?op=lnl_Add_Body') .'" ><i class="fa fa-plus-square me-2"></i></a>'. __d('two_newsleter', 'Corps de message') .'</h4>';
        
        ShowBody();

        echo '
        <h4 class="my-3"><a href="' . site_url('admin.php?op=lnl_Add_Footer') .'"><i class="fa fa-plus-square me-2"></i></a>'. __d('two_newsleter', 'Message de pied de page') .'</h4>';
        
        ShowFooter();

        echo '
        <hr />
        <h4>'. __d('two_newsleter', 'Assembler une lettre et la tester') .'</h4>
        <form id="ltesto" action="' . site_url('admin.php') .'" method="post">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-floating mb-3">
                    <input class="form-control" type="number" name="Xheader" id="testXheader"min="0" />
                    <label for="testXheader">'. __d('two_newsleter', 'Entête') .'</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating mb-3">
                    <input class="form-control" type="number" name="Xbody" id="testXbody" maxlength="11" />
                    <label for="testXbody">'. __d('two_newsleter', 'Corps') .'</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating mb-3">
                    <input class="form-control" type="number" name="Xfooter" id="testXfooter" min="0" />
                    <label for="testXfooter">'. __d('two_newsleter', 'Pied') .'</label>
                    </div>
                </div>
                <div class="mb-3 col-sm-12">
                    <input type="hidden" name="op" value="lnl_Test" />
                    <button class="btn btn-primary" type="submit">'. __d('two_newsleter', 'Valider') .'</button>
                </div>
            </div>
        </form>
        <hr />
        <h4>'. __d('two_newsleter', 'Envoyer La Lettre') .'</h4>
        <form id="lsendo" action="' . site_url('admin.php') .'" method="post">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-floating mb-3">
                    <input class="form-control" type="number" name="Xheader" id="Xheader" />
                    <label for="Xheader">'. __d('two_newsleter', 'Entête') .'</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating mb-3">
                    <input class="form-control" type="number" name="Xbody" id="Xbody" min="0" />
                    <label for="Xbody">'. __d('two_newsleter', 'Corps') .'</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating mb-3">
                    <input class="form-control" type="number" name="Xfooter" id="Xfooter" />
                    <label for="Xfooter">'. __d('two_newsleter', 'Pied') .'</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-floating mb-3">
                    <input class="form-control" type="text" maxlength="255" id="Xsubject" name="Xsubject" />
                    <label for="Xsubject">'. __d('two_newsleter', 'Sujet') .'</label>
                    <span class="help-block text-end"><span id="countcar_Xsubject"></span></span>
                    </div>
                </div>
                <hr />
                <div class="mb-3 col-sm-12">
                    <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" value="All" checked="checked" id="tous" name="Xtype" />
                    <label class="form-check-label" for="tous">'. __d('two_newsleter', 'Tous les Utilisateurs') .'</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" value="Mbr" id="mem" name="Xtype" />
                    <label class="form-check-label" for="mem">'. __d('two_newsleter', 'Seulement aux membres') .'</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" value="Out" id="prosp" name="Xtype" />
                    <label class="form-check-label" for="prosp">'. __d('two_newsleter', 'Seulement aux prospects') .'</label>
                    </div>
                </div>';

        $tmp_groupe = '';
        foreach (groupe::liste_group() as $groupe_id => $groupe_name) {
            if ($groupe_id == '0') {
                $groupe_id = '';
            }

            $tmp_groupe .= '<option value="'. $groupe_id .'">'. $groupe_name .'</option>';
        }

        echo '
                <div class="mb-3 col-sm-12">
                    <select class="form-select" name="Xgroupe">'. $tmp_groupe .'</select>
                </div>
                <input type="hidden" name="op" value="lnl_Send" />
                <div class="mb-3 col-sm-12">
                    <button class="btn btn-primary" type="submit">'. __d('two_newsleter', 'Valider') .'</button>
                </div>
            </div>
            </form>';

        $fv_parametres = '
                Xbody: {
                validators: {
                    regexp: {
                    regexp:/^\d{1,11}$/,
                    message: "0 | 1"
                    }
                }
            },';

        $arg1 = '
            var formulid = ["ltesto","lsendo"];
            inpandfieldlen("Xsubject",255);';

        css::adminfoot('fv', $fv_parametres, $arg1, '');
    }

    /**
     * [Del_Question description]
     *
     * @param   string  $retour  [$retour description]
     * @param   string  $param   [$param description]
     *
     * @return  void
     */
    function Del_Question(string $retour, string $param): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <div class="alert alert-danger">'. __d('two_newsleter', 'Etes-vous sûr de vouloir effacer cet Article ?') .'</div>
        <a href="' . site_url('admin.php?op='. $retour .'&amp;'. $param) .'" class="btn btn-danger btn-sm">'. __d('two_newsleter', 'Oui') .'</a>
        <a href="javascript:history.go(-1)" class="btn btn-secondary btn-sm">'. __d('two_newsleter', 'Non') .'</a>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [Test description]
     *
     * @param   string  $Yheader  [$Yheader description]
     * @param   string  $Ybody    [$Ybody description]
     * @param   string  $Yfooter  [$Yfooter description]
     *
     * @return  void
     */
    function Test(string $Yheader, string $Ybody, string $Yfooter): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        $result = sql_query("SELECT  FROM ". $NPDS_Prefix ."lnl_head_foot WHERE = AND =''");
        $Xheader = sql_fetch_row($result);

        $lnl_head_foot = DB::table('lnl_head_foot')
            ->select('text', 'html')
            ->where('type', 'HED')
            ->where('ref', $Yheader)
            ->first();

        $lnl_body = DB::table('lnl_body')
            ->select('text')
            ->where('html', $lnl_head_foot['html'])
            ->where('ref', $Ybody)
            ->first();

        $head_foot = DB::table('lnl_head_foot')
            ->select('text')
            ->where('type', 'FOT')
            ->where('html', $lnl_head_foot['html'])
            ->where('ref', $Yfooter)
            ->first();

        if ($Xheader[1] == 1) {
            echo '
            <hr />
            <h3 class="mb-3">'. __d('two_newsleter', 'Prévisualiser') .' HTML</h3>';
            
            $Xmime = 'html-nobr';
            $message = metalang::meta_lang($lnl_head_foot['text'] . $$lnl_body['text'] . $head_foot['text']);
        } else {
            echo '
            <hr />
            <h3 class="mb-3">'. __d('two_newsleter', 'Prévisualiser') .' '. __d('two_newsleter', 'TEXTE') .'</h3>';
            
            $Xmime = 'text';
            $message = metalang::meta_lang(nl2br($lnl_head_foot['text']) . nl2br($$lnl_body['text']) . nl2br($head_foot['text']));
        }

        echo '
        <div class="card card-body">
        '. $message .'
        </div>
        <a class="btn btn-secondary my-3" href="javascript:history.go(-1)" >'. __d('two_newsleter', 'Retour en arrière') .'</a>';

        mailler::send_email($adminmail, 'LNL TEST', $message, Config::get('npds.adminmail'), true, $Xmime, '');

        css::adminfoot('', '', '', '');
    }

    /**
     * [lnl_list description]
     *
     * @return  void
     */
    function lnl_list():  void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_newsleter', 'Liste des LNL envoyées') .'</h3>
        <table data-toggle="table" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <tr>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right">
                        ID
                    </th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right">
                        '. __d('two_newsleter', 'Entête') .'
                    </th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right">
                        '. __d('two_newsleter', 'Corps') .'
                    </th>
                    <th class="n-t-col-xs-1" data-halign="center" data-align="right">
                        '. __d('two_newsleter', 'Pied') .'
                    </th>
                    <th data-halign="center" data-align="right">
                        '. __d('two_newsleter', 'Nbre d\'envois effectués') .'
                    </th>
                    <th data-halign="center" data-align="center">
                        '. __d('two_newsleter', 'Type') .'
                    </th>
                    <th data-halign="center" data-align="right">
                        '. __d('two_newsleter', 'Date') .'
                    </th>
                    <th data-halign="center" data-align="center">
                        '. __d('two_newsleter', 'Etat') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $lnl_send = DB::table('lnl_send')->select('ref', 'header', 'body', 'footer', 'number_send', 'type_send', 'date', 'status')->orderBy('date')->get();

        foreach($lnl_send as $send) {

            echo '
                <tr>
                    <td>
                        '. $$send['ref'] .'
                    </td>
                    <td>
                        '. $$send['header'] .'
                    </td>
                    <td>
                        '. $$send['body'] .'
                    </td>
                    <td>
                        '. $$send['footer'] .'
                    </td>
                    <td>
                        '. $$send['number_send'] .'
                    </td>
                    <td>
                        '. $$send['type_send'] .'
                    </td>
                    <td>
                        '. $$send['date'] .'
                    </td>';

            if ($$send['status'] == "NOK") {
                echo '<td class="text-danger">'. $$send['status'] .'</td>';
            } else {
                echo '<td>'. $$send['status'] .'</td>';
            }

            echo '</tr>';
        }

        echo '
            </tbody>
        </table>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [lnl_user_list description]
     *
     * @return  void
     */
    function lnl_user_list(): void 
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('lnl'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-2">'. __d('two_newsleter', 'Liste des prospects') .'</h3>
        <table id="tad_prospect" data-toggle="table" data-search="true" data-striped="true" data-mobile-responsive="true" data-show-export="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <tr>
                    <th class="n-t-col-xs-5" data-halign="center" data-sortable="true">
                        '. __d('two_newsleter', 'E-mail') .'
                    </th>
                    <th class="n-t-col-xs-3" data-halign="center" data-align="right" data-sortable="true">
                        '. __d('two_newsleter', 'Date') .'
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="center" data-sortable="true">
                        '. __d('two_newsleter', 'Etat') .'
                    </th>
                    <th class="n-t-col-xs-2" data-halign="center" data-align="right" data-sortable="true">
                        '. __d('two_newsleter', 'Fonctions') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $lnl_outside_users = DB::table('lnl_outside_users')->select('email', 'date', 'status')->orderBy('date')->get();

        foreach ($lnl_outside_users as $outside_users) {

            echo '
                <tr>
                    <td>
                        '. $outside_users['email'] .'
                    </td>
                    <td>
                        '. $outside_users['date'] .'
                    </td>';

            if ($outside_users['status'] == "NOK") { 
                echo '<td class="text-danger">'. $outside_users['status'] .'</td>';
            } else {
                echo '<td class="text-success">'. $outside_users['status'] .'</td>';
            }

            echo '<td>
                    <a href="' . site_url('admin.php?op=lnl_Sup_User&amp;lnl_user_email='. $outside_users['email']) .'" class="text-danger">
                        <i class="fas fa-trash fa-lg text-danger" data-bs-toggle="tooltip" title="'. __d('two_newsleter', 'Effacer') .'"></i>
                    </a>
                </td>
            </tr>';
        }

        echo '
            </tbody>
        </table>
        <br /><a href="javascript:history.go(-1)" class="btn btn-secondary">'. __d('two_newsleter', 'Retour en arrière') .'</a>';

        css::adminfoot('', '', '', '');
    }

    switch ($op) {
        case "Sup_Header":
            Del_Question("lnl_Sup_HeaderOK", "Headerid=$Headerid");
            break;

        case "Sup_Body":
            Del_Question("lnl_Sup_BodyOK", "Bodyid=$Bodyid");
            break;

        case "Sup_Footer":
            Del_Question("lnl_Sup_FooterOK", "Footerid=$Footerid");
            break;

        case "Sup_HeaderOK":
            DB::table('lnl_head_foot')->where('ref', $Headerid)->delete();

            header('location: ' . site_url('admin.php?op=lnl'));
            break;

        case "Sup_BodyOK":
            DB::table('lnl_body')->where('ref', $Bodyid)->delete();

            header('location: ' . site_url('admin.php?op=lnl'));
            break;

        case "Sup_FooterOK":
            DB::table('lnl_head_foot')->where('ref', $Footerid)->delete();

            header('location: ' . site_url('admin.php?op=lnl'));
            break;

        case "Shw_Header":
            Detail_Header_Footer($Headerid, "HED");
            break;

        case "Shw_Body":
            Detail_Body($Bodyid);
            break;

        case "Shw_Footer":
            Detail_Header_Footer($Footerid, "FOT");
            break;

        case "Add_Header":
            Add_Header_Footer("HED");
            break;

        case "Add_Header_Submit":
            Add_Header_Footer_Submit("HED", $xtext, $html);
            header('location: ' . site_url('admin.php?op=lnl'));
            break;

        case "Add_Header_Mod":
            DB::table('lnl_head_foot')->where('ref', $ref)->update(array(
                'text'  => $xtext,
            ));

            header('location: ' . site_url('admin.php?op=lnl_Shw_Header&Headerid='. $ref));
            break;

        case "Add_Body":
            Add_Body();
            break;

        case "Add_Body_Submit":
            Add_Body_Submit($xtext, $html);
            header("location: admin.php?op=lnl");
            break;

        case "Add_Body_Mod":
            DB::table('lnl_body')->where('ref', $ref)->update(array(
                'text'  => $xtext,
            ));

            header('location: ' . site_url('admin.php?op=lnl_Shw_Body&Bodyid='. $ref));
            break;

        case "Add_Footer":
            Add_Header_Footer("FOT");
            break;

        case "Add_Footer_Submit":
            Add_Header_Footer_Submit("FOT", $xtext, $html);
            header('location: ' . site_url('admin.php?op=lnl'));
            break;

        case "Add_Footer_Mod":
            DB::table('lnl_head_foot')->where('ref', $ref)->update(array(
                'text'  => $xtext,
            ));

            header('location: ' . site_url('admin.php?op=lnl_Shw_Footer&Footerid='. $ref));
            break;

        case "Test":
            Test($Xheader, $Xbody, $Xfooter);
            break;

        case "List":
            lnl_list();
            break;

        case "User_List":
            lnl_user_list();
            break;

        case "Sup_User":
            DB::table('lnl_outside_users')->where('email', $lnl_user_email)->delete();

            header('location: ' . site_url('admin.php?op=lnl_User_List'));
            break;

        case "Send":
            $deb = 0;

            // nombre de messages envoyé par boucle.
            $limit = 50; 
            
            if (!isset($debut)) {
                $debut = 0;
            }

            if (!isset($number_send)) {
                $number_send = 0;
            }

            $nuke_url = Config::get('npds.nuke_url');

            $lnl_head_foot = DB::table('lnl_head_foot')
                ->select('text', 'html')
                ->where('type', 'HED')
                ->where('ref', $Xheader)
                ->first();

            $lnl_body = DB::table('lnl_body')
                ->select('text', 'html')
                ->where('html', $lnl_head_foot['html'])
                ->where('ref', $Xbody)
                ->first();

            $head_foot = DB::table('lnl_head_foot')
                ->select('text', 'html')
                ->where('type', 'FOT')
                ->where('html', $lnl_head_foot['html'])
                ->where('ref', $Xfooter)
                ->get();

            $subject = stripslashes($Xsubject);
            $message = $lnl_head_foot['text'] . $lnl_body['text'] . $head_foot['text'];

            $Xmime = (($lnl_head_foot['html'] == 1) ? 'html-nobr' : 'text');

            if ($Xtype == "All") {
                $Xtype = "Out";
                $OXtype = "All";
            }

            // Outside Users
            if ($Xtype == "Out") {
                $nrows = DB::table('lnl_outside_users')->select('email')->where('status', 'OK')->get();

                $result_lnl_outside_users = DB::table('lnl_outside_users')
                                                ->select('email')
                                                ->where('status', 'OK')
                                                ->orderBy('email')
                                                ->limit($limit)
                                                ->offset($debut)
                                                ->get();

                foreach ($result_lnl_outside_users as $outside_users) {
                    if (($$outside_users['email'] != "Anonyme") or ($$outside_users['email'] != "Anonymous")) {
                        
                        if ($$outside_users['email'] != '') {
                            if (($message != '') and ($subject != '')) {
                                
                                if ($Xmime == "html-nobr") {
                                    $Xmessage = $message ."<br /><br /><hr noshade>";
                                    $Xmessage .= __d('two_newsleter', 'Pour supprimer votre abonnement à notre Lettre, suivez ce lien') ." : <a href=\"". site_url('lnl.php?op=unsubscribe&email='. $outside_users['email']) ."\">". __d('two_newsleter', 'Modifier') ."</a>";
                                } else {
                                    $Xmessage = $message ."\n\n------------------------------------------------------------------\n";
                                    $Xmessage .= __d('two_newsleter', 'Pour supprimer votre abonnement à notre Lettre, suivez ce lien') ." : ".  site_url('lnl.php?op=unsubscribe&email='. $outside_users['email']) ."";
                                }

                                mailler::send_email($$outside_users['email'], $subject, metalang::meta_lang($Xmessage), "", true, $Xmime, '');
                                $number_send++;
                            }
                        }
                    }
                }
            }

            // NPDS Users
            if ($Xtype == 'Mbr') {
                if ($Xgroupe != '') {

                    $nrows = DB::table('users')
                        ->select('users.uid')
                        ->join('users_status', 'users.uid', '=', 'users_status.uid')
                        ->where('users_status.open', 1)
                        ->where('users.email', '!=', '') 
                        ->where('users_status.groupe', 'like', '%'.$Xgroupe.',%')
                        ->orWhere('users_status.groupe', 'like', '%,'.$Xgroupe.'%')
                        ->orWhere('users_status.groupe', '=', $Xgroupe)
                        ->where('users.user_lnl', 1)
                        ->count();

                    $resultGP = DB::table('users')
                        ->select('users.uid', 'users.uid', 'users_status.groupe')
                        ->join('users_status', 'users.uid', '=', 'users_status.uid')
                        ->where('users_status.open', 1)
                        ->where('users.email', '!=', '') 
                        ->where('users_status.groupe', 'like', '%'.$Xgroupe.',%')
                        ->orWhere('users_status.groupe', 'like', '%,'.$Xgroupe.'%')
                        ->orWhere('users_status.groupe', '=', $Xgroupe)
                        ->where('users.user_lnl', 1)
                        ->orderBy('users.email')
                        ->limit($limit)
                        ->offser($debut)
                        ->get();

                    foreach ($resultGP as $user_groupe) {   
                        $tab_groupe = explode(',', $user_groupe['groupe']);

                        if ($tab_groupe)
                            foreach ($tab_groupe as $groupevalue) {
                                if ($groupevalue == $Xgroupe) {
                                    $result[] = $user_groupe['email'];
                                }
                            }
                    }

                    if (is_array($result)) { 
                        $boucle = true;
                    } else {
                        $boucle = false;
                    }

                } else {
                    $nrows = DB::table('users')
                        ->select('users.uid')
                        ->join('users_status', 'users.uid', '=', 'users_status.uid')
                        ->where('users_status.open', 1)
                        ->where('users.user_lnl', 1)
                        ->where('users.email', '!=', '')
                        ->count();

                    $result = DB::table('users')
                        ->select('users.email')
                        ->join('users_status', 'users.uid', '=', 'users_status.uid')
                        ->where('users_status.open', 1)
                        ->where('users.user_lnl', 1)
                        ->orderBy('email')
                        ->limit($limit)
                        ->offset($debut)
                        ->get();

                    $boucle = true;
                }

                if ($boucle) {
                    foreach ($result as $user_lnl) {  
                        if (($user_lnl['email'] != "Anonyme") or ($user_lnl['email'] != "Anonymous")) {
                            if ($user_lnl['email'] != '') {

                                if (($message != '') and ($subject != '')) {
                                    mailler::send_email($user_lnl['email'], $subject, metalang::meta_lang($message), "", true, $Xmime, '');
                                    $number_send++;
                                }
                            }
                        }
                    }
                }
            }

            $deb = $debut + $limit;
            $chartmp = '';
            
            settype($OXtype, 'string');

            if ($deb >= $nrows) {
                if ((($OXtype == "All") and ($Xtype == "Mbr")) or ($OXtype == "")) {
                    if (($message != '') and ($subject != '')) {
                        $timeX = date("Y-m-d H:m:s", time());
                        
                        if ($OXtype == "All") {
                            $Xtype = "All";
                        }

                        if (($Xtype == "Mbr") and ($Xgroupe != "")) {
                            $Xtype = $Xgroupe;
                        }

                        DB::table('lnl_send')->insert(array(
                            'ref'           => 0,
                            'header'        => $Xheader,
                            'body'          => $Xbody,
                            'footer'        => $Xfooter,
                            'number_send'   => $number_send,
                            'type_send'     => $Xtype,
                            'date'          => $timeX,
                            'status'        => 'OK',
                        ));
                    }

                    header('location: ' . site_url('admin.php?op=lnl'));
                    break;
                } else {
                    if ($OXtype == "All") {
                        $chartmp = "$Xtype : $nrows / $nrows";
                        $deb = 0;
                        $Xtype = "Mbr";

                        $nrows = DB::table('users')
                            ->select('users.uid')
                            ->join('users_status', 'users.uid', '=', 'users_status.uid')
                            ->where('users_status.open', 1)
                            ->where('users.user_lnl', 1)
                            ->where('users.name', '!=', '')
                            ->where('users.email', '!=', '')
                            ->count();
                    }
                }
            }

            if ($chartmp == '') {
                $chartmp = "$Xtype : $deb / $nrows";
            }

            include("storage/meta/meta.php");
            
            echo "<script type=\"text/javascript\">
                    //<![CDATA[
                    function redirect() {
                        window.location=\"" . site_url('admin.php?op=lnl_Send&debut='. $deb .'&OXtype='. $OXtype .'&Xtype='. $Xtype .'&Xgroupe='. $Xgroupe .'&Xheader='. $Xheader .'&Xbody='. $Xbody .'&Xfooter='. $Xfooter .'&number_send='. $number_send .'&Xsubject='. $Xsubject) ."\";
                    }
                    setTimeout(\"redirect()\",10000);
                    //]]>
                    </script>";
            echo '
                <link href="'. $nuke_url .'/themes/npds-boost_sk/style/style.css" title="default" rel="stylesheet" type="text/css" media="all">
                <link id="bsth" rel="stylesheet" href="'. $nuke_url .'/themes/_skins/default/bootstrap.min.css">
                </head>
                    <body>
                    <div class="d-flex justify-content-center mt-4">
                        <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <div class="text-center mt-4">
                        '. __d('two_newsleter', 'Transmission LNL en cours') .' => '. $chartmp .'<br /><br />NPDS - Portal System
                        </div>
                    </div>
                    </body>
                </html>';
            break;

        default:
            main();
            break;
    }

}