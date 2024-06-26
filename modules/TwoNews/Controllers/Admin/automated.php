<?php

namespace Modules\TwoNews\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Automated extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'automated';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'autoStory';

        $this->f_titre = __d('two_news', 'Articles programmés');

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
     * [puthome description]
     *
     * @param   int   $ihome  [$ihome description]
     *
     * @return  void
     */
    function puthome(int $ihome): void
    {
        echo '<div class="mb-3 row">
                <label class="col-sm-4 col-form-label" for="ihome">'. __d('two_news', 'Publier dans la racine ?') .'</label>
                <div class="col-sm-8 my-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="ihome" name="ihome" value="0" '. (($ihome == 0) ? 'checked="checked"' : '') .' />
                        <label class="form-check-label" for="ihome">'. __d('two_news', 'Oui') .'</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="ihome1" name="ihome" value="1" '. (($ihome == 1) ? 'checked="checked"' : '') .' />
                        <label class="form-check-label" for="ihome1">'. __d('two_news', 'Non') .'</label>
                    </div>
                    <p class="help-block">
                        '. __d('two_news', 'Ne s\'applique que si la catégorie : \'Articles\' n\'est pas sélectionnée.') .'
                    </p>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-4 col-form-label" for="members">'. __d('two_news', 'Seulement aux membres') .', '. __d('two_news', 'Groupe') .'.</label>
                <div class="col-sm-8 my-2">
                    <div class="form-check form-check-inline">';

        // a verifier !!!
        if (($ihome > 1) and ($ihome <= 127)) {
            $Mmembers = $ihome;
            $sel1 = 'checked="checked"';
            $sel2 = '';
        } elseif ($ihome < 0) {
            $sel1 = 'checked="checked"';
            $sel2 = '';
        } else {
            $sel1 = '';
            $sel2 = 'checked="checked"'; 
        }

        echo '
                    <input class="form-check-input" type="radio" id="members" name="members" value="1" '. $sel1 .' />
                    <label class="form-check-label" for="members">'. __d('two_news', 'Oui') .'</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="members1" name="members" value="0" '. $sel2 .' />
                    <label class="form-check-label" for="members1">'. __d('two_news', 'Non') .'</label>
                    </div>
                </div>
            </div>';

        // ---- Groupes
        isset($Mmember) ? $Mmembers : $Mmembers = '';

        $tmp_groupe = '';
        foreach (groupe::liste_group() as $groupe_id => $groupe_name) {
            if ($groupe_id == '0') {
                $groupe_id = '';
            }

            $tmp_groupe .= '<option value="'. $groupe_id .'" '. (($Mmembers == $groupe_id) ? 'selected="selected"' : '') .'>'. $groupe_name .'</option>';
        }

        echo '
            <div class="mb-3 row" id="choixgroupe">
                <label class="col-sm-4 col-form-label" for="Mmembers">'. __d('two_news', 'Groupe') .'</label>
                <div class="col-sm-8">
                    <select class="form-select" id="Mmembers" name="Mmembers">'. $tmp_groupe .'</select>
                </div>
            </div>';
    }

    /**
     * [SelectCategory description]
     *
     * @param   int   $cat  [$cat description]
     *
     * @return  void
     */
    function SelectCategory(int $cat): void
    {
        echo ' 
            <div class="mb-3 row">
                <label class="col-sm-4 col-form-label" for="catid">'. __d('two_news', 'Catégorie') .'</label>
                <div class="col-sm-8">
                    <select class="form-select" id="catid" name="catid">
                        <option name="catid" value="0" '. (($cat == 0) ? 'selected="selected"' : '') .'>'. __d('two_news', 'Articles') .'</option>';
        
        $storie_categorie = DB::table('stories_cat')->select('catid', 'title')->get();

        foreach ($storie_categorie as $categorie) {

            echo '<option name="catid" value="'. $categorie['catid'] .'" '. (($categorie['catid'] == $cat) ? 'selected' : '') .'>'. language::aff_langue($categorie['title']) .'</option>';
        }

        echo '
                    </select>
                    <p class="help-block text-end">
                        <a href="'. site_url('admin.php?op=AddCategory') .'" class="btn btn-outline-primary btn-sm" title="'. __d('two_news', 'Ajouter') .'" data-bs-toggle="tooltip" >
                            <i class="fa fa-plus-square fa-lg"></i>
                        </a>&nbsp;
                        <a class="btn btn-outline-primary btn-sm" href="'. site_url('admin.php?op=EditCategory') .'" title="'. __d('two_news', 'Editer') .'" data-bs-toggle="tooltip" >
                            <i class="fa fa-edit fa-lg"></i>
                        </a>&nbsp;<a class="btn btn-outline-danger btn-sm" href="'. site_url('admin.php?op=DelCategory') .'" title="'. __d('two_news', 'Effacer') .'" data-bs-toggle="tooltip">
                            <i class="fas fa-trash fa-lg"></i>
                        </a>
                    </p>
                </div>
            </div>';
    }

    /**
     * [autoStory description]
     *
     * @return  void
     */
    function autoStory(): void
    {
        global $aid, $radminsuper, $f_meta_nom, $f_titre;

        include("themes/default/header.php");
        
        GraphicAdmin(manuel('automated'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3>'. __d('two_news', 'Liste des articles') .'</h3>
        <table id="tab_adm" data-toggle="table" data-striped="true" data-show-toggle="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <tr>
                    <th class="n-t-col-xs-6" data-sortable="true" data-halign="center">
                        '. __d('two_news', 'Titre') .'
                    </th>
                    <th class="n-t-col-xs-4 small" data-sortable="true" data-align="center" data-align="right">
                        '. __d('two_news', 'Date prévue de publication') .'
                    </th>
                    <th class="n-t-col-xs-2" data-align="center">
                        '. __d('two_news', 'Fonctions') .'
                    </th>
                </tr>
            </thead>
            <tbody>';

        $autonews = DB::table('autonews')->select('anid', 'title', 'date_debval', 'topic')->orderBy('date_debval', 'ASC')->get();

        foreach ($autonews as $news) {   
            if ($news['anid'] != '') {
                $affiche = false;
                
                $topics = DB::table('topics')->select('topicadmin')->where('topicid', $news['topic'])->first();

                if ($radminsuper) {
                    $affiche = true;
                } else {
                    $topicadminX = explode(",", $topics['topicadmin']);
                    
                    for ($i = 0; $i < count($topicadminX); $i++) {
                        if (trim($topicadminX[$i]) == $aid) { 
                            $affiche = true;
                        }
                    }
                }

                if ($new['title'] == '') {
                    $new['title'] = __d('two_news', 'Aucun Sujet');
                }

                if ($affiche) {
                    echo '
                    <tr>
                        <td>
                            <a href="'. site_url('admin.php?op=autoEdit&amp;anid=' . $news['anid']) .'">'. language::aff_langue($news['title']) .'</a>
                        </td>
                        <td>
                            '. date::formatTimestamp($news['date_debval']) .'
                        </td>
                        <td>
                            <a href="'. site_url('admin.php?op=autoEdit&amp;anid=' . $news['anid']) .'">
                                <i class="fa fa-edit fa-lg me-2" title="'. __d('two_news', 'Afficher l\'article') .'" data-bs-toggle="tooltip"></i>
                            </a>
                            <a href="'. site_url('admin.php?op=autoDelete&amp;anid=' . $news['anid']) .'">&nbsp;
                                <i class="fas fa-trash fa-lg text-danger" title="'. __d('two_news', 'Effacer l\'Article') .'" data-bs-toggle="tooltip" ></i>
                            </a>
                        </td>
                    </tr>';
                } else {
                    echo '
                    <tr>
                        <td>
                            <i>'. language::aff_langue($news['title']) .'</i>
                        </td>
                        <td>
                            '. date::formatTimestamp($news['date_debval']) .'
                        </td>
                        <td>
                            &nbsp;
                        </td>
                    </tr>';
                }
            }
        }

        echo '
            </tbody>
        </table>';

        css::adminfoot('', '', '', '');
    }

    /**
     * [autoDelete description]
     *
     * @param   int   $anid  [$anid description]
     *
     * @return  void
     */
    function autoDelete(int $anid): void
    {
        DB::table('autonews')->where('anid', $anid)->delete();

        Header('Location: '. site_url('admin.php?op=autoStory'));
    }

    /**
     * [autoEdit description]
     *
     * @param   int   $anid  [$anid description]
     *
     * @return  void
     */
    function autoEdit(int $anid): void
    {
        global $aid, $radminsuper;

        $f_meta_nom = 'autoStory';
        $f_titre = __d('two_news', 'Editer un Article');

        //==> controle droit
        admindroits($aid, $f_meta_nom);
        //<== controle droit

        $autonews = DB::table('autonews')
                    ->select('catid', 'title', 'time', 'hometext', 'bodytext', 'topic', 'informant', 'notes', 'ihome', 'date_debval', 'date_finval', 'auto_epur')
                    ->where('anid', $anid)
                    ->fist();

        $autonews['titre'] = stripslashes($autonews['title']);
        $autonews['hometext'] = stripslashes($autonews['hometext']);
        $autonews['bodytext'] = stripslashes($autonews['bodytext']);
        $autonews['notes'] = stripslashes($autonews['notes']);

        if ($autonews['topic'] < 1) {
            $autonews['topic'] = 1;
        }

        $affiche = false;
        
        $topics = DB::table('topics')
                    ->select('topicname', 'topictext', 'topicimage', 'topicadmin')
                    ->where('topicid', $autonews['topic'])
                    ->fist();

        if ($radminsuper) {
            $affiche = true;
        } else {
            $topicadminX = explode(',', $topics['topicadmin']);
            for ($i = 0; $i < count($topicadminX); $i++) {
                if (trim($topicadminX[$i]) == $aid) {
                    $affiche = true;
                }
            }
        }

        if (!$affiche) {
            header('location: '. site_url('admin.php?op=autoStory'));
        }

        $topiclogo = '<span class="badge bg-secondary" title="'. $topics['topictext'] .'" data-bs-toggle="tooltip" data-bs-placement="left">
            <strong>'. language::aff_langue($topics['topicname']) .'</strong>
        </span>';

        include("themes/default/header.php");

        GraphicAdmin(manuel('automated'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3>'. __d('two_news', 'Editer l\'Article Automatique') .'</h3>
        '. language::aff_local_langue('', 'local_user_language', __d('two_news', 'Langue de Prévisualisation')) .'
        <div class="card card-body mb-3">';
        
        if ($topics['topicimage'] !== '') {
            if (!$imgtmp = theme::theme_image('topics/' . $topics['topicimage'])) {
                $imgtmp = Config::get('npds.tipath') . $topics['topicimage'];
            }

            if (file_exists($imgtmp)) {
                $topiclogo = '<img class="img-fluid " src="'. $imgtmp .'" align="right" alt="topic_logo" loading="lazy" title="'. $topics['topictext'] .'" data-bs-toggle="tooltip" data-bs-placement="left" />';
            }
        }

        post::code_aff('<div class="d-flex">
        <div class="w-100 p-2 ps-0">
            <h3>'. $autonews['titre'] .'</h3>
        </div>
        <div class="align-self-center p-2 flex-shrink-1 h3">
            '. $topiclogo .'
        </div>
        </div>', '<div class="text-muted">'. $autonews['hometext'] .'</div>', $autonews['bodytext'], $autonews['notes']);

        echo '<hr />
        <b>'. __d('two_news', 'Utilisateur') .'</b>'. $autonews['informant'] .'
        <br />
        </div>
        <form action="'. site_url('admin.php') .'" method="post" name="adminForm" id="autoedit">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="title">'. __d('two_news', 'Titre') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="title" name="title" size="50" value="'. $autonews['titre'] .'" required="required" />
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topic">'. __d('two_news', 'Sujet') .'</label>
                <div class="col-sm-8">
                    <select class="form-select" id="topic" name="topic">';

        if ($radminsuper) {
            echo '<option value="">'. __d('two_news', 'Tous les Sujets') .'</option>';
        }

        $toplist = DB::table('topics')
                    ->select('topicid', 'topictext', 'topicadmin')
                    ->orderBy('topictext')
                    ->get();

        foreach ($toplist as $list) {    
            $affiche = false;
            
            if ($radminsuper) {
                $affiche = true;
            } else {
                $topicadminX = explode(',', $list['topicadmin']);
                
                for ($i = 0; $i < count($topicadminX); $i++) {
                    if (trim($topicadminX[$i]) == $aid) { 
                        $affiche = true;
                    }
                }
            }

            if ($affiche) {
                echo '<option '. (($list['topicid'] == $autonews['topic']) ? 'selected="selected" ' : '') .' value="'. $list['topicid'] .'">'. language::aff_langue($list['topics']) .'</option>';
            }
        }

        echo ' 
                    </select>
                </div>
            </div>';

        SelectCategory($autonews['catid']);

        puthome($autonews['ihome']);

        echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="hometext">'. __d('two_news', 'Texte d\'introduction') .'</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" rows="25" id="hometext" name="hometext" >'. $autonews['hometext'] .'</textarea>
                </div>
            </div>
            '. editeur::aff_editeur('hometext', '') . '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="bodytext">'. __d('two_news', 'Texte étendu') . '</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" rows="25" id="bodytext" name="bodytext" >'. $autonews['bodytext'] .'</textarea>
                </div>
            </div>
            '. editeur::aff_editeur('bodytext', '');

        if ($aid != $autonews['informant']) {
            echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-12" for="notes">'. __d('two_news', 'Notes') .'</label>
                <div class="col-sm-12">
                    <textarea class="tin form-control" rows="7" id="notes" name="notes">'. $autonews['notes'] .'</textarea>
                </div>
            </div>
            '. editeur::aff_editeur('notes', '');
        }

        $dd_pub = substr($autonews['date_debval'], 0, 10);
        $fd_pub = substr($autonews['date_finval'], 0, 10);
        $dh_pub = substr($autonews['date_debval'], 11, 5);
        $fh_pub = substr($autonews['date_finval'], 11, 5);

        post::publication($dd_pub, $fd_pub, $dh_pub, $fh_pub, $autonews['auto_epur']);

        echo '
            <div class="mb-3 row">
                <div class="col-sm-12">
                    <input type="hidden" name="anid" value="'. $autonews['anid'] .'" />
                    <input type="hidden" name="informant" value="'. $autonews['informant'] .'" />
                    <input type="hidden" name="op" value="autoSaveEdit" />
                    <input class="btn btn-primary" type="submit" value="'. __d('two_news', 'Sauver les modifications') .'" />
                </div>
            </div>
        </form>';

        $fv_parametres = '
            !###!
            mem_y.addEventListener("change", function (e) {
                if(e.target.checked) {
                    choixgroupe.style.display="flex";
                }
            });
            mem_n.addEventListener("change", function (e) {
                if(e.target.checked) {
                    choixgroupe.style.display="none";
                }
            });';

        $arg1 = '
            var formulid = ["autoedit"];
            const choixgroupe = document.getElementById("choixgroupe");
            const mem_y = document.querySelector("#members");
            const mem_n = document.querySelector("#members1");
            mem_y.checked ? "" : choixgroupe.style.display="none" ;';

        css::adminfoot('fv', $fv_parametres, $arg1, '');
    }

    /**
     * [autoSaveEdit description]
     *
     * @param   int     $anid         [$anid description]
     * @param   string  $title        [$title description]
     * @param   string  $hometext     [$hometext description]
     * @param   string  $bodytext     [$bodytext description]
     * @param   int     $topic        [$topic description]
     * @param   string  $notes        [$notes description]
     * @param   int     $catid        [$catid description]
     * @param   int     $ihome        [$ihome description]
     * @param   string  $informant    [$informant description]
     * @param   int     $members      [$members description]
     * @param   string                [ description]
     * @param   int     $Mmembers     [$Mmembers description]
     * @param   string  $date_debval  [$date_debval description]
     * @param   string  $date_finval  [$date_finval description]
     * @param   int     $epur         [$epur description]
     *
     * @return  void
     */
    function autoSaveEdit(int $anid, string $title, string $hometext, string $bodytext, int $topic, string $notes, int $catid, int $ihome, string $informant, int $members, string|int $Mmembers, string $date_debval, string $date_finval, int $epur): void
    {
        $date_debval = !isset($date_debval) ? $dd_pub . ' ' . $dh_pub . ':01' : $date_debval;
        $date_finval = !isset($date_finval) ? $fd_pub . ' ' . $fh_pub . ':01' : $date_finval;
        
        if ($date_finval < $date_debval) {
            $date_finval = $date_debval;
        }

        $title = stripslashes(str::FixQuotes(str_replace('"', '&quot;', $title)));
        $hometext = stripslashes(str::FixQuotes($hometext));
        $bodytext = stripslashes(str::FixQuotes($bodytext));
        $notes = stripslashes(str::FixQuotes($notes));

        if (($members == 1) and ($Mmembers == '')) {
            $ihome = '-127';
        }

        if (($members == 1) and (($Mmembers > 1) and ($Mmembers <= 127))) { 
            $ihome = $Mmembers;
        }

        DB::table('autonews')->where('anid', $anid)->update(array(
            'catid'         => $catid,
            'title'         => $title,
            'time'          => 'now()',
            'hometext'      => $hometext,
            'bodytext'      => $bodytext,
            'topic'         => $topic,
            'notes'         => $notes,
            'ihome'         => $ihome,
            'date_debval'   => $date_debval,
            'date_finval'   => $date_finval,
            'auto_epur'     => $epur,
        ));

        if (Config::get('npds.ultramode')) {
            news::ultramode();
        }
        
        Header('Location: '. site_url('admin.php?op=autoEdit&anid='. $anid));
    }

}