<?php

namespace Modules\TwoCore\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class AdminDashboard extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = '';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = '';

        $this->f_titre = __d('two_core', '');

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

    function adminMain($deja_affiches)
    {
        global $aid, $NPDS_Prefix;
    
        include("themes/default/header.php");
    
        Config::set('npds.short_menu_admin', false);
        
        $radminsuper = GraphicAdmin(manuel('admin'));
    
        echo '
        <div id="adm_men_art" class="adm_workarea">
        <h2><img src="assets/images/admin/submissions.' . Config::get('npds.admf_ext') . '" class="adm_img" title="' . __d('two_core', 'Articles') . '" alt="icon_' . __d('two_core', 'Articles') . '" />&nbsp;' . __d('two_core', 'Derniers') . ' ' . Config::get('npds.admart') . ' ' . __d('two_core', 'Articles') . '</h2>';
    
        // = DB::table('')->select()->where('', )->orderBy('')->get();
    
        $resul = sql_query("SELECT sid FROM " . $NPDS_Prefix . "stories");
        $nbre_articles = sql_num_rows($resul);
    
        settype($deja_affiches, "integer");
    
        // = DB::table('')->select()->where('', )->orderBy('')->get();
    
        $result = sql_query("SELECT sid, title, hometext, topic, informant, time, archive, catid, ihome FROM " . $NPDS_Prefix . "stories ORDER BY sid DESC LIMIT $deja_affiches, ".Config::get('npds.admart'));
    
        $nbPages = ceil($nbre_articles / Config::get('npds.admart'));
        $current = 1;
    
        if ($deja_affiches >= 1) {
            $current = $deja_affiches / Config::get('npds.admart');
        } else if ($deja_affiches < 1) {
            $current = 0;
        } else {
            $current = $nbPages;
        }
    
        $start = ($current * Config::get('npds.admart'));
    
        if ($nbre_articles) {
            echo '
            <table id ="lst_art_adm" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-buttons-class="outline-secondary" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons">
                <thead>
                    <tr>
                    <th data-sortable="true" data-halign="center" data-align="right" class="n-t-col-xs-1">ID</th>
                    <th data-halign="center" data-sortable="true" data-sorter="htmlSorter" class="n-t-col-xs-5">' . __d('two_core', 'Titre') . '</th>
                    <th data-sortable="true" data-halign="center" class="n-t-col-xs-4">' . __d('two_core', 'Sujet') . '</th>
                    <th data-halign="center" data-align="center" class="n-t-col-xs-2">' . __d('two_core', 'Fonctions') . '</th>
                    </tr>
                </thead>
                <tbody>';
            
            $i = 0;
            
            while ((list($sid, $title, $hometext, $topic, $informant, $time, $archive, $catid, $ihome) = sql_fetch_row($result)) and ($i < Config::get('npds.admart'))) {
                $affiche = false;
                
                // = DB::table('')->select()->where('', )->orderBy('')->get();
    
                $result2 = sql_query("SELECT topicadmin, topictext, topicimage FROM " . $NPDS_Prefix . "topics WHERE topicid='$topic'");
                list($topicadmin, $topictext, $topicimage) = sql_fetch_row($result2);
                
                // = DB::table('')->select()->where('', )->orderBy('')->get();
    
                $result3 = sql_query("SELECT title FROM " . $NPDS_Prefix . "stories_cat WHERE catid='$catid'");
                list($cat_title) = sql_fetch_row($result3);
    
                if ($radminsuper) {
                    $affiche = true;
                } else {
                    $topicadminX = explode(',', $topicadmin);
                    for ($iX = 0; $iX < count($topicadminX); $iX++) {
                        if (trim($topicadminX[$iX]) == $aid) { 
                            $affiche = true;
                        }
                    }
                }
    
                $hometext = strip_tags($hometext, '<br><br />');
                $lg_max = 200;
    
                if (strlen($hometext) > $lg_max) {
                    $hometext = substr($hometext, 0, $lg_max) . ' ...';
                }
                
                echo '
                <tr>
                    <td>' . $sid . '</td>
                    <td>';
    
                $title = language::aff_langue($title);
    
                if ($archive) {
                    echo $title . ' <i>(archive)</i>';
                } else {
                    if ($affiche) {
                        echo '<a data-bs-toggle="popover" data-bs-placement="left" data-bs-trigger="hover" href="article.php?sid=' . $sid . '" data-bs-content=\'   <div class="thumbnail"><img class="img-rounded" src="assets/images/topics/' . $topicimage . '" height="80" width="80" alt="topic_logo" /><div class="caption">' . htmlentities($hometext, ENT_QUOTES) . '</div></div>\' title="' . $sid . '" data-bs-html="true">' . ucfirst($title) . '</a>';
                        if ($ihome == 1) {
                            echo '<br /><small><span class="badge bg-secondary" title="' . __d('two_core', 'Catégorie') . '" data-bs-toggle="tooltip">' . language::aff_langue($cat_title) . '</span> <span class="text-danger">non publié en index</span></small>';
                        } else {
                            if ($catid > 0) {
                                echo '<br /><small><span class="badge bg-secondary" title="' . __d('two_core', 'Catégorie') . '" data-bs-toggle="tooltip"> ' . language::aff_langue($cat_title) . '</span> <span class="text-success"> publié en index</span></small>';
                            }
                        }
                    } else {
                        echo '<i>' . $title . '</i>';
                    }
                }
    
                if ($topictext == '') {
                    echo '</td>
                    <td>';
                } else {
                    echo '</td>
                    <td>' . $topictext . '<a href="index.php?op=newtopic&amp;topic=' . $topic . '" class="tooltip">' . language::aff_langue($topictext) . '</a>';
                }
    
                if ($affiche) {
                    echo '</td>
                    <td>
                    <a href="admin.php?op=EditStory&amp;sid=' . $sid . '" ><i class="fas fa-edit fa-lg me-2" title="' . __d('two_core', 'Editer') . '" data-bs-toggle="tooltip"></i></a>
                    <a href="admin.php?op=RemoveStory&amp;sid=' . $sid . '" ><i class="fas fa-trash fa-lg text-danger" title="' . __d('two_core', 'Effacer') . '" data-bs-toggle="tooltip"></i></a>';
               } else {
                    echo '</td>
                    <td>';
                }
    
                echo '</td>
                </tr>';
                $i++;
            }
    
            echo '
                </tbody>
            </table>
            <div class="d-flex my-2 justify-content-between flex-wrap">
            <ul class="pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#">' . $nbre_articles . ' ' . __d('two_core', 'Articles') . '</a></li>
                <li class="page-item disabled"><a class="page-link" href="#">' . $nbPages . ' ' . __d('two_core', 'Page(s)') . '</a></li>
            </ul>';
    
            echo paginator::paginate('admin.php?op=suite_articles&amp;deja_affiches=', '', $nbPages, $current, 1, Config::get('npds.admart'), $start);
    
            echo '
            </div>';
    
            echo '
            <form id="fad_articles" class="form-inline" action="admin.php" method="post">
                <label class="me-2 mt-sm-1">' . __d('two_core', 'ID Article:') . '</label>
                <input class="form-control  me-2 mt-sm-3 mb-2" type="number" name="sid" />
                <select class="form-select me-2 mt-sm-3 mb-2" name="op">
                    <option value="EditStory" selected="selected">' . __d('two_core', 'Editer un Article') . '</option>
                    <option value="RemoveStory">' . __d('two_core', 'Effacer l\'Article') . '</option>
                </select>
                <button class="btn btn-primary ms-sm-2 mt-sm-3 mb-2" type="submit">' . __d('two_core', 'Ok') . ' </button>
            </form>';
        }
    
        echo '</div>';
    
        include("themes/default/footer.php");
    }

}