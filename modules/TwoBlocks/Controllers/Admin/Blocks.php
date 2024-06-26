<?php

namespace Modules\TwoBlock\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Blocks extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'blocks';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'blocks';

        $this->f_titre = __d('two_blocks', 'Gestion des blocs');

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
 * [droits_bloc description]
 *
 * @param   string  $member  [$member description]
 * @param   int     $j       [$j description]
 * @param   string  $lb      [$lb description]
 *
 * @return  void
 */
function droits_bloc(string $member, int $j, string $lb): void
{
    echo '
    <div class="mb-3">
        <div class="form-check form-check-inline">';

    $checked = $member == -127 ? ' checked="checked"' : '';

    echo '
            <input type="radio" id="adm'. $j . $lb .'" name="members" value="-127" '. $checked .' class="form-check-input" />
            <label class="form-check-label" for="adm'. $j . $lb .'">'. __d('two_blocks', 'Administrateurs') .'</label>
        </div>
        <div class="form-check form-check-inline">';

    $checked = $member == -1 ? ' checked="checked"' : '';

    echo '
            <input type="radio" id="ano'. $j . $lb .'" name="members" value="-1" '. $checked .' class="form-check-input" />
            <label class="form-check-label" for="ano'. $j . $lb .'">'. __d('two_blocks', 'Anonymes') .'</label>
        </div>
        <div class="form-check form-check-inline">';

    if ($member > 0) {
        echo '
                <input type="radio" id="mem'. $j . $lb .'" name="members" value="1" checked="checked" class="form-check-input"/>
                <label class="form-check-label" for="mem'. $j . $lb .'">'. __d('two_blocks', 'Membres') .'</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="tous'. $j . $lb . '" name="members" value="0" class="form-check-input" />
                <label class="form-check-label" for="tous'. $j . $lb .'">'. __d('two_blocks', 'Tous') .'</label>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="Mmember[]" class="col-form-label col-sm-12">'. __d('two_blocks', 'Groupes') .'</label>
            <div class="col-sm-12">
                '. groupe::groupe($member) .'
            </div>
        </div>';
    } else {
        $checked = $member == 0 ? ' checked="checked"' : '';

        echo '
                <input type="radio" id="mem'. $j . $lb .'" name="members" value="1" class="form-check-input" />
                <label class="form-check-label" for="mem'. $j . $lb .'">'. __d('two_blocks', 'Membres') .'</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="tous'. $j . $lb .'" name="members" value="0"' . $checked .' class="form-check-input" />
                <label class="form-check-label" for="tous'. $j . $lb .'">'. __d('two_blocks', 'Tous') .'</label>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="Mmember[]" class="col-form-label col-sm-12">'. __d('two_blocks', 'Groupes') .'</label>
            <div class="col-sm-12">
                '. groupe::groupe($member) .'
            </div>
        </div>';
    }
}

    /**
     * [blocks description]
     *
     * @return  void
     */
    function blocks(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('blocks'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3>' . __d('two_blocks', 'Edition des Blocs de gauche') . '</h3>';
        
        $lblocks = DB::table('lblocks')->select('id', 'title', 'content', 'member', 'Lindex', 'cache', 'actif', 'aide', 'css')->orderBy('Lindex', 'ASC')->get();

        if ($lblocks > 0) {
            echo '
        <script type="text/javascript">
            //<![CDATA[
                $("#adm_workarea").on("click", "a.togxyg",function() {
                if ( $("#all_g").attr("title") !== "'. __d('two_blocks', 'Replier la liste') .'" ) {
                    $("#all_g").attr("title","'. __d('two_blocks', 'Replier la liste') .'")
                    $("#tad_blocgauc td.togxg").attr("style","display: block-inline")
                    $("#tad_blocgauc a.tog i").attr("class","fa fa-caret-up fa-lg text-primary me-2")
                    $("#tad_blocgauc a.tog").attr("title","'. __d('two_blocks', 'Replier la liste') .'")
                    $( "#tad_blocgauc a.tog" ).each(function(index) {
                    var idi= $(this).attr("id")
                    var idir = idi.replace("show", "hide");
                    $(this).attr("id",idir)
                    });
                    } 
                else {
                    $("#all_g").attr("title","'. __d('two_blocks', 'Déplier la liste') .'")
                    $("#tad_blocgauc td.togxg").attr("style","display: none")
                    $("#tad_blocgauc a.tog i").attr("class","fa fa-caret-down fa-lg text-primary me-2")
                    $("#tad_blocgauc a.tog").attr("title","'. __d('two_blocks', 'Déplier la liste') .'")
                    $( "#tad_blocgauc a.tog" ).each(function(index) {
                    var idi= $(this).attr("id")
                    var idir = idi.replace("hide", "show");
                    $(this).attr("id",idir)
                    });
                    }
                });
                //]]>
        </script>
        <div class="">
        <table id="tad_blocgauc" class="table table-hover table-striped" >
            <thead>
                <tr>
                    <th><a class="togxyg"><i id="all_g" class="fa fa-navicon" title="'. __d('two_blocks', 'Déplier la liste') .'"></i></a>&nbsp;'. __d('two_blocks', 'Titre') .'</th>
                    <th class="d-none d-sm-table-cell text-center">'. __d('two_blocks', 'Actif') .'</th>
                    <th class="d-none d-sm-table-cell text-end">Index</th>
                    <th class="d-none d-sm-table-cell text-end">'. __d('two_blocks', 'Rétention') .'</th>
                    <th class="text-end">ID</th>
                </tr>
            </thead>
            <tbody>';
        
            $j = 0;
            foreach($lblocks as $lblock) {
                $funct = '';
                
                if ($lblock['title'] == '') {
                    //$$lblock['title'] = __d('two_blocks', 'Sans nom');
                    $pos_func = strpos($lblock['content'], 'function#');
                    $pos_nl = strpos($lblock['content'], chr(13), $pos_func);
                    
                    if ($pos_func !== false) {
                        $funct = '<span style="font-size: 0.65rem;"> (';
                        
                        if ($pos_nl !== false) {
                            $funct .= substr($lblock['content'], $pos_func, $pos_nl - $pos_func);
                        } else {
                            $funct .= substr($lblock['content'], $pos_func);
                        }
                        
                        $funct .= ')</span>';
                    }
                    $funct = __d('two_blocks', 'Sans nom') . $funct;
                }

                echo $lblock['actif'] 
                    ? '<tr class="table-success">' 
                    : '<tr class="table-danger">';

                echo '
                    <td align="left">
                    <a class="tog" id="show_bloga_'. $lblock['id'] .'" title="'. __d('two_blocks', 'Déplier la liste') .'"><i id="i_bloga_'. $lblock['id'] .'" class="fa fa-caret-down fa-lg text-primary me-2" ></i></a>';
                
                echo language::aff_langue($lblock['title']) .' '. $funct . '</td>';
                
                echo $lblock['actif'] 
                    ? '<td class="d-none d-sm-table-cell text-center">'. __d('two_blocks', 'Oui') .'</td>' 
                    : '<td class="text-danger d-none d-sm-table-cell text-center">'. __d('two_blocks', 'Non') .'</td>';

                echo '
                    <td class="d-none d-sm-table-cell" align="right">'. $lblock['Lindex'] .'</td>
                    <td class="d-none d-sm-table-cell" align="right">'. $lblock['cache'] .'</td>
                    <td class="text-end">'. $lblock['id'] .'</td>
                </tr>
                <tr>
                    <td id="bloga_' . $lblock['id'] . '" class="togxg" style="display:none;" colspan="5">
                    <form id="fad_bloga_'. $lblock['id'] .'" action="'. site_url('admin.php') .'" method="post">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <fieldset>
                                <legend>'. __d('two_blocks', 'Contenu') .'</legend>
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" id="titlega_'. $lblock['id'] .'" name="title" maxlength="1000" value="'. $lblock['title'] .'" />
                                    <label for="titlega_'. $lblock['id'] .'">'. __d('two_blocks', 'Titre') .'</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="contentga_'. $lblock['id'] .'" name="content" style="height:140px">'. $lblock['content'] .'</textarea>
                                    <label for="contentga_'. $lblock['id'] .'">' . __d('two_blocks', 'Contenu') .'</label>
                                    <span class="help-block"><a href="javascript:void(0);" onclick="window.open(\''. site_url('autodoc.php?op=blocs') .'\', \'windocu\', \'width=720, height=400, resizable=yes,menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes\');">'. __d('two_blocks', 'Manuel en ligne') .'</a></span>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" rows="2" id="BLaidega_'. $lblock['id'] .'" name="BLaide" style="height:100px">'. $lblock['aide'] .'</textarea>
                                    <label for="BLaidega_'. $lblock['id'] .'">'. __d('two_blocks', 'Aide en ligne de ce bloc') .'</label>
                                </div>
                                </fieldset>
                                <fieldset>
                                <legend>'. __d('two_blocks', 'Droits') .'</legend>';

                echo droits_bloc($lblock['member'], $j, 'L');

                echo '
                                </fieldset>
                                <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <select class="form-select" name="op">
                                        <option value="changelblock" selected="selected">'. __d('two_blocks', 'Modifier un Bloc gauche') .'</option>
                                        <option value="deletelblock">'. __d('two_blocks', 'Effacer un Bloc gauche') .'</option>
                                        <option value="droitelblock">'. __d('two_blocks', 'Transférer à Droite') .'</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <fieldset>
                                <legend>' . __d('two_blocks', 'Paramètres') . '</legend>
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="number" id="Lindexga_'. $lblock['id'] .'" name="Lindex" max="9999" value="'. $lblock['Lindex'] .'" />
                                    <label for="Lindexga_'. $lblock['id'] .'">Index</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="number" id="Scachega_'. $lblock['id'] .'" name="Scache" min="0" max="99999" value="'. $lblock['cache'] .'" />
                                    <label for="Scachega_'. $lblock['id'] .'">' . __d('two_blocks', 'Rétention') .'</label>
                                    <span class="help-block">'. __d('two_blocks', 'Chaque bloc peut utiliser SuperCache. La valeur du délai de rétention 0 indique que le bloc ne sera pas caché (obligatoire pour le bloc function#adminblock).') .'</span>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="Sactif'. $j .'L" name="Sactif" value="ON" ';
                
                if ($lblock['actif']) {
                    echo 'checked="checked" ';
                }
                
                echo '/>
                                    <label class="form-check-label" for="Sactif'. $j .'L">'. __d('two_blocks', 'Activer le Bloc') .'</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="css'. $j .'L" name="css" value="1" ';

                if ($lblock['css'] == '1') {
                    echo 'checked="checked" ';
                }

                echo '/>
                                    <label class="form-check-label" for="css'. $j .'L">'. __d('two_blocks', 'CSS Specifique') .'</label>
                                </div>
                                </fieldset>
                            </div>
                            <input type="hidden" name="id" value="'. $lblock['id'] .'" />
                        </div>
                        <button class="btn btn-primary mb-2" type="submit">'. __d('two_blocks', 'Ok') .'</button>
                    </form>
                    <script type="text/javascript">
                    //<![CDATA[
                        tog("bloga_'. $lblock['id'] .'","show_bloga_'. $lblock['id'] .'","hide_bloga_'. $lblock['id'] .'");
                    //]]>
                    </script>
                    </td>
                </tr>';

                $j++;
            }

            echo '
            </tbody>
        </table>
        </div>';
        }

        echo '
        <hr />
        <h3>'. __d('two_blocks', 'Edition des Blocs de droite') .'</h3>';

        $rblocks = DB::table('rblocks')->select('id', 'title', 'content', 'member', 'Rindex', 'cache', 'actif', 'aide', 'css')->orderBy('Rindex', 'ASC')->get();

        if ($rblocks > 0) {
            echo '
        <script type="text/javascript">
            //<![CDATA[
                $("#adm_workarea").on("click", "a.togxyd",function() {
                    $(".fa.fa-navicon").attr("title","'. __d('two_blocks', 'Replier la liste') .'")
                    $("#tad_blocdroi a.tog i").attr("class","fa fa-caret-down fa-lg me-1 text-primary me-2")
                    $("#tad_blocdroi a.tog").attr("data-bs-original-title","'. __d('two_blocks', 'Déplier la liste') .'")
                    $( "#tad_blocdroi a.tog" ).each(function(index) {
                    var idi= $(this).attr("id")
                    var idir = idi.replace("hide", "show");
                    $(this).attr("id",idir)
                    });
                });
                //]]>
        </script>
        <table id="tad_blocdroi" class="table table-hover table-striped " >
            <thead class="w-100">
                <tr class="w-100">
                    <th><a class="togxyd"><i class="fa fa-navicon fa-lg tooltipbyclass" title="'. __d('two_blocks', 'Déplier la liste la liste') .'"></i></a>&nbsp;'. __d('two_blocks', 'Titre') .'</th>
                    <th class="d-none d-sm-table-cell text-center">'. __d('two_blocks', 'Actif') .'</th>
                    <th class="d-none d-sm-table-cell text-end">Index</th>
                    <th class="d-none d-sm-table-cell text-end">'. __d('two_blocks', 'Rétention') .'</th>
                    <th class="text-end">ID</th>
                </tr>
            </thead>
            <tbody>';

            $j = 0;
            foreach($rblocks as $rblock) {
                $funct = '';

                if ($rblock['title'] == '') {
                    //$rblock['title'] = __d('two_blocks', 'Sans nom');
                    $pos_func = strpos($rblock['content'], 'function#');
                    $pos_nl = strpos($rblock['content'], chr(13), $pos_func);

                    if ($pos_func !== false) {
                        $funct = '<span style="font-size: 0.65rem"> (';

                        if ($pos_nl !== false) {
                            $funct .= substr($rblock['content'], $pos_func, $pos_nl - $pos_func);
                        } else {
                            $funct .= substr($rblock['content'], $pos_func);
                        }

                        $funct .= ')</span>';
                    }
                    $funct = __d('two_blocks', 'Sans nom') . $funct;
                }

                echo $rblock['actif'] 
                    ? '<tr class="table-success w-100 mw-100">' 
                    : '<tr class="table-danger w-100 mw-100">';

                echo '
                    <td align="left">
                    <a data-bs-toggle="collapse" data-bs-target="#blodr_'. $rblock['id'] .'" aria-expanded="false" aria-controls="blodr_'. $rblock['id'] .'" class="tog tooltipbyclass" id="show_blodr_' . $rblock['id'] . '" title="' . __d('two_blocks', 'Déplier la liste') . '"><i id="i_blodr_' . $rblock['id'] . '" class="fa fa-caret-down fa-lg text-primary me-2" ></i></a>';
                
                echo language::aff_langue($rblock['title']) .' '. $funct .'</td>';
                
                echo $rblock['actif'] 
                    ? '<td class="d-none d-sm-table-cell text-center" >' . __d('two_blocks', 'Oui') . '</td>' 
                    : '<td class="text-danger d-none d-sm-table-cell text-center">' . __d('two_blocks', 'Non') . '</td>';

                echo '
                    <td class="d-none d-sm-table-cell text-end">'. $rblock['Rindex'] .'</td>
                    <td class="d-none d-sm-table-cell text-end">'. $rblock['cache'] .'</td>
                    <td class="text-end">'. $rblock['id'] .'</td>
                </tr>
                <tr class="w-100">
                    <td id="blodr_'. $rblock['id'] .'" class="togxd collapse" colspan="5">
                    <form id="fad_blodr_'. $rblock['id'] .'" action="'. site_url('admin.php') .'" method="post">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <fieldset>
                                <legend>' . __d('two_blocks', 'Contenu') . '</legend>
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" id="titledr_'. $rblock['id'] .'" name="title" maxlength="1000" value="'. $rblock['title'] .'" />
                                    <label for="titledr_'. $rblock['id'] .'">'. __d('two_blocks', 'Titre') .'</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" style="height:140px;" id="contentdr_'. $rblock['id'] .'" name="content">'. $rblock['content'] .'</textarea>
                                    <label for="contentdr_'. $rblock['id'] .'">'. __d('two_blocks', 'Contenu') .'</label>
                                    <span class="help-block"><a href="javascript:void(0);" onclick="window.open(\''. site_url('autodoc.php?op=blocs') .'\', \'windocu\', \'width=720, height=400, resizable=yes,menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes\');">'. __d('two_blocks', 'Manuel en ligne') .'</a></span>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" style="height:100px;" id="BRaidedr_'. $rblock['id'] .'" name="BRaide">'. $rblock['aide'] .'</textarea>
                                    <label class="col-form-label col-sm-12" for="BRaidedr_'. $rblock['id'] .'">'. __d('two_blocks', 'Aide en ligne de ce bloc') .'</label>
                                </div>
                                </fieldset>
                                <fieldset>
                                <legend>'. __d('two_blocks', 'Droits') .'</legend>';

                echo droits_bloc($rblock['member'], $j, 'R');

                echo '
                                </fieldset>
                                <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <select class="form-select" name="op">
                                        <option value="changerblock" selected="selected">'. __d('two_blocks', 'Modifier un Bloc droit') .'</option>
                                        <option value="deleterblock">'. __d('two_blocks', 'Effacer un Bloc droit'). '</option>
                                        <option value="gaucherblock">'. __d('two_blocks', 'Transférer à Gauche'). '</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <fieldset>
                                <legend>'. __d('two_blocks', 'Paramètres') .'</legend>
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="number" id="Rindexdr_'. $rblock['id'] .'" name="Rindex" min="0" max="9999" value="'. $rblock['Rindex'] .'" />
                                    <label for="Rindexdr_'. $rblock['id'] .'">Index</label>
                                </div>
                                <div class="form-floating mb-3"">
                                    <input class="form-control" type="number" name="Scache" id="Scache" min="0" max="99999" value="'. $rblock['cache'] .'" />
                                    <label for="Scache">'. __d('two_blocks', 'Rétention') .'</label>
                                    <span class="help-block">'. __d('two_blocks', 'Chaque bloc peut utiliser SuperCache. La valeur du délai de rétention 0 indique que le bloc ne sera pas caché (obligatoire pour le bloc function#adminblock).') .'</span>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check" >
                                        <input type="checkbox" class="form-check-input" id="Sactif'. $j .'R" name="Sactif" value="ON" ';

                if ($rblock['actif']) {
                    echo 'checked="checked" ';
                }

                echo '/>
                                        <label class="form-check-label" for="Sactif'. $j .'R">'. __d('two_blocks', 'Activer le Bloc') .'</label>
                                    </div>
                                    <div class="form-check" >
                                        <input type="checkbox" class="form-check-input" id="css'. $j .'R" name="css" value="1" ';

                if ($rblock['css'] == "1") {
                    echo 'checked="checked" ';
                }

                echo '/>
                                        <label class="form-check-label" for="css'. $j .'R"> '. __d('two_blocks', 'CSS Specifique') .'</label>
                                    </div>
                                </div>
                                </fieldset>
                            </div>
                            <input type="hidden" name="id" value="'. $rblock['id'] .'" />
                        </div>
                        <button class="btn btn-primary mb-3" type="submit">'. __d('two_blocks', 'Ok') .'</button>
                    </form>
                    <script type="text/javascript">
                    //<![CDATA[
                        tog("blodr_'. $rblock['id'] .'","show_blodr_'. $rblock['id'] .'","hide_blodr_'. $rblock['id'] .'");
                    //]]>
                    </script>
                    </td>
                </tr>';

                $j++;
            }

            echo '
            </tbody>
        </table>';
        }

        echo '
        <hr />
        <h3 class="my-3">'. __d('two_blocks', 'Créer un nouveau Bloc') .'</h3>
        <form id="blocknewblock" action="'. site_url('admin.php') .'" method="post" name="adminForm">
            <div class="row g-3">
                <div class="col-md-8">
                    <fieldset>
                    <legend>'. __d('two_blocks', 'Contenu') .'</legend>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="text" id="nblock_title" name="title" maxlength="1000" />
                        <label for="nblock_title">'. __d('two_blocks', 'Titre') .'</label>
                        <span class="help-block text-end" id="countcar_nblock_title"></span>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" name="xtext" id="nblock_xtext" style="height:140px;"></textarea>
                        <label for="nblock_xtext">'. __d('two_blocks', 'Contenu') .'</label>
                        <span class="help-block"><a href="javascript:void(0);" onclick="window.open(\''. site_url('autodoc.php?op=blocs') .'\', \'windocu\', \'width=720, height=400, resizable=yes,menubar=no,location=no,directories=no,status=no,copyhistory=no,toolbar=no,scrollbars=yes\');">'. __d('two_blocks', 'Manuel en ligne') .'</a></span>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" rows="2" name="Baide" id="nblock_Baide"></textarea>
                        <label for="nblock_Baide">'. __d('two_blocks', 'Aide en ligne') .'</label>
                    </div>
                    </fieldset>
                    <fieldset>
                    <legend>'. __d('two_blocks', 'Droits') .'</legend>';

        echo droits_bloc('0', 0, '');

        echo '
                    </fieldset>
                    <fieldset>
                    <legend>'. __d('two_blocks', 'Position') .'</legend>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="radio" id="nblock_opL" name="op" value="makelblock" checked="checked" class="form-check-input"/>
                            <label class="form-check-label" for="nblock_opL">'. __d('two_blocks', 'Créer un Bloc gauche') .'</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="nblock_opR" name="op" value="makerblock" class="form-check-input"/>
                            <label class="form-check-label" for="nblock_opR">'. __d('two_blocks', 'Créer un Bloc droite') .'</label>
                        </div>
                    </div>
                    </fieldset>
                </div>
                <div class="col-md-4">
                    <fieldset>
                    <legend>'. __d('two_blocks', 'Paramètres') .'</legend>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="number" name="index" id="nblock_index" min="0" max="9999" />
                        <label for="nblock_index">Index</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="number" name="Scache" id="nblock_Scache" min="0" max="99999" value="60" />
                        <label for="nblock_Scache">'. __d('two_blocks', 'Rétention') .'</label>
                        <span class="help-block">'. __d('two_blocks', 'Chaque bloc peut utiliser SuperCache. La valeur du délai de rétention 0 indique que le bloc ne sera pas caché (obligatoire pour le bloc function#adminblock).') .'</span>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="SHTML" id="nblock_shtml" value="ON" />
                            <label class="form-check-label" for="nblock_shtml">HTML</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="CSS" id="nblock_css" value="ON" />
                            <label class="form-check-label" for="nblock_css">CSS</label>
                        </div>
                    </div>
                    </fieldset>
                </div>
            </div>
            <button class="btn btn-primary mb-2" type="submit">'. __d('two_blocks', 'Valider') .'</button>
        </form>';

        $arg1 = '
            var formulid = ["blocknewblock"];
            inpandfieldlen("nblock_title",1000);';

        css::adminfoot('fv', '', $arg1, '');
    }


}