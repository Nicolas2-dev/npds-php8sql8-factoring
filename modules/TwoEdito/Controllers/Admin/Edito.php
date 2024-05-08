<?php

namespace Modules\TwoEdito\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Edito extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'edito';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'edito';

        $this->f_titre = __d('two_', 'Edito');

        parent::initialize($request);
    }


    /**
     * edito('', '', '', '', '');
     */
    function edito($edito_type, $contents, $Xaff_jours, $Xaff_jour, $Xaff_nuit)
    {
        global $f_meta_nom, $f_titre;
    
        include("themes/default/header.php");
    
        GraphicAdmin(manuel('edito'));
        adminhead($f_meta_nom, $f_titre);
    
        echo '<hr />';
    
        if ($contents == '') {
            echo '
            <form id="fad_edi_choix" action="'. site_url('admin.php?op=Edito_load') .'" method="post">
                <fieldset>
                    <legend>'. __d('two_edito', 'Type d\'éditorial') .'</legend>
                    <div class="mb-3">
                    <select class="form-select" name="edito_type" onchange="submit()">
                        <option value="0">'. __d('two_edito', 'Modifier l\'Editorial') .' ...</option>
                        <option value="G">'. __d('two_edito', 'Anonyme') .'</option>
                        <option value="M">'. __d('two_edito', 'Membre') .'</option>
                    </select>
                    </div>
                </fieldset>
            </form>';
    
            css::adminfoot('', '', '', '');
    
        } else {
            if ($edito_type == 'G') {
                $edito_typeL = ' ' . __d('two_edito', 'Anonyme');
            } elseif ($edito_type == 'M') {
                $edito_typeL = ' ' . __d('two_edito', 'Membre');
            }
    
            if (strpos($contents, '[/jour]') > 0 && stristr($contents, '[/jour]') && stristr($contents, '[/nuit]')) {
                $contentJ = substr($contents, strpos($contents, '[jour]') + 6, strpos($contents, '[/jour]') - 6);
                $contentN = substr($contents, strpos($contents, '[nuit]') + 6, strpos($contents, '[/nuit]') - 19 - strlen($contentJ));
            }
    
            if (!isset($contentJ) and !strpos($contents, '[/jour]')) {
                $contentJ = $contents;
            }
    
            if (!isset($contentN) and !strpos($contents, '[/nuit]')) {
                $contentN = $contents;
            }
            
            echo '
            <form id="admineditomod" action="'. site_url('admin.php') .'" method="post" name="adminForm">
                <fieldset>
                <legend>'. __d('two_edito', 'Edito') .' :'. $edito_typeL .'</legend>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="XeditoJ">'. __d('two_edito', 'Le jour') .'</label>
                    <div class="col-sm-12">
                    <textarea class="tin form-control" name="XeditoJ" rows="20" >';
    
            echo htmlspecialchars($contentJ, ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8');
    
            echo '</textarea>
                    </div>
                </div>';
    
            echo editeur::aff_editeur('XeditoJ', '');
    
            echo '
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="XeditoN">'. __d('two_edito', 'La nuit') .'</label>';
    
            echo editeur::aff_editeur('XeditoN', '');
    
            echo '
                    <div class="col-sm-12">
                    <textarea class="tin form-control" name="XeditoN" rows="20">';
    
            echo htmlspecialchars($contentN, ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8');
    
            echo '</textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label" for="aff_jours">'. __d('two_edito', 'Afficher pendant') .'</label>
                    <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text">'. __d('two_edito', 'jour(s)') .'</span>
                        <input class="form-control" type="number" name="aff_jours" id="aff_jours" min="0" step="1" max="999" value="'. $Xaff_jours .'" data-fv-digits="true" required="required" />
                    </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-8 ms-sm-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="aff_jour" name="aff_jour" value="checked" '. $Xaff_jour .' />
                        <label class="form-check-label" for="aff_jour">'. __d('two_edito', 'Le jour') .'</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="aff_nuit" name="aff_nuit" value="checked" '. $Xaff_nuit .' />
                        <label class="form-check-label" for="aff_nuit">'. __d('two_edito', 'La nuit') .'</label>
                    </div>
                    </div>
                </div>
    
            <input type="hidden" name="op" value="Edito_save" />
            <input type="hidden" name="edito_type" value="'. $edito_type .'" />
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto ">
                    <button class="btn btn-primary col-12" type="submit" name="edito_confirm"><i class="fa fa-check fa-lg"></i>&nbsp;'. __d('two_edito', 'Sauver les modifications') .' </button>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto ">
                    <a href="'. site_url('admin.php?op=Edito') .'" class="btn btn-secondary col-12">'. __d('two_edito', 'Abandonner') .'</a>
                </div>
            </div>
            </fieldset>
            </form>';
    
            $arg1 = '
                var formulid = ["admineditomod"];';
    
            $fv_parametres = '
            aff_jours: {
                validators: {
                    digits: {
                        message: "This must be a number"
                    }
                }
            },';
    
            css::adminfoot('fv', $fv_parametres, $arg1, '');
        }
    }

    /**
     * 
     */
    private function Edito_load(Request $request)
    {
        if ($edito_type == 'G') {

            $Xcontents = '';
            $Xibidout['aff_jours'] = '';
            $Xibidout['aff_nuit'] = '';

            if (file_exists('storage/static/edito.txt')) {
                $fp = fopen('storage/static/edito.txt', 'r');
                
                if (filesize('storage/static/edito.txt') > 0) {
                    $Xcontents = fread($fp, filesize('storage/static/edito.txt'));
                }
                fclose($fp);
            }
        } elseif ($edito_type == 'M') {
            
            $Xcontents = '';
            $Xibidout['aff_jours'] = '';
            $Xibidout['aff_nuit'] = '';

            if (file_exists('storage/static/edito_membres.txt')) {
                $fp = fopen('storage/static/edito_membres.txt', 'r');
                
                if (filesize('storage/static/edito_membres.txt') > 0) {
                    $Xcontents = fread($fp, filesize('storage/static/edito_membres.txt'));
                }
                fclose($fp);
            }
        }

        $Xcontents = preg_replace('#<!--|/-->#', '', $Xcontents);

        if ($Xcontents == '') {
            $Xcontents = 'Edito ...';
        } else {
            $ibid = strstr($Xcontents, 'aff_jours');
            parse_str($ibid, $Xibidout);
        }

        if ($Xibidout['aff_jours']) {
            $Xcontents = substr($Xcontents, 0, strpos($Xcontents, 'aff_jours'));
        } else {
            $Xibidout['aff_jours'] = 20;
            $Xibidout['aff_jour'] = 'checked="checked"';
            $Xibidout['aff_nuit'] = 'checked="checked"';
        }

        edito($edito_type, $Xcontents, $Xibidout['aff_jours'], $Xibidout['aff_jour'], $Xibidout['aff_nuit']);

        return $this->createView()
            ->shares('title', __d('two_edito', ''));
    }

    /**
     * 
     */
    public function edito_mod_save($edito_type, $XeditoJ, $XeditoN, $aff_jours, $aff_jour, $aff_nuit)
    {
        if ($aff_jours <= 0) {
            $aff_jours = '999';
        }
    
        if ($edito_type == 'G') {
            $fp = fopen("storage/static/edito.txt", "w");
            fputs($fp, "[jour]". str_replace('&quot;', '"', stripslashes($XeditoJ)) .'[/jour][nuit]'. str_replace('&quot;', '"', stripslashes($XeditoN)) .'[/nuit]');
            fputs($fp, 'aff_jours=' . $aff_jours);
            fputs($fp, '&aff_jour=' . $aff_jour);
            fputs($fp, '&aff_nuit=' . $aff_nuit);
            fputs($fp, '&aff_date=' . time());
            fclose($fp);
        } elseif ($edito_type == 'M') {
            $fp = fopen('storage/static/edito_membres.txt', 'w');
            fputs($fp, '[jour]'. str_replace('&quot;', '"', stripslashes($XeditoJ)) .'[/jour][nuit]'. str_replace('&quot;', '"', stripslashes($XeditoN)) .'[/nuit]');
            fputs($fp, 'aff_jours=' . $aff_jours);
            fputs($fp, '&aff_jour=' . $aff_jour);
            fputs($fp, '&aff_nuit=' . $aff_nuit);
            fputs($fp, '&aff_date=' . time());
            fclose($fp);
        }
    
        global $aid;
        logs::Ecr_Log('security', "editoSave () by AID : $aid", '');
    
        url::redirect_url('admin.php?op=Edito');
    }


}