<?php

namespace Modules\TwoCore\Controllers\Admin\Database;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Optimysql extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'optimysql';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'OptimySQL';

        $this->f_titre = __d('two_core', 'Optimisation de la base de données') .' : '. Config::get('database.default.database');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {
        $date_opt = date(__d('two_core', 'dateforop'));
        $heure_opt = date("h:i a");
        
        include("themes/default/header.php");
        
        GraphicAdmin(manuel('optimysql'));
        adminhead($f_meta_nom, $f_titre);
        
        // Insertion de valeurs d'initialisation de la table (si nécessaire)
        $optimy = DB::table('optimy')->select('optid')->first();
        
        if (!$optimy['optid'] or ($optimy['optid'] == '')) {
            DB::table('optimy')->insert(array(
                'optid'       => 1,
                'optgain'     => 0,
                'optdate'     => '',
                'opthour'     => '',
                'optcount'     => 0,
            ));
        }
        
        // Extraction de la date et de l'heure de la précédente optimisation
        $last_opti = '';
        
        $optimy = DB::table('optimy')->select('optdate', 'opthour')->where('optid', 1)->first();
        
        if (!$optimy['optdate'] or ($optimy['optdate'] == '') or !$optimy['opthour'] or ($optimy['opthour'] == '')) {
        } else {
            $last_opti = __d('two_core', 'Dernière optimisation effectuée le') . " : " . $optimy['optdate'] . " " . __d('two_core', ' à ') . " " . $optimy['opthour'] . "<br />\n";
        }
        
        $tot_data = 0;
        $tot_idx = 0;
        $tot_all = 0;
        $li_tab_opti = '';
        
        if ($tables = DB::select('SHOW TABLE STATUS')) {
        
            foreach ($tables as $table) {
                $tot_data = $table['Data_length'];
                $tot_idx  = $table['Index_length'];
                $total = ($tot_data + $tot_idx);
                $total = ($total / 1024);
                $total = round($total, 3);
                $gain = $table['Data_free'];
                $gain = ($gain / 1024);
        
                settype($total_gain, 'integer');
        
                $total_gain += $gain;
                $gain = round($gain, 3);
                
                $resultat = DB::optimyTable($table['Name']);
        
                if ($gain == 0) {
                    $li_tab_opti .= '
                    <tr class="table-success">
                        <td align="right">' . $table['Name'] .'</td>
                        <td align="right">' . $total .' Ko</td>
                        <td align="center">' . __d('two_core', 'optimisée') .'</td>
                        <td align="center"> -- </td>
                    </tr>';
                } else {
                    $li_tab_opti .= '
                    <tr class="table-danger">
                        <td align="right">' . $table['Name'] .'</td>
                        <td align="right">' . $total .' Ko</td>
                        <td class="text-danger" align="center">' . __d('two_core', 'non optimisée') .'</td>
                        <td align="right">' . $gain .' Ko</td>
                    </tr>';
                }
            }
        }
        
        $total_gain = round($total_gain, 3);
        
        // Historique des gains
        // Extraction du nombre d'optimisation effectuée
        $optimys = DB::table('optimy')->select('optgain', 'optcount')->where('optid', 1)->first();
        
        $newgain = ($optimys['optgain'] + $total_gain);
        $newcount = ($optimys['optcount'] + 1);
        
        // Enregistrement du nouveau gain
        DB::table('optimy')->where('optid', 1)->update(array(
            'optgain'       => $newgain,
            'optdate'       => $date_opt,
            'opthour'       => $heure_opt,
            'optcount'      => $newcount,
        ));
        
        // Lecture des gains précédents et addition
        $optimy = DB::table('optimy')->select('optgain', 'optcount')->where('optid', 1)->first();
        
        echo '<hr /><p class="lead">' . __d('two_core', 'Optimisation effectuée') .' : ' . __d('two_core', 'Gain total réalisé') .' ' . $total_gain .' Ko</br>';
        echo $last_opti;
        echo '
            ' . __d('two_core', 'A ce jour, vous avez effectué ') .' ' . $optimy['optcount'] .' optimisation(s) ' . __d('two_core', ' et réalisé un gain global de ') .' ' . $optimys['optgain'] .' Ko.</p>
            <table id="tad_opti" data-toggle="table" data-striped="true" data-show-toggle="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <tr>
                    <th data-sortable="true" data-halign="center" data-align="center">' . __d('two_core', 'Table') .'</th>
                    <th data-halign="center" data-align="center">' . __d('two_core', 'Taille actuelle') .'</th>
                    <th data-sortable="true" data-halign="center" data-align="center">' . __d('two_core', 'Etat') .'</th>
                    <th data-halign="center" date-align="center">' . __d('two_core', 'Gain réalisable') .'</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td>' . __d('two_core', 'Gain total réalisé') .' : </td>
                    <td>' . $optimy['optgain'] .' Ko</td>
                </tr>
            </tfoot>
            <tbody>';
        
        echo $li_tab_opti;
        
        echo '
            </tbody>
            </table>';
        
        css::adminfoot('', '', '', '');
        
        global $aid;
        logs::Ecr_Log('security', "OptiMySql() by AID : $aid", '');
        
    }

}