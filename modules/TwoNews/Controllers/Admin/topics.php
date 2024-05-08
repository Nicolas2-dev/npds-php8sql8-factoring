<?php

namespace Modules\TwoNews\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Topics extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'topics';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'topicsmanager';

        $this->f_titre = __d('two_news', 'Gestion des sujets');

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
     * [topicsmanager description]
     *
     * @return  void
     */
    function topicsmanager(): void 
    {
        global $f_meta_nom, $f_titre, $nook;

        include("themes/default/header.php");

        GraphicAdmin(manuel('topics'));
        adminhead($f_meta_nom, $f_titre);

        $result = DB::table('topics')->select('topicid', 'topicname', 'topicimage', 'topictext')->orderBy('topicname')->get();

        settype($topicadmin, 'string');

        if ($result > 0) {
            echo '
            <hr />
            <h3 class="my-3">'. __d('two_news', 'Sujets actifs') .'<span class="badge bg-secondary float-end">'. sql_num_rows($result) .'</span></h3>';
            
            foreach($result as $topic) {
                echo '
                <div class="card card-body mb-2" id="top_'. $topic['topicid'] .'">
                    <div class=" topi">
                        <div class="">';

                if (($topic['topicimage']) or ($topic['topicimage'] != '')) {
                    echo '<a href="'. site_url('admin.php?op=topicedit&amp;topicid='. $topic['topicid']) .'"><img class="img-thumbnail" style="height:80px;  max-width:120px" src="'. Config::get('npds.tipath') . $topicimage .'" data-bs-toggle="tooltip" title="ID : '. $topicid .'" alt="'. $topic['topicname'] .'" /></a>';
                } else {
                    echo '<a href="'. site_url('admin.php?op=topicedit&amp;topicid='. $topic['topicid']) .'"><img class="img-thumbnail" style="height:80px;  max-width:120px" src="'. Config::get('npds.tipath') .'topics.png" data-bs-toggle="tooltip" title="ID : '. $topicid .'" alt="'. $topic['topicname'] .'" /></a>';
                }

                echo '
                        </div>
                        <div class="">
                            <h4 class="my-3"><a href="'. site_url('admin.php?op=topicedit&amp;topicid='. $topic['topicid']) .'" ><i class="fa fa-edit me-1 align-middle"></i>'. language::aff_langue($topic['topicname']) .'</a></h4>
                            <p>'. language::aff_langue($topic['topictext']) .'</p>
                            <div id="shortcut-tools_'. $topic['topicid'] .'" class="n-shortcut-tools" style="display:none;">
                                <a class="text-danger btn" href="'. site_url('admin.php?op=topicdelete&amp;topicid='. $topicid .'&amp;ok=0') .'" ><i class="fas fa-trash fa-2x"></i></a></div>
                        </div>
                    </div>
                </div>';
            }
        }

        echo '
        <hr />
        <a name="addtopic"></a>';

        if (isset($nook)) {
            echo '<div class="alert alert-danger alert-dismissible fade show">Le nom de ce sujet existe déjà ! <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }

        echo '
        <h3 class="my-4">'. __d('two_news', 'Ajouter un nouveau Sujet') .'</h3>
        <form action="'. site_url('admin.php') .'" method="post" id="topicmake">
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topicname">'. __d('two_news', 'Intitulé') .'</label>
                <div class="col-sm-8">
                    <input id="topicname" class="form-control" type="text" name="topicname" maxlength="20" value="'. $topic['topicname'] .'" placeholder="'. __d('two_news', 'cesiteestgénial') .'" required="required" />
                    <span class="help-block">'. __d('two_news', '(un simple nom sans espaces)') .' - '. __d('two_news', 'max caractères') .' : <span id="countcar_topicname"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topictext">'. __d('two_news', 'Texte') .'</label>
                <div class="col-sm-8">
                    <textarea id="topictext" class="form-control" rows="3" name="topictext" maxlength="250" placeholder="'. __d('two_news', 'ce site est génial') .'" required="required" >'. $topictext .'</textarea>
                    <span class="help-block">'. __d('two_news', '(description ou nom complet du sujet)') .' - '. __d('two_news', 'max caractères') .' : <span id="countcar_topictext"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topicimage">'. __d('two_news', 'Image') .'</label>
                <div class="col-sm-8">
                    <input id="topicimage" class="form-control" type="text" name="topicimage" maxlength="20" value="'. $topic['topicimage'] .'" placeholder="genial.png" />
                    <span class="help-block">'. __d('two_news', '(nom de l\'image + extension)') .' ('. Config::get('npds.tipath') .'). - '. __d('two_news', 'max caractères') .' : <span id="countcar_topicimage"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="topicadmin">'. __d('two_news', 'Administrateur(s)') .'</label>
                <div class="col-sm-8">
                    <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user-cog fa-lg"></i></span>
                    <input class="form-control" type="text" id="topicadmin" name="topicadmin" maxlength="255" value="'. $topicadmin .'" required="required" />
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="op" value="topicmake" />
                    <button class="btn btn-primary" type="submit" ><i class="fa fa-plus-square fa-lg me-2"></i>'. __d('two_news', 'Ajouter un Sujet') .'</button>
                </div>
            </div>
        </form>';

        echo '
        <script type="text/javascript">
            //<![CDATA[
                var topid="";
                $(".topi").hover(function(){
                    topid = $(this).parent().attr("id");
                    topid=topid.substr (topid.search(/\d/))
                    $button=$("#shortcut-tools_"+topid);
                    $button.show();
                }, function(){
                $button.hide();
                });
            //]]>
        </script>';

        // le validateur pour topicadmin ne fonctionne pas ?!!
        $fv_parametres = '
        topicadmin: {
            validators: {
                callback: {
                    message: "Please choose an administrator FROM the provided list.",
                    callback: function(value, validator, $field) {
                    diff="";
                    var value = $field.val();
                                console.log(value);//

                    if (value === "") {return true;}
                    function split( n ) {
                    return n.split( /,\s*/ );
                    }
                    diff = $(split(value)).not(admin).get();
                    console.log(diff);
                    if (diff!="") {return false;}
                    return true;
                    }
                }
            }
        },

        topicname: {
            validators: {
                regexp: {
                    regexp: /^\w+$/i,
                    message: "'. __d('two_news', 'Doit être un mot sans espace.') .'"
                }
            }
        },

        topicimage: {
            validators: {
                regexp: {
                    regexp: /^[\w]+\\.(jpg|jpeg|png|gif)$/,
                    message: "'. __d('two_news', 'Doit être un nom de fichier valide avec une de ces extensions : jpg, jpeg, png, gif.') .'"
                }
            }
        },';

        $arg1 = '
        var formulid = ["topicmake"];
        inpandfieldlen("topicname",20);
        inpandfieldlen("topictext",250);
        inpandfieldlen("topicimage",20);
        inpandfieldlen("topicadmin",255);';

        echo js::auto_complete_multi('admin', 'aid', 'authors', 'topicadmin', '');

        css::adminfoot('fv', $fv_parametres, $arg1, '');
    }

    /**
     * [topicedit description]
     *
     * @param   int   $topicid  [$topicid description]
     *
     * @return  void
     */
    function topicedit(int $topicid): void 
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('topics'));
        adminhead($f_meta_nom, $f_titre);

        $topic  = DB::table('topics')->select('topicid', 'topicname', 'topicimage', 'topictext', 'topicadmin')->where('topicid', $topicid)->first();

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_news', 'Editer le Sujet :') .' <span class="text-muted">'. language::aff_langue($topic['topicname']) .'</span></h3>';

        if ($topic['topicimage'] != '') {
            echo '
        <div class="card card-body my-4 py-3"><img class="img-fluid mx-auto d-block" src="'. Config::get('npds.tipath') . $topic['topicimage'] .'" alt="image-sujet" /></div>';
        }

        echo '
        <form action="'. site_url('admin.php') .'" method="post" id="topicchange">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="topicname">'. __d('two_news', 'Intitulé') .'</label>
                    <div class="col-sm-8">
                    <input id="topicname" class="form-control" type="text" name="topicname" maxlength="20" value="'. $topic['topicname'] .'" placeholder="'. __d('two_news', 'cesiteestgénial') .'" required="required" />
                    <span class="help-block">'. __d('two_news', '(un simple nom sans espaces)') .' - '. __d('two_news', 'max caractères') .' : <span id="countcar_topicname"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="topictext">'. __d('two_news', 'Texte') .'</label>
                    <div class="col-sm-8">
                    <textarea id="topictext" class="form-control" rows="3" name="topictext" maxlength="250" placeholder="'. __d('two_news', 'ce site est génial') .'" required="required">'. $topic['topictext'] .'</textarea>
                    <span class="help-block">'. __d('two_news', '(description ou nom complet du sujet)') .' - '. __d('two_news', 'max caractères') .' : <span id="countcar_topictext"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="topicimage">'. __d('two_news', 'Image') .'</label>
                    <div class="col-sm-8">
                    <input id="topicimage" class="form-control" type="text" name="topicimage" maxlength="20" value="'. $topic['topicimage'] .'" placeholder="genial.png" />
                    <span class="help-block">'. __d('two_news', '(nom de l\'image + extension)') .' ('. Config::get('npds.tipath') .'). - '. __d('two_news', 'max caractères') .' : <span id="countcar_topicimage"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-4" for="topicadmin">'. __d('two_news', 'Administrateur(s) du sujet') .'</label>
                    <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-cog fa-lg"></i></span>
                        <input class="form-control" type="text" id="topicadmin" name="topicadmin" maxlength="255" value="'. $topic['topicadmin'] .'" />
                    </div>
                    </div>
                </div>
            </fieldset>
            <fieldset>
            <hr />
            <h4 class="my-3">'. __d('two_news', 'Ajouter des Liens relatifs au Sujet') .'</h4>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="name">'. __d('two_news', 'Nom du site') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" name="name" id="name" maxlength="30" />
                    <span class="help-block">'. __d('two_news', 'max caractères') .' : <span id="countcar_name"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="url">'. __d('two_news', 'URL') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="url" name="url" id="url" maxlength="320" placeholder="http://www.valideurl.org" />
                    <span class="help-block">'. __d('two_news', 'max caractères') .' : <span id="countcar_url"></span></span>
                </div>
            </div>
            </fieldset>
            <div class="mb-3 row">
                <input type="hidden" name="'.$topic['topicid'].'" value="'. $topic['topicid'] .'" />
                <input type="hidden" name="op" value="topicchange" />
                <div class="col-sm-8 ms-sm-auto">
                    <button class="btn btn-primary" type="submit">'. __d('two_news', 'Sauver les modifications') .'</button>
                    <button class="btn btn-secondary" onclick="javascript:document.location.href=\''. site_url('admin.php?op=topicsmanager') .'\'">'. __d('two_news', 'Retour en arrière') .'</button>
                </div>
            </div>
        </form>';
        /*
        <form id="fad_deltop" action="'. site_url('admin.php') .'" method="post">
            <input type="hidden" name="topic['topicid']" value="'.$topic['topicid'].'" />
            <input type="hidden" name="op" value="topicdelete" />
        </form>
        <button class="btn btn-danger"><i class="fas fa-trash fa-lg"></i>&nbsp;&nbsp;'.__d('two_news', 'Effacer le Sujet !').'</button>
        */

        echo '
            <hr />
            <h3 class="my-2">'. __d('two_news', 'Gérer les Liens Relatifs : ') .' <span class="text-muted">'. language::aff_langue($topic['topicname']) .'</span></h3>';

        $r_related = DB::table('related')->select('rid', 'name', 'url')->where('tid', $topic['topicid'])->first();

        echo '
        <table id="tad_linkrel" data-toggle="table" data-striped="true" data-icons="icons" data-icons-prefix="fa">
            <thead>
                <th data-sortable="true" data-halign="center">'. __d('two_news', 'Nom') .'</th>
                <th data-sortable="true" data-halign="center">'. __d('two_news', 'Url') .'</th>
                <th class="n-t-col-xs-2" data-halign="center" data-align="right">'. __d('two_news', 'Fonctions') .'</th>
            </thead>
            <tbody>';

        while (list($rid, $name, $url) = sql_fetch_row($res)) {
        foreach ($r_relatad as $related)
            echo '
                    <tr>
                        <td>'. $related['name'] .'</td>
                        <td><a href="'. $related['url'] .'" target="_blank">'. $related['url'] .'</a></td>
                        <td>
                        <a href="'. site_url('admin.php?op=relatededit&amp;tid='. $topic['topicid'] .'&amp;rid='. $related['rid']) .'" ><i class="fas fa-edit fa-lg" data-bs-toggle="tooltip" title="'. __d('two_news', 'Editer') .'"></i></a>&nbsp;
                        <a href="'. $related['url'] .'" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a>&nbsp;
                        <a href="'. site_url('admin.php?op=relateddelete&amp;tid='. $topic['topicid'] .'&amp;rid='. $related['rid']) .'" ><i class="fas fa-trash fa-lg text-danger" data-bs-toggle="tooltip" title="'. __d('two_news', 'Effacer') .'"></i></a>
                        </td>
                    </tr>';
        }

        echo '
                </tbody>
            </table>';

        $fv_parametres = '
        topicadmin: {
            validators: {
                callback: {
                    message: "Please choose an administrator from the provided list.",
                    callback: function(value, validator, $field) {
                    diff="";
                    var value = $field.val();
                    if (value === "") {return true;}
                    function split( n ) {
                        return n.split( /,\s*/ );
                    }
                    diff = $(split(value)).not(admin).get();
                    console.log(diff);
                    if (diff!="") {return false;}
                    return true;
                    }
                }
            }
        },
        topicimage: {
            validators: {
                regexp: {
                    regexp: /^[\w]+\\.(jpg|jpeg|png|gif)$/,
                    message: "This must be a valid file name with one of this extension jpg, jpeg, png, gif."
                }
            }
        },
        topicname: {
            validators: {
                regexp: {
                    regexp: /^\w+$/i,
                    message: "This must be a simple word without space."
                }
            }
        },';

        $arg1 = '
        var formulid = ["topicchange"];
        inpandfieldlen("topicname",20);
        inpandfieldlen("topictext",250);
        inpandfieldlen("topicimage",20);
        inpandfieldlen("name",30);
        inpandfieldlen("url",320);
        ';

        echo js::auto_complete_multi('admin', 'aid', 'authors', 'topicadmin', '');

        css::adminfoot('fv', $fv_parametres, $arg1, '');
    }

    /**
     * [relatededit description]
     *
     * @param   int   $tid  [$tid description]
     * @param   int   $rid  [$rid description]
     *
     * @return  void
     */
    function relatededit(int $tid, int $rid): void 
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('topics'));
        adminhead($f_meta_nom, $f_titre);

        $related = DB::table('related')->select('name', 'url')->where('rid', $rid)->first();

        $topic = DB::table('topics')->select('topictext', 'topicimage')->where('topicid', $tid)->first();

        echo '
        <hr />
        <h3>'. __d('two_news', 'Sujet : ') .' '. $topic['topictext'] .'</h3>
        <h4>'. __d('two_news', 'Editer les Liens Relatifs') .'</h4>';

        if ($topic['topicimage'] != "") {  
            echo '
        <div class="thumbnail">
            <img class="img-fluid " src="'. Config::get('npds.tipath') . $topic['topicimage'] .'" alt="'. $topic['topictext'] .'" />
        </div>';
        }

        echo '
        <form class="form-horizontal" action="'. site_url('admin.php') .'" method="post" id="editrelatedlink">
            <fieldset>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="name">'. __d('two_news', 'Nom du site') .'</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="name" id="name" value="'. $related['name'] .'" maxlength="30" required="required" />
                    <span class="help-block text-end"><span id="countcar_name"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="url">'. __d('two_news', 'URL') .'</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <a href="'. $related['url'] .'" target="_blank"><i class="fas fa-external-link-alt fa-lg"></i></a>
                        </span>
                        <input type="url" class="form-control" name="url" id="url" value="'. $related['url'] .'" maxlength="320" />
                    </div>
                    <span class="help-block text-end"><span id="countcar_url"></span></span>
                    </div>
                    <input type="hidden" name="op" value="relatedsave" />
                    <input type="hidden" name="tid" value="'. $tid .'" />
                    <input type="hidden" name="rid" value="'. $rid .'" />
                </fieldset>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <button class="btn btn-primary col-12" type="submit">'. __d('two_news', 'Sauver les modifications') .'</button>
                </div>
            </div>
        </form>';

        $arg1 = '
            var formulid = ["editrelatedlink"];
            inpandfieldlen("name",30);
            inpandfieldlen("url",320);';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [relatedsave description]
     *
     * @param   int     $tid   [$tid description]
     * @param   int     $rid   [$rid description]
     * @param   string  $name  [$name description]
     * @param   string  $url   [$url description]
     *
     * @return  void
     */
    function relatedsave(int $tid, int $rid, string $name, string $url): void
    {
        DB::table('related')->where('rid', $rid)->update(array(
            'name'      => $name,
            'url'       => $url,
        ));

        Header('Location: '. site_url('admin.php?op=topicedit&topicid='. $tid));
    }

    /**
     * [relateddelete description]
     *
     * @param   int   $tid  [$tid description]
     * @param   int   $rid  [$rid description]
     *
     * @return  void
     */
    function relateddelete(int $tid, int $rid): void 
    {
        DB::table('related')->where('rid', $rid)->delete();

        Header('Location: '. site_url('admin.php?op=topicedit&topicid='. $tid));
    }

    /**
     * [topicmake description]
     *
     * @param   string  $topicname   [$topicname description]
     * @param   string  $topicimage  [$topicimage description]
     * @param   string  $topictext   [$topictext description]
     * @param   string  $topicadmin  [$topicadmin description]
     *
     * @return  void
     */
    function topicmake(string $topicname, string $topicimage, string $topictext, string $topicadmin): void
    {
        $topicname = stripslashes(str::FixQuotes($topicname));

        $istopicname = DB::table('topics')->select('*')->where('topicname', $topicname)->first();

        if ($istopicname !== 0) {
            Header('Location: '. site_url('admin.php?op=topicsmanager&nook=nook#addtopic'));
            die();
        }

        $topicimage = stripslashes(str::FixQuotes($topicimage));
        $topictext = stripslashes(str::FixQuotes($topictext));

        DB::table('topics')->insert(array(
            'topicname'         => $topicname,
            'topicimage'        => $topicimage,
            'topictext'         => $topictext,
            'counter'           => 0,
            'topicadmin	'       => $topicadmin,
        ));

        global $aid;
        logs::Ecr_Log("security", "topicMake ($topicname) by AID : $aid", "");

        $topicadminX = explode(",", $topicadmin);
        array_pop($topicadminX);

        for ($i = 0; $i < count($topicadminX); $i++) {
            trim($topicadminX[$i]);

            $nres = DB::table('droits')->select('*')->where('d_aut_aid', $topicadminX[$i])->where('d_droits', 11112)->get();

            if ($nres == 0) {
                DB::table('droits')->insert(array(
                    'd_aut_aid'       => $topicadminX[$i],
                    'd_fon_fid'       => 2,
                    'd_droits'       => 11112,
                ));

            }
        }

        Header('Location: '. site_url('admin.php?op=topicsmanager#addtopic'));
    }

    /**
     * [topicchange description]
     *
     * @param   int     $topicid     [$topicid description]
     * @param   string  $topicname   [$topicname description]
     * @param   string  $topicimage  [$topicimage description]
     * @param   string  $topictext   [$topictext description]
     * @param   string  $topicadmin  [$topicadmin description]
     * @param   string  $name        [$name description]
     * @param   string  $url         [$url description]
     *
     * @return  void
     */
    function topicchange(int $topicid, string $topicname, string $topicimage, string $topictext, string $topicadmin, string $name, string $url): void
    {
        $topicadminX = explode(',', $topicadmin);
        array_pop($topicadminX);

        $res = DB::table('droits')->select('*')->where('d_droits', 11112)->where('d_fon_fid', 2)->get();

        $d = array();
        $topad = array();

        foreach ($res as $d) {
            $topad[] = $d['d_aut_aid'];
        }

        foreach ($topicadminX as $value) {
            if (!in_array($value, $topad)) {
                DB::table('')->insert(array(
                    'd_aut_aid'      => $value,
                    'd_fon_fid'      => 2,
                    'd_droits'       => 11112,
                ));
            }
        }

        foreach ($topad as $value) { //pour chaque droit adminsujet on regarde le nom de l'adminsujet
            if (!in_array($value, $topicadminX)) { //si le nom de l'adminsujet n'est pas dans les nouveaux adminsujet
                //on cherche si il administre un autre sujet
                // $resu =  mysqli_get_client_info() <= '8.0' 
                //     ? DB::table('topics')->select('*')->where('topicadmin', 'REGEXP', '[[:<:]]" . $value . "[[:>:]]')->first()

                //     : DB::table('topics')->select('*')->where('topicadmin', 'REGEXP', '\\b" . $value . "\\b')->first();

                $resu = DB::table('topics')->select('*')->where('topicadmin', 'REGEXP', '\\b" . $value . "\\b')->first();
                
                if (($resu == 1) and ($topicid == $resu['tid'])) {
                    DB::table('droits')->where('d_aut_aid', $value)->where('d_droits', 11112)->wxhere('d_fon_fid', 2)->delete();
                }
            }
        }

        $topicname = stripslashes(str::FixQuotes($topicname));
        $topicimage = stripslashes(str::FixQuotes($topicimage));
        $topictext = stripslashes(str::FixQuotes($topictext));
        $name = stripslashes(str::FixQuotes($name));
        $url = stripslashes(str::FixQuotes($url));

        DB::table('topics')->where('topicid', $topicid)->update(array(
            'topicname'       => $topicname,
            'topicimage'      => $topicimage,
            'topictext'       => $topictext,
            'topicadmin'      => $topicadmin,
        ));

        global $aid;
        logs::Ecr_Log("security", "topicChange ($topicname, $topicid) by AID : $aid", "");
        
        if ($name) {
            DB::table('related')->insert(array(
                'tid'       => $topicid,
                'name'      => $name,
                'url'       => $url,
            ));
        }

        Header('Location: '. site_url('admin.php?op=topicedit&topicid='. $topicid));
    }

    /**
     * [topicdelete description]
     *
     * @param   int   $topicid  [$topicid description]
     * @param   int   $ok       [$ok description]
     *
     * @return  void
     */
    function topicdelete(int $topicid, int $ok = 0): void
    {
        if ($ok == 1) {
            global $aid;

            // pourquoi  cette requete not used res'[sid']
            //$res = DB::table('stories')->select('sid')->where('topic', $topicid)->first();

            DB::table('stories')->where('topic', $topicid)->delete();

            logs::Ecr_Log("security", "topicDelete (stories, $topicid) by AID : $aid", "");

            DB::table('topics')->where('topicid', $topicid)->delete();

            logs::Ecr_Log("security", "topicDelete (topic, $topicid) by AID : $aid", "");

            DB::table('related')->where('tid', $topicid)->delete();

            logs::Ecr_Log("security", "topicDelete (related, $topicid) by AID : $aid", '');

            // commentaires
            if (file_exists("modules/comments/config/article.conf.php")) {
                include("modules/comments/config/article.conf.php");
                
                DB::table('posts')->where('forum_id', $forum)->where('topic_id', $topic)->delete();

                logs::Ecr_Log("security", "topicDelete (comments, $topicid) by AID : $aid", "");
            }

            Header('Location: '. site_url('admin.php?op=topicsmanager'));
        } else {
            global $f_meta_nom, $f_titre;

            include("themes/default/header.php");

            GraphicAdmin(manuel('topics'));
            adminhead($f_meta_nom, $f_titre);

            $topic = DB::table('topics')->select('topicimage', 'topicname', 'topictext')->where('topicid', $topicid)->first();

            echo '<h3 class=""><span class="text-danger">'. __d('two_news', 'Effacer le Sujet') .' : </span>'. language::aff_langue($topicname) .'</h3>';
            echo '<div class="alert alert-danger lead" role="alert">';

            if ($topic['topicimage'] != "") {
                echo '
                <div class="thumbnail">
                    <img class="img-fluid" src="'. Config::get('npds.tipath') . $topic['topicimage'] .'" alt="logo-topic" />
                </div>';
            }

            echo '
                <p>'. __d('two_news', 'Etes-vous sûr de vouloir effacer ce sujet ?') .' : '. $topic['topicname'] .'</p>
                <p>'. __d('two_news', 'Ceci effacera tous ses articles et ses commentaires !') .'</p>
                <p><a class="btn btn-danger" href="'. site_url('admin.php?op=topicdelete&amp;topicid='. $topicid .'&amp;ok=1') .'">'. __d('two_news', 'Oui') .'</a>&nbsp;<a class="btn btn-primary"href="admin.php?op=topicsmanager">'. __d('two_news', 'Non') .'</a></p>
            </div>';

            css::adminfoot('', '', '', '');
        }
    }

}