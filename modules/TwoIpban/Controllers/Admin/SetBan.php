<?php

namespace Modules\TwoIpban\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class SetBan extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'ipban';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'setban';

        $this->f_titre = __d('two_ipban', 'Administration de l\'IpBan');

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

    function ConfigureBan($ModPath, $ModStart)
    {
        global $f_meta_nom, $f_titre, $adminimg, $language, $hlpfile;
        settype($ip_ban, 'string');
        if (file_exists('storage/logs/spam.log')) {
            $fd = fopen('storage/logs/spam.log', 'r');
            while (!feof($fd)) {
                $ip_ban .= fgets($fd, 4096);
            }
            fclose($fd);
        }
        GraphicAdmin($hlpfile);
        adminhead($f_meta_nom, $f_titre, $adminimg);
        echo '
        <hr />
            <div class="card card-body mb-3">
                ' . __d('two_ipban', 'Chaque ligne ne doit contenir qu\'une adresse IP (v4 ou v6) de forme : a.b.c.d|X (ex. v4 : 168.192.1.1|5) ; a:b:c:d:e:f:g:h|X (ex. v6 : 2001:0db8:0000:85a3:0000:0000:ac1f:8001|5).') . '<br />
                <span class="text-danger lead">' . __d('two_ipban', 'Si X >= 5 alors l\'accès sera refusé !') . '</span><br />
                ' . __d('two_ipban', 'Ce fichier est mis à jour automatiquement par l\'anti-spam de NPDS.') . '
            </div>
            <form id="ipban_mod" action="admin.php" method="post">
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="ip_ban">' . __d('two_ipban', 'Liste des IP') . '</label>
                    <div class="col-sm-12">
                    <textarea id="ip_ban" class="form-control" name="ipban" rows="15">' . $ip_ban . '</textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                    <button class="btn btn-primary" type="submit">' . __d('two_ipban', 'Sauver les modifications') . '</button>
                    <input type="hidden" name="op" value="Extend-Admin-SubModule" />
                    <input type="hidden" name="ModPath" value="' . $ModPath . '" />
                    <input type="hidden" name="ModStart" value="' . $ModStart . '" />
                    <input type="hidden" name="subop" value="SaveSetBan" />
                    </div>
                </div>
            </form>';
        css::adminfoot('', '', '', '');
    }
    
    function SaveSetBan($Xip_ban)
    {
        $file = fopen('storage/logs/spam.log', 'w');
        fwrite($file, $Xip_ban);
        fclose($file);
        cache::SC_clean();
    }

}