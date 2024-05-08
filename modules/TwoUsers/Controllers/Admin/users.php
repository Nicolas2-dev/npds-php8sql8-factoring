<?php

namespace Modules\TwoUsers\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Users extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'users';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'mod_users';

        $this->f_titre = __d('two_users', 'Edition des Utilisateurs');

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
     * [displayUsers description]
     *
     * @return  void
     */
    function displayUsers(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('users'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
            <h3>'. __d('two_users', 'Extraire l\'annuaire') .'</h3>
            <form method="post" class="form-inline" action="'. site_url('admin.php') .'">
                    <div class="mb-3">
                        <label class="me-2 mt-sm-3" for="op">'. __d('two_users', 'Format de fichier') .'</label>
                        <select class="form-select me-2 mt-sm-3" name="op">
                            <option value="extractUserCSV">'. __d('two_users', 'Au format CSV') .'</option>
                        </select>
                    </div>
                    <button class="btn btn-primary ms-2 mt-3" type="submit">'. __d('two_users', 'Ok') .' </button>
            </form>
            <hr />
            <h3>'. __d('two_users', 'Rechercher utilisateur') .'</h3>
            <form method="post" class="form-inline" action="'. site_url('admin.php') .'">
            <label class="me-2 mt-sm-1" for="chng_uid">'. __d('two_users', 'Identifiant Utilisateur') .'</label>
            <input class="form-control me-2 mt-sm-3 mb-2" type="text" id="chng_uid" name="chng_uid" size="20" maxlength="10" />
            <select class="form-select me-2 mt-sm-3 mb-2" name="op">
                <option value="modifyUser">'. __d('two_users', 'Modifier un utilisateur') .'</option>
                <option value="unsubUser">'. __d('two_users', 'Désabonner un utilisateur') .'</option>
                <option value="delUser">'. __d('two_users', 'Supprimer un utilisateur') .'</option>
            </select>
            <button class="btn btn-primary ms-sm-2 mt-sm-3 mb-2" type="submit" >'. __d('two_users', 'Ok') .' </button>
            </form>';

        $chng_is_visible = 1;

        echo '
            <hr />
            <h3>'. __d('two_users', 'Créer utilisateur') .'</h3>';

        $op = 'displayUsers';

        include("support/sform/extend-user/adm_extend-user.php");

        echo js::auto_complete('membre', 'uname', 'users', 'chng_uid', 86400);

        echo '<hr />
            <h3 class="mb-3">'. __d('two_users', 'Fonctions') .'</h3>
            <a href="'. site_url('admin.php?op=checkdnsmail_users') .'">'. __d('two_users', 'Contrôler les serveurs de mail de tous les utilisateurs') .'</a><br />
            <a href="'. site_url('admin.php?op=checkdnsmail_users&amp;page=0&amp;end=1') .'">'. __d('two_users', 'Serveurs de mail incorrects') .'</a><br />';

        css::adminfoot('', '', '', '');
    }

    /**
     * [extractUserCSV description]
     *
     * @return  void
     */
    function extractUserCSV(): void
    {
        $MSos = get_os();

        if ($MSos) {
            $crlf = "\r\n";
        } else {
            $crlf = "\n";
        }

        $deliminator = ';';
        $line = "UID;UNAME;NAME;URL;EMAIL;FEMAIL;C1;C2;C3;C4;C5;C6;C7;C8;M1;M2;T1;T2" . $crlf;

        $users = DB::table('users')->select('uid', 'uname', 'name', 'url', 'email', 'femail')->where('uid', '!=', 1)->orderBy('uid')->get();

        foreach ($users as $temp_user) {
            foreach ($temp_user as $val) {
                $val = str_replace("\r\n", "\n", (string) $val);

                if (preg_match("#[$deliminator\"\n\r]#", $val)) {
                    $val = '"'. str_replace('"', '""', $val) .'"';
                }

                $line .= $val . $deliminator;
            }

            $users_extend = DB::table('users_extend')->select('C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'M1', 'M2', 'T1', 'T2')->where('uid', $temp_user['uid'])->get();

            if ($users_extend) {
                foreach ($users_extend as $temp_extend) {
                    foreach ($temp_extend as $val2) {

                        $val2 = str_replace("\r\n", "\n", $val2);

                        if (preg_match("#[$deliminator\"\n\r]#", (string) $val2)) {
                            $val2 = '"'. str_replace('"', '""', $val2) .'"';
                        }

                        $line .= $val2 . $deliminator;
                    }
                }
            }

            $line = substr($line, 0, (strlen($deliminator) * -1));
            $line .= $crlf;
        }

        send_file($line, "annuaire", "csv", $MSos);

        global $aid;
        logs::Ecr_Log('security', "ExtractUserCSV() by AID : $aid", '');
    }

    /**
     * [modifyUser description]
     *
     * @param   string|int   $chng_user  [$chng_user description]
     *
     * @return  void
     */
    function modifyUser(string|int $chng_user): void 
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('users'));
        adminhead($f_meta_nom, $f_titre);

        // peut mieux faire avec une jointure des tables
        $result = DB::table('users')
            ->select('uid', 'uname', 'name', 'url', 'email', 'femail', 'user_from', 'user_occ', 'user_intrest', 'user_viewemail', 'user_avatar', 'user_sig', 'bio', 'pass', 'send_email', 'is_visible', 'mns', 'user_lnl')
            ->where('uid', $chng_user)
            ->orWhere('uname', '=', $chng_user)
            ->first();
        
        if ($result > 0) {
            
            // pour sform en attente de modif de sform 
            $chng_uid            = $result['uid'];
            $chng_uname          = $result['uname'];
            $chng_name           = $result['name'];
            $chng_url            = $result['url'];
            $chng_email          = $result['email'];
            $chng_femail         = $result['femail'];
            $chng_user_from      = $result['user_from'];
            $chng_user_occ       = $result['user_occ'];
            $chng_user_intrest   = $result['user_intrest'];
            $chng_user_viewemail = $result['user_viewemail'];
            $chng_user_avatar    = $result['user_avatar'];
            $chng_user_sig       = $result['user_sig'];
            $chng_bio            = $result['bio'];
            $chng_pass           = $result['pass'];
            $chng_send_email     = $result['send_email'];
            $chng_is_visible     = $result['is_visible'];
            $chng_mns            = $result['mns'];
            $chng_user_lnl       = $result['user_lnl'];

            echo '
            <hr />
            <h3>'. __d('two_users', 'Modifier un utilisateur') .' : '. $chng_uname .' / '. $result['uid'] .'</h3>';

            // ppour sform
            $op = 'ModifyUser';

            $users_status = DB::table('users_status')->select('level', 'open', 'groupe', 'attachsig', 'rang')->where('uid', $result['uid'])->first();

            // pour sform en attente de modif de sform
            $level       = $users_status['level']; 
            $open        = $users_status['open'];
            $groupe      = $users_status['groupe']; 
            $attachsig   = $users_status['attachsig']; 
            $rang        = $users_status['rang'];

            $users_extend = DB::table('users_extend')
                ->select('C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'M1', 'M2', 'T1', 'T2', 'B1')
                ->where('uid', $result['uid'])
                ->first();

            // pour sform en attente de modif de sform 
            $C1 = $users_extend['C1']; 
            $C2 = $users_extend['C2']; 
            $C3 = $users_extend['C3']; 
            $C4 = $users_extend['C4']; 
            $C5 = $users_extend['C5']; 
            $C6 = $users_extend['C6']; 
            $C7 = $users_extend['C7']; 
            $C8 = $users_extend['C8']; 
            $M1 = $users_extend['M1']; 
            $M2 = $users_extend['M2']; 
            $T1 = $users_extend['T1']; 
            $T2 = $users_extend['T2']; 
            $B1 = $users_extend['B1'];

            include("support/sform/extend-user/adm_extend-user.php");
        } else {
            error_handler("Utilisateur inexistant !" . "<br />");
        }

        css::adminfoot('', '', '', '');
    }

    /**
     * [error_handler description]
     *
     * @param   [type]  $ibid  [$ibid description]
     *
     * @return  void
     */
    function error_handler($ibid): void
    {
        echo '
        <div class="alert alert-danger" align="center">'. __d('two_users', 'Merci d\'entrer l\'information en fonction des spécifications') .'<br />
        <strong>'. $ibid .'</strong><br /><a class="btn btn-secondary" href="'. site_url('admin.php?op=mod_users') .'" >'. __d('two_users', 'Retour en arrière') .'</a>
        </div>';
    }

    /**
     * [Minisites description]
     *
     * @param   int     $chng_mns    [$chng_mns description]
     * @param   string  $chng_uname  [$chng_uname description]
     *
     * @return  void
     */
    function Minisites(int $chng_mns, string $chng_uname): void
    {
        // Création de la structure pour les MiniSites dans storage/users_private/$chng_uname
        if ($chng_mns) {
            include("modules/upload/config/upload.conf.php");

            if ($DOCUMENTROOT == '') {
                global $DOCUMENT_ROOT;
                $DOCUMENTROOT = ($DOCUMENT_ROOT) ? $DOCUMENT_ROOT : $_SERVER['DOCUMENT_ROOT'];
            }

            $user_dir = $DOCUMENTROOT . $racine . "/storage/users_private/" . $chng_uname;
            $repertoire = $user_dir . "/mns";

            if (!is_dir($user_dir)) {
                @umask(0000);
                
                if (@mkdir($user_dir, 0777)) {
                    $fp = fopen($user_dir .'/index.html', 'w');
                    fclose($fp);
                    @umask(0000);
                    
                    if (@mkdir($repertoire, 0777)) {
                        $fp = fopen($repertoire .'/index.html', 'w');
                        fclose($fp);

                        $fp = fopen($repertoire .'/.htaccess', 'w');
                        @fputs($fp, 'Deny from All');
                        fclose($fp);
                    }
                }
            } else {
                @umask(0000);
                if (@mkdir($repertoire, 0777)) {
                    $fp = fopen($repertoire .'/index.html', 'w');
                    fclose($fp);

                    $fp = fopen($repertoire .'/.htaccess', 'w');
                    @fputs($fp, "Deny from All");
                    fclose($fp);
                }
            }

            // copie de la matrice par défaut
            $directory = $racine .'/modules/blog/matrice';
            $handle = opendir($DOCUMENTROOT . $directory);
            
            while (false !== ($file = readdir($handle))) {
                $filelist[] = $file;
            }

            asort($filelist);

            foreach ($filelist as $key => $file) {
                if ($file <> '.' and $file <> '..') {
                    @copy($DOCUMENTROOT . $directory .'/'. $file, $repertoire .'/'. $file);
                }
            }

            closedir($handle);
            unset($filelist);

            global $aid;
            logs::Ecr_Log('security', "CreateMiniSite($chng_uname) by AID : $aid", '');
        }
    }

    /**
     * [updateUser description]
     *
     * @param   int     $chng_uid             [$chng_uid description]
     * @param   string  $chng_uname           [$chng_uname description]
     * @param   string  $chng_name            [$chng_name description]
     * @param   string  $chng_url             [$chng_url description]
     * @param   string  $chng_email           [$chng_email description]
     * @param   string  $chng_femail          [$chng_femail description]
     * @param   string  $chng_user_from       [$chng_user_from description]
     * @param   string  $chng_user_occ        [$chng_user_occ description]
     * @param   string  $chng_user_intrest    [$chng_user_intrest description]
     * @param   string  $chng_user_viewemail  [$chng_user_viewemail description]
     * @param   string  $chng_avatar          [$chng_avatar description]
     * @param   string  $chng_user_sig        [$chng_user_sig description]
     * @param   int     $chng_bio             [$chng_bio description]
     * @param   string  $chng_pass            [$chng_pass description]
     * @param   string  $chng_pass2           [$chng_pass2 description]
     * @param   int     $level                [$level description]
     * @param   int     $open_user            [$open_user description]
     * @param   string  $chng_groupe          [$chng_groupe description]
     * @param   string  $chng_send_email      [$chng_send_email description]
     * @param   int     $chng_is_visible      [$chng_is_visible description]
     * @param   int     $chng_mns             [$chng_mns description]
     * @param   mixed   $C1                   [$C1 description]
     * @param   mixed   $C2                   [$C2 description]
     * @param   mixed   $C3                   [$C3 description]
     * @param   mixed   $C4                   [$C4 description]
     * @param   mixed   $C5                   [$C5 description]
     * @param   mixed   $C6                   [$C6 description]
     * @param   mixed   $C7                   [$C7 description]
     * @param   mixed   $C8                   [$C8 description]
     * @param   mixed   $M1                   [$M1 description]
     * @param   mixed   $M2                   [$M2 description]
     * @param   mixed   $T1                   [$T1 description]
     * @param   mixed   $T2                   [$T2 description]
     * @param   mixed   $B1                   [$B1 description]
     * @param   int     $raz_avatar           [$raz_avatar description]
     * @param   int     $chng_rank            [$chng_rank description]
     * @param   int     $chng_lnl             [$chng_lnl description]
     *
     * @return  void                          [return description]
     */
    function updateUser(int $chng_uid, string $chng_uname, string $chng_name, string $chng_url, string $chng_email, string $chng_femail, string $chng_user_from, string $chng_user_occ, 
    string $chng_user_intrest, string $chng_user_viewemail, string $chng_avatar, string $chng_user_sig, int $chng_bio, string $chng_pass, string $chng_pass2, int $level, 
    int $open_user, string $chng_groupe, string $chng_send_email, int $chng_is_visible, int $chng_mns, mixed $C1, mixed $C2, mixed $C3, mixed $C4, mixed $C5, mixed $C6, 
    mixed $C7, mixed $C8, mixed $M1, mixed $M2, mixed $T1, mixed $T2, mixed $B1, int $raz_avatar, int $chng_rank, int $chng_lnl): void
    {

        $result = DB::table('users')->select('uname')->where('uid', '!=', $chng_uid)->where('uname', $chng_uname)->first();

        if ($result > 0) {
            global $f_meta_nom, $f_titre;
            
            include("themes/default/header.php");
            
            GraphicAdmin(manuel('users'));
            adminhead($f_meta_nom, $f_titre);
            
            echo error_handler(__d('two_users', 'ERREUR : cet identifiant est déjà utilisé') .'<br />');
            
            css::adminfoot('', '', '', '');
            return;
        }

        $tmp = 0;

        if ($chng_pass2 != '') {
            if ($chng_pass != $chng_pass2) {
                global $f_meta_nom, $f_titre;
                
                include("themes/default/header.php");
                
                GraphicAdmin(manuel('users'));
                adminhead($f_meta_nom, $f_titre);
                
                echo error_handler(__d('two_users', 'Désolé, les nouveaux Mots de Passe ne correspondent pas. Cliquez sur retour et recommencez') .'<br />');
                
                css::adminfoot('', '', '', '');
                return;
            }

            $tmp = 1;
        }

        if (mailler::checkdnsmail($chng_email) === false) {
            global $f_meta_nom, $f_titre;
            
            include("themes/default/header.php");
            
            GraphicAdmin(manuel('users'));
            adminhead($f_meta_nom, $f_titre);
            
            echo error_handler(__d('two_users', 'Erreur : DNS ou serveur de mail incorrect') .'<br />');
            
            css::adminfoot('', '', '', '');
            
            return;
        }

        $tmp_mns = DB::table('users')->select('mns')->where('uid', $chng_uid)->first();

        if ($tmp_mns['mns'] == 0 and $chng_mns == 1) {
            Minisites($chng_mns, $chng_uname);
        }

        if ($chng_send_email == '') {
            $chng_send_email = '0';
        }

        if ($chng_is_visible == '') {
            $chng_is_visible = '1';
        } else {
            $chng_is_visible = '0';
        }

        if ($raz_avatar) {
            $chng_avatar = "blank.gif";
        }

        if ($tmp == 0) {
            DB::table('users')->where('uid', $chng_uid)->update(array(
                'uname'             => $chng_uname,
                'name'              => $chng_name,
                'email'             => $chng_email,
                'femail'            => $chng_femail,
                'url'               => $chng_url,
                'user_from'         => $chng_user_from,
                'user_occ'          => $chng_user_occ,
                'user_intrest'      => $chng_user_intrest,
                'user_viewemail'    => $chng_user_viewemail,
                'user_avatar'       => $chng_avatar,
                'user_sig'          => $chng_user_sig,
                'bio'               => $chng_bio,
                'send_email'        => $chng_send_email,
                'is_visible'        => $chng_is_visible,
                'mns'               => $chng_mns,
                'user_lnl'          => $chng_lnl,
            ));
        }

        if ($tmp == 1) {
            $AlgoCrypt = PASSWORD_BCRYPT;
            $min_ms = 100;
            $options = ['cost' => users::getOptimalBcryptCostParameter($chng_pass, $AlgoCrypt, $min_ms)];
            $hashpass = password_hash($chng_pass, $AlgoCrypt, $options);
            $cpass = crypt($chng_pass, $hashpass);

            DB::table('users')->where('uid', $chng_uid)->update(array(
                'uname'             => $chng_uname,
                'name'              => $chng_name,
                'email'             => $chng_email,
                'femail'            => $chng_femail,
                'url'               => $chng_url,
                'user_from'         => $chng_user_from,
                'user_occ'          => $chng_user_occ,
                'user_intrest'      => $chng_user_intrest,
                'user_viewemail'    => $chng_user_viewemail,
                'user_avatar'       => $chng_avatar,
                'user_sig'          => $chng_user_sig,
                'bio'               => $chng_bio,
                'send_email'        => $chng_send_email,
                'is_visible'        => $chng_is_visible,
                'mns'               => $chng_mns,
                'pass'              => $cpass,
                'hashkey'           => 1,
                'user_lnl'          => $chng_lnl,
            ));
        
        }

        if ($chng_user_viewemail) {
            $attach = 1;
        } else {
            $attach = 0;
        }

        if ($open_user == '') {
            $open_user = 0;
        }

        if (preg_match('#[a-zA-Z_]#', $chng_groupe)) {
            $chng_groupe = '';
        }

        if ($chng_groupe != '') {
            $tab_groupe = explode(',', $chng_groupe);

            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    if (($groupevalue == "0") and ($groupevalue != '')) {
                        $chng_groupe = '';
                    }

                    if ($groupevalue == "1") {
                        $chng_groupe = '';
                    }

                    if ($groupevalue > "127") {
                        $chng_groupe = '';
                    }
                }
            }
        }

        DB::table('users_status')->where('uid', $chng_uid)->update(array(
            'attachsig'      => $attach,
            'level'         => $level,
            'open'          => $open_user,
            'groupe'        => $chng_groupe,
            'rang'          => $chng_rank
        ));
        
        DB::table('users_extend')->where('uid', $chng_uid)->update(array(
            'C1'       => $C1,
            'C2'       => $C2,
            'C3'       => $C3,
            'C4'       => $C4,
            'C5'       => $C5,
            'C6'       => $C6,
            'C7'       => $C7,
            'C8'       => $C8,
            'M1'       => $M1,
            'M2'       => $M2,
            'T1'       => $T1,
            'T2'       => $T2,
            'B1'       => $B1,
        ));

        $contents = '';
        $filename = "storage/users_private/usersbadmail.txt";
        $handle = fopen($filename, "r");

        if (filesize($filename) > 0) {
            $contents = fread($handle, filesize($filename));
        }

        fclose($handle);

        $re = '/#'. $chng_uid .'\|(\d+)/m';
        $maj = preg_replace($re, '', $contents);

        $file = fopen("storage/users_private/usersbadmail.txt", 'w');
        fwrite($file, $maj);
        fclose($file);

        global $aid;
        logs::Ecr_Log('security', "UpdateUser($chng_uid, $chng_uname) by AID : $aid", '');

        global $referer;
        if ($referer != site_url('memberslist.php'))  {
            Header('Location: '. site_url('admin.php?op=mod_users'));
        } else {
            Header('Location: '. site_url('memberslist.php'));
        }
    }

    /**
     * [nonallowedUsers description]
     *
     * @return  void
     */
    function nonallowedUsers(): void 
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('users'));
        adminhead($f_meta_nom, $f_titre);

        $users = DB::table('users')
            ->leftJoin('users_status', 'users_status.uid', '=', 'users.uid')
            ->select('users.uid', 'users.uname', 'users.name', 'users.user_regdate')
            ->where('users_status.open', 0)->orderBy('users.user_regdate')
            ->get();

        echo '
        <hr />
        <h3>'. __d('two_users', 'Utilisateur(s) en attente de validation') .'<span class="badge bg-secondary float-end">'. count($users) .'</span></h3>
        <table class="table table-no-bordered table-sm " data-toggle="table" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-buttons-class="outline-secondary" data-icons="icons" data-icons-prefix="fa" data-show-columns="true">
            <thead>
                <tr>
                    <th data-halign="center" data-align="center" class="n-t-col-xs-1" ><i class="fa fa-user-o fa-lg me-1 align-middle"></i>ID</th>
                    <th data-halign="center" data-sortable="true">'. __d('two_users', 'Identifiant') .'</th>
                    <th data-halign="center" data-align="left" data-sortable="true">'. __d('two_users', 'Name') .'</th>
                    <th data-halign="center" data-align="right">'. __d('two_users', 'Date') .'</th>
                    <th data-halign="center" data-align="center" class="n-t-col-xs-2" >'. __d('two_users', 'Fonctions') .'</th>
                </tr>
            </thead>
            <tbody>';

        foreach($users as $user) {  
            echo '
                <tr class="table-danger">
                    <td>'. $user['uid'] .'</td>
                    <td>'. $user['uname'] .'</td>
                    <td>'. $user['name'] .'</td>
                    <td>'. date('d/m/Y @ h:m', (int) $user['user_regdate']) .'</td>
                    <td>
                    <a class="me-3" href="'. site_url('admin.php?chng_uid='. $user['uid'] .'&amp;op=modifyUser#add_open_user') .'"><i class="fa fa-edit fa-lg" title="'. __d('two_users', 'Edit') .'" data-bs-toggle="tooltip"></i></a>
                    </td>
                </tr>';
        }

        echo '
            </body>
        </table>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [checkdnsmailusers description]
     *
     * @return  void
     */
    function checkdnsmailusers(): void 
    {
        global $f_meta_nom, $f_titre, $page, $end, $autocont;

        include("themes/default/header.php");

        GraphicAdmin(manuel('users'));
        adminhead($f_meta_nom, $f_titre);

        if (!isset($page)) {
            $page = 1;
        }

        if (!isset($end)) {
            $end = 0;
        }

        settype($end, 'integer');

        $pagesize = 40;
        $min = $pagesize * ($page - 1);
        $max = $pagesize;
        $next_page = $page + 1;

        $total = DB::table('users')->select('uid')->where('uid', '>', 1)->count();

        settype($total, 'integer');

        if (($page * $pagesize) > $total) {
            $end = 1;
        }

        $wrongdnsmail = 0;
        $arrayusers = array();
        $image = '18.png';

        $subject = __d('two_users', 'Votre adresse Email est incorrecte.');
        $time = date(__d('two_users', 'dateinternal'), time() + ((int) Config::get('npds.gmt') * 3600));
        $message = __d('two_users', 'Votre adresse Email est incorrecte.') .' ('. __d('two_users', 'DNS ou serveur de mail incorrect') .').<br />'. __d('two_users', 'Tous vos abonnements vers cette adresse Email ont été suspendus.') .'<br /><a href="'. site_url('user.php?op=edituser') .'">'. __d('two_users', 'Merci de fournir une nouvelle adresse Email valide.') .' <i class="fa fa-user fa-2x align-middle fa-fw"></i></a><br />'. __d('two_users', 'Sans réponse de votre part sous 60 jours vous ne pourrez plus vous connecter en tant que membre sur ce site.') .' '. __d('two_users', 'Puis votre compte pourra être supprimé.') .'<br /><br />'. __d('two_users', 'Contacter l\'administration du site.') .'<a href="mailto:'. Config::get('npds.adminmail') .'" target="_blank"><i class="fa fa-at fa-2x align-middle fa-fw"></i>';
        
        $output = '';
        $contents = '';
        $filename = "storage/users_private/usersbadmail.txt";
        $handle = fopen($filename, "r");
        
        if (filesize($filename) > 0) {
            $contents = fread($handle, filesize($filename));
        }

        fclose($handle);
        $datenvoi = '';
        $datelimit = '';

        $userchecked = DB::table('users')->select('uid', 'uname', 'email')->where('uid', '>', 1)->orderBy('uid')->limit($min)->offset($max)->get();
        
        foreach ($userchecked as $user) { 

            $uid    = $user['uid'];
            $uname  = $user['uname'];
            $email  = $user['email'];

            if (mailler::checkdnsmail($email) === true and mailler::isbadmailuser($uid) === true) {
                $re = '/#'. $uid .'\|(\d+)/m';
                $maj = preg_replace($re, '', $contents);

                $file = fopen("storage/users_private/usersbadmail.txt", 'w');
                fwrite($file, $maj);
                fclose($file);
            }

            if (mailler::checkdnsmail($email) === false) {
                if (mailler::isbadmailuser($uid) === false) {
                    $arrayusers[] = '#'. $uid .'|'. time();

                    //suspension des souscriptions
                    DB::table('subscribe')->where('uid', $uid)->delete();

                    global $aid;
                    logs::Ecr_Log("security", "UnsubUser($uid) by AID : $aid", "");

                    //suspension de l'envoi des mails pour PM suspension lnl
                    DB::table('users')->where('uid', $uid)->update(array(
                        'send_email'     => 0,
                        'user_lnl'       => 0,
                    ));

                    //envoi private message
                    DB::table('priv_msgs')->insert(array(
                        'msg_image'         => $image,
                        'subject'           => $subject,
                        'from_userid'       => 1,
                        'to_userid'         => $uid,
                        'msg_time'          => $time,
                        'msg_text'          => '<br /><code>$email</code><br /><br />$message',
                    ));

                    $datenvoi = date('d/m/Y');
                    $datelimit = date('d/m/Y', time() + 5184000);
                }

                if (mailler::isbadmailuser($uid) === true) {
                    $re = '/#'. $uid .'\|(\d+)/m';
                    preg_match($re, $contents, $res);

                    $datenvoi = date('d/m/Y', (int) $res[1]);
                    $datelimit = date('d/m/Y', $res[1] + 5184000);
                }

                $wrongdnsmail++;
                $output .= '<li>'. __d('two_users', 'DNS ou serveur de mail incorrect') .' : 
                    <a class="alert-link" href="'. site_url('admin.php?chng_uid='. $uid .'&amp;op=modifyUser') .'">
                        '. $uname .'
                    </a>
                    <span class="float-end"><i class="far fa-envelope me-1 align-middle"></i>
                        <small>'. $datenvoi .'</small>
                        <i class="fa fa-ban mx-1 align-middle"></i><small>'. $datelimit .'</small>
                    </span>
                </li>';
            }
        }

        $file = fopen("storage/users_private/usersbadmail.txt", 'a+');
        fwrite($file, implode('', $arrayusers));
        fclose($file);

        $ck = '';

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_users', 'Contrôle des serveurs de mails') .'</h3>
        <div class="alert alert-success lead">';

        if ($end != 1) {
            if (!isset($autocont)) {
                $autocont = 0;
            }

            settype($autocont, 'integer');

            if ($autocont == 1) {
                $ck = 'checked="checked"';
            } else {
                $ck = '';
            }

            echo '
            <div>'. __d('two_users', 'Serveurs de mails contrôlés') .'<span class="badge bg-success float-end">'. ($page * $pagesize) .'</span><br /></div>
            <a class="btn btn-success btn-sm mt-2" href="'. site_url('admin.php?op=checkdnsmail_users&amp;page='. $next_page .'&amp;end='. $end) .'">Continuer</a>
            <hr />
            <div class="text-end"><input id="controlauto" '. $ck .' type="checkbox" /></div>
            <script type="text/javascript">
            //<![CDATA[
                $(function () {
                    check = $("#controlauto").is(":checked");
                    if(check)
                    setTimeout(function(){ document.location.href="'. site_url('admin.php?op=checkdnsmail_users&page='. $next_page .'&end='. $end .'&autocont=1') .'"; }, 3000);
                });
                $("#controlauto").on("click", function(){
                    check = $("#controlauto").is(":checked");
                    if(check)
                    setTimeout(function(){ document.location.href="'. site_url('admin.php?op=checkdnsmail_users&page='. $next_page .'&end='. $end .'&autocont=1') .'"; }, 3000);
                    else
                    setTimeout(function(){ document.location.href="'. site_url('admin.php?op=checkdnsmail_users&page='. $next_page .'&end='. $end .'&autocont=0') .'"; }, 3000);
                });
            //]]>
            </script>';
        } else {
            echo __d('two_users', 'Serveurs de mails contrôlés') .'<span class="badge bg-success float-end">'. $total .'</span>';
        }
        
        echo
        '</div>';

        if ($end != 1) {
            if ($wrongdnsmail > 0) {
                echo '
            <div class="alert alert-danger">
                <p class="lead">'. __d('two_users', 'DNS ou serveur de mail incorrect') .'<span class="badge bg-danger float-end">'. $wrongdnsmail .'</span></p>
                <hr />
                '. __d('two_users', 'Toutes les souscriptions de ces utilisateurs ont été suspendues.') .'<br />
                '. __d('two_users', 'Un message privé leur a été envoyé sans réponse à ce message sous 60 jours ces utilisateurs ne pourront plus se connecter au site.') .'<br /><br />
                <ul>'. $output .'</ul>
            </div>';
            } else {
                echo '<div class="alert alert-success">OK</div>';
            }
        }

        if ($end == 1) {
            $re = '/#(\d+)\|(\d+)/m';
            preg_match_all($re, $contents, $matches);

            $u = $matches[1];
            $t = $matches[2];

            $nbu = count($u);
            $unames = array();
            $whereInParameters  = implode(',', $u);

            $result = DB::table('users')->select('uid', 'uname')->where('uid', 'IN', $whereInParameters)->get();

            foreach ($result as $names) {
                $unames[] = $names['uname'];
                $uids[] = $names['uid'];
            }

            echo '
        <div class="alert alert-danger">
            <div class="lead">'. __d('two_users', 'DNS ou serveur de mail incorrect') .' <span class="badge bg-danger float-end">'. $nbu .'</span></div>';
            
            if ($nbu > 0) {
                echo '
                <hr />'. __d('two_users', 'Toutes les souscriptions de ces utilisateurs ont été suspendues.') .'<br />
            '. __d('two_users', 'Un message privé leur a été envoyé sans réponse à ce message sous 60 jours ces utilisateurs ne pourront plus se connecter au site.') .'<br /><br />
            <ul>';

                for ($row = 0; $row < $nbu; $row++) {
                    $dateenvoi = date('d/m/Y', (int) $t[$row]);
                    $datelimit = date('d/m/Y', $t[$row] + 5184000);
                    echo '
                    <li>'. __d('two_users', 'DNS ou serveur de mail incorrect') .' <i class="fa fa-user-o me-1 "></i> : <a class="alert-link" href="'. site_url('admin.php?chng_uid='. $uids[$row] .'&amp;op=modifyUser') .'">'. $unames[$row] .'</a><span class="float-end"><i class="far fa-envelope me-1 align-middle"></i><small>'. $dateenvoi .'</small><i class="fa fa-ban mx-1 align-middle"></i><small>'. $datelimit .'</small></span></li>';
                }

                echo '
            </ul>';
            }

            echo '
        </div>';
        }

        css::adminfoot('', '', '', '');
    }

    switch ($op) {
        case 'extractUserCSV':
            extractUserCSV();
            break;

        case "modifyUser":
            modifyUser($chng_uid);
            break;

        case 'updateUser':
            settype($add_user_viewemail, 'integer');
            settype($add_is_visible, 'string');
            settype($add_mns, 'integer');
            settype($B1, 'string');
            settype($raz_avatar, 'integer');
            settype($add_send_email, 'integer');

            if (isset($add_group)) {
                $add_group = implode(',', $add_group);
            } else {
                $add_group = '';
            }

            updateUser($chng_uid, $add_uname, $add_name, $add_url, $add_email, $add_femail, $add_user_from, $add_user_occ, $add_user_intrest, $add_user_viewemail, $add_avatar, $add_user_sig, $add_bio, $add_pass, $add_pass2, $add_level, $add_open_user, $add_group, $add_send_email, $add_is_visible, $add_mns, $C1, $C2, $C3, $C4, $C5, $C6, $C7, $C8, $M1, $M2, $T1, $T2, $B1, $raz_avatar, $chng_rank, $user_lnl);
            break;

        case 'delUser':

            include("themes/default/header.php");

            GraphicAdmin(manuel('users'));

            echo '
            <h3 class="text-danger mb-3">'. __d('two_users', 'Supprimer un utilisateur') .'</h3>
            <div class="alert alert-danger lead">'. __d('two_users', 'Etes-vous sûr de vouloir effacer') .' '. __d('two_users', 'Utilisateur') .' <strong>'. $chng_uid .'</strong> ? <br />
                <a class="btn btn-danger mt-3" href="admin.php?op=delUserConf&amp;del_uid='. $chng_uid .'&amp;referer='. basename($referer) .'">'. __d('two_users', 'Oui') .'</a>';
            
            if (basename($referer) != site_url('memberslist.php')) {
                echo '
                <a class="btn btn-secondary mt-3" href="'. site_url('admin.php?op=mod_users') .'">'. __d('two_users', 'Non') .'</a>';
            } else {
                echo '
                <a class="btn btn-secondary mt-3" href="'. site_url('memberslist.php') .'">'. __d('two_users', 'Non') .'</a>';
            }

            echo '
            </div>';

            include("themes/default/footer.php");
            break;

        case 'delUserConf':
            $users = DB::table('users')->select('uid', 'uname')->where('uid', $del_uid)->orWhere('uname', '=', $del_uid)->first();

            $del_uid = $users['uid'];
            $del_uname = $users['uname'];
            
            if ($del_uid != 1) {
                DB::table('users')->where('uid', $del_uid)->delete();

                DB::table('users_status')->where('uid', $del_uid)->delete();

                DB::table('users_extend')->where('uid', $del_uid)->delete();

                DB::table('subscribe')->where('uid', $del_uid)->delete();

                //  Changer les articles et reviews pour les affecter à un pseudo utilisateurs  ( 0 comme uid et ' ' comme uname )
                DB::table('stories')->where('informant', $del_uname)->update(array(
                    'informant'       => ' ',
                )); 
                
                DB::table('reviews')->where('reviewer', $del_uname)->update(array(
                    'reviewer'       => ' ',
                ));

                include("modules/upload/config/upload.conf.php");

                if ($DOCUMENTROOT == '') {
                    global $DOCUMENT_ROOT;
                    if ($DOCUMENT_ROOT) {
                        $DOCUMENTROOT = $DOCUMENT_ROOT;
                    } else {
                        $DOCUMENTROOT = $_SERVER['DOCUMENT_ROOT'];
                    }
                }

                $user_dir = $DOCUMENTROOT . $racine .'/storage/users_private/'. $del_uname;

                // Supprimer son ministe s'il existe
                if (is_dir($user_dir .'/mns')) {
                    $dir = opendir($user_dir .'/mns');
                    
                    while (false !== ($nom = readdir($dir))) {
                        if ($nom != '.' && $nom != '..' && $nom != '') {
                            @unlink($user_dir .'/mns/'. $nom);
                        }
                    }
                    closedir($dir);
                    @rmdir($user_dir .'/mns');
                }

                // Mettre un fichier 'delete' dans sa home_directory si elle existe
                if (is_dir($user_dir)) {
                    $fp = fopen($user_dir .'/delete', 'w');
                    fclose($fp);
                }

                // Changer les posts, les commentaires, ... pour les affecter à un pseudo utilisateurs  ( 0 comme uid et ' ' comme uname)
                DB::table('posts')->where('poster_id', $del_uid)->update(array(
                    'poster_id'       => 0,
                ));

                // Met à jour les modérateurs des forums
                $pat = '#\b'. $del_uid .'\b#';

                $forums = DB::table('forums')->select('forum_id', 'forum_moderator')->get();

                foreach($forums as $forum) {
                    $tmp_moder = explode(',', $forum['forum_moderator']);

                    if (preg_match($pat, $forum['forum_moderator'])) {
                        unset($tmp_moder[array_search($del_uid, $tmp_moder)]);
                        
                        DB::table('forums')->where('forum_id', $forum['forum_id'])->update(array(
                            'forum_moderator'       => implode(',', $tmp_moder),
                        ));
                    }
                }

                // Mise à jour du fichier badmailuser
                $contents = '';
                $filename = "storage/users_private/usersbadmail.txt";
                $handle = fopen($filename, "r");

                if (filesize($filename) > 0) {
                    $contents = fread($handle, filesize($filename));
                }

                fclose($handle);
                $re = '/#'. $del_uid .'\|(\d+)/m';
                $maj = preg_replace($re, '', $contents);
                $file = fopen("storage/users_private/usersbadmail.txt", 'w');
                fwrite($file, $maj);
                fclose($file);

                global $aid;
                logs::Ecr_Log('security', "DeleteUser($del_uid) by AID : $aid", '');
            }

            if ($referer != site_url('memberslist.php')) {
                Header('Location: '. site_url('admin.php?op=mod_users'));
            } else {
                Header('Location: '. site_url('memberslist.php'));
            }
            break;

        case 'addUser':
            settype($add_user_viewemail, 'integer');
            settype($add_is_visible, 'string');
            settype($add_mns, 'integer');
            settype($B1, 'string');
            settype($raz_avatar, 'integer');
            settype($add_send_email, 'integer');

            if (DB::table('users')->select('uname')->where('uname', $add_uname)->first() > 0) {

                include("themes/default/header.php");

                GraphicAdmin(manuel('users'));
                adminhead($f_meta_nom, $f_titre);

                echo error_handler('<i class="fa fa-exclamation me-2"></i>'. __d('two_users', 'ERREUR : cet identifiant est déjà utilisé') .'<br />');
                
                css::adminfoot('', '', '', '');
                return;
            }

            if (!($add_uname && $add_email && $add_pass) or (preg_match('#[^a-zA-Z0-9_-]#', $add_uname))) {

                include("themes/default/header.php");

                GraphicAdmin(manuel('users'));
                adminhead($f_meta_nom, $f_titre);

                echo error_handler(__d('two_users', 'Vous devez remplir tous les Champs') .'<br />'); // ce message n'est pas très précis ..
                
                css::adminfoot('', '', '', '');
                return;
            }

            if (mailler::checkdnsmail($add_email) === false) {
                global $f_meta_nom, $f_titre;
                
                include("themes/default/header.php");

                GraphicAdmin(manuel('users'));
                adminhead($f_meta_nom, $f_titre);
                
                echo error_handler(__d('two_users', 'Erreur : DNS ou serveur de mail incorrect') .'<br />');
                
                css::adminfoot('', '', '', '');
                return;
            }

            $AlgoCrypt = PASSWORD_BCRYPT;
            $min_ms = 100;
            $options = ['cost' => users::getOptimalBcryptCostParameter($add_pass, $AlgoCrypt, $min_ms)];
            $hashpass = password_hash($add_pass, $AlgoCrypt, $options);
            $add_pass = crypt($add_pass, $hashpass);

            if ($add_is_visible == '') {
                $add_is_visible = '1';
            } else {
                $add_is_visible = '0';
            }

            $user_regdate = time() + ((int) Config::get('npds.gmt') * 3600);

            $Default_Theme = Config::get('npds.Default_Theme');
            $Default_Skin = Config::get('npds.Default_Theme');

            $usr_id = DB::table('users')->insertGetId(array(
                'name'          => $add_name,
                'uname'         => $add_uname,
                'email'         => $add_email,
                'femail'        => $add_femail,
                'url'           => $add_url,
                'user_regdate'  => $user_regdate,
                'user_from'     => $add_user_from,
                'user_occ'      => $add_user_occ,
                'user_intrest'  => $add_user_intrest,
                'user_viewemail'=> $add_user_viewemail,
                'user_avatar'   => $add_avatar,
                'user_sig'      => $add_user_sig,
                'bio'           => $add_bio,
                'pass'          => $add_pass,
                'hashkey'       => 1,
                'send_email'    => $add_send_email,
                'is_visible'    => $add_is_visible,
                'mns'           => $add_mns,
                'theme'         => ($Default_Theme+$Default_Skin),
            ));

            //$user = DB::table('users')->select(uid)->where('uname', $add_uname)->first(); /// recup id sur retour insertGetId
            DB::table('users_extend')->insert(array(
                'uid'       => $usr_id,
                'C1'       => $C1,
                'C2'       => $C2,
                'C3'       => $C3,
                'C4'       => $C4,
                'C5'       => $C5,
                'C6'       => $C6,
                'C7'       => $C7,
                'C8'       => $C8,
                'M1'       => $M1,
                'M2'       => $M2,
                'T1'       => $T1,
                'T2'       => $T2,
                'B1'       => $B1,
            ));
            
            if ($add_user_viewemail) {
                $attach = 1;
            } else {
                $attach = 0;
            }

            if (isset($add_group)) {
                $add_group = implode(',', $add_group);
            } else {
                $add_group = '';
            }

            DB::table('users_status')->insert(array(
                'uid'           => $usr_id,
                'posts'         => 0,
                'attachsig'     => $attach,
                'rang'          => $chng_rank,
                'level'         => $add_level,
                'open'          => 1,
                'groupe'        => $add_group,
            ));

            Minisites($add_mns, $add_uname);

            global $aid;
            logs::Ecr_Log('security', "AddUser($add_name, $add_uname) by AID : $aid", '');

            Header('Location: '. site_url('admin.php?op=mod_users'));
            break;

        case 'unsubUser':
            $users = DB::table('users')->select('uid')->where('uid', $chng_uid)->orWhere('uname', '=', $chng_uid)->first();

            if ($users != 1) {
                DB::table('subscribe')->where('uid', $users['chng_uid'])->delete();

                global $aid;
                logs::Ecr_Log("security", "UnsubUser($chng_uid) by AID : $aid", "");
            }

            Header('Location: '. site_url('admin.php?op=mod_users'));
            break;

        case 'nonallowed_users':
            nonallowedUsers();
            break;

        case 'checkdnsmail_users':
            checkdnsmailusers();
            break;
            
        case 'mod_users':
        default:
            displayUsers();
            break;
    }

}